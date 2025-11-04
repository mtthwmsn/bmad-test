# Pour Test App — Product Requirements Document (PRD)

## 🧭 Product Overview

**Product Name:** Pour Test Competition App  
**Purpose:** Facilitate live, in-person "Pour Test" competitions where bartenders pour specific volumes of liquid (in ml) as accurately and quickly as possible, without tools.  
**Primary Users:** Referees (Admins), Spectators, Competitors  
**Tech Stack:** PHP 8.1+, templating engine (e.g., Blade or Twig), MariaDB

---

## 👥 User Roles & Goals

### 🧑‍⚖️ Referee / Admin
- Create and run competitions
- Add and manage competitors
- Input pour results during live events
- Oversee accurate score calculation and leaderboard updates

### 👤 Competitor
- Participate in competitions (passively)
- Appear on public leaderboards with profile info
- Share personal results from past events

### 👀 Spectator
- View live progress of competitions
- Follow competitors in real-time
- Access competitor profiles and linked DG content

---

## ⚙️ Feature-Level Requirements

### 1. Competition Management
- Create competition (draft): name, date, location
- Add competitors (new or existing)
- Define multiple stages per competition with required pour measures
- Activate competition for live usage
- Edit/delete draft competitions

### 2. Competition Execution (Live Event)
- List of active competitions
- Select competitor to start stage
- Start/stop timer (per stage)
- Input actual poured amounts per measure
- Calculate and display score (accuracy + time-based)
- Progress through all stages and competitors

### 3. Competitor Management
- Create/edit/delete competitor profiles
- Fields: name, country, bar, Instagram, DG profile URL
- Search and filter by name, bar, country
- Duplicate detection
- Assign to competitions

### 4. Public Display (Live Screen)
- Show current competitor, stage, and measures
- Live update as results are entered
- Show timer per stage
- Final score reveal after last stage
- Leaderboard: sorted by score
- QR codes for DG profiles (optional for MVP)
- Projector-friendly layout with auto-refresh

---

## 🧾 User Stories

### Referee/Admin
- As a referee, I want to create a competition with name, date, and location
- As a referee, I want to define pour stages with required measures
- As a referee, I want to start/stop a timer and enter pour volumes
- As a referee, I want to assign competitors and move them through stages
- As a referee, I want to see live results and final scores

### Competitor
- As a competitor, I want my results shown on the leaderboard
- As a competitor, I want my profile to display bar, country, and links
- As a competitor, I want to look up and share previous events when a competition is not running

### Spectator
- As a spectator, I want to watch the current competitor in real time
- As a spectator, I want to see live timer, pours, and accuracy updates
- As a spectator, I want to view a leaderboard that updates automatically
- As a spectator, I want to scan a QR code to access a competitor's DG profile

---

## 🥇 MVP Scope

### Must Have (MVP)
- Competition creation and stage setup
- Competitor CRUD and assignment
- Referee panel: run competition, timer, pour input, scoring
- Public display: real-time results + leaderboard
- Device-agnostic responsive UI
- PHP 8.1+, templating engine, MariaDB backend

### Nice to Have (Post-MVP)
- Competitor profile stats and archive
- Past event access + shareable links
- QR codes to DG profiles
- Manual result corrections

### Future Ideas
- Global rankings
- Seasonal leagues and sponsored events
- Offline mode or multi-referee sync
- Editorial and branded content integration

---

## ✅ Summary
The Pour Test app is a lean, competition-focused tool for referees to facilitate accurate, fast-paced bartender challenges, with public-facing components to drive spectator engagement and link to the broader Difford's Guide brand ecosystem.

