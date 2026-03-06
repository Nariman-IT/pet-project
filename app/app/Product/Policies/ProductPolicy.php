<?php

namespace App\Product\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function create(User $user): bool
    {
       return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
