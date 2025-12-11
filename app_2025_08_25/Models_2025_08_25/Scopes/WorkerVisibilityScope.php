<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkerVisibilityScope implements Scope
{
    protected $user;

    /**
     * Inject the authenticated user.
     */
    public function __construct()
    {
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) {
            return; // If in CLI (Tinker, Artisan commands), don't apply scope
        }

        if (!$this->user) {
            # If no user is authenticated, return no records
            $builder->whereRaw('1 = 0');
            return;
        }

        # Super Admin & HR can see all workers
        if ($this->user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
            return;
        }

        # PSP can only see their own workers
        if ($this->user->hasRole('PSP')) {
            $builder->where('psp_id', $this->user->psp->id);
            return;
        } else {
            # If no valid role, prevent any workers from being returned
            $builder->whereRaw('1 = 0');
        }
    }
}
