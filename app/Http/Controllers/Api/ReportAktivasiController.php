<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\report_aktivasi;
use App\Models\member;
use App\Models\pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

class ReportAktivasiController extends Controller
{

    public function index()
    {
        $report_aktivasis = report_aktivasi::with('member', 'pegawai')->get();

        if (count($report_aktivasis) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $report_aktivasis
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
            'ID_MEMBER' => 'required',
            'ID_PEGAWAI' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }

        $member = member::find($request->ID_MEMBER);
        if (!$member) {
            return response([
                'message' => 'Member Not Exist',
                'data' => null
            ], 404);
        }

        $rownumber = DB::table('report_aktivasis')->count() + 1;
        $struk_number = sprintf("%03d", $rownumber);

        $struk_date = Carbon::now()->format('y.m');

        $TANGGAL_TRANSAKSI = Carbon::now();

        $EXPIRE_DATE = Carbon::parse($TANGGAL_TRANSAKSI)->addYear();

        $report_aktivasi = report_aktivasi::create([
            'NO_STRUK_AKTIVASI' => $struk_date . '.' . $struk_number,
            'ID_MEMBER' => $request->ID_MEMBER,
            'ID_PEGAWAI' => $request->ID_PEGAWAI,
            'TANGGAL_TRANSAKSI' => $TANGGAL_TRANSAKSI,
            'PRICE' => 3000000,
            'EXPIRE_DATE' => $EXPIRE_DATE
        ]);

        if ($report_aktivasi) {
            $member = member::find($request->ID_MEMBER);
            $member->update([
                'EXPIRE_DATE' => $EXPIRE_DATE,
                'STATUS_MEMBERSHIP' => 1
            ]);

            return response([
                'message' => 'Add Report Aktivasi Success',
                'data' => $report_aktivasi,
                'data member' => $member
            ], 201);
        } else {
            return response([
                'success' => false,
                'message' => 'Add Report Aktivasi Failed',
                'data'    => $report_aktivasi
            ], 409);
        }
    }

    public function show($NO_STRUK_AKTIVASI)
    {
        $report_aktivasis = report_aktivasi::find($NO_STRUK_AKTIVASI);

        if (!is_null($report_aktivasis)) {
            return response([
                'message' => 'Retrieve Report Aktivasi Success',
                'data' => $report_aktivasis
            ], 200);
        }

        return response([
            'message' => 'Report Aktivasi Not Found',
            'data' => null
        ], 400);
    }

    // TIDAK BISA DI-UPDATE
    public function update(Request $request, $NO_STRUK_AKTIVASI)
    {
        //
    }

    public function destroy($NO_STRUK_AKTIVASI)
    {
        $report_aktivasi = report_aktivasi::find($NO_STRUK_AKTIVASI);

        if (is_null($report_aktivasi)) {
            return response([
                'message' => 'Report Aktivasi Not Found',
                'date' => null
            ], 404);
        }

        if ($report_aktivasi->delete()) {
            return response([
                'message' => 'Delete Report Aktivasi Success',
                'data' => $report_aktivasi
            ], 200);
        }

        return response([
            'message' => 'Delete Report Aktivasi Failed',
            'data' => null,
        ], 400);
    }

    // Option 2
    public function printReportAktivasi($NO_STRUK_AKTIVASI)
    {
        $report_aktivasi = report_aktivasi::find($NO_STRUK_AKTIVASI);

        if (is_null($report_aktivasi)) {
            return response([
                'message' => 'Report Aktivasi Not Found',
                'data' => null
            ], 404);
        }

        $member = member::find($report_aktivasi->ID_MEMBER);
        $pegawai = pegawai::find($report_aktivasi->ID_PEGAWAI);

        $data_print = [
            'report_aktivasi' => $report_aktivasi,
            'member' => $member,
            'pegawai' => $pegawai
        ];

        $pdf = PDF::loadview('report_aktivasi_card', $data_print);

        return $pdf->download('Report_Aktivasi_Card_' . $member->FULL_NAME . '.pdf');
    }
}
