<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeFunctionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        #Define a custom Blade directive for a single or multiple permissions
        Blade::if('hasPermissionTo', function ($permissions, $requireAll = false) {

            /** @var \App\Models\User|null $user */
            $user = Auth::user(); #Explicitly tell the IDE this is the User model

            if (!$user) {
                return false; #No user is authenticated
            }

            #Check multiple permissions
            if (is_array($permissions)) {
                foreach ($permissions as $permission) {
                    $hasPermission = $user->hasPermission($permission);

                    if ($requireAll && !$hasPermission) {
                        return false; #Require all permissions, but one is missing
                    }

                    if (!$requireAll && $hasPermission) {
                        return true; #At least one permission exists
                    }
                }

                return $requireAll; #Return true only if all permissions exist
            }

            #Check single permission
            return $user->hasPermission($permissions);
        });


        #Define a custom Blade directive for a single or multiple roles
        Blade::if('hasRole', function ($roles, $requireAll = false) {

            /** @var \App\Models\User|null $user */
            $user = Auth::user(); #Explicitly tell the IDE this is the User model

            if (!$user) {
                return false; #No user is authenticated
            }

            #Check multiple roles
            if (is_array($roles)) {
                foreach ($roles as $role) {
                    $hasRole = $user->hasRole($role);

                    if ($requireAll && !$hasRole) {
                        return false; #Require all roles, but one is missing
                    }

                    if (!$requireAll && $hasRole) {
                        return true; #At least one role exists
                    }
                }

                return $requireAll; #Return true only if all roles exist
            }

            #Check single role
            return $user->hasRole($roles);
        });
    }
}
