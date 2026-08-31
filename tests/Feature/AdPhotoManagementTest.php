<?php

use App\Models\Ad;
use App\Models\AdImage;
use App\Models\Category;
use App\Models\User;

it('allows deleting selected ad photos while editing an ad', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'phone' => '03001234567',
    ]);
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $ad = Ad::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Used Laptop',
        'slug' => 'used-laptop',
        'description' => 'Lightly used laptop in good condition.',
        'price' => 45000,
        'condition' => 'used',
        'city' => 'Lahore',
        'status' => 'available',
    ]);

    $keepImage = $ad->images()->create(['image_path' => 'ad_images/keep.jpg']);
    $removeImage = $ad->images()->create(['image_path' => 'ad_images/remove.jpg']);

    $this->actingAs($admin)
        ->putJson(route('ads.update', $ad), [
            'title' => 'Used Laptop',
            'category_id' => $category->id,
            'description' => 'Lightly used laptop in good condition.',
            'price' => 45000,
            'condition' => 'used',
            'city' => 'Lahore',
            'status' => 'available',
            'delete_image_ids' => [$removeImage->id],
        ])
        ->assertOk();

    expect($ad->fresh()->images()->count())->toBe(1)
        ->and($ad->fresh()->images()->pluck('id')->all())->toContain($keepImage->id)
        ->and($ad->fresh()->images()->pluck('id')->all())->not->toContain($removeImage->id);
});
