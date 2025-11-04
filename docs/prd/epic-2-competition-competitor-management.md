# Epic 2: Competition & Competitor Management

## Epic Goal

Build the administrative interface for referees to create and manage competitions, stages, measures, and competitor profiles.

## Epic Description

This epic implements the core CRUD operations and administrative interfaces needed for referees to set up and manage pour test competitions. It includes authentication, competition management (with inline stage/measure editing), and competitor management with search/filter capabilities.

**Why This Matters:**
Before competitions can be run live, referees need the ability to create competition structures, define stages with pour measures, and manage competitor profiles. This epic provides the foundation for the referee workflow.

## Prerequisites

- Epic 1 completed (database foundation in place)
- Understanding of Laravel MVC pattern
- Familiarity with Blade templating
- Basic knowledge of authentication flows

## Stories

### Story 2.1: Authentication Setup for Referees

**Goal:** Implement basic Laravel authentication for referee/admin users only (no public registration).

**Requirements:**
- Set up Laravel's built-in authentication system
- Create User model and migration (if not already present)
- Implement login/logout functionality
- Create auth middleware for admin routes
- Create a simple seeder for test referee account
- Disable public registration routes

**Acceptance Criteria:**
1. Referees can log in with username/password
2. All `/admin/*` routes require authentication
3. Unauthenticated users are redirected to login
4. Login view is styled consistently with app design
5. Test referee account exists for development
6. Public cannot self-register (registration disabled)

**Tasks:**
- Install Laravel Breeze or implement custom auth
- Create users table migration (if needed)
- Configure authentication guards and middleware
- Create login view
- Create UserSeeder with test referee account
- Apply `auth` middleware to admin routes
- Remove/disable registration routes

---

### Story 2.2: Competition CRUD Operations

**Goal:** Create full CRUD interface for managing competitions (create, read, update, delete).

**Requirements:**
- List view: Display all competitions with status badges
- Create form: Name, city, country code, date, status (default: draft)
- Edit form: Update competition details
- Delete: Remove competition (with confirmation)
- Validation: All fields required, country_code must be 2 chars
- Flash messages for success/error feedback
- Responsive design for all viewport sizes

**Acceptance Criteria:**
1. `/admin/competitions` displays paginated list of all competitions
2. Create form validates input and saves new competition
3. Edit form pre-populates existing data and saves changes
4. Delete removes competition and all related data (cascade)
5. Status badges show visual indicators (draft=gray, active=green, completed=blue)
6. Flash messages appear after create/update/delete actions
7. All views are responsive (mobile/tablet/laptop/large screen)

**Routes:**
- `GET /admin/competitions` → index
- `GET /admin/competitions/create` → create
- `POST /admin/competitions` → store
- `GET /admin/competitions/{id}/edit` → edit
- `PUT /admin/competitions/{id}` → update
- `DELETE /admin/competitions/{id}` → destroy

**Tasks:**
- Create CompetitionController with CRUD methods
- Create Form Request for validation (StoreCompetitionRequest, UpdateCompetitionRequest)
- Create Blade views: index, create, edit
- Define routes in `routes/web.php` with `auth` middleware
- Add status badge component/partial
- Add flash message display component
- Test all CRUD operations

---

### Story 2.3: Stage & Measure Management (Inline)

**Goal:** Allow referees to manage stages and measures directly within the competition edit view (no separate CRUD screens).

**Requirements:**
- Edit competition view includes stages section
- Add/edit/delete stages inline
- Each stage can have multiple measures
- Add/edit/delete measures inline per stage
- Stage fields: order, name (nullable), expected_seconds, status
- Measure fields: name, target_ml, order
- Support reordering stages
- Validation for all fields
- AJAX/Livewire for better UX (optional, can use form submission)

**Acceptance Criteria:**
1. Competition edit view shows all existing stages
2. Can add new stage with order, name, expected_seconds
3. Can edit stage details inline
4. Can delete stage (with confirmation if has measures)
5. Each stage shows its measures
6. Can add/edit/delete measures per stage
7. Reordering stages updates `order` field
8. All changes persist to database
9. Validation prevents invalid data (e.g., negative ml values)

**Routes:**
- `POST /admin/competitions/{competitionId}/stages` → store stage
- `PUT /admin/stages/{id}` → update stage
- `DELETE /admin/stages/{id}` → destroy stage
- `POST /admin/stages/{stageId}/measures` → store measure
- `PUT /admin/measures/{id}` → update measure
- `DELETE /admin/measures/{id}` → destroy measure

**Tasks:**
- Create StageController with store/update/destroy methods
- Create MeasureController with store/update/destroy methods
- Update competition edit view to include stages section
- Add stage form partial/component
- Add measure form partial/component
- Add JavaScript for add/remove stage/measure UI
- Implement reordering functionality
- Add validation for stages and measures
- Test inline add/edit/delete operations

