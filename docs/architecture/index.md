# Architecture Documentation Index

## Overview

This directory contains the sharded architecture documentation for the Pour Test Competition App. Each document focuses on a specific aspect of the system architecture.

## Quick Start

**New to the project?** Read documents in this order:

1. [Tech Stack](tech-stack.md) - Understand the technologies used
2. [Database Schema](database-schema.md) - Learn the data model
3. [Project Structure](unified-project-structure.md) - Navigate the codebase
4. [Coding Standards](coding-standards.md) - Follow project conventions

**Building a feature?** Reference these based on your task:

- Backend work → [Backend Architecture](backend-architecture.md)
- Frontend work → [Frontend Architecture](frontend-architecture.md)
- API endpoints → [REST API Spec](rest-api-spec.md)
- Testing → [Testing Strategy](testing-strategy.md)

---

## Core Architecture Documents

### [Tech Stack](tech-stack.md)
**Purpose**: Technology choices, versions, and dependencies

**Contents**:
- PHP 8.1+, Laravel 10+, MariaDB
- Frontend technologies (Blade, JavaScript, CSS)
- Testing tools (PHPUnit, Codeception)
- Development and deployment requirements
- Browser support and responsive breakpoints

**When to reference**:
- Setting up development environment
- Adding new dependencies
- Understanding version constraints

---

### [Coding Standards](coding-standards.md)
**Purpose**: Code style, conventions, and best practices

**Contents**:
- PSR-12 PHP standards
- Laravel naming conventions (models, controllers, routes, migrations)
- Code organization patterns
- Documentation requirements (PHPDoc)
- Type declarations and strict typing
- Error handling guidelines
- Security best practices
- Git conventions

**When to reference**:
- Before writing any code
- During code review
- When creating new files/classes

---

### [Database Schema](database-schema.md)
**Purpose**: Complete database design and relationships

**Contents**:
- All 7 tables with full column specifications
- Foreign key relationships and cascade rules
- Enum values for status fields
- Required vs nullable fields (complete list)
- Migration order
- Business rules (one attempt per stage, no retries, etc.)
- Validation constraints (decimal(6,2), etc.)

**When to reference**:
- Creating migrations
- Writing models
- Understanding data relationships
- Implementing business logic

---

### [Testing Strategy](testing-strategy.md)
**Purpose**: Testing approach, tools, and requirements

**Contents**:
- Unit tests (PHPUnit) - 80% coverage for models/services
- Feature tests (PHPUnit) - HTTP, auth, validation
- Browser tests (Codeception) - End-to-end workflows
- Test organization and naming conventions
- Coverage requirements by component
- Running tests and CI pipeline
- Definition of Done testing checklist

**When to reference**:
- Writing tests for a story
- Understanding coverage requirements
- Setting up test environment
- Verifying story completion

---

### [Unified Project Structure](unified-project-structure.md)
**Purpose**: Directory organization and file locations

**Contents**:
- Complete directory tree
- File naming conventions
- Organization by feature (controllers, models, views, tests)
- Custom structure decisions (Services, frontend src/)
- Route organization
- Documentation structure (architecture/, prd/, stories/)
- Git ignore rules

**When to reference**:
- Creating new files
- Navigating the codebase
- Understanding where code belongs
- Organizing imports and dependencies

---

## Implementation-Specific Documents

### [Backend Architecture](backend-architecture.md)
**Purpose**: Backend patterns, controllers, and services

**Contents**:
- MVC + Service Layer architecture
- Request flow diagram
- All 6 controllers with routes and methods
- Service layer (ScoringService, TimingService, etc.)
- Middleware (auth, CSRF)
- Error handling strategy
- Validation patterns (Form Requests)
- Dependency injection examples
- Business logic implementation

**When to reference**:
- Implementing controllers
- Creating services
- Adding routes
- Understanding request/response flow
- Implementing business rules

---

### [REST API Spec](rest-api-spec.md)
**Purpose**: API endpoint specifications

**Contents**:
- Complete API endpoint list with request/response formats
- Authentication requirements per endpoint
- Validation rules
- Error response formats
- Polling strategy for live updates
- Rate limiting
- HTTP status codes

**When to reference**:
- Implementing API endpoints
- Building frontend AJAX calls
- Implementing live polling
- Understanding data formats
- Handling API errors

---

### [Frontend Architecture](frontend-architecture.md)
**Purpose**: Frontend patterns, Blade templates, JavaScript, and CSS

