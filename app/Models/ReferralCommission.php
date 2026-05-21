<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCommission extends Model
{
    use HasFactory;
    protected $table = 'referral_commissions';

    protected $fillable = ['user_id','from_user_id','level','amount','is_refunded'];
    
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
