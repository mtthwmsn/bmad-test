# Unified Project Structure

## Overview

This document defines the directory structure and file organization for the Pour Test Competition App. The structure is based on Laravel conventions with custom organization for services and frontend assets.

## Root Directory Structure

```
/
├── app/                    # Application core
│   ├── Config/            # Configuration classes
│   ├── Controllers/       # HTTP controllers
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic services
│   └── View/              # View composers and components
├── src/                   # Frontend source files (pre-build)
│   ├── javascript/        # JavaScript source files
│   └── css/               # CSS source files
├── public/                # Public web root
│   ├── dist/              # Compiled assets
│   │   ├── javascript/    # Compiled JavaScript
│   │   └── css/           # Compiled CSS
│   ├── index.php          # Laravel entry point
│   └── ...                # Other public assets
├── resources/             # Laravel resources
│   ├── views/             # Blade templates
│   └── lang/              # Language files
├── database/              # Database files
│   ├── migrations/        # Database migrations
│   ├── seeders/           # Database seeders
│   └── factories/         # Model factories
├── tests/                 # Test files
│   ├── Unit/              # Unit tests
│   ├── Feature/           # Feature tests
│   └── Acceptance/        # Codeception acceptance tests
├── routes/                # Route definitions
│   ├── web.php            # Web routes
│   └── api.php            # API routes (if needed)
├── config/                # Configuration files
├── storage/               # Storage files
│   ├── app/               # Application storage
│   ├── framework/         # Framework cache/sessions
│   └── logs/              # Log files
├── bootstrap/             # Bootstrap files
├── docs/                  # Project documentation
│   ├── architecture/      # Architecture documentation
│   ├── prd/               # Product requirements (epics)
│   └── stories/           # User stories
└── vendor/                # Composer dependencies
```

## Application Directory (`app/`)

### Controllers (`app/Http/Controllers/`)

```
app/Http/Controllers/
├── Controller.php              # Base controller
├── CompetitionController.php   # Competition CRUD
├── StageController.php         # Stage management (inline with competition)
├── CompetitorController.php    # Competitor CRUD and assignment
├── AttemptController.php       # Attempt/timer/results management
├── DisplayController.php       # Public display screens
└── LeaderboardController.php   # Leaderboard compilation
```

**Naming Convention**: `{Resource}Controller.php` (singular resource name)

**Structure**:
- RESTful methods: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- Custom methods after RESTful methods
- Apply `auth` middleware in constructor for admin/run routes

---

### Models (`app/Models/`)

```
app/Models/
├── Competition.php
├── Stage.php
├── Measure.php
├── Competitor.php
├── CompetitionCompetitor.php  # Pivot model (if using)
├── Attempt.php
└── AttemptResult.php
```

**Naming Convention**: Singular, PascalCase

**Model Structure** (from coding-standards.md):
1. Table name (if non-standard)
2. Fillable/guarded properties
3. Casts
4. Relationships
5. Accessors/mutators
6. Custom methods

---

### Services (`app/Services/`)

```
app/Services/
├── ScoringService.php       # Score calculation logic
├── TimingService.php        # Duration/timing calculations
├── LeaderboardService.php   # Leaderboard aggregation
└── DisplayService.php       # Live display data formatting
```

**Purpose**: Extract complex business logic from controllers

**Structure**:
1. Properties
2. Constructor (dependency injection)
3. Public methods
4. Private helper methods

**Usage in Controllers**:
```php
use App\Services\ScoringService;

class AttemptController extends Controller
{
    protected $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }
}
```

---

### Config (`app/Config/`)

Custom configuration classes (if needed). Standard Laravel config files remain in `/config/`.

---

### View (`app/View/`)

View composers and Blade components (if using).

```
app/View/
└── Components/
    ├── CompetitionCard.php
    └── LeaderboardRow.php
```

---

## Frontend Source (`src/`)

