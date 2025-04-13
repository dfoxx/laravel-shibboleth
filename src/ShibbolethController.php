<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShibbolethController extends Controller
{
    use AuthenticatesUsers;

    public function username()
    {
        return config('shibboleth.identifier_key');
    }

    public function shibboleth(Request $request)
    {
        if (app()->environment() === 'local') {
            return redirect('/');
        } elseif (app()->environment() === 'production') {
            $app_url = config('app.url');
            $handler = $request->server('Shib-Handler');
            return redirect("{$handler}/Login?target={$app_url}");
        }
    }

    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('login');
    }
}
