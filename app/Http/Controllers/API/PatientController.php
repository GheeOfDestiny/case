<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        return Patient::with(['appointments', 'medicalRecords'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        return Patient::create($request->all());
    }

    public function show($id)
    {
        return Patient::with(['appointments', 'medicalRecords'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients,email,' . $id,
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($request->all());

        return $patient;
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return response()->noContent();
    }
}
