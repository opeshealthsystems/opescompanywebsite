<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\AttendanceRecord;
use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminWorkforceRecordCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); return $u; }
    private function attendanceValues(WorkflowInput $input): array
    {
        $data = $input->validate(['user_id' => 'required|integer|exists:users,id', 'date' => 'required|date_format:Y-m-d', 'status' => 'required|in:present,absent,late,half_day,on_leave,remote,holiday',
            'check_in' => 'sometimes|nullable|date_format:H:i', 'check_out' => 'sometimes|nullable|date_format:H:i', 'notes' => 'sometimes|nullable|string|max:5000', 'hours_worked' => 'prohibited', 'id' => 'prohibited']);
        $start = $data['check_in'] ?? null; $end = $data['check_out'] ?? null;
        if ($start && $end && strcmp($end, $start) < 0) throw ValidationException::withMessages(['check_out' => 'Check-out must be at or after check-in.']);
        $data['hours_worked'] = $start && $end ? ((int) substr($end, 0, 2) * 60 + (int) substr($end, 3, 2)) - ((int) substr($start, 0, 2) * 60 + (int) substr($start, 3, 2)) : 0;
        return $data;
    }
    private function assertAttendanceUnique(array $data, ?int $ignore = null): void
    {
        $q = AttendanceRecord::query()->where('user_id', $data['user_id'])->whereDate('date', $data['date']);
        if ($ignore !== null) $q->where('id', '!=', $ignore);
        if ($q->exists()) throw ValidationException::withMessages(['date' => 'An attendance record already exists for this employee and date.']);
    }
    private function timesheetValues(WorkflowInput $input): array
    {
        $data = $input->validate(['user_id' => 'required|integer|exists:users,id', 'project_id' => 'sometimes|nullable|integer|exists:projects,id', 'date' => 'required|date_format:Y-m-d',
            'hours' => 'required|numeric|decimal:2|min:0.25|max:24|multiple_of:0.25', 'description' => 'sometimes|nullable|string|max:500', 'is_billable' => 'required|boolean', 'id' => 'prohibited']);
        if (!empty($data['project_id']) && !Project::query()->whereKey($data['project_id'])->whereIn('status', ['active', 'planning'])->exists()) throw ValidationException::withMessages(['project_id' => 'Choose an active or planning project.']);
        return $data;
    }
    public function createAttendance(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->attendanceValues($input);
        $r = DB::transaction(function () use ($data, $actor) { $this->assertAttendanceUnique($data); $r = AttendanceRecord::create($data); DomainOutbox::append('event', 'workforce.attendance_record_created.v1', ['references' => [['resource' => 'AttendanceRecord', 'id' => $r->id]]], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id], 201);
    }
    public function updateAttendance(WorkflowInput $input, AttendanceRecord $attendanceRecord): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->attendanceValues($input);
        $r = DB::transaction(function () use ($attendanceRecord, $data, $actor) { $r = AttendanceRecord::query()->lockForUpdate()->findOrFail($attendanceRecord->id); $this->assertAttendanceUnique($data, $r->id); $r->update($data); DomainOutbox::append('event', 'workforce.attendance_record_updated.v1', ['references' => [['resource' => 'AttendanceRecord', 'id' => $r->id]], 'changed_fields' => array_keys($data)], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id]);
    }
    public function deleteAttendance(WorkflowInput $input, AttendanceRecord $attendanceRecord): WorkflowResult
    {
        $actor = $this->admin($input); $id = DB::transaction(function () use ($attendanceRecord, $actor) { $r = AttendanceRecord::query()->lockForUpdate()->findOrFail($attendanceRecord->id); $id = $r->id; $r->delete(); DomainOutbox::append('event', 'workforce.attendance_record_deleted.v1', ['references' => [['resource' => 'AttendanceRecord', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
    public function createTimesheet(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->timesheetValues($input);
        $r = DB::transaction(function () use ($data, $actor) { $r = Timesheet::create($data); DomainOutbox::append('event', 'workforce.timesheet_created.v1', ['references' => [['resource' => 'Timesheet', 'id' => $r->id]]], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id], 201);
    }
    public function updateTimesheet(WorkflowInput $input, Timesheet $timesheet): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->timesheetValues($input);
        $r = DB::transaction(function () use ($timesheet, $data, $actor) { $r = Timesheet::query()->lockForUpdate()->findOrFail($timesheet->id); $r->update($data); DomainOutbox::append('event', 'workforce.timesheet_updated.v1', ['references' => [['resource' => 'Timesheet', 'id' => $r->id]], 'changed_fields' => array_keys($data)], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id]);
    }
    public function deleteTimesheet(WorkflowInput $input, Timesheet $timesheet): WorkflowResult
    {
        $actor = $this->admin($input); $id = DB::transaction(function () use ($timesheet, $actor) { $r = Timesheet::query()->lockForUpdate()->findOrFail($timesheet->id); $id = $r->id; $r->delete(); DomainOutbox::append('event', 'workforce.timesheet_deleted.v1', ['references' => [['resource' => 'Timesheet', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}
