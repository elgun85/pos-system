<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CustomerTransaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerTransactionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CustomerTransaction');
    }

    public function view(AuthUser $authUser, CustomerTransaction $customerTransaction): bool
    {
        return $authUser->can('View:CustomerTransaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CustomerTransaction');
    }

    public function update(AuthUser $authUser, CustomerTransaction $customerTransaction): bool
    {
        return $authUser->can('Update:CustomerTransaction');
    }

    public function delete(AuthUser $authUser, CustomerTransaction $customerTransaction): bool
    {
        return $authUser->can('Delete:CustomerTransaction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CustomerTransaction');
    }

    public function restore(AuthUser $authUser, CustomerTransaction $customerTransaction): bool
    {
        return $authUser->can('Restore:CustomerTransaction');
    }

    public function forceDelete(AuthUser $authUser, CustomerTransaction $customerTransaction): bool
    {
        return $authUser->can('ForceDelete:CustomerTransaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CustomerTransaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CustomerTransaction');
    }

    public function replicate(AuthUser $authUser, CustomerTransaction $customerTransaction): bool
    {
        return $authUser->can('Replicate:CustomerTransaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CustomerTransaction');
    }

}