<?php

namespace App\Http\Controllers;

use App\Models\ShopItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $items = ShopItem::all()->groupBy('category');
        $owned = $user->shopItems()->pluck('shop_item_id')->toArray();

        return view('market', compact('items', 'owned'));
    }

    public function buy(ShopItem $item)
    {
        $user = Auth::user();

        if (in_array($item->id, $user->shopItems()->pluck('shop_item_id')->toArray())) {
            return response()->json(['error' => 'already_owned'], 400);
        }

        if ($user->coins < $item->price) {
            return response()->json(['error' => 'not_enough_coins'], 400);
        }

        $user->decrement('coins', $item->price);
        $user->shopItems()->attach($item->id, ['purchased_at' => now()]);

        $this->applyCosmetic($user, $item);

        return response()->json([
            'coins'    => $user->fresh()->coins,
            'equipped' => true,
            'category' => $item->category,
            'css'      => $item->css_value,
        ]);
    }

    public function equip(ShopItem $item)
    {
        $user = Auth::user();

        if (!in_array($item->id, $user->shopItems()->pluck('shop_item_id')->toArray())) {
            return response()->json(['error' => 'not_owned'], 400);
        }

        $this->applyCosmetic($user, $item);

        return response()->json([
            'equipped' => true,
            'category' => $item->category,
            'css'      => $item->css_value,
        ]);
    }

    public function unequip(Request $request)
    {
        $user     = Auth::user();
        $category = $request->input('category');
        $col      = $this->col($category);

        if ($col) {
            $user->$col = null;
            $user->save();
        }

        return response()->json(['unequipped' => true, 'category' => $category]);
    }

    private function applyCosmetic($user, ShopItem $item): void
    {
        $col = $this->col($item->category);
        if ($col) {
            $user->$col = $item->css_value;
            $user->save();
        }
    }

    private function col(string $category): ?string
    {
        return match($category) {
            'font'           => 'cosmetic_font',
            'border'         => 'cosmetic_border',
            'nickname_color' => 'cosmetic_nickname_color',
            default          => null,
        };
    }
}
