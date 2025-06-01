<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;

use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Entities\Role;

class UserController extends Controller
{

    /* To remove */
    public function customerProfile(Request $request, $id)
    {   
        $user = User::findOrFail($id);
        // $this->authorize('view', $user);
        return view('helpguide::dashboard.customers.profile', ['user' => $user]);
    }

    public function employeeProfile(Request $request, $id)
    {   
        $user = User::findOrFail($id);
        // $this->authorize('view', $user);
        return view('helpguide::dashboard.employees.profile', ['user' => $user]);
    }
    /* End to remove */

    public function show($id)
    {
      $user = User::findOrFail($id);
      // $this->authorize('view', $user);
      return new UserResource($user);
    }
    
    public function list()
    {
        // $this->authorize('viewAny', User::class);
        $user = User::orderBy('id', 'desc')->paginate(500);
        return UserResource::collection($user);
    }

    public function customersList()
    {
        // $this->authorize('viewAny', User::class);
        $user = User::orderBy('id', 'desc')->paginate(500);
        return UserResource::collection($user);
    }

    public function store(Request $request)
    {
        // $this->authorize('updateEmployee', User::class);
        $validatedData = Validator::make($request->all(), [
            'fields.user_id' => ['required', 'integer'],
            'fields.name' => ['required', 'string'],
            'fields.email' => ['required', 'string', 'email'],
            'fields.role.value' => ['required', 'integer'],
        ],[
            'fields.user_id.required' => __('The user account id field is required'),
            'fields.name.required' => __('The name field can not be empty'),
            'fields.email.required' => __('The email field can not be empty'),
            'fields.role.value.required' => __('Please choose a role'),
        ]);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        $fields = $request->input('fields');

        // Check if the user account exists
        if(!$user = User::find($fields['user_id'])){
            return ['status' => 'fail', "messages" => [__('The user account could not be found')]];
        }

        // if the email has been changed make a validation
        if($user->email != $fields['email']){
            $validatedData = Validator::make($request->all(),
                ['fields.email' => ['required', 'string', 'unique:users,email', 'email'],],
                ['fields.email.unique' => __('E-mail address you entered is already in use on another user account')]
            );
            // Check errors
            if ($validatedData->fails()) {
                return ['status' => 'fail', "messages" => $validatedData->errors()];
            }

            // looks ok 
            $user->email = $fields['email'];
        }


        // Current User can not change his role
        if($user->id != Auth::id()){
            // Make sure the role is exists 
            if(!$role = Role::find($fields['role']['value'])){
                return ['status' => 'fail', "messages" => [__('The role you choose does not exists')]];
            }
            // each user account has one role, remove the old one and assign the new one
            $user->syncRoles([$role->name]);       
        }

        // Assign all other data
        $user->name = $fields['name'];
        
        // Check if the password has been set and validate it
        if($fields['password']){
            $validatedData = Validator::make($request->all(),
                ['fields.password' => ['required', 'string', 'min:8', 'confirmed']],
            );
            // Check errors
            if ($validatedData->fails()) {
                return ['status' => 'fail', "messages" => $validatedData->errors()];
            }

            // looks ok 
            $user->password = Hash::make($fields['password']);
        }
        
        if(!$user->save()){
            return ['status' => 'fail', "messages" => [__('Failed to update the user account, please try again')]];
        }

        return [
            'status' => 'ok',
            'message' => __('Changes has been saved'),
            'data' => new UserResource($user)
        ];
    }

