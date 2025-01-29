<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class NewsSourceModel extends DatabaseQuerier
{
    protected string $tableName = "newssource";
    protected string $primaryKey = "id";
    protected ?string $primaryKeyType = "autoincrement";
}
