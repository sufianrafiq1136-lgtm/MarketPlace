<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with('ad.category', 'ad.user', 'ad.images')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Ad $ad): RedirectResponse
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('ad_id', $ad->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('status', 'Removed from favorites.');
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'ad_id' => $ad->id,
        ]);

        return back()->with('status', 'Added to favorites.');
    }
}
