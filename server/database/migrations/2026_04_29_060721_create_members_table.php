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
            $table->unsignedMediumInteger('unique_code', 5);
            $table->string('full_name');
            $table->string('phone');
            $table->string('photo_path')->nullable();
            $table->string('gender')->nullable();
            $table->date('start_date');
            $table->date('expiry_date');
            $table->tinyInteger('status')->default(USER_STATUS_ACTIVE);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->constraint('unique_code')->unique(['gym_id', 'unique_code']);
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
