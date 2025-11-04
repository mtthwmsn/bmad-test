# Frontend Architecture

## Overview

The Pour Test Competition App uses **server-rendered views** with Blade templating. JavaScript is used for interactivity (timers, polling, form validation) and CSS for responsive design.

**No frontend framework** (React, Vue, etc.) - Keep it simple with vanilla JavaScript + Blade.

---

## Technology Stack

- **Templating**: Laravel Blade
- **JavaScript**: ES6+ (vanilla, no framework)
- **CSS**: CSS3 with responsive design
- **Build Tool**: Laravel Mix or Vite (Laravel default)
- **Target Browsers**: Modern browsers (Chrome, Firefox, Safari, Edge - latest 2 versions)

---

## Responsive Design

### Breakpoints

Target 4 viewport sizes:

| Device | Width | CSS Media Query |
|--------|-------|-----------------|
| Mobile | 320px - 767px | `@media (max-width: 767px)` |
| Tablet | 768px - 1024px | `@media (min-width: 768px) and (max-width: 1024px)` |
| Laptop | 1025px - 1440px | `@media (min-width: 1025px) and (max-width: 1440px)` |
| Large Screen/Projector | 1441px+ | `@media (min-width: 1441px)` |

### Mobile-First Approach

Write base styles for mobile, then use `min-width` media queries to adapt for larger screens:

```css
/* Mobile first (base styles) */
.container {
  padding: 1rem;
}

/* Tablet and up */
@media (min-width: 768px) {
  .container {
    padding: 2rem;
  }
}

/* Desktop and up */
@media (min-width: 1025px) {
  .container {
    padding: 3rem;
  }
}
```

---

## Blade Templates

### Layout Structure

#### Main Layout (`layouts/app.blade.php`)
Base layout for all pages.

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pour Test Competition')</title>
    <link rel="stylesheet" href="{{ asset('dist/css/app.css') }}">
    @stack('styles')
