<?php

namespace MajesticStart\Database\Models;

use MajesticStart\Database\DatabaseQuerier;
use MajesticStart\Database\QueryBuilder;
use PDO;

final class SettingModel extends DatabaseQuerier
{
    protected string $tableName = "setting";
    protected string $primaryKey = "name";
    protected ?string $primaryKeyType = null;

    /**
     * @param array<string, string> $orderBy
     * @return array<string, string>
     */
    public function select_all($orderBy = []): array
    {
        return $this->db->select_query(
            "SELECT * FROM `setting`" . QueryBuilder::makeOrderBy($orderBy),
            [],
            "fetchAll",
            PDO::FETCH_KEY_PAIR
        );
    }
}
