<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class UserModel extends DatabaseQuerier
{
    protected string $tableName = "user";
    protected string $primaryKey = "majesticloud_user_id";
    protected ?string $primaryKeyType = null;

    public function insert_one(array $data, bool $return_id = false)
    {
        $sql = "INSERT INTO `user`(majesticloud_user_id, majesticloud_session_token) VALUES (?,?) ON DUPLICATE KEY UPDATE `majesticloud_session_token` = ?";

        return $this->db->write_query($sql, [
            $data["majesticloud_user_id"],
            $data["majesticloud_session_token"],
            $data["majesticloud_session_token"]
        ]);
    }
}
