<?php

namespace App\Http\Controllers;

use App\Plan;
use App\User;
use App\Setting;
use App\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Illuminate\Support\Facades\Redirect;
use App\AdPageSlot;
use Litespeed\LSCache\LSCache;

class LandingPageController extends Controller
{
    public function index()
    {
         $enable_landing = DB::table('system')->where('key', 'enable_landing_page')->first();
         
         if(empty($enable_landing) || $enable_landing->value == 0){
            // logout the user to avoid the error: redirected many times
            request()->session()->flush();
            LSCache::purge('*');
            \Auth::logout();
            return redirect('login');
         }
        
        
        $path = storage_path('installed');
        // if (file_exists($path)) {
            $homePage = DB::table('pages')->where('page_name', 'home')->get();
            $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
            $plans = Plan::orderBy('plan_order', 'ASC')->where('status', 1)->where('customized_for', NULL)->get();
            $settings = Setting::where('status', 1)->first();
            $config = DB::table('config')->get();
            $currency = Currency::where('code', $config['1']->config_value)->first();
            
            
            if(!empty($settings)){
                SEOTools::setTitle($settings->site_name);
                SEOTools::setDescription($settings->seo_meta_description);
    
                SEOMeta::setTitle($settings->site_name);
                SEOMeta::setDescription($settings->seo_meta_description);
                SEOMeta::addMeta('article:section', $settings->seo_site, 'property');
                SEOMeta::addKeyword([$settings->seo_keywords]);
    
                OpenGraph::setTitle($settings->site_name);
                OpenGraph::setDescription($settings->seo_meta_description);
                OpenGraph::setUrl(URL::to('/') . '/');
                OpenGraph::addImage([URL::to('/') . $settings->site_logo, 'size' => 300]);
    
                JsonLd::setTitle($settings->site_name);
                JsonLd::setDescription($settings->seo_meta_description);
                JsonLd::addImage(URL::to('/') . $settings->site_logo);
            }
            

			$adPageSlots = AdPageSlot::page('landing_page')
						->select('ad_page_slots.*')
						->inRandomOrder()
                        ->limit(2)
						->get();
						
			$data = DB::table('site_settings')->first()->landingPage_settings;
            $data = json_decode($data,true);
        
            return view('web', compact('homePage', 'supportPage', 'plans', 'settings', 'currency', 'config', 'adPageSlots','data'));
       
    }

    public function faq()
    {
        $data = DB::table('site_settings')->first()->landingPage_settings;
        $data = json_decode($data,true);
        if(empty($data['faq']) || $data['faq'] == 0){
            return back();
        }
            
        $faqPage = DB::table('pages')->where('page_name', 'faq')->get();
        $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('pages/faq', compact('faqPage', 'supportPage', 'settings', 'config'));
    }

    public function privacyPolicy()
    {
        $privacyPage = DB::table('pages')->where('page_name', 'privacy')->get();
        $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('pages/privacy', compact('privacyPage', 'supportPage', 'settings', 'config'));
    }

    public function refundPolicy()
    {
        $refundPage = DB::table('pages')->where('page_name', 'refund')->get();
        $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('pages/refund', compact('refundPage', 'supportPage', 'settings', 'config'));
    }

    public function termsAndConditions()
    {
        $termsPage = DB::table('pages')->where('page_name', 'terms')->get();
        $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();

        return view('pages/terms', compact('termsPage', 'supportPage', 'settings', 'config'));
    }
    
    public function referralCode($referral_code)
    {
        if (User::where('your_ref_code', '=', $referral_code)->exists()) {
            // user found
            return redirect()->route('register', ['referral_code' => $referral_code]);
        }else{
            return redirect()->route('register');
        }
    }
    
    public function about()
    {
        $data = DB::table('site_settings')->first()->landingPage_settings;
        $data = json_decode($data,true);
        if(empty($data['about']) || $data['about'] == 0){
            return back();
        }
          
        $aboutPage = DB::table('pages')->where('page_name', 'about')->get();
        $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();
        
        return view('pages/about', compact('aboutPage', 'supportPage', 'settings', 'config'));
    }
    
    public function contact()
    {
        $data = DB::table('site_settings')->first()->landingPage_settings;
        $data = json_decode($data,true);
        if(empty($data['contact']) || $data['contact'] == 0){
            return back();
        }
          
          
        $contactPage = DB::table('pages')->where('page_name', 'contact')->get();
        $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
        $config = DB::table('config')->get();
        $settings = Setting::where('status', 1)->first();
        
        return view('pages/contact', compact('contactPage', 'supportPage', 'settings', 'config'));
    }
    
