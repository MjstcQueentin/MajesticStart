<?php

namespace MajesticStart\Database\Models;

use MajesticStart\Database\DatabaseQuerier;

final class NewsSourceModel extends DatabaseQuerier
{
    protected string $tableName = "newssource";
    protected string $primaryKey = "id";
    protected ?string $primaryKeyType = "autoincrement";
}
