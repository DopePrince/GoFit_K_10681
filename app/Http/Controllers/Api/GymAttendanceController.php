<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\gym_attendance;
use App\Models\gym_booking;
use App\Models\member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

class GymAttendanceController extends Controller
{
    public function index()
    {
        $gym_attendances = gym_attendance::with('gym_booking')->get();

        if (count($gym_attendances) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $gym_attendances
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
            'ID_GYM_BOOKING' => 'required',
            'BOOKED_SLOT' => 'required',
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        // No Struk presensi = ID_GYM_ATTENDANCE
        $rownumber = DB::table('gym_attendances')->count() + 1;
        $id_number = sprintf("%03d", $rownumber);
        $id_date = Carbon::now()->format('y.m');

        $DATE_TIME = Carbon::now();

        $gym_attendance = gym_attendance::create([
            'ID_GYM_ATTENDANCE' => $id_date . '.' . $id_number,
            'ID_GYM_BOOKING' => $request->ID_GYM_BOOKING,
            'DATE_TIME' => $DATE_TIME,
            'BOOKED_SLOT' => $request->BOOKED_SLOT,
        ]);

        return response([
            'message' => 'Add Gym Attendance Success',
            'data' => $gym_attendance
        ], 200);
    }

    public function show($ID_GYM_ATTENDANCE)
    {
        $gym_attendances = gym_attendance::find($ID_GYM_ATTENDANCE);

        if(!is_null($gym_attendances)){
            return response([
                'message' => 'Retrieve Gym Attendance Success',
                'data' => $gym_attendances
            ], 200);
        }

        return response([
            'message' => 'Gym Attendance Not Found',
            'data' => null
        ], 400);
    }

    // TIDAK BISA DI-UPDATE
    // public function update(Request $request, $ID_GYM_ATTENDANCE)
    // {
    //     $gym_attendance = gym_booking::find($ID_GYM_ATTENDANCE);

    //     if(is_null($gym_attendance)){
    //         return response([
    //             'message' => 'Gym Attendance Not Found',
    //             'data' => null
    //         ], 404);
    //     }

    //     $updateData = $request->all();
    //     $validator = Validator::make($updateData, [
    //         'ID_GYM_BOOKING' => $request->ID_GYM_BOOKING,
    //         'BOOKED_SLOT' => $request->BOOKED_SLOT,
    //     ]);

    //     if($validator->fails()){
    //         return response(['message' => $validator->errors()], 400);
    //     }

    //     $DATE_TIME = Carbon::now();

    //     $gym_attendance->ID_GYM_BOOKING = $updateData['ID_GYM_BOOKING'];
    //     $gym_attendance->DATE_TIME = $updateData[$DATE_TIME];
    //     $gym_attendance->BOOKED_SLOT = $updateData['BOOKED_SLOT'];
        
    //     if($gym_attendance->save()){
    //         return response([
    //             'message' => 'Update Gym Attendance Success',
    //             'data' => $gym_attendance
    //         ], 200);
    //     }

    //     return response([
    //         'message' => 'Add Gym Attendance Success',
    //         'data' => $gym_attendance
    //     ], 200);
    // }

    public function destroy($ID_GYM_ATTENDANCE)
    {
        $gym_attendance = gym_attendance::find($ID_GYM_ATTENDANCE);

        if(is_null($gym_attendance)){
            return response([ 
                'message' => 'Gym Attendance Not Found',
                'date' => null
            ], 404);
        }

        if($gym_attendance->delete()){
            return response([
                'message' => 'Delete Gym Attendance Success',
                'data' => $gym_attendance
            ], 200);
        }

        return response([
            'message' => 'Delete Gym Attendance Failed',
            'data' => null,
        ], 400);
    }

}
