<?= $this->extend('backend/layout/page-layout') ?>

<?= $this->section('content') ?>

<style>
/* Container grid untuk card */
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    padding: 1rem 0;
}

/* Card styling */
.project-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.project-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
}

/* Judul proyek */
.project-name {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #333;
}

/* Detail proyek */
.project-detail {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1rem;
}

/* Progress bar container */
.progress-container {
    background: #e0e0e0;
    border-radius: 20px;
    height: 16px;
    overflow: hidden;
    margin-bottom: 1rem;
}

/* Progress bar fill */
.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #4caf50, #81c784);
    width: 0;
    border-radius: 20px;
    transition: width 1.2s ease-in-out;
}

/* Status badge */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #fff;
    text-transform: capitalize;
    background-color: #2196f3;
    user-select: none;
}

/* Warna status berbeda */
.status-badge.completed {
    background-color: #4caf50;
}

.status-badge.in-progress {
    background-color: #ff9800;
}

.status-badge.pending {
    background-color: #f44336;
}

/* Tombol edit */
.btn-edit {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    background-color: #2196f3;
    border: none;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: background-color 0.3s ease;
    text-decoration: none;
}

.btn-edit:hover {
    background-color: #1976d2;
}

/* Ikon pensil (edit) */
.btn-edit svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}
</style>

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
                    <div class="card-block">
                        <h4>Selamat datang, <?= esc(get_user()->name) ?>!</h4>
                        <p>Berikut adalah daftar proyek yang Anda kelola atau pantau:</p>

                        <?php if (!empty($projects)) : ?>
                        <div class="projects-grid">
                            <?php foreach ($projects as $project) : ?>
                            <div class="project-card" tabindex="0" aria-label="Proyek <?= esc($project['name']) ?>">
                                <div>
                                    <div class="project-name"><?= esc($project['name']) ?></div>
                                    <div class="project-detail"><strong>Lokasi:</strong>
                                        <?= esc($project['location']) ?></div>
                                    <div class="project-detail"><strong>Periode:</strong>
                                        <?= esc($project['start_date']) ?> s/d <?= esc($project['end_date']) ?></div>
                                    <div class="project-detail">
                                        <span
                                            class="status-badge <?= strtolower(str_replace(' ', '-', $project['status'])) ?>">
                                            <?= esc(ucfirst($project['status'])) ?>
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <div class="progress-container" aria-label="Progress proyek">
                                        <div class="progress-bar"
                                            style="width: <?= esc($project['progress_percentage'] ?? 0) ?>%;"></div>
                                    </div>
                                    <small><?= esc($project['progress_percentage'] ?? 0) ?>% selesai</small>
                                </div>


                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else : ?>
                        <p>Tidak ada proyek yang dapat ditampilkan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>