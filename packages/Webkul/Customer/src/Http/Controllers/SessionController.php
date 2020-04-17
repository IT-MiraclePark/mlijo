<?php

namespace Webkul\Customer\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Models\Customer;
use Hash;
use Cookie;

class SessionController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new Repository instance.
     *
     * @return void
    */
    protected $customer;
    
    public function __construct(CustomerRepository $customer)
    {
        $this->middleware('customer')->except(['show','create', 'redirectToProvider', 'handleProviderCallback']);

        $this->_config = request('_config');

        $this->customer = $customer;
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('customer.profile.index');
        } else {
            return view($this->_config['view']);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->validate(request(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! auth()->guard('customer')->attempt(request(['email', 'password']))) {
            session()->flash('error', trans('shop::app.customer.login-form.invalid-creds'));

            return redirect()->back();
        }

        if (auth()->guard('customer')->user()->status == 0) {
            auth()->guard('customer')->logout();

            session()->flash('warning', trans('shop::app.customer.login-form.not-activated'));

            return redirect()->back();
        }

        if (auth()->guard('customer')->user()->is_verified == 0) {
            session()->flash('info', trans('shop::app.customer.login-form.verify-first'));

            Cookie::queue(Cookie::make('enable-resend', 'true', 1));

            Cookie::queue(Cookie::make('email-for-resend', request('email'), 1));

            auth()->guard('customer')->logout();

            return redirect()->back();
        }

        //Event passed to prepare cart after login
        Event::dispatch('customer.after.login', request('email'));

        return redirect()->intended(route($this->_config['redirect']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->guard('customer')->logout();

        Event::dispatch('customer.after.logout', $id);

        return redirect()->route($this->_config['redirect']);
    }
     /**
    * Redirect the user to the Google authentication page.
    *
    *@return \Illuminate\Http\Response
    */

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();

        $existingUser = $this->customer->findOneWhere(['email' => $user->email]);

        if ($existingUser) {

            auth()->guard('customer')->login($existingUser, true);

            return redirect()->route('customer.profile.index');
        } else {
            $data['first_name'] = $user->name;
            $data['email'] = $user->email;
            $data['password'] = Hash::make(str_random(8));
            $data['channel_id'] = core()->getCurrentChannel()->id;

            $customer = $this->customer->create($data);

            auth()->guard('customer')->login($customer, true);

            return redirect()->route('customer.profile.index');
        }
    }
    
}