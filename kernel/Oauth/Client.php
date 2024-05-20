<?php
namespace Manomite\Oauth;

class Client
{

    public $provider;
    private $urlAuthorize = APP_DOMAIN.'/oauth/authorize';
    private $urlAccessToken = APP_DOMAIN.'/oauth/token';
    private $urlResourceOwnerDetails = APP_DOMAIN.'/oauth/api';
    public function __construct($clientId, $clientSecret, $redirectUrl)
    {
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $clientId,    // The client ID assigned to you by the provider
            'clientSecret' => $clientSecret,    // The client password assigned to you by the provider
            'redirectUri' => $redirectUrl,
            'urlAuthorize' => $this->urlAuthorize,
            'urlAccessToken' => $this->urlAccessToken,
            'urlResourceOwnerDetails' => $this->urlResourceOwnerDetails
        ]);
    }

    public function client_credentials()
    {
        try {
            // Try to get an access token using the client credentials grant.
            $accessToken = $this->provider->getAccessToken('client_credentials');
            return $accessToken->jsonSerialize();
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to get the access token
            throw new \Exception($e->getMessage());
        }
    }

    public function get_authorization_code()
    {
        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authorizationUrl = $this->provider->getAuthorizationUrl();
        return array('authorizationUrl' => $authorizationUrl);
    }

    public function process_authorization($code, $oauth2pkceCode = null)
    {
        try {
            // Optional, only required when PKCE is enabled.
            if(!empty($oauth2pkceCode)){
                $this->provider->setPkceCode($oauth2pkceCode);
            }

            // Try to get an access token using the authorization code grant.
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);
            $resourceOwner = $this->provider->getResourceOwner($accessToken);
            return array('token' => $accessToken->getToken(), 'refresh_token' => $accessToken->getRefreshToken(), 'expire' => $accessToken->getExpires(), 'resource' => $resourceOwner->toArray());
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function password($username, $password){
        try{
            $accessToken = $this->provider->getAccessToken('password', [
                'username' => $username,
                'password' => $password
            ]);
            return $accessToken->jsonSerialize();
    
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        throw new \Exception($e->getMessage());
    
    }
    }

    public function refresh($token){
        $newAccessToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $token
        ]);
        return $newAccessToken->jsonSerialize();
    }
}