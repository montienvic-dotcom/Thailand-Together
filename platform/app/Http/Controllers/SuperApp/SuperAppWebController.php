<?php

namespace App\Http\Controllers\SuperApp;

use App\Http\Controllers\Controller;
use App\Models\App\Application;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use App\Services\Cluster\ClusterManager;
use App\Services\Permission\PermissionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAppWebController extends Controller
{
    public function __construct(
        private ClusterManager $clusterManager,
        private PermissionResolver $permissionResolver,
    ) {}

    public function landing()
    {
        $countries = Country::active()
            ->with(['activeClusters' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('superapp.landing', compact('countries'));
    }

    public function clusterHome(Request $request, string $cluster)
    {
        $cluster = Cluster::active()->where('slug', $cluster)->firstOrFail();

        $apps = $cluster->activeApplications()
            ->withCount('activeModules')
            ->orderBy('sort_order')
            ->get();

        $menuApps = $this->getMenuApps($cluster, $request->user());

        return view('superapp.cluster-home', [
            'cluster' => $cluster,
            'apps' => $apps,
            'currentCluster' => $cluster,
            'menuApps' => $menuApps,
        ]);
    }

    public function appDetail(Request $request, string $cluster, int $application)
    {
        $cluster = Cluster::active()->where('slug', $cluster)->firstOrFail();
        $app = Application::with(['activeModules' => fn($q) => $q->orderBy('sort_order')])
            ->findOrFail($application);

        $modules = $app->activeModules()->orderBy('sort_order')->get();
        $menuApps = $this->getMenuApps($cluster, $request->user());

        return view('superapp.app-detail', [
            'cluster' => $cluster,
            'app' => $app,
            'modules' => $modules,
            'currentCluster' => $cluster,
            'menuApps' => $menuApps,
            'activeApp' => $app,
        ]);
    }

    /**
     * Get menu-visible apps for a cluster, filtered by user permissions.
     */
    private function getMenuApps(Cluster $cluster, $user = null)
    {
        $apps = $cluster->activeApplications()
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->get();

        if ($user && !$user->isGlobalAdmin()) {
            $apps = $apps->filter(function ($app) use ($user, $cluster) {
                return $this->permissionResolver->canAccessApp($user, $cluster->id, $app->id);
            })->values();
        }

        return $apps;
    }

    public function apiDocs()
    {
        return view('superapp.api-docs');
    }

    public function guide()
    {
        return view('superapp.guide');
    }

    public function loginForm()
    {
        return view('superapp.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $redirect = $request->query('redirect', '/');
            return redirect($redirect);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
