<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class UserModel extends DatabaseQuerier
{
    protected string $tableName = "user";
    protected string $primaryKey = "majesticloud_user_id";
    protected ?string $primaryKeyType = null;

    public function insert_one(array $data, bool $return_id = false)
    {
        // Par défaut, les catégories d'actualités sont toutes sélectionnées
        $categories = $this->db->select_query("SELECT `uuid` FROM `newscategory`");
        $categories = array_column($categories, 'uuid');

        $sql = "INSERT INTO `user`(majesticloud_user_id, majesticloud_session_token, set_newscategories) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `majesticloud_session_token` = ?";

        return $this->db->write_query($sql, [
            $data["majesticloud_user_id"],
            $data["majesticloud_session_token"],
            json_encode($categories),
            $data["majesticloud_session_token"]
        ]);
    }
}
