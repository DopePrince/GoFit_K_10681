<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\report_deposit_regular;
use App\Models\member;
use App\Models\pegawai;
use App\Models\promo_regular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

class ReportDepositRegularController extends Controller
{
    public function index()
    {
        $report_regulars = report_deposit_regular::with('member', 'pegawai', 'promo_regular')->get();

        if (count($report_regulars) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $report_regulars
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
            // 'NO_STRUK_REGULAR' => 'required',
            'ID_MEMBER' => 'required',
            'ID_PEGAWAI' => 'required',
            'ID_PROMO_REGULAR' => 'required',
            // 'TANGGAL_TRANSAKSI' => 'required',
            'TOPUP_AMOUNT' => 'required',
            // 'BONUS' => 'required',
            // 'REMAINING_REGULAR' => 'required',
            // 'TOTAL_REGULAR' => 'required'
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

        //Default data when promo regular isEmpty
        $ID_PROMO_REGULAR = null;
        $BONUS_REGULAR = 0;
        $REMAINING_REGULAR = $member->DEPOSIT_REGULAR_AMOUNT + 0;
        $TOTAL_REGULAR = $REMAINING_REGULAR + $request->TOPUP_AMOUNT + $BONUS_REGULAR;

        //Check jika member valid untuk diberlakukan promo regular
        $PROMO_REGULAR = promo_regular::where('MIN_DEPOSIT', '<=', $member->DEPOSIT_REGULAR_AMOUNT)
                                      ->where('TOPUP_AMOUNT', '<=', $request->TOPUP_AMOUNT)
                                      ->orderBy('BONUS_REGULAR', 'desc')
                                      ->first();

        //Jika ada promo regular yang available untuk member yang melakukan deposit. 
        // Jika promo available, maka akan menetapkan ID dan BONUS sesuai dengan nilai ketentuan promo
        if($PROMO_REGULAR) {
            $ID_PROMO_REGULAR = $PROMO_REGULAR->ID_PROMO_REGULAR;
            $BONUS_REGULAR = $PROMO_REGULAR->BONUS_REGULAR;
            $TOTAL_REGULAR = $REMAINING_REGULAR + $request->TOPUP_AMOUNT + $BONUS_REGULAR;
        }

        // Format for NO_STRUK, TANGGAL_TRANSAKSI
        $rownumber = DB::table('report_deposit_regulars')->count() + 1;
        $struk_number = sprintf("%03d", $rownumber);
        $struk_date = Carbon::now()->format('y.m');
            
        $TANGGAL_TRANSAKSI = Carbon::now();

        // Create Data
        $report_regular = report_deposit_regular::create([
            'NO_STRUK_REGULAR' => $struk_date . '.' . $struk_number,
            'ID_MEMBER' => $request->ID_MEMBER,
            'ID_PEGAWAI' => $request->ID_PEGAWAI,
            'ID_PROMO_REGULAR' => $ID_PROMO_REGULAR,
            'TANGGAL_TRANSAKSI' => $TANGGAL_TRANSAKSI,
            'TOPUP_AMOUNT' => $request->TOPUP_AMOUNT,
            'BONUS' => $BONUS_REGULAR,
            'REMAINING_REGULAR' => $REMAINING_REGULAR,
            'TOTAL_REGULAR' => $TOTAL_REGULAR
        ]);

        if($report_regular) {
            $member->update([
                'DEPOSIT_REGULAR_AMOUNT' => $TOTAL_REGULAR
            ]);
            return response([
                'message' => 'Deposit Regular History Created and Member Updated successfully',
                'data'    => $report_regular,
                'member'  => $member
            ], 201);
        } else {
            return response([
                'message' => 'Create Deposit Regular History Failed',
                'data' => $report_regular
            ], 409);
        }
    }

    public function show($NO_STRUK_REGULAR)
    {
        $report_regulars = report_deposit_regular::find($NO_STRUK_REGULAR);

        if (!is_null($report_regulars)) {
            return response([
                'message' => 'Retrieve Report Deposit Regular Success',
                'data' => $report_regulars
            ], 200);
        }

        return response([
            'message' => 'Report Deposit Regular Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $NO_STRUK_REGULAR)
    {
        //
    }

    public function destroy($NO_STRUK_REGULAR)
    {
        //
    }

    public function printReportDepositRegular($NO_STRUK_REGULAR)
    {
        $report_regular = report_deposit_regular::find($NO_STRUK_REGULAR);

        if (is_null($report_regular)) {
            return response([
                'message' => 'Report Deposit Regular Not Found',
                'data' => null
            ], 404);
        }

        $member = member::find($report_regular->ID_MEMBER);
        $pegawai = pegawai::find($report_regular->ID_PEGAWAI);
        $promo_regular = promo_regular::find($report_regular->ID_PROMO_REGULAR);

        $data_print = [
            'report_regular' => $report_regular,
            'member' => $member,
            'pegawai' => $pegawai,
            'promo_regular' => $promo_regular
        ];

        $pdf = PDF::loadview('report_deposit_regular_card', $data_print);

        return $pdf->download('Report_Deposit_Regular_Card_' . $member->FULL_NAME . '.pdf');
    }
}
