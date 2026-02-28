<?php

namespace ThailandTogether\Patches;

/**
 * Patch Manager - Applies, checks, and rolls back patches for CodeCanyon scripts.
 *
 * Patch types:
 * 1. File patches (find/replace in specific files)
 * 2. Override patches (service provider class replacements)
 * 3. Config patches (modify config values)
 *
 * Usage:
 *   php artisan patches:check   - Check which patches need applying
 *   php artisan patches:apply   - Apply all pending patches
 *   php artisan patches:rollback {id} - Rollback a specific patch
 */
class PatchManager
{
    private string $registryPath;
    private string $patchesDir;
    private array $registry;

    public function __construct(?string $basePath = null)
    {
        $base = $basePath ?? dirname(__DIR__);
        $this->registryPath = $base . '/registry.json';
        $this->patchesDir = $base;
        $this->registry = $this->loadRegistry();
    }

    /**
     * Check all patches and return their status.
     */
    public function check(): array
    {
        $results = [];

        foreach ($this->registry['patches'] as $patch) {
            $results[] = [
                'id' => $patch['id'],
                'app' => $patch['app'],
                'description' => $patch['description'],
                'status' => $this->checkPatch($patch),
            ];
        }

        return $results;
    }

    /**
     * Apply all pending patches.
     */
    public function applyAll(): array
    {
        $results = [];

        foreach ($this->registry['patches'] as &$patch) {
            if ($patch['applied'] ?? false) {
                $results[] = ['id' => $patch['id'], 'status' => 'already_applied'];
                continue;
            }

            $result = $this->applyPatch($patch);
            $results[] = ['id' => $patch['id'], 'status' => $result ? 'applied' : 'failed'];

            if ($result) {
                $patch['applied'] = true;
                $patch['applied_at'] = date('Y-m-d H:i:s');
            }
        }

        $this->saveRegistry();
        return $results;
    }

    /**
     * Apply a single patch.
     */
    public function applyPatch(array $patch): bool
    {
        return match ($patch['type'] ?? 'file') {
            'file' => $this->applyFilePatch($patch),
            'override' => $this->applyOverridePatch($patch),
            'config' => $this->applyConfigPatch($patch),
            default => false,
        };
    }

    /**
     * Rollback a specific patch by ID.
     */
    public function rollback(string $patchId): bool
    {
        foreach ($this->registry['patches'] as &$patch) {
            if ($patch['id'] === $patchId && ($patch['applied'] ?? false)) {
                if (!empty($patch['original_content'])) {
                    $filePath = base_path($patch['file']);
                    if (file_exists($filePath)) {
                        $content = file_get_contents($filePath);
                        $content = str_replace($patch['replace'], $patch['find'], $content);
                        file_put_contents($filePath, $content);
                    }
                }
                $patch['applied'] = false;
                $patch['rollback_at'] = date('Y-m-d H:i:s');
                $this->saveRegistry();
                return true;
            }
        }

        return false;
    }

    /**
     * Register a new patch.
     */
    public function registerPatch(array $patchData): void
    {
        $patchData['applied'] = false;
        $patchData['created_at'] = date('Y-m-d H:i:s');
        $this->registry['patches'][] = $patchData;
        $this->saveRegistry();
    }

    /**
     * Check the status of a single patch.
     */
    private function checkPatch(array $patch): string
    {
        if ($patch['applied'] ?? false) {
            // Check if the patched code is still in place
            if (($patch['type'] ?? 'file') === 'file') {
                $filePath = base_path($patch['file']);
                if (!file_exists($filePath)) {
                    return 'file_missing';
                }
                $content = file_get_contents($filePath);
                if (str_contains($content, $patch['replace'])) {
                    return 'applied';
                }
                if (str_contains($content, $patch['find'])) {
                    return 'reverted_by_update'; // CodeCanyon update overwrote our fix
                }
                return 'code_changed'; // Neither original nor patched code found
            }
            return 'applied';
        }

        // Not yet applied - check if it's still needed
        if (($patch['type'] ?? 'file') === 'file') {
            $filePath = base_path($patch['file']);
            if (!file_exists($filePath)) {
                return 'file_missing';
            }
            $content = file_get_contents($filePath);
            if (str_contains($content, $patch['find'])) {
                return 'pending'; // Bug still present, patch needed
            }
            if (str_contains($content, $patch['replace'])) {
                return 'already_fixed'; // Developer fixed it themselves
            }
            return 'code_changed'; // Code changed, manual review needed
        }

        return 'pending';
    }

    /**
     * Apply a file-based patch (find/replace).
     */
    private function applyFilePatch(array $patch): bool
    {
        $filePath = base_path($patch['file']);
        if (!file_exists($filePath)) {
            return false;
        }

        $content = file_get_contents($filePath);
        if (!str_contains($content, $patch['find'])) {
            return false; // Original code not found
        }

        $content = str_replace($patch['find'], $patch['replace'], $content);
        file_put_contents($filePath, $content);

        // Create backup
        $backupPath = $filePath . '.patch-backup.' . ($patch['id'] ?? 'unknown');
        copy($filePath, $backupPath);

        return true;
    }

    /**
     * Apply an override patch (register service provider override).
     */
    private function applyOverridePatch(array $patch): bool
    {
        // Override patches work via PatchServiceProvider
        // They don't modify files directly
        return true;
    }

    /**
     * Apply a config patch.
     */
    private function applyConfigPatch(array $patch): bool
    {
        $filePath = base_path($patch['file']);
        if (!file_exists($filePath)) {
            return false;
        }

        $content = file_get_contents($filePath);
        $content = str_replace($patch['find'], $patch['replace'], $content);
        file_put_contents($filePath, $content);

        return true;
    }

    private function loadRegistry(): array
    {
        if (!file_exists($this->registryPath)) {
            return ['version' => '1.0.0', 'patches' => []];
        }

        return json_decode(file_get_contents($this->registryPath), true) ?? ['version' => '1.0.0', 'patches' => []];
    }

    private function saveRegistry(): void
    {
        $this->registry['last_checked'] = date('Y-m-d H:i:s');
        file_put_contents(
            $this->registryPath,
            json_encode($this->registry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
