<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\member;
use App\Models\instructor;
use App\Models\pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'EMAIL' => 'required',
            'PASSWORD' => 'required'
        ]);

        if (is_null($request->EMAIL) || is_null($request->PASSWORD)) {
            return response(['message' => 'Inputan tidak boleh kosong'], 400);
        }

        $pegawai = null;
        $instructor = null;
        $member = null;

        if (pegawai::where('EMAIL', '=', $loginData['EMAIL'])->first()) {
            $loginPegawai = pegawai::where('EMAIL', '=', $loginData['EMAIL'])->first();

            if (Hash::check($loginData['PASSWORD'], $loginPegawai['PASSWORD'])) {
                $pegawai = pegawai::where('EMAIL', $loginData['EMAIL'])->first();
            } else {
                return response([
                    'message' => 'Wrong Pegawai Email or Password',
                    'data' => $pegawai
                ], 400);
            }
            $token = bcrypt(uniqid());
            return response([
                'message' => 'Successfully logged in as Pegawai',
                'data' => $pegawai,
                'role' => $pegawai['ROLE'],
                'token' => $token
            ]);
        } else if (instructor::where('EMAIL', '=', $loginData['EMAIL'])->first()) {
            $loginInstructor = instructor::where('EMAIL', '=', $loginData['EMAIL'])->first();

            if (Hash::check($loginData['PASSWORD'], $loginInstructor['PASSWORD'])) {
                $instructor = instructor::where('EMAIL', $loginData['EMAIL'])->first();
            } else {
                return response([
                    'message' => 'Wrong Instructor Email or Password',
                    'data' => $instructor
                ], 400);
            }
            $token = bcrypt(uniqid());
            return response([
                'message' => 'Successfully logged in as Instructor',
                'data' => $instructor,
                'role' => 'Instructor',
                'token' => $token
            ]);
        } else {
            $loginMember = member::where('EMAIL', '=', $loginData['EMAIL'])->first();

            if (Hash::check($loginData['PASSWORD'], $loginMember['PASSWORD'])) {
                $member = member::where('EMAIL', $loginData['EMAIL'])->first();
            } else {
                return response([
                    'message' => 'Wrong Member Email or Password',
                    'data' => $member
                ], 400);
            }
            $token = bcrypt(uniqid());
            return response([
                'message' => 'Successfully logged in as Member',
                'data' => $member,
                'role' => 'Member',
                'token' => $token
            ]);
        }

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'EMAIL' => 'required|email',
            'OLDPASS' => 'required',
            'NEWPASS' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 422);
        }

        $pegawai = pegawai::where('EMAIL', $request->EMAIL)->firstOrFail();

        if (Hash::check($request->OLDPASS, $pegawai->PASSWORD)) {
            $pegawai->update([
                'PASSWORD' => bcrypt($request->NEWPASS),
            ]);

            return response([
                'success' => true,
                'message' => 'Password changed successfully',
                'pegawai' => $pegawai,
            ], 200);
        } else {
            return response([
                'success' => false,
                'message' => 'Current password does not match',
            ], 401);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'EMAIL' => 'required|email',
            'PASSWORD' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 422);
        }

        $member = member::where('EMAIL', $request->EMAIL)->firstOrFail();
        // if (!$member) {
        //     return response([
        //         'success' => false,
        //         'message' => 'Member Not Found',
        //     ], 404);
        // }

        // $resettedPass = bcrypt($member->TANGGAL_LAHIR);
        // $member->update([
        //     'PASSWORD' => $resettedPass
        // ]);

        $member->update([
            'PASSWORD' => bcrypt($member->TANGGAL_LAHIR)
        ]);

        if ($member) {
            return response([
                'success' => true,
                'message' => 'Password resetted successfully',
                'member' => $member,
            ], 200);
        } else {
            return response([
                'success' => false,
                'message' => 'Password failed to change',
            ], 409);
        }
    }
}
