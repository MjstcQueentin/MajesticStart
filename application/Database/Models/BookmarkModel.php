<?php

namespace MajesticStart\Database\Models;

use MajesticStart\Database\DatabaseQuerier;

final class BookmarkModel extends DatabaseQuerier
{
    protected string $tableName = "bookmark";
    protected string $primaryKey = "uuid";
    protected ?string $primaryKeyType = "uniqid";

    /**
     * Delete all bookmarks of a user
     *
     * @param string $user_uuid
     * @return bool
     */
    public function delete_all_of_user($user_uuid)
    {
        $sql = "DELETE FROM `bookmark` WHERE user_id = ?";

        return $this->db->write_query($sql, [
            $user_uuid
        ]);
    }
}
