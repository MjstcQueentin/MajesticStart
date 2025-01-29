<?php

/**
 * This class allows one to connect to Majestic Start's database.
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 */
final class DatabaseConnection
{
    private PDO $pdo;
    private static DatabaseConnection $instance;

    /**
     * Get the shared instance of DatabaseConnection.
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new DatabaseConnection();
        }

        return self::$instance;
    }

    /**
     * Database connection constructor.
     * Initialises the PDO.
     */
    function __construct()
    {
        $this->pdo = new PDO("mysql:host=" . DATABASE['host'] . ";dbname=" . DATABASE['dbname'], DATABASE["user"], DATABASE["pwd"], [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]);
    }

    /**
     * Effectue une requête de sélection de données, et retourne le résultat sous la forme d'un tableau associatif.
     * @param string $sql Requête à préparer
     * @param array $params Paramètres, rangés dans l'ordre d'apparition des "?", ou avec des clés correspondant aux placeholders de la requête.
     * @param string $fetchFunction "fetch", "fetchAll" ou "fetchColumn"
     * @param int $fetchParam Mode de récupération des résultats, ou index de la colonne pour fetchColumn
     * @return array
     */
    public function select_query(string $sql, array $params = [], string $fetchFunction = "fetchAll", int $fetchParam = PDO::FETCH_ASSOC)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $results = $stmt->$fetchFunction($fetchParam);
        $stmt->closeCursor();
        return $results;
    }

    /**
     * Effectue une requête d'écriture de données, et retourne un booléen indiquant sa réussite ou son échec.
     * @param string $sql Requête à préparer
     * @param array $params Paramètres, rangés dans l'ordre d'apparition des "?", ou avec des clés correspondant aux placeholders de la requête.
     * @return bool
     */
    public function write_query(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        $stmt->closeCursor();
        return $result;
    }

    /**
     * Returns the ID of the last inserted row
     */
    public function insert_id()
    {
        return $this->pdo->lastInsertId();
    }

    public function start_transaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        return $this->pdo->rollBack();
    }
}
