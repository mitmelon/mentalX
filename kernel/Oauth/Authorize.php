<?php
use Manomite\{
    Mouth\Auth,
    Mouth\Neck,
    Protect\PostFilter,
    SDK\Google\Recaptcha
};
use Slim\Csrf\Guard;
use Slim\Psr7\Factory\ResponseFactory;
include_once __DIR__ . '/Server.php';

$auth = (new Auth())->loggedin();

if (isset($auth['status']) && $auth['status'] !== false) {
    if ($auth['level'] !== 'user') {
        $neck->error('Sorry! you are not allowed to request for this type of resource.', 505);
        exit;
    }
    $security = new PostFilter();
    $neck = new Neck();
    
    $request = \OAuth2\Request::createFromGlobals();
    $res = new \OAuth2\Response();

    // validate the authorize request
    if (!$server->validateAuthorizeRequest($request, $res)) {
        $res->send();
        die;
    }
    // display an authorization form
    if (empty($_POST)) {
        $params = $request->getAllQueryParameters();
        $data = $storage->getClientDetails($params['client_id']);
        /*
            [state] => 881d650badce0e1cd9555c6ee7a1f25d
            [response_type] => code
            [approval_prompt] => auto
            [redirect_uri] => http://127.0.0.1/manomite/manoexpress/dashboard
            [client_id] => testclient123
        */
        exit($neck->oauth_accept($params, $data['user_id']));
    } else {
        // print the authorization code if the user has authorized your client
        $token = $security->strip($_POST['token']);
        $recap = new Recaptcha($token);

        if($security->nothing($token)){
            $neck->error('Sorry! request failed security code accessment check', 400);
            exit;
        }
        if($recap->verify(0.9) === false){
            $neck->error(EXTERNAL_REJECTED, 508);
            exit;
        }
        $is_authorized = ($security->strip($_POST['response']) === 'allow');
        $server->handleAuthorizeRequest($request, $res, $is_authorized, $auth['user']);
        if ($is_authorized) {
            // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
            $res->getHttpHeader('Location');
        }
    }
    $res->send();
}
$responseFactory = new ResponseFactory();
$guard = new Guard($responseFactory);
$csrfNameKey = $guard->getTokenNameKey();
$csrfValueKey = $guard->getTokenValueKey();
$keyPair = $guard->generateToken();
$neck = new Neck();
$neck->gate($csrfNameKey, $csrfValueKey, $keyPair['csrf_name'], $keyPair['csrf_value']);