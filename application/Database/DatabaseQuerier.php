<?php

namespace MajesticStart\Database;

use Exception;

/**
 * This class allows one to make queries into one table of Majestic Start's database.
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 */
abstract class DatabaseQuerier
{
    /** @var DatabaseConnection $db Shared instance of the database connection */
    protected DatabaseConnection $db;

    /** @var string $tableName Name of the table to query */
    protected string $tableName;

    /** @var string $primaryKey Primary key of the table */
    protected string $primaryKey;

    /** @var string $primaryKeyType "autoincrement", "uniqid" ou NULL */
    protected ?string $primaryKeyType = "autoincrement";

    private int|string $insertId;

    /**
     * Returns a new instance of a model
     * @template T
     * @param class-string<T> $name Name of the model
     * @return T
     * @throws Exception If the model does not exist
     */
    public static function model(string $name): DatabaseQuerier
    {
        $modelClassPath = __DIR__ . "/Models/{$name}.php";
        if (!is_file($modelClassPath)) {
            throw new Exception("Model {$name} not found");
        }
        require_once $modelClassPath;
        $modelClassName = "MajesticStart\\Database\\Models\\{$name}";
        return new $modelClassName();
    }

    function __construct()
    {
        $this->db = DatabaseConnection::instance();
    }

    public function select_one(string|int $id): array|null
    {
        $sql = "SELECT * FROM :tableName WHERE :primaryKey = ?";
        $sql = str_replace(":tableName", QueryBuilder::escape_identifier($this->tableName), $sql);

        $sql = str_replace(":primaryKey", QueryBuilder::escape_identifier($this->primaryKey), $sql);

        $resultSet = $this->db->select_query($sql, [$id], "fetch");
        if (empty($resultSet)) {
            return null;
        } else {
            return $resultSet;
        }
    }

    /**
     * @param array $where
     * @param array $orderBy
     * @return array
     */
    public function select(array $where, array $orderBy = []): array
    {
        if (empty($where)) {
            return $this->select_all();
        }

        $sql = "SELECT * FROM :tableName";
        $sql = str_replace(":tableName", QueryBuilder::escape_identifier($this->tableName), $sql);

        $where = QueryBuilder::makeWhere($where);
        $params = $where["params"];
        $sql .= $where["where"];

        $sql .= QueryBuilder::makeOrderBy($orderBy);

        $resultSet = $this->db->select_query($sql, $params);
        if (empty($resultSet)) {
            return [];
        } else {
            return $resultSet;
        }
    }

    /**
     * @param array $orderBy
     * @return array
     */
    public function select_all(array $orderBy = []): array
    {
        $sql = "SELECT * FROM :tableName";

        $sql = str_replace(":tableName", QueryBuilder::escape_identifier($this->tableName), $sql);

        $sql .= QueryBuilder::makeOrderBy($orderBy);

        return $this->db->select_query($sql);
    }

    /**
     * @param array $data
     * @param bool $return_id
     * @return int|string|bool
     */
    public function insert_one(array $data, bool $return_id = false): int|string|bool
    {
        if ($this->primaryKeyType == "uniqid") {
            $uniqid = uniqid();
            $data[$this->primaryKey] = $uniqid;
        }

        $sql = "INSERT INTO " . QueryBuilder::escape_identifier($this->tableName);
        $sql .=
            "(" .
            implode(
                ",",
                array_map(function ($key) {
                    return QueryBuilder::escape_identifier($key);
                }, array_keys($data)),
            ) .
            ")";

        $sql .=
            "VALUES(" .
            implode(
                ",",
                array_map(function ($key) {
                    return "?";
                }, array_values($data)),
            ) .
            ")";

        $success = $this->db->write_query($sql, array_values($data));
        if ($success) {
            $this->insertId = !empty($uniqid) ? $uniqid : $this->db->insert_id();
        }
        return $return_id ? $this->insertId : $success;
    }

    public function insert_id(): string
    {
        return $this->insertId;
    }

    /**
     * @param array $dataset
     * @return int
     * @throws Exception
     */
    public function insert(array $dataset): int
    {
        $insert_count = 0;

        foreach ($dataset as $data) {
            if ($this->insert_one($data)) {
                $insert_count++;
            }
        }

        return $insert_count;
    }

    /**
     * @param string|int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update_one(string|int $id, array $data): bool
    {
        $sql = "UPDATE :tableName SET :data WHERE :primaryKey = ?";
        $sql = str_replace(":tableName", QueryBuilder::escape_identifier($this->tableName), $sql);

        $sql = str_replace(
            ":data",
            implode(
                ", ",
                array_map(function ($key) {
                    return QueryBuilder::escape_identifier($key) . " = ?";
                }, array_keys($data)),
            ),
            $sql,
        );

        $sql = str_replace(":primaryKey", QueryBuilder::escape_identifier($this->primaryKey), $sql);

        return $this->db->write_query($sql, array_merge(array_values($data), [$id]));
    }

    /**
     * @param string|int $id
     * @return bool
     * @throws Exception
     */
    public function delete_one(string|int $id): bool
    {
        $sql = "DELETE FROM :tableName WHERE :primaryKey = ?";
        $sql = str_replace(":tableName", QueryBuilder::escape_identifier($this->tableName), $sql);

        $sql = str_replace(":primaryKey", QueryBuilder::escape_identifier($this->primaryKey), $sql);

        return $this->db->write_query($sql, [$id]);
    }

    /**
     * @param array $where
     * @return bool
     * @throws Exception
     */
    public function delete(array $where): bool
    {
        if (empty($where)) {
            throw new Exception("Deleting without conditions is forbidden.");
        }

        $sql = "DELETE FROM :tableName";

        $sql = str_replace(":tableName", QueryBuilder::escape_identifier($this->tableName), $sql);

        $where = QueryBuilder::makeWhere($where);
        $params = $where["params"];
        $sql .= $where["where"];

        return $this->db->write_query($sql, $params);
    }
}
