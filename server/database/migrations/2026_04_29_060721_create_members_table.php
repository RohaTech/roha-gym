<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_type_id')->constrained('membership_types')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->unsignedInteger('unique_code');
            $table->string('full_name');
            $table->string('phone');
            $table->string('photo_path')->nullable();
            $table->string('gender')->nullable();
            $table->date('start_date');
            $table->date('expiry_date');
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['gym_id', 'unique_code'], 'members_gym_id_unique_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
