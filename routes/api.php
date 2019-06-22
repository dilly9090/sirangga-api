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
Route::post('update-profil/{id}','ApiController@update_profil');
Route::post('changepassword/{id}','ApiController@changepassword');

//jadwalfilterbydate
//jadwalall
//jadwalfilterbydate

Route::post('login','ApiController@login');