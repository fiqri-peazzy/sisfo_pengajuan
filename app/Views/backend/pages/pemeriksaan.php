<?= $this->extend('backend/layout/page-layout') ?>

<?= $this->section('content') ?>
<style>
/* --- CSS Styling sudah Anda berikan, saya sertakan ulang di sini --- */
/* Styling utama card */
.card {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 20px;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    border: none;
    position: relative;
}

/* Efek hover pada card */
.card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
}

/* Judul card */
.card .card-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 6px;
}

/* Subtitle (nama kontraktor) */
.card .card-subtitle {
    font-size: 1rem;
    color: #7f8c8d;
    margin-bottom: 12px;
}

/* Paragraf informasi */
.card p {
    font-size: 0.95rem;
    color: #34495e;
    margin-bottom: 8px;
}

/* Badge status */
.badge {
    font-size: 0.85rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Warna badge berdasarkan status */
.badge-warning {
    background-color: #f39c12;
    color: #fff;
}

.badge-success {
    background-color: #27ae60;
    color: #fff;
}

.badge-danger {
    background-color: #c0392b;
    color: #fff;
}

/* Container tombol aksi */
.card .d-flex.justify-content-end {
    margin-top: 15px;
}

/* Tombol aksi */
.card .btn {
    font-size: 0.9rem;
    padding: 8px 14px;
    border-radius: 8px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

/* Warna tombol */
.btn-primary {
    background-color: #2980b9;
    border: none;
    color: #fff;
}

.btn-primary:hover {
    background-color: #1c5980;
    box-shadow: 0 4px 12px rgba(41, 128, 185, 0.6);
}

.btn-danger {
    background-color: #e74c3c;
    border: none;
    color: #fff;
}

.btn-danger:hover {
    background-color: #b73225;
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.6);
}

.btn-info {
    background-color: #3498db;
    border: none;
    color: #fff;
}

.btn-info:hover {
    background-color: #217dbb;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.6);
}

/* Margin kanan untuk tombol pertama */
.btn+.btn {
    margin-left: 10px;
}

/* Responsif: card full width di layar kecil */
@media (max-width: 576px) {
    .card {
        padding: 15px;
    }
}

/* Filter bar styling */
.filter-bar {
    margin-bottom: 20px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-bar button {
    background-color: #ecf0f1;
    border: none;
    padding: 10px 18px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    color: #34495e;
}

.filter-bar button.active,
.filter-bar button:hover {
    background-color: #2980b9;
    color: white;
}

/* Tombol periksa (+ icon) di pojok kanan bawah card */
.btn-check {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background-color: #27ae60;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(39, 174, 96, 0.5);
    transition: background-color 0.3s ease;
}

.preview-iframe-container {
    background: #fff;
    border: 1px solid #ddd;
    padding: 0;
    position: relative;
    border-radius: 4px;
}

.preview-iframe-container .btn-close {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    width: 28px;
    height: 28px;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
}


.btn-check:hover {
    background-color: #1e8449;
}
</style>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pemeriksaan Kuantitas dan Kualitas</h5>
                    <p class="m-b-0">Verifikasi dokumen dan kualitas pekerjaan oleh Konsultan</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('home') ?>"> <i class="fa fa-home"></i> Beranda </a>
                    </li>
                    <li class="breadcrumb-item active">Pemeriksaan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">
                <div class="card">
                    <div class="card-header p-1">
                        <div class="filter-bar">
                            <button class="filter-btn active" data-filter="all">Semua</button>
                            <button class="filter-btn" data-filter="pending">Belum Diperiksa</button>
                            <button class="filter-btn" data-filter="rejected">Ditolak</button>
                            <button class="filter-btn" data-filter="approved">Disetujui</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="card-container">

                            <?php
                            // Mengelompokkan data berdasarkan bulan dan tahun
                            $groupedFiles = [];
                            foreach ($files as $file) {
                                $key = $file['tahun'] . '-' . str_pad($file['bulan'], 2, '0', STR_PAD_LEFT);
                                if (!isset($groupedFiles[$key])) {
                                    $groupedFiles[$key] = [];
                                }
                                $groupedFiles[$key][] = $file;
                            }

                            // Sort bulan dari yang terbaru ke terlama (opsional)
                            krsort($groupedFiles);

                            // Menampilkan data berdasarkan kelompok bulan
                            foreach ($groupedFiles as $monthYear => $filesInMonth) :
                                // Pecah key bulan dan tahun
                                list($tahun, $bulan) = explode('-', $monthYear);
                            ?>
                            <div class="col-12 mb-3">
                                <h4><?= mapping_month(intval($bulan)) ?> <?= $tahun ?></h4>
                                <hr>
                            </div>

                            <?php foreach ($filesInMonth as $file) : ?>
                            <div class="col-lg-6 col-md-12 mb-4 card-item" data-status="<?= esc($file['status']) ?>">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <h5 class="card-title mb-2"><?= esc($file['project_name']) ?></h5>
                                        <h6 class="card-subtitle mb-2 mt-2 text-muted">
                                            <?= esc($file['contractor_name']) ?></h6>
                                        <p class="mb-1">
                                            <strong>Tanggal Upload :</strong>
                                            <?= date('d M Y', strtotime($file['created_at'])) ?>
                                        </p>
                                        <p class="mb-3">
                                            <strong>Bulan/Tahun :</strong>
                                            <?= mapping_month($file['bulan']) ?> <?= $file['tahun'] ?>
                                        </p>
                                        <p>
                                            <?php if ($file['status'] == 'pending') : ?>
                                            <span class="badge badge-warning">Menunggu Verifikasi</span>
                                            <?php elseif ($file['status'] == 'approved') : ?>
                                            <span class="badge badge-success">Terverifikasi</span>
                                            <?php elseif ($file['status'] == 'rejected') : ?>
                                            <span class="badge badge-danger">Ditolak</span>
                                            <?php else : ?>
                                            <span class="badge badge-secondary"><?= esc($file['status']) ?></span>
                                            <?php endif; ?>
                                        </p>
                                        <button class="btn-check" title="Periksa" data-id="<?= esc($file['id']) ?>"
                                            id="btn-periksa-file">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <?php endforeach; ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pemeriksaan -->
<div class="modal fade" id="modalPemeriksaan" tabindex="-1" aria-labelledby="modalPemeriksaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formPemeriksaan">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPemeriksaanLabel">Pemeriksaan File oleh Konsultan</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="file_id" id="file_id" value="">

                    <div id="previewFiles" class="mb-4">
                        <!-- Preview file akan dimuat di sini -->
                    </div>

                    <hr>
                    <div id="dokumenPendukungSection" class="mb-4"></div>
                    <hr>

                    <div id="approvalSection">
                        <!-- Dinamis: untuk setiap file ada opsi approve/reject dan catatan revisi jika reject -->
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Pemeriksaan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// JavaScript untuk filter bar
document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', () => {
        // Hapus kelas active dari semua tombol
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        // Tambah kelas active pada tombol yang diklik
        button.classList.add('active');

        const filter = button.getAttribute('data-filter');
        const cards = document.querySelectorAll('.card-item');

        cards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-status') === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

$(document).on('click', '#btn-periksa-file', function() {
    var fileId = $(this).data('id');
    var url = '<?= site_url('get_file_pemeriksaan/') ?>' + fileId;
    $('#file_id').val(fileId);

    // Reset modal content
    $('#previewFiles').html('<p>Memuat file...</p>');
    $('#approvalSection').html('');

    // Tampilkan modal
    $('#modalPemeriksaan').modal('show');

    // Ambil data file dan pemeriksaan via AJAX
    $.getJSON(url, function(response) {
        if (response.success) {
            var files = response.files;
            var pemeriksaan = response.pemeriksaan;
            var dokumenPendukung = response.dokumen_pendukung;


            var previewHtml =
                '<h5 style="margin-bottom:20px;">Daftar File Kontraktor</h5><div class="list-group">';
            $.each(files, function(key, file) {
                if (file.url !== null) {
                    previewHtml += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold">${key.replace(/_/g, ' ').toUpperCase()}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-lihat-file" data-key="${key}" data-url="${file.url}">
                            Lihat File
                        </button>
                    </div>
                    <div class="preview-iframe-container mt-2 mb-3" id="preview-${key}" style="display:none; position: relative;">
                        <button type="button" class="btn-close btn-tutup-preview" aria-label="Close" style="position: absolute; top: 5px; right: 10px; z-index: 10;"></button>
                        <iframe src="${file.url}" style="width:100%; height:400px; border:1px solid #ddd;" frameborder="0"></iframe>
                    </div>
                `;
                } else {
                    previewHtml +=
                        `<div> Laporan ${key} Tidak Ditemukan Atau Belum Diupload oleh Kontraktor`;
                }

            });
            previewHtml += '</div>';
            $('#previewFiles').html(previewHtml);


            if (dokumenPendukung.length > 0) {
                var dokumenHtml = '<h5 class="mt-3 mb-3">Dokumen Pendukung</h5><ul class="list-group">';
                $.each(dokumenPendukung, function(i, dokumen) {
                    dokumenHtml += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${dokumen.nama_file} (${dokumen.tipe_dokumen})</span>
                        <a href="${dokumen.url}" target="_blank" class="btn btn-sm btn-info">Lihat Dokumen</a>
                    </li>`;
                });
                dokumenHtml += '</ul>';
                $('#dokumenPendukungSection').html(dokumenHtml);
            } else {
                $('#dokumenPendukungSection').html(
                    '<p class="text-muted">Tidak ada dokumen pendukung.</p>');
            }
            var approvalHtml = '<h5 class="mb-3">Approval Pemeriksaan</h5>';
            $.each(files, function(key, file) {
                // Cek status pemeriksaan sebelumnya untuk file ini (jika ada)
                var prevStatus = '';
                var prevCatatan = '';
                if (pemeriksaan && pemeriksaan[key]) {
                    prevStatus = pemeriksaan[key].status_pemeriksaan;
                    prevCatatan = pemeriksaan[key].catatan_umum || '';
                }

                approvalHtml += `
                    <div class="mb-3 border p-3 rounded">
                        <label class="form-label fw-bold">${key.replace(/_/g, ' ').toUpperCase()}</label>
                        <div>
                            <label><input type="radio" name="status_${key}" value="disetujui" ${prevStatus === 'disetujui' ? 'checked' : ''} required> Disetujui</label>
                            <label class="ms-3"><input type="radio" name="status_${key}" value="ditolak" ${prevStatus === 'ditolak' ? 'checked' : ''} required> Ditolak</label>
                        </div>
                        <div class="mt-2 catatan-revisi-container" style="display: ${prevStatus === 'ditolak' ? 'block' : 'none'};">
                            <label for="catatan_${key}" class="form-label">Catatan Revisi (wajib jika ditolak)</label>
                            <textarea class="form-control" id="catatan_${key}" name="catatan_${key}" rows="2" placeholder="Masukkan catatan revisi...">${prevCatatan}</textarea>
                        </div>
                    </div>
                `;
            });
            $('#approvalSection').html(approvalHtml);
        } else {
            $('#previewFiles').html('<p>Gagal memuat data file.</p>');
        }
    }).fail(function() {
        $('#previewFiles').html('<p>Terjadi kesalahan saat memuat data.</p>');
    });
});

// Event untuk tombol "Lihat File" - toggle preview iframe
$(document).on('click', '.btn-lihat-file', function() {
    var key = $(this).data('key');
    var previewContainer = $('#preview-' + key);

    if (previewContainer.is(':visible')) {
        previewContainer.slideUp();
        $(this).text('Lihat File');
    } else {
        // Tutup semua preview lain sebelum membuka yang ini
        $('.preview-iframe-container').slideUp();
        $('.btn-lihat-file').text('Lihat File');

        previewContainer.slideDown();
        $(this).text('Tutup Preview');
    }
});

// Event untuk tombol tutup preview (X)
$(document).on('click', '.btn-tutup-preview', function() {
    var previewContainer = $(this).closest('.preview-iframe-container');
    var key = previewContainer.attr('id').replace('preview-', '');
    previewContainer.slideUp();
    $('.btn-lihat-file[data-key="' + key + '"]').text('Lihat File');
});

// Tampilkan/ sembunyikan catatan revisi saat radio ditolak dipilih
$(document).on('change', 'input[type=radio]', function() {
    var name = $(this).attr('name');
    var val = $(this).val();
    var container = $(this).closest('.mb-3').find('.catatan-revisi-container');
    if (val === 'ditolak') {
        container.show();
        container.find('textarea').attr('required', true);
    } else {
        container.hide();
        container.find('textarea').removeAttr('required').val('');
    }
});

// Submit form pemeriksaan
$('#formPemeriksaan').on('submit', function(e) {
    e.preventDefault();

    // Validasi catatan revisi untuk file yang ditolak
    var valid = true;
    $('#approvalSection .mb-3').each(function() {
        var status = $(this).find('input[type=radio]:checked').val();
        if (status === 'ditolak') {
            var catatan = $(this).find('textarea').val().trim();
            if (!catatan) {
                toastr.error('Catatan revisi wajib diisi untuk file yang ditolak.');
                valid = false;
                return false; // break each
            }
        }
    });
    if (!valid) return;

    var formData = $(this).serialize();

    $.post('<?= route_to('save_multiple') ?>', formData, function(response) {
        if (response.success) {
            toastr.success('Pemeriksaan berhasil disimpan!');
            $('#modalPemeriksaan').modal('hide');
            location.reload();
        } else {
            alert('Gagal menyimpan pemeriksaan: ' + response.message);
        }
    }, 'json').fail(function() {
        alert('Terjadi kesalahan saat menyimpan pemeriksaan.');
    });
});
</script>

<?= $this->endSection('scripts') ?>