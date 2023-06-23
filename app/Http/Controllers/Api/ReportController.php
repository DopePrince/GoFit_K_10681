<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Arr;

class ReportController extends Controller
{
    public function GetReportAktivitasKelas(Request $request)
    {
        $result = DB::select('
        SELECT
            class_details.CLASS_NAME,
            instructors.FULL_NAME,
            CASE WHEN class_on_running_dailies.CLASS_CAPACITY - 10 < 0
                THEN ABS(class_on_running_dailies.CLASS_CAPACITY - 10)
                ELSE class_on_running_dailies.CLASS_CAPACITY - 10
            END AS CLASS_CAPACITY,
            x.libur
        FROM class_on_running_dailies
        INNER JOIN class_on_runnings ON class_on_runnings.ID_CLASS_ON_RUNNING = class_on_running_dailies.ID_CLASS_ON_RUNNING
        INNER JOIN class_details ON class_details.ID_CLASS = class_on_runnings.ID_CLASS
        INNER JOIN instructors ON instructors.ID_INSTRUCTOR = class_on_runnings.ID_INSTRUCTOR
        LEFT JOIN (
            SELECT
                ID_CLASS_ON_RUNNING_DAILY,
                COUNT(instructor_absents.IS_CONFIRMED) AS libur
            FROM instructor_absents
            WHERE IS_CONFIRMED = 1
            GROUP BY ID_CLASS_ON_RUNNING_DAILY
        ) x ON x.ID_CLASS_ON_RUNNING_DAILY = class_on_running_dailies.ID_CLASS_ON_RUNNING_DAILY
        WHERE (class_on_running_dailies.DATE BETWEEN "' . $request->Tanggals . '" AND last_day("' . $request->Tanggals . '"));
    ');

        return response()->json([
            'message' => 'Get Data Success',
            'data' => $result
        ], 200);
    }

    // With TOTAL member returned
    public function GetReportAktivitasGym(Request $request)
    {
        $Result = DB::select('
        SELECT
            DATE_FORMAT(a.Date, "%d %M %Y") AS Date,
            IFNULL(x.member, 0) AS member
        FROM (
            SELECT
                last_day("' . $request->Tanggals . '") - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS Date
            FROM
                (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
        ) a 
        LEFT JOIN (
            SELECT
                COUNT(ID_MEMBER) AS member,
                DAY(Date_Time_Booking) AS tanggal 
            FROM
                gym_bookings
            WHERE
                Date_Time_Booking IS NOT NULL
                AND (Date_Time_Booking BETWEEN "' . $request->Tanggals . '" AND last_day("' . $request->Tanggals . '"))
            GROUP BY
                DAY(Date_Time_Booking)
        ) x ON x.tanggal = DAY(a.Date)
        WHERE
            a.Date BETWEEN "' . $request->Tanggals . '" AND last_day("' . $request->Tanggals . '")
        ORDER BY
            a.Date;
    ');

        $totalMember = 0; // Initialize the total member count

        // Calculate the total member count
        foreach ($Result as $entity) {
            $totalMember += $entity->member;
        }

        return response([
            'message' => 'Get Data Success',
            'data' => $Result,
            'totalMember' => $totalMember, // Include the total member count in the response
        ], 200);
    }

    // Show all instructor even does not have attendance or absent records
    public function GetReportKinerjaInstruktur(Request $request)
    {
        $Result = DB::select('
        SELECT
            I.full_name,
            IFNULL(y.total, 0) AS jumlahHadir,
            IFNULL(z.libur, 0) AS jumlahLibur,
            IFNULL(y.late, 0) AS Telat
        FROM instructors I
        LEFT JOIN (
            SELECT
                ia.ID_INSTRUCTOR AS Instructor,
                COUNT(ia.IS_ATTENDED) + IFNULL(x.total, 0) AS total,
                SUM(TIME_TO_SEC(ia.LATE_AMOUNT)) AS late
            FROM instructor_attendances ia
            LEFT JOIN (
                SELECT
                    ia2.ID_SUBSTITUTE_INSTRUCTOR AS Instructor,
                    COUNT(ia2.IS_CONFIRMED) AS total
                FROM instructor_absents ia2
                WHERE
                    ia2.IS_CONFIRMED = 1
                    AND (
                        ia2.ABSENT_DATE_TIME BETWEEN "' . $request->Tanggals . '"
                        AND last_day("' . $request->Tanggals . '")
                    )
                    AND ia2.ID_SUBSTITUTE_INSTRUCTOR IS NOT NULL
                GROUP BY ia2.ID_SUBSTITUTE_INSTRUCTOR
            ) x ON x.Instructor = ia.ID_INSTRUCTOR
            WHERE
                ia.IS_ATTENDED = 1
                AND (
                    ia.DATE_TIME_PRESENSI BETWEEN "' . $request->Tanggals . '"
                    AND last_day("' . $request->Tanggals . '")
                )
            GROUP BY
                ia.ID_INSTRUCTOR,
                x.total
        ) y ON y.Instructor = I.id_instructor
        LEFT JOIN (
            SELECT
                ID_INSTRUCTOR AS instructor,
                COUNT(IS_CONFIRMED) AS libur
            FROM instructor_absents
            WHERE
                IS_CONFIRMED = 1
                AND (
                    instructor_absents.ABSENT_DATE_TIME BETWEEN "' . $request->Tanggals . '"
                    AND last_day("' . $request->Tanggals . '")
                )
            GROUP BY ID_INSTRUCTOR
        ) z ON z.instructor = I.id_instructor
        ORDER BY y.late;
    ');

        // Array map to find the associated instructor
        $instructorResults = [];
        foreach ($Result as $row) {
            $instructorResults[$row->full_name] = $row;
        }

        // Get all instructors from the instructors table
        $instructors = DB::table('instructors')->select('full_name')->get();

        // Merge the instructor results with instructors who don't have records
        $mergedResults = [];
        foreach ($instructors as $instructor) {
            $fullName = $instructor->full_name;
            $result = isset($instructorResults[$fullName]) ? $instructorResults[$fullName] : (object) [
                'full_name' => $fullName,
                'jumlahHadir' => 0,
                'jumlahLibur' => 0,
                'Telat' => 0
            ];
            $mergedResults[] = $result;
        }

        return response([
            'message' => 'Get Data Success',
            'data' => $mergedResults
        ], 200);
    }

    // DIUBAH untuk total deposit yang di show hanya deposit REGULER saja.
    public function GetReportLaporanPendapatan(Request $request)
    {
        $Result = DB::select('
        SELECT
            MONTHNAME(CONCAT("2023-", month.MONTH, "-1")) AS MONTH,
            IFNULL(deposit.Price, 0) AS deposit,
            IFNULL(aktivasi.price, 0) AS aktivasi
        FROM
            (
                SELECT 1 AS MONTH UNION SELECT 2 AS MONTH UNION SELECT 3 AS MONTH UNION SELECT 4 AS MONTH UNION SELECT 5 AS MONTH UNION SELECT 6 AS MONTH
                UNION SELECT 7 AS MONTH UNION SELECT 8 AS MONTH UNION SELECT 9 AS MONTH UNION SELECT 10 AS MONTH UNION SELECT 11 AS MONTH UNION SELECT 12 AS MONTH
            ) AS month
        LEFT JOIN (
            SELECT MONTH(Tanggal_Transaksi) AS Month, SUM(topup_Amount) AS price
            FROM report_deposit_regulars
            WHERE YEAR(Tanggal_Transaksi) = "' . $request->Tanggals . '"
            GROUP BY YEAR(Tanggal_Transaksi), MONTH(Tanggal_Transaksi)
        ) deposit ON deposit.Month = month.Month
        LEFT JOIN (
            SELECT MONTH(Tanggal_Transaksi) AS Month, SUM(price) AS price
            FROM report_aktivasis
            WHERE YEAR(Tanggal_Transaksi) = "' . $request->Tanggals . '"
            GROUP BY YEAR(Tanggal_Transaksi), MONTH(Tanggal_Transaksi)
        ) aktivasi ON aktivasi.Month = month.Month;
    ');

        $resultTotal = 0;
        $index = 0;
        $array = [];

        foreach ($Result as $entity) {
            $resultTotal += $entity->deposit + $entity->aktivasi;
            $indexGraph = (object)["label" => $entity->MONTH, "y" => $entity->deposit + $entity->aktivasi, "x" => $index + 1];
            array_push($array, $indexGraph);
            $index += 1;
        }

        $arrayTotal = [0 => ["result" => $resultTotal]];

        return response()->json([
            'message' => 'Get Data Success',
            'data' => $Result,
            'total' => $arrayTotal,
            'dataGrafik' => $array
        ], 200);
    }


    public function printAktivitasKelas($Tanggal)
    {
        $Periode = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y-m');
        $Tanggal = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y-m-d');
        $Result = DB::select('SELECT class_details.CLASS_NAME,instructors.FULL_NAME,class_on_running_dailies.CLASS_CAPACITY,x.libur FROM `class_on_running_dailies`
        inner join class_on_runnings on class_on_runnings.ID_CLASS_ON_RUNNING = class_on_running_dailies.ID_CLASS_ON_RUNNING
        inner join class_details on class_details.ID_CLASS = class_on_runnings.ID_CLASS
        inner join instructors on instructors.ID_INSTRUCTOR = class_on_runnings.ID_INSTRUCTOR
        left join (
            select ID_CLASS_ON_RUNNING_DAILY,count(instructor_absents.IS_CONFIRMED) as libur from instructor_absents
            where IS_CONFIRMED = 1 group by ID_CLASS_ON_RUNNING_DAILY
        )x on x.ID_CLASS_ON_RUNNING_DAILY = class_on_running_dailies.ID_CLASS_ON_RUNNING_DAILY
        where (class_on_running_dailies.DATE BETWEEN  "' . $Tanggal . '" AND last_day("' . $Tanggal . '"));');

        $data_print = [
            'Results' => $Result,
            'TanggalTerbit' => Carbon::now()->format('d-m-Y'),
            'Periode' => $Periode,
        ];

        $pdf = PDF::loadview('report_aktivasi_kelas_bulanan', $data_print);

        return $pdf->download('Report_Aktivitas_Kelas.pdf');
    }


    public function printReportAktivitasGym($Tanggal)
    {
        $Periode = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y-m');
        $Tanggal = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y-m-d');
        $Result = DB::select('select DATE_FORMAT(a.Date, " %d %M %Y") as Date,if(isnull(x.member),0,x.member )as member
        from (
            select last_day("' . $Tanggal . '") - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY as Date
            from (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as a
            cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as b
            cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as c
        ) a 
        left join (
            select COUNT(ID_MEMBER) as member,DAY(Date_Time_Booking) as tanggal 
            from gym_bookings
            where Date_Time_Booking is not null and (Date_Time_Booking BETWEEN  "' . $Tanggal . '" AND last_day("' . $Tanggal . '"))
            group by DAY(Date_Time_Booking)
        ) x on x.tanggal = DAY(a.Date)
        where a.Date between "' . $Tanggal . '" and last_day("' . $Tanggal . '") order by a.Date;');
        $data_print = [
            'Results' => $Result,
            'TanggalTerbit' => Carbon::now()->format('d-m-Y'),
            'Periode' => $Periode,
        ];

        $pdf = PDF::loadview('report_aktivasi_gym', $data_print);

        return $pdf->download('Report_Aktivitas_Gym.pdf');
    }

    public function printReportKinerjaInstruktur($Tanggal)
    {
        $Periode = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y-m');
        $Tanggal = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y-m-d');
        $Result = DB::select('SELECT full_name,if(isnull(y.total),0,y.total) as jumlahHadir,if(isnull(z.libur),0,z.libur)  as jumlahLibur,if(isnull(I.LATE_AMOUNT),0,TIME_TO_SEC(I.LATE_AMOUNT))  as Telat FROM `instructors` I
        left join (
                select  instructor_attendances.ID_INSTRUCTOR  as Instructor, 	count(instructor_attendances.IS_ATTENDED)+ if(isnull(x.total),0,x.total) as total ,TIME_TO_SEC(LATE_AMOUNT) as late
            from instructor_attendances 
            left join (
                 select  instructor_absents.ID_SUBSTITUTE_INSTRUCTOR  as Instructor, 	count(instructor_absents.IS_CONFIRMED) as total 
            from instructor_absents
            where instructor_absents.IS_CONFIRMED = 1 and (instructor_absents.ABSENT_DATE_TIME BETWEEN "' . $Tanggal . '" AND last_day("' . $Tanggal . '")) and instructor_absents.ID_SUBSTITUTE_INSTRUCTOR is not null
            group by  instructor_absents.ID_SUBSTITUTE_INSTRUCTOR
            )x on x.Instructor = instructor_attendances.ID_INSTRUCTOR
            where IS_ATTENDED = 1 and (DATE_TIME_PRESENSI BETWEEN "' . $Tanggal . '" AND last_day("' . $Tanggal . '"))
            group by  instructor_attendances.ID_INSTRUCTOR ,x.total,TIME_TO_SEC(LATE_AMOUNT)
        )y on y.Instructor = I.id_instructor
        left join (
            select ID_INSTRUCTOR as instructor, COUNT(IS_CONFIRMED) as libur from instructor_absents
        WHERE IS_CONFIRMED = 1 and (instructor_absents.ABSENT_DATE_TIME 	BETWEEN "' . $Tanggal . '" AND last_day("' . $Tanggal . '"))
        group by ID_INSTRUCTOR
        ) z on z.instructor = I.id_instructor order by y.late;');



        $data_print = [
            'Results' => $Result,
            'TanggalTerbit' => Carbon::now()->format('d-m-Y'),
            'Periode' => $Periode,
        ];

        $pdf = PDF::loadview('report_kinerja_intructur', $data_print);

        return $pdf->download('Report_Kinerja_Instructor.pdf');
    }


    public function printReportLaporan($Tanggal)
    {
        $Periode = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y');
        $Tanggal = Carbon::createFromFormat('Ymd', $Tanggal)->format('Y-m-d');
        $Result = DB::select('select MONTHNAME(CONCAT("2018-",month.MONTH,"-1")) as MONTH,if(isnull(deposit.Price),0,deposit.Price)+if(isnull(depositPaket.price),0,depositPaket.price) as deposit,if(isnull(aktivasi.price),0,aktivasi.price) as aktivasi   from 
        (SELECT 1 AS MONTH
                               UNION SELECT 2 AS MONTH
                               UNION SELECT 3 AS MONTH
                               UNION SELECT 4 AS MONTH
                               UNION SELECT 5 AS MONTH
                               UNION SELECT 6 AS MONTH
                               UNION SELECT 7 AS MONTH
                               UNION SELECT 8 AS MONTH
                               UNION SELECT 9 AS MONTH
                               UNION SELECT 10 AS MONTH
                               UNION SELECT 11 AS MONTH
                               UNION SELECT 12 AS MONTH) as month
         LEFT JOIN (
             SELECT MONTH(Tanggal_Transaksi) as Month, SUM(topup_Amount)  as price
            FROM report_deposit_regulars where YEAR(Tanggal_Transaksi) = "' . $Tanggal . '"
            GROUP BY YEAR(Tanggal_Transaksi), MONTH(Tanggal_Transaksi)
         ) deposit on deposit.Month = month.Month
         LEFT JOIN (
             SELECT MONTH(Tanggal_Transaksi) as Month, SUM(price) as price
            FROM report_aktivasis where YEAR(Tanggal_Transaksi) = "' . $Tanggal . '"
            GROUP BY YEAR(Tanggal_Transaksi), MONTH(Tanggal_Transaksi)
         )aktivasi on aktivasi.Month = month.Month
         
          LEFT JOIN (
             SELECT MONTH(Tanggal_Transaksi) as month, SUM(Total_price) as price
            FROM report_deposit_classes where YEAR(Tanggal_Transaksi) = "' . $Tanggal . '"
            GROUP BY YEAR(Tanggal_Transaksi), MONTH(Tanggal_Transaksi)
         )depositPaket on depositPaket.Month = month.Month;');

        $resultTotal = 0;
        foreach ($Result as $entity) {
            $resultTotal += $entity->deposit + $entity->aktivasi;
        }
        $data_print = [
            'Results' => $Result,
            'Total' => $resultTotal,
            'TanggalTerbit' => Carbon::now()->format('d-m-Y'),
            'Periode' => $Periode,
        ];

        $pdf = PDF::loadview('report_laporan_pendapatan', $data_print);

        return $pdf->download('Report_Pendapatan_Bulanan.pdf');
    }
}
