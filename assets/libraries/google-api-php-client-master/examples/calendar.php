<?php
error_reporting(E_ALL);
require_once './../src/Google/autoload.php';
session_start();

if ((isset($_SESSION)) && (!empty($_SESSION))) {
    echo "There are cookies<br>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {

    $client = new Google_Client();
    $client->setApplicationName("Google Calendar PHP Starter Application");
    $client->setClientId('');
    $client->setClientSecret('');
    $client->setRedirectUri('');
    $client->addScope("https://www.googleapis.com/auth/urlshortener");
    $client->setScopes( Google_Service_Calendar::CALENDAR_READONLY );

    if (isset($_SESSION['access_token']) ){
        $accessToken = file_get_contents($_SESSION['access_token']);
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for an access token.
        $accessToken = $client->authenticate($authCode);

        // Store the credentials to disk.
        $_SESSION['access_token'] = $accessToken;
    }
    $client->setAccessToken($accessToken);

    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
        $client->refreshToken($client->getRefreshToken());
        $_SESSION['access_token'] =  $client->getAccessToken();
    }
    return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
        $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
    }
    return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

// Print the next 10 events on the user's calendar.
$calendarId = 'primary';
$optParams = array(
    'maxResults' => 10,
    'orderBy' => 'startTime',
    'singleEvents' => TRUE,
    'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);

if (count($results->getItems()) == 0) {
    print "No upcoming events found.\n";
} else {
    print "Upcoming events:\n";
    foreach ($results->getItems() as $event) {
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        printf("%s (%s)\n", $event->getSummary(), $start);
    }
}