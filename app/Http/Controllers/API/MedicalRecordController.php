<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        return MedicalRecord::with('patient')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'description' => 'required|string',
        ]);

        return MedicalRecord::create($request->all());
    }

    public function show($id)
    {
        return MedicalRecord::with('patient')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'description' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::findOrFail($id);
        $medicalRecord->update($request->all());

        return $medicalRecord;
    }

    public function destroy($id)
    {
        $medicalRecord = MedicalRecord::findOrFail($id);
        $medicalRecord->delete();

        return response()->noContent();
    }
}
