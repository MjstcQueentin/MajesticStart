# Fichier de configuration

Vous devez créer `config.php` pour faire fonctionner Majestic Start.

```php
define("ENVIRONMENT", "dev");
define('ENVIRONMENT_ROOT', 'http://start.lesmajesticiels.localhost');

define("DATABASE", [
    "host" => "localhost",
    "dbname" => "homepage",
    "user" => "developer",
    "pwd" => "Azerty123$"
]);

define('WEATHER_APIKEY', 'dummy');

define('MAJESTICLOUD_URI', 'http://api.cloud.lesmajesticiels.localhost');
define('MAJESTICLOUD_USER_URI', 'http://cloud.lesmajesticiels.localhost');
define('MAJESTICLOUD_CLIENT_ID', 'dummy123-4567-89ab-cdef-hijklmnopqrs');
define('MAJESTICLOUD_CLIENT_SECRET', 'dummy');

define("WEBMASTER_NAME", "John Foo");
define("WEBMASTER_LOCATION", "Bar");
define("WEBMASTER_EMAIL", "foo@example.com");
define("WEBMASTER_PHONE", "0123456789");

define("HOSTER_NAME", "Hoster Society");
define("HOSTER_LOCATION", "Location");
define("HOSTER_EMAIL", "hosters@example.com");
define("HOSTER_PHONE", "0987654321");
```
