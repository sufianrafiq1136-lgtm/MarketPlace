<?php

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;

it('shows only the authenticated users ads in my ads data', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $owner = User::factory()->create(['phone' => '03001234567']);
    $otherUser = User::factory()->create(['phone' => '03007654321']);

    $ownerAd = Ad::create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'title' => 'Owner Laptop',
        'slug' => 'owner-laptop',
        'description' => 'Owner listing',
        'price' => 50000,
        'condition' => 'used',
        'city' => 'Lahore',
        'status' => 'available',
    ]);

    Ad::create([
        'user_id' => $otherUser->id,
        'category_id' => $category->id,
        'title' => 'Other Laptop',
        'slug' => 'other-laptop',
        'description' => 'Other listing',
        'price' => 60000,
        'condition' => 'used',
        'city' => 'Karachi',
        'status' => 'available',
    ]);

    $this->actingAs($owner)
        ->getJson(route('ads.my.data'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $ownerAd->id);
});

it('lets the owner edit and delete their own ad', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $owner = User::factory()->create(['phone' => '03001234567']);

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

    $this->actingAs($owner)
        ->putJson(route('ads.update', $ad), [
            'title' => 'Updated Laptop',
            'category_id' => $category->id,
            'description' => 'Updated description',
            'price' => 47000,
            'condition' => 'used',
            'city' => 'Lahore',
            'status' => 'available',
        ])
        ->assertOk();

    expect($ad->fresh()->title)->toBe('Updated Laptop');

    $this->actingAs($owner)
        ->deleteJson(route('ads.destroy', $ad))
        ->assertOk();

    expect(Ad::find($ad->id))->toBeNull();
});

it('forbids other users from editing or deleting someone elses ad', function () {
    $category = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);

    $owner = User::factory()->create(['phone' => '03001234567']);
    $otherUser = User::factory()->create(['phone' => '03007654321']);

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

    $this->actingAs($otherUser)
        ->putJson(route('ads.update', $ad), [
            'title' => 'Hacked Title',
            'category_id' => $category->id,
            'description' => 'Hacked description',
            'price' => 1000,
            'condition' => 'used',
            'city' => 'Lahore',
            'status' => 'available',
        ])
        ->assertForbidden();

    $this->actingAs($otherUser)
        ->deleteJson(route('ads.destroy', $ad))
        ->assertForbidden();
});
