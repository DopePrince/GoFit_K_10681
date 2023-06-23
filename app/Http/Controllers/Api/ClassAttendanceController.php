<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\class_attendance;
use App\Http\Requests\Storeclass_attendanceRequest;
use App\Http\Requests\Updateclass_attendanceRequest;

class ClassAttendanceController extends Controller
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
     * @param  \App\Http\Requests\Storeclass_attendanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storeclass_attendanceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\class_attendance  $class_attendance
     * @return \Illuminate\Http\Response
     */
    public function show(class_attendance $class_attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\class_attendance  $class_attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(class_attendance $class_attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updateclass_attendanceRequest  $request
     * @param  \App\Models\class_attendance  $class_attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Updateclass_attendanceRequest $request, class_attendance $class_attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\class_attendance  $class_attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(class_attendance $class_attendance)
    {
        //
    }
}
