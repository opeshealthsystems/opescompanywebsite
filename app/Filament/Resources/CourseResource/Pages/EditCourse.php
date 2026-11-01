<?php
namespace App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
class EditCourse extends EditRecord {
    protected static string $resource = CourseResource::class;
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.learning.courses.update', $data, ['course' => $record->id]);
    }
}
