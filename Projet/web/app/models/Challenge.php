<?php namespace models;

use util\PGSQLConnection;

/**
 * Classe concernant les challenges en général
 */
class Challenge
{
    protected int $challengeId;
    private string $name;
    private int $eveId;

    /**
     * Constructeur de la classe Challenge
     */
    public function __construct(int $challengeId, string $name, int $eve_id)
    {
        $this->challengeId = $challengeId;
        $this->name = $name;
        $this->eveId = $eve_id;
    }

    /**
     * Enregistre un challenge dans la base de données
     * @return void
     */
    public function save()
    {
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                Challenge (nom, eve_id)
                VALUES (:name, :eve_id)");
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":eve_id", $this->eveId);
        $stmt->execute();

        $stmt = PGSQLConnection::instance()->prepare("SELECT challengeId FROM Challenge WHERE nom = :name");
        $stmt->bindParam(":name", $this->name);
        $stmt->execute();
        $this->challengeId = $stmt->fetch()['challengeid'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getChallengeId(): int
    {
        return $this->challengeId;
    }

    public function getEveId(): int
    {
        return $this->eveId;
    }

    /**
     * Vérifie si un challenge existe déjà dans la base de données
     * @return true si le challenge existe déjà, false sinon
     */
    public static function exists(int $id): bool
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT COUNT(1) FROM challenge C WHERE c.challengeid = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }
}