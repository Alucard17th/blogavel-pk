<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogavel_posts', function (Blueprint $table): void {
            $table->unsignedBigInteger('author_id')->nullable()->after('featured_media_id');
        });
    }

    public function down(): void
    {
        Schema::table('blogavel_posts', function (Blueprint $table): void {
            $table->dropColumn('author_id');
        });
    }
};
