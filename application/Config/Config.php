<?php

namespace MajesticStart\Config;

/**
 * Configuration handler for Majestic Start.
 */
class Config
{
    /**
     * The environment to run Majestic Start in.
     */
    private string $environment = "development";

    /**
     * The root URI of the application.
     */
    private string $rootUri = "http://localhost/";

    /**
     * The database credentials.
     */
    private array $databaseCredentials = [
        "host" => "localhost",
        "dbname" => "majesticstart",
        "user" => "",
        "pwd" => "",
    ];

    /**
     * The OpenWeatherMap API key.
     */
    private string $openWeatherMapApiKey = "";

    /**
     * Whether MajestiCloud features are enabled.
     * When false, the user won't be able to customize the portal.
     */
    private bool $majestiCloudEnabled = false;

    /**
     * The MajestiCloud API URI.
     */
    private string $majestiCloudApiUri = "";

    /**
     * The MajestiCloud User-Facing UI URI.
     */
    private string $majestiCloudFrontUri = "";

    /**
     * The MajestiCloud Client ID.
     */
    private string $majestiCloudClientId = "";

    /**
     * The MajestiCloud Client Secret.
     */
    private string $majestiCloudClientSecret = "";

    /**
     * The webmaster information.
     */
    private array $webmaster = [
        "name" => "John Doe",
        "location" => "",
        "email" => "webmaster@localhost",
        "phone" => "",
    ];

    /**
     * The hoster information.
     */
    private array $hoster = [
        "name" => "John Doe",
        "location" => "",
        "email" => "hoster@localhost",
        "phone" => "",
    ];

    /**
     * The singleton instance of the Config class.
     */
    private static Config $instance;

    /**
     * Returns the singleton instance of the Config class.
     * @return Config The singleton instance of the Config class.
     */
    public static function getInstance(): Config
    {
        if (!isset(self::$instance)) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    /**
     * Loads the environment variables from the .env file.
     * @param string $envPath The path to the .env file.
     */
    public function loadEnv($envPath): void
    {
        $env = parse_ini_file($envPath);

        foreach ($env as $key => $value) {
            if (stripos($key, ".") !== false) {
                $explode = explode(".", $key);
                $key = $explode[0];
                $subKey = $explode[1];
            }

            if (property_exists($this, $key)) {
                if (isset($subKey)) {
                    $this->$key[$subKey] = $value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }
}
