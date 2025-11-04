# Database Schema

## Overview

MariaDB database using Laravel migrations and Eloquent ORM. All tables use `id` as primary key (auto-incrementing unsigned big integer) and include `created_at` and `updated_at` timestamps.

## Schema Diagram

```
competitions (1) ──< stages (M)
    │                   │
    │                   └──< measures (M)
    │
    └──< competition_competitor (M) ──> competitors (1)
                                            │
attempts (M) ───────────────────────────────┘
    │
    ├── competition_id (FK)
    ├── competitor_id (FK)
    ├── stage_id (FK)
    │
    └──< attempt_results (M) ──> measures (1)
```

## Tables

### competitions

Stores competition events.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Competition name |
| city | VARCHAR(255) | NOT NULL | City location |
| country_code | CHAR(2) | NOT NULL | ISO 3166-1 alpha-2 country code |
| date | DATE | NOT NULL | Competition date |
| status | ENUM | NOT NULL, DEFAULT 'draft' | Competition status |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Enum Values for `status`**: `draft`, `active`, `completed`

**Relationships**:
- `hasMany(Stage::class)`
- `belongsToMany(Competitor::class)->using(CompetitionCompetitor::class)`
- `hasMany(Attempt::class)`

**Cascade Deletes**: When deleted, cascade delete all related `stages`, `measures`, `competition_competitor`, `attempts`, and `attempt_results`

---

### stages

Defines stages within a competition.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| competition_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to competitions |
| order | INTEGER | NOT NULL | Display/execution order |
| name | VARCHAR(255) | NULLABLE | Optional stage name |
| expected_seconds | INTEGER | NOT NULL | Expected completion time in seconds |
| status | ENUM | NOT NULL, DEFAULT 'pending' | Stage status |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Enum Values for `status`**: `pending`, `active`, `complete`

**Foreign Keys**:
- `competition_id` REFERENCES `competitions(id)` ON DELETE CASCADE

**Relationships**:
- `belongsTo(Competition::class)`
- `hasMany(Measure::class)`
- `hasMany(Attempt::class)`

**Business Rules**:
- Stages can be reordered after creation
- Each stage must have at least one measure

---

### measures

Defines pour measurements required for each stage.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| stage_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to stages |
| target_ml | DECIMAL(6,2) | NOT NULL | Target volume in milliliters |
| order | INTEGER | NOT NULL | Display order within stage |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Foreign Keys**:
- `stage_id` REFERENCES `stages(id)` ON DELETE CASCADE

**Relationships**:
- `belongsTo(Stage::class)`
- `hasMany(AttemptResult::class)`

**Validation**:
- `target_ml` must be DECIMAL(6,2): max value 9999.99, 2 decimal places
- Client-side validation: ensure input is float within constraint

**Business Rules**:
- At least one measure per stage required
- Measures define the specific pour volumes competitors must attempt

---

### competitors

Stores competitor/bartender information.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Competitor full name |
| country | VARCHAR(255) | NOT NULL | Country name |
| bar_name | VARCHAR(255) | NULLABLE | Bar/establishment name |
| instagram | VARCHAR(255) | NULLABLE | Instagram handle |
| diffords_profile | VARCHAR(255) | NULLABLE | Difford's Guide profile URL |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Relationships**:
- `belongsToMany(Competition::class)->using(CompetitionCompetitor::class)`
- `hasMany(Attempt::class)`

**Business Rules**:
- Competitor name should be unique (consider adding unique index in future)
- Optional social/profile fields for public display

---

### competition_competitor

Pivot table linking competitors to competitions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| competition_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to competitions |
| competitor_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to competitors |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Foreign Keys**:
- `competition_id` REFERENCES `competitions(id)` ON DELETE CASCADE
- `competitor_id` REFERENCES `competitors(id)` ON DELETE CASCADE

**Indexes**:
- Consider unique index on `(competition_id, competitor_id)` to prevent duplicates

**Business Rules**:
- A competitor can be assigned to multiple competitions
- A competition can have multiple competitors

---

### attempts

Records each competitor's attempt at a stage.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| competition_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to competitions |
| competitor_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to competitors |
| stage_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to stages |
| started_at | DATETIME | NULLABLE | Attempt start time |
| ended_at | DATETIME | NULLABLE | Attempt end time |
| duration_ms | INTEGER | NULLABLE | Duration in milliseconds |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Foreign Keys**:
- `competition_id` REFERENCES `competitions(id)` ON DELETE CASCADE
- `competitor_id` REFERENCES `competitors(id)` ON DELETE CASCADE
- `stage_id` REFERENCES `stages(id)` ON DELETE CASCADE

**Relationships**:
- `belongsTo(Competition::class)`
- `belongsTo(Competitor::class)`
- `belongsTo(Stage::class)`
- `hasMany(AttemptResult::class)`

**Business Rules**:
- **NO RETRIES**: Each competitor gets ONE attempt per stage (enforce in application logic)
- Competitors must complete stages in series (not parallel)
- `duration_ms` calculated as: `(ended_at - started_at)` in milliseconds
- All three timestamp fields remain NULL until attempt begins

**Validation**:
- Prevent creating multiple attempts for same competitor+stage combination
- Ensure `ended_at` > `started_at` when both are set

---

### attempt_results

Stores the actual poured volume for each measure in an attempt.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| attempt_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to attempts |
| measure_id | BIGINT UNSIGNED | FK, NOT NULL | Foreign key to measures |
| poured_ml | DECIMAL(6,2) | NOT NULL | Actual poured volume in ml |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Foreign Keys**:
- `attempt_id` REFERENCES `attempts(id)` ON DELETE CASCADE
- `measure_id` REFERENCES `measures(id)` ON DELETE CASCADE

**Relationships**:
- `belongsTo(Attempt::class)`
- `belongsTo(Measure::class)`

**Validation**:
- `poured_ml` must be DECIMAL(6,2): max value 9999.99, 2 decimal places
- Client-side validation: ensure input is float within constraint

**Business Rules**:
- One result per measure per attempt
- Results used for accuracy scoring calculation

---

## Migration Order

Migrations must be created and run in this order to respect foreign key constraints:

1. `competitions`
2. `stages` (requires competitions)
3. `measures` (requires stages)
4. `competitors`
5. `competition_competitor` (requires competitions and competitors)
6. `attempts` (requires competitions, competitors, stages)
7. `attempt_results` (requires attempts and measures)

## Indexes (Future Consideration)

While not required initially, consider adding these indexes for performance:

- `stages.competition_id`
- `measures.stage_id`
- `attempts.competition_id`
- `attempts.competitor_id`
- `attempts.stage_id`
- `attempt_results.attempt_id`
- `attempt_results.measure_id`
- Unique: `competition_competitor(competition_id, competitor_id)`
- Unique: `attempts(competition_id, competitor_id, stage_id)` (enforce no retries)

## Soft Deletes

**NOT USING SOFT DELETES** - Hard deletes with cascade are sufficient for this application.

## Required vs Nullable Fields Summary

### Nullable Fields (Complete List)
- `competitor.bar_name`
- `competitor.instagram`
- `competitor.diffords_profile`
- `attempt.started_at`
- `attempt.ended_at`
- `attempt.duration_ms`
- `stage.name`

### All Other Fields
Required (NOT NULL)
