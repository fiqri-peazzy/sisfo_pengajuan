<?= $this->extend('backend/layout/page-layout') ?>
<?= $this->section('stylesheets') ?>
<link href="https://cdn.datatables.net/v/bs5/dt-2.3.1/fc-5.0.4/fh-4.0.2/r-3.0.4/datatables.min.css" rel="stylesheet"
    integrity="sha384-FHQoEKeXM8RLelSpnbDMQg5HVyDoS5YyIbZB4B4LP9oJS6hIj7++I894iGrDp7VD" crossorigin="anonymous">

<?= $this->endSection('stylesheets') ?>
<?= $this->section('content') ?>
<style>
.file-list {
    padding: 0;
    margin: 0;
}

.file-item {
    margin: 5px 0;
}

.file-link {
    text-decoration: none;
    color: #007bff;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.3s ease;
}

.file-link:hover {
    text-decoration: underline;
    color: #0056b3;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
}

.file-link i {
    font-size: 16px;
}

/* Warna ikon sesuai jenis file */
.file-link .fa-file-pdf {
    color: #dc3545;
    /* Merah untuk ikon PDF */
}

.file-link .fa-chart-bar {
    color: #17a2b8;
    /* Biru untuk ikon chart */
}

.file-link .fa-clipboard-check {
    color: #28a745;
    /* Hijau untuk ikon checklist */
}

.project-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    cursor: pointer;
}

.project-card:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.project-title {
    font-weight: 600;
    font-size: 1.25rem;
    margin-bottom: 8px;
}

.project-location {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 12px;
}

.btn-upload {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
}

.btn-upload:hover {
    background-color: #0056b3;
}

.btn-upload i {
    font-size: 1.1rem;
}


.card {
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    padding: 25px;
    background-color: #fff;
}

.form-label {
    font-weight: 600;
    color: #333;
}

.btn-submit {
    background-color: #4e73df;
    border: none;
    padding: 10px 25px;
    font-weight: 600;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.btn-submit:hover {
    background-color: #224abe;
}

.file-input {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 6px 12px;
    width: 100%;
}

/* Table styling */
#tableRiwayatSertifikat th,
#tableRiwayatSertifikat td {
    vertical-align: middle;
}

/* Badge status */
.badge-status {
    padding: 5px 10px;
    border-radius: 12px;
    font-weight: 600;
    color: white;
    display: inline-block;
    min-width: 90px;
    text-align: center;
}

.badge-pending {
    background-color: #dc3545;
    /* red */
}

.badge-inprogress {
    background-color: #007bff;
    /* blue */
}

