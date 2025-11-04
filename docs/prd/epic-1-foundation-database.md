# Epic 1: Foundation and Database Setup

## Epic Goal

Establish the foundational Laravel application structure and database schema to support Pour Test competitions, including all core models and relationships.

## Epic Description

This epic creates the technical foundation for the Pour Test Competition App. It includes setting up the Laravel project, configuring the MariaDB database, and implementing all core models and migrations based on the architecture specification.

**Why This Matters:**
All other features depend on this foundation. Without properly structured models and migrations, we cannot build competition management, competitor tracking, or the live display features.

## Prerequisites

- PHP 8.1+ installed
- Composer installed
- MariaDB server available
- Basic understanding of Laravel conventions

## Stories

### Story 1.1: Laravel Project Setup and Configuration

**Goal:** Initialize a new Laravel 10+ project with MariaDB configuration and basic project structure.

**Requirements:**
- Install Laravel 10+ via Composer
- Configure `.env` file for MariaDB connection
- Set up basic application settings (timezone, locale, app name)
- Configure session and cache drivers
- Set up basic directory structure

**Acceptance Criteria:**
1. Laravel application runs successfully on `php artisan serve`
2. Database connection to MariaDB is successful
3. Default Laravel migrations run without errors
4. Application environment is configured for development

**Tasks:**
- Create new Laravel project
- Configure database credentials in `.env`
- Test database connection
- Run default migrations
- Verify application serves correctly

---

### Story 1.2: Core Database Models and Migrations

**Goal:** Create all database migrations and Eloquent models for competitions, stages, measures, competitors, attempts, and results.

**Requirements:**
- Create migrations for all 7 tables from architecture spec:
  - `competitions` table
  - `stages` table
  - `measures` table
  - `competitors` table
  - `competition_competitor` pivot table
  - `attempts` table
  - `attempt_results` table
- Create corresponding Eloquent models with relationships
- Define fillable fields and validation rules
- Set up model relationships (hasMany, belongsTo, belongsToMany)

**Acceptance Criteria:**
1. All migrations run successfully and create tables with correct schema
2. All models are created with proper namespace and structure
3. Model relationships are defined and functional
4. Foreign key constraints are properly set
5. Enum fields (status) are correctly configured
6. Timestamps are enabled on all models

**Data Model Details (from architecture.md):**

**competition:**
- id (PK)
- name (string)
- city (string)
- country_code (string, 2 chars)
- date (date)
- status (enum: draft, active, completed)
- timestamps

**stage:**
- id (PK)
- competition_id (FK)
- order (integer)
- name (string, nullable)
- expected_seconds (integer)
- status (enum: pending, active, complete)
- timestamps

**measure:**
- id (PK)
- stage_id (FK)
- target_ml (decimal 6,2)
- order (integer)
- timestamps

**competitor:**
- id (PK)
- name (string)
- country (string)
- bar_name (string, nullable)
- instagram (string, nullable)
- diffords_profile (string, nullable)
- timestamps

**competition_competitor:**
- id (PK)
- competition_id (FK)
- competitor_id (FK)
- timestamps

**attempt:**
- id (PK)
- competition_id (FK)
- competitor_id (FK)
- stage_id (FK)
- started_at (datetime, nullable)
- ended_at (datetime, nullable)
- duration_ms (integer, nullable)
- timestamps

**attempt_result:**
- id (PK)
- attempt_id (FK)
- measure_id (FK)
- poured_ml (decimal 6,2)
- timestamps

**Tasks:**
- Create migration for `competitions` table
- Create migration for `stages` table with FK to competitions
- Create migration for `measures` table with FK to stages
- Create migration for `competitors` table
- Create migration for `competition_competitor` pivot table
- Create migration for `attempts` table with FKs
- Create migration for `attempt_results` table with FKs
- Create `Competition` model with relationships
- Create `Stage` model with relationships
- Create `Measure` model with relationships
- Create `Competitor` model with relationships
- Create `Attempt` model with relationships
- Create `AttemptResult` model with relationships
- Test all model relationships with tinker
- Create database seeder with sample data for testing

---

### Story 1.3: Model Factories and Basic Seeders

**Goal:** Create model factories and seeders to support development and testing with realistic data.

**Requirements:**
- Create factory for each model with realistic fake data
- Create database seeder with sample competitions, competitors, and attempts
- Ensure seeded data respects relationships and constraints
- Create separate seeders for development vs. production

**Acceptance Criteria:**
1. All models have factories with appropriate fake data
2. `php artisan db:seed` populates database with valid sample data
3. Seeded data includes at least:
   - 2 competitions (1 draft, 1 active)
   - 3 stages per competition with 2-4 measures each
   - 5 competitors
   - Competitor assignments to active competition
   - Sample attempts with results for active competition
4. All foreign key relationships are valid in seeded data
5. Enum values are correctly set

**Tasks:**
- Create CompetitionFactory
- Create StageFactory
- Create MeasureFactory
- Create CompetitorFactory
- Create AttemptFactory
- Create AttemptResultFactory
- Create DatabaseSeeder with logical data flow
- Test seeding process
- Document seeder usage in README

---

## Epic Success Criteria

- All migrations execute successfully
- All models and relationships work correctly
- Database can be seeded with realistic test data
- Code follows Laravel conventions and best practices
- Foundation is ready for controller and service layer development

## Dependencies

**External:**
- None (this is the foundation)

**Internal:**
- PHP 8.1+
- Composer
- MariaDB server

## Technical Notes

**From architecture.md:**
- Using Laravel (assumed based on PHP 8.1+ and Blade templating)
- MariaDB as database
- Server-rendered views (Blade)
- Follow Laravel naming conventions for models and tables

## Risks and Mitigation

**Risk:** Migration issues due to foreign key constraints
**Mitigation:** Create migrations in correct order (parent tables before child tables)

**Risk:** Model relationship errors
**Mitigation:** Test each relationship in tinker before proceeding

## Definition of Done

- [ ] All 3 stories completed
- [ ] All migrations run cleanly on fresh database
- [ ] All model relationships tested and working
- [ ] Database can be seeded with test data
- [ ] Code reviewed for Laravel best practices
- [ ] Basic tests created for critical models
- [ ] Documentation updated with database setup instructions
