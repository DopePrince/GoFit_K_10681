<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\class_booking;
use App\Models\class_deposit;
use App\Models\class_detail;
use App\Models\instructor;
use App\Models\member;
use App\Models\class_on_running;
use App\Models\class_on_running_daily;
use App\Models\instructor_attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\PDF;

class ClassBookingController extends Controller
{

    public function index()
    {
        $class_bookings = class_booking::with('member', 'class_on_running_daily', 'class_on_running_daily.class_on_running', 'class_on_running_daily.class_on_running.class_detail', 'class_on_running_daily.instructor')->get();

        if (count($class_bookings) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $class_bookings
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
        $storeData = $request->all();
        $validator = Validator::make($storeData, [
            'ID_MEMBER' => 'required',
            'ID_CLASS_ON_RUNNING_DAILY' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        // Check if member's membership status is 0
        $member = member::find($request->ID_MEMBER);
        if (!$member || $member->STATUS_MEMBERSHIP != 1) {
            return response([
                'message' => 'Members membership status is not active',
                'data' => null
            ], 400);
        }

        // Check if class_on_running_daily CLASS_CAPACITY still available
        $class_on_running_daily = class_on_running_daily::find($request->ID_CLASS_ON_RUNNING_DAILY);
        if (!$class_on_running_daily || $class_on_running_daily->CLASS_CAPACITY <= 0) {
            return response([
                'message' => 'Class capacity is already full',
                'data' => null
            ], 400);
        }

        // Check if member have class_deposit for the booked class, if YES then it will reduce the CLASS_AMOUNT by 1, and insert PAYMENT_TYPE = paket
        // If NO, then it will reduce the DEPOSIT_REGULAR_AMOUNT in member table according to PRICE in class_detail, and insert PAYMENT_TYPE = regular
        $class_detail = DB::select('SELECT class_details.* from class_details
        JOIN class_on_runnings ON class_on_runnings.ID_CLASS = class_details.ID_CLASS
        JOIN class_on_running_dailies ON class_on_running_dailies.ID_CLASS_ON_RUNNING = class_on_runnings.ID_CLASS_ON_RUNNING
        WHERE class_on_running_dailies.ID_CLASS_ON_RUNNING_DAILY = "' . $request->ID_CLASS_ON_RUNNING_DAILY . '";');

        $class_deposit = DB::select('SELECT *
                        FROM class_deposits
                        WHERE class_deposits.ID_MEMBER = "' . $request->ID_MEMBER . '"
                        AND ID_CLASS = "' . $class_detail[0]->ID_CLASS . '";');

        // Saat store untuk member yang tidak punya deposit dari kelas yang dipilih, muncul error
        if (empty($class_deposit)) {
            // if (!$class_deposit[0]) {
            $member = member::find($request->ID_MEMBER);
            if ($member->DEPOSIT_REGULAR_AMOUNT < $class_detail[0]->PRICE) {
                return response([
                    'message' => 'Uang yang dimiliki member tidak mencukupi',
                    'data' => null
                ], 400);
            } else {
                $member->DEPOSIT_REGULAR_AMOUNT -= $class_detail[0]->PRICE;
                $PAYMENT_TYPE = 'Regular';
                $member->save();
            }
        } else {
            DB::update('UPDATE class_deposits SET CLASS_AMOUNT = CLASS_AMOUNT-1 WHERE ID_CLASS_DEPOSIT = "' . $class_deposit[0]->ID_CLASS_DEPOSIT . '";');
            $PAYMENT_TYPE = 'Paket';
        }

        // Generate nomor booking
        $rownumber = DB::table('class_bookings')->count() + 1;
        $id_number = sprintf("%03d", $rownumber);
        $id_date = Carbon::now()->format('y.m');

        // DATE_TIME is inserted with today date
        $DATE_TIME = Carbon::now();

        $class_booking = class_booking::create([
            'ID_CLASS_BOOKING' => $id_date . '.' . $id_number,
            'ID_MEMBER' => $request->ID_MEMBER,
            'ID_CLASS_ON_RUNNING_DAILY' => $request->ID_CLASS_ON_RUNNING_DAILY,
            'DATE_TIME' => $DATE_TIME,
            'PAYMENT_TYPE' => $PAYMENT_TYPE,
            'STATUS_PRESENSI' => 0
        ]);

        $class_on_running_daily->CLASS_CAPACITY -= 1;
        $class_on_running_daily->save();

        return response([
            'message' => 'Add Class Booking Success',
            'data' => $class_booking
        ], 200);
    }

    public function show($ID_CLASS_BOOKING)
    {
        $class_bookings = class_booking::find($ID_CLASS_BOOKING);

        if (!is_null($class_bookings)) {
            return response([
                'message' => 'Retrieve Class Booking Success',
                'data' => $class_bookings
            ], 200);
        }

        return response([
            'message' => 'Class Booking Not Found',
            'data' => null
        ], 400);
    }

    // public function update(Request $request, $ID_CLASS_BOOKING)
    // {

    // }

    // public function destroy($ID_CLASS_BOOKING)
    // {
    //     $class_booking = class_booking::findOrFail($ID_CLASS_BOOKING);

    //     $class_on_running = class_on_running::findOrFail($class_booking->ID_CLASS_ON_RUNNING);

    //     // Hitung selisih hari antara hari ini dengan tanggal dari DATE di class_on_running
    //     $class_on_running_daily = class_on_running_daily::where('ID_CLASS_ON_RUNNING', $class_on_running->ID_CLASS_ON_RUNNING)
    //         ->where('DATE', Carbon::parse($class_booking->DATE_TIME)->format('Y-m-d'))
    //         ->firstOrFail();

    //     $days_difference = Carbon::parse($class_on_running_daily->DATE)->diffInDays(Carbon::parse($class_booking->DATE_TIME), false);

    //     if ($days_difference < 1) {
    //         return response([
    //             'message' => 'Class booking cannot be canceled less than one day before the class starts',
    //             'data' => null
    //         ], 400);
    //     }
    //     // Delete related class_booking
    //     $class_booking->delete();
    //     // Increment CLASS_CAPACITY in class_on_running because 1 data have been deleted
    //     $class_on_running->CLASS_CAPACITY += 1;
    //     $class_on_running->save();

    //     return response([
    //         'success' => true,
    //         'message' => 'Class booking successfully deleted',
    //         'data' => $class_booking
    //     ], 200);
    // }

    public function deleteClassBooking($ID_CLASS_BOOKING)
    {
        //MAKSIMAL H-1
        $class_booking = class_booking::findOrFail($ID_CLASS_BOOKING);

        $class_on_running_daily = class_on_running_daily::findOrFail($class_booking->ID_CLASS_ON_RUNNING_DAILY);

        // Hitung selisih hari antara hari ini dengan tanggal dari DATE di class_on_running_daily
        $today = Carbon::now();
        $class_on_running_daily_DATE = Carbon::parse($class_on_running_daily->DATE)->startOfDay();

        $days_difference = $today->diffInDays($class_on_running_daily_DATE, false);

        if ($days_difference < 1) {
            return response([
                'message' => 'Class booking cannot be canceled less than one day before the class starts',
                'data' => null
            ], 400);
        }

        $id_class_on_running_daily = $class_booking->class_on_running_daily->ID_CLASS_ON_RUNNING_DAILY;
        $id_member = $class_booking->member->ID_MEMBER;

        $class_detail = DB::select('SELECT class_details.* from class_details
        JOIN class_on_runnings ON class_on_runnings.ID_CLASS = class_details.ID_CLASS
        JOIN class_on_running_dailies ON class_on_running_dailies.ID_CLASS_ON_RUNNING = class_on_runnings.ID_CLASS_ON_RUNNING
        WHERE class_on_running_dailies.ID_CLASS_ON_RUNNING_DAILY = "' . $id_class_on_running_daily . '";');

        $class_deposit = DB::select('SELECT *
                        FROM class_deposits
                        WHERE class_deposits.ID_MEMBER = "' . $id_member . '"
                        AND ID_CLASS = "' . $class_detail[0]->ID_CLASS . '";');

        if (empty($class_deposit)) {
            //$member = member::find($request->ID_MEMBER);
            $member = member::find($class_booking->member->ID_MEMBER);
            if ($member) {
                $member->DEPOSIT_REGULAR_AMOUNT += $class_detail[0]->PRICE;
                $member->save();
            } else {
                return response([
                    'message' => 'Member tidak ditemukan',
                    'data' => null
                ], 400);
            }
        } else {
            $class_deposit = class_deposit::find($class_deposit[0]->ID_CLASS_DEPOSIT);
            DB::update('UPDATE class_deposits SET CLASS_AMOUNT = CLASS_AMOUNT+1 WHERE ID_CLASS_DEPOSIT = ?', [$class_deposit->ID_CLASS_DEPOSIT]);
        }

        // Delete related class_booking
        $class_booking->delete();
        // Increment CLASS_CAPACITY in class_on_running by 1 after delete data
        $class_on_running_daily->CLASS_CAPACITY += 1;
        $class_on_running_daily->save();

        return response([
            'success' => true,
            'message' => 'Class booking successfully deleted',
            'data' => $class_booking
        ], 200);
    }

    // public function printPresensiRegular($ID_CLASS_BOOKING)
    // {
    //     $class_booking = class_booking::find($ID_CLASS_BOOKING);

    //     if (is_null($class_booking)) {
    //         return response([
    //             'message' => 'Class Booking Not Found',
    //             'data' => null
    //         ], 404);
    //     }

    //     $ID_MEMBER = member::findOrFails($class_booking->ID_MEMBER);

    //     $memberData = member::join('class_bookings', 'members.ID_MEMBER', '=', 'class_bookings.ID_MEMBER')
    //         ->join('class_on_runnings', 'class_bookings.ID_CLASS_ON_RUNNING', '=', 'class_on_runnings.ID_CLASS_ON_RUNNING')
    //         ->join('class_on_running_dailies', 'class_on_runnings.ID_CLASS_ON_RUNNING', '=', 'class_on_running_dailies.ID_CLASS_ON_RUNNING')
    //         ->join('class_details', 'class_on_runnings.ID_CLASS', '=', 'class_details.ID_CLASS')
    //         ->select('members.*')
    //         ->where('members.ID_MEMBER', $ID_MEMBER)
    //         ->get();

    //     foreach ($classData as $booking) {
    //         // Akses data class_on_running terkait
    //         $class_on_running = $booking->class_on_running;
    //         // Akses data class_on_running_daily terkait
    //         $class_on_running_daily = $class_on_running->class_on_running_daily;
    //         // Akses data class_detail terkait
    //         $class_detail = $class_on_running->class_detail;

    //         // Lakukan operasi lain sesuai kebutuhan
    //         $depositReduction = $class_detail->PRICE;
    //         $member->DEPOSIT_REGULAR_AMOUNT -= $depositReduction;
    //         $member->save();
    //     }
    // }

    public function printPresensiRegular($ID_CLASS_BOOKING)
    {
        $class_booking = class_booking::find($ID_CLASS_BOOKING);

        if (is_null($class_booking)) {
            return response([
                'message' => 'Class Booking Not Found',
                'data' => null
            ], 404);
        }

        $member = member::find($class_booking->ID_MEMBER);
        $class_on_running_daily = class_on_running_daily::find($class_booking->ID_CLASS_ON_RUNNING_DAILY);
        $instructor = instructor::find($class_on_running_daily->ID_INSTRUCTOR);
        $class_on_running = class_on_running::find($class_on_running_daily->ID_CLASS_ON_RUNNING);
        $class_detail = class_detail::find($class_on_running->ID_CLASS);

        $data_print = [
            'class_booking' => $class_booking,
            'member' => $member,
            'class_on_running_daily' => $class_on_running_daily,
            'instructor' => $instructor,
            'class_on_running' => $class_on_running,
            'class_detail' => $class_detail
        ];

        $pdf = PDF::loadview('report_presensi_regular_card', $data_print);

        return $pdf->download('Report_Presensi_Regular_Card_' . $member->FULL_NAME . '.pdf');
    }

    public function printPresensiPaket($ID_CLASS_BOOKING)
    {
        $class_booking = class_booking::find($ID_CLASS_BOOKING);

        if (is_null($class_booking)) {
            return response([
                'message' => 'Class Booking Not Found',
                'data' => null
            ], 404);
        }

        $member = member::find($class_booking->ID_MEMBER);
        $class_on_running_daily = class_on_running_daily::find($class_booking->ID_CLASS_ON_RUNNING_DAILY);
        $instructor = instructor::find($class_on_running_daily->ID_INSTRUCTOR);
        $class_on_running = class_on_running::find($class_on_running_daily->ID_CLASS_ON_RUNNING);
        $class_detail = class_detail::find($class_on_running->ID_CLASS);

        $class_deposit = DB::select('SELECT *
                        FROM class_deposits
                        JOIN members ON class_deposits.ID_MEMBER = members.ID_MEMBER
                        JOIN class_details ON class_deposits.ID_CLASS = class_details.ID_CLASS
                        WHERE class_deposits.ID_MEMBER = "' . $member->ID_MEMBER . '"
                        AND class_deposits.ID_CLASS = "' . $class_detail->ID_CLASS . '";');

        $data_print = [
            'class_booking' => $class_booking,
            'member' => $member,
            'class_on_running_daily' => $class_on_running_daily,
            'instructor' => $instructor,
            'class_on_running' => $class_on_running,
            'class_detail' => $class_detail,
            'class_deposit' => $class_deposit[0]
        ];

        $pdf = PDF::loadview('report_presensi_paket_card', $data_print);

        return $pdf->download('Report_Presensi_Paket_Card_' . $member->FULL_NAME . '.pdf');
    }

    public function printPresensi($ID_CLASS_BOOKING)
    {
        $class_booking = class_booking::find($ID_CLASS_BOOKING);

        if (!$class_booking) {
            return response([
                'message' => 'Class Booking Not Found',
                'data' => null
            ], 404);
        }

        if ($class_booking->PAYMENT_TYPE == 'Regular' && $class_booking->STATUS_PRESENSI == 1) {
            return $this->printPresensiRegular($ID_CLASS_BOOKING);
        } else if ($class_booking->PAYMENT_TYPE == 'Paket' && $class_booking->STATUS_PRESENSI == 1) {
            return $this->printPresensiPaket($ID_CLASS_BOOKING);
        } else {
            return response([
                'message' => 'Member is not eligible to Print Presensi',
                'data' => null
            ], 400);
        }
    }

    public function confirmMemberPresensiByInstructor($ID_CLASS_BOOKING)
    {
        $class_booking = class_booking::find($ID_CLASS_BOOKING);

        if (is_null($class_booking)) {
            return response([
                'message' => 'Class Booking Not Found',
                'data' => null
            ], 404);
        }

        $class_booking->STATUS_PRESENSI = 1;
        $class_booking->save();

        // TAMBAHIN KONDISI YANG MANA STATUS_PRESENSI MEMBER BISA DIUPDATE JIKA PRESENSI INSTRUCTOR SUDAH TERKONFIRMASI MO
        // BUAT InstructorAttendanceController
    }
}
