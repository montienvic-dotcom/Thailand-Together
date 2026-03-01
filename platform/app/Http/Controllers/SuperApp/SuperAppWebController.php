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
        $user = $request->user();
        $cluster = Cluster::active()->where('slug', $cluster)->firstOrFail();

        $apps = $cluster->activeApplications()
            ->withCount('activeModules')
            ->orderBy('sort_order')
            ->get();

        return view('superapp.cluster-home', compact('cluster', 'apps'));
    }

    public function appDetail(Request $request, string $cluster, int $application)
    {
        $cluster = Cluster::active()->where('slug', $cluster)->firstOrFail();
        $app = Application::with(['activeModules' => fn($q) => $q->orderBy('sort_order')])
            ->findOrFail($application);

        $modules = $app->activeModules()->orderBy('sort_order')->get();

        return view('superapp.app-detail', compact('cluster', 'app', 'modules'));
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
