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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('users')->cascadeOnDelete();
            $table->string('plan_type', 20); // 'lifetime' | 'monthly'
            $table->unsignedInteger('months')->nullable(); // months purchased (monthly only)
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 8)->default('ETB');
            $table->date('starts_at');
            $table->date('ends_at')->nullable(); // null = lifetime / never expires
            $table->date('paid_at');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['gym_id', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
