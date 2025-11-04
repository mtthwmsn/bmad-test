# Testing Strategy

## Overview

The Pour Test Competition App uses a layered testing approach:
- **Unit Tests** for models and services (PHPUnit)
- **Browser/Acceptance Tests** for end-to-end workflows (Codeception)

## Testing Tools

### PHPUnit
- **Purpose**: Unit and feature testing
- **Version**: Included with Laravel (latest)
- **Location**: `/tests/Unit/` and `/tests/Feature/`
- **Command**: `php artisan test` or `./vendor/bin/phpunit`

### Codeception
- **Purpose**: Browser-based acceptance testing
- **Version**: Latest stable
- **Location**: `/tests/Acceptance/`
- **Command**: `./vendor/bin/codecept run`
- **Configuration**: `codeception.yml`

## Test Types

### 1. Unit Tests (PHPUnit)

**What to Test**:
- Model methods and relationships
- Service class methods (ScoringService, TimingService, etc.)
- Helper functions
- Business logic calculations

**Coverage Requirements**:
- **Minimum**: 80% code coverage for models and services
- **Target**: 90%+ for critical business logic (scoring calculations)

**Location**: `/tests/Unit/`

**Naming Convention**: `{ClassName}Test.php`
- Example: `CompetitionTest.php`, `ScoringServiceTest.php`

**Example Structure**:
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Competition;

class CompetitionTest extends TestCase
{
    /** @test */
    public function it_can_create_a_competition()
    {
        $competition = Competition::factory()->create([
            'name' => 'Test Competition',
            'status' => 'draft',
        ]);

        $this->assertInstanceOf(Competition::class, $competition);
        $this->assertEquals('draft', $competition->status);
    }

    /** @test */
    public function it_has_many_stages()
    {
        $competition = Competition::factory()->create();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $competition->stages
        );
    }
}
```

**Test Database**: Use SQLite in-memory for fast unit tests
```php
// phpunit.xml configuration
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

---

### 2. Feature Tests (PHPUnit)

**What to Test**:
- HTTP requests and responses
- Form validation
- Authentication/authorization
- Route accessibility
- Controller behavior

**Location**: `/tests/Feature/`

**Naming Convention**: `{Feature}Test.php`
- Example: `CompetitionManagementTest.php`, `AuthenticationTest.php`

**Example Structure**:
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Competition;

class CompetitionManagementTest extends TestCase
{
    /** @test */
    public function authenticated_users_can_view_competitions()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/competitions');

        $response->assertStatus(200);
    }

    /** @test */
    public function guests_cannot_access_admin_routes()
    {
        $response = $this->get('/admin/competitions');

        $response->assertRedirect('/login');
    }
}
```

---

### 3. Browser/Acceptance Tests (Codeception)

**What to Test**:
- Complete user workflows
- UI interactions (forms, buttons, navigation)
- Multi-step processes
- JavaScript functionality
- Responsive design elements

**Location**: `/tests/Acceptance/`

**Naming Convention**: `{Workflow}Cest.php`
- Example: `CreateCompetitionCest.php`, `RunCompetitionCest.php`

**Example Structure**:
```php
<?php

class CreateCompetitionCest
{
    public function _before(AcceptanceTester $I)
    {
        // Login as referee
        $I->amOnPage('/login');
        $I->fillField('username', 'referee');
        $I->fillField('password', 'password');
        $I->click('Login');
    }

    public function createNewCompetition(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/competitions');
        $I->click('Create Competition');

        $I->fillField('name', 'Test Pour Competition');
        $I->fillField('city', 'London');
        $I->fillField('country_code', 'GB');
        $I->fillField('date', '2024-12-31');

        $I->click('Save');

        $I->see('Competition created successfully');
        $I->see('Test Pour Competition');
    }
}
```

**Browser Driver**: Use ChromeDriver or similar for Codeception
```yaml
# codeception.yml
actor: AcceptanceTester
extensions:
    enabled:
        - Codeception\Extension\RunFailed
modules:
    config:
        WebDriver:
            url: 'http://localhost:8000'
            browser: chrome
