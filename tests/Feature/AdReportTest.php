<?php

use App\Models\Ad;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;

it('allows an authenticated user to submit a report for an ad', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $reporter = User::factory()->create(['phone' => '03001234567']);
    $owner = User::factory()->create(['phone' => '03007654321']);

    $ad = Ad::create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'title' => 'Used Laptop',
        'slug' => 'used-laptop',
        'description' => 'Lightly used laptop.',
        'price' => 45000,
        'condition' => 'used',
        'city' => 'Lahore',
        'status' => 'available',
    ]);

    $this->actingAs($reporter)
        ->post(route('ads.report.store', $ad), [
            'reason' => 'This listing contains misleading details.',
        ])
        ->assertRedirect();

    expect(Report::where('ad_id', $ad->id)->where('user_id', $reporter->id)->exists())->toBeTrue();
});
