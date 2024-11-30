<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class PlannedEventModel extends DatabaseQuerier
{
    protected string $tableName = "planned_event";
    protected string $primaryKey = "id";
    protected ?string $primaryKeyType = "autoincrement";

    public function select_today()
    {
        $sql = "SELECT * FROM `planned_event` WHERE ? BETWEEN from_date AND until_date";

        return $this->db->select_query($sql, [date("md")], "fetch");
    }
}
