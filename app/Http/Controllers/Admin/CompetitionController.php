<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompetitionRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use App\Models\Competition;
use App\Models\Competitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    /**
     * Display a listing of competitions.
     */
    public function index(): View
    {
        $competitions = Competition::orderBy('date', 'desc')->paginate(10);

        return view('admin.competitions.index', compact('competitions'));
    }

    /**
     * Show the form for creating a new competition.
     */
    public function create(): View
    {
        return view('admin.competitions.create');
    }

    /**
     * Store a newly created competition in storage.
     */
    public function store(StoreCompetitionRequest $request): RedirectResponse
    {
        Competition::create($request->validated());

        return redirect()
            ->route('admin.competitions.index')
            ->with('success', 'Competition created successfully.');
    }

    /**
     * Show the form for editing the specified competition.
     */
    public function edit(Competition $competition): View
    {
        $competition->load(['stages' => function ($query) {
            $query->orderBy('order');
        }, 'stages.measures' => function ($query) {
            $query->orderBy('order');
        }]);

        // Get all competitors and assigned competitor IDs
        $allCompetitors = Competitor::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $assignedCompetitorIds = $competition->competitors()->pluck('competitors.id')->toArray();

        return view('admin.competitions.edit', compact('competition', 'allCompetitors', 'assignedCompetitorIds'));
    }

    /**
     * Update the specified competition in storage.
     */
    public function update(UpdateCompetitionRequest $request, Competition $competition): RedirectResponse
    {
        $competition->update($request->validated());

        return redirect()
            ->route('admin.competitions.index')
            ->with('success', 'Competition updated successfully.');
    }

    /**
     * Remove the specified competition from storage.
     */
    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();

        return redirect()
            ->route('admin.competitions.index')
            ->with('success', 'Competition deleted successfully.');
    }

    /**
     * Assign competitors to the specified competition.
     */
    public function assignCompetitors(Request $request, Competition $competition): RedirectResponse
    {
        $request->validate([
            'competitor_ids' => 'sometimes|array',
            'competitor_ids.*' => 'exists:competitors,id'
        ]);

        $competition->competitors()->sync($request->input('competitor_ids', []));

        return redirect()
            ->route('admin.competitions.edit', $competition)
            ->with('success', 'Competitors assigned successfully.');
    }
}
