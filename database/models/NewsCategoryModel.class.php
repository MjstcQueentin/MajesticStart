<?php
require_once(__DIR__ . "/../DatabaseQuerier.class.php");

final class NewsCategoryModel extends DatabaseQuerier
{
    protected string $tableName = "newscategory";
    protected string $primaryKey = "uuid";
}
