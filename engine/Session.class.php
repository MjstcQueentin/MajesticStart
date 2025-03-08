<?php

class SessionUtils
{
    public static function is_logged_in()
    {
        if (defined("MAJESTICLOUD_ENABLE") && MAJESTICLOUD_ENABLE == false) return false;
        return session_status() == PHP_SESSION_ACTIVE && !empty($_SESSION["user_uuid"]);
    }

    public static function init_session($code)
    {
        $ch = curl_init(MAJESTICLOUD_URI . "/oauth/token.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "X-MAJESTICLOUD-CLIENT: " . $_SERVER["REMOTE_ADDR"]
            ],
            CURLOPT_POSTFIELDS => http_build_query([
                "authorization_code" => $code,
                "client_uuid" => MAJESTICLOUD_CLIENT_ID,
                "client_secret" => MAJESTICLOUD_CLIENT_SECRET
            ])
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) throw new RuntimeException(curl_error($ch));
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) throw new RuntimeException($response);

        $response = json_decode($response, true);
        $_SESSION["token"] = $response["access_token"];

        curl_close($ch);
        self::get_user_data();
    }

    public static function get_user_data()
    {
        $ch = curl_init(MAJESTICLOUD_URI . "/user/");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $_SESSION["token"]
            ]
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) throw new RuntimeException(curl_error($ch));
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) throw new RuntimeException($response);

        $response = json_decode($response, true);
        $_SESSION["user_uuid"] = $response["data"]["uuid"];
        $_SESSION["user"] = $response["data"];
    }

    public static function profile_picture()
    {
        $ch = curl_init(MAJESTICLOUD_URI . "/user/profile_picture.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $_SESSION["token"]
            ]
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) throw new RuntimeException(curl_error($ch));
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) throw new RuntimeException($response);

        return "data:" . curl_getinfo($ch, CURLINFO_CONTENT_TYPE) . ";base64," . base64_encode($response);
    }

    public static function destroy()
    {
        $ch = curl_init(MAJESTICLOUD_URI . "/session/current.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $_SESSION["token"]
            ]
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) throw new RuntimeException(curl_error($ch));
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) throw new RuntimeException($response);

        session_unset();
        session_destroy();
    }
}
