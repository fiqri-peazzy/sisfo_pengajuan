<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->group('', ['filter' => 'cifilter:auth'], static function ($routes) {
    $routes->get('home', 'MainController::index', ['as' => 'home']);
    $routes->get('logout', 'MainController::logoutHandler', ['as' => 'logout']);
    $routes->get('data-proyek', 'MainController::pageProject', ['as' => 'page_project']);
    $routes->post('save-project', 'MainController::saveProject', ['as' => 'save_project']);
    $routes->get('project_dt', 'MainController::datatableProject', ['as' => 'project_dt']);
    $routes->get('get-project', 'MainController::getProject', ['as' => 'get_project']);
    $routes->post('update-project', 'MainController::updateProject', ['as' => 'update_project']);
    $routes->post('delete-project', 'MainController::deleteProject', ['as' => 'delete_project']);

    $routes->get('pengajuan', 'MainController::pagePengajuan', ['as' => 'payment.submissions']);
    $routes->get('cek_files_sertifikat', 'MainController::cekFilesSertifikat', ['as' => 'cek_files_sertifikat']);
    $routes->post('submit_sertifikat', 'MainController::submitSertifikat', ['as' => 'submit_sertifikat']);
    $routes->get('tableRiwayatSertifikat', 'MainController::tableRiwayatSertifikat', ['as' => 'tableRiwayatSertifikat_url']);

    $routes->get('pemeriksaan', 'MainController::pagePemeriksaan', ['as' => 'pemeriksaan']);
    $routes->get('get_file_pemeriksaan/(:num)', 'MainController::getFileAndPemeriksaan/$1', ['as' => 'get_file_pemeriksaan']);

    $routes->post('save-multiple', 'MainController::saveMultiple', ['as' => 'save_multiple']);

    $routes->get('laporan-pengajuan', 'MainController::pageLaporan', ['as' => 'cetak_laporan']);
    $routes->get('tableLaporan', 'MainController::tableLaporan', ['as' => 'datatable_laporan']);
    $routes->get('laporan_cetak', 'MainController::laporan_cetak', ['as' => 'laporan_cetak']);

    $routes->get('persetejuan-pengajuan', 'MainController::pagePersetujuan', ['as' => 'persetujuan_pengajuan']);
    $routes->get('hasil-pemeriksaan', 'MainController::apiHasilPemeriksaan', ['as' => 'apiHasilPemeriksaan']);
    $routes->post('update-status-dokumen', 'MainController::apiUpdatePersetujuan', ['as' => 'apiUpdatePersetujuan']);
    $routes->get('get_status', 'MainController::getStatusFile', ['as' => 'get_status']);

    $routes->get('page_users', 'UserController::pageUsers', ['as' => 'page_users']);
    $routes->post('add_user', 'UserController::addUser', ['as' => 'add_user']);
    $routes->get('datatable_user', 'UserController::datatablesUser', ['as' => 'datatable_user']);
    $routes->get('data-users', 'UserController::dataUser', ['as' => 'get.user']);
    $routes->post('edit-user', 'UserController::dataUserUpdate', ['as' => 'data.user.update']);
    $routes->get('hapus-user', 'UserController::dataUserDrop', ['as' => 'data.user.drop']);

    $routes->get('progress', 'MainController::pageProgress', ['as' => 'progress']);
    $routes->post('update-progress', 'MainController::updateProgress', ['as' => 'update-progress']);
});
$routes->group('', ['filter' => 'cifilter:guest'], static function ($routes) {
    $routes->get('login', 'AuthController::loginForm', ['as' => 'admin.login.form']);
    $routes->post('login', 'AuthController::loginHandler', ['as' => 'admin.login.handler']);
});