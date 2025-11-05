<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptResult;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Stage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RunController extends Controller
{
    /**
     * Display the run dashboard for a competition.
     */
    public function dashboard(Competition $competition): View
    {
        $competition->load(['stages' => function ($query) {
            $query->orderBy('order');
        }, 'stages.measures' => function ($query) {
            $query->orderBy('order');
        }, 'competitors', 'attempts.competitor', 'attempts.stage']);

        return view('run.dashboard', compact('competition'));
    }

    /**
     * Show the attempt screen for a specific competitor and stage.
     */
    public function createAttempt(Competition $competition, Competitor $competitor, Stage $stage): View
    {
        // Verify the competitor is assigned to this competition
        if (!$competition->competitors->contains($competitor)) {
            abort(404, 'Competitor not assigned to this competition.');
        }

        // Verify the stage belongs to this competition
        if ($stage->competition_id !== $competition->id) {
            abort(404, 'Stage does not belong to this competition.');
        }

        $stage->load(['measures' => function ($query) {
            $query->orderBy('order');
        }]);

        return view('run.attempt', compact('competition', 'competitor', 'stage'));
    }

    /**
     * Store a new attempt with results.
     */
    public function storeAttempt(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'competition_id' => 'required|exists:competitions,id',
            'competitor_id' => 'required|exists:competitors,id',
            'stage_id' => 'required|exists:stages,id',
            'duration_ms' => 'required|integer|min:0',
            'started_at' => 'required|date',
            'ended_at' => 'required|date|after:started_at',
            'results' => 'required|array',
            'results.*.measure_id' => 'required|exists:measures,id',
            'results.*.poured_ml' => 'required|numeric|min:0',
        ]);

        // Create the attempt
        $attempt = Attempt::create([
            'competition_id' => $validated['competition_id'],
            'competitor_id' => $validated['competitor_id'],
            'stage_id' => $validated['stage_id'],
            'status' => 'completed',
            'started_at' => $validated['started_at'],
            'ended_at' => $validated['ended_at'],
            'duration_ms' => $validated['duration_ms'],
        ]);

        // Create attempt results for each measure
        foreach ($validated['results'] as $result) {
            AttemptResult::create([
                'attempt_id' => $attempt->id,
                'measure_id' => $result['measure_id'],
                'poured_ml' => $result['poured_ml'],
            ]);
        }

        return redirect()
            ->route('run.dashboard', $validated['competition_id'])
            ->with('success', 'Attempt recorded successfully!');
    }
}
