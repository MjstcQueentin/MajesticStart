<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class TopicModel extends DatabaseQuerier
{
    protected string $tableName = "topic";
    protected string $primaryKey = "id";
}