.badge-completed {
    background-color: #28a745;
    /* green */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .page-header-title h5 {
        font-size: 1.4rem;
    }

    .btn-submit {
        width: 100%;
    }
}
</style>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pengajuan Sertifikat Bulanan Kontraktor</h5>
                    <p class="m-b-0">Formulir pengajuan sertifikat bulanan untuk memudahkan pelaporan dan monitoring
                        progres pekerjaan konstruksi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb-title float-md-end">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('home') ?>"> <i class="fa fa-home"></i> Beranda </a>
                    </li>
                    <li class="breadcrumb-item active">Pengajuan Sertifikat Bulanan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">

                <!-- Form Pengajuan Sertifikat Bulanan -->
                <div class="card">
                    <div class="card-header">
                        <h5>Pengajuan Sertifikat Bulanan</h5>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <?php foreach ($projects as $project) : ?>
                            <div class="col-md-4">
                                <div class="project-card">
                                    <div class="project-title"><?= $project->name ?></div>
                                    <div class="project-location"><?= $project->location ?></div>
                                    <div><small>Periode: <?= $project->start_date ?> s/d
                                            <?= $project->end_date ?></small></div>
                                    <button class="btn-upload mt-3 btnPengajuan" data-project-id="<?= $project->id ?>">
                                        <i class="bi bi-upload"></i> Ajukan Sertifikat
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Tabel Riwayat Pengajuan Sertifikat -->
                <div class="card mt-4">
                    <h5 class="mb-3">Riwayat Pengajuan Sertifikat Bulanan</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="tableRiwayatSertifikat">
                            <thead class="bg-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Project</th>
                                    <th>Bulan</th>
                                    <th>Dokumen</th>
                                    <th>Dokumen Pendukung</th>
                                    <th>Status</th>

                                    <th>Tanggal Pengajuan</th>
                                    <th>Keterangan</th>

                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi secara dinamis via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPengajuanSertifikat" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formPengajuanSertifikat" enctype="multipart/form-data" method="post"
            action="<?= route_to('submit_sertifikat') ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Pengajuan Sertifikat Bulanan</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="project_id" name="project_id" value="">

                    <div class="mb-3">
                        <label for="bulan" class="form-label">Bulan Pengajuan</label>
                        <input type="month" class="form-control" id="bulan" name="bulan" required>
                    </div>

                    <div class="mb-3">
                        <label for="fileSertifikat" class="form-label">Upload File Sertifikat (PDF)</label>
                        <input type="file" class="form-control" id="fileSertifikat" name="fileSertifikat"
                            accept="application/pdf">
                    </div>

                    <div id="previewSertifikat" class="mb-3"
                        style="border:1px solid #ddd; padding:10px; max-height:320px; overflow:auto;">
                        <!-- Link preview file sertifikat akan muncul di sini -->
                    </div>

                    <div class="mb-3">
                        <label for="fileKuantitas" class="form-label">Upload File Kuantitas (PDF)</label>
                        <input type="file" class="form-control" id="fileKuantitas" name="fileKuantitas"
                            accept="application/pdf">
                    </div>

                    <div id="previewKuantitas" class="mb-3"
                        style="border:1px solid #ddd; padding:10px; max-height:320px; overflow:auto;">
                        <!-- Link preview file kuantitas akan muncul di sini -->
                    </div>

                    <div class="mb-3">
                        <label for="fileKualitas" class="form-label">Upload File Kualitas (PDF)</label>
                        <input type="file" class="form-control" id="fileKualitas" name="fileKualitas"
                            accept="application/pdf">
                    </div>

                    <div id="previewKualitas" class="mb-3"
                        style="border:1px solid #ddd; padding:10px; max-height:320px; overflow:auto;">
                        <!-- Link preview file kualitas akan muncul di sini -->
                    </div>

                    <div class="mb-3">
                        <label for="filePendukung" class="form-label">Upload Dokumen Pendukung Lainnya (Opsional,
                            PDF/Excel/Word)</label>
                        <input type="file" class="form-control" id="filePendukung" name="filePendukung[]" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <small class="form-text text-muted">Anda dapat mengupload lebih dari satu file.</small>
                    </div>

                    <div id="previewPendukung" class="mb-3"
                        style="border:1px solid #ddd; padding:10px; max-height:320px; overflow:auto;">
                        <!-- Preview dokumen pendukung PDF akan muncul di sini -->
                    </div>

                    <div id="previewMessage" class="text-muted mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection('content') ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/v/bs5/dt-2.3.1/fc-5.0.4/fh-4.0.2/r-3.0.4/datatables.min.js"
    integrity="sha384-UlpiJ5VTeja8kTxd8Wwmf7CBqwEPRKb8iMCjUp5cn0BdJtf7gA/0/YlWi/yhlPWh" crossorigin="anonymous">
