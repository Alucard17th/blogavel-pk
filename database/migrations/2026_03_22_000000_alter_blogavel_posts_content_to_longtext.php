<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('blogavel_posts') || !Schema::hasColumn('blogavel_posts', 'content')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `blogavel_posts` MODIFY `content` LONGTEXT NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('blogavel_posts') || !Schema::hasColumn('blogavel_posts', 'content')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `blogavel_posts` MODIFY `content` TEXT NULL');
        }
    }
};