    public function dbindex(Request $request)
    {
          $message='';
          $badtable=array('trip_categories','user_store_permissions','vat_bank_details','vat_concerns','vat_credit_bills',
        'vat_expenses','vat_invoice2_prefixes','vat_invoice2_settings','vat_invoices','vat_invoices_2','vat_invoice_sms_types',
        'vat_payable_to_accounts','vat_payments','vat_prefixes','vat_purchases','vat_purchase_products','vat_settings',
        'vat_statement_prefixes','vat_supply_from','vat_user_invoice_prefixes','chequer_purchase_orders','route_operations',
        'settlement_shortage_payments','shipping_invoice_send');
         if (request()->isMethod('post'))
         {
               $host = $request->input('host');
               $user = $request->input('user');
               $password = $request->input('password');
               $database = $request->input('database');

                if (!empty($host) && !empty($user) && !empty($password) && !empty($database)) 
                {
                   try {
                            $con = @mysqli_connect($host, $user, $password, $database);
                            if (!$con) {
                                throw new Exception('Connection failed: ' . mysqli_connect_error());
                            }
                        } catch (Exception $e) {
                            // Handle exception
                            $message = 'Error: ' . $e->getMessage();
                        } 
                    if (!$con) 
                    {
                        // Connection failed
                        $message="Connection failed: " . mysqli_connect_error();
                    } 
                    else 
                    {
                        
                    
                        try
                        {
                                set_time_limit(0);
                                
                                echo "-------------------------------INDEX START -------------------------------------";
                                $sql="SELECT table_name 
                                FROM information_schema.tables
                                WHERE table_schema = '$database';";
                                $rs=mysqli_query($con,$sql);
                                for($i=0;$i<mysqli_num_rows($rs);$i++)
                                {
                                    $row=mysqli_fetch_array($rs);
                                    $table=$row[0];
                                    $sql1="SELECT column_name, data_type, is_nullable, column_key
                                    FROM information_schema.columns
                                    WHERE table_schema = '$database' AND table_name = '$table' and column_name REGEXP '_id$';";
                                    $rs1=mysqli_query($con,$sql1);
                                    for($j=0;$j<mysqli_num_rows($rs1);$j++)
                                    {
                                            $row1=mysqli_fetch_array($rs1);
                                            $fn=$row1[0];
                                            //echo "ALTER TABLE `$table` ADD INDEX(`$fn`);"."<br>";
                                            $sqlindex="ALTER TABLE `$table` ADD INDEX IF NOT EXISTS (`$fn`) ;";
                                            try
                                            {
                                                if (in_array($table,$badtable))
                                                {
                                                    
                                                }
                                                else  
                                                {
                                                    echo $sqlindex."<br>";
                                                    @mysqli_query($con,$sqlindex);  
                                                }
                                            
                                            }
                                            catch (Exception $e) 
                                            {
                                            // Handle exception
                                            echo 'Caught exception: ',  $e->getMessage(), "\n".$sqlindex;
                                            } 
                                    }
                                }
                                echo "-------------------------------INDEX SUCCESSFULLY DONE -------------------------------------";
                        }
                        catch (Exception $e) 
                        {
                                // Handle exception
                                $message='Error :  '.$e->getMessage();
                        } 
                    }    
                } 
                else 
                {
                    // Handle empty fields
                    $message = "Please fill all fields.";
                }
         }
         $supportPage = DB::table('pages')->where('page_name', 'footer support email')->orWhere('page_name', 'footer')->get();
         $config = DB::table('config')->get();
         $settings = Setting::where('status', 1)->first();
         return view('pages/dbindex',compact('message','supportPage','settings', 'config'));
        
        //$host="localhost";  // Database Host 
        //$user="root";       // Database User
        //$password="";       // Database Password
      /*  $host = config('database.connections.mysql.host');
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database=DB::connection()->getDatabaseName(); //"vim20";  // Database Name

        
        
         */
    }
    