</script>
<script>
$('.btnPengajuan').on('click', function() {
    var project_id = $(this).data('project-id');
    var kontraktor_id = <?= get_user()->role == 'kontraktor' ? get_user()->id : 'null' ?>;

    var modal = $('#modalPengajuanSertifikat');
    modal.find('#project_id').val(project_id);

    // Bersihkan preview sebelumnya
    modal.find('#previewSertifikat').html('');
    modal.find('#previewKuantitas').html('');
    modal.find('#previewKualitas').html('');
    modal.find('#previewPendukung').html('');
    modal.find('#previewMessage').text('Memuat data...');

    // Tampilkan modal
    modal.modal('show');

    // AJAX request untuk cek file sertifikat dan dokumen pendukung
    $.getJSON("<?= route_to('cek_files_sertifikat') ?>", {
            project_id: project_id,
            kontraktor_id: kontraktor_id
        })
        .done(function(response) {
            if (response.success) {
                modal.find('#previewMessage').text('');

                // Fungsi untuk buat link preview dengan tombol tutup
                function createPreviewLink(label, url, container) {
                    if (!url) {
                        container.html('<p>Tidak ada file ' + label + ' sebelumnya.</p>');
                        return;
                    }
                    var html = `
                    <div class="preview-link-container" style="position: relative; margin-bottom: 10px;">
                        <a href="${url}" target="_blank" rel="noopener noreferrer">${label} - Klik untuk preview</a>
                        <button type="button" class="btn btn-sm btn-danger btn-close-preview" style="position: absolute; right: 0; top: 0;">&times;</button>
                    </div>
                `;
                    container.html(html);

                    // Event tombol tutup preview
                    container.find('.btn-close-preview').on('click', function() {
                        container.html('<p>Preview ' + label + ' ditutup.</p>');
                    });
                }

                // Preview file sertifikat, kuantitas, dan kualitas
                createPreviewLink('File Sertifikat', response.files.file_sertifikat, modal.find(
                    '#previewSertifikat'));
                createPreviewLink('File Kuantitas', response.files.file_kuantitas, modal.find(
                    '#previewKuantitas'));
                createPreviewLink('File Kualitas', response.files.file_kualitas, modal.find(
                    '#previewKualitas'));

                // Preview dokumen pendukung (jika ada)
                if (response.dokumen_pendukung && response.dokumen_pendukung.length > 0) {
                    var pendukungHtml = '';
                    response.dokumen_pendukung.forEach(function(doc) {
                        if (doc.tipe_dokumen === 'pdf') {
                            pendukungHtml += `
                            <div class="preview-link-container" style="position: relative; margin-bottom: 10px;">
                                <a href="${doc.path_file}" target="_blank" rel="noopener noreferrer">${doc.nama_file} - Klik untuk preview</a>
                                <button type="button" class="btn btn-sm btn-danger btn-close-preview" style="position: absolute; right: 0; top: 0;">&times;</button>
                            </div>
                        `;
                        } else {
                            pendukungHtml +=
                                `<div><strong>${doc.nama_file}</strong> (Preview hanya untuk PDF)</div>`;
                        }
                    });
                    modal.find('#previewPendukung').html(pendukungHtml);

                    // Event tombol tutup preview untuk dokumen pendukung
                    modal.find('#previewPendukung .btn-close-preview').on('click', function() {
                        $(this).parent().html('<p>Preview dokumen ditutup.</p>');
                    });
                } else {
                    modal.find('#previewPendukung').html('<p>Tidak ada dokumen pendukung sebelumnya.</p>');
                }

            } else {
                modal.find('#previewMessage').text('Belum ada pengajuan sertifikat sebelumnya.');
                modal.find('#previewSertifikat').html('');
                modal.find('#previewKuantitas').html('');
                modal.find('#previewKualitas').html('');
                modal.find('#previewPendukung').html('');
            }
        })
        .fail(function() {
            modal.find('#previewMessage').text('Gagal memuat data file.');
        });
});


$('#fileSertifikat').on('change', function() {
    const file = this.files[0];
    const previewContainer = $('#previewSertifikat');
    previewContainer.empty();

    if (file && file.type === 'application/pdf') {
        const fileURL = URL.createObjectURL(file);
        const iframe = $('<iframe>', {
            src: fileURL,
            width: '100%',
            height: '300px',
            frameborder: 0
        });
        previewContainer.append(iframe);
    } else {
        previewContainer.html('<p style="color:red;">File harus berupa PDF.</p>');
    }
});

// Preview dokumen pendukung (hanya nama file, bisa dikembangkan preview lebih lanjut)
$('#filePendukung').on('change', function() {
    const files = this.files;
    const previewContainer = $('#previewPendukung');
    previewContainer.empty();

    if (files.length === 0) {
        previewContainer.html('<p>Tidak ada file pendukung yang dipilih.</p>');
        return;
    }

    const list = $('<ul>');
    for (let i = 0; i < files.length; i++) {
        list.append($('<li>').text(files[i].name));
    }
    previewContainer.append(list);
});

// Submit form via AJAX
$('#formPengajuanSertifikat').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Tampilkan status upload
    $('#previewMessage').html('Mengirim pengajuan...');

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#previewMessage').html('<span style="color:green;">' + response.message +
                    '</span>');
                // Reset form dan preview jika perlu
                $('#formPengajuanSertifikat')[0].reset();
                $('#previewSertifikat').empty();
                $('#previewPendukung').empty();
                riwayat_DT.ajax.reload(null, false);
                setTimeout(() => {
                    $('#modalPengajuanSertifikat').modal('hide');
                }, 1500);


            } else {
                $('#previewMessage').html('<span style="color:red;">' + response.message +
                    '</span>');
            }
        },
        error: function(xhr, status, error) {
            $('#previewMessage').html('<span style="color:red;">Terjadi kesalahan: ' + error +
                '</span>');
        }
    });
});

var riwayat_DT = $('#tableRiwayatSertifikat').DataTable({
    processing: true,
    serverSide: true,
    ajax: "<?= route_to('tableRiwayatSertifikat_url') ?>",
    dom: "Brtip",
    info: false,
    ordering: false,

    fnCreatedRow: function(row, data, index) {
        $('td', row).eq(0).html(index + 1);
    },
});
</script>
<?= $this->endSection('scripts') ?>