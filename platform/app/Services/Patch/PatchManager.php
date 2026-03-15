<?php

namespace App\Services\Patch;

use Illuminate\Support\Facades\File;

/**
 * Patch Manager — Applies, checks, and rolls back patches for external scripts.
 *
 * Patch types supported:
 * - find_replace: Find a string in a target file and replace it
 * - override: Service provider class replacements (registered via provider)
 * - config: Modify config file values
 *
 * Patches are defined as JSON files and tracked via registry.json.
 * Applied state is tracked separately in applied.json to keep
 * the registry clean and version-controllable.
 */
class PatchManager
{
    private string $patchesDir;

    private string $registryPath;

    private string $appliedPath;

    public function __construct()
    {
        $this->patchesDir = base_path('../patches');
        $this->registryPath = $this->patchesDir . '/registry.json';
        $this->appliedPath = $this->patchesDir . '/applied.json';
    }

    /**
     * Apply a specific patch by ID.
     *
     * @return array{id: string, status: string, message: string}
     */
    public function apply(string $patchId): array
    {
        $registry = $this->getRegistry();
        $applied = $this->getApplied();
        $patch = $this->findPatchInRegistry($patchId, $registry);

        if ($patch === null) {
            return [
                'id' => $patchId,
                'status' => 'not_found',
                'message' => "Patch '{$patchId}' not found in registry.",
            ];
        }

        if (isset($applied[$patchId])) {
            return [
                'id' => $patchId,
                'status' => 'already_applied',
                'message' => "Patch '{$patchId}' is already applied.",
            ];
        }

        $result = $this->executePatch($patch);

        if ($result['success']) {
            $applied[$patchId] = [
                'applied_at' => now()->toIso8601String(),
                'app' => $patch['app'] ?? 'unknown',
                'description' => $patch['description'] ?? '',
            ];
            $this->saveApplied($applied);
        }

        return [
            'id' => $patchId,
            'status' => $result['success'] ? 'applied' : 'failed',
            'message' => $result['message'],
        ];
    }

    /**
     * Apply all pending patches from the registry.
     *
     * @return array<int, array{id: string, status: string, message: string}>
     */
    public function applyAll(): array
    {
        $registry = $this->getRegistry();
        $results = [];

        foreach ($registry as $patch) {
            $id = $patch['id'] ?? null;
            if ($id === null) {
                continue;
            }

            $status = $this->getPatchStatus($id);

            if ($status === 'applied' || $status === 'already_fixed') {
                $results[] = [
                    'id' => $id,
                    'status' => 'skipped',
                    'message' => "Patch is {$status}.",
                ];
                continue;
            }

            if ($status === 'pending') {
                $results[] = $this->apply($id);
                continue;
            }

            $results[] = [
                'id' => $id,
                'status' => $status,
                'message' => "Patch cannot be applied (status: {$status}).",
            ];
        }

        return $results;
    }

    /**
     * Check status of all patches in the registry.
     *
     * @return array<int, array{id: string, app: string, description: string, type: string, status: string}>
     */
    public function check(): array
    {
        $registry = $this->getRegistry();
        $results = [];

        foreach ($registry as $patch) {
            $id = $patch['id'] ?? 'unknown';
            $results[] = [
                'id' => $id,
                'app' => $patch['app'] ?? 'unknown',
                'description' => $patch['description'] ?? '',
                'type' => $patch['type'] ?? 'find_replace',
                'status' => $this->getPatchStatus($id),
            ];
        }

        return $results;
    }

    /**
     * Rollback a specific patch by reversing the find/replace operation.
     *
     * @return array{id: string, status: string, message: string}
     */
    public function rollback(string $patchId): array
    {
        $applied = $this->getApplied();

        if (! isset($applied[$patchId])) {
            return [
                'id' => $patchId,
                'status' => 'not_applied',
                'message' => "Patch '{$patchId}' is not currently applied.",
            ];
        }

        $registry = $this->getRegistry();
        $patch = $this->findPatchInRegistry($patchId, $registry);

        if ($patch === null) {
            return [
                'id' => $patchId,
                'status' => 'not_found',
                'message' => "Patch '{$patchId}' not found in registry.",
            ];
        }

        $result = $this->executeRollback($patch);

        if ($result['success']) {
            unset($applied[$patchId]);
            $this->saveApplied($applied);
        }

        return [
            'id' => $patchId,
            'status' => $result['success'] ? 'rolled_back' : 'rollback_failed',
            'message' => $result['message'],
        ];
    }

