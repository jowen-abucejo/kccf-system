<?php

namespace App\Traits;

trait UserStamps
{
    public static function bootUserStamps()
    {
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if (property_exists($model, 'created_by') && auth()->user()) {
                $model->created_by = auth()->user()->id;
                $model->updated_by = auth()->user()->id;
            }
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (property_exists($model, 'updated_by') && auth()->user()) {
                $model->updated_by = auth()->user()->id;
                echo "updating";
            }
        });

        static::deleting(function ($model) {
            if (property_exists($model, 'deleted_by') && auth()->user()) {
                $model->deleted_by = auth()->user()->id;
                echo "deleting";
            }
        });
    }
}
