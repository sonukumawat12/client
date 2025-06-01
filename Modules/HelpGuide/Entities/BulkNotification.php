<?php

namespace Modules\HelpGuide\Entities;

use Modules\HelpGuide\Entities\Article;
use Modules\HelpGuide\Entities\ArticleTranslation;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class BulkNotification extends Model
{

    protected $guarded = ['id'];
    protected $casts = [
        'contacts' => 'array'
    ];

    public function getContacts()
    {
        return User::whereIn('id', $this->contacts ?? [])->pluck('name', 'id') ?? [];
    }

    public static function ultimateSMS($phone, $sms)
    {
        $sms_server = 'https://vimi8.xyz/api/v3/sms/send';
        $sender_id = setting('ultimate_sender_id');
        $ultimate_token = setting('ultimate_token');

        if (!empty($sms_server) && !empty($phone) && !empty($sms) && !empty($sender_id) && !empty($ultimate_token)) {

            $url = $sms_server;

            $sdata = array(
                'recipient' => $phone,
                'sender_id' => $sender_id,
                'type' => 'plain',
                'message' => $sms
            );

            $token = $ultimate_token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sdata));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                "Authorization: Bearer $token"
            ));
            $result = curl_exec($ch);
            curl_close($ch);

            if (empty($result)) {
                $error_message = error_get_last()['message'];
            }
        } else {
        }
    }

    public static function hutchSendSMS($phone, $sms)
    {
        $sms_server = "https://bsms.hutch.lk/api/sendsms";
        $auth_link = 'https://bsms.hutch.lk/api/login';
        $version = "v1";
        $username = setting('hutch_username');;
        $password = setting('hutch_password');;
        $mask = setting('hutch_mask');;

        if (
            !empty($sms_server) &&  !empty($auth_link) && !empty($version) &&
            !empty($username) && !empty($password) && !empty($mask)
        ) {

            $url = $sms_server;

            $sdata = array(
                "campaignName" => "Demo",
                "mask" => $mask,
                "numbers" => $phone,
                "content" => $sms
            );

            $token = self::hutchAuth();

            if (!empty($token)) {
                logger($token);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($sdata),
                    CURLOPT_HTTPHEADER => array(
                        'X-API-VERSION: v1',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token,
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
            }
        }
    }

    public static function hutchAuth()
    {
        $accessToken = null;
        $auth_link = '';
        $version = '';
        $username = '';
        $password = '';
        if (!empty($auth_link) && !empty($version) && !empty($username) && !empty($password)) {
            $curl = curl_init();

            $sdata = array(
                'username' => $username,
                'password' => $password,
            );

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HUTCH_AUTH_LINK'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>  json_encode($sdata),
                CURLOPT_HTTPHEADER => array(
                    'X-API-VERSION: ' . $version,
                    'Content-Type: application/json',
                ),
            ));

            $response = curl_exec($curl);
            $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Get the HTTP status code

            curl_close($curl);

            if ($status_code === 200) {
                $data = json_decode($response, true);

                if (isset($data['accessToken'])) {
                    $accessToken = $data['accessToken'];
                }
            }
        }

        return $accessToken;
    }
}
