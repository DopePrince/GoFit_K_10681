<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\class_on_running_daily;
use App\Models\class_on_running;
use App\Models\instructor;
use App\Models\instructor_absent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassOnRunningDailyController extends Controller
{

    public function index()
    {
        $class_on_running_dailies = class_on_running_daily::with('class_on_running', 'class_on_running.class_detail', 'instructor')->get();

        if (count($class_on_running_dailies) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $class_on_running_dailies
            ], 200);
        }

        return response([
            'message' => 'Daily Schedule data has not been generated yet',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
    }

    public function generateDailySchedule()
    {
        $class_on_running_daily = class_on_running_daily::all();
        $class_on_running = class_on_running::all();

        if (!($class_on_running_daily->isEmpty()) || $class_on_running_daily->count() != $class_on_running->count()) {
            DB::table('class_on_running_dailies')->delete();

            foreach ($class_on_running as $cor) {
                $DATE = Carbon::parse($cor['DATE'], 'Asia/Jakarta');
                $STATUS = 'Normal';
                $DAY_NAME = $DATE->format('l');
                $ID_INSTRUCTOR = $cor->ID_INSTRUCTOR;
                $START_CLASS = $cor->START_CLASS;
                $END_CLASS = $cor->END_CLASS;
                $CLASS_CAPACITY = 10;

                $class_on_running_daily = class_on_running_daily::firstOrCreate([
                    'ID_CLASS_ON_RUNNING' => $cor['ID_CLASS_ON_RUNNING'],
                    'ID_INSTRUCTOR' => $ID_INSTRUCTOR,
                    'DATE' => $DATE->addWeek()->format('Y-m-d'),
                    'DAY_NAME' => $DAY_NAME,
                    'START_CLASS' => $START_CLASS,
                    'END_CLASS' => $END_CLASS,
                    'STATUS' => $STATUS,
                    'CLASS_CAPACITY' => $CLASS_CAPACITY
                ]);
            }

            $class_on_running_daily = class_on_running_daily::all();

            if ($class_on_running_daily->isNotEmpty()) {
                return response([
                    'success' => true,
                    'message' => 'Daily Class Schedule generated successfully',
                    'data'    => $class_on_running_daily,
                ], 201);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Failed to genereate Daily Class Schedule',
                    'data'    => $class_on_running_daily,
                ], 409);
            }
        } else {
            $class_on_running_daily = $class_on_running_daily->map(function ($class_on_running_daily) {
                $DATE = $class_on_running_daily->DATE;
                $DAY_NAME = Carbon::parse($DATE)->format('l');
                $class_on_running_daily->update([
                    'DATE' => Carbon::parse($DATE)->addWeek(),
                    'DAY_NAME' => $DAY_NAME,
                ]);

                return $class_on_running_daily;
            });

            $class_on_running_daily = class_on_running_daily::all();

            if ($class_on_running_daily) {
                return response([
                    'success' => true,
                    'message' => 'Daily Class Schedule generated successfully',
                    'data'    => $class_on_running_daily->values(),
                ], 201);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Failed to generate Daily Class Schedule',
                    'data'    => null,
                ], 409);
            }
        }
    }

    public function show($ID_CLASS_ON_RUNNING_DAILY)
    {
        $class_on_running_dailies = class_on_running_daily::find($ID_CLASS_ON_RUNNING_DAILY);

        if (!is_null($class_on_running_dailies)) {
            return response([
                'message' => 'Retrieve Class On Running Daily Success',
                'data' => $class_on_running_dailies
            ], 200);
        }

        return response([
            'message' => 'Class On Running Daily Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_CLASS_ON_RUNNING_DAILY)
    {
        $class_on_running_daily = class_on_running_daily::find($ID_CLASS_ON_RUNNING_DAILY);

        if (is_null($class_on_running_daily)) {
            return response([
                'message' => 'Class On Running Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'STATUS' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $class_on_running_daily->STATUS = $updateData['STATUS'];

        if ($class_on_running_daily->save()) {
            return response([
                'message' => 'Update Class On Running Daily Success',
                'data' => $class_on_running_daily
            ], 200);
        }

        return response([
            'message' => 'Add Class On Running Daily Success',
            'data' => $class_on_running_daily
        ], 200);
    }

    public function destroy($ID_CLASS_ON_RUNNING_DAILY)
    {
        $class_on_running_daily = class_on_running::find($ID_CLASS_ON_RUNNING_DAILY);

        if (is_null($class_on_running_daily)) {
            return response([
                'message' => 'Class On Running Daily Not Found',
                'date' => null
            ], 404);
        }

        if ($class_on_running_daily->delete()) {
            return response([
                'message' => 'Delete Class On Running Daily Success',
                'data' => $class_on_running_daily
            ], 200);
        }

        return response([
            'message' => 'Delete Class On Running Daily Failed',
            'data' => null,
        ], 400);
    }
}
