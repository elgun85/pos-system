<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Damage;
use Illuminate\Auth\Access\HandlesAuthorization;

class DamagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Damage');
    }

    public function view(AuthUser $authUser, Damage $damage): bool
    {
        return $authUser->can('View:Damage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Damage');
    }

    public function update(AuthUser $authUser, Damage $damage): bool
    {
        return $authUser->can('Update:Damage');
    }

    public function delete(AuthUser $authUser, Damage $damage): bool
    {
        return $authUser->can('Delete:Damage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Damage');
    }

    public function restore(AuthUser $authUser, Damage $damage): bool
    {
        return $authUser->can('Restore:Damage');
    }

    public function forceDelete(AuthUser $authUser, Damage $damage): bool
    {
        return $authUser->can('ForceDelete:Damage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Damage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Damage');
    }

    public function replicate(AuthUser $authUser, Damage $damage): bool
    {
        return $authUser->can('Replicate:Damage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Damage');
    }

}