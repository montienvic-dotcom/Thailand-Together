# Patch Engine Autoloading Note

The Patch Management System is integrated directly into the Laravel application
rather than being autoloaded from this `patches/engine/` directory.

## Where the code lives

| Component | Location | Namespace |
|-----------|----------|-----------|
| PatchManager | `platform/app/Services/Patch/PatchManager.php` | `App\Services\Patch` |
| PatchServiceProvider | `platform/app/Providers/PatchServiceProvider.php` | `App\Providers` |
| PatchApply command | `platform/app/Console/Commands/PatchApply.php` | `App\Console\Commands` |
| PatchCheck command | `platform/app/Console/Commands/PatchCheck.php` | `App\Console\Commands` |
| PatchRollback command | `platform/app/Console/Commands/PatchRollback.php` | `App\Console\Commands` |

## Why not autoload from patches/engine/?

Laravel's PSR-4 autoloading is configured for the `platform/` directory via
`composer.json`. Placing the service classes inside the Laravel `app/` directory
means they are autoloaded automatically without any additional Composer
configuration.

The `patches/` directory at the project root contains only:
- `registry.json` — master list of all patch definitions
- `applied.json` — tracking file for applied patches
- `backups/` — automatic backups created when patches are applied
- `engine/` — legacy reference files (this directory)

## Legacy files

The files in `patches/engine/` (PatchManager.php, Commands/) are the original
prototype implementation with the `ThailandTogether\Patches` namespace. They are
superseded by the Laravel-integrated version described above.
