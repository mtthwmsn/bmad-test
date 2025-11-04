# Coding Standards

## PHP Standards

### PSR Standards
- **PSR-12**: Extended Coding Style Guide (strictly enforced)
- **PSR-4**: Autoloading Standard (enforced by Laravel/Composer)

### Laravel Conventions
Follow all Laravel framework conventions and best practices:

#### Naming Conventions

**Models**
- Singular, PascalCase: `Competition`, `Competitor`, `Attempt`
- Extend `Illuminate\Database\Eloquent\Model`

**Controllers**
- Singular resource name + "Controller": `CompetitionController`, `CompetitorController`
- Use resource controller methods: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- Extend `App\Http\Controllers\Controller`

**Services**
- Descriptive name + "Service": `ScoringService`, `TimingService`, `LeaderboardService`
- Place in `app/Services/` directory

**Migrations**
- Laravel convention: `create_table_name_table` or `add_column_to_table_name_table`
- Use descriptive names with timestamps: `2024_01_01_000000_create_competitions_table.php`

**Routes**
- Use RESTful conventions
- Group related routes
- Use route names: `competitions.index`, `competitions.store`, etc.

**Blade Views**
- Lowercase, kebab-case: `competition-list.blade.php`, `edit-stage.blade.php`
- Organize in directories by controller/resource

#### Database Conventions

**Table Names**
- Plural, snake_case: `competitions`, `attempt_results`, `competition_competitor`
- Pivot tables: alphabetically ordered, singular: `competition_competitor`

**Column Names**
- snake_case: `competitor_id`, `expected_seconds`, `duration_ms`
- Foreign keys: `{table_singular}_id` (e.g., `competition_id`)
- Boolean columns: prefix with `is_` or `has_`: `is_active`, `has_completed`

**Timestamps**
- Always include `created_at` and `updated_at` on all tables
- Use `$table->timestamps()` in migrations

#### Code Organization

**Eloquent Models**
```php
class Competition extends Model
{
    // 1. Table name (if non-standard)
    // 2. Fillable/guarded
    protected $fillable = ['name', 'city', 'country_code', 'date', 'status'];

    // 3. Casts
    protected $casts = [
        'date' => 'date',
        'status' => 'string',
    ];

    // 4. Relationships
    public function stages()
    {
        return $this->hasMany(Stage::class);
    }

    // 5. Accessors/Mutators
    // 6. Methods
}
```

**Controllers**
```php
class CompetitionController extends Controller
{
    // 1. Constructor (middleware, dependencies)
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 2. Resource methods (index, create, store, show, edit, update, destroy)
    // 3. Custom methods
}
```

**Services**
```php
class ScoringService
{
    // 1. Properties
    // 2. Constructor
    // 3. Public methods
    // 4. Private helper methods
}
```

## Code Quality Standards

### Formatting
- Use **Laravel Pint** for automatic code formatting
- Run `./vendor/bin/pint` before commits
- Configuration: Default Laravel Pint preset (PSR-12)

### Documentation
- Use PHPDoc blocks for classes and public methods
- Include `@param`, `@return`, `@throws` tags
- Document complex logic with inline comments

```php
/**
 * Calculate the final score for a competitor's attempt.
 *
 * @param Attempt $attempt The attempt to score
 * @return float The calculated final score
 * @throws \InvalidArgumentException If attempt is incomplete
 */
public function calculateScore(Attempt $attempt): float
{
    // Implementation
}
```

### Type Declarations
- Use strict types: `declare(strict_types=1);` at top of all PHP files
- Type hint all parameters and return types
- Use nullable types where appropriate: `?string`, `?int`

```php
declare(strict_types=1);

public function updateStage(int $id, array $data): Stage
{
    // Implementation
}
```

### Error Handling
- Use type-specific exceptions: `InvalidArgumentException`, `ModelNotFoundException`
- Fatal errors should be caught by Laravel's exception handler
- Display user-friendly error page: "Something went wrong"
- Log all errors to `storage/logs/laravel.log`

### Validation
- Use Form Request classes for complex validation
- Follow schema constraints (e.g., `decimal(6,2)` for measures)
- Validate on both client-side and server-side
- Required fields: All fields EXCEPT those explicitly marked nullable

**Nullable Fields (Complete List)**:
- `competitor.bar_name`
- `competitor.instagram`
- `competitor.diffords_profile`
- `attempt.started_at`
- `attempt.ended_at`
- `attempt.duration_ms`
- `stage.name`

### Security
- Never use raw SQL queries (use Eloquent or Query Builder)
- Use parameterized queries
- Validate and sanitize all user input
- Use CSRF protection (enabled by default in Laravel)
- Use Laravel's built-in authentication
- Hash passwords with bcrypt (Laravel default)

## JavaScript/CSS Standards

### JavaScript
- ES6+ syntax
- Use `const` and `let`, avoid `var`
- Use arrow functions where appropriate
- Comment complex logic
- Organize in `/src/javascript/` during development

### CSS
- Use BEM naming convention for classes: `.block__element--modifier`
- Mobile-first responsive design
- Use CSS variables for theming
- Organize in `/src/css/` during development

### Build Process
- Compile assets with Laravel Mix or Vite
- Output to `/public/dist/javascript/` and `/public/dist/css/`
- Minify for production

## Version Control

### Git Conventions
- Use conventional commits: `feat:`, `fix:`, `refactor:`, `docs:`, etc.
- Write descriptive commit messages
- One logical change per commit

### Branch Strategy
- `main` - production-ready code
- `develop` - integration branch
- Feature branches: `feature/story-1.1-laravel-setup`

## General Best Practices

1. **DRY (Don't Repeat Yourself)** - Extract repeated logic to services or helper functions
2. **SOLID Principles** - Especially Single Responsibility
3. **Laravel Conventions** - Always follow framework conventions over custom approaches
4. **Readability** - Code should be self-documenting; use clear variable names
5. **Testing** - Write tests before marking stories as complete
6. **Dependencies** - Keep minimal; evaluate before adding new packages

## Code Review Checklist

- [ ] Follows PSR-12 standards
- [ ] Passes Laravel Pint formatting
- [ ] Includes proper type hints
- [ ] Has appropriate PHPDoc blocks
- [ ] Follows Laravel naming conventions
- [ ] Validates user input
- [ ] Handles errors appropriately
- [ ] Includes tests
- [ ] No sensitive data in code
- [ ] Follows project structure
