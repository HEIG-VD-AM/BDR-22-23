<?php namespace models;

use util\PGSQLConnection;

/**
 * Classe concernant les étapes
 */
class Step {
    private string $description;
    private int $points;
    private int $difficulty;
    private string $flag;
    private int $challengeId;
    private string $name;

    /**
     * Constructeur de la classe Step
     */
    public function __construct(string $description, int $points, int $difficulty, string $flag, int $challengeId, string $name)
    {
        $this->description = $description;
        $this->points = $points;
        $this->difficulty = $difficulty;
        $this->flag = $flag;
        $this->challengeId = $challengeId;
        $this->name = $name;
    }

    /**
     * Enregistre une étape dans la base de données
     * @return void
     */
    public function save() {
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                etape (descriptionetape, nbpoints, difficulte, flag, nom, jeo_challengeid)
                VALUES (:description, :points, :difficulty, :flag, :name, :id) ON CONFLICT DO NOTHING");
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":points", $this->points);
        $stmt->bindParam(":difficulty", $this->difficulty);
        $stmt->bindParam(":flag", $this->flag);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":id", $this->challengeId);
        $stmt->execute();
    }

    public function getName() : string {
        return $this->name;
    }

    public function getDescription() : string {
        return $this->description;
    }

    public function getPoints() : int {
        return $this->points;
    }

    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    public function getChallengeId() : int {
        return $this->challengeId;
    }

    /**
     * Vérifie si une étape a été complétée par une équipe
     * @return true si l'étape a été complétée, false sinon
     */
    public function isCompleted(string $teamName) : bool  {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(1) FROM equipe_etape EE 
            INNER JOIN etape E ON EE.eta_nom = E.nom
            WHERE EE.eta_nom = :name AND EE.equ_nom = :team");
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":team", $teamName);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Vérifie si une étape a été complétée par une équipe de manière statique
     * @return true si l'étape a été complétée, false sinon
     */
    public static function isStepCompleted(string $stepName, string $teamName) : bool  {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(1) FROM equipe_etape EE 
            INNER JOIN etape E ON EE.eta_nom = E.nom
            WHERE EE.eta_nom = :name AND EE.equ_nom = :team");
        $stmt->bindParam(":name", $stepName);
        $stmt->bindParam(":team", $teamName);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Vérifie si le flag donné est correct
     * @return true si le flag est correct, false sinon
     */
    public static function checkFlag(string $stepName, string $flag) {
        $stmt = PGSQLConnection::instance()->prepare("SELECT COUNT(1) FROM etape E WHERE E.nom = :name AND E.flag = :flag");
        $stmt->bindParam(":name", $stepName);
        $stmt->bindParam(":flag", $flag);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Valide une étape pour une équipe
     * @return void
     */
    public static function validateFlag(string $stepName, int $challengeId, string $teamName) {
        $stmt = PGSQLConnection::instance()->prepare("INSERT INTO equipe_etape (equ_nom, eta_jeo_challengeid, eta_nom) VALUES (:equipe, :id, :etape)");
        $stmt->bindParam(":equipe", $teamName);
        $stmt->bindParam(":id", $challengeId);
        $stmt->bindParam(":etape", $stepName);
        $stmt->execute();
    }
}