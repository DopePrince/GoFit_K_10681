<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\class_on_running;
use App\Models\instructor;
use App\Models\class_detail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassOnRunningController extends Controller
{
    public function index()
    {
        $class_on_runnings = class_on_running::with('instructor', 'class_detail')->get();

        if (count($class_on_runnings) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $class_on_runnings
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
            'ID_CLASS_ON_RUNNING' => 'required',
            'ID_INSTRUCTOR' => 'required',
            'ID_CLASS' => 'required',
            'DATE' => 'required|date_format:Y-m-d',
            'START_CLASS' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $end_class = Carbon::parse($request->START_CLASS)->addHour();

        $day_name = Carbon::parse($request->DATE)->format('l');

        $class_on_running = class_on_running::all();
        foreach ($class_on_running as $class_on_running) {
            if (
                $class_on_running->ID_INSTRUCTOR == $request->ID_INSTRUCTOR &&
                $class_on_running->ID_CLASS == $request->ID_CLASS &&
                $class_on_running->DATE == $request->DATE &&
                $class_on_running->START_CLASS == $request->START_CLASS
            ) {
                return response([
                    'success' => false,
                    'message' => 'Jadwal Umum already exist',
                ], 409);
            } else if (
                $class_on_running->ID_INSRUCTOR == $request->ID_INSTRUCTOR &&
                $class_on_running->DATE == $request->DATE &&
                $class_on_running->START_CLASS == $request->START_CLASS
            ) {
                return response([
                    'success' => false,
                    'message' => 'Instructor conflict with data inside Jadwal Umum',
                ], 409);
            }
        }

        $class_on_running = class_on_running::create([
            'ID_CLASS_ON_RUNNING' => $request->ID_CLASS_ON_RUNNING,
            'ID_INSTRUCTOR' => $request->ID_INSTRUCTOR,
            'ID_CLASS' => $request->ID_CLASS,
            'DATE' => $request->DATE,
            'DAY_NAME' => $day_name,
            'START_CLASS' => $request->START_CLASS,
            'END_CLASS' => $end_class,
            'CLASS_CAPACITY' => 10,
        ]);

        if ($class_on_running) {
            return response([
                'message' => 'Add Class On Running Success',
                'data' => $class_on_running
            ], 201);
        } else {
            return response([
                'message' => 'Add Class On Running Failed',
                'data' => $class_on_running
            ], 409);
        }
    }

    public function show($ID_CLASS_ON_RUNNING)
    {
        $class_on_runnings = class_on_running::find($ID_CLASS_ON_RUNNING);

        if (!is_null($class_on_runnings)) {
            return response([
                'message' => 'Retrieve Class On Running Success',
                'data' => $class_on_runnings
            ], 200);
        }

        return response([
            'message' => 'Class On Running Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_CLASS_ON_RUNNING)
    {
        $class_on_running = class_on_running::find($ID_CLASS_ON_RUNNING);

        if (is_null($class_on_running)) {
            return response([
                'message' => 'Class On Running Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'ID_INSTRUCTOR' => 'required',
            'ID_CLASS' => 'required',
            'DATE' => 'required|date_format:Y-m-d',
            'START_CLASS' => 'required',
            'CLASS_CAPACITY' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $start_class = Carbon::parse($request->START_CLASS);
        $end_class = $start_class->addHour();

        $day_name = Carbon::parse($request->DATE)->format('l');

        $class_on_running = class_on_running::all();
        foreach ($class_on_running as $class_on_running) {
            if (
                $class_on_running->ID_INSTRUCTOR == $request->ID_INSTRUCTOR &&
                $class_on_running->ID_CLASS == $request->ID_CLASS &&
                $class_on_running->DATE == $request->DATE &&
                $class_on_running->START_CLASS == $start_class
            ) {
                return response([
                    'success' => false,
                    'message' => 'Jadwal Umum already exist',
                ], 409);
                break;
            } else if (
                $class_on_running->ID_INSRUCTOR == $request->ID_INSTRUCTOR &&
                $class_on_running->DATE == $request->DATE &&
                $$class_on_runnning->START_CLASS == $request->START_CLASS
            ) {
                return response([
                    'success' => false,
                    'message' => 'Instructor conflict with data inside Jadwal Umum',
                ], 409);
            }
        }

        $class_on_running->ID_INSTRUCTOR = $updateData['ID_INSTRUCTOR'];
        $class_on_running->ID_CLASS = $updateData['ID_CLASS'];
        $class_on_running->DATE = $updateData['DATE'];
        $class_on_running->DAY_NAME = $day_name;
        $class_on_running->START_CLASS = $start_class;
        $class_on_running->END_CLASS = $end_class;
        $class_on_running->CLASS_CAPACITY = $updateData['CLASS_CAPACITY'];

        if ($class_on_running->save()) {
            return response([
                'message' => 'Update Class On Running Success',
                'data' => $class_on_running
            ], 200);
        }

        return response([
            'message' => 'Add Class On Running Success',
            'data' => $class_on_running
        ], 200);
    }

    public function destroy($ID_CLASS_ON_RUNNING)
    {
        $class_on_running = class_on_running::find($ID_CLASS_ON_RUNNING);

        if (is_null($class_on_running)) {
            return response([
                'message' => 'Class On Running Not Found',
                'date' => null
            ], 404);
        }

        if ($class_on_running->delete()) {
            return response([
                'message' => 'Delete Class On Running Success',
                'data' => $class_on_running
            ], 200);
        }

        return response([
            'message' => 'Delete Class On Running Failed',
            'data' => null,
        ], 400);
    }
}
