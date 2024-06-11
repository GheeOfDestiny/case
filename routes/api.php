<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\MedicalRecordController;

Route::apiResource('patients', PatientController::class);
Route::apiResource('doctors', DoctorController::class);
Route::apiResource('appointments', AppointmentController::class);
Route::apiResource('medical-records', MedicalRecordController::class);


Route::middleware('auth:sanctum')->group(function () {

    Route::delete('/delete/{id}', [UserController::class, 'destroy']);

    // Appointments
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'showForDoctor']);
    Route::get('/appointmentCatch/{id}', [AppointmentController::class, 'catch']);
    Route::get('/getAdminAppointments', [AppointmentController::class, 'getAdminAppointments']);


    Route::put('/appointmentsApprove/{id}', [AppointmentController::class, 'approveAppointment']);
    Route::put('/denyAppointment/{id}', [AppointmentController::class, 'denyAppointment']);
    Route::put('/updatePatientAppointment/{id}', [AppointmentController::class, 'updatePatientAppointment']);

    Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
    Route::get('/doctor/records/{id}', [RecordController::class, 'getRecord']);
    Route::put('/updateDate/{id}', [AppointmentController::class, 'updateDate']);

    // Medical Records
    Route::get('/records', [RecordController::class, 'index']);
    Route::post('/records', [RecordController::class, 'store']);
    Route::get('/records/{id}', [RecordController::class, 'show']);
    Route::get('/getDoctorsRecord/{id}', [RecordController::class, 'getDoctorRecords']);
    Route::put('/updateRecord/{id}', [RecordController::class, 'updateRecord']);
    Route::get('/getPatientRecord/{id}', [RecordController::class, 'getPatientRecord']);
    Route::get('/getAdminRecord', [RecordController::class, 'getAdminRecord']);

    Route::put('/records/{id}', [RecordController::class, 'update']);
    Route::delete('/records/{id}', [RecordController::class, 'destroy']);

    

});