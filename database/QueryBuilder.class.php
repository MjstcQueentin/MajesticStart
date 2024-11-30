<?php

final class QueryBuilder
{
    public static function escape_identifier($identifier)
    {
        $identifier = str_ireplace("`", "", $identifier);
        return "`$identifier`";
    }

    public static function escape_keyword($keyword)
    {
        return str_replace(
            ["'", '"', '`', ";"],
            ["", "", "", ""],
            $keyword
        );
    }

    public static function makeWhere(array $where)
    {
        $conditions = [];
        $params = [];
        foreach ($where as $key => $value) {
            if (!isset($value)) {
                $conditions[$key] = " IS NULL";
            } else if (is_array($value)) {
                $conditions[$key] = " IN(" . str_pad("?", count($value) * 2 - 1, ",?") . ")";
                $params = array_merge($params, $value);
            } else {
                $conditions[$key] =  " = ?";
                array_push($params, $value);
            }
        }

        $where = " WHERE " . implode(" AND ", array_map(function ($key) use ($conditions) {
            return QueryBuilder::escape_identifier($key) . $conditions[$key];
        }, array_keys($where)));

        return [
            "where" => $where,
            "params" => $params
        ];
    }

    public static function makeOrderBy(array $orderBy)
    {
        if (empty($orderBy)) return "";

        return " ORDER BY " . implode(", ", array_map(function ($key) use ($orderBy) {
            return self::escape_identifier($key) . " " . self::escape_keyword($orderBy[$key]);
        }, array_keys($orderBy)));
    }
}
