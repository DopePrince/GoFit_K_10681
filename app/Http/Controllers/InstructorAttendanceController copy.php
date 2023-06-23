<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function store(Request $request)
    {

        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'id_jadwal_harian' => 'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $jadwal_harian = Jadwal_harian::find($storeData['id_jadwal_harian']);
        $instruktur = Instruktur::find($jadwal_harian['id_instruktur']);

        // $updateJamMulai = Carbon::now()->format('H:i:s');
        // $updateJamMulai = '08:03:10';

        $jamMulaiKelas = Carbon::parse($jadwal_harian['jam_mulai']);
        $updateJamMulai = Carbon::parse('08:03:10');
        $keterlambatan = $updateJamMulai->diff($jamMulaiKelas);

        $keterlambatanInstruktur = Carbon::parse($instruktur['keterlambatan']);

        $hours = $keterlambatan->h;
        $minutes = $keterlambatan->i;
        $second = $keterlambatan->s;

        $totalKeterlambatan = $keterlambatanInstruktur->addHours($hours)->addMinutes($minutes)->addSeconds($second);
        $hasilKeterlambatan = $totalKeterlambatan->toTimeString();

        $storeData['update_jam_mulai'] = $updateJamMulai;
        $storeData['keterlambatan'] = $hours.':'. $minutes.':'.$second;

        // $instruktur->update([
        //     'keterlambatan' => $hasilKeterlambatan
        // ]);

        $instruktur->keterlambatan = $hasilKeterlambatan;
        $instruktur->save();

        $presensi_instruktur = Presensi_instruktur::create($storeData);
        $presensi_instruktur = Presensi_instruktur::latest()->first();
        
        return response([
            'message' => 'Add Presensi_instruktur Success',
            'data' => $presensi_instruktur
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
}
