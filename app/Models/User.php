<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'city',
        'phone',
        'referral_code',
        'referred_by',
        'pan_no',
        'parent_id',
        'left_child',
        'right_child',
        'role',
        'occupation',
        'password',
        'is_profile_completed',
        'refresh_token'
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'plan_purchase_date' => 'datetime',

    ];
    public function videoLikes()
    {
        return $this->hasMany(LikeModel::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function creatorRequest()
    {
        return $this->hasOne(CreatorRequest::class, 'user_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
    public function leftChild()
    {
        return $this->belongsTo(User::class, 'left_child');
    }

    public function rightChild()
    {
        return $this->belongsTo(User::class, 'right_child');
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'user_favourite')
                ->withTimestamps();
    }
}
