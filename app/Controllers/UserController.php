<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;
use App\Libraries\Hash;
use SSP;


class UserController extends BaseController
{
    protected $helpers = ['url', 'form'];
    protected $db;
    public function __construct()
    {
        require_once APPPATH . 'ThirdParty/ssp.php';
        $this->db = db_connect();
        $this->sql_details = [
            'user' => env('database.default.username'),
            'pass' => env('database.default.password'),
            'db'   => env('database.default.database'),
            'host' => env('database.default.hostname'),
        ];
    }

    public function pageUsers()
    {
        $data = [
            'pageTitle' => 'Manajemen Data Users'
        ];
        return view('backend/pages/pageUsers', $data);
    }

    public function addUser()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();

            $rules = [
                'name' => 'required',
                'username' => 'required|is_unique[users.username]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required',
                'role' => 'required|in_list[kontraktor,konsultan]'
            ];

            $messages = [
                'name' => [
                    'required' => 'Nama wajib diisi.'
                ],
                'username' => [
                    'required' => 'Username wajib diisi.',
                    'is_unique' => 'Username sudah digunakan.'
                ],
                'email' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email sudah digunakan.'
                ],
                'password' => [
                    'required' => 'Password wajib diisi.'
                ],
                'role' => [
                    'required' => 'Role wajib dipilih.',
                    'in_list' => 'Role tidak valid.'
                ]
            ];

            $validation->setRules($rules, $messages);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->response->setJSON([
                    'error' => $validation->getErrors()
                ]);
            }

            // Simpan data ke database
            $userModel = new User();

            $userModel->save([
                'name' => $this->request->getPost('name'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => Hash::make($this->request->getPost('password')),
                'role' => $this->request->getPost('role')
            ]);

            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Data pengguna berhasil ditambahkan.'
            ]);
        }

        // Jika bukan AJAX
        return redirect()->back();
    }

    public function datatablesUser()
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
                'db' => 'username',
                'dt' => 2,
            ],
            [
                'db' => 'email',
                'dt' => 3,
            ],
            [
                'db' => 'role',
                'dt' => 4,
                'formatter' => function ($d, $row) {
                    return strtoupper($d);
                }
            ],
            [
                'db' => 'id',
                'dt' => 5,
                'formatter' => function ($d, $row) {
                    return '
                    <div class="btn-group">
                    <button data-id=' . $d . ' class="btn btn-sm btn-warning btn-edit-user" title="edit">
                    <i class="fa fa-edit"></i>
                    </button> ' .
                        '<button data-id="' . $d . '" class="btn btn-sm btn-danger btn-delete btn-delete-user" title="hapus">
                        <i class="fa fa-trash"></i>
                        </button>
                        </div>';
                }
            ],
        ];

        return $this->response->setJSON(SSP::simple($_GET, $this->sql_details, 'users', 'id', $columns));
    }

    public function dataUser()
    {
        $request = \Config\Services::request();

        if ($request->isAJAX()) {
            $id = $request->getVar('id');
            $userModel = new User();

            $data = $userModel->find($id);
            return $this->response->setJSON(['data' => $data]);
        }
    }

    public function dataUserUpdate()
    {
        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $id = $this->request->getPost('id');

            // Ambil user yang sedang diupdate
            $userModel = new User();
            $user = $userModel->find($id);

            if (!$user) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'Data pengguna tidak ditemukan.'
                ]);
            }

            $rules = [
                'name' => 'required',
                'username' => "required|is_unique[users.username,id,{$id}]",
                'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'role' => 'required|in_list[kontraktor,konsultan]'
            ];

            // Jika password diisi, tambahkan validasi
            if ($this->request->getPost('password')) {
                $rules['password'] = 'required';
            }

            $messages = [
                'name' => [
                    'required' => 'Nama wajib diisi.'
                ],
                'username' => [
                    'required' => 'Username wajib diisi.',
                    'is_unique' => 'Username sudah digunakan.'
                ],
                'email' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email sudah digunakan.'
                ],
                'password' => [
                    'required' => 'Password wajib diisi.'
                ],
                'role' => [
                    'required' => 'Role wajib dipilih.',
                    'in_list' => 'Role tidak valid.'
                ]
            ];

            $validation->setRules($rules, $messages);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->response->setJSON([
                    'error' => $validation->getErrors()
                ]);
            }

            $updateData = [
                'name' => $this->request->getPost('name'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('role'),
            ];

            if ($this->request->getPost('password')) {
                $updateData['password'] = Hash::make($this->request->getPost('password'));
            }

            $userModel->update($id, $updateData);

            return $this->response->setJSON([
                'status' => 1,
                'msg' => 'Data pengguna berhasil diperbarui.',

            ]);
        }

        return redirect()->back();
    }
    public function dataUserDrop()
    {
        $id = $this->request->getGet('id');

        if ($id == 2) {
            // User dengan id 1 tidak bisa dihapus
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User tidak bisa di hapus'
            ]);
        }

        $userModel = new User();

        if ($userModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus user'
            ]);
        }
    }
}