Pre-compiled JavaScript and CSS source files.

```
src/
├── javascript/
│   ├── app.js                  # Main JavaScript entry
│   ├── timer.js                # Timer functionality
│   ├── polling.js              # Live update polling
│   └── validation.js           # Client-side validation
└── css/
    ├── app.css                 # Main stylesheet
    ├── admin.css               # Admin panel styles
    ├── display.css             # Public display styles
    └── responsive.css          # Responsive breakpoints
```

**Build Process**: Compiled to `/public/dist/` using Laravel Mix or Vite

---

## Public Directory (`public/`)

Web-accessible root directory.

```
public/
├── dist/                       # Compiled assets (generated)
│   ├── javascript/
│   │   └── app.js
│   └── css/
│       └── app.css
├── images/                     # Static images (if needed)
├── favicon.ico
└── index.php                   # Laravel entry point
```

**Important**: Do NOT place source files in `/public/`. Only compiled/built assets.

---

## Resources Directory (`resources/`)

### Views (`resources/views/`)

```
resources/views/
├── layouts/
│   ├── app.blade.php           # Main layout
│   ├── admin.blade.php         # Admin panel layout
│   └── display.blade.php       # Public display layout
├── auth/
│   └── login.blade.php         # Login page
├── admin/
│   ├── competitions/
│   │   ├── index.blade.php     # List competitions
│   │   ├── create.blade.php    # Create competition form
│   │   ├── edit.blade.php      # Edit competition (unified)
│   │   └── show.blade.php      # View competition details
│   └── competitors/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── run/
│   ├── dashboard.blade.php     # Run competition dashboard
│   ├── timer.blade.php         # Timer + input screen
│   └── results.blade.php       # Show attempt results
├── display/
│   ├── live.blade.php          # Live competition display
│   └── leaderboard.blade.php   # Leaderboard screen
├── competitor/
│   └── profile.blade.php       # Public competitor profile
└── errors/
    ├── 404.blade.php
    ├── 500.blade.php
    └── error.blade.php         # Generic "Something went wrong"
```

**Naming Convention**: lowercase, kebab-case: `competition-list.blade.php`

**Organization**: Group by controller/feature area

---

## Database Directory (`database/`)

```
database/
├── migrations/
│   ├── 2024_01_01_000001_create_competitions_table.php
│   ├── 2024_01_01_000002_create_stages_table.php
│   ├── 2024_01_01_000003_create_measures_table.php
│   ├── 2024_01_01_000004_create_competitors_table.php
│   ├── 2024_01_01_000005_create_competition_competitor_table.php
│   ├── 2024_01_01_000006_create_attempts_table.php
│   └── 2024_01_01_000007_create_attempt_results_table.php
├── seeders/
│   ├── DatabaseSeeder.php      # Main seeder
│   ├── CompetitionSeeder.php   # Competition sample data
│   └── CompetitorSeeder.php    # Competitor sample data
└── factories/
    ├── CompetitionFactory.php
    ├── StageFactory.php
    ├── MeasureFactory.php
    ├── CompetitorFactory.php
    ├── AttemptFactory.php
    └── AttemptResultFactory.php
```

**Migration Naming**: Follow Laravel convention with timestamp prefix

**Order**: Migrations must run in dependency order (see database-schema.md)

---

## Tests Directory (`tests/`)

```
tests/
├── Unit/
│   ├── Models/
│   │   ├── CompetitionTest.php
│   │   ├── StageTest.php
│   │   └── CompetitorTest.php
│   └── Services/
│       ├── ScoringServiceTest.php
│       ├── TimingServiceTest.php
│       └── LeaderboardServiceTest.php
├── Feature/
│   ├── CompetitionManagementTest.php
│   ├── CompetitorManagementTest.php
│   ├── AuthenticationTest.php
│   └── AttemptManagementTest.php
└── Acceptance/
    ├── CreateCompetitionCest.php
    ├── RunCompetitionCest.php
    └── ViewDisplayCest.php
```