```

---

## Test Coverage by Component

### Models (Unit Tests - 80% coverage minimum)
- [ ] Competition model
  - Factory creation
  - Relationships (stages, competitors, attempts)
  - Status transitions
  - Cascade deletes
- [ ] Stage model
  - Relationships
  - Ordering functionality
- [ ] Measure model
  - Validation (decimal 6,2)
- [ ] Competitor model
  - Required vs nullable fields
- [ ] Attempt model
  - One attempt per stage enforcement
  - Duration calculation
- [ ] AttemptResult model
  - Result storage and retrieval

### Services (Unit Tests - 90% coverage minimum)
- [ ] ScoringService
  - TET calculation
  - OTS calculation
  - OAP calculation
  - TP calculation
  - AP calculation
  - Final score calculation
  - Edge cases (zero values, perfect scores)
- [ ] TimingService
  - Duration calculation from timestamps
  - Millisecond accuracy
- [ ] LeaderboardService
  - Score aggregation
  - Competitor ranking
  - Tie handling

### Controllers (Feature Tests)
- [ ] CompetitionController
  - CRUD operations
  - Authorization checks
  - Validation errors
- [ ] CompetitorController
  - Search and filter
  - Assignment to competitions
- [ ] AttemptController
  - Timer start/stop
  - Result input validation
- [ ] DisplayController
  - Public access (no auth)
  - Live data formatting

### Workflows (Codeception - Critical paths)
- [ ] **Create Competition**
  - Login as referee
  - Create competition with stages and measures
  - Assign competitors
- [ ] **Run Competition**
  - Start stage for competitor
  - Enter pour results
  - Complete stage
  - View results
- [ ] **View Live Display**
  - Access as guest (no auth)
  - See current competitor
  - See live timer
  - See leaderboard updates
- [ ] **Responsive Design**
  - Test on mobile viewport (375px)
  - Test on tablet viewport (768px)
  - Test on desktop viewport (1920px)

---

## Running Tests

### Run All Tests
```bash
php artisan test
./vendor/bin/codecept run
```

### Run Specific Test Types
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# Codeception acceptance tests
./vendor/bin/codecept run acceptance
```

### Run with Coverage
```bash
php artisan test --coverage
php artisan test --coverage-html coverage-report
```

### Run Specific Test File
```bash
php artisan test tests/Unit/ScoringServiceTest.php
./vendor/bin/codecept run tests/Acceptance/CreateCompetitionCest.php
```

---

## Test Data Management

### Factories
- Use Laravel factories for all models
- Define realistic default values
- Create state variations (draft competition, active competition, etc.)

### Seeders
- Development seeder: realistic sample data
- Test seeder: minimal data for specific test scenarios
- Never use seeders in tests (use factories instead for isolation)

### Database Reset
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    // Database automatically reset before each test
}
```

---

## Continuous Integration

### Pre-Commit Checks
- Run `./vendor/bin/pint` (formatting)
- Run `php artisan test` (all tests pass)

### CI Pipeline (Future)
1. Install dependencies
2. Run PHPUnit tests with coverage
3. Run Codeception tests
4. Generate coverage report
5. Fail if coverage < 80%

---

## Definition of Done - Testing Requirements

A story is not complete until:

- [ ] All new models have unit tests (80%+ coverage)
- [ ] All new services have unit tests (90%+ coverage)
- [ ] All new controllers have feature tests
- [ ] Critical workflows have Codeception tests
- [ ] All tests pass locally
- [ ] Code formatted with Laravel Pint
- [ ] Manual testing completed (if specified in story)

---

## Manual Testing

When automated tests are insufficient or impractical, include manual test steps in the story:

**Example**:
```markdown
## Manual Test Steps

1. Log in as referee (username: referee, password: password)
2. Navigate to /admin/competitions
3. Click "Create Competition"
4. Fill form and submit
5. Verify competition appears in list
6. Verify status is "draft"
```

---

## Error Handling Tests

### Fatal Errors
- Test that fatal errors display "Something went wrong" page
- Test that errors are logged to `storage/logs/laravel.log`
- Test that sensitive information is NOT exposed to users

### Validation Errors
- Test all form validation rules
- Test decimal(6,2) constraints for measures
- Test required vs nullable fields
- Test error messages are user-friendly

---

## Performance Testing (Future)

Not required for MVP, but consider later:
- Load testing for live display with multiple concurrent viewers
- Database query optimization
- Response time benchmarks

---

## Notes

- Use `RefreshDatabase` trait for isolated test runs
- Mock external services (if any)
- Test both happy paths and error scenarios
- Write tests BEFORE marking story as complete
- Update this document as testing needs evolve
