<?= $this->extend('backend/layout/page-layout') ?>
<?= $this->section('stylesheets') ?>
<link href="https://cdn.datatables.net/v/dt/dt-2.3.1/fc-5.0.4/fh-4.0.2/datatables.min.css" rel="stylesheet"
    integrity="sha384-caazROfB1e3HFiqbbU0KT9TYMkLdmZXxttH1zaS9CzDwkEQC0uykbKZ1wXdj1wX1" crossorigin="anonymous">

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
                    <li class="breadcrumb-item active">Laporan Pengajuan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <div class="page-body">
                <div class="card p-4">
                    <div class="card-header">
                        <a href="<?= route_to('laporan_cetak') ?>" class="btn btn-success btn-sm">Cetak
                            Laporan</a>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-4">Laporan Pengajuan</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tableLaporan">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Proyek</th>
                                        <th>Kontraktor</th>
                                        <th>Waktu Pelaksanaan</th>
                                        <th>Status</th>
                                        <!-- <th>Aksi</th> -->
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

<?= $this->endSection('content') ?>
<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/v/dt/dt-2.3.1/fc-5.0.4/fh-4.0.2/datatables.min.js"
    integrity="sha384-QF85snFseJk+/+vSZCJWTrU5I/JPW7OdWO+PMLUdNeRn7J84G13jwD4CxiXuv+4K" crossorigin="anonymous">
</script>
<script>
var tableLaporan = $('#tableLaporan').DataTable({
    processing: true,
    serverSide: true,
    ajax: "<?= route_to('datatable_laporan') ?>",
    dom: "Brtip",
    info: false,
    ordering: false,

    fnCreatedRow: function(row, data, index) {
        $('td', row).eq(0).html(index + 1);
    },

});
</script>
<?= $this->endSection('scripts') ?>