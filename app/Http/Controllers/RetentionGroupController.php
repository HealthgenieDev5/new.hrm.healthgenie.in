<?php

namespace App\Http\Controllers;

use App\Models\RetentionGroup;
use Illuminate\Http\Request;

class RetentionGroupController extends Controller
{
    // Show all groups (for admin panel or offcanvas)
    public function index()
    {
        $groups = RetentionGroup::orderBy('type')->orderBy('min')->get();
        return view('retention_groups.index', compact('groups'));
    }

    // Show edit form for a group
    public function edit($id)
    {
        $group = RetentionGroup::findOrFail($id);
        $pageTitle = "Edit Retention Group";
        return view('retention_groups.edit', compact('group', 'pageTitle'));
    }

    // Update a group
    public function update(Request $request, $id)
    {
        $group = RetentionGroup::findOrFail($id);

        $request->validate([
            'label' => 'required|string|max:255',
            'min' => 'required|integer',
            'max' => 'required|integer',
            'points' => 'required|integer',
        ]);

        $group->update([
            'label' => $request->label,
            'min' => $request->min,
            'max' => $request->max,
            'points' => $request->points,
        ]);

        return redirect()->back()->with('success', 'Group updated successfully!');
    }
}
