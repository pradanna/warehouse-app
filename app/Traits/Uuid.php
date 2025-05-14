<?php

namespace App\Traits;

trait Uuid
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = \Ramsey\Uuid\Uuid::uuid4()->toString();
            }
        });
    }
}
