<?php
header('Content-type: text/json');

require './bp-client/ClientCredentials.php';
require './bp-client/BackplaneClient.php';
$config = parse_ini_file('./config.ini');

if (empty($_GET['bus'])) {
  echo '{"stat":"error","message":"missing bus"}';
  exit;
}
if (empty($_GET['messageUrl'])) {
  echo '{"stat":"error","message":"missing messageUrl"}';
  exit;
}

$credentials = new ClientCredentials();
$credentials->setBackplaneServerUrl($config['bp_server']);
$credentials->setClientId($config['bp_client_id']);
$credentials->setClientSecret($config['bp_client_secret']);
$client = new BackplaneClient($credentials);

$m = new Memcached();
$m->addServer('localhost', 11211);
if ($accessToken = $m->get('bp_token:' . $_GET['bus'])) {
  $client->setAccessToken($accessToken);
} else {
  $accessToken = $client->initializeAccessToken('bus:' . $_GET['bus'], 'client_credentials');
  // TODO: handle invalid grant responses
  $m->set('bp_token:' . $_GET['bus'], $accessToken, time() + $accessToken->getExpiresIn());
}

$message = $client->getSingleMessage($_GET['messageUrl']);
// TODO: handle invalid or expired token response (refresh token if needed)
echo json_encode($message);
