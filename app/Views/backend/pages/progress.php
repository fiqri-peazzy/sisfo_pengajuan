<?= $this->extend('backend/layout/page-layout') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Perbarui Progress Proyek</h5>
                    <p class="m-b-0">Memperbarui status project untuk informasi</p>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?= route_to('home') ?>"><i class="fa fa-home"></i> Beranda</a>
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
                <div class="card p-3">
                    <div class="row">

                        <?php if (!empty($projects)) : ?>
                        <?php foreach ($projects as $project) : ?>

                        <div class="col-md-6">
                            <div class="mb-4 p-3 border rounded shadow-sm">
                                <h5 class="mb-2"><?= esc($project['name']) ?></h5>
                                <p><strong>Lokasi:</strong> <?= esc($project['location']) ?></p>
                                <p><strong>Periode:</strong> <?= esc($project['start_date']) ?> s/d
                                    <?= esc($project['end_date']) ?></p>
                                <p>
                                    <strong>Status:</strong>
                                    <?php if ($project['status'] == 'pending') : ?>
                                    <span class="badge badge-warning">Pending</span>
                                    <?php elseif ($project['status'] == 'ongoing' || $project['status'] == 'on progress') : ?>
                                    <span class="badge badge-info">On Progress</span>
                                    <?php elseif ($project['status'] == 'completed') : ?>
                                    <span class="badge badge-success">Completed</span>
                                    <?php else : ?>
                                    <span class="badge badge-secondary"><?= esc($project['status']) ?></span>
                                    <?php endif; ?>
                                </p>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?= esc($project['progress_percentage']) ?>%;"
                                        aria-valuenow="<?= esc($project['progress_percentage']) ?>" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <?= esc($project['progress_percentage']) ?>%
                                    </div>
                                </div>

                                <form class="update-progress-form" method="POST"
                                    action="<?= route_to('update-progress') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="project_id" value="<?= esc($project['id']) ?>">
                                    <div class="form-group row align-items-center">
                                        <label for="progress_<?= esc($project['id']) ?>"
                                            class="col-sm-3 col-form-label">Update
                                            Progress (%)</label>
                                        <div class="col-sm-5">
                                            <input type="number" min="0" max="100" class="form-control"
                                                id="progress_<?= esc($project['id']) ?>" name="progress_percentage"
                                                value="<?= esc($project['progress_percentage']) ?>" required>
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p>Tidak ada proyek yang dapat diperbarui progressnya.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
$('.update-progress-form').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var formData = form.serialize();

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                // Update progress bar dan nilai di UI secara langsung
                var progressInput = form.find('input[name="progress_percentage"]');
                var newValue = progressInput.val();
                var progressBar = form.closest('.mb-4').find('.progress-bar');
                progressBar.css('width', newValue + '%').attr('aria-valuenow', newValue)
                    .text(newValue + '%');
            } else {
                toastr.error('Error: ' + response.message);
            }
        },
        error: function() {
            toastr.error('Terjadi kesalahan saat mengirim data update progress.');
        }
    });
});
</script>
<?= $this->endSection('scripts') ?>