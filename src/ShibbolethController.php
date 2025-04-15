<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShibbolethController extends Controller
{
    public function __invoke(Request $request)
    {
        if (app()->environment('local')) {
            return redirect('/');
        }

        $app_url = config('app.url');
        $handler = $request->server('Shib-Handler');
        return redirect("{$handler}/Login?target={$app_url}");
    }
}
