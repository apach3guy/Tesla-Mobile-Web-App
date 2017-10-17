<?php

//Global variables
    
	$id = '81527cff06843c8634fdc09e8ac0abefb46ac849f38fe1e431c2ef2106796384';
	$secret = 'c7257eb71a564034f9419ee651c7d0e5f7aa6bfbd18bafb5c5c033b093bb2fa3';
	$user = '';
	$passwd = '';


function tesla_login($id,$secret,$user,$passwd) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://owner-api.teslamotors.com/oauth/token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$request = array(
                'grant_type' => 'password',
                'client_id' => $id,
                'client_secret' => $secret,
                'email' => $user,
                'password' => $passwd,
				);
		$postdata = http_build_query($request);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_CAINFO, '/PHP/curl-ca-bundle.crt');
		return curl_exec($ch);
		curl_close($ch);
        
}

function tesla_read($suffix,$token) {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://owner-api.teslamotors.com/api/1/$suffix");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));
	curl_setopt($ch, CURLOPT_CAINFO, '/PHP/curl-ca-bundle.crt');
	$json_response = curl_exec($ch);
	$response = json_decode($json_response, true, 512, JSON_BIGINT_AS_STRING);
	return $response['response'];
	curl_close($ch);
	
}

function tesla_set($suffix,$level,$token) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://owner-api.teslamotors.com/api/1/$suffix");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",  
			"Authorization: Bearer $token"));
		curl_setopt($ch, CURLOPT_POST, true);
		$postdata = json_encode(array("percent" => $level), JSON_NUMERIC_CHECK);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_CAINFO, '/PHP/curl-ca-bundle.crt');
		$json_response = curl_exec($ch);
		$response = json_decode($json_response, true);
		return $response['response'];
		curl_close($ch);

}

?>