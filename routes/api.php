<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('alat','ApiController@alat');
Route::get('alat-ruang','ApiController@alat_ruang');
Route::get('eselon-1','ApiController@eselon_1');
Route::get('eselon-2','ApiController@eselon_2');
Route::get('notifikasi','ApiController@notifikasi');
Route::get('pinjam','ApiController@pinjam');

Route::get('pinjam-by-peminjam/{id}','ApiController@pinjam_by_peminjam');
Route::get('pinjam-by-ruang/{id}','ApiController@pinjam_by_ruang');

Route::get('jadwal-by-month/{month}/{year}','ApiController@pinjam_by_month');
Route::get('jadwal-by-date/{date1}/{date2}','ApiController@pinjam_by_date');

Route::get('jadwal-by-status/{iduser}/{status}','ApiController@jadwal_by_status');

Route::get('cekjadwal-by-date/{date}/{time}/{idruang}','ApiController@getbydate');

Route::get('pinjam-alat','ApiController@pinjam_alat');
Route::get('pinjam-alat-by-pinjamid/{id}','ApiController@pinjam_alat_by_pinjamid');
Route::get('pinjam-notes','ApiController@pinjam_notes');
Route::get('pinjam-notes-by-pinjamid/{id}','ApiController@pinjam_notes_by_pinjamid');
Route::get('pinjam-notes-by-userid/{id}','ApiController@pinjam_notes_by_userid');
Route::get('pinjam-rate','ApiController@pinjam_rate');
Route::get('pinjam-rate-by-pinjam/{id}','ApiController@pinjam_rate_by_pinjam');

Route::get('role','ApiController@role');
Route::get('ruang','ApiController@ruang');
Route::get('slider','ApiController@slider');

Route::get('user','ApiController@user');
Route::get('picruangan','ApiController@picruangan');
Route::get('update-token/{iduser}/{token}','ApiController@update_token');
Route::post('update-profil/{id}','ApiController@update_profil');
Route::post('changepassword/{id}','ApiController@changepassword');

//jadwalfilterbydate
//jadwalall
//jadwalfilterbydate

Route::post('login','ApiController@login');
Route::post('simpanpinjamruang/{iduser}','ApiController@simpanpinjamruang');

Route::get('update-pemesanan/{idpinjam}/{status}','ApiController@update_pemesanan');

Route::get('pesanan_pending','ApiController@pesanan_pending');
Route::get('list_notif_by_user/{id}','ApiController@list_notif_by_user');

Route::post('insert_notif','ApiController@insert_notif');
Route::get('update_notif/{id}','ApiController@update_notif');

Route::get('delete_by_id/{id}','ApiController@delete_by_id');
Route::get('delete_all_by_user/{iduser}','ApiController@delete_all_by_user');

Route::get('readjson','Controller@readjson');