<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles user registration. Modified to require email
    | verification and to prevent automatic login after registration.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'        => ['required', 'string', 'max:255'],
            'student_id'  => ['required', 'string', 'max:50', 'unique:users'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'       => $data['name'],
            'student_id' => $data['student_id'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
        ]);
    }

    /**
     * Override the default register logic to disable auto-login
     * and require email verification.
     */
    public function register(Request $request)
    {
        // Validate incoming request
        $this->validator($request->all())->validate();

        // Create user
        $user = $this->create($request->all());

        // Send verification email
        event(new Registered($user));

        // DO NOT log the user in automatically
        // $this->guard()->login($user);

        // Redirect to verification notice page
        return redirect()->route('verification.notice')
            ->with('message', 'Your account was created. Please check your email to verify your account.');
    }
}
