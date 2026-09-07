# 📡 API Reference Specification

This document provides complete specification for all REST API endpoints exposed by **ArtisanConnect Backend**.

---

## 1. Conventions & Standards

### Base URL
- Local Development: `http://localhost:8000`
- Production: `https://artisanconnect-backend.onrender.com`

### Required Request Headers
For all JSON and authenticated endpoints:
```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <YOUR_SANCTUM_TOKEN>
```

### Standard Status Codes
- `200 OK`: Request succeeded.
- `201 Created`: Resource successfully created.
- `400 Bad Request`: Malformed payload or validation error.
- `401 Unauthorized`: Missing or invalid Bearer token.
- `403 Forbidden`: Authenticated user lacks required permissions (e.g. non-admin).
- `404 Not Found`: Target model or route does not exist.
- `422 Unprocessable Entity`: Form validation failure.

---

## 2. Authentication Endpoints

### Register Client
Create a standard client user account.

- **Endpoint**: `POST /api/auth/register/client`
- **Auth**: Public

```json
// Request Body
{
  "full_name": "Alice Mensah",
  "email": "alice@example.com",
  "phone": "0241111111",
  "password": "password123"
}
```

```json
// Response (201 Created)
{
  "user": {
    "id": 1,
    "name": "Alice Mensah",
    "email": "alice@example.com",
    "role": "client",
    "is_verified": false,
    "is_active": true
  },
  "token": "1|hG7s9..."
}
```

---

### Register Artisan
Create an artisan profile with trade specialization and initial hourly rate.

- **Endpoint**: `POST /api/auth/register/artisan`
- **Auth**: Public

```json
// Request Body
{
  "full_name": "Kwame Boateng",
  "email": "kwame@example.com",
  "phone": "0551111111",
  "password": "password123",
  "skill_category": "Plumbing",
  "hourly_rate": 150.00,
  "bio": "Certified plumber with 10 years of domestic and commercial experience."
}
```

```json
// Response (201 Created)
{
  "user": {
    "id": 2,
    "name": "Kwame Boateng",
    "email": "kwame@example.com",
    "role": "artisan",
    "category": "Plumbing",
    "hourly_rate": "150.00",
    "is_verified": false,
    "is_active": true
  },
  "token": "2|jK8f2..."
}
```

---

### Login (Email / Password)
Authenticate existing user and obtain Sanctum Bearer token.

- **Endpoint**: `POST /api/auth/login`
- **Auth**: Public

```json
// Request Body
{
  "email": "kwame@example.com",
  "password": "password123"
}
```

```json
// Response (200 OK)
{
  "user": {
    "id": 2,
    "name": "Kwame Boateng",
    "email": "kwame@example.com",
    "role": "artisan"
  },
  "token": "3|mN9p0..."
}
```

---

### Firebase OAuth Exchange
Authenticate via Firebase OAuth ID token (e.g. Google Sign-In on mobile).

- **Endpoint**: `POST /api/auth/firebase-login`
- **Auth**: Public

```json
// Request Body
{
  "id_token": "eyJhbGciOiJSUzI1NiIs..."
}
```

```json
// Response (200 OK)
{
  "user": {
    "id": 3,
    "name": "Kofi Mensah",
    "email": "kofi@gmail.com",
    "role": "client"
  },
  "token": "4|qR1s2..."
}
```

---

### Logout
Revoke current access token.

- **Endpoint**: `POST /api/auth/logout`
- **Auth**: `auth:sanctum`

```json
// Response (200 OK)
{
  "message": "Logged out successfully"
}
```

---

## 3. User & Profile Endpoints

### Get Authenticated User Profile
- **Endpoint**: `GET /api/user`
- **Auth**: `auth:sanctum`

---

### Update User Profile
Update bio, contact phone, or location coordinates.

- **Endpoint**: `PATCH /api/user`
- **Auth**: `auth:sanctum`

```json
// Request Body
{
  "name": "Kwame Boateng",
  "phone": "0559999999",
  "bio": "Updated bio statement.",
  "latitude": 5.6037,
  "longitude": -0.1870
}
```

---

## 4. Service Jobs Endpoints

### Browse Available Jobs
Lists open jobs. If `lat` and `lng` are provided, results include spherical `distance_in_km` sorted closest first.

