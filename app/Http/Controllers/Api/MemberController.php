<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

class MemberController extends Controller
{
    public function index()
    {
        $members = member::all();

        if (count($members) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $members
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
            'EMAIL' => 'required|unique:members|email',
            'PASSWORD',
            'DEPOSIT_REGULAR_AMOUNT',
            'EXPIRE_DATE',
            'STATUS_MEMBERSHIP'
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $rownumber = DB::table('members')->count() + 1;
        $id_number = sprintf("%03d", $rownumber);

        $id_date = Carbon::now()->format('y.m');

        $member = member::create([
            'ID_MEMBER' => $id_date . '.' . $id_number,
            'FULL_NAME' => $request->FULL_NAME,
            'GENDER' => $request->GENDER,
            'TANGGAL_LAHIR' => $request->TANGGAL_LAHIR,
            'PHONE_NUMBER' => $request->PHONE_NUMBER,
            'ADDRESS' => $request->ADDRESS,
            'EMAIL' => $request->EMAIL,
            'PASSWORD' => bcrypt($request->TANGGAL_LAHIR),
            'DEPOSIT_REGULAR_AMOUNT' => $request->DEPOSIT_REGULAR_AMOUNT,
            'EXPIRE_DATE' => $request->EXPIRE_DATE,
            'STATUS_MEMBERSHIP' => $request->STATUS_MEMBERSHIP
        ]);

        return response([
            'message' => 'Add Member Success',
            'data' => $member
        ], 200);
    }

    public function show($ID_MEMBER)
    {
        $members = member::find($ID_MEMBER);

        if (!is_null($members)) {
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $members
            ], 200);
        }

        return response([
            'message' => 'Member Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $ID_MEMBER)
    {
        $member = member::find($ID_MEMBER);

        if (is_null($member)) {
            return response([
                'message' => 'Member Not Found',
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
            'DEPOSIT_REGULAR_AMOUNT' => 'required',
            'EXPIRE_DATE' => 'required|date_format:Y-m-d',
            'STATUS_MEMBERSHIP' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $member->FULL_NAME = $updateData['FULL_NAME'];
        $member->GENDER = $updateData['GENDER'];
        $member->TANGGAL_LAHIR = $updateData['TANGGAL_LAHIR'];
        $member->PHONE_NUMBER = $updateData['PHONE_NUMBER'];
        $member->ADDRESS = $updateData['ADDRESS'];
        $member->EMAIL = $updateData['EMAIL'];
        $member->PASSWORD = bcrypt($updateData['TANGGAL_LAHIR']);
        $member->DEPOSIT_REGULAR_AMOUNT = $updateData['DEPOSIT_REGULAR_AMOUNT'];
        $member->EXPIRE_DATE = $updateData['EXPIRE_DATE'];
        $member->STATUS_MEMBERSHIP = $updateData['STATUS_MEMBERSHIP'];

        if ($member->save()) {
            return response([
                'message' => 'Update Member Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Add Member Success',
            'data' => $member
        ], 200);
    }

    public function destroy($ID_MEMBER)
    {
        $member = member::find($ID_MEMBER);

        if (is_null($member)) {
            return response([
                'message' => 'Member Not Found',
                'date' => null
            ], 404);
        }

        if ($member->delete()) {
            return response([
                'message' => 'Delete Member Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Delete Member Failed',
            'data' => null,
        ], 400);
    }

    public function generateMemberCard($ID_MEMBER)
    {
        $member = member::find($ID_MEMBER);

        if (is_null($member)) {
            return response([
                'message' => 'Member Not Found',
                'data' => null
            ], 404);
        }

        $data_print = [
            'member' => $member
        ];

        $pdf = PDF::loadview('member_card', $data_print);

        return $pdf->download('Member_Card_' . $member->FULL_NAME . '.pdf');
    }

    // public function deactivateMembers()
    // {
    //     $members = member::where('EXPIRE_DATE', '<=', Carbon::today())->get();

    //     foreach ($members as $member) {
    //         $expireDate = $member->EXPIRE_DATE;
    //         $today = Carbon::today();

    //         if ($expireDate->lte($today)) {
    //             $member->STATUS_MEMBERSHIP = 0;
    //             $member->save();
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Members membership status updated successfully',
    //         'data' => $member
    //     ], 200);
    // }

    public function deactivateMembers()
    {
        // Bikin hanya bisa tekan sekali setiap hari
        $members = member::where('EXPIRE_DATE', '<=', Carbon::today())->get();

        foreach ($members as $member) {
            $member->STATUS_MEMBERSHIP = 0;
            $member->save();
        }

        return response([
            'message' => 'Members membership status updated successfully',
            'data' => $members
        ], 200);
    }

    public function showExpiringMembers()
    {
        $members = member::where('EXPIRE_DATE', '<=', Carbon::today())->get();

        if (count($members) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $members
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 200);
    }
}
