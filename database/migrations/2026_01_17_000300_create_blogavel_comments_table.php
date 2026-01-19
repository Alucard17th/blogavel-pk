<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogavel_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->constrained('blogavel_posts')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('blogavel_comments')->cascadeOnDelete();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            $table->text('content');

            $table->string('status')->default('pending');

            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['post_id', 'status']);
            $table->index(['parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogavel_comments');
    }
};
