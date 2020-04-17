<?php

namespace Webkul\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Socialite;
use Webkul\Customer\Models\Customer;

class GoogleController extends Controllers
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        //if user still login redirect to home
        if (Auth::check()){
            return redirect('/');
        }

        $oauthUser = Socialite::driver('google')->user();
        $user = User::where('google_id', $oauthUser->id)->first();
        if($user) {
            Auth::loginUsingId($user->id);
            return redirect('/');
        } else {
            $newUser = User::create([
                'name' => $oauthuser->name,
                'email' => $oauthUser->email,
                'google_id' => $oauthUser->id,
                //password unused
                'password' => bcrypt('$oauthUser->token'),
            ]);
            Auth::login($newUser);
            return redirect('/');
        }
    }
}