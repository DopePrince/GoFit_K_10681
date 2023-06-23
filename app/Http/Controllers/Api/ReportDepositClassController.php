<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\report_deposit_class;
use App\Models\member;
use App\Models\pegawai;
use App\Models\class_detail;
use App\Models\promo_class;
use App\Models\class_deposit;
use App\Models\class_on_running;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;


class ReportDepositClassController extends Controller
{

    public function index()
    {
        $report_deposit_classes = report_deposit_class::with('member', 'pegawai', 'class_detail', 'promo_class')->get();

        if (count($report_deposit_classes) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $report_deposit_classes
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
            'ID_PEGAWAI' => 'required',
            'ID_CLASS' => 'required',
            'TOTAL_PACKAGE' => 'required',
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

        $class_detail = class_detail::find($request->ID_CLASS);
        if (!$class_detail) {
            return response([
                'message' => 'Class Not Exist',
                'data' => null
            ], 404);
        }

        // Default data when promo class isEmpty
        $ID_PROMO_CLASS = null;
        $AMOUNT_DEPOSIT = $request->TOTAL_PACKAGE;
        $TOTAL_PACKAGE = $request->TOTAL_PACKAGE;
        $BONUS_PACKAGE = 0;
        $EXPIRE_DATE = null;

        // Check jika ada promo class available untuk class yang dipilih
        $promo_class = promo_class::all();

        foreach ($promo_class as $pc) {
            if ($AMOUNT_DEPOSIT == $pc['AMOUNT_DEPOSIT']) {
                $ID_PROMO_CLASS = $pc['ID_PROMO_CLASS'];
                $BONUS_PACKAGE = $pc['BONUS_PACKAGE'];
                $EXPIRE_DATE_time = Carbon::now()->addMonth($pc['DURATION']);
                $EXPIRE_DATE = $EXPIRE_DATE_time->toDateString();
                $TOTAL_PACKAGE = $AMOUNT_DEPOSIT + $BONUS_PACKAGE;
            }
        }

        // Perhitungan TOTAL_PRICE berdasarkan AMOUNT_DEPOSIT dan harga kelas dari CLASS_DETAIL
        $TOTAL_PRICE = $AMOUNT_DEPOSIT * $class_detail->PRICE;

        // Format for NO_STRUK, TANGGAL_TRANSAKSI
        $rownumber = DB::table('report_deposit_classes')->count() + 1;
        $struk_number = sprintf("%03d", $rownumber);
        $struk_date = Carbon::now()->format('y.m');

        $TANGGAL_TRANSAKSI = Carbon::now();

        // Create Data
        $report_class = report_deposit_class::create([
            'NO_STRUK_CLASS' => $struk_date . '.' . $struk_number,
            'ID_PROMO_CLASS' => $ID_PROMO_CLASS,
            'ID_CLASS' => $class_detail->ID_CLASS,
            'ID_MEMBER' => $request->ID_MEMBER,
            'ID_PEGAWAI' => $request->ID_PEGAWAI,
            'TANGGAL_TRANSAKSI' => $TANGGAL_TRANSAKSI,
            'TOTAL_PRICE' => $TOTAL_PRICE,
            'TOTAL_PACKAGE' => $TOTAL_PACKAGE,
            'EXPIRE_DATE' => $EXPIRE_DATE
        ]);

        if ($report_class) {
            // Menambahkan deposit class ke deposit class_deposit milik member
            $class_deposit = class_deposit::create([
                'ID_MEMBER' => $report_class->ID_MEMBER,
                'ID_CLASS' => $report_class->ID_CLASS,
                'CLASS_AMOUNT' => $report_class->TOTAL_PACKAGE,
                'EXPIRE_DATE' => $report_class->EXPIRE_DATE
            ]);
            return response([
                'message' => 'Deposit Class History Created and add to Member Class Deposit successful',
                'data receipt' => $report_class,
                'data deposit member' => $class_deposit
            ], 201);
        } else {
            return response([
                'message' => 'Create Deposit Class History Failed',
                'data' => $report_class
            ], 409);
        }
    }

    public function show($NO_STRUK_KELAS)
    {
        $report_depost_class = report_deposit_class::find($NO_STRUK_KELAS);

        if (!is_null($report_depost_class)) {
            return response([
                'message' => 'Retrieve Report Deposit Class Success',
                'data' => $report_depost_class
            ], 200);
        }

        return response([
            'message' => 'Report Deposit Class Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $NO_STRUK_KELAS)
    {
        //
    }

    public function destroy($NO_STRUK_KELAS)
    {
        $report_depost_class = report_deposit_class::find($NO_STRUK_KELAS);

        if (is_null($report_depost_class)) {
            return response([
                'message' => 'Report Deposit Class Not Found',
                'date' => null
            ], 404);
        }

        if ($report_depost_class->delete()) {
            return response([
                'message' => 'Delete Report Deposit Class Success',
                'data' => $report_depost_class
            ], 200);
        }

        return response([
            'message' => 'Delete Report Deposit Class Failed',
            'data' => null,
        ], 400);
    }

    public function printReportDepositClass($NO_STRUK_KELAS)
    {
        $report_class = report_deposit_class::find($NO_STRUK_KELAS);

        // $report_class = report_deposit_class::join('promo_classes', 'report_deposit_classes.ID_PROMO_CLASS', '=', 'promo_classes.ID_PROMO_CLASS')
        //                     ->join('class_details', 'promo_classes.ID_CLASS', '=', 'class_details.ID_CLASS')
        //                     ->select('report_deposit_classes.*', 'class_details.CLASS_NAME')
        //                     ->get();

        if (is_null($report_class)) {
            return response([
                'message' => 'Report Deposit Class Not Found',
                'data' => null
            ], 404);
        }

        $class_detail = class_detail::find($report_class->ID_CLASS);
        $member = member::find($report_class->ID_MEMBER);
        $pegawai = pegawai::find($report_class->ID_PEGAWAI);
        $promo_class = promo_class::find($report_class->ID_PROMO_CLASS);

        $data_print = [
            'report_class' => $report_class,
            'member' => $member,
            'pegawai' => $pegawai,
            'class_detail' => $class_detail,
            'promo_class' => $promo_class
        ];

        $pdf = PDF::loadview('report_deposit_class_card', $data_print);

        return $pdf->download('Report_Deposit_Class_Card_' . $member->FULL_NAME . '.pdf');
    }
}
