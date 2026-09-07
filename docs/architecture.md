# 🏛️ System Architecture & Design

This document details the software architecture, data modeling, authentication lifecycle, and technical decisions underpinning the **ArtisanConnect Backend**.

---

## 1. High-Level Architecture

ArtisanConnect is structured as a **modular monolith** serving two distinct client interfaces:
1. **Flutter Mobile Application**: Consumes JSON REST APIs over HTTPS, authenticated via personal access tokens (`Bearer` via Laravel Sanctum) or Firebase Auth tokens.
2. **Platform Web Admin Portal**: Server-rendered Laravel Blade interface styled with Tailwind CSS and Chart.js, secured by session-based authentication and role-checking middleware.

```mermaid
graph TD
    subgraph Clients ["Client Surfaces"]
        FlutterClient["Flutter Mobile App (Client Mode)"]
        FlutterArtisan["Flutter Mobile App (Artisan Mode)"]
        AdminBrowser["Desktop Web Browser (Admin Portal)"]
    end

    subgraph GatewayLayer ["Routing & Security Layer"]
        RateLimiter["Rate Limiting & CORS"]
        ApiAuth["Sanctum / Bearer Token Middleware"]
        WebAuth["Session Guard & CSRF Protection"]
        AdminMiddleware["AdminMiddleware ('role === admin')"]
    end

    subgraph Controllers ["Controllers & Application Layer"]
        PublicControllers["Public & Auth Controllers"]
        AppControllers["Mobile API Controllers<br/>(Jobs, Bids, Chat, Reviews)"]
        AdminControllers["Admin Controllers<br/>(Dashboard, Users, Jobs, Categories, Reviews)"]
        MetricsService["AdminMetricsService (Analytics Engine)"]
    end

    subgraph DataLayer ["Persistence & External Services"]
        Postgres[(PostgreSQL / Supabase)]
        Firebase["Firebase Auth & Credentials"]
        Storage["Local / Cloud Storage (Job Images)"]
    end

    FlutterClient & FlutterArtisan --> RateLimiter
    AdminBrowser --> WebAuth

    RateLimiter --> ApiAuth
    ApiAuth --> AppControllers
    RateLimiter --> PublicControllers
    
    WebAuth --> AdminMiddleware
    AdminMiddleware --> AdminControllers
    AdminControllers --> MetricsService

    AppControllers & AdminControllers --> Postgres
    PublicControllers --> Firebase
    AppControllers --> Storage
```

---

## 2. Entity-Relationship (ER) Diagram

```mermaid
erDiagram
    USERS ||--o{ SERVICE_JOBS : "posts (as client)"
    USERS ||--o{ APPLICATIONS : "submits (as artisan)"
    USERS ||--o{ REVIEWS : "receives / gives"
    USERS ||--o{ MESSAGES : "sends / receives"
    
    SERVICE_JOBS ||--o{ APPLICATIONS : "receives"
    SERVICE_JOBS ||--o{ REVIEWS : "generates"
    SERVICE_JOBS }o--|| CATEGORIES : "categorized by"

    USERS {
        bigint id PK
        string name
        string email UK
        string phone
        string role "client | artisan | admin"
        boolean is_verified "KYC verified status"
        boolean is_active "account active status"
        string category "Artisan primary trade"
        decimal hourly_rate
        text bio
        text verification_notes "Admin audit log"
        decimal latitude
        decimal longitude
        string password
        timestamp email_verified_at
        timestamps created_at_updated_at
    }

    SERVICE_JOBS {
        bigint id PK
        bigint client_id FK
        string title
        text description
        string category
        decimal budget
        string location
        string status "open | in_progress | completed | cancelled"
        json images "array of image paths"
        decimal latitude
        decimal longitude
        timestamp deleted_at "SoftDeletes"
        timestamps created_at_updated_at
    }

    APPLICATIONS {
        bigint id PK
        bigint service_job_id FK
        bigint artisan_id FK
        string status "pending | accepted | rejected"
        text proposal
        timestamps created_at_updated_at
    }

    CATEGORIES {
        bigint id PK
        string name UK
        string icon_name "Flutter Icons identifier"
        string color_hex "Theme accent (#RRGGBB)"
        timestamps created_at_updated_at
    }

    REVIEWS {
        bigint id PK
        bigint service_job_id FK
        bigint client_id FK
        bigint artisan_id FK
        int rating "1 to 5"
        text comment
        timestamps created_at_updated_at
    }

    MESSAGES {
        bigint id PK
        bigint sender_id FK
        bigint receiver_id FK
        text message
        boolean is_read
        timestamps created_at_updated_at
    }
```

---

## 3. Authentication & Authorization Lifecycles

### A. Mobile Client Authentication
- **Sanctum Token Authentication**: Standard register/login endpoints return an API Bearer token (`auth_token`). The Flutter application includes this header: `Authorization: Bearer <token>`.
- **Firebase Authentication Exchange**: Artisans or clients who sign in via Google/Apple Auth in Flutter send their Firebase ID token to `POST /api/auth/firebase-login`. The backend verifies the token with the Firebase Admin SDK and issues a Sanctum personal access token.

### B. Administrator Authorization
- All `/admin/*` routes are protected by `App\Http\Middleware\AdminMiddleware`.
- The middleware enforces:
  1. User must be authenticated (`Auth::check()`).
  2. User must possess role `admin` (`$user->role === 'admin'`).
  3. User must not be deactivated (`$user->is_active === true`).
- Unauthorized web visits are redirected to `/admin/login` with flash error messages. API calls return `403 Forbidden`.

---

## 4. Geospatial Matching Engine (Haversine Formula)

When clients or artisans browse jobs with latitude and longitude query parameters (`/api/jobs?lat=5.6037&lng=-0.1870`), the backend computes the exact spherical distance between the user's coordinates and the job's coordinates using the **Haversine formula**:

$$\Delta\sigma = 2 \arcsin \sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos\phi_1 \cos\phi_2 \sin^2\left(\frac{\Delta\lambda}{2}\right)}$$

$$d = R \cdot \Delta\sigma \quad (R = 6,371\text{ km})$$

This calculation is implemented in `ServiceJobController.php` and dynamically attaches a `distance_in_km` attribute to each job, sorting the results ascending so users see the closest jobs first.

---

## 5. Soft Deletes & Data Integrity

Service jobs implement `Illuminate\Database\Eloquent\SoftDeletes`. When a job is cancelled or removed by a client, the record is soft-deleted (`deleted_at` timestamp populated), preserving historical audit trails, accounting records, and associated reviews.
