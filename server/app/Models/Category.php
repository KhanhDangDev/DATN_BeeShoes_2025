<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    protected $table = 'categories';

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    public function __construct(array $attributes = [])
    {
        $this->fillable = array_merge(parent::getBaseFillable(), $this->fillable);
        parent::__construct($attributes);
    }
}
