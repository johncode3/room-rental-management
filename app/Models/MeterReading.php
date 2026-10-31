<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterReading extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'reading_date', 'old_electric', 'new_electric', 'old_water', 'new_water'];

    protected function casts(): array
    {
        return [
            'reading_date' => 'date',
            'old_electric' => 'decimal:2',
            'new_electric' => 'decimal:2',
            'old_water' => 'decimal:2',
            'new_water' => 'decimal:2',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
