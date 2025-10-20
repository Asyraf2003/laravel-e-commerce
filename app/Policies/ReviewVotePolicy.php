<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReviewVote;
use App\Policies\Concerns\AdminBypass;

class ReviewVotePolicy
{
    use AdminBypass;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ReviewVote $vote): bool
    {
        return $vote->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ReviewVote $vote): bool
    {
        return $vote->user_id === $user->id;
    }

    public function delete(User $user, ReviewVote $vote): bool
    {
        return $vote->user_id === $user->id;
    }

    public function restore(User $user, ReviewVote $vote): bool
    {
        return $vote->user_id === $user->id;
    }

    public function forceDelete(User $user, ReviewVote $vote): bool
    {
        return $user->isAdmin();
    }
}
