# Pour Test App — Architecture Specification

## ⚙️ Tech Stack Overview

- **Backend:** PHP 8.1+, Laravel (assumed)
- **Templating Engine:** Blade or Twig
- **Database:** MariaDB
- **Frontend:** Server-rendered views (Blade), projector-optimized display
- **Live Sync:** Polling (initial), optional WebSockets (v2)

---

## 🧩 Core Database Schema

### `competition`
- id (PK)
- name
- city
- country_code
- date
- status (enum: draft, active, completed)
- created_at / updated_at

### `stage`
- id (PK)
- competition_id (FK)
- order (int)
- name (optional)
- expected_seconds (int)
- status (enum: pending, active, complete)
- created_at / updated_at

### `measure`
- id (PK)
- stage_id (FK)
- target_ml (decimal 6,2)
- order (int)
- created_at / updated_at

### `competitor`
- id (PK)
- name
- country
- bar_name
- instagram
- diffords_profile
- created_at / updated_at

### `competition_competitor`
- id (PK)
- competition_id (FK)
- competitor_id (FK)
- created_at / updated_at

### `attempt`
- id (PK)
- competition_id (FK)
- competitor_id (FK)
- stage_id (FK)
- started_at (datetime)
- ended_at (datetime)
- duration_ms (int)
- created_at / updated_at

### `attempt_result`
- id (PK)
- attempt_id (FK)
- measure_id (FK)
- poured_ml (decimal 6,2)
- created_at / updated_at

---

## 📦 Controllers & Services

### `CompetitionController`
- Create/edit competitions
- Activate competitions
- View competition list

### `StageController`
- Create/edit stages inline within competition view
- Define required measures
- Set stage status

### `CompetitorController`
- CRUD + search/filter
- Assign to competitions

### `AttemptController`
- Start/stop timer
- Input results per measure
- View attempt result breakdown

### `DisplayController`
- Live stage display
- Real-time leaderboard

### `LeaderboardController`
- Compile scores and rankings
- Expose event and competitor histories

### Service Layer
- `ScoringService` — calculates OAP, OTS, TET, TP, AP, and final score
- `TimingService` — handles duration calc
- `LeaderboardService` — aggregates scores
- `DisplayService` — formats live view payloads

---

## 🖼️ Frontend Routes & Views

### Admin Panel
- `/admin/competitions` — list view
- `/admin/competition/{id}` — unified edit view:
  - Title, city, country
  - Competitor assignment
  - Stage + measure definition

### Run Interface
- `/run/competition/{id}` — run dashboard
- `/run/competition/{id}/competitor/{competitor_id}/stage/{stage_id}` — timer + input screen
- `/run/attempt/{id}/results` — show result

### Public Display
- `/display/competition/{id}/live` — projector screen (auto-refreshing)
- `/display/competition/{id}/leaderboard` — real-time scores

### Competitor & Archive
- `/competitor/{id}` — public profile + past events
- `/events` — archive
- `/event/{id}` — event result page

---

## 🔢 Scoring Logic

### Stage Configuration
Each `stage` defines an `expected_seconds` value.

### Calculation (done after all stages complete):

Let:
- TET = sum of all stage expected times (in seconds)
- OTS = sum of all `duration_ms / 1000`
- OAP = average accuracy percentage across all pours:

```
OAP = average of: 100 - (abs(poured_ml - target_ml) / target_ml × 100)
```

Then compute:
- TP = `1.5 × (TET - OTS)`
- AP = `1000 × (OAP - 0.95)`
- Final Score = `TP + AP`

---

## 📡 Live Update Mechanics

### Initial Implementation
- Client-side polling (1–2s) for display screens
- Controller endpoints return current competitor, pours, times, and leaderboard

### Future Option
- Laravel Echo + Soketi or Pusher for true WebSocket sync

---

## ❌ Dropped Features
- QR Code generation (deferred)
- Individual measure edit routes (merged into stage editing)
- Separate competitor assignment screen (now inline in competition edit view)

---

## ✅ Summary
This architecture supports real-time competition management and spectator engagement using a robust Laravel+MariaDB backend, minimal UI complexity, and an extensible scoring system built for fairness and performance.

