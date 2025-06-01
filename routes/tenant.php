<?php

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\CommonReportController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;
use App\Http\Controllers\Chequer\ChequeWriteController;
use Modules\MyHealth\Http\Controllers\SugerReadingController;
use Modules\MyHealth\Http\Controllers\DoctorController;
use Modules\MyHealth\Http\Controllers\MedicationController;
use Modules\MPCS\Http\Controllers\Form9ASettingsController;
use App\Services\DetectDatabaseChangesService;

include_once('install_r.php');

Route::middleware(['web', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class, ScopeSessions::class])->group(function () {
     // Pages
Route::get('/index', 'LandingPageController@index')->name('home-locale');
Route::get('faq', 'LandingPageController@faq')->name('faq');
Route::get('about-us', 'LandingPageController@about')->name('about');
Route::get('contact-us', 'LandingPageController@contact')->name('contact');
Route::get('support', 'LandingPageController@support')->name('support');
Route::get('privacy-policy', 'LandingPageController@privacyPolicy')->name('privacy.policy');
Route::get('terms-and-conditions', 'LandingPageController@termsAndConditions')->name('terms.and.conditions');
Route::get('refund-policy', 'LandingPageController@refundPolicy')->name('refund.policy');
Route::match(['get', 'post'], 'dbindex', 'LandingPageController@dbindex')->name('dbindex');
Route::match(['get', 'post'], 'dbdate', 'LandingPageController@dbdate')->name('dbdate');

Route::get('get-random-ad/{id}', 'ProfileController@getRandomAd')->name('random.ad');

Route::get('/images/{filename}', 'ImageController@show')->name('image.show');

Route::get('/products/adjust', 'ProductController@adjustStock');

Route::get('/discounts/adjust', '\Modules\Petro\Http\Controllers\SettlementController@adjustDiscounts');

Route::get('/correct-stock-accs', 'AccountController@corectStockAccounts');

Route::get('/transfer-postdated', 'AccountController@transferPostDatedCheques');

Route::get('/restore-payments', 'AccountController@restorePayments');

Route::get('/restore-settlement-payments', 'AccountController@restoreSettlementPayments');


Route::post('process/verify', 'App\Http\Controllers\UsersOPTController@checkVerification');
Route::get('resent-opt-code', 'App\Http\Controllers\UsersOPTController@resendOpt');

 Route::get('user/list', 'ManageUserController@list')->name('user.list');

Route::post('reCAPCHA/install', 'ManageUserController@reCapcha_setting');


// include_once('install_r.php');

Route::get('/clear_cache', function() {

    try {

        Artisan::call('cache:clear');

        Artisan::call('view:clear');

        Artisan::call('config:clear');

        Artisan::call('route:clear');

        $output = [

            'success' => true,

            'msg' => __('lang_v1.success')

        ];

    } catch (\Exception $e) {

        Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

        $output = [

            'success' => false,

            'msg' => __('messages.something_went_wrong')

        ];

    }

    return $output;

    // return what you want

});

Route::post('fetch/AccountName', 'ContactGroupController@FetchAccount');

Route::middleware(['IsInstalled', 'bootstrap', 'web', 'language', 'lscache:no-cache'])->group(function () {

    Route::get('/', function () {

        return redirect('/index');

    });

    // Route::get('/', [

    //     'as' => 'login',

    //     'uses' => 'Auth\LoginController@showLoginForm'

    // ]);
    
    Route::get('/get_loan_data', 'ShowCustomerLoansController@getLoanData')->name("show_customer_loans.getloandata");
    Route::get('/show_customer_loans', 'ShowCustomerLoansController@index')->name("show_customer_loans.index");
    Route::get('/show_customer_loans/{id}/edit', 'ShowCustomerLoansController@edit')->name("show_customer_loans.edit");
    Route::put('/show_customer_loans/{id}/update', 'ShowCustomerLoansController@update')->name("show_customer_loans.update");
    Route::delete('/show_customer_loans/{id}', 'ShowCustomerLoansController@destroy')->name("show_customer_loans.destroy");

    Route::get('taxonomies-ajax-index-page', 'TaxonomyController@getTaxonomyIndexPage');

     Route::get('taxonomies-edit-auto/{id}', 'TaxonomyController@edit_device');
     Route::put('taxonomies_device/{id}', 'TaxonomyController@update_device');
  
    Route::resource('taxonomies', 'TaxonomyController');

    Route::post('/register/member', 'MemberRegisterController@store')->name('business.member_register');

    Route::post('/login/member', 'Auth\MemberLoginController@memberLogin');

    Route::post('/member/forgot-password', 'Auth\MemberForgotPasswordController@sendResetLinkEmail')->name('member_password.email');

    Route::post('/member/reset-password', 'Auth\MemberResetPasswordController@showResetForm')->name('member_password.reset');

    Route::post('/login/employee', 'Auth\EmployeeLoginController@employeeLogin');

    Route::post('/employee/forgot-password', 'Auth\EmployeeForgotPasswordController@sendResetLinkEmail')->name('employee_password.email');

    Route::post('/employee/reset-password', 'Auth\EmployeeResetPasswordController@showResetForm')->name('employee_password.reset');

    Route::get('/products/stock-history/{id}', 'ProductController@productStockHistory');
    
    Route::get('/products/disable/{id}', 'ProductController@disable');
    Route::post('/products/disable', 'ProductController@saveDisable');

    Route::get('/products/stock-history/get-stores/{id}', 'ProductController@productGetStore');

    Route::get('/products/tank-stock-history/{id}', 'ProductController@productTankStockHistory');

    Route::get('/products/stock-history/get-tanks/{id}', 'ProductController@productGetTank');

    Route::post('/register/customer', 'CustomerController@store')->name('business.customer_register');

    Route::get('/logout/customer', 'Auth\CustomerLoginController@logout');

    Route::post('/login/customer', 'Auth\CustomerLoginController@customerLogin');

    Route::post('/customer/forgot-password', 'Auth\CustomerForgotPasswordController@sendResetLinkEmail')->name('customer_password.email');

    Route::post('/customer/reset-password', 'Auth\CustomerResetPasswordController@showResetForm')->name('customer_password.reset');

    Route::get('/logout/agent', 'Auth\AgentLoginController@logout');

    Route::post('/login/agent', 'Auth\AgentLoginController@agentLogin');

    Route::post('/agent/forgot-password', 'Auth\AgentForgotPasswordController@sendResetLinkEmail')->name('agent_password.email');

    Route::post('/agent/reset-password', 'Auth\AgentResetPasswordController@showResetForm')->name('agent_password.reset');

    Auth::routes();

    Route::get('/business/get-business-by-category', 'BusinessController@getBusinessByCategory');

    Route::get('/business/register', 'BusinessController@getRegister')->name('business.getRegister');

    Route::post('/business/register', 'BusinessController@postRegister')->name('business.postRegister');

    Route::post('/business/visitor-register', 'BusinessController@postVisitorRegister')->name('business.postVisitorRegister');

    Route::post('/business/patient-register', 'BusinessController@postPatientRegister')->name('business.postPatientRegister');

    Route::post('/business/agent-register', 'BusinessController@postAgentRegister')->name('business.postAgentRegister');

    Route::post('/business/register/check-username', 'BusinessController@postCheckUsername')->name('business.postCheckUsername');

    Route::post('/business/register/check-email', 'BusinessController@postCheckEmail')->name('business.postCheckEmail');

    Route::post('/business/register/check-username-agent', 'BusinessController@postCheckUsernameAgent')->name('business.postCheckUsernameAgent');

    Route::post('/business/register/check-email-agent', 'BusinessController@postCheckEmailAgent')->name('business.postCheckEmailAgent');

    Route::get('/invoice/{token}', 'SellPosController@showInvoice')

        ->name('show_invoice');

    Route::get('/pump-operator/login', 'Auth\PumpOperatorLoginController@login');
    
    Route::get('/module-subscription', '\Modules\Superadmin\Http\Controllers\SubscriptionController@getModuleSubscription')->name('module.subscription');


    Route::post('/pump-operator/login', 'Auth\PumpOperatorLoginController@postLogin');

    Route::get('/pump-operator/logout', 'Auth\PumpOperatorLoginController@logout');

    Route::get('/property-user/login', 'Auth\PropertyUserLoginController@login');

    Route::post('/property-user/login', 'Auth\PropertyUserLoginController@postLogin');

    Route::get('/property-user/logout', 'Auth\PropertyUserLoginController@logout');

    Route::post('/login/visitor', 'Auth\VisitorLoginController@visitorLogin');

});

Route::middleware(['web', 'auth:customer', 'language'])->group(function () {

    Route::post('/customer/get-profile', 'CustomerController@updateProfile');

    Route::get('/customer/profile', 'CustomerController@getProfile');

    Route::post('/customer/update-password', 'CustomerController@updatePassword')->name('customer.updatePassword');

    Route::get('/customer/home/get-totals', 'CustomerController@getTotals');

    Route::get('/customer/home', 'CustomerController@index')->name('customer-home');

    Route::get('/customer/order/get_upload_document_image/{business_id}', 'Ecom\EcomCustomerOrderController@uploadDocumentImage');

    Route::post('/customer/order/save_upload_document_image', 'Ecom\EcomCustomerOrderController@uploadDocumentImageSave');

    Route::get('/customer/order/lists', 'Ecom\EcomCustomerOrderController@getOrders');

    Route::get('/customer/order/get-image/{id}', 'Ecom\EcomCustomerOrderController@getImage');

    Route::get('/customer/order/uploaded', 'Ecom\EcomCustomerOrderController@getUploadedOrders');

    Route::get('/make-the-bill/{id}', 'Ecom\EcomCustomerOrderController@makeTheBill');

    Route::get('/customer/confirm-order/{id}', 'Ecom\EcomCustomerOrderController@confirmOrder');

    Route::get('/customer/pos/create', 'Ecom\EcomCustomerOrderController@createPos');

    Route::post('/customer/order/save-payment', 'Ecom\EcomCustomerOrderController@savePayment');

    Route::get('/customer/order/add-payment/{transaction_id}', 'Ecom\EcomCustomerOrderController@addPayment');

    Route::resource('/customer/order', 'Ecom\EcomCustomerOrderController');

    Route::get('/customer/details/ledger', 'Ecom\ContactController@getLedger');

    Route::get('/customer/details/list-sucrity-deposit', 'Ecom\ContactController@listSecurityDeposit');

    Route::resource('/customer/details', 'Ecom\ContactController');

});

Route::middleware(['web', 'auth:agent'])->group(function () {

    Route::post('/agent/update-password', 'AgentController@updatePassword');

    Route::post('/agent/get-profile', 'AgentController@updateProfile');

    Route::get('/agent/profile', 'AgentController@getProfile');

    Route::get('agent/home', 'AgentController@home');

    Route::resource('agent/', 'AgentController');

});

Route::group(['middleware' => 'auth:employee,web', 'prefix' => 'employee', 'namespace' => '\Modules\HR\Http\Controllers'], function () {

    Route::get('/home', 'EmployeeController@home')->name('employee-home');

    Route::get('/attendance', 'AttendanceController@getEmployeeAttendance');

    Route::get('/salaries', 'PayrollPaymentController@getEmployeeSalariesPayment');

    Route::get('/leave-request', 'LeaveRequestController@getEmployeeLeaveRequest');

});

Route::group(['middleware' => 'auth:member,web', 'prefix' => 'member', 'namespace' => '\Modules\Member\Http\Controllers'], function () {

    Route::get('/home', 'MemberController@home')->name('member-home');

    Route::get('/profile', 'MemberController@getProfile');

    Route::put('/profile', 'MemberController@updateProfile');

    Route::get('/suggestions/change-status/{id}', 'SuggestionController@getUpdateStatus');

    Route::post('/suggestions/change-status/{id}', 'SuggestionController@postUpdateStatus');

    Route::resource('/suggestions', 'SuggestionController');

});

Route::group(['middleware' => 'auth:visitor,web', 'prefix' => 'visitor', 'namespace' => '\Modules\Visitor\Http\Controllers'], function () {

    Route::get('/home', 'VisitController@home')->name('visitor-home');

});

Route::get('default-district/get-drop-down', 'DefaultDistrictController@getDistrictDropDown');

Route::get('default-district/getTowns', 'DefaultDistrictController@getTownsByDistrict');

Route::resource('default-district', 'DefaultDistrictController');

Route::resource('default-town', 'DefaultTownController');

Route::group(['namespace' => '\Modules\Visitor\Http\Controllers'], function () {

    Route::get('/visitor/{business_id}/{location_id}', 'VisitorController@createVisitor');

    Route::post('/visitor/store', 'VisitorController@saveVisitor');

    Route::get('/visitor/get-detail-if-registered', 'VisitorController@getDetailIfRegistered');

    Route::post('/visitor/register', 'VisitorRegistrationController@selfRegistration');

});

//Routes for authenticated users only

Route::middleware(['App\Http\Middleware\AutoLogoutMiddleware', 'IsInstalled', 'auth:customer,web', 'SetSessionData', 'DayEnd', 'language', 'timezone', 'bootstrap','isVerified'])->group(function () {

    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('/home', 'HomeController@index')->name('home');
    
    Route::get('/dashboard-logistics', 'DashboardLogisticsController@index')->name('home');
    
    Route::get('/home/not-subscribed', 'HomeController@notSubscribed')->name('not-subscribed');

    Route::get('/home/get-totals', 'HomeController@getTotals');

    Route::get('/home/product-stock-alert', 'HomeController@getProductStockAlert');

    Route::get('/home/purchase-payment-dues', 'HomeController@getPurchasePaymentDues');

    Route::get('/home/sales-payment-dues', 'HomeController@getSalesPaymentDues');

    Route::get('/load-more-notifications', 'HomeController@loadMoreNotifications');

    Route::get('/clear-cache', 'BusinessController@clearCache');

    Route::post('/test-email', 'BusinessController@testEmailConfiguration');

    Route::resource('/customer-settings', 'CustomerSettingsController');

    Route::get('/customer-limit-approval/get-approval-details/{customer_id}/{requested_user}', 'CustomerLimitApprovalController@getApprovalDetails');

    Route::post('/customer-limit-approval/update-approval-details/{customer_id}', 'CustomerLimitApprovalController@updateApprovalLimit');

    Route::get('/customer-limit-approval/send-reuqest-for-approval/{customer_id}', 'CustomerLimitApprovalController@sendRequestForApproval');

    Route::resource('/customer-limit-approval', 'CustomerLimitApprovalController');

    Route::post('/test-sms', 'BusinessController@testSmsConfiguration');

    Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.getBusinessSettings');
    
    Route::resource('/reports/settings', 'ReportConfigurationsController');

    Route::post('/business/update', 'BusinessController@postBusinessSettings')->name('business.postBusinessSettings');

    Route::get('/user/profile', 'UserController@getProfile')->name('user.getProfile');

    Route::post('/user/update', 'UserController@updateProfile')->name('user.updateProfile');

    Route::post('/user/update-password', 'UserController@updatePassword')->name('user.updatePassword');

    Route::get('/business/dayEnd', 'BusinessController@dayEnd')->name('business.dayEnd');

    Route::resource('brands', 'BrandController');

    Route::resource('vehicle', 'VehicleController');

    Route::post('/home/login_payroll', 'HomeController@loginPayroll');

    Route::resource('tax-rates', 'TaxRateController');

    Route::get('get_sub_units', 'UnitController@getSubUnits');

    Route::resource('units', 'UnitController');

    Route::get('/contacts/get_outstanding', 'ContactController@get_outstanding');
    
    Route::get('/contacts-summary/get-contact/{type}', 'ContactSummaryController@getContact');
    Route::get('/contacts-summary/index', 'ContactSummaryController@index');
    Route::get('/contacts-summary/ledger', 'ContactSummaryController@getLedger');
    
    Route::get('/contacts/update-vatnumber/{id}', 'ContactController@addVatNumber');
    Route::put('/contacts/update-vatnumber/{id}', 'ContactController@updateVatNumber');
    
    Route::get('/contacts/credit-sales', 'ContactCreditSales@index');
    
    Route::get('/contacts/settings', 'ContactController@settings');
    
    Route::get('/contacts/balance-details/{id}', 'ContactController@balanceDetails');
    
    Route::post('/contacts/save-settings', 'ContactController@save_settings');

    Route::get('/contacts/stock-report/{supplier_id}', 'ContactController@getSupplierStockReport');
    
    Route::get('/contacts/add-more-numbers/{id}', 'ContactController@add_notification_numbers');
    
    Route::post('/contacts/add-more-numbers/{id}', 'ContactController@save_notification_numbers');

    Route::get('/contacts/ledger', 'ContactController@getLedger');
    
    Route::get('/contacts/payments', 'ContactController@getPayment');

    Route::get('/contacts/import', 'ContactController@getImportContacts')->name('contacts.import');
    
    Route::get('/contacts/import-balance', 'ContactController@getImportBalance');
    
    Route::post('/contacts/import-balance', 'ContactController@postImportBalance');

    Route::post('/contacts/import', 'ContactController@postImportContacts');

    Route::post('/contacts/check-contact-id', 'ContactController@checkContactId');

    Route::get('/contacts/customers', 'ContactController@getCustomers');

    Route::get('/contacts/suppliers', 'ContactController@getSuppliers');
    
    Route::get('/contacts/create_customer', 'ContactController@create_customer');
    Route::get('/contacts/customer_loan', 'ContactController@customer_loans');
     
     Route::get('/contacts/customer_loan_view', 'ContactController@customer_loan_view');
     
     Route::get('/contacts/customer_loans_list', 'ContactController@customer_loans_list');
    
     Route::get('/contacts/loan', 'ContactController@loan');
    
    Route::get('/contacts/create_mappings', 'SupplierMappingController@store');  
     
    Route::post('/contacts/mapping', 'SupplierMappingController@store'); 
     
     Route::get('/contacts/add-supplier-map-product', 'SupplierMappingController@addMapping');
     
    Route::get('/contacts/add-supplier-map-product/get-supplier-mapped', 'SupplierMappingController@getSupplierMapped');
    
    Route::get('/contacts/delete-supplier-map-product/delete-supplier-mapped', 'SupplierMappingController@deleteSupplierMapped');
     
    Route::get('/product/product-bind-supplier', 'SupplierMappingController@createMapping');
    Route::get('/product/product-bind-supplier-get', 'SupplierMappingController@indexSupplier');
    
    Route::get('/get-customer-reference/barcode', 'CustomerReferenceController@getCustomerReferenceBarcode');

    Route::get('/get-customer-reference/{id}', 'CustomerReferenceController@getCustomerReference');

    Route::resource('customer-reference', 'CustomerReferenceController');

    Route::resource('customer-statement-settings', 'CustomerStatementSettingController');
    
    Route::resource('customer-statement-logos', 'CustomerStatementLogoController');

    Route::get('/get-customer-statement-no', 'CustomerStatementController@getCustomerStatementNo');
    
    Route::get('customer-statement/list-payments', 'CustomerStatementController@listStatementPayments');
    Route::get('customer-statement/pay-total/{statement_id}', 'CustomerStatementController@payTotalStatement');
    Route::post('customer-statement/pay-total/{statement_id}', 'CustomerStatementController@postPayTotalStatement');
    
   Route::get('customer-statement/get-statement-list', 'CustomerStatementController@getCustomerStatementList');
    Route::get('customer-statement/get-statement-list-pmts', 'CustomerStatementController@getCustomerStatementListPmt');

    Route::get('customer-statement/reprint/{statement_id}', 'CustomerStatementController@rePrint');
    Route::get('customer-statement/export-excel/{statement_id}', 'CustomerStatementController@exportExcel');
    
    Route::get('customer-statement/export-excel-pmt/{statement_id}', 'CustomerStatementController@exportExcelPmt');
    
    Route::get('customer-statement/reprint-pmt/{statement_id}', 'CustomerStatementController@rePrintPmt');
    Route::get('customer-statement/show-pmt/{statement_id}', 'CustomerStatementController@showPmt');
    
    Route::post('/download-pdf', 'CustomerStatementController@downloadPdf');

    Route::get('customer-interest', 'CustomerPaymentController@CustomerInterest');

    Route::get('customer-payment-information/{customer}/{type}','CustomerPaymentController@customerPaymentInformations');
    
    Route::get('customer-payment-view/{id}','CustomerPaymentController@viewPayment');
    
    Route::get('customer-payment-print/{id}','CustomerPaymentController@printPayment');
    
    Route::get('customer-info-for/{for}/{data}', 'CustomerPaymentController@customerInfoFor'); // @eng 19/2

    Route::get('customer-statement/delete/{id}', 'CustomerStatementController@destroyPayments');

    Route::resource('customer-statement', 'CustomerStatementController');

    Route::resource('manual-bill', 'ManualBillController');
    Route::post('manual-bill/get-table-data', 'ManualBillController@getTableData');
    
    Route::resource('product-bind-supplier', 'SupplierMappingController');
    
    Route::get('customer-date', 'CustomerStatementController@getMinimumDate');

    Route::resource('customer-payments', 'CustomerPaymentController');

    Route::resource('customer-payment-simple', 'CustomerPaymentSimpleController');

    Route::resource('interest-settings', 'InterestSettingController');

    Route::get('customer-payment-bulk/get-payment-table', 'CustomerPaymentBulkController@bulkPaymentTable');

    Route::resource('customer-payment-bulk', 'CustomerPaymentBulkController');

    Route::get('contacts/toggle-activate/{contact_id}', 'ContactController@toggleActivate');

    Route::get('/contacts/list-security-deposit/{contact_id}', 'ContactController@listSecurityDeposit');

    Route::post('contacts/mass-destroy', 'ContactController@massDestroy');
    
    Route::post('contacts/ob-export', 'ContactController@exportBalance');

    Route::get('outstanding-received-report', 'ContactController@getOutstandingReceivedReport');
    
    Route::get('get-outstanding-filters', 'ContactController@getOutstandingFilters');

    Route::get('issued-payment-details', 'ContactController@getIssuedPaymentDetails');
    
    Route::get('returned-cheque-details', 'ContactController@getReturnedCheques');
    //add by sakhawat
    Route::resource('opt-verification', 'UsersOPTController');
   
    Route::resource('contacts', 'ContactController');
    //add by sakhawat
    Route::get('location/customer-form/{id}', 'ContactController@getCustomerForm')->name('location.customer.form');

    Route::get('get-contacts', 'ContactController@getContacts');

    Route::post('check-mobile', 'ContactController@checkMobile');

    Route::resource('crm-activity-details', 'CrmActivityDetailController');

    Route::resource('crm-activity', 'CRMActivityController');

    Route::get('/crm/add_comments', 'CRMController@addComments');

    Route::get('/crm/show_comments', 'CRMController@showComments');

    Route::resource('crm', 'CRMController');
    
    Route::post('/crmgroups/check_group', 'CrmGroupController@checkGroupName');
    Route::resource('crmgroups', 'CrmGroupController');

    Route::resource('categories', 'CategoryController');

    Route::get('merged-sub-category/get-sub-categories/{category_id}', 'MergedSubCategoryController@getSubCategories');

    Route::resource('merged-sub-category', 'MergedSubCategoryController');

    Route::resource('variation-templates', 'VariationTemplateController');
    
    Route::get('/products/download-excel', 'ProductController@downloadExcel');

    Route::get('variation-transfer/get-variation-by-category', 'VariationTransferController@getVariationByCategory');

    Route::get('variation-transfer/get-variation-of-product/{variation_id}', 'VariationTransferController@getVariationOfProduct');

    Route::resource('variation-transfer', 'VariationTransferController');

    Route::get('/delete-media/{media_id}', 'ProductController@deleteMedia');

    Route::post('/products/mass-deactivate', 'ProductController@massDeactivate');

    Route::get('/products/activate/{id}', 'ProductController@activate');

    Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');

    Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
    
    Route::resource('stock-conversion', 'StockConversionController');
     
    Route::get('stock-conversion', 'StockConversionController@index'); 
    
    Route::post('/stock-conversion/store', 'StockConversionController@store'); 
    
    Route::get('/stock-conversion/create', 'StockConversionController@create');
    
    Route::get('/stock-conversion/destroy/{id}', 'StockConversionController@destroy');
    
    Route::get('/stock-conversion/get-stock-unit/{id}', 'StockConversionController@getStockUnit');
    
    Route::get('view/{id}', [StockConversionController::class, 'view'])->name('stockconversion.view'); 
     
    Route::get('edit/{id}', [StockConversionController::class, 'edit'])->name('stockconversion.edit');
    
    Route::get('delete/{id}', [StockConversionController::class, 'edit'])->name('stockconversion.delete');

    Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');

    Route::get('/products/min-selling-prices/{id}', 'ProductController@minSellPrice');

    Route::post('/products/min-selling-prices-update', 'ProductController@minSellPriceUpdate');

    Route::post('/products/mass-delete', 'ProductController@massDestroy');

    Route::get('/products/view/{id}', 'ProductController@view');

    Route::get('/products/list', 'ProductController@getProducts');
    
    Route::get('/products/list-sa', 'ProductController@getProductsStockAdjustment');
    
    Route::get('/products/list-pos', 'ProductController@getProductsPos');

    Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');

    Route::post('/products/bulk-edit', 'ProductController@bulkEdit');

    Route::post('/products/bulk-update', 'ProductController@bulkUpdate');

    Route::post('/products/bulk-update-location', 'ProductController@updateProductLocation');

    Route::get('/products/get-product-to-edit/{product_id}', 'ProductController@getProductToEdit');

    Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');

    Route::post('/products/get_product_category_wise', 'ProductController@getProductsCategoryWise');

    Route::get('/products/get_sub_units', 'ProductController@getSubUnits');

    Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');

    Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');

    Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');

    Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');

    Route::post('/products/check_product_sku', 'ProductController@checkProductSku');

    Route::get('/products/quick_add', 'ProductController@quickAdd');

    Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');

    Route::get('/products/get-combo-product-entry-row', 'ProductController@getComboProductEntryRow');

    Route::resource('products', 'ProductController');

    Route::get('/purchases/get-supplier-due', 'PurchaseController@getSupplierDue');

    Route::post('/purchases/update-status', 'PurchaseController@updateStatus');

    Route::get('/purchases/get_products', 'PurchaseController@getProductsPurchases');

    Route::get('/purchases/get_suppliers', 'PurchaseController@getSuppliers');
  Route::get('/purchases/get_suppliers/{id}', 'PurchaseController@getSuppliertId');
    Route::get('/purchases/get_edit_unload_tank_row', 'PurchaseController@getEditUnloadTankRow');

    Route::get('/purchases/get_unload_tank_row', 'PurchaseController@getUnloadTankRow');

    Route::get('/purchases/get_unload_tank_row_bulk', 'PurchaseController@getUnloadTankRowBulk');

    Route::post('/purchases/get_purchase_entry_row_bulk', 'PurchaseController@getPurchaseEntryRowBulk');

    Route::post('/purchases/get_purchase_entry_row', 'PurchaseController@getPurchaseEntryRow');

    Route::post('/purchases/get_purchase_entry_row_temp', 'PurchaseController@getPurchaseEntryRowTemp');

    Route::post('/purchases/check_ref_number', 'PurchaseController@checkRefNumber');

    Route::get('/purchases/print/{id}', 'PurchaseController@printInvoice');

    Route::get('purchases/get-payment-method-by-location-id/{location_id}', 'PurchaseController@getPaymentMethodByLocationId');

    Route::post('purchases/get_payment_row_bulk', 'PurchaseController@getPaymentRowBulk');

    Route::post('purchases/get_payment_row', 'PurchaseController@getPaymentRow');

    Route::post('purchases/save-purchase-bulk', 'PurchaseController@savePurchaseBulk');

    Route::get('purchases/add-purchase-bulk', 'PurchaseController@addPurchaseBulk');

    Route::get('purchases/get-invoice-no', 'PurchaseController@getInvoiceNo');

    Route::resource('purchases', 'PurchaseController');

    Route::resource('purchase-pos', 'PurchasePosController');

    Route::get('/import-sales', 'ImportSalesController@index');

    Route::post('/import-sales/preview', 'ImportSalesController@preview');

    Route::post('/import-sales', 'ImportSalesController@import');

    Route::get('/revert-sale-import/{batch}', 'ImportSalesController@revertSaleImport');

    Route::get('/import-purchases', 'ImportPurchasesController@index');

    Route::post('/import-purchases/preview', 'ImportPurchasesController@preview');

    Route::post('/import-purchases', 'ImportPurchasesController@import');

    Route::get('/revert-sale-import/{batch}', 'ImportPurchasesController@revertSaleImport');

    Route::get('/toggle-subscription/{id}', 'SellPosController@toggleRecurringInvoices');

    Route::get('/toggle_popup', 'SellPosController@toggle_popup');
    
    
    Route::post('/get-customer-details', 'SellPosController@getCustomerDueDetails');
    
    
    

    Route::post('/sells/pos/get-types-of-service-details', 'SellPosController@getTypesOfServiceDetails');

    Route::get('/sells/subscriptions', 'SellPosController@listSubscriptions');

    Route::get('/sells/invoice-url/{id}', 'SellPosController@showInvoiceUrl');

    Route::get('/sells/over-limit-sales', 'SellController@overLimitSales');

    Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');

    Route::get('/sales/drafts', 'SellController@getDrafts');

    Route::get('/sales/customer/orders/get-image/{id}', 'SellController@getImage');

    Route::get('/sales/customer/orders', 'SellController@getCustomerOrders');

    Route::get('/sales/customer/uploaded-orders', 'SellController@getCustomerUploadedOrders');

    Route::get('/sales/quotations', 'SellController@getQuotations');

    Route::get('/sales/outstanding_report', 'SellController@showReport');

    Route::get('/sells/draft-dt', 'SellController@getDraftDatables');

    Route::get('/sells/get-invoice', 'SellController@getInvoiveNo');

    Route::resource('sales', 'SellController');

    Route::resource('reserved-stocks', 'ReservedStocksController');

    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', 'SellPosController@getProductRow');

    Route::get('sells/pos/get_product_row_temp/{variation_id}/{location_id}/{temp_qty}', 'SellPosController@getProductRowTemp');

    Route::post('/sells/pos/get_payment_row', 'SellPosController@getPaymentRow');

    Route::get('/sales/pos/get_payment_account_id/{payment_method}', 'SellPosController@getPaymentRowAccountId');

    Route::post('/sells/pos/get-reward-details', 'SellPosController@getRewardDetails');

    Route::get('/sells/pos/get-recent-transactions', 'SellPosController@getRecentTransactions');

    //add by sakhawat 
    Route::get('/sells/pos/get-recent-transactions-popup', 'SellPosController@getRecentTransactionPopup');


    Route::get('/sells/{transaction_id}/print', 'SellPosController@printInvoice')->name('sell.printInvoice');

    Route::get('/sells/{ref_number}/print_invoice', 'SellPosController@printOustandingInvoice')->name('sell.printOustandingInvoice');

    Route::get('/sells/pos/get-product-suggestion', 'SellPosController@getProductSuggestion');

    Route::get('pos/get_customer_details', 'SellPosController@getCustomerDetails');

    Route::get('purchase/get_supplier_details', 'PurchaseController@getSupplierDetails');

    Route::resource('pos', 'SellPosController');
    
    Route::get('/sells/tpos/get_product_row/{variation_id}/{location_id}', 'TposController@getProductRow');
    
    Route::get('/fpos/create', 'TposController@createFpos');
    
    Route::post('/fpos/customer-tpos', 'TposController@customerTpos');
    
    Route::post('/fpos/create', 'TposController@storeFpos');
    
    Route::get('/list/fpos', 'TposController@indexFpos');
    
    Route::get('/sells/add-tpos/{id}', 'TposController@getTposProducts');
    
    Route::resource('tpos', 'TposController');

    Route::resource('roles', 'RoleController');

    Route::post('update-password/{id}', 'ManageUserController@updatePassword');

    Route::get('change-password/{id}', 'ManageUserController@changePassword');

    Route::get('business-users', 'ManageUserController@businessUsers');

    Route::post('lock_screen', 'ManageUserController@lockScreen');

    Route::post('check_user_password', 'ManageUserController@checkUserPassword');

    Route::resource('users', 'ManageUserController');
    //add by sakhawat
    Route::get('change/reCAPTCHA/{id}', 'ManageUserController@changeReCAPTCHAStatus');

    Route::resource('group-taxes', 'GroupTaxController');

    Route::get('/barcodes/set_default/{id}', 'BarcodeController@setDefault');

    Route::resource('barcodes', 'BarcodeController');

    //Invoice schemes..

    Route::get('/invoice-schemes/set_default/{id}', 'InvoiceSchemeController@setDefault');

    Route::resource('invoice-schemes', 'InvoiceSchemeController');

    //Print Labels

    Route::get('/labels/show', 'LabelsController@show');

    Route::get('/labels/add-product-row', 'LabelsController@addProductRow');

    Route::get('/labels/preview', 'LabelsController@preview');

    //Reports...
    Route::any('/reports/getreview_changes_table', 'ReportController@getReviewChanges');
    
    Route::any('/reports/getreview_details/{id}', 'ReportController@getReviewDetails');
    
    Route::post('/reports/email_report', 'ReportController@email_reports');
    
    Route::get('/reports/get-credit-status-totals', 'ReportController@getCreditStatusTotalsReport');
    
    Route::post('/review-all', 'ReportController@addDailyReportReview');
    
    Route::get('/customized_reports', 'ReportCustomizedController@getProductReportCustomized');
    
    Route::get('/get_cummulative_sales', 'ReportCustomizedController@getCummulativeSales');
    
     Route::get('/get_pumpers_sales', 'ReportCustomizedController@getPumperSales');
    
    Route::post('/daily-review', 'ReportController@reviewSesction');
    
    Route::post('/daily-review-undo', 'ReportController@reviewSesctionUndo');
    
    
    // Combined Reports    
    Route::any('/reports/combined/stock-report', 'ReportController@getCombinedStockSummaryReport');
    Route::any('/reports/combined/customer-outstanding-report', 'ReportController@getCustomerOutstandingReport');
    Route::any('/reports/combined/supplier-outstanding-report', 'ReportController@getSupplierOutstandingReport');
    
    Route::any('/reports/stock-purchase-sale-report', 'ReportController@stockPurchaseSaleReport');

    Route::get('/reports/activity', 'ReportController@getActivityReport');

    //add by sakhawat

    Route::get('/customer-report/activity', 'CustomerStatementController@getUserActivityReport');

    Route::get('/reports/management', 'ReportController@getManagementReport');

    Route::get('/reports/verification', 'ReportController@getVerificationReport');

    Route::get('/reports/payment-status', 'ReportController@getPaymentStatusReport');

    Route::get('/reports/product', 'ReportController@getProductReport');

    Route::get('/reports/contact', 'ReportController@getContactReport');

    Route::get('/reports/service-staff-report', 'ReportController@getServiceStaffReport');

    Route::get('/reports/service-staff-line-orders', 'ReportController@serviceStaffLineOrders');

    Route::get('/reports/table-report', 'ReportController@getTableReport');

    Route::get('/reports/profit-loss', 'ReportController@getProfitLoss');

    Route::get('/reports/get-opening-stock', 'ReportController@getOpeningStock');

    Route::get('/reports/get-product-transaction-summary', 'ReportController@getProductTransactionSummary');

    Route::get('/reports/purchase-sell', 'ReportController@getPurchaseSell');

    Route::get('/reports/customer-supplier', 'ReportController@getCustomerSuppliers');

    Route::get('/reports/product-transaction-report', 'ReportController@getProductTransactionReport');

    Route::get('/reports/product-weight-loss-excess-report', 'ReportController@getWeightLossExcessReport');

    Route::get('/reports/stock-report', 'ReportController@getStockReport');
    
     Route::get('/reports/stock-summary', 'ReportController@getStockSummaryReport');

    Route::get('/reports/stock-details', 'ReportController@getStockDetails');

    Route::get('/reports/tax-report', 'ReportController@getTaxReport');

    Route::get('/reports/trending-products', 'ReportController@getTrendingProducts');

    Route::get('/reports/expense-report', 'ReportController@getExpenseReport');

    Route::get('/reports/stock-adjustment-report', 'ReportController@getStockAdjustmentReport');

    Route::get('/reports/register-report', 'ReportController@getRegisterReport');

    Route::get('/reports/sales-representative-report', 'ReportController@getSalesRepresentativeReport');

    Route::get('/reports/sales-representative-total-expense', 'ReportController@getSalesRepresentativeTotalExpense');

    Route::get('/reports/sales-representative-total-sell', 'ReportController@getSalesRepresentativeTotalSell');

    Route::get('/reports/sales-representative-total-commission', 'ReportController@getSalesRepresentativeTotalCommission');

    Route::get('/reports/stock-expiry', 'ReportController@getStockExpiryReport');

    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', 'ReportController@getStockExpiryReportEditModal');

    Route::post('/reports/stock-expiry-update', 'ReportController@updateStockExpiryReport')->name('updateStockExpiryReport');

    Route::get('/reports/customer-group', 'ReportController@getCustomerGroup');

    Route::get('/reports/product-purchase-report-summary', 'ReportController@getproductPurchaseReportSummary');

    Route::get('/reports/product-purchase-report', 'ReportController@getproductPurchaseReport');

    Route::get('/reports/product-sell-report-summary', 'ReportController@getproductSellReportSummary');

    Route::get('/reports/product-sell-report', 'ReportController@getproductSellReport');

    Route::get('/reports/sales-report', 'ReportController@getproductSalesReportDuplicate');

    Route::get('/reports/product-sell-grouped-report', 'ReportController@getproductSellGroupedReport');

    Route::get('/reports/product-sales-grouped-report-duplicate', 'ReportController@getproductSalesGroupedReportDuplicate');

    Route::get('/reports/lot-report', 'ReportController@getLotReport');

    Route::get('/reports/purchase-payment-report', 'ReportController@purchasePaymentReport');

    Route::get('/reports/sell-payment-report', 'ReportController@sellPaymentReport');

    Route::get('/reports/product-stock-details', 'ReportController@productStockDetails');

    Route::get('/reports/adjust-product-stock', 'ReportController@adjustProductStock');

    Route::get('/reports/get-profit/{by?}', 'ReportController@getProfit');

    Route::get('/reports/items-report', 'ReportController@itemsReport');

    Route::get('/reports/outstanding_report', 'ReportController@getOutstandingReport');

    Route::get('/reports/aging_report', 'ReportController@getAgingReport');
    
    Route::get('/reports/aging_report_total', 'ReportController@getAgingReportTotal');
    
    Route::get('/reports/aging_outstanding', 'ReportController@getAgingTotalOutstanding');

    Route::get('/reports/daily-summary-report', 'ReportController@getDailySummaryReport');

    Route::get('/reports/daily-report', 'ReportController@getDailyReport');
    Route::get('/reports/financial-status', 'ReportController@getFinancialStatus');
    Route::get('/reports/daily-report/getMeterSalesDetails', 'ReportController@getMeterSalesDetails')->name('getMeterSalesDetails');
    Route::get('/reports/daily-report/getSoldItemsReportDetail', 'ReportController@getSoldItemsReportDetail')->name('getSoldItemsReportDetail');
    Route::get('/reports/daily-report/getchequesReceivedReport', 'ReportController@getchequesReceivedReport')->name('getchequesReceivedReport');
    Route::get('/reports/daily-report/getexpensesReport', 'ReportController@getexpensesReport')->name('getexpensesReport');
    Route::get('/reports/daily-report/getOutStandingReceived', 'ReportController@getOutStandingReceivedDataTable')->name('getOutStandingReceivedDataTable');

     // @sakhawat kamran

     Route::group(['prefix' => 'reports'], function () {

        Route::get('commons-stock-summary', 'CommonReportController@getStockSummary');

        Route::get('cus-outstanding-report', 'CommonReportController@getCustomerOutstandingReport');

        

    });
    
    // Route::get('/reports/checkdaily-report', 'ReportController@checkgetDailyReport');

    Route::get('/reports/montly-report', 'ReportController@getMonthlyReport')->name('report.monthlyReport');

    // Route::get('/reports/checkmontly-report', 'ReportController@checkgetMonthlyReport');

    Route::get('/reports/comparison-report', 'ReportController@getComparisonReport');

    Route::get('/reports/get_daily_report_modal_view/{type}', 'ReportController@getDailyReportDetailsView');

    Route::get('/reports/user_activity', 'ReportController@getUserActivityReport');
     //add by sakhawat
    Route::get('/customer-report/activity', 'CustomerStatementController@getUserActivityReport');

    Route::get('/reports/get-stock-value', 'ReportController@getStockValue');

    Route::get('business-location/activate-deactivate/{location_id}', 'BusinessLocationController@activateDeactivateLocation');

    //Business Location Settings...

    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {

        Route::get('settings', 'LocationSettingsController@index')->name('settings');

        Route::post('settings', 'LocationSettingsController@updateSettings')->name('settings_update');

    });

    //Business Locations...

    Route::post('business-location/check-location-id', 'BusinessLocationController@checkLocationId');

    Route::resource('business-location', 'BusinessLocationController');

    //sakhi

    Route::post('location-currency', 'BusinessLocationController@getCurrency')->name('location.currency');
    
    Route::get('location-has-stores-count/{location_id}', 'StoreController@locationHasStoreCount');
    
    Route::get('store-permissions', 'StoreController@fetchUserStorePermissions');
    Route::get('create-store-permissions', 'StoreController@createStorePermission');
    Route::get('edit-store-permissions/{id}', 'StoreController@editUserPermission');
    Route::delete('delete-store-permissions/{id}', 'StoreController@destroyPermission');
    Route::post('create-store-permissions', 'StoreController@storeUserPermission');
    
    Route::resource('stores', 'StoreController');

    //Invoice layouts..

    Route::resource('invoice-layouts', 'InvoiceLayoutController');

    //Expense Categories...

    Route::get('get-expense-account-category-id/{category_id}', 'ExpenseCategoryController@getAccountIdByCategory');

    Route::get('expense-categories/get-drop-down', 'ExpenseCategoryController@getExpenseCategoryDropDown');

    Route::post('expense-categories/check-duplicate', 'ExpenseCategoryController@checkDuplicate');

    Route::resource('expense-categories', 'ExpenseCategoryController');
    
    Route::resource('expense-categories-code', 'ExpenseCategoryCodeController');
    
    Route::resource('expense-categories-number', 'ExpenseCategoryNumberController');

    //Expenses...

    Route::get('expenses/get-payment-method-by-location-id', 'ExpenseController@getPaymentMethodByLocationDropDown');
    
    Route::get('expenses/ro-expenses/{id}', 'ExpenseController@routeperationExpenses');

    Route::resource('expenses', 'ExpenseController');

    //add By Sakhawat 
    Route::get('expense/print/{id}', 'ExpenseController@print')->name('expense-print');


    //Transaction payments...

    Route::get('/payments/get-payment-method-by-location-id/{location_id}', 'TransactionPaymentController@getPaymentMethodByLocationDropDown');

    Route::get('/payments/show-child-payments/{payment_id}', 'TransactionPaymentController@showChildPayments');

    Route::get('/payments/view-payment/{payment_id}', 'TransactionPaymentController@viewPayment');

    Route::get('/payments/get-cheque-dropdown-by-bank-id/{bank_id}/{contact_id}', 'TransactionPaymentController@getChequeDropdownByBankId');

    Route::get('/payments/get-payment-details-by-id/{payment_id}', 'TransactionPaymentController@getPaymentDetailsById');

    Route::get('/payments/add_payment/{transaction_id}', 'TransactionPaymentController@addPayment');
    
    Route::get('/payments/print/{transaction_id}', 'TransactionPaymentController@print');

    Route::post('payments/refund-security-deposit/{contact_id}', 'TransactionPaymentController@getRefundSecurityDeposit');

    Route::get('payments/security-deposit/{contact_id}', 'TransactionPaymentController@getSecurityDeposit');

    Route::post('payments/security-deposit/{contact_id}', 'TransactionPaymentController@postSecurityDeposit');

    Route::get('/payments/refund-payment/{contact_id}', 'TransactionPaymentController@getRefundPayment');

    Route::post('/payments/refund-payment/{contact_id}', 'TransactionPaymentController@postRefundPayment');

    Route::get('/payments/advance-payment/{contact_id}', 'TransactionPaymentController@getAdvancePayment');
    
    Route::get('/payments/direct-loan/{contact_id}', 'TransactionPaymentController@getDirectLoan');

    Route::post('/payments/advance-payment/{contact_id}', 'TransactionPaymentController@postAdvancePayment');
    
    Route::post('/payments/direct-loan/{contact_id}', 'TransactionPaymentController@postDirectLoan');
    
    Route::get('/payments/refund_deposit/{contact_id}', 'TransactionPaymentController@getRefundDeposit');

    Route::post('/payments/refund_deposit/{contact_id}', 'TransactionPaymentController@postRefundDeposit');

    Route::get('/payments/pay-contact-due/{contact_id}', 'TransactionPaymentController@getPayContactDue');

    Route::post('/payments/pay-contact-due', 'TransactionPaymentController@postPayContactDue');
    
    Route::get('/api/get-liability-accounts', [TransactionPaymentController::class, 'getLiabilityAccounts']);
    
    
    Route::get('/payments/pay-vat-due/{contact_id}', 'TransactionPaymentController@getPayVatDue');

    Route::post('/payments/pay-vat-due', 'TransactionPaymentController@postPayVatDue');
    

    Route::get('payments/pending-payment/{id}', 'TransactionPaymentController@pendingPayment');

    Route::get('payments/pending-payment-confirm/{id}', 'TransactionPaymentController@pendingPaymentConfirm');

    Route::get('payments/get-accounts-dropdown', 'TransactionPaymentController@getAccountDropDown');

    Route::get('payments/get-payment-datatable/{id}', 'TransactionPaymentController@getPaymentDatatable');

    Route::get('payments/get-transaction-shortages-datatable/{id}', 'TransactionPaymentController@getTransactionShortagesDataTable');

    
    Route::get('payments/view/{id}', 'TransactionPaymentController@showdetails');

    Route::get('payments/get-payment-view-datatable/{id}', 'TransactionPaymentController@getPaymentViewDatatable');
    Route::get('payments/credit-sales', 'TransactionPaymentController@show_credit_sales');
    
    
    Route::resource('payments', 'TransactionPaymentController');

    //Printers...

    Route::resource('printers', 'PrinterController');

    Route::resource('business-category', 'BusinessCategoryController');
    
    Route::get('stock-settings/fetch-accounts/{type}/{id}', 'StockAdjustmentSettings@get_account_by');
    
    Route::get('stock-settings/edit/{id}', 'StockAdjustmentSettings@edit');

    Route::resource('stock-settings', 'StockAdjustmentSettings');
    

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');

    Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');

    Route::get('stock-adjustments/edit/{transaction_id}', 'StockAdjustmentController@edit');

    Route::post('/stock-adjustments/{id}', 'StockAdjustmentController@update');

    Route::post('/stock-adjustments-new/get_product_row_stock_transfer', 'StockAdjustmentController@getProductRowStockTransfer');

    Route::post('/stock-adjustments/get_product_row_temp', 'StockAdjustmentController@getProductRowTemp');

    Route::post('/stock-adjustments/get_inventory_account', 'StockAdjustmentController@getInventoryAccount');

    Route::get('stock-adjustments/inventory-adjustment-account', 'StockAdjustmentController@getInventoryAdjustmentAccount');

    Route::resource('stock-adjustments', 'StockAdjustmentController');

    Route::get('/cash-register/register-details', 'CashRegisterController@getRegisterDetails');

    Route::get('/cash-register/close-register', 'CashRegisterController@getCloseRegister');

    Route::post('/cash-register/close-register', 'CashRegisterController@postCloseRegister');

    Route::resource('cash-register', 'CashRegisterController');

    //Import products

    Route::get('/import-products', 'ImportProductsController@index');

    Route::post('/import-products/store', 'ImportProductsController@store');

    //Sales Commission Agent

    Route::resource('sales-commission-agents', 'SalesCommissionAgentController');

    //Stock Transfer

    Route::get('stock-transfers/print/{id}', 'StockTransferController@printInvoice');

    Route::get('/stock-transfer/get_transfer_location/{id}', 'StockTransferController@getBusinessLocationExcept');

    Route::get('/stock-transfer/get_transfer_location_temp/{id}', 'StockTransferController@getBusinessLocationTemp');

    Route::get('/stock-transfer/get_transfer_store_id/{id}', 'StockTransferController@getBusinessLocationStoreId');

    Route::get('/stock-transfer/get_transfer_store_id_temp/{id}', 'StockTransferController@getBusinessLocationStoreIdTemp');

    Route::get('store-list', 'StockTransferController@listStores');

    Route::resource('stock-transfers', 'StockTransferController');

    Route::post('stock-transfers-request/received-transfer/{id}', 'StockTransferRequestController@postReceivedTransfer');

    Route::get('stock-transfers-request/received-transfer/{id}', 'StockTransferRequestController@getReceivedTrasnfer');

    Route::get('stock-transfers-request/stop-notification/{id}', 'StockTransferRequestController@stopNotification');

    Route::get('stock-transfers-request/get-notification-poup/{id}', 'StockTransferRequestController@getNotificationPopup');

    Route::get('List_Store_Transaction', 'StockController@List_Store_Transaction');

    Route::post('getdata', 'StockController@makeTable')->name('Rooms.maketable');

    Route::get('/getsubcat/{name}', 'StockController@getsubcat');

    Route::get('/getproduct/{name}', 'StockController@getproduct');

    Route::get('/getproductfind/{name}', 'StockController@getproductfind');

    //      function () {

    //     return view('stock_transfer.requests.List_Store_Transaction');

    // }

    Route::post('stock-transfers-request/save-transfer', 'StockTransferRequestController@saveTransfer');

    Route::get('stock-transfers-request/create-transfer/{id}', 'StockTransferRequestController@createTransfer');

    Route::resource('stock-transfers-request', 'StockTransferRequestController');

    Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add');

    Route::post('/opening-stock/save', 'OpeningStockController@save');

    //Customer Groups

    Route::resource('contact-group', 'ContactGroupController');

    //Import opening stock

    Route::get('/import-opening-stock', 'ImportOpeningStockController@index');

    Route::post('/import-opening-stock/store', 'ImportOpeningStockController@store');

    //Sell return

    Route::post('sell-return/save-pos-return', 'SellReturnController@savePosReturn');

    Route::resource('sell-return', 'SellReturnController');

    Route::get('sell-return/get-product-row', 'SellReturnController@getProductRow');

    Route::get('/sell-return/print/{id}', 'SellReturnController@printInvoice');

    Route::get('/sell-return/add/{id}', 'SellReturnController@add');

    //Backup

    Route::get('backup/download/{file_name}', 'BackUpController@download');

    Route::get('backup/delete/{file_name}', 'BackUpController@delete');
    Route::get('backup/restore/{file_name}', 'BackUpController@restore');

    Route::resource('backup', 'BackUpController', ['only' => [

        'index', 'create', 'store'

    ]]);

    Route::get('export-selling-price-group/toggle-activate/{id}', 'SellingPriceGroupController@toggleActivate');

    Route::get('export-selling-price-group', 'SellingPriceGroupController@export');

    Route::post('import-selling-price-group', 'SellingPriceGroupController@import');

    Route::resource('selling-price-group', 'SellingPriceGroupController');

    Route::resource('notification-templates', 'NotificationTemplateController')->only(['index', 'store']);

    // Route::get('notification-templates/email-template', 'NotificationTemplateController@index');

    // Route::get('notification-templates/sms-template', 'NotificationTemplateController@index');

    Route::get('notification/get-template/{transaction_id}/{template_for}', 'NotificationController@getTemplate');

    Route::post('notification/send', 'NotificationController@send');

    Route::post('/purchase-return/update', 'CombinedPurchaseReturnController@update');

    Route::get('/purchase-return/edit/{id}', 'CombinedPurchaseReturnController@edit');

    Route::post('/purchase-return/save', 'CombinedPurchaseReturnController@save');

    Route::post('/purchase-return/get_product_row', 'CombinedPurchaseReturnController@getProductRow');

    Route::get('/purchase-return/create', 'CombinedPurchaseReturnController@create');

    Route::get('/purchase-return/add/{id}', 'PurchaseReturnController@add');

    Route::resource('/purchase-return', 'PurchaseReturnController', ['except' => ['create']]);

    Route::get('/discount/activate/{id}', 'DiscountController@activate');

    Route::post('/discount/mass-deactivate', 'DiscountController@massDeactivate');

    Route::resource('discount', 'DiscountController');

    Route::resource('super-manager/visitors', 'SuperManagerVisitorController');

    Route::group(['prefix' => 'accounting-module'], function () {
        Route::get('/check-insufficient-balance-for-accounts', 'AccountController@getAccsForWhichToCheckInsufficientBalances');// @eng 15/2

        Route::get('/journals/get-account-dropdown-by-type/{account_type_id}', 'JournalController@getAccountDropdownByAccountType');

        Route::get('/journals/get_row', 'JournalController@getRow');

        Route::resource('/journal', 'JournalController');
        
        Route::get('/note', 'AccountController@note');
        Route::get('/post-dated-cheques-filters', 'PostdatedChequeController@postDatedFilters');
        Route::get('/old-post-dated-cheques-filters', 'PostdatedChequeController@oldpostDatedFilters');

        Route::get('/old-post-dated-cheques', 'PostdatedChequeController@oldPostDatedCheques');
        
        Route::get('/dated-cheques-party-type', 'PostdatedChequeController@partyType');
        Route::resource('/post-dated-cheques', 'PostdatedChequeController');
        
        Route::resource('/fixed-asset', 'FixedAssetController');

        Route::get('/get-profit-loss-report', 'AccountController@getProfitLossReport');

        Route::delete('/delete-account-transaction/{transaction_id}', 'AccountController@deleteAccountTransaction');

        Route::post('/update-account-transaction/{transaction_id}', 'AccountController@updateAccountTransaction');

        Route::get('/eidt-account-transaction/{transaction_id}', 'AccountController@editAccountTransaction');

        Route::get('/get-account-dp', 'AccountController@getBankAccountDropDown');

        Route::get('/get-account-group-name-dp', 'AccountController@getBankAccountByGroupDP');

        Route::get('/get-account-by-group-id/{group_id}', 'AccountController@getAccountByGroupId');

        Route::get('/get-account-group-by-account/{type_id}', 'AccountController@getAccountGroupByAccount');

        Route::get('/get-parent-account-by-type/{type_id}', 'AccountController@getParentAccountsByType');

        Route::get('/account/image-modal', 'AccountController@imageModal');

        Route::resource('/account-settings', 'AccountSettingController');

        Route::get('/account/fix-sales-accounts', 'AccountController@fixDecemberSalesAccounts')->name('accounts.fixDecemberSalesAccounts');

        Route::get('/account/fix-sales-accounts/{id}', 'AccountController@updateDecemberSalesAccounts')->name('accounts.updateDecemberSalesAccounts');

        Route::get('/account/correct-sale-income-accounts-tax', 'AccountController@correctSaleIncomeAccountsTax')->name('accounts.correctSaleIncomeAccountsTax');
        Route::get('/account/correct-sale-income-accounts-tax/{id}', 'AccountController@updateSaleIncomeAccountsTax')->name('accounts.updateSaleIncomeAccountsTax');
        Route::get('/correct-sell-lines-tax', 'AccountController@correctSellLinesTax')->name('accounts.correctSellLinesTax');
        Route::get('/update-sell-lines-tax', 'AccountController@updateSellLinesTax')->name('accounts.updateSellLinesTax');
        Route::get('/correct-sell-lines-decimal-difference', 'AccountController@correctSellLinesDecimalDifference')->name('accounts.correctSellLinesDecimalDifference');
        Route::get('/update-sell-lines-decimal-difference', 'AccountController@updateSellLinesDecimalDifference')->name('accounts.updateSellLinesDecimalDifference');
        Route::get('/account/correct-cogs-accounts-tax', 'AccountController@correctCOGSAccountsTax')->name('accounts.correctCOGSAccountsTax');
        Route::get('/account/correct-cogs-accounts-tax/{id}', 'AccountController@updateCOGSAccountsTax')->name('accounts.updateCOGSAccountsTax');
        Route::get('/account/update-accounts-receivable-settlement-customer-payment-to-credit', 'AccountController@getAccountsReceivableSettlementCustomerPaymentToCredit')->name('accounts.getAccountsReceivableSettlementCustomerPaymentToCredit');
        Route::get('/account/update-accounts-receivable-settlement-customer-payment-to-credit/{id}', 'AccountController@updateAccountsReceivableSettlementCustomerPaymentToCredit')->name('accounts.updateAccountsReceivableSettlementCustomerPaymentToCredit');
        Route::get('/account/update-finished-goods-account-pos-sale-tax', 'AccountController@getFinishedGoodsAccountPosSaleTax')->name('accounts.getFinishedGoodsAccountPosSaleTax');
        Route::get('/account/update-finished-goods-account-pos-sale-tax/{id}', 'AccountController@updateFinishedGoodsAccountPosSaleTax')->name('accounts.updateFinishedGoodsAccountPosSaleTax');
        Route::get('/account/update-cash-account-pos-sale-tax', 'AccountController@getCashAccountPosSaleTax')->name('accounts.getCashAccountPosSaleTax');
        Route::get('/account/update-cash-account-pos-sale-tax/{id}', 'AccountController@updateCashAccountPosSaleTax')->name('accounts.updateCashAccountPosSaleTax');
        Route::get('/account/correct-accounts-discount', 'AccountController@correctAccountsProductWiseDiscount')->name('accounts.correctAccountsProductWiseDiscount');
        Route::get('/account/update-accounts-discount/{id}', 'AccountController@updateAccountsProductWiseDiscount')->name('accounts.updateAccountsProductWiseDiscount');

        Route::resource('/account', 'AccountController');

        Route::get('/fund-transfer/{id}', 'AccountController@getFundTransfer');
        
        Route::get('/account-number/{id}', 'AccountController@getAccNo');

        Route::post('/fund-transfer', 'AccountController@postFundTransfer');

        Route::get('/cheque-list', 'AccountController@getChequeList');

        Route::post('/cheque-deposit', 'AccountController@postChequeDeposit');

        Route::get('/cheque-deposit', 'AccountController@getChequeDeposit');
        
        Route::get('/realize-cheque-deposit', 'AccountController@getRealizeChequeDeposit');
        Route::get('/realize-cheque-list', 'AccountController@getRealizeChequeList');
        Route::post('/realize-cheque-deposit', 'AccountController@postRealizeChequeDeposit');
        Route::resource('/realized-cheques', 'RealizedChequeController');

        Route::get('/deposit/{id}', 'AccountController@getDeposit');

        Route::post('/deposit', 'AccountController@postDeposit');

        Route::get('/notes/{id}', 'AccountController@getNotes');

        Route::get('/reconcile/{id}', 'AccountController@reconcile');

        Route::get('/close/{id}', 'AccountController@close');

        Route::get('/disabled-account', 'AccountController@disabledAccount');

        Route::get('/disabled-status/{id}', 'AccountController@disabledStatus');

        Route::get('/delete-account-transaction/{id}', 'AccountController@destroyAccountTransaction');

        Route::get('/get-account-balance/{id}', 'AccountController@getAccountBalance');

        Route::get('/get-description/{id}', 'AccountController@getDescription');

        Route::get('/income-statement', 'AccountReportsController@incomeStatement');

        Route::get('/balance-sheet', 'AccountReportsController@balanceSheet');
        
        Route::get('/balance-sheet-comparison', 'AccountReportsController@balanceSheetComparison');

        Route::get('/trial-balance', 'AccountReportsController@trialBalance');
        
        Route::get('/trial-balance-cumulative', 'AccountReportsController@trialBalanceCumulative');

        Route::get('/payment-account-report', 'AccountReportsController@paymentAccountReport');

        Route::get('/link-account/{id}', 'AccountReportsController@getLinkAccount');

        Route::post('/link-account', 'AccountReportsController@postLinkAccount');

        Route::get('/check_account_number', 'AccountController@checkAccountNumber');

        Route::post('/check_account_names', 'AccountController@getAccountNames');

        Route::get('/cash-flow', 'AccountController@cashFlow');

        Route::post('/import', 'AccountController@postImportAccounts');

        Route::get('/import', 'AccountController@getImportAccounts')->name('accounts.import');
        
        Route::get('/edit-cheque-ob/{id}', 'AccountController@editChequeOb');
        Route::delete('/delete-cheque-ob/{id}', 'AccountController@deleteChequeOb');
        Route::post('/edit-cheque-ob/{id}', 'AccountController@updateChequeOb');


        Route::get('/main-account-book/{id}', 'AccountController@getMainAccountBook');

        Route::get('/main-account-balance/{id}', 'AccountController@getAccountBalanceMain');

        Route::get('/list-deposit-transfer', 'AccountController@listDepositTransfer');
        
        Route::get('/cheques-ob-details', 'AccountController@chequeObTransfer');

        Route::get('/edit-deposit-transfer/{id}', 'AccountController@editDepositTransfer');

        Route::post('/update-deposit-transfer/{id}', 'AccountController@updateDepositTransfer');
        

    });
    
    Route::group(['middleware' => 'IsSubscribed:discount_module'], function () {
        /* discount */
        Route::get('/discount-templates', [NewdiscountController::class, 'index']);
        Route::get('/list-discounts', [NewdiscountController::class, 'listdiscounts']);
           
   });
    
    Route::group(['prefix' => 'deposits-module','middleware' => 'IsSubscribed:deposits_module'], function () {
        Route::get('/check-insufficient-balance-for-accounts', 'DepositsController@getAccsForWhichToCheckInsufficientBalances');// @eng 15/2

        Route::get('/get-account-dp', 'DepositsController@getBankAccountDropDown');

        Route::get('/get-account-group-name-dp', 'DepositsController@getBankAccountByGroupDP');

        Route::get('/get-account-by-group-id/{group_id}', 'DepositsController@getAccountByGroupId');

        Route::get('/get-account-group-by-account/{type_id}', 'DepositsController@getAccountGroupByAccount');

        Route::get('/get-parent-account-by-type/{type_id}', 'DepositsController@getParentAccountsByType');

        Route::get('/account/image-modal', 'DepositsController@imageModal');

        Route::resource('/account', 'DepositsController');

        Route::get('/fund-transfer', 'DepositsController@getFundTransfer');
        
        Route::get('/account-number/{id}', 'DepositsController@getAccNo');

        Route::post('/fund-transfer', 'DepositsController@postFundTransfer');

        Route::get('/cheque-list', 'DepositsController@getChequeList');

        Route::post('/cheque-deposit', 'DepositsController@postChequeDeposit');

        Route::get('/cheque-deposit', 'DepositsController@getChequeDeposit');
        
        Route::get('/realize-cheque-deposit', 'DepositsController@getRealizeChequeDeposit');
        Route::get('/realize-cheque-list', 'DepositsController@getRealizeChequeList');
        Route::post('/realize-cheque-deposit', 'DepositsController@postRealizeChequeDeposit');

        Route::get('/deposit/{id}', 'DepositsController@getDeposit');

        Route::post('/deposit', 'DepositsController@postDeposit');

        Route::get('/get-account-balance/{id}', 'DepositsController@getAccountBalance');

        Route::get('/list-deposit-transfer', 'DepositsController@listDepositTransfer');
        
        Route::get('/cheques-ob-details', 'DepositsController@chequeObTransfer');

        Route::get('/edit-deposit-transfer/{id}', 'DepositsController@editDepositTransfer');

        Route::post('/update-deposit-transfer/{id}', 'DepositsController@updateDepositTransfer');

    });
    
    Route::post('/account_details', 'AccountController@account_details');

    Route::resource('default-account', 'DefaultAccountController');

    Route::resource('default-account-types', 'DefaultAccountTypeController');

    Route::get('default-account-group/get-account-groups-by-type/{type_id}', 'DefaultAccountGroupController@getDefaultAccountGroupByType');

    Route::resource('default-account-group', 'DefaultAccountGroupController');

    Route::get('get-account-groups/{type_id}', 'AccountGroupController@getAccountGroupByType');

    Route::resource('account-groups', 'AccountGroupController');

    Route::resource('account-types', 'AccountTypeController');

    Route::get('/site-settings', ['as' => 'site_settings.view', 'uses' => 'SiteSettingController@viewPage']);

    Route::post('/update-site-settings', ['as' => 'site_settings.update_settings', 'uses' => 'SiteSettingController@updateSettings']);

    Route::get('/help/view', ['as' => 'site_settings.help_view', 'uses' => 'SiteSettingController@help']);

    Route::post('/help/update', ['as' => 'site_settings.help_update', 'uses' => 'SiteSettingController@helpUpdate']);

    Route::post('/getcurrency', ['as' => 'site_settings.getcurrency', 'uses' => 'SiteSettingController@getCurrencyCode']);

    //Restaurant module

    Route::group(['prefix' => 'modules'], function () {

        Route::resource('tables', 'Restaurant\TableController');

        Route::resource('modifiers', 'Restaurant\ModifierSetsController');

        //Map modifier to products

        Route::get('/product-modifiers/{id}/edit', 'Restaurant\ProductModifierSetController@edit');

        Route::post('/product-modifiers/{id}/update', 'Restaurant\ProductModifierSetController@update');

        Route::get('/product-modifiers/product-row/{product_id}', 'Restaurant\ProductModifierSetController@product_row');

        Route::get('/add-selected-modifiers', 'Restaurant\ProductModifierSetController@add_selected_modifiers');

        Route::get('/kitchen', 'Restaurant\KitchenController@index');

        Route::get('/kitchen/mark-as-cooked/{id}', 'Restaurant\KitchenController@markAsCooked');

        Route::post('/refresh-orders-list', 'Restaurant\KitchenController@refreshOrdersList');

        Route::post('/refresh-line-orders-list', 'Restaurant\KitchenController@refreshLineOrdersList');

        Route::get('/orders', 'Restaurant\OrderController@index');

        Route::get('/orders/mark-as-served/{id}', 'Restaurant\OrderController@markAsServed');

        Route::get('/data/get-pos-details', 'Restaurant\DataController@getPosDetails');

        Route::get('/orders/mark-line-order-as-served/{id}', 'Restaurant\OrderController@markLineOrderAsServed');

    });

    Route::get('bookings/get-todays-bookings', 'Restaurant\BookingController@getTodaysBookings');

    Route::resource('bookings', 'Restaurant\BookingController');

    Route::resource('types-of-service', 'TypesOfServiceController');

    Route::get('sells/edit-shipping/{id}', 'SellController@editShipping');

    Route::put('sells/update-shipping/{id}', 'SellController@updateShipping');

    Route::get('shipments', 'SellController@shipments');

    Route::resource('warranties', 'WarrantyController');
    Route::resource('stock-taking', 'StockTakingController');

    Route::resource('family-members', 'FamilyController');

    Route::resource('sample-medical-product-import', 'ImportMedicalProductController');

    Route::post('massSavePharmacy', 'SampleMedicalProductController@massSavePharmacy');

    Route::resource('sample-medical-product-list', 'SampleMedicalProductController');

    Route::get('/clear_data/{type}', 'TempController@clearData');

    Route::post('/save_add_pos_data', 'TempController@saveAddPosTemp');

    Route::post('/save_sale_temp_data', 'TempController@saveAddSaleTemp');

    Route::post('/save_add_expense_data', 'TempController@saveAddExpenseTemp');

    Route::post('/save_purchase_temp_data', 'TempController@saveAddPurchaseTemp');

    Route::post('/save_settlement_sw_temp_data', 'TempController@saveSettlementSWTemp');

    Route::post('/save_sale_return_temp_data', 'TempController@saveSaleReturnTemp');

    Route::post('/save_stock_transfer_temp_data', 'TempController@saveStockTransferTemp');

    Route::post('/save_stock_Adjustment_temp_data', 'TempController@saveStockAdjustmentTemp');

    Route::get('customer-payment', 'CustomerSellController@paymentShow');

    Route::resource('customer-sales', 'CustomerSellController');

    Route::resource('customer-sell-return', 'CustomerSellReturnController');

    Route::get('customer-order-list', 'CustomerOrderController@getOrders');

    Route::resource('customer-order', 'CustomerOrderController');

    //common controller for document & note

    Route::get('get-document-note-page', 'DocumentAndNoteController@getDocAndNoteIndexPage');

    Route::post('post-document-upload', 'DocumentAndNoteController@postMedia');

    Route::resource('note-documents', 'DocumentAndNoteController');

    //chequer Dashboard
    // Route::get('chequerDashboard',[ChequeWriteController::class,'chequerDashboard'])->name('chequerDashboard');
    Route::get('chequerDashboard', 'Chequer\ChequeWriteController@chequerDashboard');
    Route::post('filter_monthly', 'Chequer\ChequeWriteController@filter_monthly');

    // Route::get('chequerDashboard', 'Chequer\ChequerDashboardController@chequerDashboard');

    // Cheque Writing Module

    Route::post('get-templates-uploads', 'Chequer\ChequeTemplateController@uploadImageFile');

    Route::post('get-templates-values', 'Chequer\ChequeTemplateController@getTemplateValues');

    Route::get('get-templates-link-bank/{id?}', 'Chequer\ChequeTemplateController@linkbank');

    Route::post('get-templates-add-bank', 'Chequer\ChequeTemplateController@addbank');

    Route::delete('get-templates-delete-bank/{id}', 'Chequer\ChequeTemplateController@delbank');

    Route::resource('cheque-templates', 'Chequer\ChequeTemplateController');

    Route::get('check-unique-cheque-no', 'Chequer\ChequeWriteController@getChequeNoUniqueOrNotCheck');

    Route::post('get-templatewise-bank-accounts', 'Chequer\ChequeWriteController@getTempleteWiseBankAccounts');

    Route::post('list-payee-temp', 'Chequer\ChequeWriteController@listOfPayeeTemp');

    Route::post('get-purchase-order-data-by-id', 'Chequer\ChequeWriteController@getPurchaseOrderDataById');

    Route::post('check-template-id', 'Chequer\ChequeWriteController@checkTemplateId');

    Route::post('get-template-cheque', 'Chequer\ChequeWriteController@getTemplatechaque');

    Route::post('get-next-cheque-no', 'Chequer\ChequeWriteController@getNextChequedNO');
    
    Route::get('get-account-type-cheque', 'Chequer\ChequeWriteController@getAccountType');
   
    Route::resource('cheque-write', 'Chequer\ChequeWriteController');
    
    Route::post('cheque-write-store', 'Chequer\ChequeWriteController@store')->name('cheque-write-store');
    
    Route::get('get-next-cheque-no-template', 'Chequer\ChequeWriteController@getChequedNoTemplate');

    //updated 
    Route::get('/get-next-cheque-number', 'Chequer\ChequeWriteController@getNextChequeNumber');

    Route::resource('stamps', 'Chequer\ChequerStampController');

    Route::resource('cheque-numbers', 'Chequer\ChequeNumberController');

    Route::resource('cheque-numbers-m-entries', 'Chequer\ChequeNumbersMEntryController');

    Route::get('payees', 'Chequer\ManagePayeeController@index')->name('payees.index');

    Route::post('create-payees', 'Chequer\ManagePayeeController@store')->name('payees.create');

    Route::get('ledger/{id?}', 'Chequer\ManagePayeeController@getLedger')->name('payees.ledger');

    Route::post('ledger/{id?}', 'Chequer\ManagePayeeController@getLedger')->name('payees.ledger');

    Route::get('delete-payees/{id?}', 'Chequer\ManagePayeeController@destroy')->name('payees.delete');

    Route::get('inactive-payees/{id?}', 'Chequer\ManagePayeeController@Inactive')->name('payees.inactive');

    Route::get('active-payees/{id?}', 'Chequer\ManagePayeeController@Active')->name('payees.active');

    Route::post('printed_cheque_details', 'Chequer\ChequeNumberController@printedcheque');

    Route::get('printed_cheque_details', 'Chequer\ChequeNumberController@printedcheque');

    Route::get('default_setting', 'SettingController@index');
    //@6948 Chequer Module
    Route::get('get-link-template-account/{id}', 'SettingController@linkTemplateBankaccount');
    Route::resource('default-fonts', 'Chequer\DefaultFontsController');
    //@end6948 

    Route::post('update_setting', 'SettingController@updateSettings');

    //@6949 write Cheque Cancel Cheque
    // Route::get('deleted_cheque_details', 'Chequer\DeletedChequeController@index');

    // Route::post('deleted_cheque_details', 'Chequer\DeletedChequeController@index');
    
    Route::resource('cancell_cheque_details', 'Chequer\CancellChequeController');
    Route::get('get-account-book-number/{id}', 'Chequer\CancellChequeController@getAccountBookNumber');
    Route::get('get-account-book-cheques/{id}', 'Chequer\CancellChequeController@getAccountBookNumberCheques');
    
        Route::get('cancell_cheque_details-add', 'Chequer\CancellChequeController@add');
        Route::get('/chequer/cancel-cheque/note/{id}', [App\Http\Controllers\Chequer\CancellChequeController::class, 'note'])->name('chequer.cancel_cheque.note');

          Route::post('cancell_cheque_details-note-store', 'Chequer\CancellChequeController@store_note');
    //@end6949

    Route::get('getBank', 'Chequer\DeletedChequeController@getBank');

    Route::post('add_deleted_cheque', 'Chequer\DeletedChequeController@store');

    Route::resource('ledger-discount', 'LedgerDiscountController', ['only' => [

        'edit', 'destroy', 'store', 'update'

    ]]);

    Route::get('get_account_sub_type', 'ContactController@getAccSubType');

    Route::get('get_discount_account', 'ContactController@getDiscountAcc');

});

Route::middleware(['EcomApi'])->prefix('api/ecom')->group(function () {

    Route::get('products/{id?}', 'ProductController@getProductsApi');

    Route::get('categories', 'CategoryController@getCategoriesApi');

    Route::get('brands', 'BrandController@getBrandsApi');

    Route::post('customers', 'ContactController@postCustomersApi');

    Route::get('settings', 'BusinessController@getEcomSettings');

    Route::get('variations', 'ProductController@getVariationsApi');

    Route::post('orders', 'SellPosController@placeOrdersApi');

});
Route::get('get-dose-for-medicine', [SugerReadingController::class, 'getDoseForMedicine'])->name('get-dose-for-medicine');
Route::get('get-medicines-for-health-issue', [SugerReadingController::class, 'getMedicinesForHealthIssue'])->name('get-medicines-for-health-issue');
Route::post('add_doctor', [DoctorController::class, 'store']);
Route::get('specializations', [DoctorController::class, 'getSpecialization']);
Route::get('medicine/view/{id}', [MedicationController::class, 'viewMed'])->name('medicine.view');

});

