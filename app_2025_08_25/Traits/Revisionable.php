<?php

namespace App\Traits;

use App\Models\Revision;
use Illuminate\Support\Facades\Auth;

trait Revisionable
{
    protected static function bootRevisionable()
    {
        static::updated(function ($model) {
            $model->saveRevision();
        });
    }

    /**
     * Save a revision when the model is updated.
     */
    public function saveRevision()
    {
        $original = $this->getOriginal(); // Original values
        $changes = $this->getDirty(); // Changed values

        unset($changes['updated_at']);
        unset($original['updated_at']);

        if (!empty($changes)) {
            Revision::create([
                'revisionable_type' => self::class, // Model class
                'revisionable_id' => $this->id, // Model ID
                'old_values' => json_encode(array_intersect_key($original, $changes)), // Old values
                'new_values' => json_encode($changes), // New values
                'user_id' => Auth::id(), // Logged-in user ID
            ]);
        }
    }

    /**
     * Define the relationship to revisions.
     */
    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisionable');
    }
}
