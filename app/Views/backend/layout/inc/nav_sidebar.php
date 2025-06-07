<nav class="pcoded-navbar">
    <div class="sidebar_toggle"><a href="#"><i class="fas fa-times"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">
        <div>
            <div class="main-menu-header">
                <img class="img-80 img-radius"
                    src="<?= get_user()->picture != null ? '/images/users/' . get_user()->picture  : '/backend/assets/images/avatar-default.png' ?>"
                    alt="User-Profile-Image">
                <div class="user-details">
                    <span id="more-details"><?= get_user()->name ?><i class="fa fa-caret-down"></i></span>
                </div>
            </div>
            <div class="main-menu-content">
                <ul>
                    <li class="more-details">
                        <a href="<?= route_to('user.profile') ?>"><i class="fas fa-user"></i> Profil</a>
                        <a href="<?= route_to('user.settings') ?>"><i class="fas fa-cog"></i> Pengaturan</a>
                        <a href="<?= route_to('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <?php $role = get_user()->role; ?>

        <!-- Menu Umum -->
        <div class="pcoded-navigation-label">Navigasi</div>
        <ul class="pcoded-item pcoded-left-item">
            <li>
                <a href="<?= route_to('home') ?>" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="pcoded-mtext">Dashboard</span>
                </a>
            </li>
        </ul>

        <!-- Menu Khusus Role -->
        <?php if ($role == 'ppk') : ?>
        <div class="pcoded-navigation-label">Manajemen Proyek</div>
        <ul class="pcoded-item pcoded-left-item">
            <li>
                <a href="<?= route_to('page_project') ?>">
                    <span class="pcoded-micon"><i class="fas fa-road"></i></span>
                    <span class="pcoded-mtext">Data Proyek</span>
                </a>
            </li>
            <li>
                <a href="<?= route_to('persetujuan_pengajuan') ?>">
                    <span class="pcoded-micon"><i class="fas fa-file-invoice-dollar"></i></span>
                    <span class="pcoded-mtext">Pengajuan Pembayaran</span>
                </a>
            </li>
            <li>
                <a href="<?= route_to('cetak_laporan') ?>">
                    <span class="pcoded-micon"><i class="fas fa-file-alt"></i></span>
                    <span class="pcoded-mtext">Cetak Laporan</span>
                </a>
            </li>
        </ul>
        <?php elseif ($role == 'kontraktor') : ?>
        <div class="pcoded-navigation-label">Proyek Saya</div>
        <ul class="pcoded-item pcoded-left-item">

            <li>
                <a href="<?= route_to('payment.submissions') ?>">
                    <span class="pcoded-micon"><i class="fas fa-file-upload"></i></span>
                    <span class="pcoded-mtext">Ajukan Pembayaran</span>
                </a>
            </li>
            <li>
                <a href="<?= route_to('progress') ?>">
                    <span class="pcoded-micon"><i class="fas fa-tasks"></i></span>
                    <span class="pcoded-mtext">Update Progress</span>
                </a>
            </li>

        </ul>
        <?php elseif ($role == 'konsultan') : ?>
        <div class="pcoded-navigation-label">Verifikasi & Monitoring</div>
        <ul class="pcoded-item pcoded-left-item">

            <li>
                <a href="<?= route_to('pemeriksaan') ?>">
                    <span class="pcoded-micon"><i class="fas fa-check-circle"></i></span>
                    <span class="pcoded-mtext">Pemeriksaan</span>
                </a>
            </li>
            <!-- <li>
                <a href="<?= route_to('laporan.konsultan') ?>">
                    <span class="pcoded-micon"><i class="fas fa-file-alt"></i></span>
                    <span class="pcoded-mtext">Laporan Konsultan</span>
                </a>
            </li> -->
        </ul>
        <?php endif; ?>

        <!-- Menu Pengaturan (Admin/PPK) -->
        <?php if ($role == 'ppk') : ?>
        <div class="pcoded-navigation-label">Pengaturan</div>
        <ul class="pcoded-item pcoded-left-item">
            <li>
                <a href="<?= route_to('page_users') ?>">
                    <span class="pcoded-micon"><i class="fas fa-users-cog"></i></span>
                    <span class="pcoded-mtext">Manajemen User</span>
                </a>
            </li>
        </ul>
        <?php endif; ?>
    </div>
</nav>