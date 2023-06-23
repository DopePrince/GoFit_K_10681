<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GymController extends Controller
{
    public function index()
    {
        $gyms = gym::all();

        if (count($gyms) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $gyms
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
            // 'GYM_CAPACITY' => 'required',
            'DATE' => 'required',
            'START_TIME' => 'required',
            'END_TIME' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $gym = gym::create([
            'GYM_CAPACITY' => 10,
            'DATE' => $request->DATE,
            'START_TIME' => $request->START_TIME,
            'END_TIME' => $request->END_TIME
        ]);

        return response([
            'message' => 'Add Gym Success',
            'data' => $gym
        ], 200);
    }

    public function show($ID_GYM)
    {
        $gyms = gym::find($ID_GYM);

        if(!is_null($gyms)){
            return response([
                'message' => 'Retrieve Gym Success',
                'data' => $gyms
            ], 200);
        }

        return response([
            'message' => 'Gym Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_GYM)
    {
        $gym = gym::find($ID_GYM);

        if(is_null($gym)){
            return response([
                'message' => 'Gym Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'GYM_CAPACITY' => 'required',
            'DATE' => 'required',
            'START_TIME' => 'required',
            'END_TIME' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $gym->GYM_CAPACITY = $updateData['GYM_CAPACITY'];
        $gym->DATE = $updateData['DATE'];
        $gym->START_TIME = $updateData['START_TIME'];
        $gym->END_TIME = $updateData['END_TIME'];
        
        if($gym->save()){
            return response([
                'message' => 'Update Gym Success',
                'data' => $gym
            ], 200);
        }

        return response([
            'message' => 'Add Gym Success',
            'data' => $gym
        ], 200);
    }

    public function destroy($ID_GYM)
    {
        $gym = gym::find($ID_GYM);

        if(is_null($gym)){
            return response([ 
                'message' => 'Gym Not Found',
                'date' => null
            ], 404);
        }

        if($gym->delete()){
            return response([
                'message' => 'Delete Gym Success',
                'data' => $gym
            ], 200);
        }

        return response([
            'message' => 'Delete Gym Failed',
            'data' => null,
        ], 400);
    }
}
