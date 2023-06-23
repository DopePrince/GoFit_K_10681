<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\promo_regular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoRegularController extends Controller
{

    public function index()
    {
        $promo_regulars = promo_regular::all();

        if(count($promo_regulars) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $promo_regulars
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
            'ID_PROMO_REGULAR' => 'required',
            'TOPUP_AMOUNT' => 'required',
            'BONUS_REGULAR' => 'required',
            'MIN_DEPOSIT' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $promo_regular = promo_regular::create([
            'ID_PROMO_REGULAR' => $request->ID_PROMO_REGULAR,
            'TOPUP_AMOUNT' => $request->TOPUP_AMOUNT,
            'BONUS_REGULAR' => $request->BONUS_REGULAR,
            'MIN_DEPOSIT' => $request->MIN_DEPOSIT
        ]);

        return response([
            'message' => 'Add Promo Regular Success',
            'data' => $promo_regular
        ], 200);
    }

    public function show($ID_PROMO_REGULAR)
    {
        $promo_regulars = promo_regular::find($ID_PROMO_REGULAR);

        if(!is_null($promo_regulars)){
            return response([
                'message' => 'Retrieve Promo Regular Success',
                'data' => $promo_regulars
            ], 200);
        }

        return response([
            'message' => 'Promo Regular Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_PROMO_REGULAR)
    {
        $promo_regular = promo_regular::find($ID_PROMO_REGULAR);

        if(is_null($promo_regular)){
            return response([
                'message' => 'Promo Regular Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'TOPUP_AMOUNT' => 'required',
            'BONUS_REGULAR' => 'required',
            'MIN_DEPOSIT' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $promo_regular->TOPUP_AMOUNT = $updateData['TOPUP_AMOUNT'];
        $promo_regular->BONUS_REGULAR = $updateData['BONUS_REGULAR'];
        $promo_regular->MIN_DEPOSIT = $updateData['MIN_DEPOSIT'];
        
        if($promo_regular->save()){
            return response([
                'message' => 'Update Promo Regular Success',
                'data' => $promo_regular
            ], 200);
        }

        return response([
            'message' => 'Add Promo Regular Success',
            'data' => $promo_regular
        ], 200);
    }

    public function destroy($ID_PROMO_REGULAR)
    {
        $promo_regular = promo_regular::find($ID_PROMO_REGULAR);

        if(is_null($promo_regular)){
            return response([ 
                'message' => 'Promo Regular Not Found',
                'date' => null
            ], 404);
        }

        if($promo_regular->delete()){
            return response([
                'message' => 'Delete Promo Regular Success',
                'data' => $promo_regular
            ], 200);
        }

        return response([
            'message' => 'Delete Promo Regular Failed',
            'data' => null,
        ], 400);
    }
}
