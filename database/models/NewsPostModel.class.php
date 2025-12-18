<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class NewsPostModel extends DatabaseQuerier
{
    protected string $tableName = "newspost";
    protected string $primaryKey = "guid";
    protected ?string $primaryKeyType = "uniqid";

    /**
     * Retire de la table tous les posts
     */
    public function delete_all()
    {
        return $this->db->write_query("DELETE FROM `newspost`;");
    }

    /**
     * Sélectionne les posts provenant des fluxs d'une catégorie
     */
    public function select_of_category(string $newscategory_uuid, ?int $limit)
    {
        $sql = "SELECT `newspost`.*, `newssource`.`name` as `newssource_name`, `newssource`.`logo_dark` AS `newssource_logo_dark`, `newssource`.`logo_light` AS `newssource_logo_light`
        FROM `newspost` 
        INNER JOIN `newsfeed` ON `newspost`.`newsfeed_uuid` = `newsfeed`.`uuid`
        INNER JOIN `newssource` ON `newsfeed`.`newssource_id` = `newssource`.`id`
        WHERE `newspost`.`newsfeed_uuid` IN(SELECT `newsfeed_uuid` FROM `newscategory_has_newsfeed` WHERE `newscategory_uuid` = ?)
        ORDER BY `newspost`.`publication_date` DESC";
        if (!empty($limit)) $sql .= " LIMIT $limit";

        return $this->db->select_query($sql, [$newscategory_uuid]);
    }
}
