<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return Api::json(['success' => true, 'authenticated' => Api::actor($request) === 'admin']);
    }

    public function login(Request $request): JsonResponse
    {
        $this->requireFetchHeader($request);
        Api::throttle($request, 'login', 8, 900);

        $hash = config('french.admin_password_hash');
        $password = (string) (Api::body($request)['password'] ?? '');
        if ($hash === '' || $password === '' || ! Hash::check($password, $hash)) {
            usleep(400_000);
            Api::fail('Wrong password.', 401);
        }

        $request->session()->regenerate();
        $request->session()->put('admin', true);

        return Api::json(['success' => true]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->requireFetchHeader($request);
        if ($request->hasSession()) {
            $request->session()->invalidate();
        }

        return Api::json(['success' => true]);
    }

    private function requireFetchHeader(Request $request): void
    {
        if ($request->header('X-Requested-With') !== 'fetch') {
            Api::fail('Bad request.', 403);
        }
    }
}
