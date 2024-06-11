<?php

namespace App\Http\Controllers;
use App\Models\Appointment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('patient', 'doctor')->get();
        return response()->json($appointments);
    }

    public function show(Request $request)
    {
        $patient_id = $request->query('patient_id');
    
        $query = DB::table('appointments')
            ->join('users', 'appointments.doctor_id', '=', 'users.id')
            ->select('appointments.*', 'users.name as doctor_name')
            ->where('appointments.patient_id', '=', $patient_id);
    
        $appointments = $query->get();
    
        return response()->json($appointments);
    }
    
    public function showForDoctor(Request $request)
{
    $doctor_id = $request->query('doctor_id');

    $query = DB::table('appointments')
        ->join('users', 'appointments.patient_id', '=', 'users.id')
        ->select('appointments.*', 'users.name as patient_name')
        ->where('appointments.doctor_id', '=', $doctor_id);
        
    $appointments = $query->get();

    return response()->json($appointments);
}

    
    
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'status' => 'required|string',
            'appointment_date' => 'required|date',
        ]);
    
        $appointment = DB::table('appointments')->insert([
            'patient_id' => $request->input('patient_id'),
            'doctor_id' => $request->input('doctor_id'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'appointment_date' => $request->input('appointment_date'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return response()->json($appointment, 201);
    }
    
    public function catch($id)
    {
        $appointment = DB::table('appointments')
            ->select('appointments.*', 'users.name as patient_name')
            ->join('users', 'users.id', '=', 'appointments.patient_id')
            ->where('appointments.id', $id)
            ->first();
    
        return response()->json($appointment);
    }

    public function updateDate(Request $request, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date',
        ]);
    
        $affected = DB::table('appointments')
                        ->where('id', $id)
                        ->update(['appointment_date' => $request->input('appointment_date')]);
    
        if ($affected) {
            $appointment = DB::table('appointments')->find($id);
            return response()->json($appointment);
        } else {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
    }

    public function updatePatientAppointment(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'doctor_id' => 'required|integer',
            'description' => 'required|string',
            'appointment_date' => 'required|date',
        ]);
    
        DB::table('appointments')
            ->where('id', $id)
            ->update([
                'doctor_id' => $request->doctor_id,
                'description' => $request->description,
                'appointment_date' => $request->appointment_date,
                'updated_at' => now(),
            ]);
    
        return response()->json(['message' => 'Appointment updated successfully'], 200);
    }
    
    
    
  

    public function approveAppointment(Request $request, $id)
{
    $validated = $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
    ]);

    $appointment_id = $validated['appointment_id'];

    DB::transaction(function () use ($appointment_id) {
        DB::table('appointments')
            ->where('id', $appointment_id)
            ->update(['status' => 'Approved']);

        $appointment = DB::table('appointments')->where('id', $appointment_id)->first();

        DB::table('records')->insert([
            'appointment_id' => $appointment_id,
            'doctor_id' => $appointment->doctor_id,
            'findings' => '',
            'payment_status' => 'Pending',
            
        ]);
    });

    return response()->json([
        'message' => 'Appointment approved and record created successfully.'
    ]);
}


public function denyAppointment(Request $request, $id)
{
    $validated = $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
    ]);

    $appointment_id = $validated['appointment_id'];

        DB::table('appointments')
            ->where('id', $appointment_id)
            ->update(['status' => 'Denied']);

            return response()->json([
                'message' => 'Appointment denied.'
            ]);
    }



    public function getAdminAppointments()
{
    $appointments = DB::table('appointments')
        ->join('users as patients', 'appointments.patient_id', '=', 'patients.id')
        ->join('users as doctors', 'appointments.doctor_id', '=', 'doctors.id')
        ->select(
            'appointments.description',
            'appointments.appointment_date',
            'patients.name as patient_name',
            'doctors.name as doctor_name'
        )
        ->get();

    return $appointments;
}

    




    

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json(null, 204);
    }
}
