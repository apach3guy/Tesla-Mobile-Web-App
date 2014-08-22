<?php

require_once('db.config');

function tesla_login($user,$passwd) {
    $fields_string= 'user_session[email]=' . $user . '&user_session[password]=' . $passwd;

        //open connection
        $ch = curl_init();
		$url = 'https://portal.vn.teslamotors.com/login';

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch,CURLOPT_COOKIEFILE, "cookie.txt");
        curl_exec($ch);

        //close connection
        curl_close($ch);
}

function tesla_read($suffix) {
	$url = "https://portal.vn.teslamotors.com/$suffix";
	$send_curl = curl_init($url);
	curl_setopt($send_curl, CURLOPT_URL, $url);
	curl_setopt($send_curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($send_curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($send_curl, CURLOPT_COOKIEFILE, 'cookie.txt');
	curl_setopt($send_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($send_curl, CURLOPT_VERBOSE, TRUE);
	$json_response = curl_exec($send_curl); 
	// $status = curl_getinfo($send_curl, CURLINFO_HTTP_CODE); 
	curl_close($send_curl);
	$response = json_decode($json_response, true);
	return $response;
}

// This one doesn't work because I haven't updated it
function open_stream($user,$id,$token) {
	$url = "https://streaming.vn.teslamotors.com/stream/" . $id . "/?values=speed,power";
	$send_curl = curl_init($url);
	curl_setopt($send_curl, CURLOPT_URL, $url);
	curl_setopt($send_curl, CURLOPT_USERPWD, $user . ":" . $token);
	curl_setopt($send_curl, CURLOPT_HEADER, 0);
	curl_setopt($send_curl, CURLOPT_BUFFERSIZE, 256);
	curl_setopt($send_curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($send_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($send_curl, CURLOPT_VERBOSE, TRUE);
	return curl_exec($send_curl);
	curl_close($send_curl);
}

?>