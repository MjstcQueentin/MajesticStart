<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class NewsSourceModel extends DatabaseQuerier
{
    protected string $tableName = "newssource";
    protected string $primaryKey = "uuid";

    public function select_in_category(string $newscategory_uuid)
    {
        return $this->db->select_query(
            "SELECT `newssource`.* 
            FROM `newssource` 
            INNER JOIN `newscategory_has_newssource` ON `newscategory_has_newssource`.`newssource_uuid` = `newssource`.`uuid` 
            WHERE `newscategory_uuid` = ?",
            [$newscategory_uuid]
        );
    }
}
