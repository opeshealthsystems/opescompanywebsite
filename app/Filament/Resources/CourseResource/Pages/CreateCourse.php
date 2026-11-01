<?php
namespace App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
class CreateCourse extends CreateRecord {
    protected static string $resource = CourseResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        return \App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter::record('admin.learning.courses.create', $data);
    }
}
