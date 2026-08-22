<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'channels';
    protected $guarded = ['id'];
    protected $fillable = ['company_id', 'parent_channel_id', 'name', 'level', 'is_active'];
    protected $hidden = ['created_at'];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'created_at' => 'datetime',
    ];


    // //حراء شوفي هون 
    // public function parent() : BelongsTo {
    //     return $this->belongsTo(Account::class);
    // }

    // //حراء شوفي هون 
    // public function company() : BelongsTo {
    //     return $this->belongsTo(Account::class, 'company_id', 'id');
    // }

    
    public function childern () : HasMany {
        return $this->hasMany(Channel::class, 'parent_channel_id', 'id');
    }
    
    // public function parent() : BelongsTo {
    //     return $this->belongsTo(Channel::class, 'parent_channel_id', 'id');
    // }
// حراء شوفي هون 
    // public function members() : BelongsToMany {
    //     return $this->belongsToMany(User::class, 'channel_members')
    //     ->withPivot(['channel_role','is_muted','joined_at'])
    //     ->withTimestamps(false);
    // }

    public function scopeRoot ($query){
        return $query->whereNull('parent_channel_id')
        ->where('level', 0);
    }

    public function scopeForCompany ($query, $companyId){
        return $query->where('company_id', $companyId);
    }

}


