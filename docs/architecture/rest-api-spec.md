# REST API Specification

## Overview

The Pour Test Competition App primarily uses server-rendered views (Blade templates), but includes some API endpoints for:
1. Live polling for public display screens
2. AJAX operations during competition execution

All API endpoints return JSON responses.

## Authentication

- **Admin/Run Routes**: Require authentication (session-based, Laravel auth)
- **Public Display Routes**: No authentication required

## Response Format

### Success Response
```json
{
  "success": true,
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "error": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### HTTP Status Codes
- `200 OK` - Success
- `201 Created` - Resource created
- `422 Unprocessable Entity` - Validation error
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Fatal error

---

## API Endpoints

### Competition Execution (Authenticated)

#### Start Attempt
Start timer for a competitor's attempt at a stage.

**Endpoint**: `POST /api/attempt/start`

**Authentication**: Required (referee)

**Request Body**:
```json
{
  "competition_id": 1,
  "competitor_id": 5,
  "stage_id": 2
}
```

**Validation Rules**:
- `competition_id`: required, exists in competitions table
- `competitor_id`: required, exists in competitors table
- `stage_id`: required, exists in stages table
- Validation: No existing attempt for this competitor + stage combination

**Success Response** (201):
```json
{
  "success": true,
  "data": {
    "attempt_id": 42,
    "started_at": "2024-01-15T14:30:00.000000Z"
  }
}
```

**Error Response** (422):
```json
{
  "success": false,
  "error": "Stage already attempted. Retries are not allowed."
}
```

---

#### Stop Attempt
Stop timer and calculate duration.

**Endpoint**: `POST /api/attempt/stop`

**Authentication**: Required (referee)

**Request Body**:
```json
{
  "attempt_id": 42
}
```

**Validation Rules**:
- `attempt_id`: required, exists in attempts table
- `started_at` must not be null
- `ended_at` must be null (not already stopped)

**Success Response** (200):
```json
{
  "success": true,
  "data": {
    "attempt_id": 42,
    "ended_at": "2024-01-15T14:31:23.500000Z",
    "duration_ms": 83500
  }
}
```

**Error Response** (422):
```json
{
  "success": false,
  "error": "Attempt has already been stopped."
}
```

---

#### Save Attempt Results
Save poured volumes for all measures in an attempt.

**Endpoint**: `POST /api/attempt/results`

**Authentication**: Required (referee)

**Request Body**:
```json
{
  "attempt_id": 42,
  "results": [
    {
      "measure_id": 10,
      "poured_ml": 49.75
    },
    {
      "measure_id": 11,
      "poured_ml": 25.10
    }
  ]
}
```

**Validation Rules**:
- `attempt_id`: required, exists in attempts table
- `results`: required, array
- `results.*.measure_id`: required, exists in measures table
- `results.*.poured_ml`: required, numeric, decimal(6,2), min:0, max:9999.99
- All measures for the stage must have results

**Success Response** (201):
```json
{
  "success": true,
  "data": {
    "attempt_id": 42,
    "results_count": 2,
    "score": {
      "accuracy": 0.985,
      "time_points": 42.5,
      "accuracy_points": 35.0,
      "total": 77.5
    }
  }
}
```

**Error Response** (422):
```json
{
  "success": false,
  "error": "Missing results for some measures",
  "errors": {
    "results": ["Results must include all measures for this stage"]
  }
}
```

---

### Live Display (Public)

#### Get Live Competition Data
Fetch current state of competition for live display (polled every 1-2 seconds).

**Endpoint**: `GET /api/display/competition/{id}/live`

**Authentication**: Not required (public)

**URL Parameters**:
- `id`: Competition ID

**Success Response** (200):
```json
{
  "success": true,
  "data": {
    "competition": {
      "id": 1,
      "name": "London Pour Test 2024",
      "status": "active"
    },
    "current_attempt": {
      "id": 42,
      "competitor": {
        "id": 5,
        "name": "John Smith",
        "country": "United Kingdom",
        "bar_name": "The Cocktail Club"
      },
      "stage": {
        "id": 2,
        "name": "Stage 2",
        "order": 2
      },
      "started_at": "2024-01-15T14:30:00.000000Z",
      "ended_at": null,
      "elapsed_ms": 23500,
      "measures": [
        {
          "id": 10,
          "target_ml": 50.00,
          "poured_ml": 49.75,
          "order": 1
        },
        {
          "id": 11,
          "target_ml": 25.00,
          "poured_ml": null,
          "order": 2
        }
      ]
    }
  }
}
```

**Response when no active attempt**:
```json
{
  "success": true,
  "data": {
    "competition": {
      "id": 1,
      "name": "London Pour Test 2024",
      "status": "active"
    },
    "current_attempt": null
  }
}
```

---

#### Get Leaderboard
Fetch current leaderboard for a competition (polled every 2-3 seconds).

**Endpoint**: `GET /api/display/competition/{id}/leaderboard`

**Authentication**: Not required (public)

**URL Parameters**:
- `id`: Competition ID

**Success Response** (200):
```json
{
  "success": true,
  "data": {
    "competition": {
      "id": 1,
      "name": "London Pour Test 2024",
      "date": "2024-01-15"
    },
    "leaderboard": [
      {
        "rank": 1,
        "competitor": {
          "id": 5,
          "name": "John Smith",
          "country": "United Kingdom",
          "bar_name": "The Cocktail Club",
          "instagram": "@johnsmith"
        },
        "score": 87.5,
        "time_points": 52.5,
        "accuracy_points": 35.0,
        "average_accuracy": 0.985,
        "total_time_ms": 125000
      },
      {
        "rank": 2,
        "competitor": {
          "id": 3,
          "name": "Jane Doe",
          "country": "France",
          "bar_name": "Le Bar",
          "instagram": null
        },
        "score": 79.2,
        "time_points": 45.0,
        "accuracy_points": 34.2,
        "average_accuracy": 0.9842,
        "total_time_ms": 135000
      }
    ],
    "updated_at": "2024-01-15T14:35:00.000000Z"
  }
}
```

---

### Competitor Management (Authenticated)

#### Search Competitors
Search and filter competitors (for assignment to competitions).

**Endpoint**: `GET /api/competitors/search`

**Authentication**: Required (referee)

**Query Parameters**:
- `q`: Search query (matches name)
- `country`: Filter by country
- `limit`: Results limit (default: 20, max: 100)

**Example**: `/api/competitors/search?q=john&country=United%20Kingdom&limit=10`

**Success Response** (200):
```json
{
  "success": true,
  "data": {
    "competitors": [
      {
        "id": 5,
        "name": "John Smith",
        "country": "United Kingdom",
        "bar_name": "The Cocktail Club",
        "instagram": "@johnsmith"
      },
      {
        "id": 8,
        "name": "Johnny Walker",
        "country": "United Kingdom",
        "bar_name": "Walker's Bar",
        "instagram": null
      }
    ],
    "total": 2
  }
}
```

---

#### Assign Competitor to Competition
Add a competitor to a competition.

**Endpoint**: `POST /api/competition/{id}/assign-competitor`

**Authentication**: Required (referee)

**URL Parameters**:
- `id`: Competition ID

**Request Body**:
```json
{
  "competitor_id": 5
}
```

**Validation Rules**:
- `competitor_id`: required, exists in competitors table
- Validation: Competitor not already assigned to this competition

**Success Response** (201):
```json
{
  "success": true,
  "data": {
    "competition_id": 1,
    "competitor_id": 5,
    "message": "Competitor assigned successfully"
  }
}
```

**Error Response** (422):
```json
{
  "success": false,
  "error": "Competitor already assigned to this competition"
}
```

---

#### Remove Competitor from Competition
Remove a competitor from a competition.

**Endpoint**: `DELETE /api/competition/{competitionId}/competitor/{competitorId}`

**Authentication**: Required (referee)

**URL Parameters**:
- `competitionId`: Competition ID
- `competitorId`: Competitor ID

**Success Response** (200):
```json
{
  "success": true,
  "message": "Competitor removed from competition"
}
```

---

## Polling Strategy

### Live Display Screen
**Endpoint**: `/api/display/competition/{id}/live`

**Polling Interval**: 1-2 seconds

**Client-Side JavaScript**:
```javascript
let pollInterval = 1500; // 1.5 seconds

