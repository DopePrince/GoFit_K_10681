<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\promo_class;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoClassController extends Controller
{
    public function index()
    {
        $promo_classes = promo_class::with('class_detail')->get();

        if(count($promo_classes) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $promo_classes
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
            'ID_PROMO_CLASS' => 'required',
            'ID_CLASS' => 'required',
            'AMOUNT_DEPOSIT' => 'required',
            'BONUS_PACKAGE' => 'required',
            'DURATION' => 'required',
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $promo_class = promo_class::create([
            'ID_PROMO_CLASS' => $request->ID_PROMO_CLASS,
            'ID_CLASS' => $request->ID_CLASS,
            'AMOUNT_DEPOSIT' => $request->AMOUNT_DEPOSIT,
            'BONUS_PACKAGE' => $request->BONUS_PACKAGE,
            'DURATION' => $request->DURATION,
        ]);

        return response([
            'message' => 'Add Promo Class Success',
            'data' => $promo_class
        ], 200);
    }

    public function show($ID_PROMO_CLASS)
    {
        $promo_classes = promo_class::find($ID_PROMO_CLASS);

        if(!is_null($promo_classes)){
            return response([
                'message' => 'Retrieve Promo Class Success',
                'data' => $promo_classes
            ], 200);
        }

        return response([
            'message' => 'Promo Class Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_PROMO_CLASS)
    {
        $promo_class = promo_class::find($ID_PROMO_CLASS);

        if(is_null($promo_class)){
            return response([
                'message' => 'Promo Class Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'ID_CLASS' => 'required',
            'AMOUNT_DEPOSIT' => 'required',
            'BONUS_PACKAGE' => 'required',
            'DURATION' => 'required',
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $promo_class->ID_CLASS = $updateData['ID_CLASS'];
        $promo_class->AMOUNT_DEPOSIT = $updateData['AMOUNT_DEPOSIT'];
        $promo_class->BONUS_PACKAGE = $updateData['BONUS_PACKAGE'];
        $promo_class->DURATION = $updateData['DURATION'];
        
        if($promo_class->save()){
            return response([
                'message' => 'Update Promo Class Success',
                'data' => $promo_class
            ], 200);
        }

        return response([
            'message' => 'Add Promo Class Success',
            'data' => $promo_class
        ], 200);
    }

    public function destroy($ID_PROMO_CLASS)
    {
        $promo_class = promo_class::find($ID_PROMO_CLASS);

        if(is_null($promo_class)){
            return response([ 
                'message' => 'Promo Class Not Found',
                'date' => null
            ], 404);
        }

        if($promo_class->delete()){
            return response([
                'message' => 'Delete Promo Class Success',
                'data' => $promo_class
            ], 200);
        }

        return response([
            'message' => 'Delete Promo Class Failed',
            'data' => null,
        ], 400);
    }
}
