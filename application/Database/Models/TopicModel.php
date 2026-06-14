<?php

namespace MajesticStart\Database\Models;

use MajesticStart\Database\DatabaseQuerier;

final class TopicModel extends DatabaseQuerier
{
    protected string $tableName = "topic";
    protected string $primaryKey = "id";
    protected ?string $primaryKeyType = "autoincrement";
}
