# Backend Architecture

## Overview

The backend follows Laravel's MVC pattern with an additional Service Layer for complex business logic. This keeps controllers thin and promotes code reuse.

## Architecture Layers

```
Routes → Middleware → Controllers → Services → Models → Database
   ↓                                    ↓
Views ←─────────────────────────────────┘
```

## Request Flow

1. **Route**: Request matches a route in `routes/web.php`
2. **Middleware**: Authentication, CSRF protection
3. **Controller**: Validates input, calls service
4. **Service**: Executes business logic, interacts with models
5. **Model**: Eloquent ORM operations on database
6. **Response**: Controller returns view or JSON

---

## Controllers

### Purpose
- Handle HTTP requests
- Validate input
- Delegate to services
- Return responses (views or JSON)

### Structure
Located in `app/Http/Controllers/`

#### CompetitionController
**Responsibility**: Manage competition CRUD operations

**Routes**:
- `GET /admin/competitions` → `index()` - List all competitions
- `GET /admin/competitions/create` → `create()` - Show create form
- `POST /admin/competitions` → `store()` - Save new competition
- `GET /admin/competitions/{id}` → `show()` - View competition details
- `GET /admin/competitions/{id}/edit` → `edit()` - Show edit form (unified: includes stages, measures, competitor assignment)
- `PUT /admin/competitions/{id}` → `update()` - Update competition
- `DELETE /admin/competitions/{id}` → `destroy()` - Delete competition (cascade delete related data)

**Authorization**: Requires authentication (`auth` middleware)

**Key Methods**:
```php
public function edit(int $id): View
{
    $competition = Competition::with(['stages.measures', 'competitors'])->findOrFail($id);
    return view('admin.competitions.edit', compact('competition'));
}

public function update(Request $request, int $id): RedirectResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'country_code' => 'required|string|size:2',
        'date' => 'required|date',
        'status' => 'required|in:draft,active,completed',
    ]);

    $competition = Competition::findOrFail($id);
    $competition->update($validated);

    return redirect()->route('competitions.index')
        ->with('success', 'Competition updated successfully');
}
```

---

#### StageController
**Responsibility**: Manage stages inline within competition edit view

**Routes**:
- `POST /admin/competitions/{competitionId}/stages` → `store()` - Add stage to competition
- `PUT /admin/stages/{id}` → `update()` - Update stage (including reorder)
- `DELETE /admin/stages/{id}` → `destroy()` - Delete stage

**Authorization**: Requires authentication

**Key Behavior**:
- Stages are created/edited within competition edit screen (not separate CRUD)
- Supports reordering via `order` field

---

#### CompetitorController
**Responsibility**: Manage competitors and assignment to competitions

**Routes**:
- `GET /admin/competitors` → `index()` - List competitors (with search/filter)
- `GET /admin/competitors/create` → `create()` - Show create form
- `POST /admin/competitors` → `store()` - Save new competitor
- `GET /admin/competitors/{id}/edit` → `edit()` - Edit competitor
- `PUT /admin/competitors/{id}` → `update()` - Update competitor
- `DELETE /admin/competitors/{id}` → `destroy()` - Delete competitor
- `POST /admin/competitions/{id}/assign` → `assignToCompetition()` - Assign competitor to competition

**Search & Filter**:
```php
public function index(Request $request): View
{
    $query = Competitor::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('country')) {
        $query->where('country', $request->country);
    }

    $competitors = $query->paginate(20);

    return view('admin.competitors.index', compact('competitors'));
}
```

---

#### AttemptController
**Responsibility**: Manage competition execution (timer, results input)

**Routes**:
- `GET /run/competition/{id}` → `dashboard()` - Competition run dashboard
- `GET /run/competition/{id}/competitor/{competitorId}/stage/{stageId}` → `timerScreen()` - Timer + input screen
- `POST /run/attempt/start` → `start()` - Start attempt timer
- `POST /run/attempt/stop` → `stop()` - Stop timer, save duration
- `POST /run/attempt/results` → `saveResults()` - Save pour results
- `GET /run/attempt/{id}/results` → `showResults()` - Display attempt results

**Authorization**: Requires authentication

**Business Rules Enforced**:
- ONE attempt per competitor per stage (validate before creating)
- Competitor cannot have multiple active attempts (series, not parallel)
- All measures for a stage must have results entered

