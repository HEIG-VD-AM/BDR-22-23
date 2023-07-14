<?php namespace models;

use DateTime;
use util\PGSQLConnection;

/**
 * Classe concernant les Challenges Jeopardy
 */
class Jeopardy extends Challenge
{
    private string $description;
    private string $category;
    private string $author;
    private DateTime $creationDate;
    private DateTime $endDate;

    public function __construct(int $challengeId, string $name, int $eve_id, string $description, string $category, string $author, DateTime $creationDate, DateTime $endDate)
    {
        //parent constructor call
        parent::__construct($challengeId, $name, $eve_id);
        $this->description = $description;
        $this->category = $category;
        $this->author = $author;
        $this->creationDate = $creationDate;
        $this->endDate = $endDate;
    }

    /**
     * Enregistre un challenge Jeopardy dans la base de données
     * @return void
     */
    public function save()
    {
        parent::save();
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                Challenge_Jeopardy (challengeId, descriptionJeopardy, typeJeopardy, auteur, dateCreation, dateFin)
                VALUES (:challengeId, :description, :typeJeopardy, :auteur, :dateCreation, :dateFin) ON CONFLICT DO NOTHING");

        $startDate = date_format($this->creationDate, "Y-m-d H:i:s");
        $endDate = date_format($this->endDate, "Y-m-d H:i:s");
        $stmt->bindParam(":challengeId", $this->challengeId);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":typeJeopardy", $this->category);
        $stmt->bindParam(":auteur", $this->author);
        $stmt->bindParam(":dateCreation", $startDate);
        $stmt->bindParam(":dateFin", $endDate);
        $stmt->execute();
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    /**
     * Vérifie si un challenge Jeopardy existe dans la base de données
     * @return true si le challenge existe, false sinon
     */
    public static function exists(int $id): bool
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT COUNT(1) FROM challenge_jeopardy C WHERE C.challengeid = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Récupères les étapes d'un challenge Jeopardy
     * @return un tableau d'étapes
     */
    public function getSteps()
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT E.* FROM etape E 
            INNER JOIN challenge_jeopardy CJ ON CJ.challengeid = E.jeo_challengeid
            WHERE E.jeo_challengeid = :id
        ");
        $id = parent::getChallengeId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $steps = array();
        foreach ($results as $step) {
            $steps[] = new Step(
                $step["descriptionetape"],
                $step["nbpoints"],
                $step["difficulte"],
                $step["flag"],
                $step["jeo_challengeid"],
                $step["nom"]
            );
        }
        return $steps;
    }

    /**
     * Récupère une liste ordonnée des équipes ayant participé à un challenge Jeopardy
     * @return le tableau des équipes
     */
    public static function teamLeaderBoard(int $id)
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT * FROM points_par_equipe_par_challenge P
            WHERE P.challengeId = :id
            ORDER BY P.sum DESC");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Récupère les poiints d'une équipe pour un challenge Jeopardy
     * @return les points de l'équipe
     */
    public function getPointsForATeam(string $teamName): int
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT sum FROM points_par_equipe_par_challenge WHERE challengeid = :challengeId AND equ_nom = :teamName");
        $challengeId = parent::getChallengeId();
        $stmt->bindParam(":challengeId", $challengeId);
        $stmt->bindParam(":teamName", $teamName);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Vérifie si toues les étapes d'un challenge Jeopardy ont été résolues par une équipe
     * @return true si toutes les étapes ont été résolues, false sinon
     */
    public function hasDoneAllSteps(string $teamName): bool
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(*) FROM equipe_a_fait_jeo_challenge WHERE equ_nom = :teamName AND challengeid = :challengeId");
        $stmt->bindParam(":teamName", $teamName);
        $challengeId = parent::getChallengeId();
        $stmt->bindParam(":challengeId", $challengeId);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Récupère les types de challenge Jeopardy
     * @return un tableau de types de challenge Jeopardy
     */
    public static function getChallengeTypes(): array
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM type_challenge_jeopardy ORDER BY nomtype");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $types = array();
        foreach ($result as $type) {
            $types[] = $type['nomtype'];
        }
        return $types;
    }

    /**
     * Rajoute un type de challenge Jeopardy dans la base de données
     * @return void
     */
    public static function addChallengeType(string $type) : void {
        $stmt = PGSQLConnection::instance()->prepare("INSERT INTO type_challenge_jeopardy (nomtype) VALUES (:type) ON CONFLICT DO NOTHING");
        $stmt->bindParam(":type", $type);
        $stmt->execute();
    }

    /**
     * Supprime un type de challenge Jeopardy dans la base de données
     * @return void
     */
    public static function deleteChallengeType(string $type) : void {
        $stmt = PGSQLConnection::instance()->prepare("DELETE FROM type_challenge_jeopardy WHERE nomtype = :type");
        $stmt->bindParam(":type", $type);
        $stmt->execute();
    }
}