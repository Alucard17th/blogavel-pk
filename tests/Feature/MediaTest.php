<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Tests\Feature;

use Blogavel\Blogavel\Models\Media;
use Blogavel\Blogavel\Tests\Support\User;
use Blogavel\Blogavel\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

final class MediaTest extends TestCase
{
    public function test_admin_can_upload_and_delete_media(): void
    {
        config()->set('blogavel.api_admin_auth', 'sanctum');
        config()->set('blogavel.manage_blog_gate', false);
        config()->set('blogavel.media_disk', 'public');
        config()->set('blogavel.media_directory', 'blogavel');

        Storage::fake('public');

        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('test.jpg');

        $create = $this->post(
            '/api/blogavel/v1/admin/media',
            ['file' => $file],
            ['Accept' => 'application/json'],
        );

        $create->assertStatus(201);
        $id = $create->json('data.id');

        $media = Media::query()->findOrFail($id);
        Storage::disk('public')->assertExists($media->path);

        $delete = $this->deleteJson("/api/blogavel/v1/admin/media/{$id}");
        $delete->assertStatus(200);
        Storage::disk('public')->assertMissing($media->path);
    }
}