**Contents**:
- Server-rendered Blade architecture
- Layout organization (app, admin, display)
- Responsive design strategy (mobile-first, 4 breakpoints)
- JavaScript modules (timer, polling, validation)
- CSS architecture (BEM, variables, components)
- Projector optimization for display screens
- Build process (Laravel Mix/Vite)
- Accessibility and performance

**When to reference**:
- Creating Blade templates
- Writing JavaScript
- Styling components
- Implementing responsive design
- Building display screens
- Form validation

---

## Document Relationships

```
                    ┌─────────────────┐
                    │  Tech Stack     │
                    └────────┬────────┘
                             │
            ┌────────────────┴────────────────┐
            │                                 │
    ┌───────▼────────┐              ┌────────▼────────┐
    │ Backend Arch   │              │ Frontend Arch   │
    │ - Controllers  │              │ - Blade         │
    │ - Services     │              │ - JavaScript    │
    │ - Middleware   │              │ - CSS           │
    └───────┬────────┘              └────────┬────────┘
            │                                 │
            │        ┌────────────────┐       │
            └────────► REST API Spec  ◄───────┘
                     └────────┬───────┘
                              │
                ┌─────────────┴─────────────┐
                │                           │
        ┌───────▼────────┐          ┌──────▼──────┐
        │ Database       │          │ Project     │
        │ Schema         │          │ Structure   │
        └────────────────┘          └──────┬──────┘
                                           │
                                    ┌──────▼──────┐
                                    │ Coding      │
                                    │ Standards   │
                                    └──────┬──────┘
                                           │
                                    ┌──────▼──────┐
                                    │ Testing     │
                                    │ Strategy    │
                                    └─────────────┘
```

---

## Usage Guidelines

### For Story Managers (Planning Stories)

When creating a story, reference:
1. **Database Schema** - for data model details
2. **Backend/Frontend Architecture** - for implementation patterns
3. **Project Structure** - for file locations
4. **Testing Strategy** - for test requirements

Include specific references in story's "Dev Notes" section:
```markdown
[Source: architecture/database-schema.md#competitions-table]
[Source: architecture/backend-architecture.md#scoring-service]
```

### For Developers (Implementing Stories)

Before starting implementation:
1. Read the **Story** file (contains extracted, relevant architecture details)
2. Reference **Coding Standards** for style guidelines
3. Refer to specific architecture docs as needed (linked in story)
4. Consult **Testing Strategy** for test requirements

### For Reviewers

Check implementation against:
1. **Coding Standards** - style and conventions
2. **Architecture Documents** - correct patterns used
3. **Testing Strategy** - adequate test coverage
4. **Story Acceptance Criteria** - requirements met

---

## Updating Architecture Documents

### When to Update

- Tech stack changes (new dependencies, version upgrades)
- Database schema changes (new tables, columns, relationships)
- New routes or API endpoints added
- Coding standards evolve
- Testing requirements change

### How to Update

1. Update the relevant architecture document
2. Update related documents if needed (e.g., schema change → update Backend Architecture)
3. Update this index if structure changes
4. Reference the change in the story or epic that required it
5. Notify team of significant architecture changes

---

## Related Documentation

- [Product Requirements Document](../prd.md) - User-facing feature requirements
- [Original Architecture](../architecture.md) - Consolidated architecture (kept for reference)
- [Epics](../prd/) - Feature epics
- [Stories](../stories/) - Implementation stories

---

## Document Status

| Document | Status | Last Updated |
|----------|--------|--------------|
| Tech Stack | ✅ Complete | 2025-11-04 |
| Coding Standards | ✅ Complete | 2025-11-04 |
| Database Schema | ✅ Complete | 2025-11-04 |
| Testing Strategy | ✅ Complete | 2025-11-04 |
| Project Structure | ✅ Complete | 2025-11-04 |
| Backend Architecture | ✅ Complete | 2025-11-04 |
| REST API Spec | ✅ Complete | 2025-11-04 |
| Frontend Architecture | ✅ Complete | 2025-11-04 |

---

## Notes

- All architecture documents follow BMAD (Backlog Management and Development) methodology
- Documents are sharded for easy navigation and reduced cognitive load
- Each document is self-contained but cross-references related docs
- Story files should extract and summarize relevant architecture details (no need for devs to read full architecture docs)
- Keep documents up-to-date as project evolves
