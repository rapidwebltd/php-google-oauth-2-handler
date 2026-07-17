<?php

namespace RapidWeb\GoogleOAuth2Handler;

use InvalidArgumentException;
use RuntimeException;

class GoogleOAuth2Handler
{
    private $clientId;
    private $clientSecret;
    private $scopes;
    private $refreshToken;
    private $redirectUri;
    private $client;
    
    public $authUrl;

    public function __construct($clientId, $clientSecret, $scopes, $refreshToken = '', $redirectUri = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
        $this->refreshToken = $refreshToken;
        $this->redirectUri = $redirectUri;

        if (!$this->refreshToken && !$this->redirectUri) {
            throw new InvalidArgumentException('A redirect URI is required when requesting a new Google authorization code.');
        }

        $this->setupClient();
    }

    private function setupClient()
    {
        $this->client = new \Google_Client();

        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        if ($this->redirectUri) {
            $this->client->setRedirectUri($this->redirectUri);
        }
        $this->client->setAccessType('offline');
        if (method_exists($this->client, 'setPrompt')) {
            $this->client->setPrompt('consent');
        } else {
            $this->client->setApprovalPrompt('force');
        }

        foreach ($this->scopes as $scope) {
            $this->client->addScope($scope);
        }

        if ($this->refreshToken) {
            $this->client->refreshToken($this->refreshToken);
        } else {
            $this->authUrl = $this->client->createAuthUrl();
        }
    }

    public function getRefreshToken($authCode)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);

        if (isset($accessToken['error'])) {
            throw new RuntimeException('Google rejected the authorization code: '.($accessToken['error_description'] ?? $accessToken['error']));
        }

        if (empty($accessToken['refresh_token'])) {
            throw new RuntimeException('Google did not return a refresh token. Revoke the existing grant and authorize again with consent.');
        }

        return $accessToken['refresh_token'];
    }

    public function performRequest($method, $url, $body = null, array $options = [])
    {
        $httpClient = $this->client->authorize();

        if ($body !== null && !array_key_exists('body', $options)) {
            $options['body'] = $body;
        }

        return $httpClient->request($method, $url, $options);
    }
}
