<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', 'Api\AuthController@login');
Route::post('updatePassword', 'Api\AuthController@updatePassword');
Route::post('resetPassword', 'Api\AuthController@resetPassword');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Pegawai
Route::get('pegawai', 'Api\PegawaiController@index');
Route::post('pegawai', 'Api\PegawaiController@store');
Route::get('pegawai/{ID_PEGAWAI}', 'Api\PegawaiController@show');
Route::put('pegawai/{ID_PEGAWAI}', 'Api\PegawaiController@update');
Route::delete('pegawai/{ID_PEGAWAI}', 'Api\PegawaiController@destroy');
//Instructor
Route::get('instructor', 'Api\InstructorController@index');
Route::post('instructor', 'Api\InstructorController@store');
Route::get('instructor/{ID_INSTRUCTOR}', 'Api\InstructorController@show');
Route::put('instructor/{ID_INSTRUCTOR}', 'Api\InstructorController@update');
Route::delete('instructor/{ID_INSTRUCTOR}', 'Api\InstructorController@destroy');
Route::post('instructor_late_reset', 'Api\InstructorController@resetLateAmount');
//Instructor Absent
Route::get('instructor_absent', 'Api\InstructorAbsentController@index');
Route::post('instructor_absent', 'Api\InstructorAbsentController@store');
Route::get('instructor_absent/{ID_INSTRUCTOR_ABSENT}', 'Api\InstructorAbsentController@show');
Route::put('instructor_absent/{ID_INSTRUCTOR_ABSENT}', 'Api\InstructorAbsentController@update');
Route::delete('instructor_absent/{ID_INSTRUCTOR_ABSENT}', 'Api\InstructorAbsentController@destroy');
Route::get('instructor_absent_not_confirmed', 'Api\InstructorAbsentController@showNotConfirmed');
Route::put('instructor_absent_confirmation/{ID_INSTRUCTOR_ABSENT}', 'Api\InstructorAbsentController@updateConfirmation');
//Instructor Attendance
Route::get('instructor_attendance', 'Api\InstructorAttendanceController@index');
Route::post('instructor_attendance/{ID_INSTRUCTOR_ATTENDANCE}', 'Api\InstructorAttendanceController@store');
Route::get('instructor_attendance/{ID_INSTRUCTOR_ATTENDANCE}', 'Api\InstructorAttendanceController@show');
Route::get('instructor_attendance_update_start_time/{ID_INSTRUCTOR_ATTENDANCE}', 'Api\InstructorAttendanceController@updateSTARTTIMEandISATTENDED');
//Member
Route::get('member', 'Api\MemberController@index');
Route::post('member', 'Api\MemberController@store');
Route::get('member/{ID_MEMBER}', 'Api\MemberController@show');
Route::put('member/{ID_MEMBER}', 'Api\MemberController@update');
Route::delete('member/{ID_MEMBER}', 'Api\MemberController@destroy');
Route::get('member_show_expire', 'Api\MemberController@showExpiringMembers');
Route::post('member_deactivate', 'Api\MemberController@deactivateMembers');
//Promo_Class
Route::get('promo_class', 'Api\PromoClassController@index');
Route::post('promo_class', 'Api\PromoClassController@store');
Route::get('promo_class/{ID_PROMO_CLASS}', 'Api\PromoClassController@show');
Route::put('promo_class/{ID_PROMO_CLASS}', 'Api\PromoClassController@update');
Route::delete('promo_class/{ID_PROMO_CLASS}', 'Api\PromoClassController@destroy');
//Promo_Regular
Route::get('promo_regular', 'Api\PromoRegularController@index');
Route::post('promo_regular', 'Api\PromoRegularController@store');
Route::get('promo_regular/{ID_PROMO_REGULAR}', 'Api\PromoRegularController@show');
Route::put('promo_regular/{ID_PROMO_REGULAR}', 'Api\PromoRegularController@update');
Route::delete('promo_regular/{ID_PROMO_REGULAR}', 'Api\PromoRegularController@destroy');
//Gym
Route::get('gym', 'Api\GymController@index');
Route::post('gym', 'Api\GymController@store');
Route::get('gym/{ID_GYM}', 'Api\GymController@show');
Route::put('gym/{ID_GYM}', 'Api\GymController@update');
Route::delete('gym/{ID_GYM}', 'Api\GymController@destroy');
//Gym Booking + Gym Attendance
Route::get('gym_booking', 'Api\GymBookingController@index');
Route::post('gym_booking', 'Api\GymBookingController@store');
Route::get('gym_booking/{ID_GYM_BOOKING}', 'Api\GymBookingController@show');
Route::put('gym_booking_add_presensi/{ID_GYM_BOOKING}', 'Api\GymBookingController@addPresensiGym');
// Route::delete('gym_booking/{ID_GYM_BOOKING}', 'Api\GymBookingController@destroy');
Route::post('delete_gym_booking/{ID_GYM_BOOKING}', 'Api\GymBookingController@deleteGymBooking');
Route::get('gym_booking_presensi_print/{ID_GYM_BOOKING}', 'Api\GymBookingController@printPresensiGym');
//Class Detail
Route::get('class_detail', 'Api\ClassDetailController@index');
Route::post('class_detail', 'Api\ClassDetailController@store');
Route::get('class_detail/{ID_CLASS}', 'Api\ClassDetailController@show');
Route::put('class_detail/{ID_CLASS}', 'Api\ClassDetailController@update');
Route::delete('class_detail/{ID_CLASS}', 'Api\ClassDetailController@destroy');
//Class On Running
Route::get('class_on_running', 'Api\ClassOnRunningController@index');
Route::post('class_on_running', 'Api\ClassOnRunningController@store');
Route::get('class_on_running/{ID_CLASS_ON_RUNNING}', 'Api\ClassOnRunningController@show');
Route::put('class_on_running/{ID_CLASS_ON_RUNNING}', 'Api\ClassOnRunningController@update');
Route::delete('class_on_running/{ID_CLASS_ON_RUNNING}', 'Api\ClassOnRunningController@destroy');
//Class On Running Daily
Route::get('class_on_running_daily', 'Api\ClassOnRunningDailyController@index');
Route::post('class_on_running_daily', 'Api\ClassOnRunningDailyController@generateDailySchedule');
Route::get('class_on_running_daily/{ID_CLASS_ON_RUNNING_DAILY}', 'Api\ClassOnRunningDailyController@show');
Route::put('class_on_running_daily/{ID_CLASS_ON_RUNNING_DAILY}', 'Api\ClassOnRunningDailyController@update');
//Class Deposit
Route::get('class_deposit', 'Api\ClassDepositController@index');
Route::post('class_deposit', 'Api\ClassDepositController@store');
Route::get('class_deposit/{ID_CLASS_DEPOSIT}', 'Api\ClassDepositController@show');
Route::put('class_deposit/{ID_CLASS_DEPOSIT}', 'Api\ClassDepositController@update');
Route::delete('class_deposit/{ID_CLASS_DEPOSIT}', 'Api\ClassDepositController@destroy');
Route::get('class_deposit_show_expire', 'Api\ClassDepositController@listExpiringMemberClassDeposits');
Route::post('class_deposit_reset', 'Api\ClassDepositController@resetClassDeposit');
//Class Booking by Member
Route::get('class_booking', 'Api\ClassBookingController@index');
Route::post('class_booking', 'Api\ClassBookingController@store');
Route::get('class_booking/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@show');
// Route::put('class_booking/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@update');
//Route::delete('delete_class_booking/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@destroy');
Route::post('delete_class_booking/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@deleteClassBooking');
Route::get('class_booking_regular_print/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@printPresensiRegular');
Route::get('class_booking_paket_print/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@printPresensiPaket');
Route::get('class_booking_print_presensi/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@printPresensi');
Route::put('class_booking_status_presensi_member/{ID_CLASS_BOOKING}', 'Api\ClassBookingController@confirmMemberPresensiByInstructor');
//Report Aktivasi
Route::get('report_aktivasi', 'Api\ReportAktivasiController@index');
Route::post('report_aktivasi', 'Api\ReportAktivasiController@store');
Route::get('report_aktivasi/{NO_STRUK_AKTIVASI}', 'Api\ReportAktivasiController@show');
Route::delete('report_aktivasi/{NO_STRUK_AKTIVASI}', 'Api\ReportAktivasiController@destroy');
Route::get('report_aktivasi_print/{NO_STRUK_AKTIVASI}', 'Api\ReportAktivasiController@printReportAktivasi');
//Deposit Regular History
Route::get('report_regular', 'Api\ReportDepositRegularController@index');
Route::post('report_regular', 'Api\ReportDepositRegularController@store');
Route::get('report_regular/{NO_STRUK_REGULAR}', 'Api\ReportDepositRegularController@show');
Route::delete('report_regular/{NO_STRUK_REGULAR}', 'Api\ReportDepositRegularController@destroy');
Route::get('report_regular_print/{NO_STRUK_REGULAR}', 'Api\ReportDepositRegularController@printReportDepositRegular');
//Deposit Class History
Route::get('report_class', 'Api\ReportDepositClassController@index');
Route::post('report_class', 'Api\ReportDepositClassController@store');
Route::get('report_class/{NO_STRUK_CLASS}', 'Api\ReportDepositClassControllerController@show');
Route::delete('report_class/{NO_STRUK_CLASS}', 'Api\ReportDepositClassController@destroy');
Route::get('report_class_print/{NO_STRUK_CLASS}', 'Api\ReportDepositClassController@printReportDepositClass');
//Report
Route::post('report_aktivitas_class', 'Api\ReportController@GetReportAktivitasKelas');
Route::post('report_aktivitas_gym', 'Api\ReportController@GetReportAktivitasGym');
Route::post('report_kinerja_instruktur', 'Api\ReportController@GetReportKinerjaInstruktur');
Route::post('report_laporan_pendapatan', 'Api\ReportController@GetReportLaporanPendapatan');

Route::get('report_aktivitas_class_print/{Tanggal}', 'Api\ReportController@printAktivitasKelas');
Route::get('report_aktivitas_gym_print/{Tanggal}', 'Api\ReportController@printReportAktivitasGym');
Route::get('report_kinerja_instruktur_print/{Tanggal}', 'Api\ReportController@printReportKinerjaInstruktur');
Route::get('report_laporan_pendapatan_print/{Tanggal}', 'Api\ReportController@printReportLaporan');
