<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\AttendanceRecord;
use App\Models\Timesheet;
use Illuminate\Database\Eloquent\Builder;

final class AdminWorkforceRecordQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function attendance(AttendanceRecord $r): array { return ['id' => $r->id, 'user_id' => $r->user_id, 'employee_name' => $r->employee?->name, 'date' => $r->date?->toDateString(), 'check_in' => $r->check_in, 'check_out' => $r->check_out, 'status' => $r->status, 'hours_worked' => $r->hours_worked, 'notes' => $r->notes]; }
    private function timesheet(Timesheet $r): array { return ['id' => $r->id, 'user_id' => $r->user_id, 'user_name' => $r->user?->name, 'project_id' => $r->project_id, 'project_title' => $r->project?->title, 'date' => $r->date?->toDateString(), 'hours' => $r->hours, 'description' => $r->description, 'is_billable' => (bool) $r->is_billable]; }
    private function page($p, callable $map): array { return ['items' => $p->getCollection()->map($map)->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]; }
    public function attendanceQuery(WorkflowInput $input): Builder { $this->admin($input); return AttendanceRecord::query()->with('employee')->orderByDesc('date'); }
    public function timesheetQuery(WorkflowInput $input): Builder { $this->admin($input); return Timesheet::query()->with(['user', 'project'])->orderByDesc('date'); }
    public function attendanceIndex(WorkflowInput $input): WorkflowResult { return WorkflowResult::payload($this->page($input->paginate($this->attendanceQuery($input)), fn ($r) => $this->attendance($r))); }
    public function attendanceShow(WorkflowInput $input, AttendanceRecord $attendanceRecord): WorkflowResult { $this->admin($input); $attendanceRecord->loadMissing('employee'); return WorkflowResult::payload(['attendance_record' => $this->attendance($attendanceRecord)]); }
    public function timesheetIndex(WorkflowInput $input): WorkflowResult { return WorkflowResult::payload($this->page($input->paginate($this->timesheetQuery($input)), fn ($r) => $this->timesheet($r))); }
    public function timesheetShow(WorkflowInput $input, Timesheet $timesheet): WorkflowResult { $this->admin($input); $timesheet->loadMissing(['user', 'project']); return WorkflowResult::payload(['timesheet' => $this->timesheet($timesheet)]); }
}
