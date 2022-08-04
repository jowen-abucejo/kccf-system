<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait UserStamps
{
    public static function bootUserStamps()
    {
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if (auth()->user()) {
                if (Schema::hasColumn($model->getTable(), 'created_by')) {
                    $model->created_by = auth()->user()->id;
                }
                if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                    $model->updated_by = auth()->user()->id;
                }
            }
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_by') && auth()->user()) {
                $model->updated_by = auth()->user()->id;
            }
        });

        static::deleting(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by') && auth()->user()) {
                $model->deleted_by = auth()->user()->id;
            }
        });
    }
}
