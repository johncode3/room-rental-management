<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['full_name', 'phone', 'national_id', 'emergency_contact'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
