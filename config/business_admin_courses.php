<?php

$input = ['name' => 'workflowInput', 'kind' => 'input'];
$course = ['name' => 'course', 'kind' => 'model', 'class' => 'App\\Models\\Course', 'key' => 'id'];
$lesson = ['name' => 'lesson', 'kind' => 'model', 'class' => 'App\\Models\\CourseLesson', 'key' => 'id'];
$commands = 'App\\Contexts\\Learning\\Application\\Commands\\AdminCourseCommands';
$queries = 'App\\Contexts\\Learning\\Application\\Queries\\AdminCourseQueries';
$def = fn (string $handler, string $method, array $parameters, bool $command, string $path, string $verb, string $action) => [
    'handler' => $handler, 'method' => $method, 'parameters' => $parameters, 'command' => $command, 'context' => 'Learning',
    'roles' => ['super_admin', 'admin'], 'authenticated' => true, 'scope' => $command ? 'learning:write' : 'learning:read',
    'api_path' => $path, 'http_methods' => [$verb], 'source_controller' => 'CourseResource', 'source_action' => $action,
];

return [
    'admin.learning.courses.index' => $def($queries, 'courses', [$input], false, 'learning/courses', 'GET', 'index'),
    'admin.learning.courses.show' => $def($queries, 'course', [$input, $course], false, 'learning/courses/{course}', 'GET', 'show'),
    'admin.learning.courses.create' => $def($commands, 'createCourse', [$input], true, 'learning/courses', 'POST', 'create'),
    'admin.learning.courses.update' => $def($commands, 'updateCourse', [$input, $course], true, 'learning/courses/{course}', 'PUT', 'update'),
    'admin.learning.courses.delete' => $def($commands, 'deleteCourse', [$input, $course], true, 'learning/courses/{course}', 'DELETE', 'delete'),
    'admin.learning.courseLessons.index' => $def($queries, 'lessons', [$input, $course], false, 'learning/courses/{course}/lessons', 'GET', 'lessons.index'),
    'admin.learning.courseLessons.show' => $def($queries, 'lesson', [$input, $course, $lesson], false, 'learning/courses/{course}/lessons/{lesson}', 'GET', 'lessons.show'),
    'admin.learning.courseLessons.create' => $def($commands, 'createLesson', [$input, $course], true, 'learning/courses/{course}/lessons', 'POST', 'lessons.create'),
    'admin.learning.courseLessons.update' => $def($commands, 'updateLesson', [$input, $course, $lesson], true, 'learning/courses/{course}/lessons/{lesson}', 'PUT', 'lessons.update'),
    'admin.learning.courseLessons.delete' => $def($commands, 'deleteLesson', [$input, $course, $lesson], true, 'learning/courses/{course}/lessons/{lesson}', 'DELETE', 'lessons.delete'),
];
