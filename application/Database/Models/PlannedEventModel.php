<?php

namespace MajesticStart\Database\Models;

use MajesticStart\Database\DatabaseQuerier;

final class PlannedEventModel extends DatabaseQuerier
{
    protected string $tableName = "planned_event";
    protected string $primaryKey = "id";
    protected ?string $primaryKeyType = "autoincrement";

    /**
     * Selects all events that are planned for today
     *
     * @return array
     */
    public function select_today(): array
    {
        $sql = "SELECT * FROM `planned_event` WHERE ? BETWEEN from_date AND until_date";

        return $this->db->select_query($sql, [date("md")], "fetch");
    }
}