**Key Methods**:
```php
public function start(Request $request): JsonResponse
{
    $validated = $request->validate([
        'competition_id' => 'required|exists:competitions,id',
        'competitor_id' => 'required|exists:competitors,id',
        'stage_id' => 'required|exists:stages,id',
    ]);

    // Validate: no existing attempt for this competitor+stage
    $existing = Attempt::where('competitor_id', $validated['competitor_id'])
        ->where('stage_id', $validated['stage_id'])
        ->first();

    if ($existing) {
        return response()->json(['error' => 'Stage already attempted'], 422);
    }

    $attempt = Attempt::create([
        ...$validated,
        'started_at' => now(),
    ]);

    return response()->json(['attempt_id' => $attempt->id]);
}

public function stop(Request $request): JsonResponse
{
    $attempt = Attempt::findOrFail($request->attempt_id);
    $attempt->ended_at = now();
    $attempt->duration_ms = $attempt->started_at->diffInMilliseconds($attempt->ended_at);
    $attempt->save();

    return response()->json(['success' => true]);
}
```

---

#### DisplayController
**Responsibility**: Serve public display screens (no authentication)

**Routes**:
- `GET /display/competition/{id}/live` → `live()` - Live stage display (auto-refresh)
- `GET /display/competition/{id}/leaderboard` → `leaderboard()` - Real-time leaderboard

**Authorization**: NO authentication required (public access)

**Live Updates**: Client-side polling (1-2 second interval)

**Key Methods**:
```php
public function live(int $id): View
{
    $competition = Competition::with(['stages', 'competitors'])->findOrFail($id);

    // Get current active attempt
    $currentAttempt = Attempt::where('competition_id', $id)
        ->whereNotNull('started_at')
        ->whereNull('ended_at')
        ->with(['competitor', 'stage.measures', 'results'])
        ->first();

    return view('display.live', compact('competition', 'currentAttempt'));
}

public function leaderboard(int $id): View
{
    $competition = Competition::findOrFail($id);

    // Use LeaderboardService to compile scores
    $leaderboard = app(LeaderboardService::class)->getLeaderboard($id);

    return view('display.leaderboard', compact('competition', 'leaderboard'));
}
```

---

#### LeaderboardController
**Responsibility**: Compile and expose leaderboards

**Routes**:
- `GET /api/competition/{id}/leaderboard` → `getLeaderboard()` - JSON leaderboard data (for polling)
- `GET /competitor/{id}` → `competitorProfile()` - Public competitor profile with past events

**Authorization**: Public access

---

## Service Layer

### Purpose
- Encapsulate complex business logic
- Promote code reuse across controllers
- Simplify unit testing
- Keep controllers thin

### Services

#### ScoringService
**Location**: `app/Services/ScoringService.php`

**Responsibility**: Calculate competition scores

**Methods**:
```php
declare(strict_types=1);

namespace App\Services;

use App\Models\Attempt;
use App\Models\Competition;

class ScoringService
{
    /**
     * Calculate final score for a competitor in a competition.
     *
     * Formula (from architecture.md):
     * - TET = sum of all stage expected times
     * - OTS = sum of all attempt durations (in seconds)
     * - OAP = average accuracy percentage across all pours
     * - TP = 1.5 × (TET - OTS)
     * - AP = 1000 × (OAP - 0.95)
     * - Final Score = TP + AP
     *
     * @param int $competitionId
     * @param int $competitorId
     * @return float
     */
    public function calculateFinalScore(int $competitionId, int $competitorId): float
    {
        $competition = Competition::with('stages')->findOrFail($competitionId);

        // TET: Total Expected Time
        $tet = $competition->stages->sum('expected_seconds');

        // Get all attempts for this competitor
        $attempts = Attempt::where('competition_id', $competitionId)
            ->where('competitor_id', $competitorId)
            ->with('results.measure')
            ->get();

        // OTS: Overall Time Spent (in seconds)
        $ots = $attempts->sum(fn($attempt) => $attempt->duration_ms / 1000);

        // OAP: Overall Accuracy Percentage
        $oap = $this->calculateOAP($attempts);

        // TP: Time Points
        $tp = 1.5 * ($tet - $ots);

        // AP: Accuracy Points
        $ap = 1000 * ($oap - 0.95);

        // Final Score
        return $tp + $ap;
    }

    /**
     * Calculate Overall Accuracy Percentage.
     *
     * OAP = average of: 100 - (abs(poured_ml - target_ml) / target_ml × 100)
     *
     * @param \Illuminate\Database\Eloquent\Collection $attempts
     * @return float Percentage (0.0 to 1.0)
     */
    private function calculateOAP($attempts): float
    {
        $accuracies = [];

        foreach ($attempts as $attempt) {
            foreach ($attempt->results as $result) {
                $target = $result->measure->target_ml;
                $poured = $result->poured_ml;
                $accuracy = 1.0 - (abs($poured - $target) / $target);
                $accuracies[] = $accuracy;
            }
        }

        return empty($accuracies) ? 0.0 : array_sum($accuracies) / count($accuracies);
    }
}
```

---

#### TimingService
**Location**: `app/Services/TimingService.php`

