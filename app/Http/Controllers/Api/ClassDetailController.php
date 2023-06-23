<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\class_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassDetailController extends Controller
{
    public function index()
    {
        $class_details = class_detail::all();

        if (count($class_details) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $class_details
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
            'ID_CLASS' => 'required',
            'CLASS_NAME' => 'required',
            'PRICE' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $class_detail = class_detail::create([
            'ID_CLASS' => $request->ID_CLASS,
            'CLASS_NAME' => $request->CLASS_NAME,
            'PRICE' => $request->PRICE
        ]);

        // $class_detail = class_detail::create($storeData);

        return response([
            'message' => 'Add Class Detail Success',
            'data' => $class_detail
        ], 200);
    }

    public function show($ID_CLASS)
    {
        $class_details = class_detail::find($ID_CLASS);

        if (!is_null($class_details)) {
            return response([
                'message' => 'Retrieve Class Detail Success',
                'data' => $class_details
            ], 200);
        }

        return response([
            'message' => 'Class Detail Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_CLASS)
    {
        $class_detail = class_detail::find($ID_CLASS);

        if (is_null($class_detail)) {
            return response([
                'message' => 'Class Detail Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'CLASS_NAME' => 'required',
            'PRICE' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $class_detail->CLASS_NAME = $updateData['CLASS_NAME'];
        $class_detail->PRICE = $updateData['PRICE'];

        if ($class_detail->save()) {
            return response([
                'message' => 'Update Class Detail Success',
                'data' => $class_detail
            ], 200);
        }

        return response([
            'message' => 'Add Class Detail Success',
            'data' => $class_detail
        ], 200);
    }

    public function destroy($ID_CLASS)
    {
        $class_detail = class_detail::find($ID_CLASS);

        if (is_null($class_detail)) {
            return response([
                'message' => 'Class Detail Not Found',
                'date' => null
            ], 404);
        }

        if ($class_detail->delete()) {
            return response([
                'message' => 'Delete Class Detail Success',
                'data' => $class_detail
            ], 200);
        }

        return response([
            'message' => 'Delete Class Detail Failed',
            'data' => null,
        ], 400);
    }
}