**Naming Convention**: `{ClassName}Test.php` for PHPUnit, `{Workflow}Cest.php` for Codeception

---

## Routes (`routes/`)

```
routes/
├── web.php                     # Main web routes
└── api.php                     # API routes (if needed for polling)
```

**Route Organization in `web.php`**:
```php
// Public routes
Route::get('/display/competition/{id}/live', [DisplayController::class, 'live']);
Route::get('/display/competition/{id}/leaderboard', [DisplayController::class, 'leaderboard']);

// Authentication routes
Auth::routes(['register' => false]); // Only login, no registration

// Authenticated referee routes
Route::middleware(['auth'])->group(function () {
    // Admin panel
    Route::prefix('admin')->group(function () {
        Route::resource('competitions', CompetitionController::class);
        Route::resource('competitors', CompetitorController::class);
    });

    // Run interface
    Route::prefix('run')->group(function () {
        Route::get('competition/{id}', [AttemptController::class, 'dashboard']);
        Route::post('attempt/start', [AttemptController::class, 'start']);
        Route::post('attempt/stop', [AttemptController::class, 'stop']);
    });
});
```

---

## Documentation (`docs/`)

```
docs/
├── architecture/
│   ├── index.md                        # Architecture navigation
│   ├── tech-stack.md                   # Technology stack
│   ├── coding-standards.md             # Coding conventions
│   ├── database-schema.md              # Database design
│   ├── testing-strategy.md             # Testing approach
│   ├── unified-project-structure.md    # This file
│   ├── backend-architecture.md         # Backend patterns
│   ├── rest-api-spec.md                # API specifications
│   └── frontend-architecture.md        # Frontend patterns
├── prd/
│   ├── epic-1-foundation-database.md
│   └── epic-2-*.md
├── stories/
│   ├── 1.1.story.md
│   └── *.story.md
├── prd.md                              # Original PRD
└── architecture.md                     # Original architecture doc
```

---

## Configuration Files (Root)

```
/
├── .env                        # Environment configuration (DO NOT COMMIT)
├── .env.example                # Example environment file (COMMIT)
├── .gitignore                  # Git ignore rules
├── composer.json               # PHP dependencies
├── composer.lock               # Locked dependency versions
├── package.json                # Node dependencies (for frontend build)
├── package-lock.json           # Locked Node dependencies
├── phpunit.xml                 # PHPUnit configuration
├── codeception.yml             # Codeception configuration
├── artisan                     # Laravel CLI
├── webpack.mix.js              # Laravel Mix config (if using)
└── vite.config.js              # Vite config (if using)
```

---

## File Naming Conventions Summary

| Type | Convention | Example |
|------|------------|---------|
| Controllers | PascalCase + Controller | `CompetitionController.php` |
| Models | PascalCase (singular) | `Competition.php` |
| Services | PascalCase + Service | `ScoringService.php` |
| Migrations | snake_case with timestamp | `2024_01_01_create_competitions_table.php` |
| Views | kebab-case + .blade.php | `competition-list.blade.php` |
| Tests (PHPUnit) | PascalCase + Test | `CompetitionTest.php` |
| Tests (Codeception) | PascalCase + Cest | `CreateCompetitionCest.php` |
| JavaScript | kebab-case | `timer.js` |
| CSS | kebab-case | `admin.css` |

---

## Git Ignore

Ensure `.gitignore` includes:
```
/vendor/
/node_modules/
/public/dist/
/storage/*.key
/.env
.phpunit.result.cache
```

---

## Notes

- Follow Laravel conventions wherever possible
- Keep controllers thin, move business logic to services
- Organize views by controller/feature for easy navigation
- Separate source (`/src/`) from compiled (`/public/dist/`) assets
- Test files mirror application structure
- Documentation organized by BMAD principles (architecture shards, epics, stories)
