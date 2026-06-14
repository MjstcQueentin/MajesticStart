<?php

namespace MajesticStart\Core;

use RuntimeException;

/**
 * Session data management on the MajestiCloud instance.
 * Currently supports MajestiCloud 3.0
 */
final class Session
{
    /**
     * Check if the user is logged in.
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        // MajestiCloud must be enabled
        if (!config("majestiCloudEnabled")) {
            return false;
        }

        // The session must be active and a User ID must be set
        return session_status() == PHP_SESSION_ACTIVE && !empty($_SESSION["user_uuid"]);
    }

    /**
     * Initialize the session with the given authorization code.
     *
     * @param string $code
     * @return void
     * @throws RuntimeException
     */
    public static function initSession(string $code): void
    {
        $ch = curl_init(config("majestiCloudApiUri") . "/oauth/token.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                "authorization_code" => $code,
                "client_uuid" => config("majestiCloudClientId"),
                "client_secret" => config("majestiCloudClientSecret"),
            ]),
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            throw new RuntimeException(curl_error($ch));
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
            throw new RuntimeException($response);
        }

        $response = json_decode($response, true);
        $_SESSION["token"] = $response["access_token"];

        curl_close($ch);
        self::fetchUserData();
    }

    /**
     * Fetch the logged user data on MajestiCloud and load it in $_SESSION.
     */
    public static function fetchUserData(): void
    {
        $ch = curl_init(config("majestiCloudApiUri") . "/user/");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $_SESSION["token"]],
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            throw new RuntimeException(curl_error($ch));
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
            throw new RuntimeException($response);
        }

        $response = json_decode($response, true);
        $_SESSION["user_uuid"] = $response["data"]["uuid"];
        $_SESSION["user"] = $response["data"];
    }

    /**
     * Fetch the logged user's profile picture.
     */
    public static function profilePicture(): string
    {
        $ch = curl_init(config("majestiCloudApiUri") . "/user/profile_picture.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $_SESSION["token"]],
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            throw new RuntimeException(curl_error($ch));
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
            throw new RuntimeException($response);
        }

        return "data:" . curl_getinfo($ch, CURLINFO_CONTENT_TYPE) . ";base64," . base64_encode($response);
    }

    /**
     * Destroy the current session.
     */
    public static function destroy(): void
    {
        $ch = curl_init(config("majestiCloudApiUri") . "/session/current.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $_SESSION["token"]],
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            throw new RuntimeException(curl_error($ch));
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
            throw new RuntimeException($response);
        }

        session_unset();
        session_destroy();
    }
}
