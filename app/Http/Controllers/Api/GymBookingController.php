<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\gym_booking;
use App\Models\gym;
use App\Models\member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;

class GymBookingController extends Controller
{
    public function index()
    {
        $gym_bookings = gym_booking::with('member', 'gym')->get();

        if (count($gym_bookings) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $gym_bookings
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
        // TAMBAHIN member hanya bisa booking 1 kali sehari
        $storeData = $request->all();
        $validator = Validator::make($storeData, [
            'ID_MEMBER' => 'required',
            'ID_GYM' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $member = member::find($request->ID_MEMBER);
        if (!$member || $member->STATUS_MEMBERSHIP == 0) {
            return response([
                'message' => 'Member Not Found or Member membership not active',
                'data' => null
            ], 400);
        }

        $gym = gym::find($request->ID_GYM);
        if (!$gym || $gym->GYM_CAPACITY <= 0) {
            return response([
                'message' => 'Gym Not Found or Gym capacity is already full',
                'data' => null
            ], 400);
        }

        // Check if today date is the same as DATE_TIME_BOOKING
        // If the same, then it will return response
        $todayDate = Carbon::today()->startOfDay();
        $existingBooking = gym_booking::where('ID_MEMBER', $request->ID_MEMBER)
            ->whereDate('DATE_TIME_BOOKING', $todayDate)
            ->first();

        if ($existingBooking) {
            return response([
                'message' => 'Member has already made a booking today',
                'data' => null
            ], 400);
        }

        // No Struk presensi = ID_GYM_BOOKING
        $rownumber = DB::table('gym_bookings')->count() + 1;
        $id_number = sprintf("%03d", $rownumber);
        $id_date = Carbon::now()->format('y.m');

        $DATE_TIME_BOOKING = Carbon::parse($gym->DATE)->startOfDay();
        $DATE_TIME_PRESENSI = null;

        $gym_booking = gym_booking::create([
            'ID_GYM_BOOKING' => $id_date . '.' . $id_number,
            'ID_MEMBER' => $request->ID_MEMBER,
            'ID_GYM' => $request->ID_GYM,
            'DATE_TIME_BOOKING' => $DATE_TIME_BOOKING,
            'DATE_TIME_PRESENSI' => $DATE_TIME_PRESENSI
        ]);

        $gym->GYM_CAPACITY -= 1;
        $gym->save();

        return response([
            'message' => 'Add Gym Booking Success',
            'data' => $gym_booking
        ], 200);
    }

    public function show($ID_GYM_BOOKING)
    {
        $gym_bookings = gym_booking::find($ID_GYM_BOOKING);

        if (!is_null($gym_bookings)) {
            return response([
                'message' => 'Retrieve Gym Booking Success',
                'data' => $gym_bookings
            ], 200);
        }

        return response([
            'message' => 'Gym Booking Not Found',
            'data' => null
        ], 400);
    }

    public function addPresensiGym($ID_GYM_BOOKING)
    {
        $gym_booking = gym_booking::find($ID_GYM_BOOKING);

        if (is_null($gym_booking)) {
            return response([
                'message' => 'Gym Booking Not Found',
                'data' => null
            ], 404);
        }

        //Menambah presensi GYM adalah hanya meng-update DATE_TIME dari store 
        //data yang sebelumnya adalah NULL, menjadi tanggal hari ini
        $gym_booking->DATE_TIME_PRESENSI = Carbon::now();

        if ($gym_booking->save()) {
            return response([
                'message' => 'Add Presensi Gym Success',
                'data' => $gym_booking
            ], 200);
        }

        return response([
            'message' => 'Add Presensi Gym Success',
            'data' => $gym_booking
        ], 200);
    }

    // public function destroy($ID_GYM_BOOKING)
    // {
    //     $gym_booking = gym_booking::find($ID_GYM_BOOKING);

    //     if(is_null($gym_booking)){
    //         return response([ 
    //             'message' => 'Gym Booking Not Found',
    //             'date' => null
    //         ], 404);
    //     }

    //     if($gym_booking->delete()){
    //         return response([
    //             'message' => 'Delete Gym Booking Success',
    //             'data' => $gym_booking
    //         ], 200);
    //     }

    //     return response([
    //         'message' => 'Delete Gym Booking Failed',
    //         'data' => null,
    //     ], 400);
    // }

    public function deleteGymBooking($ID_GYM_BOOKING)
    {
        // MAKSIMAL H-1
        $gym_booking = gym_booking::find($ID_GYM_BOOKING);

        if (!$gym_booking) {
            return response([
                'message' => 'Gym Booking Data Not Found',
                'data' => null
            ], 400);
        }

        $gym = gym::find($gym_booking->ID_GYM);

        if (!$gym) {
            return response([
                'message' => 'Gym Data Not Found',
                'data' => null
            ], 400);
        }

        $today_date = Carbon::today()->startOfDay();
        $gym_booking_date = Carbon::parse($gym_booking->DATE_TIME_BOOKING)->startOfDay();

        if ($today_date->diffInDays($gym_booking_date, false) < 1) {
            return response([
                'message' => 'Gym booking cannot be canceled less than one day before the booked time',
                'data' => null
            ], 400);
        }
        // Delete related gym_booking
        $gym_booking->delete();
        // Increment GYM_CAPACITY in gym by 1 after delete data
        $gym->GYM_CAPACITY += 1;
        $gym->save();

        return response([
            'success' => true,
            'message' => 'Gym Booking successfully deleted',
            'data' => $gym_booking
        ], 200);
    }

    public function printPresensiGym($ID_GYM_BOOKING)
    {
        $gym_booking = gym_booking::find($ID_GYM_BOOKING);

        if (is_null($gym_booking)) {
            return response([
                'message' => 'Gym Booking Not Found',
                'data' => null
            ], 404);
        }

        $member = member::find($gym_booking->ID_MEMBER);
        $gym = gym::find($gym_booking->ID_GYM);

        $data_print = [
            'gym_booking' => $gym_booking,
            'gym' => $gym,
            'member' => $member
        ];

        $pdf = PDF::loadview('gym_presensi_card', $data_print);

        return $pdf->download('Gym_Attendance_Card_' . $member->FULL_NAME . '.pdf');
    }
}
