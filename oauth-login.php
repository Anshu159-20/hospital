<?php
session_start();
include 'include/config.php';

$provider = hms_get('provider');
$providers = array(
    'google' => array(
        'client_id' => GOOGLE_CLIENT_ID,
        'authorize' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'scope' => 'openid email profile'
    ),
    'github' => array(
        'client_id' => GITHUB_CLIENT_ID,
        'authorize' => 'https://github.com/login/oauth/authorize',
        'scope' => 'read:user user:email'
    )
);

if (!isset($providers[$provider]) || $providers[$provider]['client_id'] === '') {
    hms_flash('warning', 'This social login is not configured yet.');
    hms_redirect('user-login.php');
}

$state = bin2hex(random_bytes(32));
$_SESSION['oauth_state'] = $state;
$_SESSION['oauth_provider'] = $provider;
$redirectUri = hms_oauth_redirect_uri();
$query = array(
    'client_id' => $providers[$provider]['client_id'],
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => $providers[$provider]['scope'],
    'state' => $state
);

if ($provider === 'google') {
    $query['access_type'] = 'online';
    $query['prompt'] = 'select_account';
}

hms_redirect($providers[$provider]['authorize'] . '?' . http_build_query($query));

function hms_oauth_redirect_uri()
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/oauth-callback.php';
}
?>