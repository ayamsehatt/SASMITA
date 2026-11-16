<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Autentikasi
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Dasbor & fitur utama
$routes->get('dashboard', 'Auth::dashboard');
$routes->get('rps', 'Rps::index');
$routes->get('rps/create', 'Rps::create');
$routes->post('rps', 'Rps::store');
$routes->get('rps/(:segment)/sumber', 'Rps::downloadSource/$1');
$routes->post('rps/(:segment)/hapus', 'Rps::delete/$1');
$routes->get('rps/(:segment)', 'Rps::show/$1');

// Asesmen: daftar draft, builder, dan unduh PDF
$routes->get('asesmen', 'Asesmen::index');
$routes->get('asesmen/baru', 'Asesmen::newDraft');
$routes->post('asesmen/pdf', 'Asesmen::pdf');
$routes->get('asesmen/(:segment)', 'Asesmen::edit/$1');
