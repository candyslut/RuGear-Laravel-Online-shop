<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    public function suggest(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3'
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . env('DADATA_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
            'query' => $request->query,
            'count' => 5,
        ]);

        return response()->json([
            'suggestions' => $response->json()['suggestions'] ?? []
        ]);
    }
}
