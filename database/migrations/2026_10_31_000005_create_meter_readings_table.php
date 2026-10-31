<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained()->restrictOnDelete();
            $table->date('reading_date');
            $table->decimal('old_electric', 10, 2)->default(0);
            $table->decimal('new_electric', 10, 2);
            $table->decimal('old_water', 10, 2)->default(0);
            $table->decimal('new_water', 10, 2);
            $table->timestamps();
            $table->unique(['contract_id', 'reading_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
