<?php
setcookie("oauth_token", '', time()-100);
setcookie("oauth_token_secret", '', time()-100);
header('Location: sign-in.php');
?>
