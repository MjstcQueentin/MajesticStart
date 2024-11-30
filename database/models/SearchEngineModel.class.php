<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class SearchEngineModel extends DatabaseQuerier
{
    protected string $tableName = "searchengine";
    protected string $primaryKey = "uuid";
}
