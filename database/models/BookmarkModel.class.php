<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class BookmarkModel extends DatabaseQuerier
{
    protected string $tableName = "bookmark";
    protected string $primaryKey = "uuid";
}