    /**
     * Load and return the full registry as an array of patch definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRegistry(): array
    {
        if (! File::exists($this->registryPath)) {
            return [];
        }

        $data = json_decode(File::get($this->registryPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        // Support both flat array and object with "patches" key
        if (isset($data['patches']) && is_array($data['patches'])) {
            return $data['patches'];
        }

        if (is_array($data) && (empty($data) || isset($data[0]))) {
            return $data;
        }

        return [];
    }

    /**
     * Get the status of a specific patch.
     *
     * Possible statuses:
     * - applied: patch is applied and the replacement string exists in target
     * - pending: patch has not been applied and the find string exists in target
     * - conflict: patch has not been applied and the find string is NOT in target
     * - already_fixed: not applied but replacement string already exists
     * - file_missing: target file does not exist
     * - reverted_by_update: was applied but find string is back (external update)
     * - code_changed: was applied but neither find nor replace string found
     * - not_found: patch ID not in registry
     */
    public function getPatchStatus(string $patchId): string
    {
        $registry = $this->getRegistry();
        $patch = $this->findPatchInRegistry($patchId, $registry);

        if ($patch === null) {
            return 'not_found';
        }

        $applied = $this->getApplied();
        $isApplied = isset($applied[$patchId]);
        $type = $patch['type'] ?? 'find_replace';

        if ($type !== 'find_replace') {
            return $isApplied ? 'applied' : 'pending';
        }

        $targetFile = $this->resolveTargetFile($patch);

        if (! File::exists($targetFile)) {
            return 'file_missing';
        }

        $content = File::get($targetFile);
        $findString = $patch['find'] ?? '';
        $replaceString = $patch['replace'] ?? '';

        $hasFindString = str_contains($content, $findString);
        $hasReplaceString = str_contains($content, $replaceString);

        if ($isApplied) {
            if ($hasReplaceString) {
                return 'applied';
            }
            if ($hasFindString) {
                return 'reverted_by_update';
            }

            return 'code_changed';
        }

        // Not applied
        if ($hasFindString) {
            return 'pending';
        }
        if ($hasReplaceString) {
            return 'already_fixed';
        }

        return 'conflict';
    }

    /**
     * Get the applied patches tracking data.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getApplied(): array
    {
        if (! File::exists($this->appliedPath)) {
            return [];
        }

        $data = json_decode(File::get($this->appliedPath), true);

        return is_array($data) ? $data : [];
    }

    /**
     * Resolve the absolute path for a patch's target file.
     */
    private function resolveTargetFile(array $patch): string
    {
        $targetFile = $patch['target_file'] ?? $patch['file'] ?? '';

        // If it's already absolute, use it directly
        if (str_starts_with($targetFile, '/')) {
            return $targetFile;
        }

        // Resolve relative to Laravel base path
        return base_path($targetFile);
    }

    /**
     * Find a patch definition in the registry by its ID.
     */
    private function findPatchInRegistry(string $patchId, array $registry): ?array
    {
        foreach ($registry as $patch) {
            if (($patch['id'] ?? null) === $patchId) {
                return $patch;
            }
        }

        return null;
    }

    /**
     * Execute a patch (apply the find/replace to the target file).
     *
     * @return array{success: bool, message: string}
     */
    private function executePatch(array $patch): array
    {
        $type = $patch['type'] ?? 'find_replace';

        return match ($type) {
            'find_replace' => $this->executeFindReplace($patch),
            'override' => ['success' => true, 'message' => 'Override patch registered via service provider.'],
            'config' => $this->executeFindReplace($patch),
            default => ['success' => false, 'message' => "Unknown patch type: {$type}"],
        };
    }

    /**
     * Execute a find/replace patch on the target file.
     *
     * @return array{success: bool, message: string}
     */
    private function executeFindReplace(array $patch): array
    {
        $targetFile = $this->resolveTargetFile($patch);
        $findString = $patch['find'] ?? '';
        $replaceString = $patch['replace'] ?? '';

        if (empty($findString)) {
            return ['success' => false, 'message' => 'Patch has no "find" string defined.'];
        }

        if (! File::exists($targetFile)) {
            return ['success' => false, 'message' => "Target file not found: {$targetFile}"];
        }

        $content = File::get($targetFile);

        if (! str_contains($content, $findString)) {
            return ['success' => false, 'message' => 'Find string not found in target file (conflict).'];
        }

        // Create backup before modifying
        $backupDir = $this->patchesDir . '/backups';
        if (! File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backupFile = $backupDir . '/' . ($patch['id'] ?? 'unknown') . '_' . basename($targetFile) . '.bak';
        File::copy($targetFile, $backupFile);

        // Apply the replacement
        $newContent = str_replace($findString, $replaceString, $content);
        File::put($targetFile, $newContent);

        return ['success' => true, 'message' => "Patch applied to {$targetFile}"];
    }

    /**
     * Execute a rollback by reversing find/replace.
     *
     * @return array{success: bool, message: string}
     */
    private function executeRollback(array $patch): array
    {
        $type = $patch['type'] ?? 'find_replace';

        if ($type === 'override') {
            return ['success' => true, 'message' => 'Override patch rollback — restart application to take effect.'];
        }

        $targetFile = $this->resolveTargetFile($patch);
        $findString = $patch['find'] ?? '';
        $replaceString = $patch['replace'] ?? '';

        if (! File::exists($targetFile)) {
            return ['success' => false, 'message' => "Target file not found: {$targetFile}"];
        }

        $content = File::get($targetFile);

        if (! str_contains($content, $replaceString)) {
            return ['success' => false, 'message' => 'Replace string not found in target — cannot rollback.'];
        }

        // Reverse the replacement
        $newContent = str_replace($replaceString, $findString, $content);
        File::put($targetFile, $newContent);

        return ['success' => true, 'message' => "Patch rolled back in {$targetFile}"];
    }

    /**
     * Save the applied patches tracking file.
     */
    private function saveApplied(array $applied): void
    {
        $dir = dirname($this->appliedPath);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put(
            $this->appliedPath,
            json_encode($applied, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"
        );
    }
}
