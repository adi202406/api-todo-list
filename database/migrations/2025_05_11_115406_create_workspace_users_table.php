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
        Schema::create('workspace_user', function (Blueprint $table) {
            $table->id(); // BIGINT PRIMARY KEY
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50); // 'owner', 'editor', 'viewer'
            $table->enum('status', ['active', 'pending', 'removed'])->default('active');
            $table->foreignId('invited_by')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->softDeletes(); // Soft delete
            $table->timestamps();

            // Prevent duplicate user in same workspace
            $table->unique(['workspace_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_users');
    }
};
