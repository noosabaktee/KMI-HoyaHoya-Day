<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    public function handle(Request $request)
    {
        // If it's a GET health check without payload
        if ($request->isMethod('get') && empty($request->all())) {
            return $this->verify();
        }

        $payload = $request->json()->all();
        if (empty($payload)) {
            $payload = $request->all();
        }
        if (empty($payload)) {
            $raw = $request->getContent();
            $payload = !empty($raw) ? json_decode($raw, true) : [];
        }

        if (!is_array($payload)) {
            $payload = [];
        }

        Log::info('MPWA Webhook Received: ', $payload);

        $result = $this->whatsAppService->handleIncomingWebhook($payload);

        return response()->json($result);
    }

    public function verify()
    {
        return response()->json([
            'status' => 'active',
            'service' => 'KMI Hoya-Hoya Festival WhatsApp Webhook Gateway',
            'time' => now()->toIso8601String(),
        ]);
    }
}