    public function storeCustomer(Request $request)
    {
        // $this->authorize('updateCustomer', User::class);
        $validatedData = Validator::make($request->all(), [
            'user_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
        ],[
            'user_id.required' => __('The user account id field is required'),
            'name.required' => __('The name field can not be empty'),
            'email.required' => __('The email field can not be empty'),
        ]);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        // Check if the user account exists
        if(!$user = User::find($request->input('user_id'))){
            return ['status' => 'fail', "messages" => [__('The user account could not be found')]];
        }

        // if the email has been changed make a validation
        if($user->email != $request->input('email')){
            $validatedData = Validator::make($request->all(),
                ['email' => ['required', 'string', 'unique:users,email', 'email'],],
                ['email.unique' => __('E-mail address you entered is already in use on another user account')]
            );

            // Check errors
            if ($validatedData->fails()) {
                return ['status' => 'fail', "messages" => $validatedData->errors()];
            }

            // looks ok 
            $user->email = $request->input('email');
        }

        // Make sure the role is exists 
        if(!$role = Role::where('name', 'customer')->first()){
            return ['status' => 'fail', "messages" => [__('Role "customer" does not exists')]];
        }
            
        // each user account has one role, remove the old one and assign the new one
        $user->syncRoles([$role->name]);

        // Assign all other data
        $user->name = $request->input('name');
        
        // Check if the password has been set and validate it
        if($request->input('password')){
            $validatedData = Validator::make($request->all(),
                ['password' => ['required', 'string', 'min:8', 'confirmed']],
            );
            // Check errors
            if ($validatedData->fails()) {
                return ['status' => 'fail', "messages" => $validatedData->errors()];
            }

            // looks ok 
            $user->password = Hash::make($request->input('password'));
        }
        
        if(!$user->save()){
            return ['status' => 'fail', "messages" => [__('Failed to update the user account, please try again')]];
        }

        return [
            'status' => 'ok',
            'message' => __('Changes has been saved'),
            'data' => new UserResource($user)
        ];
    }

    public function create(Request $request)
    {
        // $this->authorize('updateEmployee', User::class);
        $validatedData = Validator::make($request->all(), [
            'fields.name' => ['required', 'string'],
            'fields.email' => ['required', 'string', 'unique:users,email', 'email'],
            'fields.role.value' => ['required', 'integer'],
            'fields.password' => ['required', 'string', 'min:8', 'confirmed'],
        ],[
            'fields.name.required' => __('The name field can not be empty'),
            'fields.email.required' => __('The email field can not be empty'),
            'fields.role.value.required' => __('Please choose a role'),
            'fields.email.unique' => __('E-mail address you entered is already in use on another user account')
        ]);
        
        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        $fields = $request->input('fields');

        if(!$role = Role::find($fields['role']['value'])){
            return ['status' => 'fail', "messages" => [__('The role you choose does not exists')]];
        }

        $user = new User();
        $user->name = $fields['name'];
        $user->email = $fields['email'];
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->avatar = $user->defaultAvatar();
        $user->password = Hash::make($fields['password']);

        if(!$user->save()){
            return ['status' => 'fail', "messages" => [__('Failed to create the user account, please try again')]];
        }

        $user->syncRoles([$role->name]);
        
        return [
            'status' => 'ok',
            'message' => __('Changes has been saved'),
            'data' => new UserResource($user)
        ];
    }

    public function createCustomer(Request $request)
    {
        // $this->authorize('updateCustomer', User::class);
        $validatedData = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'unique:users,email', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],[
            'name.required' => __('The name field can not be empty'),
            'email.required' => __('The email field can not be empty'),
            'email.unique' => __('E-mail address you entered is already in use on another user account')
        ]);
        
        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        if(!$role = Role::where('name', 'customer')->first()){
            return ['status' => 'fail', "messages" => [__('Role "customer" does not exists')]];
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->avatar = $user->defaultAvatar();
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->password = Hash::make($request->input('password'));

        if(!$user->save()){
            return ['status' => 'fail', "messages" => [__('Failed to create the user account, please try again')]];
        }

        $user->syncRoles([$role->name]);
        
        return [
            'status' => 'ok',
            'message' => __('Changes has been saved'),
            'data' => new UserResource($user)
        ];
    }

    public function addnotes(Request $request)
    {
        $user = User::findOrFail($request->input('userid'));
        // $this->authorize('updateCustomer', User::class);
        $user->notes = $request->input('notes');
        if ($user->save()) {return new UserResource($user);}
    }

    public function destroy(Request $request)
    {
        // $this->authorize('delete', User::class);
        try{
            $user = User::findOrFail($request->user_id);
        }catch(ModelNotFoundException $e){
            return response()->json(['status' => 'fail', 'message' => __('The selected account could not be found!')], 422);
        }

        if($request->user_id == Auth::id()){
            return response()->json(['status' => 'fail', 'message' => __('The current logged in user account can not be deleted')], 422);
        }

        if ($user->delete()) return new UserResource($user);
        return response()->json(['status' => 'fail', 'message' => __('Failed to delete the selected user account, Please try again')],422);
    }
}
