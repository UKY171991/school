<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('next_roll')) {
                $lastRoll = \App\Models\Student::max('roll_number');
                return response()->json(['next_roll' => ($lastRoll ? (int)$lastRoll + 1 : 1001)]);
            }
            $query = \App\Models\Student::with(['grade', 'section', 'school'])->latest();
            
            if (auth()->user()->isMasterAdmin() && $request->has('school_id') && !empty($request->school_id)) {
                $query->where('school_id', $request->school_id);
            }

            if ($request->has('grade_id') && !empty($request->grade_id)) {
                $query->where('grade_id', $request->grade_id);
            }
            if ($request->has('section_id') && !empty($request->section_id)) {
                $query->where('section_id', $request->section_id);
            }

            $students = $query->get();
            return response()->json($students);
        }
        $grades = collect();
        $sections = collect();
        $admins = collect();

        if (auth()->user()->isMasterAdmin()) {
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('slug', 'admin');
            })->with('school')->get();

            if ($request->has('school_id') && !empty($request->school_id)) {
                $grades = \App\Models\Grade::where('school_id', $request->school_id)->orderBy('name', 'asc')->get();
            }
            if ($request->has('grade_id') && !empty($request->grade_id)) {
                $sections = \App\Models\Section::where('grade_id', $request->grade_id)->orderBy('name', 'asc')->get();
            }
        } else {
            $grades = \App\Models\Grade::where('school_id', auth()->user()->school_id)->orderBy('name', 'asc')->get();
            // Important: Keep sections empty initially to prevent duplicates from different grades showing up.
            // The frontend dynamic dropdown logic will populate this when a grade is selected.
            $sections = collect();
        }

        return view('admissions.index', compact('grades', 'sections', 'admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'roll_number' => 'required|string|unique:students,roll_number',
            'dob' => 'required|date',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
            'school_id' => auth()->user()->isMasterAdmin() ? 'required|exists:schools,id' : 'nullable',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        if (!auth()->user()->isMasterAdmin()) {
            $validated['school_id'] = auth()->user()->school_id;
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = \App\Models\Student::create($validated);

        return response()->json(['success' => __('Student admitted successfully.'), 'student' => $student]);
    }

    public function show(string $id)
    {
        $student = \App\Models\Student::with(['grade', 'section', 'school'])->findOrFail($id);
        return response()->json($student);
    }

    public function update(Request $request, string $id)
    {
        $student = \App\Models\Student::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,'.$student->id,
            'roll_number' => 'required|string|unique:students,roll_number,'.$student->id,
            'dob' => 'required|date',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
            'school_id' => auth()->user()->isMasterAdmin() ? 'required|exists:schools,id' : 'nullable',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        if (!auth()->user()->isMasterAdmin()) {
            $validated['school_id'] = auth()->user()->school_id;
        }

        if ($request->hasFile('photo')) {
            if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return response()->json(['success' => __('Student info updated successfully.'), 'student' => $student]);
    }

    public function destroy(string $id)
    {
        $student = \App\Models\Student::findOrFail($id);
        $student->delete();

        return response()->json(['success' => __('Student record deleted successfully.')]);
    }

    public function deletePhoto(string $id)
    {
        $student = \App\Models\Student::findOrFail($id);
        
        if ($student->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            $student->photo = null;
            $student->save();
        }
        
        return response()->json(['success' => __('Photo deleted successfully.')]);
    }
}
