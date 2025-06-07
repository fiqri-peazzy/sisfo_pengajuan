<?= $this->extend('backend/layout/page-layout') ?>

<?= $this->section('stylesheets') ?>
<link href="https://cdn.datatables.net/v/bs5/dt-2.3.1/fc-5.0.4/fh-4.0.2/r-3.0.4/datatables.min.css" rel="stylesheet"
    integrity="sha384-FHQoEKeXM8RLelSpnbDMQg5HVyDoS5YyIbZB4B4LP9oJS6hIj7++I894iGrDp7VD" crossorigin="anonymous">

<?= $this->endSection('stylesheets') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Manajemen Data Project</h5>
                    <p class="m-b-0">Kelola data proyek konstruksi: tambah, edit, dan hapus.</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('dashboard.home') ?>"> <i class="fa fa-home"></i> Beranda </a>
                    </li>
                    <li class="breadcrumb-item">Manajemen Project</li>
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
                    <div class="card-header">

                        <button class="btn btn-primary btn-sm" id="btnAddProject">
                            <i class="fa fa-plus"></i> Tambah Project
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Tabel Data Project -->
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless table-hover table-striped" id="projectsTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Project</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
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

<div class="modal fade" id="projectModal" tabindex="-1" role="dialog" aria-labelledby="projectModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="projectForm" method="post" action="<?= route_to('save_project') ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectModalLabel">Tambah Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Nama Project -->
                    <div class="form-group">
                        <label for="projectName">Nama Project</label>
                        <input type="text" class="form-control" id="projectName" name="name">
                        <span class="error-text text-danger name_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="projectDescription">Deskripsi Project</label>
                        <input type="text" class="form-control" id="projectDescription" name="description">
                        <span class="error-text text-danger description_error"></span>
                    </div>

                    <!-- Lokasi -->
                    <div class="form-group">
                        <label for="projectLocation">Lokasi</label>
                        <input type="text" class="form-control" id="projectLocation" name="location">
                        <span class="error-text text-danger location_error"></span>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="form-group">
                        <label for="projectStartDate">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="projectStartDate" name="start_date">
                        <span class="error-text text-danger start_date_error"></span>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="form-group">
                        <label for="projectEndDate">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="projectEndDate" name="end_date">
                        <span class="error-text text-danger end_date_error"></span>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="projectStatus">Status</label>
                        <select class="form-control" id="projectStatus" name="status">
                            <option value="pending">Pending</option>
                            <option value="in-progress">Progress</option>
                        </select>
                        <span class="error-text text-danger status_error"></span>
                    </div>
                    <div class="form-group" id="progressContainer" style="display:none;">
                        <label for="progressPercentage">Progress (%)</label>
                        <input type="number" class="form-control" id="progressPercentage" name="progress_percentage"
                            min="0" max="100" step="1" value="<?= set_value('progress_percentage') ?>">
                        <span class="error-text text-danger progress_percentage_error"></span>
                    </div>
                    <!-- Kontraktor -->
                    <div class="form-group">
                        <label for="kontraktorId">Kontraktor</label>
                        <select class="form-control" id="kontraktorId" name="kontraktor_id">
                            <option value="">-- Pilih Kontraktor --</option>
                            <?php foreach ($kontraktors as $kontraktor) : ?>
                            <option value="<?= $kontraktor['id'] ?>"><?= esc($kontraktor['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-text text-danger kontraktor_id_error"></span>
                    </div>

                    <!-- Konsultan -->
                    <div class="form-group">
                        <label for="konsultanId">Konsultan</label>
                        <select class="form-control" id="konsultanId" name="konsultan_id">
                            <option value="">-- Pilih Konsultan --</option>
                            <?php foreach ($konsultans as $konsultan) : ?>
                            <option value="<?= $konsultan['id'] ?>"><?= esc($konsultan['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-text text-danger konsultan_id_error"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveProject">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editProjectForm" method="post" action="<?= route_to('update_project') ?>">
            <input type="hidden" name="id" id="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Nama Project -->
                    <div class="form-group">
                        <label for="projectName">Nama Project</label>
                        <input type="text" class="form-control" id="projectName" value="<?= set_value('name') ?>"
                            name="name">
                        <span class="error-text text-danger name_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="projectDescription">Deskripsi Project</label>
                        <input type="text" class="form-control" id="projectDescription"
                            value="<?= set_value('description') ?>" name="description">
                        <span class="error-text text-danger description_error"></span>
                    </div>

                    <!-- Lokasi -->
                    <div class="form-group">
                        <label for="projectLocation">Lokasi</label>
                        <input type="text" class="form-control" id="projectLocation"
                            value="<?= set_value('location') ?>" name="location">
                        <span class="error-text text-danger location_error"></span>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="form-group">
                        <label for="projectStartDate">Tanggal Mulai</label>
                        <input type="date" class="form-control" value="<?= set_value('start_date') ?>"
                            id="projectStartDate" name="start_date">
                        <span class="error-text text-danger start_date_error"></span>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="form-group">
                        <label for="projectEndDate">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="projectEndDate" value="<?= set_value('end_date') ?>"
                            name="end_date">
                        <span class="error-text text-danger end_date_error"></span>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="projectStatus">Status</label>
                        <select class="form-control" id="projectStatus" value="<?= set_value('status')  ?>"
                            name="status">
                            <option value="pending">Pending</option>
                            <option value="in-progress">Progress</option>
                        </select>
                        <span class="error-text text-danger status_error"></span>
                    </div>
                    <div class="form-group" id="progressContainer" style="display:none;">
                        <label for="progressPercentage">Progress (%)</label>
                        <input type="number" class="form-control" id="progressPercentage" name="progress_percentage"
                            min="0" max="100" step="1" value="<?= set_value('progress_percentage') ?>">
                        <span class="error-text text-danger progress_percentage_error"></span>
                    </div>
                    <!-- Kontraktor -->
                    <div class="form-group">
                        <label for="kontraktorId">Kontraktor</label>
                        <select class="form-control" id="kontraktorId" value="<?= set_value('kontraktor_id') ?>"
                            name="kontraktor_id">
                            <option value="">-- Pilih Kontraktor --</option>
                            <?php foreach ($kontraktors as $kontraktor) : ?>
                            <option value="<?= $kontraktor['id'] ?>"><?= esc($kontraktor['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-text text-danger kontraktor_id_error"></span>
                    </div>

                    <!-- Konsultan -->
                    <div class="form-group">
                        <label for="konsultanId">Konsultan</label>
                        <select class="form-control" id="konsultanId" value="<?= set_value('konsultan_id') ?>"
                            name="konsultan_id">
                            <option value="">-- Pilih Konsultan --</option>
                            <?php foreach ($konsultans as $konsultan) : ?>
                            <option value="<?= $konsultan['id'] ?>"><?= esc($konsultan['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="error-text text-danger konsultan_id_error"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveProject">Simpan</button>
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
function toggleProgressInput() {
    var status = $('#projectStatus').val();
    if (status === 'in-progress') {
        $('#progressContainer').show();
    } else {
        $('#progressContainer').hide();
        $('#progressPercentage').val(''); // reset nilai jika disembunyikan
    }
}

// Jalankan saat load halaman untuk menghandle kasus edit atau reload form
toggleProgressInput();

$('#projectStatus').on('change', function() {
    toggleProgressInput();
});


$('#btnAddProject').on('click', function() {
    var modal = $('body').find('div#projectModal');

    var form = modal.find('#projectForm');

    $(form)[0].reset();
    modal.modal('show');
});

$('#projectForm').on('submit', function(e) {
    e.preventDefault();
    var form = this;
    var formData = new FormData(form);
    var modal = $('body').find('div#projectModal');

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false, // Jangan ubah data menjadi query string
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                modal.modal('hide')
                project_DT.ajax.reload(null, false);
                toastr.success('Project sukses di tambahkan.');
            } else if (response.status === 'validation_error') {
                $.each(response.errors, function(key, val) {
                    $('.' + key + '_error').text(val);
                });
            } else {
                alert('Something went wrong');
            }
        },
        error: function() {
            alert('Terjadi kesalahan saat menyimpan data.');
        }
    });
});

var project_DT = $('#projectsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "<?= route_to('project_dt') ?>",
    dom: "Brtip",
    info: false,
    ordering: false,

    fnCreatedRow: function(row, data, index) {
        $('td', row).eq(0).html(index + 1);
    },

});

$(document).on('click', '.btnEditProject', function(e) {
    e.preventDefault();
    var id_project = $(this).data('id');
    var modal = $('#editProjectModal');
    var url = '<?= route_to('get_project') ?>';

    $.getJSON(url, {
        id: id_project
    }, function(response) {
        if (response.success) {
            var project = response.data;
            // Set nilai input di modal edit
            modal.find('#id').val(project.id);
            modal.find('#projectName').val(project.name);
            modal.find('#projectDescription').val(project.description);
            modal.find('#projectLocation').val(project.location);
            modal.find('#projectStartDate').val(project.start_date);
            modal.find('#projectEndDate').val(project.end_date);
            modal.find('#projectStatus').val(project.status);
            modal.find('#kontraktorId').val(project.kontraktor_id);
            modal.find('#konsultanId').val(project.konsultan_id);

            // Reset error text jika ada
            modal.find('.error-text').text('');

            // Tampilkan modal
            modal.modal('show');
        } else {
            alert('Gagal mengambil data project: ' + response.message);
        }
    }).fail(function() {
        alert('Terjadi kesalahan saat mengambil data project.');
    });
});

$('#editProjectForm').on('submit', function(e) {
    e.preventDefault();

    var form = $(this);
    var url = '<?= route_to('update_project') ?>'; // Pastikan route sudah dibuat

    // Reset error text
    form.find('.error-text').text('');

    $.ajax({
        url: url,
        method: 'POST',
        data: form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            toastr.remove();
        },

        success: function(response) {
            if (response.success) {
                $('#editProjectModal').modal('hide');
                if (typeof project_DT !== 'undefined') {
                    project_DT.ajax.reload(null, false);
                }
                toastr.success(response.message);
            } else {
                if (response.errors) {
                    $.each(response.errors, function(key, val) {
                        form.find('.error-' + key).text(val);
                    });
                } else {
                    alert(response.message || 'Terjadi kesalahan saat memperbarui project.');
                }
            }
        },
        error: function(xhr, status, error) {
            alert('Terjadi kesalahan pada server: ' + error);
        }
    });
});

$(document).on('click', '.btnHapusProject', function(e) {
    e.preventDefault();

    var id_project = $(this).data('id');
    var url = '<?= route_to('delete_project') ?>';

    swal({
        title: 'Yakin ingin menghapus project ini?',
        text: "Data yang sudah dihapus tidak bisa dikembalikan!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: url,
                type: 'post',
                data: {
                    id: id_project
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        swal(
                            'Terhapus!',
                            response.message,
                            'success'
                        );
                        // Reload DataTable jika ada
                        if (typeof project_DT !== 'undefined') {
                            project_DT.ajax.reload(null, false);
                        }
                    } else {
                        swal(
                            'Gagal!',
                            response.message || 'Gagal menghapus project.',
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    swal(
                        'Error!',
                        'Terjadi kesalahan pada server: ' + error,
                        'error'
                    );
                }
            });
        }
    });
});
</script>
<?= $this->endSection('scripts') ?>