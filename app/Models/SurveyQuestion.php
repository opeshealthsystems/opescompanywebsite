<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id', 'question', 'question_fr', 'type',
        'options', 'options_fr', 'is_required', 'sort_order',
    ];

    protected $casts = [
        'options'     => 'array',
        'options_fr'  => 'array',
        'is_required' => 'boolean',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    public static function typeOptions(): array
    {
        return [
            'text'            => 'Text',
            'rating'          => 'Rating (1–5)',
            'multiple_choice' => 'Multiple Choice',
            'yes_no'          => 'Yes / No',
        ];
    }
}