Route::get('/run-database-updates', function (DetectDatabaseChangesService $detectDatabaseChangesService) {
    $result = $detectDatabaseChangesService->runSqlUpdates();
    return response()->json($result);
});

Route::middleware(['web', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class, ScopeSessions::class])->group(function () {
    Route::get('/update-databases', 'DatabaseController@getDatabases')->name('database.getDatabases');
    Route::post('/update-databases', 'DatabaseController@updateDatabases')->name('database.updateDatabases');
});

// MPCS => Form 9A Settings Module
Route::group(['middleware' => ['web'], 'prefix' => 'mpcs', 'namespace' => 'Modules\MPCS\Http\Controllers'], function () {
    Route::get('/get-9a-form', [Form9ASettingsController::class, 'get9AForm'])->name('mpcs.forms.form_9a');
    Route::get('/get-form-9a-settings', [Form9ASettingsController::class, 'index'])->name('mpcs.forms.form_9a');
    Route::get('/get-form-settings', [Form9ASettingsController::class, 'create'])->name('mpcs.forms.partials.create_9a_form_settings');
    Route::post('/store-form-settings', [Form9ASettingsController::class, 'store'])->name('mpcs.forms.form_9a');
    Route::get('/edit-form-settings/{id}', [Form9ASettingsController::class, 'edit'])->name('mpcs.forms.partials.edit_9a_form_settings');
    Route::post('/update-form-settings/{id}', [Form9ASettingsController::class, 'update'])->name('mpcs.forms.form_9a');
});