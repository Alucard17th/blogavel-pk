<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Tests\Feature;

use Blogavel\Blogavel\Models\Category;
use Blogavel\Blogavel\Tests\Support\User;
use Blogavel\Blogavel\Tests\TestCase;
use Laravel\Sanctum\Sanctum;

final class CategoriesTest extends TestCase
{
    public function test_public_categories_index_and_show(): void
    {
        Category::create(['name' => 'Cat', 'slug' => 'cat']);

        $index = $this->getJson('/api/blogavel/v1/categories');
        $index->assertStatus(200);
        $index->assertJsonFragment(['slug' => 'cat']);

        $show = $this->getJson('/api/blogavel/v1/categories/cat');
        $show->assertStatus(200);
        $show->assertJsonPath('data.slug', 'cat');
    }

    public function test_admin_can_create_update_and_delete_category(): void
    {
        config()->set('blogavel.api_admin_auth', 'sanctum');
        config()->set('blogavel.manage_blog_gate', false);

        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $create = $this->postJson('/api/blogavel/v1/admin/categories', [
            'name' => 'New',
            'slug' => 'new',
        ]);

        $create->assertStatus(201);
        $id = $create->json('data.id');

        $update = $this->putJson("/api/blogavel/v1/admin/categories/{$id}", [
            'name' => 'Updated',
            'slug' => 'updated',
        ]);

        $update->assertStatus(200);

        $delete = $this->deleteJson("/api/blogavel/v1/admin/categories/{$id}");
        $delete->assertStatus(200);
    }
}
