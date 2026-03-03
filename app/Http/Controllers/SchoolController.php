<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\School::with(['admin', 'branches']);
            if (!auth()->user()->isMasterAdmin()) {
                $query->where('id', auth()->user()->school_id);
            }
            $schools = $query->latest()->get();
            return response()->json($schools);
        }

        $admins = collect();
        if (auth()->user()->isMasterAdmin()) {
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('slug', 'admin');
            })->get();
        }

        return view('schools.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'domain_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'principal_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        if ($request->hasFile('principal_signature')) {
            $validated['principal_signature'] = $request->file('principal_signature')->store('schools/signatures', 'public');
        }

        $school = \App\Models\School::create($validated);

        if ($request->has('admin_id') && !empty($request->admin_id)) {
            \App\Models\User::where('id', $request->admin_id)->update(['school_id' => $school->id]);
        }

        return response()->json(['success' => __('School created successfully.'), 'school' => $school]);
    }

    public function show(string $id)
    {
        $school = \App\Models\School::with('admin')->findOrFail($id);
        return response()->json($school);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'domain_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'principal_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $school = \App\Models\School::findOrFail($id);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($school->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        if ($request->hasFile('principal_signature')) {
            // Delete old signature if exists
            if ($school->principal_signature) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($school->principal_signature);
            }
            $validated['principal_signature'] = $request->file('principal_signature')->store('schools/signatures', 'public');
        }

        $school->update($validated);

        if (auth()->user()->isMasterAdmin()) {
            // Remove old admin association
            \App\Models\User::where('school_id', $school->id)->where('id', '!=', $request->admin_id)->update(['school_id' => null]);
            
            // Add new admin association
            if ($request->has('admin_id') && !empty($request->admin_id)) {
                \App\Models\User::where('id', $request->admin_id)->update(['school_id' => $school->id]);
            }
        }

        return response()->json(['success' => __('School updated successfully.'), 'school' => $school]);
    }

    public function destroy(string $id)
    {
        $school = \App\Models\School::findOrFail($id);
        
        // Delete files
        if ($school->logo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($school->logo);
        }
        if ($school->principal_signature) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($school->principal_signature);
        }

        $school->delete();

        return response()->json(['success' => __('School deleted successfully.')]);
    }

    public function deleteAsset(Request $request, string $id)
    {
        $school = \App\Models\School::findOrFail($id);
        $type = $request->type; // 'logo' or 'principal_signature'
        
        if ($type == 'logo' && $school->logo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($school->logo);
            $school->logo = null;
        } elseif ($type == 'principal_signature' && $school->principal_signature) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($school->principal_signature);
            $school->principal_signature = null;
        }
        
        $school->save();
        
        return response()->json(['success' => __(ucfirst(str_replace('_', ' ', $type))) . ' ' . __('deleted successfully.')]);
    }
}
