<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CiAuth;
use App\Models\CatatanRevisi;
use App\Models\Dokumen;
use App\Models\Pemeriksaan;
use App\Models\Project;
use App\Models\Sertifikat;
use App\Models\User;
use SSP;
use CodeIgniter\HTTP\Files\UploadedFile;
use DateTime;
use TCPDF;

class MainController extends BaseController
{

    protected $db;

    public function __construct()
    {
        require_once APPPATH . 'ThirdParty/ssp.php';
        require_once ROOTPATH . 'vendor/autoload.php';
        $this->db = db_connect();
        $this->sql_details = [
            'user' => env('database.default.username'),
            'pass' => env('database.default.password'),
            'db'   => env('database.default.database'),
            'host' => env('database.default.hostname'),
        ];
    }

    public function logoutHandler()
    {
        CiAuth::forget();
        return redirect()->route('admin.login.form')->with('fail', 'anda telah logout');
    }

    public function index()
    {
        $user = CiAuth::user();
        $role = $user->role;
        $userId = $user->id;

        $projectModel = new Project();

        switch ($role) {
            case 'ppk':
                $projects = $projectModel->where('ppk_id', $userId)->findAll();
                break;

            case 'kontraktor':
                $projects = $projectModel->where('kontraktor_id', $userId)->findAll();
                break;

            case 'konsultan':
                $projects = $projectModel->where('konsultan_id', $userId)->findAll();
                break;
            default:
                $projects = $projectModel->findAll();
                break;
        }

        $data = [
            'pageTitle' => 'Dashboard',
            'role' => $role,
            'projects' => $projects,
        ];

        return view('backend/pages/home', $data);
    }

    public function pageProject()
    {

        $userModel = new User();

        $kontraktors = $userModel->where('role', 'kontraktor')->findAll();
        $konsultans = $userModel->where('role', 'konsultan')->findAll();
        $data = [
            'pageTitle' => 'Data Proyek',
            'kontraktors' => $kontraktors,
            'konsultans' => $konsultans,
        ];
        return view('backend/pages/project', $data);
    }

