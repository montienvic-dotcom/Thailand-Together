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

    public function applications()
    {
        $apps = Application::active()
            ->withCount('activeModules')
            ->orderBy('sort_order')
            ->get();

        return view('admin.applications.index', compact('apps'));
    }

    public function applicationDetail(int $application)
    {
        $app = Application::with(['activeModules' => fn($q) => $q->orderBy('sort_order')])
            ->findOrFail($application);

        return view('admin.applications.show', compact('app'));
    }

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
        return view('admin.permissions.roles', compact('roles'));
    }

    public function apiReference()
    {
        return view('admin.api-reference');
    }

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
