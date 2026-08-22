<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ChannelMember extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'channel_members';
    protected $guarded = ['id'];
    protected $fillable = ['channel_id', 'user_id', 'channel_role', 'is_muted', 'joined_at'];
    protected $hidden = ['created_at'];
    protected $casts = [
        'is_muted' => 'boolean',
        'joined_at' => 'datetime',
    ];


    public function channel() : BelongsTo {
        return $this->belongsTo(Channel::class);
    }

    // حراء شوفي هون 
    // public function user() : BelongsTo {
    //     return $this->belongsTo(User::class);
    // }

    public function scopeForChannel ($query, $channelId){
        return $query->where('channel_id', $channelId);
    }

    public function scopeForUser ($query, $userId){
        return $query->where('user_id', $userId);
    }
    
    public function scopeCompanyAdmin ($query){
        return $query->where('channel_role', 'company_admin');
    }

    
}
