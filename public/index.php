<?php
require_once '../vendor/autoload.php';
session_start();

//Load Twig templating environment
$loader = new Twig_Loader_Filesystem('../templates/');
$twig = new Twig_Environment($loader, ['debug' => true]);
$data = [];
$error = null;
try {
	//Get the episodes from the API
	$client = new GuzzleHttp\Client();
	$res = $client->request('GET', 'http://3ev.org/dev-test-api/');
	$data = json_decode($res->getBody(), true);
	//Sort the episodes
	usort($data, function ($a, $b) {
		if ($a['season'] == $b['season']) {
			return $a['episode'] - $b['episode'];
		}
		return $a['season'] - $b['season'];
	});
	$_SESSION["data"] = $data;
} catch (Exception $e) {
	$error = 'Sorry, there was an error. Please try again by reloading the page.';
}

//Render the template
echo $twig->render('page.html', ["episodes" => $_SESSION["data"], "error" => $error]);
