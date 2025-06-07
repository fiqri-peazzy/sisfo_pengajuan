<?= $this->extend('backend/layout/page-layout') ?>


<?= $this->section('stylesheets') ?>
<link href="https://cdn.datatables.net/v/dt/dt-2.3.1/fc-5.0.4/fh-4.0.2/datatables.min.css" rel="stylesheet"
    integrity="sha384-caazROfB1e3HFiqbbU0KT9TYMkLdmZXxttH1zaS9CzDwkEQC0uykbKZ1wXdj1wX1" crossorigin="anonymous">

<?= $this->endSection('stylesheets') ?>
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
                    <h5 class="m-b-10">Data Pengguna</h5>
                    <p class="m-b-0">Manajemen Pengguna Terkait Sistem Pengajuan</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('home') ?>"> <i class="fa fa-home"></i> Beranda </a>
                    </li>
                    <li class="breadcrumb-item active">Data Pengguna</li>
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
                    <div class="card-header text-right">
                        <button class="btn btn-success btn-add-users"><i class="fas fa-plus-circle"></i> Tambah</button>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">

                            <table class="table table-sm table-borderless table-hover table-strip" id="table-users">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Lengkap</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>role</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahUser" method="post" action="<?= route_to('add_user') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahUserLabel">Tambah Data Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12 form-group">
                            <label for="name">Nama</label>
                            <input type="text" class="form-control" name="name" id="name">
                            <span class="error-text text-danger name_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" id="username">
                            <span class="error-text text-danger username_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                            <span class="error-text text-danger email_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                            <span class="error-text text-danger password_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="role">Role</label>
                            <select class="form-control" name="role" id="role">
                                <option value="">-- Pilih Role --</option>
                                <option value="kontraktor">Kontraktor</option>
                                <option value="konsultan">Konsultan</option>

                            </select>
                            <span class="error-text text-danger role_error"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditUser" method="post" action="<?= route_to('data.user.update') ?>">
                <input type="hidden" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditUserLabel">Edit Data Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12 form-group">
                            <label for="name">Nama</label>
                            <input type="text" class="form-control" name="name" id="name">
                            <span class="error-text text-danger name_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" id="username">
                            <span class="error-text text-danger username_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                            <span class="error-text text-danger email_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control"
                                placeholder="Kosongkan jika tidak ingin mengubah password" name="password"
                                id="password">
                            <span class="error-text text-danger password_error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="role" id="label-role">Role</label>
                            <select class="form-control" name="role" id="role">
                                <option value="">-- Pilih Role --</option>
                                <option value="kontraktor">Kontraktor</option>
                                <option value="konsultan">Konsultan</option>
                            </select>
                            <span class="error-text text-danger role_error"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>


<script src="https://cdn.datatables.net/v/dt/dt-2.3.1/fc-5.0.4/fh-4.0.2/datatables.min.js"
    integrity="sha384-QF85snFseJk+/+vSZCJWTrU5I/JPW7OdWO+PMLUdNeRn7J84G13jwD4CxiXuv+4K" crossorigin="anonymous">
</script>
<script>
$('.btn-add-users').on('click', function() {

    var modal = $('body').find('div#modalTambahUser');
    modal.find('#formTambahUser')[0].reset();
    modal.modal('show');

});

$('#formTambahUser').on('submit', function(e) {
    e.preventDefault();
    var form = this;
    var formData = new FormData(form);

    var modal = $('body').find('div#modalTambahUser');

    $.ajax({
        url: $(form).attr('action'),
        method: 'post',
        data: formData,
        processData: false,
        dataType: 'json',
        contentType: false,
        cache: false,
        beforeSend: function() {
            toastr.remove();
            $(form).find('span.error-text').text('');
        },
        success: function(response) {
            if ($.isEmptyObject(response.error)) {
                if (response.status == 1) {
                    $(form)[0].reset();
                    modal.modal('hide');

                    toastr.success(response.msg);
                    if (typeof user_DT !== 'undefined') {
                        user_DT.ajax.reload(null, false);
                    }
                } else {
                    toastr.error(response.msg);
                }
            } else {
                $.each(response.error, function(prefix, val) {
                    $(form).find('span.' + prefix + '_error').text(val);
                });
            }
        }
    });
});


var user_DT = $('#table-users').DataTable({
    processing: true,
    serverSide: true,
    ajax: "<?= route_to('datatable_user') ?>",
    dom: "Brtip",
    info: false,
    ordering: false,

    fnCreatedRow: function(row, data, index) {
        $('td', row).eq(0).html(index + 1);
    },

});


$(document).on('click', '.btn-edit-user', function() {
    var modal = $('body').find('div#modalEditUser');
    var url = '<?= route_to('get.user') ?>';
    var id = $(this).data('id');

    $.getJSON(url, {
        id: id
    }, function(response) {
        modal.find('input[name="id"]').val(response.data.id);
        modal.find('input[name="name"]').val(response.data.name);
        modal.find('input[name="username"]').val(response.data.username);
        modal.find('input[name="email"]').val(response.data.email);
        if (response.data.role != 'ppk') {
            modal.find('select[name="role"]').val(response.data.role);

        } else {
            modal.find('select[name="role"]').remove();
            modal.find('label#label-role').text(' ');

        }
        modal.modal('show');
    });
});

$(document).on('submit', '#formEditUser', function(e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);
    var modal = $('body').find('div#modalEditUser');

    $.ajax({
        url: $(form).attr('action'),
        method: 'post',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        cache: false,
        beforeSend: function() {
            toastr.remove();
            $(form).find('span.error-text').text('');
        },
        success: function(response) {
            if ($.isEmptyObject(response.error)) {
                if (response.status == 1) {
                    $(form)[0].reset();
                    modal.modal('hide');
                    toastr.success(response.msg);
                    if (typeof user_DT !== 'undefined') {
                        user_DT.ajax.reload(null, false); // reload DataTable jika digunakan
                    }
                } else {
                    toastr.error(response.msg);
                }
            } else {
                $.each(response.error, function(prefix, val) {
                    $(form).find('span.' + prefix + '_error').text(val);
                });
            }
        }
    });
});

$(document).on('click', '.btn-delete-user', function(e) {
    e.preventDefault();
    var userId = $(this).data('id');

    swal({
        title: 'Apakah Anda yakin?',
        text: "Data user akan dihapus!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '<?= route_to('data.user.drop') ?>',
                type: 'GET',
                data: {
                    id: userId
                },
                success: function(response) {
                    if (response.status === 'error') {
                        toastr.error(response.message);
                    } else {
                        toastr.success(response.message);
                        if (typeof user_DT !== 'undefined') {
                            user_DT.ajax.reload(null,
                                false); // reload DataTable jika digunakan
                        }
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat menghapus data.');
                }
            });
        }
    });
})
</script>
<?= $this->endSection('scripts') ?>