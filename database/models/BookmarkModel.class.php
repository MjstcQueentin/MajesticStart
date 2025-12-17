<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class BookmarkModel extends DatabaseQuerier
{
    protected string $tableName = "bookmark";
    protected string $primaryKey = "uuid";
    protected ?string $primaryKeyType = "uniqid";

    public function delete_all_of_user($user_uuid)
    {
        $sql = "DELETE FROM `bookmark` WHERE majesticloud_user_id = ?";

        return $this->db->write_query($sql, [
            $user_uuid
        ]);
    }
}
