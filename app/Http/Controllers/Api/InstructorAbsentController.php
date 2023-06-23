<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\instructor;
use Illuminate\Http\Request;
use App\Models\instructor_absent;
use App\Models\class_on_running_daily;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstructorAbsentController extends Controller
{
    public function index()
    {
        $instructor_absent = instructor_absent::with('class_on_running_daily')->get();

        if (count($instructor_absent) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $instructor_absent
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
            'ID_INSTRUCTOR' => 'required',
            'ID_SUBSTITUTE_INSTRUCTOR' => 'nullable',
            'ID_CLASS_ON_RUNNING_DAILY' => 'required',
            'ABSENT_DATE_TIME',
            'ABSENT_REASON' => 'required',
            'IS_CONFIRMED'
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $instructor_absent = instructor_absent::create([
            'ID_INSTRUCTOR' => $request->ID_INSTRUCTOR,
            'ID_SUBSTITUTE_INSTRUCTOR' => $request->ID_SUBSTITUTE_INSTRUCTOR,
            'ID_CLASS_ON_RUNNING_DAILY' => $request->ID_CLASS_ON_RUNNING_DAILY,
            'ABSENT_DATE_TIME' => Carbon::now(),
            'ABSENT_REASON' => $request->ABSENT_REASON,
            'IS_CONFIRMED' => 0
        ]);

        if ($instructor_absent) {
            return response([
                'message' => 'Add Instructor Absent Success',
                'data'    => $instructor_absent
            ], 201);
        } else {
            return response([
                'message' => 'Add Instructor Absent Failed',
                'data'    => null
            ], 409);
        }
    }

    public function show($ID_INSTRUCTOR_ABSENT)
    {
        $instructor_absent = instructor_absent::find($ID_INSTRUCTOR_ABSENT);

        if (!is_null($instructor_absent)) {
            return response([
                'message' => 'Retrieve Instructor Absent Success',
                'data' => $instructor_absent
            ], 200);
        }

        return response([
            'message' => 'Instructor Absent Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_INSTRUCTOR_ABSENT)
    {
        $instructor_absent = instructor_absent::find($ID_INSTRUCTOR_ABSENT);
        if (!$instructor_absent) {
            return response([
                'message' => 'Instructor Absent Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'ID_INSTRUCTOR' => 'required',
            'ID_SUBSTITUTE_INSTRUCTOR' => 'nullable',
            'ID_CLASS_ON_RUNNING_DAILY' => 'required',
            'ABSENT_REASON' => 'required',
            'IS_CONFIRMED'
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $instructor_absent->ID_INSTRUCTOR = $updateData['ID_INSTRUCTOR'];
        $instructor_absent->ID_SUBSTITUTE_INSTRUCTOR = $updateData['ID_SUBSTITUTE_INSTRUCTOR'];
        $instructor_absent->ID_CLASS_ON_RUNNING_DAILY = $updateData['ID_CLASS_ON_RUNNING_DAILY'];
        $instructor_absent->ABSENT_REASON = $updateData['ABSENT_REASON'];
        $instructor_absent->IS_CONFIRMED = 0;

        if ($instructor_absent->save()) {
            return response([
                'message' => 'Update Instructor Absent Success',
                'data' => $instructor_absent
            ], 200);
        }

        return response([
            'message' => 'Add Class Detail Success',
            'data' => $instructor_absent
        ], 200);
    }

    public function destroy($ID_INSTRUCTOR_ABSENT)
    {
        $instructor_absent = instructor_absent::find($ID_INSTRUCTOR_ABSENT);

        if (is_null($instructor_absent)) {
            return response([
                'message' => 'Instructor Absent Not Found',
                'date' => null
            ], 404);
        }

        if ($instructor_absent->delete()) {
            return response([
                'message' => 'Delete Instructor Absent Success',
                'data' => $instructor_absent
            ], 200);
        }

        return response([
            'message' => 'Delete Instructor Absent Failed',
            'data' => null,
        ], 400);
    }

    public function showNotConfirmed()
    {
        $instructor_absents = instructor_absent::where('IS_CONFIRMED', 0)->get();

        if (count($instructor_absents) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $instructor_absents
            ], 200);
        }

        return response([
            'message' => 'No not confirmed instructor absences found',
            'data' => null
        ], 200);
    }

    public function updateConfirmation($ID_INSTRUCTOR_ABSENT)
    {
        $instructor_absent = instructor_absent::find($ID_INSTRUCTOR_ABSENT);

        if (is_null($instructor_absent)) {
            return response([
                'message' => 'Instructor Absent Not Found',
                'data' => null
            ], 404);
        }

        $instructor_absent->IS_CONFIRMED = 1;

        if ($instructor_absent->save()) {
            // get the class_on_running_daily data that corresponds with instructor_absent data
            $class_on_running_daily = class_on_running_daily::find($instructor_absent->ID_CLASS_ON_RUNNING_DAILY);

            if (is_null($class_on_running_daily)) {
                return response([
                    'message' => 'Class On Running Daily data Not Found',
                    'data' => null
                ], 404);
            }

            if ($instructor_absent->IS_CONFIRMED) {
                $original_instructor = instructor::find($class_on_running_daily->ID_INSTRUCTOR);

                if (is_null($instructor_absent->ID_SUBSTITUTE_INSTRUCTOR)) {
                    $class_on_running_daily->STATUS = "(libur)";
                } else {
                    $class_on_running_daily->ID_INSTRUCTOR = $instructor_absent->ID_SUBSTITUTE_INSTRUCTOR;
                    $class_on_running_daily->STATUS = "(menggantikan " . $original_instructor->FULL_NAME . ")";
                }
                $class_on_running_daily->save();
            }

            return response([
                'message' => 'Instructor Absent Confirmed',
                'data' => $instructor_absent
            ], 200);
        }

        return response([
            'message' => 'Instructor Absent Confirmation Failed',
            'data' => null
        ], 500);
    }
}
