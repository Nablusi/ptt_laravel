<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\ChannelMember;
use App\Models\User;

class ChannelMemberPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, Channel $channel): bool
    {
        return $user->belongsToCompany($channel->company_id);
    }

    public function assign(User $user, Channel $channel, User $targetUser): bool
    {
        return $user->isCompanyAdmin()
            && $user->belongsToCompany($channel->company_id)
            && $user->belongsToCompany($targetUser->company_id);
    }

    public function remove(User $user, Channel $channel, User $targetUser): bool
    {
        return $user->isCompanyAdmin()
            && $user->belongsToCompany($channel->company_id)
            && $user->belongsToCompany($targetUser->company_id);
    }

    public function promote(User $user, Channel $channel, User $targetUser): bool
    {
        return $user->isCompanyAdmin()
            && $user->belongsToCompany($channel->company_id)
            && $user->belongsToCompany($targetUser->company_id);
    }
}