<?php
require '../vendor/autoload.php';

$client = new Google\Client();
$client->setClientId('669161406906-tgrh1mjpbemlopcrudv30aq68p7mbscu.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost');
$client->addScope(Google\Service\Drive::DRIVE);
$client->setAccessType('offline'); // Required for getting a refresh token
$client->setPrompt('consent');     // Force consent screen to ensure refresh token is generated

// If the code parameter is not present, redirect to Google Auth URL
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    echo "Open the following URL in your browser and authorize the application: <br>";
    echo "<a href='$authUrl'>$authUrl</a>";
} else {
    // After authorization, Google redirects back with a 'code' parameter
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $accessToken = $client->getAccessToken();

    echo "Access Token: <pre>" . print_r($accessToken, true) . "</pre>";
    echo "Refresh Token: " . $accessToken['refresh_token'];
}
?>