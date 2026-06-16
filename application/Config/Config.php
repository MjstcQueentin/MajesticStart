<?php

namespace MajesticStart\Config;

/**
 * Configuration handler for Majestic Start.
 */
class Config
{
    /**
     * The environment to run Majestic Start in.
     * The value should be "production" or "environment".
     *
     * @var string
     */
    private string $environment = "development";

    /**
     * The root URI of the application.
     * This should be the URI of the application without the trailing slash.
     *
     * @var string
     */
    private string $rootUri = "http://localhost/";

    /**
     * The database credentials.
     *
     * @var array<string, string>
     */
    private array $databaseCredentials = [
        "host" => "localhost",
        "dbname" => "majesticstart",
        "user" => "",
        "pwd" => "",
    ];

    /**
     * The OpenWeatherMap settings.
     *
     * @var array<string, string>
     */
    private array $openWeatherMap = [
        "apiKey" => "",
    ];

    /**
     * The MajestiCloud settings.
     * The "enabled" key should be set to true if MajestiCloud is enabled.
     * The "apiUri" key should be set to the URI of the MajestiCloud API.
     * The "frontUri" key should be set to the URI of the MajestiCloud frontend.
     * The "clientId" key should be set to the client ID of the MajestiCloud application.
     * The "clientSecret" key should be set to the client secret of the MajestiCloud application.
     *
     * @var array<string, string|bool>
     */
    private array $majestiCloud = [
        "enabled" => false,
        "apiUri" => "",
        "frontUri" => "",
        "clientId" => "",
        "clientSecret" => "",
    ];

    /**
     * The webmaster information.
     *
     * @var array<string, string>
     */
    private array $webmaster = [
        "name" => "John Doe",
        "location" => "",
        "email" => "webmaster@localhost",
        "phone" => "",
    ];

    /**
     * The hoster information.
     *
     * @var array<string, string>
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
     * Loads the environment variables from the config.ini file.
     * @param string $configPath The path to the config.ini file.
     */
    public function loadIni(string $configPath): void
    {
        $env = parse_ini_file($configPath, true, INI_SCANNER_TYPED);

        foreach ($env as $key => $value) {
            // Ignore properties that do not exist
            if (!property_exists($this, $key)) {
                continue;
            }

            if (is_array($this->$key)) {
                // When an array is expected
                // Throw an exception when the provided value is not an array
                if (!is_array($value)) {
                    throw new \Exception(
                        "The value for $key must be an array.",
                    );
                }

                // We ensure the user doesn't inject new keys into the config value
                $diff = array_diff(array_keys($value), array_keys($this->$key));
                if (!empty($diff)) {
                    throw new \Exception(
                        "$key must contain the following keys: " .
                            implode(array_keys($this->$key)),
                    );
                }
            } else {
                // When another type is expected, we enforce it
                if (gettype($this->$key) != gettype($value)) {
                    throw new \Exception(
                        "The value for $key must be of type " .
                            gettype($this->$key),
                    );
                }
            }

            // Now we are ready to store the value
            $this->$key = $value;
        }
    }
}
