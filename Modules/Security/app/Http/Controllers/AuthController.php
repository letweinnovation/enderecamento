<?php

namespace Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    private function getRedirectUri(): string
    {
        return 'https://dsv-enderecamento.gtiplug.com.br/auth/callback';
    }

    public function devLogin()
    {
        if (config('app.env') !== 'local') {
            abort(403, 'Desenvolvimento apenas.');
        }

        $userInfo = [
            'email' => 'admin@letwe.com.br',
            'name' => 'Admin Dev',
            'picture' => 'https://ui-avatars.com/api/?name=Admin+Dev',
        ];

        $this->loginUser($userInfo);

        return redirect('/');
    }

    public function login()
    {
        $clientId = config('services.google.client_id');

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $this->getRedirectUri(),
            'response_type' => 'code',
            'scope' => 'email profile openid',
            'prompt' => 'select_account',
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/auth?'.http_build_query($params);

        return view('security::login', compact('authUrl'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function handleCallback(Request $request)
    {
        $code = $request->input('code');
        if (! $code) {
            return redirect()->route('login')->with('error', 'Código de autorização não fornecido.');
        }

        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $response = Http::post($tokenUrl, [
            'code' => $code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => $this->getRedirectUri(),
            'grant_type' => 'authorization_code',
        ]);

        $tokenData = $response->json();

        if (isset($tokenData['access_token'])) {
            $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token='.$tokenData['access_token'];
            $userInfo = Http::get($userInfoUrl)->json();

            if (isset($userInfo['email']) && strpos($userInfo['email'], '@letwe.com.br') !== false) {
                $this->loginUser($userInfo);

                return redirect('/');
            } else {
                return redirect()->route('login')->with('error', 'Acesso permitido apenas para e-mails @letwe.com.br');
            }
        }

        return redirect()->route('login')->with('error', 'Falha na autenticação com Google.');
    }

    private function loginUser(array $userInfo): void
    {
        $user = \App\Models\User::updateOrCreate(
            ['google_email' => $userInfo['email']],
            [
                'google_name' => $userInfo['name'],
                'google_picture' => $userInfo['picture'],
                'last_login' => now(),
            ]
        );

        if ($user) {
            Auth::login($user);
        }
    }
}
