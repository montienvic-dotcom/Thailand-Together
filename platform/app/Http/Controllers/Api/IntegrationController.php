<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiGateway\ApiGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function __construct(
        private ApiGatewayService $gateway,
    ) {}

    /**
     * POST /api/integrations/payment/create
     */
    public function createPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:THB,USD,VND',
            'description' => 'sometimes|string|max:500',
            'return_url' => 'sometimes|url',
            'metadata' => 'sometimes|array',
        ]);

        $adapter = $this->gateway->adapter('payment');
        $result = $adapter->createPayment($validated);

        return response()->json(['data' => $result]);
    }

    /**
     * GET /api/integrations/payment/{paymentId}/status
     */
    public function paymentStatus(string $paymentId): JsonResponse
    {
        $adapter = $this->gateway->adapter('payment');
        $result = $adapter->getPaymentStatus($paymentId);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/integrations/payment/{paymentId}/refund
     */
    public function refundPayment(Request $request, string $paymentId): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'reason' => 'sometimes|string|max:500',
        ]);

        $adapter = $this->gateway->adapter('payment');
        $result = $adapter->refund($paymentId, $validated['amount'] ?? null);

        return response()->json(['data' => $result, 'message' => 'Refund processed']);
    }

    /**
     * POST /api/integrations/sms/send
     */
    public function sendSms(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'to' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        $adapter = $this->gateway->adapter('sms');
        $result = $adapter->send($validated['to'], $validated['message']);

        return response()->json(['data' => $result, 'message' => 'SMS sent']);
    }

    /**
     * POST /api/integrations/sms/otp
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'to' => 'required|string|max:20',
            'code' => 'required|string|min:4|max:8',
        ]);

        $adapter = $this->gateway->adapter('sms');
        $result = $adapter->sendOtp($validated['to'], $validated['code']);

        return response()->json(['data' => $result, 'message' => 'OTP sent']);
    }

    /**
     * POST /api/integrations/ai/chat
     */
    public function aiChat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'context' => 'sometimes|array',
        ]);

        $adapter = $this->gateway->adapter('ai-agent');
        $result = $adapter->chat($validated['message'], $validated['context'] ?? []);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/integrations/ai/translate
     */
    public function aiTranslate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:10000',
            'from' => 'required|string|max:5',
            'to' => 'required|string|max:5',
        ]);

        $adapter = $this->gateway->adapter('ai-agent');
        $result = $adapter->translate($validated['text'], $validated['from'], $validated['to']);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/integrations/ai/tts
     */
    public function aiTextToSpeech(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:5000',
            'language' => 'sometimes|string|in:th,en,zh,ja,ko,ru',
            'voice' => 'sometimes|string|max:50',
        ]);

        $adapter = $this->gateway->adapter('ai-agent');
        $result = $adapter->textToSpeech(
            $validated['text'],
            $validated['language'] ?? 'th',
            $validated['voice'] ?? 'default'
        );

        return response()->json(['data' => $result]);
    }

    /**
     * GET /api/integrations/health
     */
    public function health(): JsonResponse
    {
        $providers = ['payment', 'sms', 'ai-agent'];
        $status = [];

        foreach ($providers as $slug) {
            try {
                $adapter = $this->gateway->adapter($slug);
                $status[$slug] = $adapter->healthCheck() ? 'ok' : 'error';
            } catch (\Throwable) {
                $status[$slug] = 'not_configured';
            }
        }

        return response()->json(['data' => $status]);
    }
}
