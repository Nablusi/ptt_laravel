<?php

namespace App\Policies;

use App\Models\Channel;

//حراء عدلي هون
use App\Models\User;

class ChannelPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    //حراء هون تعديل
    public function viewAny(User $user): bool
    {
        return $user->isCompanyAdmin() || (bool) $user->company_id;
    }

    //حراء هون تعديل
    public function view(User $user, Channel $channel): bool
    {
        return $user->belongsToCompany($channel->company_id);
    }

    //حراء هون تعديل
    public function create(User $user): bool
    {
        return $user->isCompanyAdmin();
    }

    public function createRoot(User $user): bool
    {
        return $user->isCompanyAdmin();
    }

    public function update(User $user, Channel $channel): bool
    {
        if ((int) $channel->level === 0) {
            return false;
        }

        return $user->isCompanyAdmin()
            && $user->belongsToCompany($channel->company_id);
    }

    public function delete(User $user, Channel $channel): bool
    {
        if ((int) $channel->level === 0) {
            return false;
        }

        return $user->isCompanyAdmin()
            && $user->belongsToCompany($channel->company_id);
    }

    //اهم شي اضافه هدول الميثودز في تيبل اليوزر 
//     public function isSuperAdmin(): bool
// {
//     return $this->system_role === 'super_admin';
// }

// public function isCompanyAdmin(): bool
// {
//     return $this->system_role === 'company_admin';
// }

// public function belongsToCompany(?string $companyId): bool
// {
//     return $companyId && $this->company_id === $companyId;
// }
}