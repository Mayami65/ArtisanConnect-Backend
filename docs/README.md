# 📚 ArtisanConnect Backend Documentation

Welcome to the technical documentation for **ArtisanConnect Backend**. This documentation suite is designed for engineers, system administrators, mobile developers, and DevOps teams building, integrating, and operating the ArtisanConnect marketplace platform.

---

## 🧭 Documentation Index

| Document | Description | Target Audience |
| :--- | :--- | :--- |
| **[Architecture & System Design](./architecture.md)** | Core architecture, ER diagrams, data flow, authentication model, and geospatial matching logic. | Backend Engineers, System Architects |
| **[API Reference Specification](./api_reference.md)** | Exhaustive endpoint documentation with payloads, query params, headers, and responses. | Mobile Engineers (Flutter), API Integrators |
| **[Admin Operations Handbook](./admin_guide.md)** | Operating the Web Admin portal, artisan KYC verification workflows, dispute resolution, and trade configuration. | Platform Administrators, Operations Team |
| **[Deployment & DevOps Guide](./deployment_and_operations.md)** | Production environment setup, Docker, Render deployment, Supabase connection pooling, and maintenance. | DevOps Engineers, Infrastructure Leads |

---

## ⚡ Quick Links & Interactive Tools

- **Local Web Admin Portal**: `http://localhost:8000/admin`
- **Interactive OpenAPI / Swagger Documentation**: `http://localhost:8000/docs/api` (powered by Scramble)
- **Health Check Endpoint**: `http://localhost:8000/up`

---

## 🛠️ Tech Stack at a Glance

- **Core Engine**: Laravel 12.x on PHP 8.3+
- **Database**: PostgreSQL with Supabase Pooler (`aws-0-eu-central-1.pooler.supabase.com`)
- **Authentication**: Laravel Sanctum (Bearer Token) & Firebase Admin SDK (ID Token exchange)
- **Real-Time Communications**: Firebase Cloud Messaging & database chat messaging
- **Admin UI**: Server-rendered Blade with Tailwind CSS and Chart.js
- **Test Suite**: PHPUnit 12.x with SQLite in-memory testing
