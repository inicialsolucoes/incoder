<?php

/**
 * @see App
 */
require 'App.php';

$app = new App();

/////////////////////////////////////////////////////////////
// API: Poxy ////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

$app->route('/proxy', function($request, $response) {

	try {

		$api    = $request->getParam('api' , null, true);
		$user   = $request->getParam('user', null, true);
		$pass   = $request->getParam('pass', null, true);
		$method = $request->getMethod();

		if (!$api) {
			throw new Exception("API is required!");
		}

		////////////////////////////////////////////////////////////////
		// PARSE DATA //////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////

		$parseUrl = parse_url($api);
		$scheme   = @$parseUrl['scheme'];
		$host 	  = @$parseUrl['host'];
		$path 	  = @$parseUrl['path'];
		$query 	  = @$parseUrl['query'];
		$params   = http_build_query($request->getParam());

		if (!$scheme || !$host) {
			throw new Exception("API is invalid!");
		}

		$url = "{$scheme}://{$host}{$path}";
		$qry = "{$query}&{$params}";

		if ($method == 'GET') {
			$url.= "?{$qry}";
		}

		////////////////////////////////////////////////////////////////
		// CURL REQUEST ////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL			 , $url);
		curl_setopt($curl, CURLOPT_USERPWD 		 , "{$user}:{$pass}");
		curl_setopt($curl, CURLOPT_TIMEOUT		 , 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER		 , 0);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST , $method);
		curl_setopt($curl, CURLOPT_POSTFIELDS	 , $qry);

		$res = curl_exec($curl);

		curl_close($curl);

		$res = (object) json_decode($res);

		////////////////////////////////////////////////////////////////
		// RESPONSE ////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////////

		$response->setData   (@$res->data);
		$response->setMessage(@$res->message);
		$response->setStatus (@$res->status);
		$response->send();

	} catch (Exception $e) {

		$response->setMessage($e->getMessage());
		$response->setStatus(false);
		$response->send();
	}
});

/////////////////////////////////////////////////////////////
// API: Fallback ////////////////////////////////////////////
/////////////////////////////////////////////////////////////

$app->route('/', function($request, $response) {

	$data = array (
		'version' => '1.0.0'
	);

	$response->setData($data);
	$response->send();
});