**Responsibility**: Handle duration calculations

**Methods**:
```php
public function calculateDurationMs(\DateTimeInterface $start, \DateTimeInterface $end): int
{
    return $start->diffInMilliseconds($end);
}
```

---

#### LeaderboardService
**Location**: `app/Services/LeaderboardService.php`

**Responsibility**: Aggregate scores and rankings

**Methods**:
```php
public function getLeaderboard(int $competitionId): array
{
    $competition = Competition::with('competitors')->findOrFail($competitionId);
    $scoringService = app(ScoringService::class);

    $leaderboard = [];

    foreach ($competition->competitors as $competitor) {
        $score = $scoringService->calculateFinalScore($competitionId, $competitor->id);

        $leaderboard[] = [
            'competitor' => $competitor,
            'score' => $score,
        ];
    }

    // Sort by score descending
    usort($leaderboard, fn($a, $b) => $b['score'] <=> $a['score']);

    // Add rank
    foreach ($leaderboard as $index => &$entry) {
        $entry['rank'] = $index + 1;
    }

    return $leaderboard;
}
```

---

#### DisplayService
**Location**: `app/Services/DisplayService.php`

**Responsibility**: Format data for live display screens

**Methods**:
```php
public function formatLiveData(int $competitionId): array
{
    // Format current attempt, timer, measures for display screen
    // Returns structured array optimized for Blade template
}
```

---

## Middleware

### Authentication
- **Middleware**: `auth`
- **Applied to**: All `/admin/*` and `/run/*` routes
- **Login**: Basic username/password (Laravel built-in auth)
- **Users**: Referees only (no public registration)

### CSRF Protection
- **Middleware**: `VerifyCsrfToken` (default Laravel)
- **Applied to**: All POST/PUT/DELETE requests
- **Excluded**: None (CSRF required for all form submissions)

---

## Error Handling

### Exception Handler
**Location**: `app/Exceptions/Handler.php`

**Fatal Errors**:
- Display generic "Something went wrong" page
- Log detailed error to `storage/logs/laravel.log`
- Never expose sensitive information to users

**Validation Errors**:
- Return user-friendly messages
- Highlight form fields with errors
- Preserve user input

**404 Errors**:
- Custom view: `resources/views/errors/404.blade.php`

**500 Errors**:
- Custom view: `resources/views/errors/error.blade.php` (generic message)

---

## Validation

### Form Requests
For complex validation, use Form Request classes:

**Location**: `app/Http/Requests/`

**Example**: `StoreCompetitionRequest.php`
```php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Only authenticated users
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country_code' => 'required|string|size:2',
            'date' => 'required|date',
            'status' => 'required|in:draft,active,completed',
        ];
    }

    public function messages(): array
    {
        return [
            'country_code.size' => 'Country code must be exactly 2 characters (ISO 3166-1).',
        ];
    }
}
```

**Usage in Controller**:
```php
public function store(StoreCompetitionRequest $request): RedirectResponse
{
    $validated = $request->validated();
    Competition::create($validated);
    // ...
}
```

---

## Database Interaction

### Eloquent ORM
- Use Eloquent for all database operations
- **Never** use raw SQL queries (security risk)
- Use Query Builder if Eloquent is insufficient

### Relationships
Define all relationships in models (see database-schema.md):
- `hasMany`, `belongsTo`, `belongsToMany`
- Use eager loading to prevent N+1 queries: `Competition::with(['stages', 'competitors'])->get()`

### Transactions
For operations affecting multiple tables:
```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () use ($data) {
    $competition = Competition::create($data['competition']);
    $competition->stages()->createMany($data['stages']);
});
```

---

## Dependency Injection

Use Laravel's service container for dependency injection:

```php
class AttemptController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
        protected TimingService $timingService
    ) {}

    public function showResults(int $id): View
    {
        $attempt = Attempt::with('results')->findOrFail($id);
        $score = $this->scoringService->calculateAttemptScore($attempt);

        return view('run.results', compact('attempt', 'score'));
    }
}
```

---

## Best Practices

1. **Controllers**: Thin controllers, delegate logic to services
2. **Services**: Single responsibility, well-tested
3. **Models**: Only relationships and accessors/mutators
4. **Validation**: Use Form Requests for complex rules
5. **Error Handling**: Log errors, show friendly messages
6. **Security**: Validate all input, use CSRF protection, never expose sensitive data
7. **Database**: Use Eloquent, eager load relationships, use transactions
8. **Type Hints**: Strict types, hint all parameters and returns

---

## Notes

- Follow Laravel conventions for consistency
- Keep business logic in services, not controllers
- Use dependency injection for testability
- Log all errors for debugging
- Validate both client-side and server-side (never trust client input)
