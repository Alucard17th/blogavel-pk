<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Tests\Feature;

use Blogavel\Blogavel\Models\Tag;
use Blogavel\Blogavel\Tests\Support\User;
use Blogavel\Blogavel\Tests\TestCase;
use Laravel\Sanctum\Sanctum;

final class TagsTest extends TestCase
{
    public function test_public_tags_index_and_show(): void
    {
        Tag::create(['name' => 'Tag', 'slug' => 'tag']);

        $index = $this->getJson('/api/blogavel/v1/tags');
        $index->assertStatus(200);
        $index->assertJsonFragment(['slug' => 'tag']);

        $show = $this->getJson('/api/blogavel/v1/tags/tag');
        $show->assertStatus(200);
        $show->assertJsonPath('data.slug', 'tag');
    }

    public function test_admin_can_create_update_and_delete_tag(): void
    {
        config()->set('blogavel.api_admin_auth', 'sanctum');
        config()->set('blogavel.manage_blog_gate', false);

        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $create = $this->postJson('/api/blogavel/v1/admin/tags', [
            'name' => 'New',
            'slug' => 'new',
        ]);

        $create->assertStatus(201);
        $id = $create->json('data.id');

        $update = $this->putJson("/api/blogavel/v1/admin/tags/{$id}", [
            'name' => 'Updated',
            'slug' => 'updated',
        ]);

        $update->assertStatus(200);

        $delete = $this->deleteJson("/api/blogavel/v1/admin/tags/{$id}");
        $delete->assertStatus(200);
    }
}
