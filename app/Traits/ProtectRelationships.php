<?php

namespace App\Traits;

trait ProtectRelationships
{
    public static function bootProtectRelationships()
    {
        static::deleting(function ($model) {
            if (property_exists($model, 'deletionGuardedRelations')) {
                foreach ($model->deletionGuardedRelations as $relation) {
                    if (method_exists($model, $relation) && $model->$relation()->exists()) {
                        throw new \Exception(
                            "No se puede eliminar el registro porque tiene elementos relacionados en '{$relation}'."
                        );
                    }
                }
            }
        });
    }
}
