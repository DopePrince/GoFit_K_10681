<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\class_on_running_daily;
use Illuminate\Http\Request;
use App\Models\instructor_attendance;
use App\Models\instructor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstructorAttendanceController extends Controller
{
    public function index()
    {
        $instructor_attendances = instructor_attendance::with('instructor')->get();

        if (count($instructor_attendances) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $instructor_attendances
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function store(Request $request, $ID_INSTRUCTOR_ATTENDANCE)
    {
        $storeData = $request->all();
        $validator = Validator::make($storeData, [
            'ID_CLASS_ON_RUNNING_DAILY' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $jadwal_harian = class_on_running_daily::find($storeData['ID_CLASS_ON_RUNNING_DAILY']);
        $instructor = instructor::find($jadwal_harian['ID_INSTRUCTOR']);

        $START_TIME = Carbon::parse($jadwal_harian['START_TIME']);

        $update_START_TIME = Carbon::parse('08:03:10'); // Masih data dummy

        // update_START_TIME akan diupdate melalui fungsi updateSTARTTIME yang
        // cara kerjanya adalah mengisi update_START_TIME dengan Carbon::now()
        // saat sebuah button ditekan.
        //CONTOH CODE
        $response = $this->updateSTARTTIMEandISATTENDED($ID_INSTRUCTOR_ATTENDANCE);
        if ($response->getStatusCode() == 200) {
            $update_START_TIME = $response->getData()->data->START_TIME;
        }
        //END CONTOH CODE

        $LATE = $update_START_TIME->diff($START_TIME);

        $LATE_AMOUNT = Carbon::parse($instructor['LATE_AMOUNT']);

        $jam = $LATE->h;
        $menit = $LATE->i;
        $detik = $LATE->s;

        $LATE_AMOUNT_SUM = $LATE_AMOUNT->addHours($jam)->addMinutes($menit)->addSeconds($detik)->toTimeString();
        
        $storeData['START_TIME'] = $update_START_TIME;
        //START_TIME di class_on_running_daily akan diupdate value yang sama dengan 'START_TIME'
        // instructor_attendance setelah sudah storeData.
        // BELUM BIKIN CODE BUAT ITU
        $storeData['LATE_AMOUNT'] = $jam . ':' . $menit . ':' . $detik;
        $storeData['IS_ATTENDED'] = 0;
        $storeData['DATE_TIME_PRESENSI'] = Carbon::now();

        $instructor->LATE_AMOUNT = $LATE_AMOUNT_SUM;
        $instructor->save();

        $instructor_attendance = instructor_attendance::create($storeData);

        return response([
            'message' => 'Add Instructor Attendance Success',
            'data' => $instructor_attendance
        ], 200);
    }

    public function show($ID_INSTRUCTOR_ATTENDANCE)
    {
        $instructor_attendances = instructor_attendance::find($ID_INSTRUCTOR_ATTENDANCE);

        if (!is_null($instructor_attendances)) {
            return response([
                'message' => 'Retrieve Instructor Attendance Success',
                'data' => $instructor_attendances
            ], 200);
        }

        return response([
            'message' => 'Instructor Attendance Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_INSTRUCTOR_ATTENDANCE)
    {
        //
    }

    public function destroy($ID_INSTRUCTOR_ATTENDANCE)
    {
        //
    }

    public function updateSTARTTIMEandISATTENDED($ID_INSTRUCTOR_ATTENDANCE)
    {
        $instructor_attendance = instructor_attendance::find($ID_INSTRUCTOR_ATTENDANCE);

        if (is_null($instructor_attendance)) {
            return response([
                'message' => 'Instructor Attendance Not Found',
                'data' => null
            ], 404);
        }

        $instructor_attendance->START_TIME = Carbon::now();
        $instructor_attendance->IS_ATTENDED = 1;

        if ($instructor_attendance->save()) {
            return response([
                'message' => 'Add Instructor Attendance Success',
                'data' => $instructor_attendance
            ], 200);
        }

        return response([
            'message' => 'Add Instructor Attendance Success',
            'data' => $instructor_attendance
        ], 200);
    }
}