---

### Story 2.4: Competitor CRUD with Search & Filter

**Goal:** Implement full competitor management with search, filter, and assignment capabilities.

**Requirements:**
- List view: All competitors with search and country filter
- Create form: first_name, last_name, bar_name (nullable), instagram (nullable), diffords_profile (nullable)
- Edit form: Update competitor details
- Delete: Remove competitor (with warning if assigned to competitions)
- Search: Filter by name
- Filter: Filter by country or bar
- Pagination: 20 competitors per page
- Duplicate detection: Warn if similar name exists
- Assign to competition: Multi-select interface on competition edit

**Acceptance Criteria:**
1. `/admin/competitors` displays paginated list with search box
2. Search filters by first_name OR last_name
3. Country filter dropdown shows only countries with competitors
4. Create form validates required fields (first_name, last_name)
5. Edit form pre-populates data
6. Delete shows warning if competitor has attempts
7. Competition edit view allows assigning/unassigning competitors
8. Duplicate detection warns on create (same first+last name)
9. All views responsive

**Routes:**
- `GET /admin/competitors` → index (with search/filter)
- `GET /admin/competitors/create` → create
- `POST /admin/competitors` → store
- `GET /admin/competitors/{id}/edit` → edit
- `PUT /admin/competitors/{id}` → update
- `DELETE /admin/competitors/{id}` → destroy
- `POST /admin/competitions/{id}/competitors/assign` → assign competitors to competition
- `DELETE /admin/competitions/{competitionId}/competitors/{competitorId}` → unassign competitor

**Tasks:**
- Create CompetitorController with CRUD methods
- Implement search and filter logic in index method
- Create Form Requests for validation
- Create Blade views: index (with search/filter), create, edit
- Add competitor assignment section to competition edit view
- Implement duplicate detection on store
- Add warning for delete if has attempts
- Test search, filter, and CRUD operations

---

## Epic Success Criteria

- All 4 stories completed and tested
- Referees can log in and access admin panel
- Competitions can be created, edited, and deleted
- Stages and measures can be managed inline within competition
- Competitors can be created, searched, filtered, and assigned to competitions
- All views are responsive and follow design guidelines
- Code follows Laravel conventions and passes validation
- Flash messages provide clear user feedback
- Error handling displays friendly messages

## Dependencies

**External:**
- None

**Internal:**
- Epic 1 completed (database models and migrations)
- Blade templating knowledge
- Laravel authentication understanding

## Technical Notes

**Architecture References:**
- Backend Architecture: [docs/architecture/backend-architecture.md](../architecture/backend-architecture.md)
- Coding Standards: [docs/architecture/coding-standards.md](../architecture/coding-standards.md)
- Frontend Architecture: [docs/architecture/frontend-architecture.md](../architecture/frontend-architecture.md)

**Authentication:**
- Use Laravel's built-in auth system (Breeze/Fortify or custom)
- Only referees can access admin panel
- No public user registration

**Validation:**
- Use Form Request classes for complex validation
- Country code: ISO 3166-1 alpha-2 (exactly 2 characters)
- Target_ml: Positive decimal values only
- Expected_seconds: Positive integer only

**UI/UX:**
- Mobile-first responsive design
- Status badges with color coding
- Flash messages for user feedback
- Confirmation dialogs for destructive actions (delete)

## Risks and Mitigation

**Risk:** Inline stage/measure management may be complex for users
**Mitigation:** Clear UI with add/remove buttons, visual separation between stages

**Risk:** Deleting competition with active attempts may cause issues
**Mitigation:** Add confirmation dialog warning about cascade delete

**Risk:** Duplicate competitors may be created
**Mitigation:** Implement fuzzy matching and warn user during creation

**Risk:** Authentication setup may conflict with existing User model
**Mitigation:** Check if User model exists, adapt accordingly

## Definition of Done

- [ ] All 4 stories completed
- [ ] Authentication working for referee access
- [ ] Competition CRUD fully functional
- [ ] Stage and measure inline management working
- [ ] Competitor CRUD with search/filter working
- [ ] Competitor assignment to competitions functional
- [ ] All views are responsive (4 breakpoints)
- [ ] Flash messages display correctly
- [ ] Form validation prevents invalid data
- [ ] Code reviewed for Laravel best practices
- [ ] Manual testing completed on all features
- [ ] README updated with admin panel access instructions

## Notes

- This epic focuses purely on management/admin features
- Live competition execution (timer, results entry) is deferred to Epic 3
- Public display screens are deferred to Epic 4
- Keep controllers thin, use services for complex logic
- Follow PSR-12 coding standards
- Use strict types in all PHP files
