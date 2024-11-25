<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    public function login(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ], [
            'email.required' => 'El nombre de usuario es requerido.',
            'password.required' => 'La contraseña es requerida.',
        ]);
        if ($v->fails())
            return response()->json(['estado' => false, 'errors' => $v->errors()->all()]);

        $result = $this->attemptLogin($request);
        if ($result === true)
            return $this->sendLoginResponse($request);
        else if ($result !== false)
            return $result;

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    // protected function attemptLogin(Request $request)
    // {
    //     if ($request->isAdmin !== "1") {
    //         $usuario = User::where('email', $request->email)->first();
    //         if (!$usuario)
    //             return response()->json(['estado' => false, 'errors' => ['Datos inválidos.']]);
    //         if ($usuario->hasRole('Admin'))
    //             return response()->json(['estado' => false, 'errors' => ['La contraseña es requerida.']]);
    //         Auth::login($usuario);
    //         return true;
    //     }
    //     return $this->guard()->attempt(
    //         $this->credentials($request),
    //         $request->filled('remember')
    //     );
    // }

    protected function authenticated(Request $request, $user)
    {
        $token = $user->createToken('api_auth');
        return response()->json([
            'estado' => true,
            'callback' => 'refresh',
            'auth_token' => $token->plainTextToken
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json(['estado' => false, 'errors' => ['Datos inválidos.']]);
    }
}
