<?php

namespace App\Utils;

use Illuminate\Http\Request;
use \App\UserLocation;

class LocationUtil
{

	public function __construct(){
		
	}
	
	
	public static function getIpInfo( Request $request ){
		$success = FALSE;
		$msg = '';
		$data = [];
		
		$ip_addresses = $request->ips();
		
		if( $ip_addresses ){
			$ip_address = array_pop($ip_addresses);

			$headers = [
				'Cache-Control: no-cache', 
				'Content-Type: application/json', 
			];
			
			$rs = "http://ip-api.com/json/{$ip_address}";
			$ch = curl_init($rs);
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			
			// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			$resp = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);

			if( $error ){
				$msg = 'ERROR: ' . $error;
			}else{
				if( $resp ){
					$success = TRUE;
					$msg = 'Request completed';
					// $msg .= $resp;
					$data = json_decode($resp, TRUE);
				}else{
					$msg = 'Request failed';
				}
			}
		}else{
			$msg = 'No IP provided';
		}

		return [
			'success' => $success, 
			'msg' => $msg, 
			'data' => $data, 
		];
	}

	public static function getLocationFromIP($ip, $accessToken) {
		$url = "https://api.ipregistry.co/{$ip}?key={$accessToken}";
	
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$response = curl_exec($ch);
	
		curl_close($ch);
	
		$data = json_decode($response, true);
	
		if (isset($data['location']['latitude'])) {
			return array('latitude' => $data['location']['latitude'], 'longitude' => $data['location']['longitude']);
		} else {
			return array('latitude' => null, 'longitude' => null);
		}
	}
	
	public static function getRequestLocation( Request $request ){
		$success = FALSE;
		$msg = '';
		$data = [];
		
		$ip_resp = self::getIpInfo( $request );
		$api_key = 'AIzaSyCsHrbMB_bsgtLPVdv63bbvLOoszPN4bw8'; // Replace with your API key
		if($request->latitude && $request->longitude){
			$latitude = $request->latitude;
			$longitude = $request->longitude;
			$location_data_source = "Device GPS coordinates";
		} else {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				//ip from share internet
				$ip=$_SERVER['HTTP_CLIENT_IP'];
			}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				//ip pass from proxy
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
				$ip=$_SERVER['REMOTE_ADDR'];
			}
			$location = self::getLocationFromIP($ip, "ira_HFAHWyqr33iuHlkRZhBkcBCQgL3h5Z49tr3s");
			$location_data_source = "Device IP Address";
		}
		$timestamp = time();
		
		if( $ip_resp['success'] || true){
			$ip_info = $ip_resp['data'];
			$latitude = $latitude ?? $location['latitude'] ?? $ip_info['lat'] ?? null;
			$longitude = $longitude ?? $location['longitude'] ?? $ip_info['lon'] ?? null;
			$rs = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&timestamp={$timestamp}&key={$api_key}";
			
			$headers = [
				'Cache-Control: no-cache', 
				'Content-Type: application/json', 
			];
			
			$ch = curl_init($rs);
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			
			// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			$resp = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);

			if( $error ){
				$msg = 'ERROR: ' . $error;
			}else{
				$resp = json_decode($resp, TRUE);
				if( $resp && $resp['status'] == 'OK' ){
					$success = TRUE;
					$msg = 'Request completed';
					// $msg .= $resp;

					$location_keys = [
						'country' => 'country', 
						'city' => 'locality', 
						'state' => 'administrative_area_level_1', 
						'district' => 'administrative_area_level_2', 
						'constituency' => 'administrative_area_level_3', 
						'neighborhood' => 'neighborhood', 
						'sublocality' => 'sublocality', 
						'route' => 'route', 
						'landmark' => 'landmark', 
						'address' => 'street_number', 
						'zip_code' => 'postal_code', 
					];
					
					foreach( $location_keys as $loc_index => $loc_key ){
						foreach( $resp['results'] as $addr_comp ){
							if( in_array($loc_key, $addr_comp['types']) ){
								$value = $addr_comp['formatted_address'];

								if( $loc_index != 'country' ){
									if( $loc_index == 'city' || $loc_index == 'state' ){
										$value = str_replace(', '.$data['country'], '', $value);
									}else{
										$value = str_replace(', '.$data['city'].', '.$data['country'], '', $value);
									}
								}
								$data[ $loc_index ] = $value;
								break;
							}
						}

						if( !isset($data[ $loc_index ]) ){
							$data[ $loc_index ] = '';
						}
					}

					$address = null;
					$postalCode = null;
					if (!empty($resp['results'])) {
						$addressComponents = $resp['results'][0]['address_components'];
						$address = $resp['results'][0]['formatted_address'];
				
						// Extract postal code
						foreach ($addressComponents as $component) {
							if (in_array('postal_code', $component['types'])) {
								$postalCode = $component['long_name'];
								break;
							}
						}
					}

					$data['timezone'] = $resp['timeZoneId'] ?? $ip_info['timezone'] ?? "";
					$data['latitude'] = $latitude;
					$data['longitude'] = $longitude;
					$data['location_data_source'] = $location_data_source;
					$data['address'] = (!empty($data['address'])) ? $data['address'] : ($address ?? "");
					
					if( !$data['zip_code'] ){
						$data['zip_code'] = $postalCode ?? $ip_info['zip'] ?? "";
					}
				}else{
					$msg = 'Request failed';
				}
			}
		}else{
			$msg = 'IP info not resolved';
		}
		
		return [
			'success' => $success, 
			'msg' => $msg, 
			'data' => $data, 
		];
	}
	
	
	public static function storeUserLocation( $user_id, $access_type, $data ){
		$location = new UserLocation;
		$location->user_id = $user_id;
		$location->access_type = $access_type;
		$location->access_time = time();
		$location->country = $data['country'];
		$location->city = $data['city'];
		$location->state = $data['state'];
		$location->district = $data['district'];
		$location->constituency = $data['constituency'];
		$location->neighborhood = $data['neighborhood'];
		$location->sublocality = $data['sublocality'];
		$location->route = $data['route'];
		$location->landmark = $data['landmark'];
		$location->address = $data['address'];
		$location->zip_code = $data['zip_code'];
		$location->timezone = $data['timezone'];
		$location->latitude = $data['latitude'];
		$location->longitude = $data['longitude'];
		$location->location_data_source = $data['location_data_source'];
		$location->save();
	}
	
	
}

