<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Modules\HelpGuide\Entities\SocialAccount;
use Modules\HelpGuide\Entities\CustomerPurchase;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Resources\CustomerPurchaseResource;

class CustomerPurchaseController extends Controller
{
    public function listPurchase(Request $request, $user_id)
    {
        // $this->authorize('viewAny', CustomerPurchase::class);

        $rules = [];
        $rulesMsg = [];

        $rules['user_id'] =  ['required','exists:users,id'];
        $rulesMsg['user_id.required'] = __("User not specified");
        $rulesMsg['user_id.exists'] = __("User not exists or has been deleted");

        $validatedData = Validator::make(['user_id' => $user_id], $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "errors" => $validatedData->errors()];
        }

        $article = CustomerPurchase::where('user_id', $user_id)->paginate(50);
        
        return CustomerPurchaseResource::collection($article);

    }

    public function updateEnvatoCustomerPurchaseList(Request $request, $user_id)
    {
        // $this->authorize('update', CustomerPurchase::class);

        $rules = [];
        $rulesMsg = [];

        $rules['user_id'] =  ['required','exists:users,id'];
        $rulesMsg['user_id.required'] = __("User not specified");
        $rulesMsg['user_id.exists'] = __("User not exists or has been deleted");

        $validatedData = Validator::make(['user_id' => $user_id], $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "errors" => $validatedData->errors()];
        }

        // Get user Envato Account
        $envatoAccount = SocialAccount::select('user_id', 'id','access_token','refresh_token','updated_at')->where('provider', 'envato')->where('user_id', $user_id)->first();

        if(!$envatoAccount){
            return ['status' => 'fail', "errors" => [__('No Envato account found for the selected user')]];
        }

        // Owner Envato app
        if(!setting('envato_oauth_app_id', false) || !setting('envato_oauth_app_secret', false)){
            return ['status' => 'fail', "errors" => [__('Envato app not available, to fetch the envato user purchase list you must set your Envato app ID and secret on the settings page first')]];
        }

        //return $envatoAccount;
        $lastTokenUpdate = new Carbon($envatoAccount->updated_at);
        $now = Carbon::now();


        // Check if the token still valid or update it, token are valid for 1 Hour
        if($now->diffInMinutes($lastTokenUpdate) > 55){
            
            try {
                $newToken = $this->renewToken($envatoAccount->refresh_token);
            } catch(\Throwable $e){
                $errorDetails = __('Failed to grant Envato new Token').', '.$e->getMessage();
                return ['status' => 'fail', "errors" => [$errorDetails]];
            }

            // Save new Token
            $envatoAccount->access_token = $newToken;
            $envatoAccount->save();
        }

        try {
            $listPurchases = $this->getListPurchases($envatoAccount->access_token);
        } catch(\Throwable $e){
            $errorDetails = __('Failed to fetch Envato user purchases').', '.$e->getMessage();
            return ['status' => 'fail', "errors" => [$errorDetails]];
        }

        // If User has any purchase store it 
        if(count($listPurchases['purchases']) == 0){
             return ['status' => 'ok', "message" => __('The Envato user has not purchased any of your items')];
        }

        foreach($listPurchases['purchases'] as $item){

            $sold_at = new Carbon($item['sold_at']);
            $supported_until = new Carbon($item['supported_until']);

            $itemDetails = [
                "buyer" => $listPurchases['buyer']['username'],
                "amount" => $item['amount'],
                "sold_at" =>  $sold_at->format('Y-m-d H:i:s'),
                "license" => $item['license'],
                "support_amount" => $item['support_amount'],
                "supported_until" => $supported_until->format('Y-m-d H:i:s'),
                "item_id" => $item['item']['id'],
                "item_name" => $item['item']['name'],
                "item_icon" => $item['item']['previews']['icon_preview']['icon_url'],
            ];
            
            CustomerPurchase::updateOrCreate(
                ['user_id' => $envatoAccount->user_id, 'purchase_code' => $item['code']],
                $itemDetails
            );
        }

        return ['status' => 'ok', "message" => __('The Envato user purchase list has been updated')];
    }

    public function renewToken($refreshToken)
    {
        // Refresh token
        $newToken = Http::post('https://api.envato.com/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => setting('envato_oauth_app_id'),
            'client_secret' => setting('envato_oauth_app_secret')
        ]);

        if(!$newToken->successful()){
            if(isset($newToken->json()['error_description'])){
                throw new \ErrorException('Envato Error message : '.$newToken->json()['error_description']);
            }

            throw new \ErrorException('failed with status' . $response->status());
        }

        if(!isset($newToken->json()['access_token'])){
            throw new \ErrorException(__('Unknown error, please try again'));
        }
        
        // We got the new token
        return  $newToken->json()['access_token'];
    }

    public function getListPurchases($accessToken)
    {
        // Fetch purchases list from user Envato account
        $listPurchases = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
        ])->get('https://api.envato.com/v3/market/buyer/purchases');

        if(!$listPurchases->successful()){
            if(isset($listPurchases->json()['error'])){
                throw new \ErrorException(', Envato Error message : '.$listPurchases->json()['error']);
            }

            throw new \ErrorException('failed with status ' . $listPurchases->status());
        }

        if(!isset($listPurchases->json()['purchases'])){
            throw new \ErrorException(__('Unknown error, please try again'));
        }
        
        // We got the user details and purchases
        return  $listPurchases->json();
    }
}
