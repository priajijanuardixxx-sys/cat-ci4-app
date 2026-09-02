<?php

// app/Config/Routes.php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -------------------------------------------------------------------
// Rute Publik (Login/Logout)
// -------------------------------------------------------------------
$routes->get('login', 'Auth::index');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('/', 'Auth::index');


// -------------------------------------------------------------------
// Rute Terproteksi (Semua User yang Login)
// -------------------------------------------------------------------
$routes->group('/', ['filter' => 'auth'], function($routes) {
    // Rute utama setelah login
	$routes->get('dashboard', 'Dashboard::index');
});


// Rute Super Admin (Hanya Super Admin)
$routes->group('superadmin', ['filter' => ['auth', 'superadmin']], function($routes) {
	$routes->get('users', 'SuperAdmin\UserController::index');
	$routes->get('users/create', 'SuperAdmin\UserController::create');
	$routes->post('users/save', 'SuperAdmin\UserController::save'); 
	$routes->get('users/edit/(:num)', 'SuperAdmin\UserController::edit/$1'); 
	$routes->get('users/delete_ajax/(:num)', 'SuperAdmin\UserController::delete_ajax/$1'); 
	$routes->get('users/search_ajax', 'SuperAdmin\UserController::search_ajax');
	$routes->get('events', 'SuperAdmin\EventController::index');
	$routes->get('events/create', 'SuperAdmin\EventController::create');
	$routes->post('events/save', 'SuperAdmin\EventController::save');
	$routes->get('events/delete_ajax/(:num)', 'SuperAdmin\EventController::delete_ajax/$1');
	$routes->get('events/edit/(:num)', 'SuperAdmin\EventController::edit/$1');
	$routes->get('events/search_ajax', 'SuperAdmin\EventController::search_ajax');

});

// Rute Admin Event (Panitia & Super Admin)
$routes->group('admin', ['filter' => ['auth', 'panitia']], function($routes) {
	$routes->get('questions', 'Admin\QuestionController::index');
	$routes->get('questions/create', 'Admin\QuestionController::create');
	$routes->post('questions/save', 'Admin\QuestionController::save');
	$routes->get('questions/edit/(:num)', 'Admin\QuestionController::edit/$1');
	$routes->get('questions/delete/(:num)', 'Admin\QuestionController::delete/$1');
	$routes->post('questions/import', 'Admin\QuestionController::import');
	$routes->get('peserta', 'Admin\PesertaController::index'); 
	$routes->get('peserta/create', 'Admin\PesertaController::create');
	$routes->post('peserta/save', 'Admin\PesertaController::save');
	$routes->get('peserta/edit/(:num)', 'Admin\PesertaController::edit/$1'); 
	$routes->get('peserta/delete/(:num)', 'Admin\PesertaController::delete/$1');
	$routes->get('categories', 'Admin\CategoryController::index'); 
	$routes->post('categories/save', 'Admin\CategoryController::save'); 
	$routes->get('categories/delete_ajax/(:num)', 'Admin\CategoryController::delete_ajax/$1');
	$routes->get('categories/preview_questions/(:num)', 'Admin\CategoryController::previewQuestionsAjax/$1');
	$routes->get('exams', 'Admin\ExamController::index');
	$routes->get('exams/load', 'Admin\ExamController::loadExamTypes');
	$routes->post('exams/save', 'Admin\ExamController::saveExamType');
	$routes->get('exams/delete/(:num)', 'Admin\ExamController::deleteExamType/$1');
	$routes->get('exams/setting/(:num)', 'Admin\ExamController::loadExamSetting/$1');
	$routes->post('exams/save_setting', 'Admin\ExamController::saveExamSetting');
	$routes->get('exams-session/start/(:num)', 'Admin\ExamSessionController::start/$1');
	$routes->get('exams-session/pause/(:num)', 'Admin\ExamSessionController::pause/$1');
	$routes->get('exams-session/resume/(:num)', 'Admin\ExamSessionController::resume/$1');
	$routes->get('exams-session/status/(:num)', 'Admin\ExamSessionController::status/$1');
	$routes->post('exams-session/reset/(:num)', 'Admin\ExamSessionController::reset/$1');

	$routes->get('correction/(:num)', 'Admin\CorrectionController::index/$1');
    $routes->post('correction/calculate_score/(:num)', 'Admin\CorrectionController::calculate_score/$1');
    $routes->get('correction/details/(:num)', 'Admin\CorrectionController::get_correction_details/$1');

	$routes->get('peserta/exam/(:num)', 'Admin\PesertaController::listByExamType/$1');

    // Rute AJAX (Panitia Only)
	$routes->group('ajax', function($routes) {
		$routes->get('questions/index', 'Admin\AjaxController::loadQuestionsIndex');
		$routes->get('peserta/search_ajax', 'Admin\PesertaController::search_ajax'); 
		$routes->get('categories/index', 'Admin\CategoryController::loadCategoryIndex'); 
	});
});

// -------------------------------------------------------------------
// Rute Client (Peserta)
// -------------------------------------------------------------------
// Participant Routes
// -------------------------------------------------------------------
$routes->group('participant', ['filter' => 'auth'], function($routes) {
	$routes->get('dashboard', 'Participant\Dashboard::index');
	$routes->get('profile', 'Participant\Profile::index');
	$routes->get('schedule', 'Participant\Dashboard::schedule');
	$routes->get('exams', 'Participant\ExamController::index');
	$routes->get('exams/status/(:num)', 'Participant\ExamController::status/$1');
	$routes->get('exams/start/(:num)', 'Participant\ExamController::start/$1');
	$routes->post('exams/submit/(:num)', 'Participant\ExamController::submit/$1');
    $routes->post('exams/save_answer', 'Participant\ExamController::save_answer');
    $routes->post('exams/finish', 'Participant\ExamController::finish');
});
