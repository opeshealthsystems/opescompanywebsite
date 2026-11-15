# EMR Demo Onboarding Form — Design

**Date:** 2026-11-15
**Status:** Approved (build)

## Purpose

When a hospital/clinic requests the EMR demo, OPES needs to collect the facility's
details and the full list of staff who will access the system, so the team can
manually create each account in the separate EMR SaaS platform. This feature captures
that intake and gives the team a place to receive, review, and track it.

It is **distinct** from the existing `DemoRequest` (a lightweight CRM *lead*). This is
the richer operational intake, including a full staff roster with a per-person
"takes tasks" flag.

## Non-goals (YAGNI)

- No account provisioning in our app — EMR accounts are created manually on the
  separate EMR SaaS. We only record + track.
- Not wired into the API-first workflow registry / OpenAPI contract. This is an
  internal operational form, not part of the public API product surface, so it stays
  isolated from `config/business_workflows.php` and the contract CI.

## Architecture

Standard Laravel + Filament, isolated from the contract registry:

- **Public bilingual form** (EN/FR) at `/{locale}/emr-onboarding` — the hospital fills
  facility info, primary contact, demo scope, and a dynamic staff list.
- **Admin Filament resource** ("EMR Onboarding", CRM group) — the team receives,
  reviews, edits, and tracks status; ticks off each staff member as EMR accounts are
  created.
- Admins get an in-app notification on each new submission (mirrors `DemoRequestCommands`).

## Data model

`hospital_onboardings`
: facility (name, type, size, country, city, address, phone, email, website,
  registration no.), primary contact (name, title, phone, email), scope (products[],
  departments, preferred_start_date, notes), plus locale, status, admin_notes, ip.
  Status: `new → reviewed → accounts_created → completed`.

`onboarding_staff` (belongs to onboarding, cascade delete)
: full_name, job_role, department, phone, email, access_level,
  **takes_tasks** (bool, from requester), **emr_account_created** (bool, admin),
  emr_username (admin), notes.

Facility type/size reuse `DemoRequest::$institutionTypes` / `$sizes` for consistency.

## Components

| Unit | Responsibility | Depends on |
|------|----------------|------------|
| Migration | Two tables | — |
| `HospitalOnboarding` model | Facility record + `staff` hasMany + option arrays | `DemoRequest` option arrays |
| `OnboardingStaff` model | Staff row + option arrays | `HospitalOnboarding` |
| `EmrOnboardingController` | Public show/submit (validate, persist, notify) | models, User roles |
| `emr-onboarding.blade.php` | Bilingual public form + staff repeater (JS) | layout, option arrays |
| `HospitalOnboardingResource` | Admin CRUD, staff repeater, status + EMR checkmarks | models |

## Testing

Feature tests: public page renders (EN/FR); submit creates onboarding + staff with
`takes_tasks` captured; empty staff rows ignored; validation errors on missing required
fields; relationship cascade. Run on SQLite (project default).
