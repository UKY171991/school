<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Teacher::with('school')->latest();
            if (auth()->user()->isMasterAdmin() && $request->has('school_id') && !empty($request->school_id)) {
                $query->where('school_id', $request->school_id);
            }
            $teachers = $query->get();
            return response()->json($teachers);
        }
        
        $admins = collect();
        if (auth()->user()->isMasterAdmin()) {
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('slug', 'admin');
            })->with('school')->get();
        }
        
        return view('teacher-profiles.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id' => auth()->user()->isMasterAdmin() ? 'required|exists:schools,id' : 'nullable',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        if (!auth()->user()->isMasterAdmin()) {
            $validated['school_id'] = auth()->user()->school_id;
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('teachers/photos', 'public');
        }

        if ($request->hasFile('signature')) {
            $validated['signature'] = $request->file('signature')->store('teachers/signatures', 'public');
        }

        $teacher = Teacher::create($validated);

        return response()->json(['success' => __('Teacher profile created.'), 'teacher' => $teacher]);
    }

    public function show(string $id)
    {
        $teacher = Teacher::with('school')->findOrFail($id);
        return response()->json($teacher);
    }

    public function update(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);
        
        $validated = $request->validate([
            'school_id' => auth()->user()->isMasterAdmin() ? 'required|exists:schools,id' : 'nullable',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,'.$teacher->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        if (!auth()->user()->isMasterAdmin()) {
            $validated['school_id'] = auth()->user()->school_id;
        }

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $validated['photo'] = $request->file('photo')->store('teachers/photos', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($teacher->signature) {
                Storage::disk('public')->delete($teacher->signature);
            }
            $validated['signature'] = $request->file('signature')->store('teachers/signatures', 'public');
        }

        $teacher->update($validated);

        return response()->json(['success' => __('Teacher profile updated.'), 'teacher' => $teacher]);
    }

    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }
        if ($teacher->signature) {
            Storage::disk('public')->delete($teacher->signature);
        }

        $teacher->delete();

        return response()->json(['success' => __('Teacher profile deleted.')]);
    }

    public function deleteAsset(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $type = $request->type; // 'photo' or 'signature'
        
        if ($type == 'photo' && $teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
            $teacher->photo = null;
        } elseif ($type == 'signature' && $teacher->signature) {
            Storage::disk('public')->delete($teacher->signature);
            $teacher->signature = null;
        }
        
        $teacher->save();
        
        return response()->json(['success' => __(ucfirst($type)) . ' ' . __('deleted successfully.')]);
    }
}
