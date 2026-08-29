<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_order_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('stage_number');
            $table->string('name');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->string('notes')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['production_order_id', 'stage_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_order_stages');
    }
};
