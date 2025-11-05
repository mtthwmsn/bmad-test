<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competitor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CompetitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Competitor::query();

        // Search by first name or last name
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($country = $request->input('country')) {
            $query->where('country_code', $country);
        }

        $competitors = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        // Get unique countries for filter dropdown
        $countries = Competitor::select('country_code')
            ->distinct()
            ->orderBy('country_code')
            ->pluck('country_code');

        return view('admin.competitors.index', compact('competitors', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.competitors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation will be done in Form Request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'bar_name' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'diffords_profile' => 'nullable|url|max:500',
            'country_code' => 'required|string|size:2',
        ]);

        // Check for duplicate
        $exists = Competitor::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['first_name' => 'A competitor with this name already exists.']);
        }

        Competitor::create($validated);

        return redirect()->route('admin.competitors.index')
            ->with('success', 'Competitor created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competitor $competitor): View
    {
        return view('admin.competitors.edit', compact('competitor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Competitor $competitor): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'bar_name' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'diffords_profile' => 'nullable|url|max:500',
            'country_code' => 'required|string|size:2',
        ]);

        // Check for duplicate (excluding current competitor)
        $exists = Competitor::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('id', '!=', $competitor->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['first_name' => 'A competitor with this name already exists.']);
        }

        $competitor->update($validated);

        return redirect()->route('admin.competitors.index')
            ->with('success', 'Competitor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competitor $competitor): RedirectResponse
    {
        // Check if competitor has attempts
        if ($competitor->attempts()->exists()) {
            return redirect()->route('admin.competitors.index')
                ->with('error', 'Cannot delete competitor with existing attempts.');
        }

        $competitor->delete();

        return redirect()->route('admin.competitors.index')
            ->with('success', 'Competitor deleted successfully.');
    }
}
