<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\class_deposit;
use App\Models\class_detail;
use App\Models\member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassDepositController extends Controller
{

    public function index()
    {
        $class_deposits = class_deposit::with('member', 'class_detail')->get();

        if (count($class_deposits) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $class_deposits
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
            'ID_CLASS_DEPOSIT' => 'required',
            'ID_MEMBER' => 'required',
            'ID_CLASS' => 'required',
            'CLASS_AMOUNT' => 'required',
            'EXPIRE_DATE' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $class_deposit = class_deposit::create([
            'ID_CLASS_DEPOSIT' => $request->ID_CLASS_DEPOSIT,
            'ID_MEMBER' => $request->ID_MEMBER,
            'ID_CLASS' => $request->ID_CLASS,
            'CLASS_AMOUNT' => $request->CLASS_AMOUNT,
            'EXPIRE_DATE' => $request->EXPIRE_DATE
        ]);

        if ($class_deposit) {
            return response([
                'message' => 'Add Class Deposit Success',
                'data' => $class_deposit,
            ], 201);
        } else {
            return response([
                'success' => false,
                'message' => 'Add Class Deposit Failed',
                'data'    => $class_deposit
            ], 409);
        }
    }

    public function show($ID_CLASS_DEPOSIT)
    {
        $class_deposits = class_deposit::find($ID_CLASS_DEPOSIT);

        if (!is_null($$class_deposits)) {
            return response([
                'message' => 'Retrieve Class Deposit Success',
                'data' => $$class_deposits
            ], 200);
        }

        return response([
            'message' => 'Class Deposit Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_CLASS_DEPOSIT)
    {
        $class_deposit = class_deposit::find($ID_CLASS_DEPOSIT);

        if (is_null($class_deposit)) {
            return response([
                'message' => 'Class Deposit Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'ID_MEMBER' => 'required',
            'ID_CLASS' => 'required',
            'CLASS_AMOUNT' => 'required',
            'EXPIRE_DATE' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $class_deposit->ID_MEMBER = $updateData['ID_MEMBER'];
        $class_deposit->ID_CLASS = $updateData['ID_CLASS'];
        $class_deposit->CLASS_AMOUNT = $updateData['CLASS_AMOUNT'];
        $class_deposit->EXPIRE_DATE = $updateData['EXPIRE_DATE'];

        if ($class_deposit->save()) {
            return response([
                'message' => 'Update Class Deposit Success',
                'data' => $class_deposit
            ], 200);
        }

        return response([
            'message' => 'Add Class Deposit Success',
            'data' => $class_deposit
        ], 200);
    }

    public function destroy($ID_CLASS_DEPOSIT)
    {
        $class_deposit = class_deposit::find($ID_CLASS_DEPOSIT);

        if (is_null($$class_deposit)) {
            return response([
                'message' => 'Class Deposit Not Found',
                'date' => null
            ], 404);
        }

        if ($class_deposit->delete()) {
            return response([
                'message' => 'Delete Class Deposit Success',
                'data' => $$class_deposit
            ], 200);
        }

        return response([
            'message' => 'Delete Class Deposit Failed',
            'data' => null,
        ], 400);
    }

    public function listExpiringMemberClassDeposits()
    {
        $expiring_members_class_deposits = DB::table('members')
            ->join('class_deposits', 'class_deposits.ID_MEMBER', '=', 'members.ID_MEMBER')
            ->whereDate('class_deposits.EXPIRE_DATE', '<=', Carbon::today()->toDateString())
            ->get(['class_deposits.*', 'members.ID_MEMBER']);

        if ($expiring_members_class_deposits->isEmpty()) {
            return response([
                'message' => 'There is no member with expiring class deposit',
                'data' => null
            ], 400);
        } else {
            return response([
                'success' => true,
                'message' => 'Members with class deposit expiring today successfully retrieved',
                'data' => $expiring_members_class_deposits
            ], 200);
        }
    }

    public function resetClassDeposit()
    {
        //Buat reset 1 kali sehari
        $currentDate = Carbon::now();
        $firstDayOfMonth = Carbon::now()->firstOfMonth();

        if (!$currentDate->equalTo($firstDayOfMonth)) {
            return response([
                'success' => false,
                'message' => 'Class deposit can only be resetted on the first day of the month'
            ], 400);
        } else {
            $expired_deposits = class_deposit::where('EXPIRE_DATE', '<=', Carbon::today())->get();

            foreach ($expired_deposits as $deposit) {
                $deposit->CLASS_AMOUNT = 0;
                $deposit->EXPIRE_DATE = null;
                $deposit->save();
            }

            return response([
                'success' => true,
                'message' => 'Class Deposit has been resetted',
                'data' => $deposit
            ], 200);
        }

        // TESTING
        // $expired_deposits = class_deposit::where('EXPIRE_DATE', '<=', Carbon::today())->get();

        // foreach ($expired_deposits as $deposit) {
        //     $deposit->CLASS_AMOUNT = 0;
        //     $deposit->EXPIRE_DATE = null;
        //     $deposit->save();
        // }

        // return response([
        //     'success' => true,
        //     'message' => 'Class Deposit has been resetted',
        //     'data' => $deposit
        // ], 200);
    }
}
