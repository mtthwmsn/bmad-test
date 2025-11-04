<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Stage;
use Illuminate\Http\RedirectResponse;

class StageController extends Controller
{
    /**
     * Store a newly created stage in storage.
     */
    public function store(Competition $competition): RedirectResponse
    {
        $validated = request()->validate([
            'order' => 'required|integer|min:1',
            'name' => 'nullable|string|max:255',
            'expected_seconds' => 'required|integer|min:1',
            'status' => 'required|in:pending,active,complete',
        ], [
            'order.required' => 'The stage order is required.',
            'order.min' => 'The stage order must be at least 1.',
            'expected_seconds.required' => 'The expected seconds is required.',
            'expected_seconds.min' => 'The expected seconds must be at least 1.',
            'status.required' => 'The stage status is required.',
            'status.in' => 'The status must be one of: pending, active, or complete.',
        ]);

        $competition->stages()->create($validated);

        return redirect()
            ->route('admin.competitions.edit', $competition)
            ->with('success', 'Stage added successfully.');
    }

    /**
     * Update the specified stage in storage.
     */
    public function update(Stage $stage): RedirectResponse
    {
        $validated = request()->validate([
            'order' => 'required|integer|min:1',
            'name' => 'nullable|string|max:255',
            'expected_seconds' => 'required|integer|min:1',
            'status' => 'required|in:pending,active,complete',
        ], [
            'order.required' => 'The stage order is required.',
            'order.min' => 'The stage order must be at least 1.',
            'expected_seconds.required' => 'The expected seconds is required.',
            'expected_seconds.min' => 'The expected seconds must be at least 1.',
            'status.required' => 'The stage status is required.',
            'status.in' => 'The status must be one of: pending, active, or complete.',
        ]);

        $stage->update($validated);

        return redirect()
            ->route('admin.competitions.edit', $stage->competition)
            ->with('success', 'Stage updated successfully.');
    }

    /**
     * Remove the specified stage from storage.
     */
    public function destroy(Stage $stage): RedirectResponse
    {
        $competition = $stage->competition;
        $stage->delete(); // Cascade deletes measures via database constraint

        return redirect()
            ->route('admin.competitions.edit', $competition)
            ->with('success', 'Stage deleted successfully.');
    }
}
