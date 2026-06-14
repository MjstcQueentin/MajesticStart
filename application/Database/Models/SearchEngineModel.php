<?php

namespace MajesticStart\Database\Models;

use MajesticStart\Database\DatabaseQuerier;

final class SearchEngineModel extends DatabaseQuerier
{
    protected string $tableName = "searchengine";
    protected string $primaryKey = "uuid";
    protected ?string $primaryKeyType = "uniqid";
}
