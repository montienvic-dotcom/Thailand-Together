<?php

namespace App\Http\Middleware;

use App\Services\Cluster\ClusterManager;
use App\Services\Permission\PermissionResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Checks if the authenticated user has access to the requested app/module.
 * Usage in routes: ->middleware('module.access:APP_CODE,MODULE_CODE')
 */
class CheckModuleAccess
{
    public function __construct(
        private PermissionResolver $permissionResolver,
        private ClusterManager $clusterManager,
    ) {}

    public function handle(Request $request, Closure $next, string $appCode, ?string $moduleCode = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $clusterId = $this->clusterManager->currentId();
        if (!$clusterId) {
            return response()->json(['error' => 'No cluster context'], 400);
        }

        // Resolve app ID from code
        $application = \App\Models\App\Application::where('code', $appCode)->first();
        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        $moduleId = null;
        if ($moduleCode) {
            $module = \App\Models\App\Module::where('application_id', $application->id)
                ->where('code', $moduleCode)
                ->first();
            if (!$module) {
                return response()->json(['error' => 'Module not found'], 404);
            }
            $moduleId = $module->id;
        }

        if (!$this->permissionResolver->canAccess($user, $clusterId, $application->id, $moduleId)) {
            return response()->json(['error' => 'Access denied to this module'], 403);
        }

        return $next($request);
    }
}
