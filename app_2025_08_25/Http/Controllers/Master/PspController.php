<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\Psp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PspController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $psps = Psp::latest()->paginate(10);
        // dd($psps);
        $data = [
            "pageTitle" => "Psps",
            "psps" => $psps,
        ];
        return view("master.psps.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userAccounts = User::all();
        $data = [
            "pageTitle" => "Create Psp",
            "userAccounts" => $userAccounts,
        ];
        return view("master.psps.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'min:3', 'unique:psps'],
            'city' => ['required', 'min:2'],
            'state' => ['required', 'min:2'],
            'pincode' => ['required', 'digits:6'],
            'address' => ['required', 'min:3'],
            'contact_person' => ['required', 'min:3'],
            'mobile_number' => ['required', 'digits:10'],
            'alternate_number' => ['required', 'digits:10'],
            'email_id' => ['required', 'min:6'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'code' => 'required|string|max:50|unique:psps,code',
            'user_id' => 'nullable|exists:users,id',
            'day_worker_target' => ['required', 'integer', 'min:0'],
            'night_worker_target' => ['required', 'integer', 'min:0'],
        ]);

        $psp = Psp::create($fields);

        if ($request->hasFile('logo')) {
            $upload_dir = 'psp/psp_' . $psp->id;
            $timestamp = time();
            $filename = 'logo_' . $timestamp . '.' . $request->file('logo')->getClientOriginalExtension();
            $logoPath = $request->file('logo')->storeAs($upload_dir, $filename, 'public');
            $psp->update(['logo' => $logoPath]);
        }

        return redirect()->route('master.psps.index')->with('success', 'Psp added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $psp = Psp::findOrFail($id);
        $userAccounts = User::all();
        $data = [
            "pageTitle" => "Edit Psp",
            "psp" => $psp,
            "userAccounts" => $userAccounts
        ];

        return view("master.psps.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // try {
        $psp = Psp::findOrFail($id);
        $fields = $request->validate([
            'name' => ['required', 'min:3', Rule::unique('psps')->ignore($psp->id)],
            'city' => ['required', 'min:2'],
            'state' => ['required', 'min:2'],
            'pincode' => ['required', 'digits:6'],
            'address' => ['required', 'min:3'],
            'contact_person' => ['required', 'min:3'],
            'mobile_number' => ['required', 'digits:10'],
            'alternate_number' => ['required', 'digits:10'],
            'email_id' => ['required', 'min:6'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'code' => ['required', 'string', 'max:50', Rule::unique('psps', 'code')->ignore($psp->id)],
            'user_id' => 'nullable|exists:users,id',
            'day_worker_target' => ['required', 'integer', 'min:0'],
            'night_worker_target' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('logo')) {
            $upload_dir = 'psp/psp_' . $psp->id;
            $timestamp = time();
            $filename = 'logo_' . $timestamp . '.' . $request->file('logo')->getClientOriginalExtension();
            $logoPath = $request->file('logo')->storeAs($upload_dir, $filename, 'public');
            $fields['logo'] = $logoPath;
        } elseif ($request->has('logo_remove') && $request->input('logo_remove') == '1') {
            $fields['logo'] = null;
        }

        $psp->update($fields);
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     dd($e->errors());
        // }
        return redirect()->route('master.psps.index')->with('success', 'Psp updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $psp = Psp::findOrFail($id);
        $psp->delete();
        return redirect()->route('master.psps.index')->with('success', 'Psp deleted successfully');
    }
}
