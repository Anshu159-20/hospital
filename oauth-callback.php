<?php
session_start();
include 'include/config.php';

$provider = isset($_SESSION['oauth_provider']) ? $_SESSION['oauth_provider'] : '';
$state = hms_get('state');
$code = hms_get('code');
$expectedState = isset($_SESSION['oauth_state']) ? $_SESSION['oauth_state'] : '';
unset($_SESSION['oauth_state'], $_SESSION['oauth_provider']);

if ($provider === '' || $state === '' || $expectedState === '' || !hash_equals($expectedState, $state) || $code === '') {
    hms_flash('danger', 'The social login request was invalid or expired.');
    hms_redirect('user-login.php');
}

$redirectUri = hms_oauth_redirect_uri();
$token = array();
if ($provider === 'google') {
    $token = hms_oauth_request('https://accounts.google.com/o/oauth2/token', array(
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code'
    ));
} else {
    $token = hms_oauth_request('https://github.com/login/oauth/access_token', array(
        'code' => $code,
        'client_id' => GITHUB_CLIENT_ID,
        'client_secret' => GITHUB_CLIENT_SECRET,
        'redirect_uri' => $redirectUri
    ), array('Accept: application/json'));
}

if (empty($token['access_token'])) {
    hms_flash('danger', 'The social login provider did not return an access token.');
    hms_redirect('user-login.php');
}

if ($provider === 'google') {
    $profile = hms_oauth_request('https://openidconnect.googleapis.com/v1/userinfo', array(), array(
        'Authorization: Bearer ' . $token['access_token']
    ), false);
    $email = isset($profile['email']) ? strtolower(trim($profile['email'])) : '';
    $name = isset($profile['name']) ? trim($profile['name']) : '';
    $verified = !empty($profile['email_verified']);
} else {
    $profile = hms_oauth_request('https://api.github.com/user', array(), array(
        'Authorization: Bearer ' . $token['access_token'],
        'User-Agent: Hospital-Management-System'
    ), false);
    $emails = hms_oauth_request('https://api.github.com/user/emails', array(), array(
        'Authorization: Bearer ' . $token['access_token'],
        'User-Agent: Hospital-Management-System'
    ), false);
    $email = '';
    $verified = false;
    foreach ($emails as $emailRow) {
        if (!empty($emailRow['primary']) && !empty($emailRow['verified'])) {
            $email = strtolower(trim($emailRow['email']));
            $verified = true;
            break;
        }
    }
    $name = isset($profile['name']) && $profile['name'] !== '' ? trim($profile['name']) : (isset($profile['login']) ? trim($profile['login']) : '');
}

if (!$verified || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    hms_flash('danger', 'A verified email address is required for social login.');
    hms_redirect('user-login.php');
}

$user = hms_fetch_one($con, 'SELECT * FROM users WHERE email = ?', 's', array($email));
if (!$user) {
    $password = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
    hms_execute($con, 'INSERT INTO users(fullName, email, password) VALUES(?, ?, ?)', 'sss', array($name !== '' ? $name : $email, $email, $password));
    $user = hms_fetch_one($con, 'SELECT * FROM users WHERE email = ?', 's', array($email));
}

if (!$user) {
    hms_flash('danger', 'We could not create your patient account.');
    hms_redirect('user-login.php');
}

session_regenerate_id(true);
$_SESSION['login'] = $email;
$_SESSION['id'] = (int) $user['id'];
$_SESSION['role'] = 'patient';
hms_redirect('dashboard.php');

function hms_oauth_redirect_uri()
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/oauth-callback.php';
}

function hms_oauth_request($url, $params = array(), $headers = array(), $post = true)
{
    $options = array('http' => array(
        'method' => $post ? 'POST' : 'GET',
        'header' => implode("\r\n", array_merge(array('Content-Type: application/x-www-form-urlencoded'), $headers)),
        'ignore_errors' => true,
        'timeout' => 15
    ));
    if ($post) {
        $options['http']['content'] = http_build_query($params);
    } elseif (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    $response = file_get_contents($url, false, stream_context_create($options));
    $data = json_decode((string) $response, true);
    return is_array($data) ? $data : array();
}
?>