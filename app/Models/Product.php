<?php

namespace App\Models;

use App\Traits\LocalTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes, LocalTimestamps;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
    ];

    protected $hidden = [
        'category_id',
        'deleted_at',
    ];

    protected $appends = [
        'images_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function beforeImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->where('position', 'before');
    }

    public function afterImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->where('position', 'after');
    }

    public function gallery(): HasMany
    {
        return $this->images()
            ->where('position', 'gallery');
    }

    public function getImagesCountAttribute(): int
    {
        return $this->images()->count();
    }
}
