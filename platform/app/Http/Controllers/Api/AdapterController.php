<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Adapter\AdapterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdapterController extends Controller
{
    public function __construct(
        private readonly AdapterService $adapterService,
    ) {}

    /**
     * GET /api/adapters
     *
     * List all registered adapters with their status.
     */
    public function index(): JsonResponse
    {
        $adapters = $this->adapterService->listAdapters();

        return response()->json([
            'data'  => $adapters,
            'total' => count($adapters),
        ]);
    }

    /**
     * GET /api/adapters/{name}/health
     *
     * Health-check a specific adapter.
     */
    public function health(string $name): JsonResponse
    {
        if (!$this->adapterService->hasAdapter($name)) {
            return response()->json([
                'error'   => 'Adapter not found',
                'adapter' => $name,
            ], 404);
        }

        $result = $this->adapterService->healthCheck($name);
        $status = $result['status'] === 'ok' ? 200 : 503;

        return response()->json([
            'adapter' => $name,
            'health'  => $result,
        ], $status);
    }

    /**
     * POST /api/adapters/{name}/execute
     *
     * Execute an action on a specific adapter.
     *
     * Request body:
     *   { "action": "listRooms", "params": { ... } }
     */
    public function execute(Request $request, string $name): JsonResponse
    {
        if (!$this->adapterService->hasAdapter($name)) {
            return response()->json([
                'error'   => 'Adapter not found',
                'adapter' => $name,
            ], 404);
        }

        $validated = $request->validate([
            'action' => ['required', 'string'],
            'params' => ['sometimes', 'array'],
        ]);

        $action = $validated['action'];
        $params = $validated['params'] ?? [];

        try {
            $adapter = $this->adapterService->getAdapter($name);

            if (!in_array($action, $adapter->getSupportedActions(), true)) {
                return response()->json([
                    'error'             => "Action [{$action}] is not supported by adapter [{$name}].",
                    'supported_actions' => $adapter->getSupportedActions(),
                ], 422);
            }

            $result = $this->adapterService->execute($name, $action, $params);

            return response()->json([
                'adapter' => $name,
                'action'  => $action,
                'data'    => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
