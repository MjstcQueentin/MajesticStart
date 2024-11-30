<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class PlannedEventModel extends DatabaseQuerier
{
    protected string $tableName = "planned_event";
    protected string $primaryKey = "id";
    protected ?string $primaryKeyType = "autoincrement";
}
