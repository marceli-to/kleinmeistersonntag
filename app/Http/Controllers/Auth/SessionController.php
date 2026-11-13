<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\Login;
use App\Actions\Auth\Logout;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
	public function create(): Response
	{
		return Inertia::render('Auth/Login');
	}

	public function store(LoginRequest $request, Login $action): RedirectResponse
	{
		$action->execute($request, $request->validated('email'), $request->validated('password'));

		return redirect()->intended('/');
	}

	public function destroy(Request $request, Logout $action): RedirectResponse
	{
		$action->execute($request);

		return redirect('/');
	}
}
