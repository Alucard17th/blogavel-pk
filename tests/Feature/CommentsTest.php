<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Tests\Feature;

use Blogavel\Blogavel\Models\Comment;
use Blogavel\Blogavel\Models\Post;
use Blogavel\Blogavel\Tests\Support\User;
use Blogavel\Blogavel\Tests\TestCase;
use Laravel\Sanctum\Sanctum;

final class CommentsTest extends TestCase
{
    public function test_public_can_create_comment_and_list_post_comments(): void
    {
        $post = Post::create([
            'title' => 'Published',
            'slug' => 'published',
            'content' => '...',
            'status' => 'published',
        ]);

        $store = $this->postJson("/api/blogavel/v1/posts/{$post->slug}/comments", [
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'content' => 'Hello',
        ]);

        $store->assertStatus(201);

        $commentId = $store->json('data.id');

        $index = $this->getJson("/api/blogavel/v1/posts/{$post->slug}/comments");
        $index->assertStatus(200);
        $index->assertJsonMissing(['id' => $commentId]);

        config()->set('blogavel.api_admin_auth', 'sanctum');
        config()->set('blogavel.manage_blog_gate', false);

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($admin);

        $approve = $this->postJson("/api/blogavel/v1/admin/comments/{$commentId}/approve");
        $approve->assertStatus(200);

        $index2 = $this->getJson("/api/blogavel/v1/posts/{$post->slug}/comments");
        $index2->assertStatus(200);
        $index2->assertJsonFragment(['id' => $commentId]);
    }

    public function test_admin_can_approve_mark_spam_and_delete_comment(): void
    {
        config()->set('blogavel.api_admin_auth', 'sanctum');
        config()->set('blogavel.manage_blog_gate', false);

        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $post = Post::create([
            'title' => 'Published',
            'slug' => 'published',
            'content' => '...',
            'status' => 'published',
        ]);

        /** @var \Blogavel\Blogavel\Models\Comment $comment */
        $comment = Comment::create([
            'post_id' => $post->id,
            'content' => 'Hello',
            'status' => 'pending',
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
        ]);

        $approve = $this->postJson("/api/blogavel/v1/admin/comments/{$comment->id}/approve");
        $approve->assertStatus(200);
        $this->assertSame('approved', Comment::query()->findOrFail($comment->id)->status);

        $spam = $this->postJson("/api/blogavel/v1/admin/comments/{$comment->id}/spam");
        $spam->assertStatus(200);
        $this->assertSame('spam', Comment::query()->findOrFail($comment->id)->status);

        $delete = $this->deleteJson("/api/blogavel/v1/admin/comments/{$comment->id}");
        $delete->assertStatus(200);
        $this->assertNull(Comment::query()->find($comment->id));
    }
}