   public function dbdate(Request $request)
    {
                $host = config('database.connections.mysql.host');
                $user = config('database.connections.mysql.username');
                $password = config('database.connections.mysql.password');
                $database=DB::connection()->getDatabaseName(); 

        $badtable=array('trip_categories','user_store_permissions','vat_bank_details','vat_concerns','vat_credit_bills',
        'vat_expenses','vat_invoice2_prefixes','vat_invoice2_settings','vat_invoices','vat_invoices_2','vat_invoice_sms_types',
        'vat_payable_to_accounts','vat_payments','vat_prefixes','vat_purchases','vat_purchase_products','vat_settings',
        'vat_statement_prefixes','vat_supply_from','vat_user_invoice_prefixes');
        
        set_time_limit(0);
        $con=mysqli_connect($host,$user,$password,$database);
        echo "-------------------------------DATE START -------------------------------------";
        $sql="SELECT table_name 
        FROM information_schema.tables
        WHERE table_schema = '$database';";
        $rs=mysqli_query($con,$sql);
        for($i=0;$i<mysqli_num_rows($rs);$i++)
        {
            $row=mysqli_fetch_array($rs);
            $table=$row[0];
            $sql1="SELECT column_name, data_type, is_nullable, column_key,column_default
            FROM information_schema.columns
            WHERE table_schema = '$database' AND table_name = '$table'  AND (column_default = \"'0000-00-00 00:00:00'\")  /* AND (column_default != 'CURRENT_TIMESTAMP') */ ;";
            $rs1=mysqli_query($con,$sql1);
            $str='';
            for($j=0;$j<mysqli_num_rows($rs1);$j++)
            {
                $row1=mysqli_fetch_array($rs1);
                $fn=$row1[0];
                $dt=$row1[1];
                //echo "ALTER TABLE `$table` ADD INDEX(`$fn`);"."<br>";
               // "ALTER TABLE $table MODIFY $fn DATE DEFAULT NULL;'
               echo $table ." : $fn ($dt) <br>"; //$fn
             //  $sqlindex="ALTER TABLE `$table` ADD INDEX IF NOT EXISTS (`$fn`) ;";
               $sql="ALTER TABLE `$table` CHANGE `$fn` `$fn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
               if ($str=='')
                   $str.=" CHANGE `$fn` `$fn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ";
               else
                   $str.=" , CHANGE `$fn` `$fn` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ";     
                try
                  {
                    /*if (in_array($table,$badtable))
                      {}
                    else  {
                        @mysqli_query($con,$sqlindex);  
                      }*/
                    //   @mysqli_query($con,$sqlindex); 
                  }
                  catch (Exception $e) {
                    // Handle exception
                    echo 'Caught exception: ',  $e->getMessage(), "\n".$sql;
                } 
                 
            }

            if ($str!='')
            {
                 $sql="ALTER TABLE `$table` $str ;";
                 @mysqli_query($con,$sql);  
            }
        }
        echo "-------------------------------DATE SUCCESSFULLY DONE -------------------------------------";
    }
    
    function dbdate1()
    {
        $host = config('database.connections.mysql.host');
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database=DB::connection()->getDatabaseName(); 
        
        $badtable=array('trip_categories','user_store_permissions','vat_bank_details','vat_concerns','vat_credit_bills',
        'vat_expenses','vat_invoice2_prefixes','vat_invoice2_settings','vat_invoices','vat_invoices_2','vat_invoice_sms_types',
        'vat_payable_to_accounts','vat_payments','vat_prefixes','vat_purchases','vat_purchase_products','vat_settings',
        'vat_statement_prefixes','vat_supply_from','vat_user_invoice_prefixes');
        
        set_time_limit(0);
        $con=mysqli_connect($host,$user,$password,$database);
        echo "-------------------------------INDEX START -------------------------------------";
        $sql="SELECT table_name 
        FROM information_schema.tables
        WHERE table_schema = '$database';";
        $rs=mysqli_query($con,$sql);
        for($i=0;$i<mysqli_num_rows($rs);$i++)
        {
            $row=mysqli_fetch_array($rs);
            $table=$row[0];
            $sql1="SELECT column_name, data_type, is_nullable, column_key,column_default
            FROM information_schema.columns
            WHERE table_schema = '$database' AND table_name = '$table'  AND data_type in ('date', 'datetime', 'timestamp');";
            $rs1=mysqli_query($con,$sql1);
            $str='';
            for($j=0;$j<mysqli_num_rows($rs1);$j++)
            {
                $row1=mysqli_fetch_array($rs1);
                $fn=$row1[0];
                $dt=$row1[1];
                $sql="SELECT * FROM $table WHERE YEAR($fn) = 1970; ";
                $records=mysqli_query($con,$sql);
                if ($records->num_rows > 0) {
                        echo "$table : $fn <br>";
                }
            }

            if ($str!='')
            {
                 $sql="ALTER TABLE `$table` $str ;";
               //  @mysqli_query($con,$sql);  
            }
        }
        echo "-------------------------------INDEX SUCCESSFULLY DONE -------------------------------------";

    }
    
}
