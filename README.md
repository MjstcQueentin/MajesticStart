![Majestic Start](https://www.lesmajesticiels.org/assets/logos/smaller/start.png "Majestic Start")

# Majestic Start

This project aims to provide Internet users with a fully customizable homepage for their browser. It includes a search bar capable to pointing to any search engine, a bookmark feature, localized weather, and RSS feeds.

In the future, this project will also include an administration panel, so professional users may deploy it on their environment and customize it to fit their company's needs.

## How to use

Users who need the tool can use [the public version](https://start.lesmajesticiels.org).

Administrators may deploy it to their environment, but they should be aware that no tool for installing or administrating it is integrated at the moment. Skills in PHP and SQL are highly recommended.

## Prerequisites

- A web server. nginx or httpd fit this purpose.
- A MySQL Server. Using any other DBMS will require editing [the connection string](./database/DatabaseConnection.class.php).
- PHP 8.1 with the following extensions:
  - [PDO](https://www.php.net/manual/en/ref.pdo-mysql.php)
  - [cURL](https://www.php.net/manual/en/curl.installation.php)
  - [GD](https://www.php.net/manual/en/image.installation.php)
  - [Sessions](https://www.php.net/manual/en/session.installation.php)

After deploying Start on your server, you will need to edit [config.php](./config/config.php) to make it work.

## License

This project is distributed with the MIT License.

```
Copyright (c) 2025 Quentin Pugeat

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
```

## Les Majesticiels

Markdown Editor is part of **Les Majesticiels**, a project from Quentin Pugeat, whose mission is to provide tools for everyone to make IT more friendly.

[![Les Majesticiels](https://www.lesmajesticiels.org/assets/logos/smaller/lesmajesticiels.png "Les Majesticiels")](https://www.lesmajesticiels.org/)
