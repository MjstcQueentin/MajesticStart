<?php

namespace MajesticStart\Database\Models;

use MajesticStart\Database\DatabaseQuerier;

final class NewsCategoryModel extends DatabaseQuerier
{
    protected string $tableName = "newscategory";
    protected string $primaryKey = "uuid";
    protected ?string $primaryKeyType = "uniqid";
}
