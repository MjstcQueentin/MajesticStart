<?php

class Database
{
    private $pdo;

    function __construct()
    {
        $this->pdo = new PDO("mysql:host=" . DATABASE['host'] . ";dbname=" . DATABASE['dbname'], DATABASE["user"], DATABASE["pwd"], [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]);
    }

    public function select_settings()
    {
        $stmt = $this->pdo->query("SELECT * FROM `setting`");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function select_searchengines($uuid = null)
    {
        if (!empty($uuid)) {
            $stmt = $this->pdo->prepare("SELECT * FROM `searchengine` WHERE `uuid` = ?");
            $stmt->execute([$uuid]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM `searchengine`");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function select_topics()
    {
        $stmt = $this->pdo->query("SELECT * FROM `topic` ORDER BY `is_official` DESC, `is_featured` DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select_newscategories($uuids = null)
    {
        if (!isset($uuids)) {
            $stmt = $this->pdo->query("SELECT * FROM `newscategory` ORDER BY `display_order` ASC;");
        } elseif (!empty($uuids)) {
            if (!is_array($uuids)) $uuids = [$uuids];
            $question_marks = str_pad("?", count($uuids) * 2 - 1, ",?");
            $stmt = $this->pdo->prepare("SELECT * FROM `newscategory` WHERE `uuid` IN ($question_marks) ORDER BY `display_order` ASC;");
            $stmt->execute($uuids);
        } else {
            return [];
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_newssource_status($newssource_uuid, $access_ok)
    {
        $stmt = $this->pdo->prepare("UPDATE `newssource` SET `access_ok` = ? WHERE `uuid` = ?");
        return $stmt->execute([$access_ok, $newssource_uuid]);
    }

    public function select_newssources($newscategory_uuid = null)
    {
        if (!empty($newscategory_uuid)) {
            $stmt = $this->pdo->prepare("SELECT `newssource`.* FROM `newssource` INNER JOIN `newscategory_has_newssource` ON `newscategory_has_newssource`.`newssource_uuid` = `newssource`.`uuid` WHERE `newscategory_uuid` = ?");
            $stmt->execute([$newscategory_uuid]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM `newssource`");
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select_bookmarks($user_id = null)
    {
        if (!empty($user_id)) {
            $stmt = $this->pdo->prepare("SELECT `bookmark`.* FROM `bookmark` WHERE `user_id` = ?");
            $stmt->execute([$user_id]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM `bookmark` WHERE `user_id` IS NULL");
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_bookmark($name, $url, $icon, $user_id)
    {
        $uuid = uniqid();
        $stmt = $this->pdo->prepare("INSERT INTO `bookmark`(`uuid`, `name`, `url`, `icon`, `user_id`) VALUES(?,?,?,?,?)");
        $stmt->execute([$uuid, $name, $url, $icon, $user_id]);

        return [
            "uuid" => $uuid,
            "name" => $name,
            "url" => $url,
            "icon" => $icon
        ];
    }

    public function delete_bookmark($uuid, $user_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM `bookmark` WHERE `uuid` = ? AND `user_id` = ?");
        return $stmt->execute([$uuid, $user_id]);
    }

    public function insert_or_update_user($user_uuid, $token)
    {
        $stmt = $this->pdo->prepare("INSERT INTO `user`(majesticloud_user_id, majesticloud_session_token) VALUES (?,?) ON DUPLICATE KEY UPDATE majesticloud_session_token = ?");
        $success = $stmt->execute([$user_uuid, $token, $token]);

        return $success;
    }

    public function select_user($user_uuid)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `user` WHERE `majesticloud_user_id` = ?");
        $stmt->execute([$user_uuid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_user($user_uuid, $set_searchengine, $set_newscategories)
    {
        $stmt = $this->pdo->prepare("UPDATE `user` SET `set_searchengine` = ?, `set_newscategories` = ? WHERE `majesticloud_user_id` = ?");
        $success = $stmt->execute([$set_searchengine, $set_newscategories, $user_uuid]);

        return $success;
    }
}