</head>
<body>
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('dist/javascript/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
```

#### Admin Layout (`layouts/admin.blade.php`)
Layout for admin panel (extends `app.blade.php`).

```blade
@extends('layouts.app')

@section('content')
<div class="admin-container">
    <nav class="admin-nav">
        @include('admin.partials.sidebar')
    </nav>

    <div class="admin-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('admin-content')
    </div>
</div>
@endsection
```

#### Display Layout (`layouts/display.blade.php`)
Layout for public display screens (full-screen, no navigation).

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Live Display')</title>
    <link rel="stylesheet" href="{{ asset('dist/css/display.css') }}">
    @stack('styles')
</head>
<body class="display-body">
    @yield('content')

    <script src="{{ asset('dist/javascript/app.js') }}"></script>
    <script src="{{ asset('dist/javascript/polling.js') }}"></script>
    @stack('scripts')
</body>
</html>
```

---

### View Organization

Views are organized by controller/feature:

```
resources/views/
├── layouts/              # Base layouts
├── partials/             # Reusable components
│   ├── header.blade.php
│   ├── footer.blade.php
│   └── flash-messages.blade.php
├── admin/
│   ├── partials/
│   │   └── sidebar.blade.php
│   ├── competitions/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php      # Unified: stages, measures, competitors
│   └── competitors/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── run/
│   ├── dashboard.blade.php     # Competition run dashboard
│   ├── timer.blade.php         # Timer + input screen
│   └── results.blade.php       # Show attempt results
├── display/
│   ├── live.blade.php          # Live stage display
│   └── leaderboard.blade.php   # Leaderboard screen
└── errors/
    ├── 404.blade.php
    ├── 500.blade.php
    └── error.blade.php         # Generic error page
```

---

## JavaScript Architecture

### File Structure

```
src/javascript/
├── app.js              # Main entry point
├── timer.js            # Timer functionality
├── polling.js          # Live update polling
├── validation.js       # Client-side form validation
└── utils.js            # Utility functions
```

### Module Pattern

Use ES6 modules:

**timer.js**:
```javascript
export class Timer {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.startTime = null;
    this.interval = null;
  }

  start() {
    this.startTime = Date.now();
    this.interval = setInterval(() => this.update(), 100);
  }

  stop() {
    if (this.interval) {
      clearInterval(this.interval);
      return this.getElapsedMs();
    }
    return 0;
  }

  update() {
    const elapsed = this.getElapsedMs();
    this.container.textContent = this.formatTime(elapsed);
  }

  getElapsedMs() {
    return this.startTime ? Date.now() - this.startTime : 0;
  }

  formatTime(ms) {
    const seconds = Math.floor(ms / 1000);
    const milliseconds = ms % 1000;
    return `${seconds}.${String(milliseconds).padStart(3, '0')}s`;
  }
}
```

**app.js**:
```javascript
import { Timer } from './timer.js';
import { initPolling } from './polling.js';
import { validateForms } from './validation.js';

document.addEventListener('DOMContentLoaded', () => {
  // Initialize timer if on timer page
  const timerEl = document.getElementById('timer');
  if (timerEl) {
    window.timer = new Timer('timer');

    document.getElementById('start-btn').addEventListener('click', () => {
      window.timer.start();
    });

    document.getElementById('stop-btn').addEventListener('click', () => {
      const elapsed = window.timer.stop();
      document.getElementById('duration').value = elapsed;
    });
  }

  // Initialize polling if on display page
  if (document.body.classList.contains('display-page')) {
    const competitionId = document.body.dataset.competitionId;
    initPolling(competitionId);
  }

  // Initialize form validation
  validateForms();
});
```

---

### Polling for Live Updates

**polling.js**:
```javascript
export function initPolling(competitionId) {
  const pollInterval = 1500; // 1.5 seconds

  function pollLiveData() {
    fetch(`/api/display/competition/${competitionId}/live`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateLiveDisplay(data.data);
        }
      })
      .catch(error => {
        console.error('Polling error:', error);
        // Show error indicator on screen
        document.getElementById('status').textContent = 'Connection lost...';
      })
      .finally(() => {
        setTimeout(pollLiveData, pollInterval);
      });
  }

  function updateLiveDisplay(data) {
    // Update competitor name
    const nameEl = document.getElementById('competitor-name');
    if (nameEl && data.current_attempt) {
      nameEl.textContent = data.current_attempt.competitor.name;
    }

    // Update timer
    const timerEl = document.getElementById('timer');
    if (timerEl && data.current_attempt && !data.current_attempt.ended_at) {
      const elapsed = data.current_attempt.elapsed_ms;
      timerEl.textContent = formatTime(elapsed);
    }

    // Update measures
    updateMeasures(data.current_attempt?.measures || []);
  }

  function formatTime(ms) {
    const seconds = Math.floor(ms / 1000);
    const milliseconds = ms % 1000;
    return `${seconds}.${String(milliseconds).padStart(3, '0')}s`;
  }

  function updateMeasures(measures) {
    measures.forEach(measure => {
      const row = document.querySelector(`[data-measure-id="${measure.id}"]`);
      if (row) {
        row.querySelector('.target').textContent = `${measure.target_ml} ml`;
        row.querySelector('.poured').textContent = measure.poured_ml
          ? `${measure.poured_ml} ml`
          : '--';
      }
    });
  }

  // Start polling
  pollLiveData();
}
```

---

### Form Validation

**validation.js**:
```javascript
export function validateForms() {
  const forms = document.querySelectorAll('form[data-validate]');

  forms.forEach(form => {
    form.addEventListener('submit', (e) => {
      let isValid = true;

      // Validate decimal(6,2) fields
      const decimalFields = form.querySelectorAll('input[data-type="decimal"]');
      decimalFields.forEach(field => {
        if (!validateDecimal(field.value)) {
          showError(field, 'Must be a number between 0 and 9999.99 with max 2 decimal places');
          isValid = false;
        } else {
          clearError(field);
        }
      });

      // Validate required fields
      const requiredFields = form.querySelectorAll('input[required], select[required]');
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          showError(field, 'This field is required');
          isValid = false;
        } else {
          clearError(field);
        }
      });

      if (!isValid) {
        e.preventDefault();
      }
    });
  });
}

function validateDecimal(value) {
  const num = parseFloat(value);
  if (isNaN(num) || num < 0 || num > 9999.99) {
    return false;
  }

  // Check max 2 decimal places
  const decimalPart = value.split('.')[1];
  if (decimalPart && decimalPart.length > 2) {
    return false;
  }

  return true;
}

function showError(field, message) {
  const errorEl = field.parentElement.querySelector('.error-message');
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.style.display = 'block';
  }
  field.classList.add('error');
}

function clearError(field) {
  const errorEl = field.parentElement.querySelector('.error-message');
  if (errorEl) {
    errorEl.style.display = 'none';
  }
  field.classList.remove('error');
}
```

---

## CSS Architecture

### File Structure

```
src/css/
├── app.css             # Main stylesheet (imports all others)
├── variables.css       # CSS custom properties (colors, spacing, etc.)
├── reset.css           # CSS reset/normalize
├── layout.css          # Layout utilities (grid, flexbox)
├── components.css      # Reusable components (buttons, forms, cards)
├── admin.css           # Admin panel specific styles
├── display.css         # Public display specific styles
└── responsive.css      # Media queries and responsive utilities
```

### CSS Variables

**variables.css**:
```css
:root {
  /* Colors */
  --color-primary: #2563eb;
  --color-secondary: #64748b;
  --color-success: #10b981;
  --color-error: #ef4444;
  --color-warning: #f59e0b;

  --color-bg: #ffffff;
  --color-bg-secondary: #f8fafc;
  --color-text: #1e293b;
  --color-text-muted: #64748b;

  /* Spacing */
  --spacing-xs: 0.25rem;
  --spacing-sm: 0.5rem;
  --spacing-md: 1rem;
  --spacing-lg: 1.5rem;
  --spacing-xl: 2rem;
  --spacing-2xl: 3rem;

  /* Typography */
  --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  --font-size-sm: 0.875rem;
  --font-size-base: 1rem;
  --font-size-lg: 1.125rem;
  --font-size-xl: 1.25rem;
  --font-size-2xl: 1.5rem;
  --font-size-3xl: 2rem;

  /* Border radius */
  --radius-sm: 0.25rem;
  --radius-md: 0.375rem;
  --radius-lg: 0.5rem;

  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
```

### BEM Naming Convention

Use BEM (Block Element Modifier) for class names:

```css
/* Block */
.card {
  background: var(--color-bg);
  border-radius: var(--radius-md);
  padding: var(--spacing-md);
}

/* Element */
.card__title {
  font-size: var(--font-size-lg);
  font-weight: 600;
}

.card__body {
  margin-top: var(--spacing-sm);
}

/* Modifier */
.card--highlighted {
  border: 2px solid var(--color-primary);
}
```

### Responsive Components

**Example: Competition Card**:
```css
.competition-card {
  background: var(--color-bg);
  padding: var(--spacing-md);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
}

.competition-card__title {
  font-size: var(--font-size-lg);
  margin-bottom: var(--spacing-sm);
}

/* Mobile: Stack vertically */
.competition-card__details {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-sm);
}

/* Tablet and up: Horizontal layout */
@media (min-width: 768px) {
  .competition-card__details {
    flex-direction: row;
    justify-content: space-between;
  }
}

/* Large screens: Larger text for projectors */
@media (min-width: 1441px) {
  .competition-card__title {
    font-size: var(--font-size-3xl);
  }
}
```

---

## Display Screens (Projector Optimization)

### Large Text & High Contrast

```css
.display-body {
  background: #000;
  color: #fff;
  font-size: var(--font-size-2xl);
}

.display-title {
  font-size: 4rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: var(--spacing-2xl);
}

.display-timer {
  font-size: 8rem;
  font-weight: 700;
  text-align: center;
  color: var(--color-success);
}

.leaderboard-row {
  padding: var(--spacing-lg);
  font-size: var(--font-size-xl);
  border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.leaderboard-row:first-child {
  background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
  color: #000;
  font-size: var(--font-size-2xl);
}
```

---

## Build Process

### Laravel Mix (if using)

**webpack.mix.js**:
```javascript
const mix = require('laravel-mix');

mix.js('src/javascript/app.js', 'public/dist/javascript')
   .css('src/css/app.css', 'public/dist/css')
   .version();
```

### Vite (Laravel 10+ default)

**vite.config.js**:
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'src/css/app.css',
                'src/javascript/app.js',
            ],
            refresh: true,
        }),
    ],
});
```

---

## Accessibility

- Use semantic HTML (`<button>`, `<nav>`, `<main>`, `<article>`)
- Include `alt` text for images
- Ensure sufficient color contrast (WCAG AA)
- Use ARIA labels where needed
- Keyboard navigation support

---

## Performance

- Minify CSS and JavaScript for production
- Use asset versioning to bust cache
- Lazy-load images if needed
- Keep JavaScript bundle small (no heavy frameworks)
- Optimize polling interval to balance freshness and server load

---

## Notes

- Keep JavaScript vanilla (no jQuery, no frameworks) for simplicity
- Use Blade for all rendering (server-side)
- Only use JavaScript for interactivity that can't be done server-side
- Ensure responsive design works on all target devices
- Test display screens on actual projectors if possible
- High contrast and large text for projector readability
