<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\instructor;
use App\Models\member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstructorController extends Controller
{

    public function index()
    {
        $instructors = instructor::all();

        if (count($instructors) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $instructors
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
            'FULL_NAME' => 'required',
            'GENDER' => 'required',
            'TANGGAL_LAHIR' => 'required|date_format:Y-m-d',
            'PHONE_NUMBER' => 'required',
            'ADDRESS' => 'required',
            'EMAIL' => 'required|unique:instructors|email',
            'PASSWORD',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $rownumber = DB::table('instructors')->count() + 1;
        $id_number = sprintf("%02d", $rownumber);

        $member = instructor::create([
            'ID_INSTRUCTOR' => 'I' . $id_number,
            'FULL_NAME' => $request->FULL_NAME,
            'GENDER' => $request->GENDER,
            'TANGGAL_LAHIR' => $request->TANGGAL_LAHIR,
            'PHONE_NUMBER' => $request->PHONE_NUMBER,
            'ADDRESS' => $request->ADDRESS,
            'EMAIL' => $request->EMAIL,
            'PASSWORD' => bcrypt($request->TANGGAL_LAHIR),
        ]);

        return response([
            'message' => 'Add Instructor Success',
            'data' => $member
        ], 200);
    }

    public function show($ID_INSTRUCTOR)
    {
        $instructors = instructor::find($ID_INSTRUCTOR);

        if (!is_null($instructors)) {
            return response([
                'message' => 'Retrieve Instructor Success',
                'data' => $instructors
            ], 200);
        }

        return response([
            'message' => 'Instructor Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_INSTRUCTOR)
    {
        $instructor = instructor::find($ID_INSTRUCTOR);

        if (is_null($instructor)) {
            return response([
                'message' => 'Instructor Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'FULL_NAME' => 'required',
            'GENDER' => 'required',
            'TANGGAL_LAHIR' => 'required|date_format:Y-m-d',
            'PHONE_NUMBER' => 'required',
            'ADDRESS' => 'required',
            'EMAIL' => 'required',
            'PASSWORD',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $instructor->FULL_NAME = $updateData['FULL_NAME'];
        $instructor->GENDER = $updateData['GENDER'];
        $instructor->TANGGAL_LAHIR = $updateData['TANGGAL_LAHIR'];
        $instructor->PHONE_NUMBER = $updateData['PHONE_NUMBER'];
        $instructor->ADDRESS = $updateData['ADDRESS'];
        $instructor->EMAIL = $updateData['EMAIL'];
        $instructor->PASSWORD = bcrypt($updateData['TANGGAL_LAHIR']);

        if ($instructor->save()) {
            return response([
                'message' => 'Update Instructor Success',
                'data' => $instructor
            ], 200);
        }

        return response([
            'message' => 'Add Instructor Success',
            'data' => $instructor
        ], 200);
    }

    public function destroy($ID_INSTRUCTOR)
    {
        $instructor = instructor::find($ID_INSTRUCTOR);

        if (is_null($instructor)) {
            return response([
                'message' => 'Instructor Not Found',
                'date' => null
            ], 404);
        }

        if ($instructor->delete()) {
            return response([
                'message' => 'Delete Instructor Success',
                'data' => $instructor
            ], 200);
        }

        return response([
            'message' => 'Delete Instructor Failed',
            'data' => null,
        ], 400);
    }

    public function resetLateAmount()
    {
        //Reset 1 kali sehari
        //Bukan 1 kali sebulan
        //GANTI CODE
        $currentDate = Carbon::now();
        $firstDayOfMonth = Carbon::now()->firstOfMonth();

        if (!$currentDate->equalTo($firstDayOfMonth)) {
            return response([
                'success' => false,
                'message' => 'Instructor late amount can only be resetted on the first day of the month'
            ], 400);
        } else {
            $instructors = Instructor::all();
            foreach ($instructors as $instructor) {
                $instructor->LATE_AMOUNT = 0;
                $instructor->save();
            }
        }
        return response([
            'success' => true,
            'message' => 'All Instructor late amount has been resetted',
            'data' => $instructor
        ], 200);

        //TESTING
        // $instructors = Instructor::all();
        // foreach ($instructors as $instructor) {
        //     $instructor->LATE_AMOUNT = 0;
        //     $instructor->save();
        // }
        // return response([
        //     'success' => true,
        //     'message' => 'All Instructor late amount has been resetted',
        //     'data' => $instructor
        // ], 200);
    }
}
