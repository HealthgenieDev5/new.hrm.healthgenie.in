# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 11 (PHP ^8.2) HRM system for HealthGenie's manufacturing workforce: worker master data, machine attendance ingestion/processing, salary + incentive calculation, tenure bonuses, production/line planning, and reporting. UI is Blade + Tailwind + Alpine.js (+ Bootstrap 5 pagination, Yajra DataTables), PDFs via dompdf, Excel via maatwebsite/excel. Auth scaffolding is Laravel Breeze.

## Commands

- Dev (server + queue listener + Vite): `composer run dev`
- Server only: `php artisan serve`; assets: `npm run dev` / `npm run build`
- Tests (Pest): `php artisan test`; single file/test: `php artisan test tests/Feature/ProfileTest.php` or `php artisan test --filter="test name"`
- Style: `./vendor/bin/pint`
- Scheduler (needed locally to exercise scheduled jobs): `php artisan schedule:work`

Tests: phpunit.xml sets array cache/session and sync queue but the sqlite DB lines are **commented out**, so tests use whatever DB `.env` points at (MySQL in this repo's `.env`). Don't run the suite against a database with real data — enable the sqlite/`:memory:` lines in phpunit.xml first. Only the Breeze auth/profile tests exist.

### Artisan commands (app/Console/Commands)

- `attendance:fetch [--month=YYYY-MM] [--worker=CODE]` — pull punches from the machine APIs
- `attendance:process [--month=YYYY-MM] [--workers=A,B]` — raw `attendances` → `processed_attendances`
- `attendance:process-worker {code} [--month=]` — single worker
- `salary:process [--month=YYYY-MM] [--workers=A,B]`
- `bonus:process` (tenure bonuses), `mail:send-absenteeism-report`, `shiftoverride:bulk`, `process:dashboard`

Schedule is defined in `routes/console.php` (Laravel 11 style, no Kernel): `attendance:fetch` every 15 min, `attendance:process` every 30 min, `bonus:process` daily 01:00, absenteeism report daily 12:00. (The comment above the fetch schedule says "3 minutes" — the code is 15.)

## Architecture

### Request flow & wiring
- `bootstrap/app.php` registers only `web` + `commands` routing. **`routes/api.php` is not loaded** (no `api:` entry), so the `/call-logs/batch` endpoint (`Api\CallLogBatchController`, `LogNazrulApiRequests` middleware) is currently unreachable unless that is changed.
- `routes/web.php` `require`s per-module files: `auth`, `attendance`, `overrides`, `master`, `reports`, `settings`, `planning` (note `routes/worker.php` exists but is not required from web.php — check before assuming worker routes live there).
- Route middleware aliases: `permission:<Name>` and `role:<Name>`. A global `CheckPspActive` is appended to the `web` group.
- `app/Providers/InterfacesServiceProvider` binds `WorkerServiceInterface`, `SalaryProcessingServiceInterface`, `WorkerAttendanceRepositoryInterface`, `ProductionPlanningInterface` to their implementations — type-hint the interfaces in controllers.
- `ObserverServiceProvider` attaches `AttendanceFilterObserver` to master models (Psp, Shift, Workertype, WorkingArea, Designation, …) to bust the `attendance_filter_data` cache via `AttendanceCacheHelper`. When adding a master model that feeds attendance filters, register it there.

### Attendance pipeline (the core of the app)
1. `Services/AttendanceApi/*` (`AttendanceApiMachineOne/Two/Three`, `HeadOfficeMachine`) call vendor attendance-machine HTTP APIs (credentials in `config/services.php`) and store raw punches into `Attendance`. `attendance:fetch` orchestrates all machines.
2. `AttendanceProcessingService` turns raw punches into `ProcessedAttendance` rows per worker/day: resolves shift (base `Shift` + temporary `ShiftOverride`, plus `ManualPunching`), computes work hours, late/early minutes, status (`P`, `H/D`, `A`), etc.
3. `AttendanceSummaryService`, `WorkerAttendanceService` (+ `ServiceHelpers/`), `AttendancePdfService` and `Controllers/Reports/*` read processed data for dashboards/reports/PDFs.
4. `SalaryProcessingService` consumes processed attendance to write `ProcessedSalary` / `ProcessedSalaryIncentive`; `TenureBonusService` handles `TenureBonusRule` / `WorkerTenureBonus`.

**Shift reduction:** there is no hardcoded 12h/8h logic. `Shift.sub_type` (`regular` | `reduced`) and `is_internal` drive `AttendanceProcessingService::processBy8HrsShift()`, which scales hours/late/early/grace by 2/3 (regular) or 30.43% reduction (reduced) unless the shift is internal. Read `SHIFT_REDUCTION_LOGIC.md` before touching this.

### Authorization model
- Custom RBAC (not Spatie): `User` ↔ `Role` ↔ `Permission`; `User::hasRole()` / `hasPermission()` compare names case-insensitively via `Str::title`. Super admin = user id `SUPER_ADMIN_ID` (env, default 1) or role "Super Admin". Policies in `app/Policies` per master entity. See `AUTHENTICATION.md` for detail.
- **PSP** (production supervisor) is a user-linked entity (`User::psp()`). Inactive PSPs are logged out by `CheckPspActive` and blocked in `LoginRequest`.
- `Worker` has a global `WorkerVisibilityScope`: roles Super Admin/HR/Admin/Hr Executive see all workers, `PSP` sees only workers with their `psp_id`, everyone else — and unauthenticated — sees none. The scope is **skipped when running in console**, so commands/tinker see all workers while web requests don't; use `withoutGlobalScopes()` when a web path legitimately needs unscoped workers (e.g. reports for other roles).
- `Revisionable` trait writes old/new values of changed attributes to `revisions` on model update — add it to models needing an audit trail.

### Layout notes
- Controllers are grouped by module (`Master/`, `Reports/`, `Overrides/`, `Planning/`, `Api/`); datatable-backed lists often have a dedicated `*DatatableService` in `app/Services`.
- Worker queries live in `WorkerRepository` / `Models/Traits/WorkerScopes`.

## Repo hygiene gotchas

The tree is full of manual backups that are not live code: `*.bkp*`, `*_bkp_*`, and whole copies like `app_2025_08_25/`, `app_bkp2026*/`, `routes_*`, `resources_*`, `database_*`. Edit only the files under `app/`, `routes/`, `resources/`, `database/`, and exclude the backup directories/suffixes from searches. Backups inside `app/` have non-`.php` endings, so they are never autoloaded. `new_hrm_healthgenie_in.sql` and `workers_import.csv` at the root are data dumps.
