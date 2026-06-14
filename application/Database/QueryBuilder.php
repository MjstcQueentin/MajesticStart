<?php

namespace MajesticStart\Database;

/**
 * @internal
 */
final class QueryBuilder
{
    /**
     * @param string $identifier
     * @return string
     */
    public static function escape_identifier(string $identifier): string
    {
        $identifier = str_ireplace("`", "", $identifier);
        return "`$identifier`";
    }

    /**
     * @param string $keyword
     * @return string
     */
    public static function escape_keyword(string $keyword): string
    {
        return str_replace(["'", '"', "`", ";"], ["", "", "", ""], $keyword);
    }

    /**
     * @param array $where
     * @return array
     */
    public static function makeWhere(array $where): array
    {
        $conditions = [];
        $params = [];
        foreach ($where as $key => $value) {
            if (!isset($value)) {
                $conditions[$key] = " IS NULL";
            } elseif (is_array($value)) {
                $conditions[$key] = " IN(" . str_pad("?", count($value) * 2 - 1, ",?") . ")";
                $params = array_merge($params, $value);
            } else {
                $conditions[$key] = " = ?";
                array_push($params, $value);
            }
        }

        $where =
            " WHERE " .
            implode(
                " AND ",
                array_map(function ($key) use ($conditions) {
                    return QueryBuilder::escape_identifier($key) . $conditions[$key];
                }, array_keys($where)),
            );

        return [
            "where" => $where,
            "params" => $params,
        ];
    }

    /**
     * Make order by clause
     * @param array $orderBy Associative array of column name and direction
     * @return string
     */
    public static function makeOrderBy(array $orderBy): string
    {
        if (empty($orderBy)) {
            return "";
        }

        return " ORDER BY " .
            implode(
                ", ",
                array_map(function ($key) use ($orderBy) {
                    return self::escape_identifier($key) . " " . self::escape_keyword($orderBy[$key]);
                }, array_keys($orderBy)),
            );
    }
}
