<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Login account and issue access token for user
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $active_version = env("API_VERSION");
        $version = $request->api_version;
        if (!$version || $version !== $active_version) {
            return response()->json(
                [
                    "error" => "Login Failed!",
                    "message" => "Application not properly configured.",
                ],
                401
            );
        }

        $request->validate([
            "email" => 'bail|required|email',
            "password" => 'bail|required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/',
        ]);
        $credentials = $request->only("email", "password");

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $user = Auth::user();

            //revoked all previous token
            DB::table("oauth_access_tokens")
                ->where("user_id", "=", $user->id)
                ->update([
                    "revoked" => true,
                ]);

            //create access token for the user
            $token = $user->createToken($user->username);
            $expiration = $token->token->expires_at->diffInSeconds(
                Carbon::now()
            );

            return response()->json([
                "access_token" => $token->accessToken,
                "token_type" => "Bearer",
                "expires_in" => $expiration,
            ]);
        }
        return response()->json(
            [
                "error" => "Login Failed!",
                "message" => "Account doesn't exist!",
            ],
            401
        );
    }

    /**
     * Logut user and revoked access token
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        //revoked all previous token
        $user->token()->revoke();
        return response()->json([
            "message" => "Logout Success",
        ]);
    }

    public function validateEmail(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' =>
                [
                    'bail',
                    'required',
                    'email',
                    'ends_with:gmail.com',
                    'unique:users,email,' . $request->ignore_id,
                ]
            ],
            [
                'required' => 'Email address is required.',
                'email' => 'Invalid email format.',
                'unique' => 'The email address is already taken.',
                'ends_with' => 'Email address must be a gmail',
            ]
        );

        return response()->json(['valid_email' => true]);
    }

    public function generatePassword()
    {
        $faker = \Faker\Factory::create();
        $random_capital = $faker->regexify('[A-Z]{1}');
        $random_lower = $faker->regexify('[a-z]{1}');
        $random_digit = $faker->randomDigit();
        $random_special = $faker->randomElement(['!', '@', '#', '$', '%', '^', '&', '*']);
        $random_prepend = $faker->regexify('[a-zA-Z0-9]{4}');

        return $random_prepend . $random_special . $random_capital . $random_digit . $random_lower;
    }
}
