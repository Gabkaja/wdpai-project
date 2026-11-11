<?php

declare(strict_types=1);

namespace App\Service;

final class KickClient
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId = getenv('KICK_CLIENT_ID') ?: '';
        $this->clientSecret = getenv('KICK_CLIENT_SECRET') ?: '';
        $this->redirectUri = getenv('KICK_REDIRECT_URI') ?: '';
    }

    private function requireOAuthConfig(): void
    {
        if ($this->clientId === '' || $this->clientSecret === '' || $this->redirectUri === '') {
            throw new \RuntimeException('Kick OAuth configuration is incomplete.');
        }
    }

    public function buildAuthorizationUrl(string $state, string $codeChallenge): string
    {
        $this->requireOAuthConfig();
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'user:read',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'state' => $state,
        ];

        return 'https://id.kick.com/oauth/authorize?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    public function exchangeCode(string $code, string $codeVerifier): string
    {
        $this->requireOAuthConfig();
        $body = http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code_verifier' => $codeVerifier,
        ], '', '&', PHP_QUERY_RFC3986);

        $ch = curl_init('https://id.kick.com/oauth/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log('Kick token exchange failed: ' . $curlError);
            throw new \RuntimeException('Kick token exchange failed.');
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('Kick token exchange failed with HTTP ' . $httpCode);
            throw new \RuntimeException('Kick token exchange failed with HTTP ' . $httpCode);
        }

        $data = json_decode($response, true);

        if (!is_array($data) || !isset($data['access_token']) || !is_string($data['access_token'])) {
            error_log('Kick token exchange returned invalid response.');
            throw new \RuntimeException('Kick token exchange returned invalid response.');
        }

        return $data['access_token'];
    }

    public function fetchCurrentUser(string $accessToken): array
    {
        $this->requireOAuthConfig();
        $ch = curl_init('https://api.kick.com/public/v1/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log('Kick current user fetch failed: ' . $curlError);
            throw new \RuntimeException('Kick current user fetch failed.');
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('Kick current user fetch failed with HTTP ' . $httpCode);
            throw new \RuntimeException('Kick current user fetch failed with HTTP ' . $httpCode);
        }

        $data = json_decode($response, true);

        if (!is_array($data) || !isset($data['data']) || !is_array($data['data']) || empty($data['data'])) {
            error_log('Kick current user fetch returned invalid response.');
            throw new \RuntimeException('Kick current user fetch returned invalid response.');
        }

        $user = $data['data'][0];

        if (!isset($user['user_id']) || !isset($user['name'])) {
            error_log('Kick current user fetch returned missing user fields.');
            throw new \RuntimeException('Kick current user fetch returned missing user fields.');
        }

        return [
            'id' => (int) $user['user_id'],
            'username' => (string) $user['name'],
        ];
    }

    public function isChannelLive(string $slug): bool
    {
        $ch = curl_init('https://kick.com/api/v2/channels/' . rawurlencode($slug));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            error_log('Kick channel fetch failed for ' . $slug . ': ' . $curlError . ' HTTP ' . $httpCode);

            return false;
        }

        $data = json_decode($response, true);

        if (!is_array($data) || !isset($data['livestream'])) {
            error_log('Kick channel fetch returned invalid response for ' . $slug);

            return false;
        }

        return $data['livestream'] !== null;
    }
}
