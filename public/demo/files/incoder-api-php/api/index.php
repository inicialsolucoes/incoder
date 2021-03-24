<?php

/**
 * @see App
 */
require 'App.php';

$app = new App();

/////////////////////////////////////////////////////////////
// API: Navigator ///////////////////////////////////////////
/////////////////////////////////////////////////////////////

$app->route('/navigator', function($request, $response) {

	require 'Navigator.php';

	try {

		$path = $request->getParam('path');

		if (!$path) {
			throw new Exception('Path is required!');
		}

		$data = Navigator::readPath($path);

		$response->setData($data);
		$response->setMessage('Success!');
		$response->send();

	} catch (Exception $e) {

		$response->setStatus(false);
		$response->setMessage($e->getMessage());
		$response->send();
	}
});

/////////////////////////////////////////////////////////////
// API: Editor //////////////////////////////////////////////
/////////////////////////////////////////////////////////////

$app->route('/editor', function($request, $response) {

	require 'Editor.php';

	try {

		$path 	 = $request->getParam('path');
		$content = $request->getParam('content');

		if (!$path) {
			throw new Exception('Path is required!');
		}

		// if (!empty($content)) {
		// 	throw new Exception('Disabled!');
		// }

		$data = Editor::file($path, $content);

		if ($data == false) {
			throw new Exception('File error!');
		}

		$response->setData($data);
		$response->setMessage('Success!');
		$response->send();

	} catch (Exception $e) {

		$response->setStatus(false);
		$response->setMessage($e->getMessage());
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