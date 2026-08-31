<?php

use App\Mail\AdCreatedMail;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('creating an ad sends the ad-created email to the owner', function () {
    Mail::fake();

    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Cars',
        'slug' => 'cars',
    ]);

    $this->actingAs($user)->postJson(route('ads.store'), [
        'title' => 'Toyota Corolla',
        'category_id' => $category->id,
        'description' => 'A clean car in great condition.',
        'price' => 1500000,
        'condition' => 'used',
        'city' => 'Karachi',
        'status' => 'available',
    ])->assertCreated();

    Mail::assertQueued(AdCreatedMail::class, function (AdCreatedMail $mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});
