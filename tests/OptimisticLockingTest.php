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
