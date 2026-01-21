<?php

use Stafe\OptimisticLocking\StaleModelException;
use Stafe\OptimisticLocking\Tests\Models\Post;

it('increments version on save', function () {
    $post = Post::create(['title' => 'A']);
    expect($post->lock_version)->toBe(1);
    $post->title = 'B';
    $post->save();
    expect($post->lock_version)->toBe(2);
});

it('throws exception on stale update', function () {
    $post = Post::create(['title' => 'A']);
    $first = Post::find($post->id);
    $second = Post::find($post->id);

    $first->title = 'B';
    $first->save();

    $second->title = 'C';
    expect(function () use ($second) {
        $second->save();
    })->toThrow(StaleModelException::class);
});

it('increments version on mass update', function () {
    $postA = Post::create(['title' => 'A']);
    $postB = Post::create(['title' => 'B']);

    Post::query()->updateAndIncrementLock(['title' => 'C']);

    expect(Post::find($postA->id)->lock_version)->toBe(2);
    expect(Post::find($postB->id)->lock_version)->toBe(2);
});
