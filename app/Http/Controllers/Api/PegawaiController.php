<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\pegawai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawais = pegawai::all();

        if(count($pegawais) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pegawais
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
            'EMAIL' => 'required|unique:pegawais|email',
            'PASSWORD',
            'ROLE' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $rownumber = DB::table('pegawais')->count() + 1;
        $id_number = sprintf("%02d", $rownumber);

        $pegawai = pegawai::create([
            'ID_PEGAWAI' => 'P'.$id_number,
            'FULL_NAME' => $request->FULL_NAME,
            'GENDER' => $request->GENDER,
            'TANGGAL_LAHIR' => $request->TANGGAL_LAHIR,
            'PHONE_NUMBER' => $request->PHONE_NUMBER,
            'ADDRESS' => $request->ADDRESS,
            'EMAIL' => $request->EMAIL,
            'PASSWORD' => bcrypt($request->TANGGAL_LAHIR),
            'ROLE' => $request->ROLE
        ]);

        return response([
            'message' => 'Add Pegawai Success',
            'data' => $pegawai
        ], 200);
    }

    public function show($ID_PEGAWAI)
    {
        $pegawais = pegawai::find($ID_PEGAWAI);

        if(!is_null($pegawais)){
            return response([
                'message' => 'Retrieve Pegawai Success',
                'data' => $pegawais
            ], 200);
        }

        return response([
            'message' => 'Pegawai Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_PEGAWAI)
    {
        $pegawai = pegawai::find($ID_PEGAWAI);

        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Not Found',
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

        if($validator->fails()){
            return response(['message' => $validator->errors()], 400);
        }

        $pegawai->FULL_NAME = $updateData['FULL_NAME'];
        $pegawai->GENDER = $updateData['GENDER'];
        $pegawai->TANGGAL_LAHIR = $updateData['TANGGAL_LAHIR'];
        $pegawai->PHONE_NUMBER = $updateData['PHONE_NUMBER'];
        $pegawai->ADDRESS = $updateData['ADDRESS'];
        $pegawai->EMAIL = $updateData['EMAIL'];
        $pegawai->PASSWORD = bcrypt($updateData['TANGGAL_LAHIR']);

        if($pegawai->save()){
            return response([
                'message' => 'Update Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Add Pegawai Success',
            'data' => $pegawai
        ], 200);
    }

    public function destroy($ID_PEGAWAI)
    {
        $pegawai = pegawai::find($ID_PEGAWAI);

        if(is_null($pegawai)){
            return response([ 
                'message' => 'Pegawai Not Found',
                'date' => null
            ], 404);
        }

        if($pegawai->delete()){
            return response([
                'message' => 'Delete Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Delete Pegawai Failed',
            'data' => null,
        ], 400);
    }
}
