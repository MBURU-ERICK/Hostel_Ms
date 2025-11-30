<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favoriteHostels()
            ->with(['landlord', 'reviews'])
            ->paginate(12);

        return view('student.favorites', compact('favorites'));
    }

    public function toggle(Request $request, $hostelId)
    {
        $hostel = Hostel::approved()->findOrFail($hostelId);
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)
                           ->where('hostel_id', $hostelId)
                           ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Hostel removed from favorites';
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'hostel_id' => $hostelId
            ]);
            $message = 'Hostel added to favorites';
            $isFavorited = true;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorited' => $isFavorited,
                'favorites_count' => $hostel->favorites_count
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function destroy($hostelId)
    {
        $favorite = Favorite::where('user_id', Auth::id())
                           ->where('hostel_id', $hostelId)
                           ->firstOrFail();

        $favorite->delete();

        return redirect()->back()->with('success', 'Hostel removed from favorites');
    }
}