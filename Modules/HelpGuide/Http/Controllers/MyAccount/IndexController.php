<?php

namespace Modules\HelpGuide\Http\Controllers\MyAccount;

use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index()
    {

      $backRoute = app('router')->getRoutes()->match(app('request')->create(redirect()->back()->getTargetUrl()));

      if( $backRoute->getName() == 'login' ){
       if( Auth::user()->isEmployee() ) return redirect(route('dashboard'));
      }
       
      return view("helpguide::my_account.index");
    }
}
