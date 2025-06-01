<?php

namespace Modules\HelpGuide\Http\Controllers\Auth;

use Modules\HelpGuide\User;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

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
        $customFields = customFields('user', 'auth_register');

        $customFieldsValidation = [];

        foreach($customFields as $field){
            if( empty( $field['rules'] ) ) continue;
            $customFieldsValidation['custom_'.$field['key']] = $field['rules'];
        }

        $rules = array_merge($customFieldsValidation, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'locale' => ['sometimes', 'in:'.implode(',', array_keys(availableLanguages()))],
        ]);

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Modules\HelpGuide\User
     */
    protected function create(array $data)
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->locale = isset($data['locale']) ? $data['locale'] : setting('default_lang', 'en');
        $user->avatar = $user->defaultAvatar();
        $user->password = Hash::make($data['password']);
        
        $user->save();

        $customFields = customFields('user', 'auth_register');

        foreach($customFields as $field){
            if( $data['custom_'.$field['key']] ){
                $user->updateMeta($field['key'], $data['custom_'.$field['key']]);
            }
        }

        $user->assignRole('customer');

        return $user;
    }
}
