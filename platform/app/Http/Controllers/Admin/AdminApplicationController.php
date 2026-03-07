<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\App\Application;
use App\Models\App\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminApplicationController extends Controller
{
    /**
     * Update application details.
     */
    public function update(Request $request, Application $application): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'sometimes|string|max:100',
            'color' => 'sometimes|string|max:7',
            'type' => 'sometimes|in:web,mobile,hybrid,api',
            'source' => 'sometimes|in:internal,external,third-party',
            'base_url' => 'nullable|url|max:500',
            'show_in_menu' => 'sometimes|boolean',
        ]);

        $application->update($data);

        return response()->json([
            'message' => 'Application updated successfully.',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Toggle application active status.
     */
    public function toggleActive(Application $application): JsonResponse
    {
        $application->update(['is_active' => ! $application->is_active]);

        return response()->json([
            'message' => $application->is_active ? 'Application activated.' : 'Application deactivated.',
            'is_active' => $application->is_active,
        ]);
    }

    /**
     * Toggle module active status.
     */
    public function toggleModule(Module $module): JsonResponse
    {
        $module->update(['is_active' => ! $module->is_active]);

        return response()->json([
            'message' => $module->is_active ? 'Module activated.' : 'Module deactivated.',
            'is_active' => $module->is_active,
        ]);
    }

    /**
     * Toggle module premium flag.
     */
    public function toggleModulePremium(Module $module): JsonResponse
    {
        $module->update(['is_premium' => ! $module->is_premium]);

        return response()->json([
            'message' => $module->is_premium ? 'Module marked as premium.' : 'Module set to free.',
            'is_premium' => $module->is_premium,
        ]);
    }

    /**
     * Update module details.
     */
    public function updateModule(Request $request, Module $module): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'route_prefix' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'is_premium' => 'sometimes|boolean',
        ]);

        $module->update($data);

        return response()->json([
            'message' => 'Module updated successfully.',
            'module' => $module->fresh(),
        ]);
    }

    /**
     * Reorder modules within an application.
     */
    public function reorderModules(Request $request, Application $application): JsonResponse
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:modules,id',
        ]);

        foreach ($data['order'] as $index => $moduleId) {
            Module::where('id', $moduleId)
                ->where('application_id', $application->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'Module order updated.']);
    }
}
