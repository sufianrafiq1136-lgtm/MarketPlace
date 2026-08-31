<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\Category;
use App\Mail\AdCreatedMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->is_admin, 403);
    }

    private function ensureOwnerOrAdmin(Ad $ad): void
    {
        abort_unless(Auth::id() === $ad->user_id || Auth::user()?->is_admin, 403);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ads.index');
    }

    public function myAds()
    {
        return view('ads.my');
    }

    public function create()
    {
        return view('ads.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function data(): JsonResponse
    {
        $ads = Ad::with('category', 'user', 'images')->latest()->get();
        $title = 'All ads';

        return response()->json([
            'success' => true,
            'data' => $ads,
            'title' => $title,
            'errors' => null,
        ]);
    }

    public function myAdsData(): JsonResponse
    {
        $user = Auth::user();

        $ads = Ad::with('category', 'user', 'images')
            ->where('user_id', $user?->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ads,
            'title' => 'My ads',
            'errors' => null,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'condition' => ['required', Rule::in(['new', 'used'])],
            'city' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['available', 'sold', 'pending'])],
            'images.*' => ['nullable', 'image', 'max:4096'],
        ]);

        $ad = Ad::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::lower(Str::random(6)),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'condition' => $validated['condition'],
            'city' => $validated['city'],
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('ad_images', 'public');
                $ad->images()->create([
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        Mail::to(Auth::user()->email)->queue(
            new AdCreatedMail($ad->load('category', 'user'))
        );

        return response()->json([
            'success' => true,
            'data' => $ad->load('category', 'user', 'images'),
            'errors' => null,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ad $ad)
    {
        $ad->load([
            'category',
            'user',
            'images',
            'messages' => fn ($query) => $query->with(['sender', 'receiver'])->latest(),
        ]);

        return view('ads.show', [
            'ad' => $ad,
        ]);
    }

    public function edit(Ad $ad)
    {
        $this->ensureOwnerOrAdmin($ad);

        return view('ads.edit', [
            'ad' => $ad->load('images'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Ad $ad): JsonResponse
    {
        $this->ensureOwnerOrAdmin($ad);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'condition' => ['required', Rule::in(['new', 'used'])],
            'city' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['available', 'sold', 'pending'])],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'delete_image_ids' => ['nullable', 'array'],
            'delete_image_ids.*' => ['integer'],
        ]);

        $ad->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::lower(Str::random(6)),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'condition' => $validated['condition'],
            'city' => $validated['city'],
            'status' => $validated['status'],
        ]);

        $deleteImageIds = array_map('intval', $request->input('delete_image_ids', []));
        if (!empty($deleteImageIds)) {
            $imagesToDelete = $ad->images()->whereIn('id', $deleteImageIds)->get();

            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }

        $remainingImages = $ad->fresh()->images()->orderBy('sort_order')->orderBy('id')->get();
        foreach ($remainingImages as $index => $image) {
            $image->update(['sort_order' => $index]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('ad_images', 'public');
                $ad->images()->create([
                    'image_path' => $path,
                    'sort_order' => $remainingImages->count() + $index,
                ]);
            }
        }

        $orderedImages = $ad->fresh()->images()->orderBy('sort_order')->orderBy('id')->get();
        foreach ($orderedImages as $index => $image) {
            $image->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'data' => $ad->fresh()->load('category', 'user', 'images'),
            'errors' => null,
        ]);
    }

    public function destroy(Ad $ad): JsonResponse
    {
        $this->ensureOwnerOrAdmin($ad);

        foreach ($ad->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $ad->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'errors' => null,
        ]);
    }
}
