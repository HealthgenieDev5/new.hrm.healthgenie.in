# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel-based Human Resource Management (HRM) system for HealthGenie. The application manages worker data, attendance tracking, salary processing, production planning, and various HR operations for manufacturing environments.

## Development Commands

- **Start development server**: `composer run dev` (runs Laravel server, queue listener, and Vite concurrently)
- **Laravel server only**: `php artisan serve`
- **Build frontend assets**: `npm run build`
- **Watch frontend assets**: `npm run dev`
- **Run tests**: `php artisan test` (using Pest PHP)
- **Code style**: `./vendor/bin/pint` (Laravel Pint)
- **Database migrations**: `php artisan migrate`
- **Queue processing**: `php artisan queue:listen`

## Architecture & Structure

### Core Laravel Structure
- **Entry point**: `public/index.php` - Laravel application entry
- **Main routes**: `routes/web.php` - Primary web routes with module includes
- **Dashboard**: `app/Http/Controllers/DashboardController.php` - Main dashboard logic

### Key Directories
- `app/Http/Controllers/` - Controllers organized by module:
  - `AttendanceController.php` - Attendance management
  - `WorkerController.php` - Worker/employee management
  - `Master/` - Master data controllers (designations, lines, etc.)
  - `Reports/` - Reporting functionality
  - `Overrides/` - Shift and manual punching overrides
- `app/Models/` - Eloquent models for all business entities
- `app/Services/` - Business logic services:
  - `AttendanceProcessingService.php` - Core attendance processing
  - `SalaryProcessingService.php` - Salary calculations
  - `WorkerService.php` - Worker-related operations
- `app/Repositories/` - Data access layer
- `resources/views/` - Blade templates organized by feature
- `database/migrations/` - Database schema definitions

### Key Models & Business Logic
- **Worker**: Core employee model with relationships to shifts, attendance, salary
- **Attendance**: Tracks worker check-ins/check-outs with processing logic
- **ProcessedSalary**: Calculated salary data with incentives
- **Planning**: Production planning and line assignments
- **Shift**: Shift definitions and overrides

### Authentication & Authorization
- Role-based access control with custom middleware
- Permissions system integrated with User model
- Super admin functionality (user ID 1 or SUPER_ADMIN_ID env)

### Frontend Technology
- Laravel Blade templates with Tailwind CSS
- Vite for asset compilation
- Alpine.js for interactive components
- DataTables for complex data grids

### Background Processing
- Queue-based attendance processing (`ProcessAttendance` command)
- Salary processing commands (`ProcessSalariesCommand`)
- Automated data fetching from attendance machines

### Testing
- Pest PHP testing framework
- PHPUnit XML configuration available