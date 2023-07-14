<?php namespace models;

use util\PGSQLConnection;

/**
 * Classe concernant les défis d'attaques-défenses
 */
class AttackDefense extends Challenge {
    private string $flag;
    private int $serverId;

    public function __construct(int $challengeId, string $flag, int $serverId, string $name, int $eveId)
    {
        parent::__construct($challengeId, $name, $eveId);
        $this->challengeId = $challengeId;
        $this->flag = $flag;
        $this->serverId = $serverId;
    }

    /**
     * Enregistre un challenge d'attaques-défenses dans la base de données
     */
    public function save() {
        parent::save();
        // ici on est sûr d'avoir le challengeid depuis la classe parente
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                Challenge_Attaque_Defense (challengeid, flag, serveurid)
                VALUES (:challengeid, :flag, :serverId) ON CONFLICT DO NOTHING") ;
        $stmt->bindParam(":challengeid", $this->challengeId);
        $stmt->bindParam(":flag", $this->flag);
        $stmt->bindParam(":serverId", $this->serverId);
        $stmt->execute();
    }

    public function getChallengeId() : int {
        return $this->challengeId;
    }

    public function getId() : int {
        return $this->serverId;
    }

    /**
     * Récupère un objet Serveur à partir de l'id d'un challenge d'attaques-défenses
     * @return Objet Serveur
     */
    public function getServerFromId() : ?Server {
    $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM serveur WHERE id = :serverId");
        $stmt->bindParam(":serverId", $this->serverId);
        $stmt->execute();
        $result = $stmt->fetch();
        if (!empty($result)) {
            return new Server(
                $result['id'],
                $result['adresselocale'],
                $result['numerosalle'],
                $result['etage'],
                $result['emailmainteneur']);
        }
        return null;
    }

    /**
     * Vérifie si un challenge d'attaques-défenses est complété par une équipe
     * @return true si le challenge est complété, false sinon
     */
    public function isCompleted(string $teamName) : bool {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(1) FROM equipe_challengeattaquedefense EC 
            INNER JOIN challenge_attaque_defense CAD ON CAD.challengeid = EC.attdef_challengeid
            WHERE EC.attdef_challengeid = :id AND EC.equ_nom = :team");
        $stmt->bindParam(":id", $this->challengeId);
        $stmt->bindParam(":team", $teamName);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Vérifie si un challenge d'attaque-défense est complété par une équipe de manière statique
     * @return true si le challenge est complété, false sinon
     */
    public static function isChallengeCompleted(int $challengeId, string $teamName) : bool  {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(1) FROM equipe_challengeattaquedefense EC 
            INNER JOIN challenge_attaque_defense CAD ON CAD.challengeid = EC.attdef_challengeid
            WHERE EC.attdef_challengeid = :id AND EC.equ_nom = :team");
        $stmt->bindParam(":id", $challengeId);
        $stmt->bindParam(":team", $teamName);
        $stmt->execute();

        return $stmt->fetchColumn() != 0;
    }

    /**
     * Vérifie si un flag donné correspond bien à celui d'un challenge d'attaques-défenses spécifique
     * @return true si le flag est correct, false sinon
     */
    public static function checkFlag(int $challengeId, string $flag) {
        $stmt = PGSQLConnection::instance()->prepare("SELECT COUNT(1) FROM challenge_attaque_defense CAD
                WHERE CAD.challengeid = :id AND CAD.flag = :flag");
        $stmt->bindParam(":id", $challengeId);
        $stmt->bindParam(":flag", $flag);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Valide un challenge d'attaques-défenses pour une équipe
     * @return void
     */
    public static function validateFlag(int $challengeId, string $teamName) {
        $stmt = PGSQLConnection::instance()->prepare("INSERT INTO equipe_challengeattaquedefense (equ_nom, attdef_challengeid) VALUES (:team, :id)");
        $stmt->bindParam(":team", $teamName);
        $stmt->bindParam(":id", $challengeId);
        $stmt->execute();
    }
}