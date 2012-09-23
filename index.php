<?php

require_once 'config.php';
require_once 'EpiTwitter/EpiCurl.php';
require_once 'EpiTwitter/EpiOAuth.php';
require_once 'EpiTwitter/EpiTwitter.php';

$Twitter = new EpiTwitter(CONSUMER_KEY, CONSUMER_SECRET);

if (isset($_GET['oauth_token']) || (isset($_COOKIE['oauth_token']) && isset($_COOKIE['oauth_token_secret']))) {
    if (!isset($_COOKIE['oauth_token']) || !isset($_COOKIE['oauth_token_secret'])) {
        // user comes from twitter
        // send token to twitter
        $Twitter->setToken($_GET['oauth_token']);

        // get secret token
        $token = $Twitter->getAccessToken();
        // cookie lasts one month
        $time = time() + 60 * 60 * 24 * 30;
        // make the cookies for tokens
        setcookie('oauth_token', $token->oauth_token, $time);
        setcookie('oauth_token_secret', $token->oauth_token_secret, $time);

        // pass tokens to EpiTwitter object
        $Twitter->setToken(
                $token->oauth_token, $token->oauth_token_secret
        );
    } else {
        // user switched pages and came
        // back or got here directly, stilled logged in
        // pass tokens to EpiTwitter object
        $Twitter->setToken(
                $_COOKIE['oauth_token'], $_COOKIE['oauth_token_secret']
        );
    }
    // the $user variable is used in the "main.php" file.
    $user = $Twitter->get_accountVerify_credentials();
    // include main page
    include ('main.php');
} elseif (isset($_GET['denied'])) {
    // user denied access
    echo 'You must sign in through twitter first';
} else {
    // user not logged in
    header('Location: sign-in.php');
}
?>
