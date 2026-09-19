<?php

namespace App\Models;

use App\Enums\PhoneNumberTypeEnum;
use App\Traits\LocalTimestamps;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    /** @use HasFactory<\Database\Factories\PhoneNumberFactory> */
    use HasFactory, LocalTimestamps;

    protected $fillable = [
        'phone',
        'type',
        'primary',
    ];

    protected $casts = [
        'type' => 'array',
        'primary' => 'boolean',
    ];

    public $allTypes;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->allTypes = PhoneNumberTypeEnum::values();
    }

    public function scopeOfType(Builder $query, array $type)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $type = array_map('strtolower', $type);

        if (array_diff($type, $this->allTypes)) {
            throw new \Exception(__('validation.invalid_value', ['attribute' => __('validation.attributes.type')]), 400);
        }

        return $this->phonesByType($query, $type);
    }

    public function phonesByType($query, array $type)
    {
        return $query->whereJsonContains('type', $type)
            ->whereJsonDoesntContain('type', array_diff($this->allTypes, $type));
    }
}
