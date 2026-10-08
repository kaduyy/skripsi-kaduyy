<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function status(): JsonResponse
    {
        return $this->forward('get', '/status');
    }

    public function qr(): JsonResponse
    {
        return $this->forward('get', '/qr');
    }

    public function pairingCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);

        return $this->forward('post', '/pairing-code', $validated);
    }

    public function reset(): JsonResponse
    {
        return $this->forward('post', '/reset-session');
    }

    private function forward(string $method, string $path, array $payload = []): JsonResponse
    {
        $url = rtrim(config('services.whatsapp.url'), '/') . $path;

        try {
            $request = Http::acceptJson()->timeout(20);
            $response = $method === 'post'
                ? $request->post($url, $payload)
                : $request->get($url);

            return response()->json(
                $response->json() ?? ['message' => 'WhatsApp service returned an invalid response.'],
                $response->status()
            );
        } catch (ConnectionException) {
            return response()->json([
                'status' => 'offline',
                'connected' => false,
                'message' => 'WhatsApp service is not reachable. Start the service and try again.',
            ], 503);
        }
    }
}