function pollLiveData(competitionId) {
  fetch(`/api/display/competition/${competitionId}/live`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        updateLiveDisplay(data.data);
      }
    })
    .catch(error => console.error('Polling error:', error))
    .finally(() => {
      setTimeout(() => pollLiveData(competitionId), pollInterval);
    });
}
```

### Leaderboard Screen
**Endpoint**: `/api/display/competition/{id}/leaderboard`

**Polling Interval**: 2-3 seconds (less frequent than live display)

---

## Error Handling

### Validation Errors (422)
Return field-specific errors:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "poured_ml": [
      "The poured ml must be a number between 0 and 9999.99.",
      "The poured ml must not exceed 2 decimal places."
    ],
    "measure_id": [
      "The selected measure id is invalid."
    ]
  }
}
```

### Not Found (404)
```json
{
  "success": false,
  "error": "Competition not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "error": "Something went wrong. Please try again."
}
```
(Detailed error logged server-side)

---

## Rate Limiting

### Public Endpoints
- Limit: 60 requests per minute per IP
- Applied to: `/api/display/*` routes

### Authenticated Endpoints
- Limit: 120 requests per minute per user
- Applied to: `/api/attempt/*`, `/api/competitors/*`, `/api/competition/*`

**Rate Limit Headers**:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1610728800
```

**Rate Limit Exceeded Response** (429):
```json
{
  "success": false,
  "error": "Too many requests. Please try again later."
}
```

---

## CORS

CORS is **not** required for MVP (all requests from same origin).

For future external access:
- Configure in `config/cors.php`
- Allow specific origins only (never use `*`)

---

## API Versioning

Not implemented for MVP. All endpoints are v1 by default.

Future: Use URL versioning (`/api/v2/...`) if breaking changes needed.

---

## Notes

- All timestamps in ISO 8601 format (UTC)
- All decimal values use 2 decimal places
- Use CSRF token for all non-GET requests
- JSON responses always include `success` field
- Log all API errors for debugging
- Never expose sensitive data in error messages
