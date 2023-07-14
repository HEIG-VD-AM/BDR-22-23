<?php namespace models;
use util\PGSQLConnection;

/**
 * Classe concernant les salles
 */
class Room {
    private int $numero;
    private string $etage;

    /**
     * Constructeur de la classe Room
     */
    public function __construct($numero, $etage) {
        $this->numero = $numero;
        $this->etage = $etage;
    }

    /**
     * Enregistre une salle dans la base de données
     * @return void
     */
    public function save() : void
    {
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                salle (numerosalle, etage)
                VALUES (:numero, :etage)");
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":etage", $this->etage);
        $stmt->execute();
    }

    /**
     * Récupères toutes les salles de la base de données
     * @return un tableau de toutes les salles
     */
    public static function getRooms(): array
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM salle");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $rooms = array();
        if (!empty($result)) {
            foreach ($result as $row) {
                $rooms[] = new Room($row['numerosalle'], $row['etage']);
            }
        }
        return $rooms;
    }

    public function getNumero() : int {
        return $this->numero;
    }

    public function getEtage() : string {
        return $this->etage;
    }
}