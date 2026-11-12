<?php

namespace App\Models;

use App\Enums\Permission;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Str;

#[Fillable(['name', 'permission'])]
#[Hidden(['token'])]
class ApiKey extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    public static function findByToken(?string $token): ?self
    {
        return $token ? static::firstWhere('token', hash('sha256', $token)) : null;
    }

    public function generateToken(): string
    {
        $token = 'ittv_' . Str::random(48);

        $this->token = hash('sha256', $token);

        return $token;
    }

    protected function casts(): array
    {
        return [
            'permission' => Permission::class,
            'last_used' => 'datetime',
        ];
    }
}
