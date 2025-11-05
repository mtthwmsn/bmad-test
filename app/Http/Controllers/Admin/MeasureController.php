<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Measure;
use App\Models\Stage;
use Illuminate\Http\RedirectResponse;

class MeasureController extends Controller
{
    /**
     * Store a newly created measure in storage.
     */
    public function store(Stage $stage): RedirectResponse
    {
        $validated = request()->validate([
            'target_ml' => 'required|numeric|min:0.1',
            'order' => 'required|integer|min:1',
        ], [
            'target_ml.required' => 'The target ml is required.',
            'target_ml.min' => 'The target ml must be at least 0.1.',
            'order.required' => 'The measure order is required.',
            'order.min' => 'The measure order must be at least 1.',
        ]);

        $stage->measures()->create($validated);

        return redirect()
            ->route('admin.competitions.edit', $stage->competition)
            ->with('success', 'Measure added successfully.');
    }

    /**
     * Update the specified measure in storage.
     */
    public function update(Measure $measure): RedirectResponse
    {
        $validated = request()->validate([
            'target_ml' => 'required|numeric|min:0.1',
            'order' => 'required|integer|min:1',
        ], [
            'target_ml.required' => 'The target ml is required.',
            'target_ml.min' => 'The target ml must be at least 0.1.',
            'order.required' => 'The measure order is required.',
            'order.min' => 'The measure order must be at least 1.',
        ]);

        $measure->update($validated);

        return redirect()
            ->route('admin.competitions.edit', $measure->stage->competition)
            ->with('success', 'Measure updated successfully.');
    }

    /**
     * Remove the specified measure from storage.
     */
    public function destroy(Measure $measure): RedirectResponse
    {
        $competition = $measure->stage->competition;
        $measure->delete();

        return redirect()
            ->route('admin.competitions.edit', $competition)
            ->with('success', 'Measure deleted successfully.');
    }
}
