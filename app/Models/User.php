<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function getPermissionsGroup() {
        $permissionsGroup = DB::table('permissions')->select('group_name')->groupBy('group_name')->get();
        return $permissionsGroup;
    }

    public static function getPermissionByGroupName($group_name) {
        $permissions = DB::table('permissions')->select('name', 'id')->where('group_name', $group_name)->get();
        return $permissions;
    }

    public static function roleHasPermissions($role, $permissions) {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
            }
        }
        return $hasPermission;
    }

    public function generateReferralLink()
    {
        return route('register') . '?ref=' . $this->referral_code;
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by', 'id')->withTrashed();
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by', 'id')->withTrashed();
    }

    public function hasVerification($status)
    {
        return Verification::where('user_id', $this->id)->where('status', $status)->exists();
    }

    public function isFrontendUser()
    {
        return $this->user_type === 'Frontend';
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->withTrashed();
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->withTrashed();
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class, 'user_id', 'id')->withTrashed();
    }
}
