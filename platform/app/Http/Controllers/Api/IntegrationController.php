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
    public function sendSMS(Request $request): JsonResponse
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

    // ── Cloud Point / Rewards ──

    /**
     * POST /api/integrations/points/earn
     */
    public function pointsEarn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|string',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        $adapter = $this->gateway->adapter('cloud-point');
        $result = $adapter->earn($validated['user_id'], $validated['points'], $validated['reason']);

        return response()->json(['data' => $result, 'message' => 'Points earned']);
    }

    /**
     * POST /api/integrations/points/redeem
     */
    public function pointsRedeem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|string',
            'points' => 'required|integer|min:1',
            'reward_code' => 'required|string|max:100',
        ]);

        $adapter = $this->gateway->adapter('cloud-point');
        $result = $adapter->redeem($validated['user_id'], $validated['points'], $validated['reward_code']);

        return response()->json(['data' => $result, 'message' => 'Points redeemed']);
    }

    /**
     * GET /api/integrations/points/balance/{userId}
     */
    public function pointsBalance(string $userId): JsonResponse
    {
        $adapter = $this->gateway->adapter('cloud-point');
        $result = $adapter->getBalance($userId);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/integrations/points/transfer
     */
    public function pointsTransfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_user_id' => 'required|string',
            'to_user_id' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $adapter = $this->gateway->adapter('cloud-point');
        $result = $adapter->transfer($validated['from_user_id'], $validated['to_user_id'], $validated['points']);

        return response()->json(['data' => $result, 'message' => 'Points transferred']);
    }

    // ── Translate (dedicated) ──

    /**
     * POST /api/integrations/translate
     */
    public function translate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:10000',
            'target' => 'required|string|max:5',
            'source' => 'sometimes|string|max:5',
        ]);

        $adapter = $this->gateway->adapter('translate');
        $result = $adapter->translate($validated['text'], $validated['target'], $validated['source'] ?? null);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/integrations/translate/batch
     */
    public function translateBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'texts' => 'required|array|min:1|max:100',
            'texts.*' => 'string|max:10000',
            'target' => 'required|string|max:5',
            'source' => 'sometimes|string|max:5',
        ]);

        $adapter = $this->gateway->adapter('translate');
        $result = $adapter->translateBatch($validated['texts'], $validated['target'], $validated['source'] ?? null);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/integrations/translate/detect
     */
    public function detectLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:5000',
        ]);

        $adapter = $this->gateway->adapter('translate');
        $result = $adapter->detectLanguage($validated['text']);

        return response()->json(['data' => $result]);
    }

    // ── TTS (dedicated) ──

    /**
     * POST /api/integrations/tts/synthesize
     */
    public function ttsSynthesize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:5000',
            'language' => 'sometimes|string|in:th,en,zh,ja,ko,ru',
            'voice' => 'sometimes|string|max:50',
            'format' => 'sometimes|string|in:mp3,wav,ogg',
        ]);

        $adapter = $this->gateway->adapter('tts');
        $result = $adapter->synthesize(
            $validated['text'],
            $validated['language'] ?? 'th',
            array_filter([
                'voice' => $validated['voice'] ?? null,
                'format' => $validated['format'] ?? null,
            ])
        );

        return response()->json(['data' => $result]);
    }

    /**
     * GET /api/integrations/tts/voices
     */
    public function ttsVoices(Request $request): JsonResponse
    {
        $adapter = $this->gateway->adapter('tts');
        $result = $adapter->listVoices($request->query('language'));

        return response()->json(['data' => $result]);
    }

    // ── HelpDesk ──

    /**
     * POST /api/integrations/helpdesk/tickets
     */
    public function createTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'priority' => 'sometimes|string|in:low,medium,high,urgent',
            'category' => 'sometimes|string|max:100',
        ]);

        $adapter = $this->gateway->adapter('helpdesk');
        $result = $adapter->createTicket($validated);

        return response()->json(['data' => $result, 'message' => 'Ticket created'], 201);
    }

    /**
     * GET /api/integrations/helpdesk/tickets/{ticketId}
     */
    public function getTicket(string $ticketId): JsonResponse
    {
        $adapter = $this->gateway->adapter('helpdesk');
        $result = $adapter->getTicket($ticketId);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /api/integrations/helpdesk/tickets/{ticketId}/comment
     */
    public function addTicketComment(Request $request, string $ticketId): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
            'is_public' => 'sometimes|boolean',
        ]);

        $adapter = $this->gateway->adapter('helpdesk');
        $result = $adapter->addComment($ticketId, $validated['body'], $validated['is_public'] ?? true);

        return response()->json(['data' => $result, 'message' => 'Comment added']);
    }

    // ── Health Check ──

    /**
     * GET /api/integrations/health
     */
    public function health(): JsonResponse
    {
        $providers = ['payment', 'sms', 'ai-agent', 'cloud-point', 'translate', 'tts', 'helpdesk'];
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
