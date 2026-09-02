<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		$data['title'] = 'Selamat Datang di Aplikasi CAT';

		echo view('layout/header', $data);
		echo view('welcome_message');
		echo view('layout/footer');
	}
}
