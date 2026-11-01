<?php

namespace App\Contexts\Learning\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\CourseCertificate;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Notifications\FeedEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared administrative course-enrollment transitions. */
final class AdminLearningCommands extends WorkflowHandler
{
    public function completeEnrollment(WorkflowInput $input, CourseEnrollment $enrollment): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);

        [$record, $certificate] = DB::transaction(function () use ($enrollment, $actor) {
            $record = CourseEnrollment::query()->lockForUpdate()->findOrFail($enrollment->id);
            $transitioned = $record->status !== 'completed';
            if ($transitioned) {
                if (!in_array($record->status, ['enrolled', 'in_progress'], true)) {
                    throw ValidationException::withMessages(['status' => 'This enrollment cannot be completed from its current state.']);
                }
                $record->update(['status' => 'completed', 'completed_at' => now()]);
                DomainOutbox::append('event', 'learning.enrollment_completed.v1', ['references' => [['resource' => 'CourseEnrollment', 'id' => $record->id]]], $actor->id);
            }

            $certificate = CourseCertificate::query()->firstOrCreate(
                ['enrollment_id' => $record->id],
                ['user_id' => $record->user_id, 'course_id' => $record->course_id],
            );
            if ($certificate->wasRecentlyCreated) {
                $certificate->loadMissing('user');
                if ($certificate->user) {
                    DomainMail::to($certificate->user)->queue(new \App\Mail\CourseCertificateIssued($certificate));
                    $notification = new FeedEntry('learning.certificate', 'Course certificate issued', 'Your course certificate is ready.', 'academic-cap', null);
                    DomainOutbox::append('notification', get_class($notification), [
                        'recipient' => \App\Contexts\Shared\Infrastructure\MessageValueCodec::encode($certificate->user),
                        'message' => \App\Contexts\Shared\Infrastructure\MessageValueCodec::message($notification),
                    ], $actor->id);
                }
                DomainOutbox::append('event', 'learning.course_certificate_issued.v1', ['references' => [['resource' => 'CourseCertificate', 'id' => $certificate->id]]], $actor->id);
            }

            return [$record, $certificate];
        }, 3);

        return WorkflowResult::payload([
            'id' => $record->id,
            'status' => $record->status,
            'completed_at' => $record->completed_at?->toISOString(),
            'certificate_id' => $certificate->id,
            'certificate_number' => $certificate->certificate_number,
        ])->with('success', 'Enrollment completed and certificate prepared.');
    }
}
