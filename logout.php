<?php

/**
 * this file unsets the cookies (sets them to an invalid time) and then
 * redirects to the login page.
 */
setcookie("oauth_token", '', time()-100);
setcookie("oauth_token_secret", '', time()-100);
header('Location: sign-in.php');
?>
