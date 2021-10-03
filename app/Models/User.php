<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class User extends Authenticatable
{
    use HasRoles;
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param string $value
     * @return string
     */
    public function setPasswordAttribute(string $value): string
    {
        return $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return Auth::user()->hasRole('admin');
    }

    /**
     * @param int $categoryId
     * @return bool
     */
    public function isExpert(int $categoryId): bool
    {
        /**
         * @psalm-suppress PossiblyNullReference
         * @psalm-suppress UndefinedInterfaceMethod
         */
        return !!Auth::user()->hasRole('expert')
            && TestCategory::setParentKeyName('parent_id')
                ->ancestorsAndSelf()
                ->where('user_id', Auth::id())
                ->count();
    }

    /**
     * @return \Spatie\Permission\Contracts\Role|Role
     */
    public static function getExpertRole()
    {
        return Role::findByName('expert');
    }

    /**
     * @return \Spatie\Permission\Contracts\Role|Role
     */
    public static function getAdminRole()
    {
        return Role::findByName('admin');
    }

    /**
     * @return User|bool
     */
    public function removeExpertRole()
    {
        if (TestCategory::where('user_id', $this->id)->count() === 0) {
            return $this->removeRole($this->getExpertRole());
        }
        return true;
    }
}
