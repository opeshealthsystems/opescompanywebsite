<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KnowledgeBaseCategory extends Model
{
    protected $fillable = ['name','slug','description','parent_id','is_public','sort_order','is_active'];
    protected $casts = ['is_public'=>'boolean','is_active'=>'boolean'];

    public function parent(): BelongsTo { return $this->belongsTo(KnowledgeBaseCategory::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(KnowledgeBaseCategory::class, 'parent_id'); }
    public function articles(): HasMany { return $this->hasMany(KnowledgeBaseArticle::class, 'category_id'); }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public static function activeOptions(): array
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('name')->pluck('name','id')->toArray();
    }
}
