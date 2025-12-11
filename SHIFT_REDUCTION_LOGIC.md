# Documentation of Implemented Shift Reduction Logic

This document outlines the actual implemented logic for handling 12-hour and 8-hour shifts, based on a full analysis of the codebase.

## 1. Executive Summary

The system handles different shift durations through a flexible, data-driven approach. There is no hardcoded logic for "12-hour" or "8-hour" shifts. Instead, the system relies on properties stored in the database for each shift record. 

A special calculation logic is triggered by a `sub_type` field on the `Shift` model, which reduces calculated attendance values by a specific percentage. The reduction from a 12-hour to an 8-hour system is managed by creating a new shift with an 8-hour duration and flagging its `sub_type` as `'reduced'`.

## 2. Data Model: The `Shift` Table

The core of the implementation lies in the `app/Models/Master/Shift.php` model and its corresponding database table. The key fields are:

*   `in_time`, `out_time`: These fields define the start and end of the shift. The system calculates the duration dynamically from these values.
*   `getShiftHoursAttribute()`: An accessor on the `Shift` model that calculates the total shift duration in hours. This allows any duration (8, 12, or otherwise) to be configured directly in the database.
*   `sub_type`: An `enum` or `string` field that can be either `'regular'` or `'reduced'`. This is the primary flag that triggers different calculation logic.
*   `is_internal`: A boolean flag. The special reduction logic is skipped if this is set to `true`.

## 3. Core Business Logic: `AttendanceProcessingService.php`

All major calculations are handled in `app/Services/AttendanceProcessingService.php`. This service processes attendance data for each worker.

### 3.1. Main Processing Flow

1.  The service retrieves a worker's punches for a given day.
2.  It determines the worker's correct shift, accounting for any temporary `ShiftOverride` records.
3.  The `getShiftDetails()` method is called to fetch the shift's properties (start time, end time, duration, and `sub_type`).
4.  The service calculates initial values for `workHoursWithinShift`, `late_coming_minutes`, `early_going_minutes`, etc., based on the punch times versus the shift's defined start and end times.
5.  The attendance status (`P`, `H/D`, `A`) is determined by comparing the calculated `workHoursWithinShift` against the shift's total duration (`$shiftHours`).

### 3.2. The `processBy8HrsShift()` Method

This method is the final and most critical step in the calculation. Despite its name, it acts as a universal modifier for attendance data based on the shift's `sub_type`.

*   **Condition:** The logic inside this method only runs if the shift's `is_internal` property is `false`.
*   **Logic Branching:**
    *   If `shift_sub_type` is `'reduced'`: The method applies a **30.43% reduction** to key metrics (`total_work_hours`, `work_hours_within_shift`, `late_coming_minutes`, `early_going_minutes`, `grace_minutes`). This is done using the `reduceBy3043Percent()` helper function.
    *   If `shift_sub_type` is `'regular'`: The method applies a **33.33% reduction** (multiplies by 2/3) to the same metrics using the `reduceBy33Percent()` helper function.

This means that the final, processed attendance values that are stored and used for payroll are scaled down based on the type of shift the employee is assigned to.

## 4. How to Use the System

To transition a worker from a 12-hour shift to an 8-hour shift, the following steps are taken:

1.  **Create a New Shift:** A new record is created in the `shifts` table via the "Manage Shifts" interface.
    *   `in_time` and `out_time` are set to define an 8-hour duration (e.g., `09:00:00` to `17:00:00`).
    *   The `sub_type` for this new shift is set to **`reduced`**.
    *   The `is_internal` flag is set to **`false`**.
2.  **Assign Workers:** Workers are assigned to this new 8-hour shift policy.

When the `AttendanceProcessingService` runs, it will identify that these workers are on a `'reduced'` shift and automatically apply the 30.43% reduction to their final attendance calculations.