    public function saveProject()
    {
        $user = get_user();

        if ($user->role !== 'ppk') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambah project.'
                ]);
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah project.');
            }
        }

        $data = $this->request->getPost();

        $data['ppk_id'] = $user->id;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required',
            'location' => 'required',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'status' => 'required',
            'kontraktor_id' => 'required|integer',
            'konsultan_id' => 'required|integer',
            'ppk_id' => 'required|integer',
        ]);
        if (isset($data['status']) && $data['status'] === 'in-progress') {
            $rules['progress_percentage'] = 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]';
        }

        if (!$validation->run($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'validation_error',
                    'errors' => $validation->getErrors()
                ]);
            } else {

                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
        }

        $projectModel = new Project();
        $projectModel->insert($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Project berhasil ditambahkan.'
            ]);
        } else {
            return redirect()->to(route_to('project.index'))->with('success', 'Project berhasil ditambahkan.');
        }
    }

    public function datatableProject()
    {
        $table = 'projects';
        $primaryKey = 'id';

        $table = 'projects';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 0],              // Nomor urut (diganti di client)
            ['db' => 'name', 'dt' => 1],   // Nama Project
            ['db' => 'location', 'dt' => 2],          // Lokasi

            [
                'db' => 'status', 'dt' => 3,
                'formatter' => function ($d, $row) {
                    $badgeClass = '';
                    switch (strtolower($d)) {
                        case 'in-progress':
                            $badgeClass = 'badge-primary';
                            break;
                        case 'completed':
                            $badgeClass = 'badge-success';
                            break;
                        case 'pending':
                            $badgeClass = 'badge-danger';
                            break;
                        default:
                            $badgeClass = 'badge-secondary';
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($d) . '</span>';
                }
            ],
            [
                'db' => 'id', 'dt' => 4,
                'formatter' => function ($d, $row) {
                    return '
                <div class="btn-group" role="group" aria-label="Aksi Project">
                    <button type="button" class="btn btn-primary btn-sm btnEditProject" data-id="' . $d . '" title="Edit Project">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btnHapusProject" data-id="' . $d . '" title="Hapus Project">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>';
                }
            ],
        ];


        return $this->response->setJSON(
            SSP::simple($_GET, $this->sql_details, $table, $primaryKey, $columns)
        );
    }

    public function getProject()
    {
        $id = $this->request->getGet('id');
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID project tidak ditemukan'
            ]);
        }

        $projectModel = new Project();
        $project = $projectModel->find($id);

        if (!$project) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Project tidak ditemukan'
            ]);
        }
        return $this->response->setJSON([
            'success' => true,
            'data' => $project
        ]);
    }

    public function updateProject()
    {
        // Cek apakah request method POST
        if (!$this->request->isAJAX() || !$this->request->getMethod() === 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $id_project = $this->request->getPost('id');

        // Ambil data POST
        $data = [
            'id' => $this->request->getPost('id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'location' => $this->request->getPost('location'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'status' => $this->request->getPost('status'),
            'kontraktor_id' => $this->request->getPost('kontraktor_id'),
            'konsultan_id' => $this->request->getPost('konsultan_id'),
        ];
        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|string|max_length[255]',
            'description' => 'permit_empty|string',
            'location' => 'required|string|max_length[255]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'status' => 'required|in_list[in-progress,completed,pending]',
            'kontraktor_id' => 'required|integer|is_not_unique[projects.kontraktor_id,id,' . $id_project . ']',
            'konsultan_id' => 'required|integer|is_not_unique[projects.konsultan_id,id,' . $id_project . ']',
        ]);

        if (!$validation->run($data)) {
            // Jika validasi gagal, kirim error per field
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $updateData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'location' => $data['location'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'kontraktor_id' => $data['kontraktor_id'],
            'konsultan_id' => $data['konsultan_id'],
        ];
        $projectModel = new Project();
        $updated = $projectModel->update($data['id'], $updateData);

        if ($updated) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Project berhasil diperbarui'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui project'
            ]);
        }
    }

    public function deleteProject()
    {
        $id = $this->request->getPost('id');

        // Validasi id harus ada dan integer
        if (empty($id) || !is_numeric($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID project tidak valid'
            ]);
        }

        $projectModel = new Project();

        // Cek apakah data project ada
        $project = $projectModel->find($id);
        if (!$project) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Project tidak ditemukan'
            ]);
        }

        // Hapus data project
        $deleted = $projectModel->delete($id);

        if ($deleted) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Project berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus project'
            ]);
        }
    }

    public function pagePengajuan()
    {
        $projectModel = new Project();

        if (get_user()->role == 'kontraktor') {
            $projects = $projectModel->asObject()->where('kontraktor_id', get_user()->id)->findAll();
        }
        $data = [
            'pageTitle' => 'Ajukan Pembayaran',
            'projects' => $projects,
        ];
        return view('backend/pages/pengajuan', $data);
    }

    public function cekFilesSertifikat()
    {
        $request = service('request');
        $project_id = $request->getGet('project_id');
        $kontraktor_id = $request->getGet('kontraktor_id');

        if (!$project_id || !$kontraktor_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter project_id dan kontraktor_id diperlukan.'
            ]);
        }

        $db = \Config\Database::connect();

        // Ambil data sertifikat terbaru berdasarkan project_id dan kontraktor_id
        $sertifikat = $db->table('sertifikat')
            ->where('project_id', $project_id)
            ->where('kontraktor_id', $kontraktor_id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        if (!$sertifikat) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada file sertifikat atau dokumen pendukung ditemukan.'
            ]);
        }

        // Siapkan data file dengan URL lengkap
        $sertifikatData = [
            'file_sertifikat' => $sertifikat->file_sertifikat ? base_url($sertifikat->file_sertifikat) : null,
            'file_kuantitas' => $sertifikat->file_kuantitas ? base_url($sertifikat->file_kuantitas) : null,
            'file_kualitas' => $sertifikat->file_kualitas ? base_url($sertifikat->file_kualitas) : null,
        ];

        return $this->response->setJSON([
            'success' => true,
            'files' => $sertifikatData,
        ]);
    }


    public function submitSertifikat()
    {
        helper(['form', 'url']);

        $response = [
            'success' => false,
            'message' => 'Terjadi kesalahan saat pengajuan.'
        ];

        $request = service('request');
        $sertifikatModel = new Sertifikat();
        $dokumenModel = new Dokumen();

        // Ambil data form
        $project_id = $request->getPost('project_id');
        $bulanInput = $request->getPost('bulan');
        $fileSertifikat = $this->request->getFile('fileSertifikat');
        $fileKuantitas = $this->request->getFile('fileKuantitas');
        $fileKualitas = $this->request->getFile('fileKualitas');
        $allFiles = $this->request->getFiles();
        $filePendukung = isset($allFiles['filePendukung']) ? $allFiles['filePendukung'] : [];

        // Validasi wajib
        if (!$project_id || !$bulanInput || !$fileSertifikat->isValid()) {
            $response['message'] = 'Data wajib belum lengkap atau file sertifikat tidak valid.';
            return $this->response->setJSON($response);
        }

        // Parse tahun dan bulan dari input
        $tahun = null;
        $bulan = null;
        if (preg_match('/^(\d{4})-(\d{2})$/', $bulanInput, $matches)) {
            $tahun = (int)$matches[1];
            $bulan = (int)$matches[2];
        } else {
            $response['message'] = 'Format bulan tidak valid. Gunakan format YYYY-MM.';
            return $this->response->setJSON($response);
        }

        // Validasi tipe file sertifikat (harus PDF)
        if ($fileSertifikat->getMimeType() !== 'application/pdf') {
            $response['message'] = 'File sertifikat harus berupa PDF.';
            return $this->response->setJSON($response);
        }

        // Validasi file kuantitas dan kualitas jika ada
        $allowedFileTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if ($fileKuantitas && $fileKuantitas->isValid() && !in_array($fileKuantitas->getClientMimeType(), $allowedFileTypes)) {
            $response['message'] = 'File kuantitas harus berupa PDF atau dokumen Word.';
            return $this->response->setJSON($response);
        }
        if ($fileKualitas && $fileKualitas->isValid() && !in_array($fileKualitas->getClientMimeType(), $allowedFileTypes)) {
            $response['message'] = 'File kualitas harus berupa PDF atau dokumen Word.';
            return $this->response->setJSON($response);
        }

        // Tentukan kontraktor_id jika ada
        $kontraktor_id = null;
        if (function_exists('get_user') && get_user()->role == 'kontraktor') {
            $kontraktor_id = get_user()->id;
        }

        // Cek apakah sudah ada data sertifikat dengan project_id, kontraktor_id, bulan, tahun yang sama
        $existingSertifikat = $sertifikatModel->where('project_id', $project_id)
            ->where('kontraktor_id', $kontraktor_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        $uploadPathSertifikat = FCPATH . 'uploads/sertifikat/';
        $uploadPathPendukung = FCPATH . 'uploads/dokumen_pendukung/';

        // Buat folder jika belum ada
        if (!is_dir($uploadPathSertifikat)) {
            mkdir($uploadPathSertifikat, 0755, true);
        }
        if (!is_dir($uploadPathPendukung)) {
            mkdir($uploadPathPendukung, 0755, true);
        }

        // Jika ada data lama, hapus file lama sertifikat, kuantitas, kualitas dan dokumen pendukung lama
        if ($existingSertifikat) {
            // Hapus file sertifikat lama
            $oldFileSertifikat = FCPATH . $existingSertifikat['file_sertifikat'];
            if (file_exists($oldFileSertifikat)) {
                unlink($oldFileSertifikat);
            }
            // Hapus file kuantitas lama
            if (!empty($existingSertifikat['file_kuantitas'])) {
                $oldFileKuantitas = FCPATH . $existingSertifikat['file_kuantitas'];
                if (file_exists($oldFileKuantitas)) {
                    unlink($oldFileKuantitas);
                }
            }
            // Hapus file kualitas lama
            if (!empty($existingSertifikat['file_kualitas'])) {
                $oldFileKualitas = FCPATH . $existingSertifikat['file_kualitas'];
                if (file_exists($oldFileKualitas)) {
                    unlink($oldFileKualitas);
                }
            }

            // Hapus file dokumen pendukung lama dan data di DB
            $oldDokumenPendukung = $dokumenModel->where('sertifikat_id', $existingSertifikat['id'])->findAll();
            foreach ($oldDokumenPendukung as $dok) {
                $oldFilePendukung = FCPATH . $dok['path_file'];
                if (file_exists($oldFilePendukung)) {
                    unlink($oldFilePendukung);
                }
                $dokumenModel->delete($dok['id']);
            }
        }

        // Simpan file sertifikat baru
        $newNameSertifikat = $fileSertifikat->getRandomName();
        if (!$fileSertifikat->move($uploadPathSertifikat, $newNameSertifikat)) {
            $response['message'] = 'Gagal menyimpan file sertifikat.';
            return $this->response->setJSON($response);
        }

        // Simpan file kuantitas baru jika ada
        $newNameKuantitas = null;
        if ($fileKuantitas && $fileKuantitas->isValid()) {
            $newNameKuantitas = $fileKuantitas->getRandomName();
            if (!$fileKuantitas->move($uploadPathSertifikat, $newNameKuantitas)) {
                $response['message'] = 'Gagal menyimpan file kuantitas.';
                return $this->response->setJSON($response);
            }
        }

        // Simpan file kualitas baru jika ada
        $newNameKualitas = null;
        if ($fileKualitas && $fileKualitas->isValid()) {
            $newNameKualitas = $fileKualitas->getRandomName();
            if (!$fileKualitas->move($uploadPathSertifikat, $newNameKualitas)) {
                $response['message'] = 'Gagal menyimpan file kualitas.';
                return $this->response->setJSON($response);
            }
        }

        $sertifikatData = [
            'project_id' => $project_id,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'file_sertifikat' => 'uploads/sertifikat/' . $newNameSertifikat,
            'file_kuantitas' => $newNameKuantitas ? 'uploads/sertifikat/' . $newNameKuantitas : null,
            'file_kualitas' => $newNameKualitas ? 'uploads/sertifikat/' . $newNameKualitas : null,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'kontraktor_id' => $kontraktor_id,
        ];

        // Insert atau update data sertifikat
        if ($existingSertifikat) {
            $sertifikatModel->update($existingSertifikat['id'], $sertifikatData);
            $sertifikat_id = $existingSertifikat['id'];
        } else {
            $sertifikat_id = $sertifikatModel->insert($sertifikatData);
            if (!$sertifikat_id) {
                unlink($uploadPathSertifikat . $newNameSertifikat);
                if ($newNameKuantitas) unlink($uploadPathSertifikat . $newNameKuantitas);
                if ($newNameKualitas) unlink($uploadPathSertifikat . $newNameKualitas);
                $response['message'] = 'Gagal menyimpan data sertifikat ke database.';
                return $this->response->setJSON($response);
            }
        }

        // Upload dan simpan data dokumen pendukung baru
        if (!empty($filePendukung) && is_array($filePendukung)) {
            $allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];

            foreach ($filePendukung as $file) {
                if (!($file instanceof \CodeIgniter\HTTP\Files\UploadedFile)) {
                    log_message('error', 'Item bukan instance UploadedFile: ' . print_r($file, true));
                    continue;
                }
                if (!$file->isValid()) {
                    log_message('error', 'File dokumen pendukung tidak valid: ' . $file->getClientName());
                    continue;
                }
                if ($file->hasMoved()) {
                    log_message('error', 'File dokumen pendukung sudah dipindahkan: ' . $file->getClientName());
                    continue;
                }
                if (!in_array($file->getClientMimeType(), $allowedTypes)) {
                    log_message('error', 'Tipe file tidak diizinkan: ' . $file->getClientMimeType());
                    continue;
                }
                $newNamePendukung = $file->getRandomName();
                if ($file->move($uploadPathPendukung, $newNamePendukung)) {
                    $insertData = [
                        'sertifikat_id' => $sertifikat_id,
                        'nama_file' => $file->getClientName(),
                        'tipe_dokumen' => $file->getClientMimeType(),
                        'path_file' => 'uploads/dokumen_pendukung/' . $newNamePendukung,
                        'uploaded_at' => date('Y-m-d H:i:s'),
                    ];
                    if (!$dokumenModel->insert($insertData)) {
                        log_message('error', 'Gagal insert dokumen pendukung: ' . json_encode($insertData));
                    }
                } else {
                    log_message('error', 'Gagal memindahkan file dokumen pendukung: ' . $file->getClientName());
                }
            }
        } else {
            log_message('info', 'Tidak ada file dokumen pendukung yang diupload.');
        }

        $response['success'] = true;
        $response['message'] = 'Pengajuan sertifikat berhasil dikirim dan file lama diganti jika ada.';

        return $this->response->setJSON($response);
    }

    public function tableRiwayatSertifikat()
    {
        $table = 'sertifikat';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 0],
            [
                'db' => 'project_id',
                'dt' => 1,
                'formatter' => function ($d, $row) {
                    $projectModel = new Project();
                    $project = $projectModel->find($d);
                    return $project['name'];
                }
            ],
            [
                'db' => 'bulan', 'dt' => 2,
                'formatter' => function ($d, $row) {

                    $bulan = str_pad($d, 2, '0', STR_PAD_LEFT);

                    $namaBulan = [
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ];

                    $bulanIndo = isset($namaBulan[$bulan]) ? $namaBulan[$bulan] : $bulan;

                    return $bulanIndo;
                }
            ],
            [
                'db' => 'id',
                'dt' => 3,
                'formatter' => function ($d, $row) {
                    $sertifikatModel = new \App\Models\Sertifikat();
                    $dokumens = $sertifikatModel->asObject()->where('id', $d)->findAll();
                    $output = '<div class="file-list">';

                    if (!empty($dokumens)) {
                        foreach ($dokumens as $dokumen) {
                            // File Sertifikat
                            if (!empty($dokumen->file_sertifikat)) {
                                $urlSertifikat = base_url($dokumen->file_sertifikat);
                                $output .= '<div class="file-item">
                        <a href="' . $urlSertifikat . '" target="_blank" class="file-link">
                            <i class="fas fa-file-pdf"></i> Dokumen Sertifikat
                        </a>
                    </div>';
                            } else {
                                $output .= '<div class="file-item text-muted">File Sertifikat belum diupload</div>';
                            }

                            // File Kuantitas
                            if (!empty($dokumen->file_kuantitas)) {
                                $urlKuantitas = base_url($dokumen->file_kuantitas);
                                $output .= '<div class="file-item">
                        <a href="' . $urlKuantitas . '" target="_blank" class="file-link">
                            <i class="fas fa-chart-bar"></i> Laporan Kuantitas
                        </a>
                    </div>';
                            } else {
                                $output .= '<div class="file-item text-muted">File Kuantitas belum diupload</div>';
                            }

                            // File Kualitas
                            if (!empty($dokumen->file_kualitas)) {
                                $urlKualitas = base_url($dokumen->file_kualitas);
                                $output .= '<div class="file-item">
                        <a href="' . $urlKualitas . '" target="_blank" class="file-link">
                            <i class="fas fa-clipboard-check"></i> Laporan Kualitas
                        </a>
                    </div>';
                            } else {
                                $output .= '<div class="file-item text-muted">File Kualitas belum diupload</div>';
                            }
                        }
                    } else {
                        $output .= '<div class="file-item">-</div>';
                    }

                    $output .= '</div>';
                    return $output;
                }
            ],


            [
                'db' => 'id',
                'dt' => 4,
                'formatter' => function ($d, $row) {
                    $dokumenModel = new \App\Models\Dokumen();
                    $docs = $dokumenModel->where('sertifikat_id', $d)->findAll();

                    if (!$docs) {
                        return '<div class="file-item text-muted">Dokumen pendukung belum diupload</div>';
                    }

                    $output = '<div class="file-list">';
                    foreach ($docs as $doc) {
                        $url = base_url($doc['path_file']);
                        $nama = esc($doc['nama_file']);
                        $output .= '<div class="file-item">
                <a href="' . $url . '" target="_blank" class="file-link">
                    <i class="fas fa-file-alt"></i> ' . $nama . '
                </a>
            </div>';
                    }
                    $output .= '</div>';

                    return $output;
                }
            ],

            [
                'db' => 'status', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    $badgeClass = '';
                    switch (strtolower($d)) {
                        case 'pending':
                            $badgeClass = 'badge-warning';
                            break;
                        case 'approved':
                            $badgeClass = 'badge-success';
                            break;
                        case 'rejected':
                            $badgeClass = 'badge-danger';
                            break;
                        default:
                            $badgeClass = 'badge-secondary';
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($d) . '</span>';
                }
            ],
            [
                'db' => 'created_at', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return formatTanggalIndonesia($d);
                }
            ],
            [
                'db' => 'id', 'dt' => 7, // kolom keterangan di index 7
                'formatter' => function ($sertifikat_id, $row) {
                    $pemeriksaanModel = new Pemeriksaan();
                    $catatanRevisiModel = new CatatanRevisi();
                    $sertifikatModel = new Sertifikat();

                    $pemeriksaanList = $pemeriksaanModel->where('sertifikat_id', $sertifikat_id)->findAll();

                    if (empty($pemeriksaanList)) {
                        return "Menunggu diperiksa oleh konsultan";
                    }

                    // Cek apakah ada file yang ditolak
                    $adaDitolak = false;
                    $catatanDitolak = [];

                    foreach ($pemeriksaanList as $pemeriksaan) {
                        if (strtolower($pemeriksaan['status_pemeriksaan']) == 'ditolak') {
                            $adaDitolak = true;

                            $catatan = $catatanRevisiModel->where([
                                'pemeriksaan_id' => $pemeriksaan['id'],
                                'file_name' => $pemeriksaan['jenis_file']
                            ])->first();

                            $catatanText = $catatan ? $catatan['catatan'] : '-';
                            $userModel = new User();
                            $user = $userModel->find($pemeriksaan['pemeriksa_id']);
                            $namaKonsultan = $user ? $user['name'] : 'Unknown';

                            $namaFile = isset($namaFileMap[$pemeriksaan['jenis_file']]) ? $namaFileMap[$pemeriksaan['jenis_file']] : $pemeriksaan['jenis_file'];

                            $tanggalFormatted = formatTanggalIndonesia($pemeriksaan['tanggal_pemeriksaan']);

                            $catatanDitolak[] = "File <strong>{$namaFile}</strong> ditolak oleh <strong>{$namaKonsultan}</strong> pada tanggal <strong>{$tanggalFormatted}</strong><br>Catatan Penolakan: {$catatanText} <br>";
                        }
                    }
                    if ($adaDitolak) {
                        return implode("<br>", $catatanDitolak);
                    }

                    $sertifikat = $sertifikatModel->find($sertifikat_id);

                    if ($sertifikat) {
                        if ($sertifikat['status'] == 'pending') {
                            return "Sudah diperiksa konsultan, menunggu persetujuan PPK";
                        } elseif ($sertifikat['status'] == 'rejected') {
                            return "Ditolak oleh PPK: " . ($sertifikat['catatan_penolakan'] ?? '-');
                        } elseif ($sertifikat['status'] == 'approved') {
                            return "Proses pengajuan selesai, dapat melanjutkan ke tahap pembayaran";
                        }
                    }

                    return "Status tidak diketahui";
                }
            ],
        ];

        return $this->response->setJSON(
            SSP::simple($_GET, $this->sql_details, $table, $primaryKey, $columns, null, 'kontraktor_id = ' . CiAuth::id())
        );
    }


    public function pagePemeriksaan()
    {
        $projectModel = new Project();
        $projects = $projectModel->findAll();
        $db = \Config\Database::connect();
        $builder = $db->table('sertifikat');
        $builder->select('sertifikat.*, projects.name as project_name, users.name as contractor_name');
        $builder->join('projects', 'sertifikat.project_id = projects.id');
        $builder->join('users', 'projects.kontraktor_id = users.id');
        $subQueryKuantitas = $db->table('pemeriksaan')
            ->select('status_pemeriksaan')
            ->where('jenis_file', 'file_kuantitas')
            ->where('sertifikat_id = sertifikat.id')
            ->orderBy('tanggal_pemeriksaan', 'DESC')
            ->limit(1)
            ->getCompiledSelect();

        $subQueryKualitas = $db->table('pemeriksaan')
            ->select('status_pemeriksaan')
            ->where('jenis_file', 'file_kualitas')
            ->where('sertifikat_id = sertifikat.id')
            ->orderBy('tanggal_pemeriksaan', 'DESC')
            ->limit(1)
            ->getCompiledSelect();

        $builder->select("(
        CASE
            WHEN (($subQueryKuantitas) = 'ditolak' OR ($subQueryKualitas) = 'ditolak') THEN 'rejected'
            WHEN (($subQueryKuantitas) = 'disetujui' AND ($subQueryKualitas) = 'disetujui') THEN 'approved'
            ELSE 'pending'
        END
    ) as status");

        $files = $builder->get()->getResultArray();

        $data = [
            'pageTitle' => 'Pemeriksaan',
            'projects' => $projects,
            'files' => $files,
        ];

        return view('backend/pages/pemeriksaan', $data);
    }

    public function getFileAndPemeriksaan($fileId)
    {
        $sertifikatModel = new Sertifikat();
        $pemeriksaanModel = new Pemeriksaan();
        $dokumenPendukungModel = new Dokumen();
        $fileData = $sertifikatModel->find($fileId);
        if (!$fileData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File sertifikat tidak ditemukan'
            ]);
        }

        $baseUrl = base_url();

        // Siapkan data file utama dengan URL lengkap
        $files = [
            'sertifikat_bulanan' => ['url' => $fileData['file_sertifikat'] ? $baseUrl . $fileData['file_sertifikat'] : null],
            'file_kuantitas' => ['url' => $fileData['file_kuantitas'] ? $baseUrl . $fileData['file_kuantitas'] : null],
            'file_kualitas' => ['url' => $fileData['file_kualitas'] ? $baseUrl . $fileData['file_kualitas'] : null],
        ];

        // Ambil data pemeriksaan sebelumnya untuk tiap file utama
        $pemeriksaanData = [];
        foreach ($files as $jenisFile => $file) {
            if ($file['url'] === null) {
                continue;
            }

            $pemeriksaan = $pemeriksaanModel
                ->where('sertifikat_id', $fileId)
                ->where('jenis_file', $jenisFile)
                ->orderBy('tanggal_pemeriksaan', 'DESC')
                ->first();

            if ($pemeriksaan) {
                $pemeriksaanData[$jenisFile] = $pemeriksaan;
            }
        }
        $dokumenPendukung = $dokumenPendukungModel
            ->where('sertifikat_id', $fileId)
            ->findAll();

        foreach ($dokumenPendukung as &$dokumen) {
            $dokumen['url'] = $dokumen['path_file'] ? $baseUrl . $dokumen['path_file'] : null;
        }

        return $this->response->setJSON([
            'success' => true,
            'files' => $files,
            'pemeriksaan' => $pemeriksaanData,
            'dokumen_pendukung' => $dokumenPendukung,
        ]);
    }


    public function saveMultiple()
    {
        $request = $this->request;
        $pemeriksaanModel = new Pemeriksaan();
        $catatanRevisiModel = new CatatanRevisi();
        $fileModel = new Sertifikat();

        $fileId = $request->getPost('file_id');
        $userId = CiAuth::id();

        // Ambil sertifikat_id dari file
        $fileData = $fileModel->find($fileId);
        if (!$fileData || empty($fileData['id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data sertifikat tidak ditemukan untuk file ini.'
            ]);
        }
        $sertifikatId = $fileData['id'];

        $jenisFiles = ['sertifikat_bulanan', 'file_kuantitas', 'file_kualitas'];

        foreach ($jenisFiles as $jenis) {
            $status = $request->getPost('status_' . $jenis);
            $catatan = $request->getPost('catatan_' . $jenis);

            if (!$status) {
                continue;
            }

            // Cek apakah data pemeriksaan sudah ada berdasarkan sertifikat_id dan jenis_file
            $existingPemeriksaan = $pemeriksaanModel
                ->where('sertifikat_id', $sertifikatId)
                ->where('jenis_file', $jenis)
                ->first();

            $dataPemeriksaan = [
                'sertifikat_id' => $sertifikatId,
                'jenis_file' => $jenis,
                'pemeriksa_id' => $userId,
                'status_pemeriksaan' => $status,
                'tanggal_pemeriksaan' => date('Y-m-d H:i:s'),
                'catatan_umum' => ($status === 'ditolak' && $catatan) ? $catatan : null,
            ];

            if ($existingPemeriksaan) {
                // Update data pemeriksaan yang sudah ada
                $pemeriksaanModel->update($existingPemeriksaan['id'], $dataPemeriksaan);
                $pemeriksaanId = $existingPemeriksaan['id'];
            } else {
                // Insert data pemeriksaan baru
                $pemeriksaanModel->insert($dataPemeriksaan);
                $pemeriksaanId = $pemeriksaanModel->getInsertID();
            }

            // Jika status ditolak dan ada catatan, cek catatan revisi
            if ($status === 'ditolak' && $catatan) {
                $existingCatatan = $catatanRevisiModel
                    ->where('pemeriksaan_id', $pemeriksaanId)
                    ->first();

                $dataCatatan = [
                    'pemeriksaan_id' => $pemeriksaanId,
                    'file_name' => $jenis,
                    'catatan' => $catatan,
                    'dibuat_oleh' => $userId,
                    'tanggal_catatan' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($existingCatatan) {
                    // Update catatan revisi yang sudah ada
                    $catatanRevisiModel->update($existingCatatan['id'], $dataCatatan);
                } else {
                    // Insert catatan revisi baru
                    $catatanRevisiModel->insert($dataCatatan);
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pemeriksaan berhasil disimpan'
        ]);
    }

    public function pageLaporan()
    {
        $data = [
            'pageTitle' => 'Laporan',

        ];
        return view('backend/pages/laporan_pengajuan', $data);
    }
    public function getLaporanRiwayat()
    {
        $pemeriksaanModel = new \App\Models\Pemeriksaan();
        $catatanRevisiModel = new \App\Models\CatatanRevisi();
        $sertifikatModel = new \App\Models\Sertifikat();
        $userModel = new \App\Models\User();

        // Ambil data sertifikat + proyek + kontraktor sekaligus
        $builder = $this->db->table('sertifikat s')
            ->select('
            s.id as sertifikat_id,
            pr.name as proyek,
            u_kon.name as kontraktor,
            s.bulan,
            s.tahun,
            s.file_sertifikat,
            s.file_kuantitas,
            s.file_kualitas,
            s.status as status_sertifikat
        ')
            ->join('projects pr', 's.project_id = pr.id')
            ->join('users u_kon', 's.kontraktor_id = u_kon.id')
            ->orderBy('pr.name', 'ASC')
            ->orderBy('s.tahun', 'DESC')
            ->orderBy('s.bulan', 'DESC');

        $sertifikats = $builder->get()->getResultArray();

        $result = [];

        // Mapping nama file untuk keterangan & key
        $namaFileMap = [
            'sertifikat_bulanan' => 'Sertifikat Bulanan',
            'file_kuantitas' => 'Laporan Kuantitas',
            'file_kualitas' => 'Laporan Kualitas',
        ];

        function formatTanggalIndonesia($tgl)
        {
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $dateObj = date_create($tgl);
            if (!$dateObj) return $tgl;
            $day = $dateObj->format('d');
            $month = intval($dateObj->format('m'));
            $year = $dateObj->format('Y');
            return $day . ' ' . $bulan[$month] . ' ' . $year;
        }

        foreach ($sertifikats as $sertifikat) {
            $pemeriksaanList = $pemeriksaanModel->where('sertifikat_id', $sertifikat['sertifikat_id'])->findAll();

            // Fungsi mengambil status pemeriksaan per tipe file
            $getStatusByFile = function ($tipe_file) use ($pemeriksaanList, $userModel) {
                foreach ($pemeriksaanList as $p) {
                    if ($p['jenis_file'] == $tipe_file) {
                        $namaPemeriksa = $userModel->find($p['pemeriksa_id']);
                        $namaPemeriksa = $namaPemeriksa ? $namaPemeriksa['name'] : '-';
                        return [
                            'status_pemeriksaan' => $p['status_pemeriksaan'],
                            'tgl_pemeriksaan' => $p['tanggal_pemeriksaan'],
                            'pemeriksa' => $namaPemeriksa,
                        ];
                    }
                }
                return [
                    'status_pemeriksaan' => '-',
                    'tgl_pemeriksaan' => '-',
                    'pemeriksa' => '-',
                ];
            };

            $buatKeterangan = function () use ($sertifikat, $pemeriksaanList, $catatanRevisiModel, $userModel, $namaFileMap, $pemeriksaanModel) {
                if (empty($pemeriksaanList)) {
                    return "Menunggu diperiksa oleh konsultan";
                }

                $adaDitolak = false;
                $catatanDitolak = [];

                foreach ($pemeriksaanList as $pemeriksaan) {
                    if (strtolower($pemeriksaan['status_pemeriksaan']) == 'ditolak') {
                        $adaDitolak = true;

                        $catatan = $catatanRevisiModel->where([
                            'pemeriksaan_id' => $pemeriksaan['id'],
                            'file_name' => $pemeriksaan['jenis_file']
                        ])->first();

                        $catatanText = $catatan ? $catatan['catatan'] : '-';
                        $user = $userModel->find($pemeriksaan['pemeriksa_id']);
                        $namaKonsultan = $user ? $user['name'] : 'Unknown';

                        $namaFile = isset($namaFileMap[$pemeriksaan['jenis_file']]) ? $namaFileMap[$pemeriksaan['jenis_file']] : $pemeriksaan['jenis_file'];

                        $tanggalFormatted = formatTanggalIndonesia($pemeriksaan['tanggal_pemeriksaan']);

                        $catatanDitolak[] = "File {$namaFile} ditolak oleh {$namaKonsultan} pada tanggal{$tanggalFormatted}
                        
                        Catatan Penolakan: {$catatanText}";
                    }
                }

                if ($adaDitolak) {
                    return implode("<br>", $catatanDitolak);
                }

                if ($sertifikat['status_sertifikat'] == 'pending') {
                    return "Sudah diperiksa konsultan, menunggu persetujuan PPK";
                } elseif ($sertifikat['status_sertifikat'] == 'rejected') {
                    return "Ditolak oleh PPK: " . ($sertifikat['catatan_penolakan'] ?? '-');
                } elseif ($sertifikat['status_sertifikat'] == 'approved') {
                    return "Proses pengajuan selesai, dapat melanjutkan ke tahap pembayaran";
                }
                return "Status tidak diketahui";
            };

            // Susun data dokumen dalam array 
            $dokumenPengajuan = [];
            foreach ($namaFileMap as $keyFile => $namaFile) {
                $bulanDokumen = mapping_month($sertifikat['bulan']);
                $keyDokumen = ($keyFile === 'sertifikat_bulanan') ? $namaFile . " ({$bulanDokumen} - {$sertifikat['tahun']})" : $namaFile;
                $dokumenPengajuan[] = [
                    $keyDokumen => $getStatusByFile($keyFile),
                ];
            }

            $result[] = [
                'proyek' => $sertifikat['proyek'],
                'kontraktor' => $sertifikat['kontraktor'],
                'dokumen_pengajuan' => $dokumenPengajuan,
                'keterangan' => $buatKeterangan(),
            ];
        }

        return $result;
    }
    public function laporan_cetak()
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Riwayat Pengajuan');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 10);
        $dataLaporan = $this->getLaporanRiwayat();

        // Header Table dengan kolom gabungan (multi row header)
        $html = '
    <h3 style="text-align:center;">Laporan Riwayat Pengajuan</h3>
    <table border="1" cellpadding="4" cellspacing="0" width="100%">
        <thead>
        <tr style="background-color:#eeeeee; font-weight:bold;">
            <th  rowspan="2" style="text-align:center;">No</th>
            <th rowspan="2">Proyek</th>
            <th rowspan="2">Kontraktor</th>
            <th  rowspan="2">Dokumen Pengajuan</th>
            <th  colspan="2" style="text-align:center;">Status</th>
            <th rowspan="2" style="text-align:center;">Tgl Pemeriksaan</th>
            <th  rowspan="2" style="text-align:center;">Pemeriksa</th>
            <th  rowspan="2">Keterangan</th>
        </tr>
        <tr style="background-color:#eeeeee; font-weight:bold;">
            <th style="text-align:center;">Disetujui</th>
            <th style="text-align:center;">Ditolak</th>
        </tr>
        </thead>
        <tbody>
    ';

        $no = 1;
        foreach ($dataLaporan as $row) {
            $dokumenCount = count($row['dokumen_pengajuan']);
            // Mulai baris untuk proyek & kontraktor dengan rowspan dokumen count
            $html .= '<tr>';

            // Kolom no, proyek, kontraktor dengan rowspan = jumlah dokumen pengajuan
            $html .= '<td style="width:10px;" rowspan="' . $dokumenCount . '" style="text-align:center; vertical-align:top;">' . $no . '</td>';
            $html .= '<td rowspan="' . $dokumenCount . '" style="vertical-align:top;">' . htmlspecialchars($row['proyek']) . '</td>';
            $html .= '<td rowspan="' . $dokumenCount . '" style="vertical-align:top;">' . htmlspecialchars($row['kontraktor']) . '</td>';

            // Ambil dokumen pertama untuk baris pertama
            $firstDokumen = $row['dokumen_pengajuan'][0];
            $firstDokumenTitle = array_keys($firstDokumen)[0];
            $firstDokumenData = $firstDokumen[$firstDokumenTitle];

            // Kolom dokumen pengajuan pertama
            $html .= '<td>' . htmlspecialchars($firstDokumenTitle) . '</td>';

            // Status disetujui
            $status = $firstDokumenData['status_pemeriksaan'] ?? '-';
            $disetujui = ($status === 'disetujui') ? '✓' : '';
            $ditolak = ($status === 'ditolak') ? '✓' : '';
            $html .= '<td style="text-align:center;">' . $disetujui . '</td>';
            $html .= '<td style="text-align:center;">' . $ditolak . '</td>';

            // Tanggal pemeriksaan format Indonesia atau "-" jika kosong
            $tanggal = ($firstDokumenData['tgl_pemeriksaan'] && $firstDokumenData['tgl_pemeriksaan'] !== '-') ? date('d M Y', strtotime($firstDokumenData['tgl_pemeriksaan'])) : '-';
            $html .= '<td style="text-align:center;">' . $tanggal . '</td>';

            // Pemeriksa
            $html .= '<td style="text-align:center;">' . htmlspecialchars($firstDokumenData['pemeriksa'] ?? '-') . '</td>';

            // Keterangan dengan rowspan dokumen count
            $html .= '<td rowspan="' . $dokumenCount . '" style="vertical-align:top;">' . htmlspecialchars($row['keterangan']) . '</td>';

            $html .= '</tr>';

            // Baris berikutnya dokumen pengajuan selain pertama
            if ($dokumenCount > 1) {
                for ($i = 1; $i < $dokumenCount; $i++) {
                    $dokumen = $row['dokumen_pengajuan'][$i];
                    $dokumenTitle = array_keys($dokumen)[0];
                    $dokumenData = $dokumen[$dokumenTitle];

                    $status = $dokumenData['status_pemeriksaan'] ?? '-';
                    $disetujui = ($status === 'disetujui') ? '✓' : '';
                    $ditolak = ($status === 'ditolak') ? '✓' : '';
                    $tanggal = ($dokumenData['tgl_pemeriksaan'] && $dokumenData['tgl_pemeriksaan'] !== '-') ? date('d M Y', strtotime($dokumenData['tgl_pemeriksaan'])) : '-';

                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($dokumenTitle) . '</td>';
                    $html .= '<td style="text-align:center;">' . $disetujui . '</td>';
                    $html .= '<td style="text-align:center;">' . $ditolak . '</td>';
                    $html .= '<td style="text-align:center;">' . $tanggal . '</td>';
                    $html .= '<td style="text-align:center;">' . htmlspecialchars($dokumenData['pemeriksa'] ?? '-') . '</td>';
                    $html .= '</tr>';
                }
            }

            $no++;
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('laporan_riwayat_pengajuan.pdf', 'I');
        exit;
    }



    public function tableLaporan()
    {
        $columns = [
            [
                'db' => 'id',
                'dt' => 0,
            ],
            [
                'db' => 'name',
                'dt' => 1,
            ],
            [
                'db' => 'kontraktor_id',
                'dt' => 2,
                'formatter' => function ($d, $row) {
                    $userModel = new User();
                    $name = $userModel->find($d);
                    return ucfirst($name['name']);
                }
            ],
            [
                'db' => 'id',
                'dt' => 3,
                'formatter' => function ($d, $row) {
                    $projectModel = new Project();
                    $projectData = $projectModel->find($d);
                    $start = date('m/Y', strtotime($projectData['start_date']));
                    $end = isset($projectData['end_date']) ? date('m/Y', strtotime($projectData['end_date'])) : '-';
                    return $start . ' s/d ' . $end;
                }
            ],
            [
                'db' => 'status',
                'dt' => 4,
                'formatter' => function ($d, $row) {
                    $status = strtolower($d);
                    $badgeClass = '';
                    $text = ucfirst($d);

                    switch ($status) {
                        case 'pending':
                            $badgeClass = 'badge-warning';
                            break;
                        case 'in-progress':
                            $badgeClass = 'badge-primary';
                            break;
                        case 'completed':
                            $badgeClass = 'badge-success';
                            break;
                        default:
                            $badgeClass = 'badge-secondary';
                            break;
                    }

                    return '<span class="badge ' . $badgeClass . '">' . $text . '</span>';
                }
            ],

            // [
            //     'db' => 'id',
            //     'dt' => 5,
            //     'formatter' => function ($d, $row) {
            //         return '<a href="' . route_to('laporan_cetak', $d) . '" target="_blank" class="btn btn-sm btn-primary"> <i class="fas fa-file-pdf"></i>Cetak PDF</a>';
            //     }
            // ],
        ];

        return $this->response->setJSON(
            SSP::simple($_GET, $this->sql_details, 'projects', 'id', $columns)
        );
    }


    public function pagePersetujuan()
    {

        $data = [
            'pageTitle' => 'Persetujuan Pengajuan Pembayaran',
        ];
        return view('backend/pages/persetujuan_pengajuan', $data);
    }

    public function apiHasilPemeriksaan()
    {
        $builder = $this->db->table('pemeriksaan p');
        $builder->select('
        p.id as pemeriksaan_id,
        p.sertifikat_id,
        p.jenis_file,
        p.status_pemeriksaan,
        p.tanggal_pemeriksaan,
        p.catatan_umum,
        s.project_id,
        s.file_sertifikat,
        s.file_kuantitas,
        s.file_kualitas,
        s.bulan,
        s.tahun,
        s.created_at as tanggal_pengajuan,
        s.status as status_sertifikat,
        pr.name as project_name,
        u.name as nama_pemeriksa
    ');
        $builder->join('sertifikat s', 's.id = p.sertifikat_id', 'left');
        $builder->join('projects pr', 'pr.id = s.project_id', 'left');
        $builder->join('users u', 'u.id = p.pemeriksa_id', 'left');
        $query = $builder->get();
        $hasilPemeriksaanRaw = $query->getResultArray();

        // Struktur data untuk mengelompokkan hasil pemeriksaan per sertifikat
        $hasilPemeriksaan = [];

        foreach ($hasilPemeriksaanRaw as $row) {
            $sertifikatId = $row['sertifikat_id'];

            // Jika sertifikat belum ada di array, inisialisasi
            if (!isset($hasilPemeriksaan[$sertifikatId])) {
                // Ambil dokumen pendukung untuk sertifikat ini
                $dokumenPendukung = $this->db->table('dokumen_pendukung')
                    ->select('id, nama_file, tipe_dokumen, path_file, uploaded_at')
                    ->where('sertifikat_id', $sertifikatId)
                    ->get()
                    ->getResultArray();

                $hasilPemeriksaan[$sertifikatId] = [
                    'sertifikat_id' => $sertifikatId,
                    'project_name' => $row['project_name'],
                    'bulan' => mapping_month($row['bulan']),
                    'tahun' => $row['tahun'],
                    'file_sertifikat' => $row['file_sertifikat'],
                    'file_kuantitas' => $row['file_kuantitas'],
                    'file_kualitas' => $row['file_kualitas'],
                    'status_sertifikat' => $row['status_sertifikat'],
                    'dokumen_pendukung' => $dokumenPendukung,
                    'pemeriksa' => $row['nama_pemeriksa'],
                    'pemeriksaan' => [
                        'file_kuantitas' => null,
                        'file_kualitas' => null,
                        'sertifikat_bulanan' => null,
                    ],
                    'tanggal_pengajuan' => formatTanggalIndonesia($row['tanggal_pengajuan']),
                ];
            }

            // Simpan status pemeriksaan per jenis file
            if (in_array($row['jenis_file'], ['file_kuantitas', 'file_kualitas', 'sertifikat_bulanan'])) {
                $hasilPemeriksaan[$sertifikatId]['pemeriksaan'][$row['jenis_file']] = [
                    'status_pemeriksaan' => $row['status_pemeriksaan'],
                    'tanggal_pemeriksaan' => formatTanggalIndonesia($row['tanggal_pemeriksaan']),
                    'catatan_umum' => $row['catatan_umum'],
                ];
            }
        }

        // Reset array keys agar menjadi numerik
        $hasilPemeriksaan = array_values($hasilPemeriksaan);

        return $this->response->setJSON(['status' => true, 'data' => $hasilPemeriksaan]);
    }

    public function apiUpdatePersetujuan()
    {
        $request = $this->request->getPost();

        $sertifikatId = $request['sertifikat_id'] ?? null;
        $status = $request['status'] ?? null;
        $catatan = $request['catatan_umum'] ?? null;

        if (!$sertifikatId || !$status) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
        }

        $dataUpdate = [
            'status' => $status,
        ];

        if ($status === 'rejected') {
            $dataUpdate['catatan_penolakan'] = $catatan;
        } else {
            // Jika bukan ditolak, kosongkan catatan penolakan
            $dataUpdate['catatan_penolakan'] = null;
        }

        $sertifikatModel = new Sertifikat();

        $updated = $sertifikatModel->update($sertifikatId, $dataUpdate);

        if ($updated) {
            return $this->response->setJSON(['status' => true, 'message' => 'Status persetujuan berhasil diperbarui']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal memperbarui status persetujuan']);
        }
    }

    public function getStatusFile()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $sertifikat_id = $request->getVar('sertifikatId');
            $sertifikatModel = new Sertifikat();
            $data = $sertifikatModel->find($sertifikat_id);

            return $this->response->setJSON(['data' => $data]);
        }
    }

    public function pageProgress()
    {
        $kontraktorId = CiAuth::id(); // Ambil dari session user login

        $projectModel = new Project();
        $projects = $projectModel->where('kontraktor_id', $kontraktorId)->findAll();

        $data = [
            'pageTitle' => 'Update Progress Project',
            'projects' => $projects,
        ];

        return view('backend/pages/progress', $data);
    }

    public function updateProgress()
    {
        $request = service('request');
        $projectId = $request->getPost('project_id');
        $progressPercentage = $request->getPost('progress_percentage');

        $projectModel = new Project();

        // Validasi sederhana
        if ($progressPercentage < 0 || $progressPercentage > 100) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Persentase progress tidak valid.']);
        }


        $updateData =  [
            'progress_percentage' => $progressPercentage,
            'updated_at' => date('Y-m-d H:i:s'),

        ];

        if ($progressPercentage > 0) {
            $updateData['status'] = 'in-progress';
        }
        // Update data
        $projectModel->update($projectId, $updateData);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Progress berhasil diperbarui.']);
    }
}