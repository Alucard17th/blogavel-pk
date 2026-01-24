<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Tests\Feature;

use Blogavel\Blogavel\Tests\Support\User;
use Blogavel\Blogavel\Tests\TestCase;
use Laravel\Sanctum\Sanctum;

final class ManageBlogGateTest extends TestCase
{
    public function test_admin_web_routes_do_not_require_manage_blog_gate_when_disabled(): void
    {
        static::$blogavelConfigOverrides = [
            'manage_blog_gate' => false,
            'admin_middleware' => ['web', 'auth'],
        ];

        $this->refreshApplicationAndMigrate();

        $user = User::query()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->actingAs($user)->get('/blogavel/admin/posts');

        $response->assertStatus(200);
    }

    public function test_admin_web_routes_require_manage_blog_gate_when_enabled(): void
    {
        static::$blogavelConfigOverrides = [
            'manage_blog_gate' => true,
            'manage_blog_allow_local' => false,
            'manage_blog_admin_emails' => ['admin@example.com'],
            'admin_middleware' => ['web', 'auth'],
        ];

        $this->refreshApplicationAndMigrate();

        $user = User::query()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->actingAs($user)->get('/blogavel/admin/posts');

        $response->assertStatus(403);
    }

    public function test_admin_api_routes_require_manage_blog_gate_when_enabled(): void
    {
        static::$blogavelConfigOverrides = [
            'api_admin_auth' => 'sanctum',
            'manage_blog_gate' => true,
            'manage_blog_allow_local' => false,
            'manage_blog_admin_ids' => [],
            'manage_blog_admin_emails' => ['admin@example.com'],
        ];

        $this->refreshApplicationAndMigrate();

        $user = User::query()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/blogavel/v1/admin/posts', [
            'title' => 'Test',
            'content' => 'Body',
            'status' => 'draft',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_api_routes_allow_manage_blog_gate_when_user_is_allowed(): void
    {
        static::$blogavelConfigOverrides = [
            'api_admin_auth' => 'sanctum',
            'manage_blog_gate' => true,
            'manage_blog_allow_local' => false,
            'manage_blog_admin_emails' => ['admin@example.com'],
        ];

        $this->refreshApplicationAndMigrate();

        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/blogavel/v1/admin/posts', [
            'title' => 'Test',
            'content' => 'Body',
            'status' => 'draft',
        ]);

        $response->assertStatus(201);
    }
}
