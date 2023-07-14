<?php namespace models;

use DateTime;
use PDO;
use util\PGSQLConnection;

/**
 * Classe concernant les Evenements
 */
class Event
{
    private int $id;

    private string $name;
    private bool $online;
    private DateTime $startDate;
    private DateTime $endDate;
    private bool $isJeopardy;
    private User $creator;

    /**
     * Constructeur de la classe Event
     */
    public function __construct(
        int      $id,
        string   $name,
        bool     $online,
        DateTime $startDate,
        DateTime $endDate,
        bool     $isJeopardy,
        User     $creator)
    {
        $this->id = $id;
        $this->name = $name;
        $this->online = $online;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->isJeopardy = $isJeopardy;
        $this->creator = $creator;
    }

    /**
     * Enregistre un événement dans la base de données
     * @return void
     */
    public function save(): void
    {
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo)
                VALUES (:name, :online, :startDate, :endDate, :isJeopardy, :creator)");

        $startDate = date_format($this->startDate, "Y-m-d H:i:s");
        $endDate = date_format($this->endDate, "Y-m-d H:i:s");
        $username = $this->creator->getUsername();
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":online", $this->online, PDO::PARAM_BOOL);
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $stmt->bindParam(":isJeopardy", $this->isJeopardy, PDO::PARAM_BOOL);
        $stmt->bindParam(":creator", $username);
        $stmt->execute();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOnline(): bool
    {
        return $this->online;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function isJeopardy(): bool
    {
        return $this->isJeopardy;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    /**
     * Prend toutes les salles d'un événement
     * @return un tableau de Room
     */
    public function getRooms(): array
    {
        // get rooms from database
        $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM salle_evenement WHERE eve_id = :id");
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $rooms = array();
        foreach ($result as $room) {
            $rooms[] = new Room($room["nosalle"], $room["etage"]);
        }
        return $rooms;
    }

    /**
     * Prend tous les challenges d'un événement
     * @return un tableau de Challenge
     */
    public function getChallenges(): array
    {
        if ($this->isJeopardy) {
            $stmt = PGSQLConnection::instance()->prepare("SELECT *
                                                          FROM challenge_jeopardy
                                                          INNER JOIN challenge C on challenge_jeopardy.challengeid = C.challengeid
                                                          WHERE C.eve_id = :id");
        } else {
            $stmt = PGSQLConnection::instance()->prepare("SELECT *
                                                          FROM challenge_attaque_defense
                                                          INNER JOIN challenge C on challenge_attaque_defense.challengeid = C.challengeid
                                                          WHERE C.eve_id = :id");
        }
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $challenges = array();

        foreach ($result as $row) {

            if ($this->isJeopardy) {
                $challenges[] = new Jeopardy(
                    $row['challengeid'],
                    $row['nom'],
                    $row['eve_id'],
                    $row['descriptionjeopardy'],
                    $row['typejeopardy'],
                    $row['auteur'],
                    new DateTime($row['datecreation']),
                    new DateTime($row['datefin'])
                );
            } else {
                $challenges[] = new AttackDefense(
                    $row['challengeid'],
                    $row['flag'],
                    $row['serveurid'],
                    $row['nom'],
                    $row['eve_id']
                );
            }
        }

        return $challenges;
    }

    /**
     * Vérifie si un événement existe dans la base de données
     * @return true si l'événement existe, false sinon
     */
    public static function exists(int $id): bool
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT COUNT(1) FROM evenement WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $count = $stmt->fetchAll();
        return $count[0] > 0;
    }

    /**
     * Retourne une liste ordrée des équipes selon leur score
     * @return le tableau des équipes
     */
    public static function teamLeaderBoard(int $id)
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT * FROM points_par_equipe_par_evenement P
            WHERE P.id = :id
            ORDER BY P.sum DESC");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Retourne une liste ordrée des équipes selon leur score de tous les Event
     * @return le tableau des équipes
     */
    public static function globalLeaderBoard()
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT equ_nom, SUM(sum) FROM points_par_equipe_par_evenement P
            GROUP BY P.equ_nom
            ORDER BY SUM(sum) DESC");
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Retourne les points pour une équipe dans un event
     * @return les points de l'équipe
     */
    public function getPointsForATeam(string $teamName): int
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT SUM(sum) FROM points_par_equipe_par_evenement P
            WHERE P.id = :id
            AND P.equ_nom = :teamName");
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":teamName", $teamName);
        $stmt->execute();
        $toReturn = $stmt->fetchColumn();
        return ($toReturn == null ? 0 : $toReturn);
    }

    /**
     * Vérifie si une équipe participe à un événement
     * @return true si l'équipe participe, false sinon
     */
    public function teamJoinedEvent(string $name): bool
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(1) FROM evenement_equipe
            WHERE eve_id = :id AND nom = :name");
        $eventId = $this->id;
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Prend la quantité de serveurs pour un événement
     * @return la quantité de serveurs
     */
    public function getServerCountForADEvent(): int
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(*)
            FROM evenement E
            INNER JOIN challenge C ON E.id = C.eve_id
            INNER JOIN challenge_attaque_defense CAD ON C.challengeid = CAD.challengeid
            WHERE E.id = :id
            GROUP BY E.id");
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $toReturn = $stmt->fetchColumn();
        return ($toReturn == null ? 0 : $toReturn);
    }

    /**
     * Prend la quantité de serveurs trouvés par une équipe dans un événement
     * @return la quantité de serveurs pour une équipe
     */
    public function getServerCountForADEventPerTeam(string $teamName): int
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(*)
            FROM equipe_challengeattaquedefense ECAD
            INNER JOIN challenge_attaque_defense CAD ON ECAD.attdef_challengeid = CAD.challengeid
            INNER JOIN challenge C ON CAD.challengeid = C.challengeid
            INNER JOIN evenement E ON C.eve_id = E.id
            WHERE E.id = :id AND ECAD.equ_nom = :teamName");
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":teamName", $teamName);
        $stmt->execute();
        $toReturn = $stmt->fetchColumn();
        return ($toReturn == null ? 0 : $toReturn);
    }

}