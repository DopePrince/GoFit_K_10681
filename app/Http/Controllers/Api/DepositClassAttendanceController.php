<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\deposit_class_attendance;
use App\Http\Requests\Storedeposit_class_attendanceRequest;
use App\Http\Requests\Updatedeposit_class_attendanceRequest;

class DepositClassAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Storedeposit_class_attendanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storedeposit_class_attendanceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\deposit_class_attendance  $deposit_class_attendance
     * @return \Illuminate\Http\Response
     */
    public function show(deposit_class_attendance $deposit_class_attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\deposit_class_attendance  $deposit_class_attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(deposit_class_attendance $deposit_class_attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updatedeposit_class_attendanceRequest  $request
     * @param  \App\Models\deposit_class_attendance  $deposit_class_attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Updatedeposit_class_attendanceRequest $request, deposit_class_attendance $deposit_class_attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\deposit_class_attendance  $deposit_class_attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(deposit_class_attendance $deposit_class_attendance)
    {
        //
    }
}
