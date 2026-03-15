<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\App\Application;
use App\Models\Auth\Group;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Models\Global\Country;
use App\Models\Integration\ApiProvider;
use App\Services\Cluster\ClusterManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminWebController extends Controller
{
    public function __construct(
        private ClusterManager $clusterManager,
    ) {}

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $data = [
            'admin_level' => $this->getAdminLevel($user),
            'countries' => Country::active()->count(),
            'clusters' => Cluster::active()->count(),
            'total_users' => User::active()->count(),
            'applications' => Application::active()->count(),
            'api_providers' => ApiProvider::active()->count(),
        ];

        return view('admin.dashboard', $data);
    }

    // ── Applications ──

    public function applications()
    {
        $apps = Application::withCount('activeModules')
            ->orderBy('sort_order')
            ->get();

        return view('admin.applications.index', compact('apps'));
    }

    public function applicationDetail(int $application)
    {
        $app = Application::with([
            'modules' => fn($q) => $q->orderBy('sort_order'),
            'clusters' => fn($q) => $q->orderBy('sort_order'),
        ])->findOrFail($application);

        return view('admin.applications.show', compact('app'));
    }

    // ── Users ──

    public function users(Request $request)
    {
        $query = User::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $users = $query->with('groups', 'roles')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $groups = Group::orderBy('sort_order')->get();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'groups', 'roles'));
    }

    public function userDetail(int $user)
    {
        $user = User::with('groups', 'roles')->findOrFail($user);
        $groups = Group::orderBy('sort_order')->get();
        $roles = Role::all();
        $clusters = Cluster::active()
            ->with(['applications' => fn($q) => $q->where('cluster_application.is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.users.show', compact('user', 'groups', 'roles', 'clusters'));
    }

    // ── API Providers ──

    public function apiProviders()
    {
        $providers = ApiProvider::withCount('credentials')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('admin.api-providers.index', compact('providers'));
    }

    public function apiProviderDetail(int $provider)
    {
        $provider = ApiProvider::with([
            'credentials' => fn($q) => $q->with('country', 'cluster'),
        ])->findOrFail($provider);

        $countries = Country::active()->get();
        $clusters = Cluster::active()->get();

        return view('admin.api-providers.show', compact('provider', 'countries', 'clusters'));
    }

    // ── Clusters & Countries ──

    public function clusters()
    {
        $countries = Country::with(['clusters' => fn($q) => $q->withCount('applications')->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.clusters.index', compact('countries'));
    }

    public function clusterDetail(int $cluster)
    {
        $cluster = Cluster::with([
            'country',
            'applications' => fn($q) => $q->orderBy('sort_order'),
        ])->findOrFail($cluster);

        $allApps = Application::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.clusters.show', compact('cluster', 'allApps'));
    }

    // ── Permissions ──

    public function permissions()
    {
        return view('admin.permissions.index');
    }

    public function permissionUsers()
    {
        $users = User::active()->paginate(20);
        return view('admin.permissions.users', compact('users'));
    }

    public function permissionGroups()
    {
        $groups = Group::withCount('users')->orderBy('sort_order')->get();
        return view('admin.permissions.groups', compact('groups'));
    }

    public function permissionRoles()
    {
        $roles = Role::withCount('users')->get();
        $permissions = \App\Models\Auth\Permission::orderBy('category')->orderBy('name')->get();
        return view('admin.permissions.roles', compact('roles', 'permissions'));
    }

    // ── Journeys ──

    public function journeys()
    {
        $journeys = DB::table('journey')
            ->leftJoin('clusters', 'journey.cluster_id', '=', 'clusters.id')
            ->select('journey.*', 'clusters.name as cluster_name')
            ->orderBy('journey.journey_code')
            ->paginate(20);

        return view('admin.journeys.index', compact('journeys'));
    }

    // ── Merchants ──

    public function merchants()
    {
        $merchants = DB::table('merchant')
            ->leftJoin('clusters', 'merchant.cluster_id', '=', 'clusters.id')
            ->select('merchant.*', 'clusters.name as cluster_name')
            ->orderBy('merchant.merchant_code')
            ->paginate(20);

        $clusters = Cluster::active()->orderBy('sort_order')->get();

        return view('admin.merchants.index', compact('merchants', 'clusters'));
    }

    // ── Reference ──

    public function apiReference()
    {
        return view('admin.api-reference');
    }

    public function roadmap()
    {
        return view('admin.roadmap');
    }

    // ── Auth ──

    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    private function getAdminLevel($user): string
    {
        if ($user->isGlobalAdmin()) return 'global';

        $countryId = $this->clusterManager->countryId();
        if ($countryId && $user->isCountryAdmin($countryId)) return 'country';

        $clusterId = $this->clusterManager->currentId();
        if ($clusterId && $user->isClusterAdmin($clusterId)) return 'cluster';

        return 'none';
    }
}
