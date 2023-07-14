<?php namespace models;
use util\PGSQLConnection;

/**
 * Classe concernant les serveurs
 */
class Server {
    private int $id;
    private string $localAddress;
    private int $roomNumber;
    private string $floor;

    private string $maintainer;

    /**
     * Constructeur de la classe Server
     */
    public function __construct($id, $localAddress, $roomNumber, $floor, $maintainer) {
        $this->id = $id;
        $this->localAddress = $localAddress;
        $this->roomNumber = $roomNumber;
        $this->floor = $floor;
        $this->maintainer = $maintainer;
    }

    /**
     * Enregistre un serveur dans la base de données
     * @return void
     */
    public function save() : void
    {
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
            serveur (adresselocale, numerosalle, etage, emailmainteneur)
            VALUES (:localAddress, :roomNumber, :floor, :maintainer)");
        $stmt->bindParam(":localAddress", $this->localAddress);
        $stmt->bindParam(":roomNumber", $this->roomNumber);
        $stmt->bindParam(":floor", $this->floor);
        $stmt->bindParam(":maintainer", $this->maintainer);
        $stmt->execute();
    }

    /**
     * Récupères tous les serveurs de la base de données
     * @return un tableau de tous les serveurs
     */
    public static function getServers(): array
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM serveur");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $servers = array();
        if (!empty($result)) {
            foreach ($result as $row) {
                $servers[] = new Server($row['id'], $row['adresselocale'], $row['numerosalle'], $row['etage'], $row['emailmainteneur']);
            }
        }
        return $servers;
    }

    public function getLocalAddress(): string {
        return $this->localAddress;
    }

    public function getLocation(): string {
        return $this->floor . $this->roomNumber;
    }

    public function getId() : int {
        return $this->id;
    }

}