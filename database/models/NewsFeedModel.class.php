<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class NewsFeedModel extends DatabaseQuerier
{
    protected string $tableName = "newsfeed";
    protected string $primaryKey = "uuid";
    protected ?string $primaryKeyType = "uniqid";

    public function select_in_category(string $newscategory_uuid)
    {
        return $this->db->select_query(
            "SELECT `newsfeed`.* 
            FROM `newsfeed` 
            INNER JOIN `newscategory_has_newsfeed` ON `newscategory_has_newsfeed`.`newsfeed_uuid` = `newsfeed`.`uuid` 
            WHERE `newscategory_uuid` = ?",
            [$newscategory_uuid]
        );
    }
}