- **Endpoint**: `GET /api/jobs`
- **Query Parameters**:
  - `lat` (float, optional): User's current latitude (e.g. `5.6037`)
  - `lng` (float, optional): User's current longitude (e.g. `-0.1870`)
- **Auth**: Public / Optional Sanctum

```json
// Response (200 OK)
[
  {
    "id": 10,
    "client_id": 1,
    "title": "Fix leaking bathroom pipe",
    "description": "Pipe joint under the sink has come loose.",
    "category": "Plumbing",
    "budget": "250.00",
    "location": "Osu, Accra",
    "status": "open",
    "latitude": "5.6000",
    "longitude": "-0.1800",
    "distance_in_km": 0.8,
    "client": {
      "id": 1,
      "name": "Alice Mensah"
    }
  }
]
```

---

### Post a New Job
Submit a job request.

- **Endpoint**: `POST /api/jobs`
- **Auth**: `auth:sanctum`
- **Content-Type**: `multipart/form-data` or `application/json`

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `title` | string | Yes | Job title (max 255 chars) |
| `description` | string | Yes | Detailed problem description |
| `category` | string | Yes | Trade category (e.g. `Plumbing`) |
| `budget` | numeric | Yes | Target budget in GHS |
| `location` | string | No | Human-readable address |
| `latitude` | numeric | No | Coordinate latitude |
| `longitude` | numeric | No | Coordinate longitude |
| `images[]` | file | No | Array of job site photos (max 5MB each) |

---

### Update Job Status
Progress job along lifecycle stages (`open` $\to$ `in_progress` $\to$ `completed` or `cancelled`).

- **Endpoint**: `PATCH /api/jobs/{serviceJob}/status`
- **Auth**: `auth:sanctum`

```json
// Request Body
{
  "status": "in_progress"
}
```

---

## 5. Applications & Bidding Endpoints

### Submit Proposal / Bid
Artisans apply to a posted service job.

- **Endpoint**: `POST /api/jobs/{serviceJob}/apply`
- **Auth**: `auth:sanctum` (Artisan role)

```json
// Request Body
{
  "proposal": "I have the replacement pipes in stock and can arrive within 30 minutes."
}
```

```json
// Response (201 Created)
{
  "id": 15,
  "service_job_id": 10,
  "artisan_id": 2,
  "status": "pending",
  "proposal": "I have the replacement pipes in stock..."
}
```

---

### Accept / Reject Proposal
Client accepts or declines an artisan's bid.

- **Endpoint**: `PATCH /api/applications/{application}/status`
- **Auth**: `auth:sanctum`

```json
// Request Body
{
  "status": "accepted"
}
```

---

## 6. Ratings & Reviews

### Submit Review for Job
Submit 1-to-5 star rating and comment after a job is completed.

- **Endpoint**: `POST /api/jobs/{serviceJob}/reviews`
- **Auth**: `auth:sanctum`

```json
// Request Body
{
  "artisan_id": 2,
  "rating": 5,
  "comment": "Exceptional service! Arrived on time and resolved the plumbing leak in under an hour."
}
```

---

### Get Artisan Reviews
- **Endpoint**: `GET /api/reviews/artisan/{artisanId}`
- **Auth**: Public

---

## 7. In-App Chat & Messages

### Get Active Conversations
Returns list of unique users with whom the authenticated user has exchanged messages.

- **Endpoint**: `GET /api/messages/conversations`
- **Auth**: `auth:sanctum`

---

### Send Message
- **Endpoint**: `POST /api/messages/{user}`
- **Auth**: `auth:sanctum`

```json
// Request Body
{
  "message": "Hello Kwame, I accepted your proposal. Please call me when you arrive."
}
```

---

## 8. Admin REST Endpoints (`auth:sanctum` + `admin`)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/admin/dashboard` | JSON payload of summary KPIs, 6-month trends, and recent feed |
| `GET` | `/api/admin/users` | List users with query filters: `role`, `verified`, `active`, `search` |
| `POST` | `/api/admin/users/{user}/verify` | Payload: `{"is_verified": 1, "verification_notes": "..."}` |
| `POST` | `/api/admin/users/{user}/toggle-active` | Toggles user account active/suspended state |
| `GET` | `/api/admin/jobs` | Query filters: `status`, `category`, `search` |
| `PATCH`| `/api/admin/jobs/{job}/status` | Dispute status override: `open`, `in_progress`, `completed`, `cancelled` |
