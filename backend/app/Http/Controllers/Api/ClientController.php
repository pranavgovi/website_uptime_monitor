<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        $clients = Client::select('id', 'email')
            ->orderBy('email')
            ->get();

        return response()->json($clients);
    }

    public function websites(Client $client): JsonResponse
    {
        $websites = $client->websites()
            ->select('id', 'url', 'is_up', 'last_checked_at')
            ->orderBy('url')
            ->get();

        return response()->json($websites);
    }
}
