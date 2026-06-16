<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KnowledgeBaseArticle extends Model
{
    protected $fillable = ['category_id','author_id','title','slug','content','status','is_public','views','tags','published_at'];
    protected $casts = ['published_at'=>'datetime','is_public'=>'boolean','tags'=>'array'];

    public function category(): BelongsTo { return $this->belongsTo(KnowledgeBaseCategory::class, 'category_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public static function statusOptions(): array
    {
        return ['draft'=>'Draft','published'=>'Published','archived'=>'Archived'];
    }
}
