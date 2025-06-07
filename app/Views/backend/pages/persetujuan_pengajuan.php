<?= $this->extend('backend/layout/page-layout') ?>

<?= $this->section('stylesheets') ?>
<style>
.tippy-box[data-theme~='custom-light'] {
    background-color: #f9f9f9;
    /* Warna latar belakang yang lembut */
    color: #333;
    /* Warna teks yang kontras */
    font-size: 14px;
    /* Ukuran font yang nyaman dibaca */
    padding: 12px 16px;
    /* Padding agar isi tooltip tidak mepet */
    border-radius: 8px;
    /* Sudut tooltip yang membulat */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    /* Bayangan halus */
    border: 1px solid #ddd;
    /* Border tipis untuk definisi */
    line-height: 1.4;
    /* Jarak antar baris */
}

/* Styling untuk judul dan label di dalam tooltip */
.tippy-box[data-theme~='custom-light'] strong {
    color: #007bff;
    /* Warna biru untuk highlight */
}

/* Styling untuk ikon info agar terlihat interaktif */
.status-info {
    /* font-weight: bold; */
    color: #007bff;
    cursor: pointer;
    user-select: none;
    transition: color 0.3s ease;
}

.status-info:hover {
    color: #0056b3;
}
</style>
<?= $this->endSection('stylesheets') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard SPK - Sistem Informasi Pengajuan Pembayaran Konstruksi</h5>
                    <p class="m-b-0">Sistem Informasi untuk memonitor dan mengelola pengajuan pembayaran prestasi
                        pekerjaan konstruksi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('home') ?>"> <i class="fa fa-home"></i> Beranda </a>
                    </li>
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
                    <div class="card-body">
                        <h5>Hasil Pemeriksaan</h5>
                        <div class="table-responsive">
                            <table id="tableHasilPemeriksaan" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Project</th>
                                        <th>Bulan/Tahun</th>
                                        <th>Dokumen</th>

                                        <th>Dokumen Pendukung</th>
                                        <th>Status Sertifikat</th>
                                        <th>Pemeriksa</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan diisi oleh JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPersetujuan" tabindex="-1" role="dialog" aria-labelledby="modalPersetujuanLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formPersetujuan">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Persetujuan Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sertifikatId" name="sertifikat_id" />
                    <div class="form-group">
                        <label>Status Persetujuan</label>
                        <select class="form-control" id="status" name="status">
                            <option value="" selected>--pilih</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea class="form-control" id="catatanUmum" name="catatan_umum" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection('content') ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
const base_url = '<?= base_url() ?>';

function renderStatus(status, fileName, jenis_file) {
    if (!fileName) return '<em>File belum diupload</em>';
    if (status === 'disetujui') return '<a href="' + base_url + fileName +
        '"><span style="color:green;">✔️ ' + jenis_file + '</span></a>';
    if (status === 'ditolak') return '<a href="' + base_url + fileName + '"><span style="color:red;">❌ ' +
        jenis_file + '</span></a>';
    if (status === '') return '<span style="color:orange;">⚠️ ' + nama_file + '</span>';
    return fileName;
}

// Load data dari API
$.ajax({
    url: '<?= route_to('apiHasilPemeriksaan') ?>',
    method: 'GET',
    dataType: 'json',
    success: function(res) {
        if (res.status) {
            let data = res.data;
            let tbody = '';
            data.forEach((item, index) => {
                let dokumenPendukungHtml = '';
                if (item.dokumen_pendukung.length > 0) {
                    item.dokumen_pendukung.forEach(doc => {
                        dokumenPendukungHtml +=
                            `<a href="<?= base_url() ?>/${doc.path_file}" target="_blank">${doc.nama_file}</a><br>`;
                    });
                } else {
                    dokumenPendukungHtml = '<em>Tidak ada dokumen pendukung</em>';
                }

                function renderStatusWithInfo(file, filePath, label) {
                    if (!filePath) return '<em>File belum diupload</em>';
                    if (!file) return '<em>Belum diperiksa</em>';
                    return `<span class="status-info" data-tippy-content="Diperiksa Pada : ${file.tanggal_pemeriksaan || '-'}<br>Catatan: ${file.catatan_umum || '-'}">` +
                        renderStatus(file.status_pemeriksaan, filePath, label) + '</span>';
                }

                let statusKuantitas = renderStatusWithInfo(item.pemeriksaan.file_kuantitas, item
                    .file_kuantitas, 'File Kuantitas');
                let statusKualitas = renderStatusWithInfo(item.pemeriksaan.file_kualitas, item
                    .file_kualitas, 'File Kualitas');
                let statusSertifikat = renderStatusWithInfo(item.pemeriksaan.sertifikat_bulanan,
                    item.file_sertifikat, 'Sertifikat Bulanan');

                tbody += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.project_name}</td>
                    <td>${item.bulan} / ${item.tahun}</td>
                    <td>
                        <ul>
                            <li>${statusSertifikat}</li>
                            <li>${statusKualitas}</li>
                            <li>${statusKuantitas}</li>
                        </ul>
                    </td>
                    <td>${dokumenPendukungHtml}</td>
                    <td>
                        <span class="badge ${
                            item.status_sertifikat.toLowerCase() === 'approved' ? 'badge-primary' :
                            item.status_sertifikat.toLowerCase() === 'rejected' ? 'badge-danger' :
                            'badge-warning'
                        }">${item.status_sertifikat}</span>
                    </td>
                    <td>${item.pemeriksa}</td>
                    <td>${item.tanggal_pengajuan ? item.tanggal_pengajuan : '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-persetujuan" data-id="${item.sertifikat_id}">Persetujuan</button>
                    </td>
                </tr>
                `;
            });
            $('#tableHasilPemeriksaan tbody').html(tbody);

            // Inisialisasi tippy.js untuk tooltip info pemeriksaan
            tippy('.status-info', {
                allowHTML: true,
                theme: 'custom-light',
                placement: 'top',
                interactive: true,
                animation: 'scale',
                delay: [100, 200],
                maxWidth: 320,
                arrow: true,
            });

        } else {
            alert('Gagal memuat data hasil pemeriksaan.');
        }
    },
    error: function() {
        alert('Terjadi kesalahan saat memuat data.');
    }
});


// Submit form persetujuan
$('#formPersetujuan').submit(function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    $.ajax({
        url: '<?= route_to('apiUpdatePersetujuan') ?>',
        method: 'POST',
        data: formData,
        success: function(res) {
            if (res.status) {
                toastr.success('Persetujuan berhasil disimpan.');
                $('#modalPersetujuan').modal('hide');
                location.reload(); // Reload halaman untuk update data
            } else {
                toastr.error('Gagal menyimpan persetujuan.');
            }
        },
        error: function() {
            toastr.error('Terjadi kesalahan saat menyimpan persetujuan.');
        }
    });
});


$(document).on('click', '.btn-persetujuan', function() {

    let sertifikatId = $(this).data('id');
    var url = '<?= route_to('get_status') ?>';
    var modal = $('body').find('div#modalPersetujuan');
    $.getJSON(url, {
        sertifikatId: sertifikatId
    }, function(response) {
        modal.find('#sertifikatId').val(response.data.id);
        modal.find('select[name="status"]').val(response.data.status);
        $('#catatanUmum').val(response.data.catatan_penolakan !== null ? response.data
            .catatan_penolakan : ' ');
        modal.modal('show');
    });


});
</script>
<?= $this->endSection('scripts') ?>