<?php namespace models;

use DateTime;
use util\PGSQLConnection;

/**
 * Classe concernant les équipes
 */
class Team {
    private string $name;
    private string $password;
    private string $type;
    private User $creator;
    private $members = [];
    private $events = [];
    private $membersToRemove = [];

    /**
     * Constructeur de la classe Team
     */
    public function __construct(string $name, string $password, string $type, User $creator)
    {
        $this->name = $name;
        $this->password = $password;
        $this->type = $type;
        $this->creator = $creator;

        // add events participating
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT * FROM evenement E
                  INNER JOIN evenement_equipe EE ON E.id = EE.eve_id
            WHERE EE.nom = :name");

        $stmt->bindParam(":name", $this->name);
        $stmt->execute();
        $events = $stmt->fetchAll();
        foreach($events as $event) {
            $this->events[] = new Event(
                $event['id'],
                $event['nom'],
                $event['estenligne'],
                new DateTime($event['datecreation']),
                new DateTime($event['datefin']),
                $event['estjeopardy'],
                User::getUserFromName($event['crt_pseudo']));
        }
    }

    /**
     * Enregistre une équipe dans la base de données
     * @return void
     */
    public function save() : void {

        // update team properties in database
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                equipe (nom, motdepasse, typeequipe, crt_pseudo)
                VALUES (:name, :password, :type, :creatorname)
                ON CONFLICT (nom) DO UPDATE
                SET motdepasse = :password, typeequipe = :type, crt_pseudo = :creatorname");

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":type", $this->type);
        $tempUsername = $this->creator->getUsername();
        $stmt->bindParam(":creatorname", $tempUsername);
        $stmt->execute();

        // update new and deleted members in database
        if(!empty($this->members)) {
            $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO
                membre_equipe (uti_pseudo, equ_nom)
                VALUES (:username, :teamname)
                ON CONFLICT (uti_pseudo, equ_nom) DO NOTHING");

            foreach ($this->members as $member) {
                $tempUsername = $member->getUsername();
                $stmt->bindParam(":username", $tempUsername);
                $stmt->bindParam(":teamname", $this->name);
                $stmt->execute();
            }
        }

        if(!empty($this->membersToRemove)) {
            $stmt = PGSQLConnection::instance()->prepare("
                DELETE FROM membre_equipe
                WHERE uti_pseudo = :username AND equ_nom = :teamname");

            foreach ($this->membersToRemove as $member) {
                $tempUsername = $member->getUsername();
                $stmt->bindParam(":username", $tempUsername);
                $stmt->bindParam(":teamname", $this->name);
                $stmt->execute();
            }
        }
    }

    public function getName() : string {
        return $this->name;
    }

    public function getPassword() : string {
        return $this->password;
    }

    public function getType() : string {
        return $this->type;
    }

    public function getCreator() : User {
        return $this->creator;
    }

    public function getMembers() {
        return $this->members;
    }

    /**
     * Ajoute un membre à l'équipe
     * @return void
     */
    public function addMember(User $member) : void {
        if (!in_array($member, $this->members)) {
            $this->members[] = $member;
        }

        $this->save();
    }

    /**
     * Supprime un membre de l'équipe
     * @return void
     */
    public function removeMember(User $member) : void {

        foreach($this->members as $m) {
            if ($m->getUsername() == $member->getUsername()) {
                $this->membersToRemove[] = $member;

                $this->members = array_udiff($this->members, [$member], function($a, $b) {
                    return strcmp($a->getUsername(), $b->getUsername());
                });
            }
        }

        $this->save();
    }

    /**
     * Rajoute un événement à l'équipe (participation)
     * @return void
     */
    public function addEvent(Event $event) : void {
        $this->events[] = $event;
    }

    /**
     * Récupère une équipe à partir d'un nom d'utilisateur
     * @return Team
     */
    public static function getTeamFromName(string $name) : ?Team {
        $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM equipe WHERE nom = :name");
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (!empty($result)) {
            $team = new Team(
                $result[0]['nom'],
                $result[0]['motdepasse'],
                $result[0]['typeequipe'],
                User::getUserFromName($result[0]['crt_pseudo']));
            $team->loadTeamMembers();
            return $team;
        }
        return null;
    }

    /**
     * Vérifie si une équipe existe dans la base de données
     * @return true si l'équipe existe, false sinon
     */
    public static function exists(string $name) : bool
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT COUNT(*) FROM Equipe WHERE nom = :name");
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Retourne le nombre de membres d'une équipe
     * @return le nombre de membres
     */
    public function countMembers() : int {
        if(isset($this->members)) {
            return count($this->members);
        } else {
            return 0;
        }
    }

    /**
     * Charge les membres de l'équipe
     * @return void
     */
    public function loadTeamMembers() : void {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT U.pseudo, U.adresseemail, U.descriptionutilisateur, U.siteinternet, U.estadministrateur, U.motdepasse
            FROM membre_equipe ME
                INNER JOIN utilisateur U ON ME.uti_pseudo = U.pseudo
            WHERE ME.equ_nom = :name");
        $stmt->bindParam(":name", $this->name);
        $stmt->execute();
        $members = $stmt->fetchAll();
        foreach ($members as $member) {
            $this->members[] = new User($member['pseudo'], $member['adresseemail'], $member['descriptionutilisateur'], $member['siteinternet'], $member['estadministrateur'], $member['motdepasse']);
        }
    }

    /**
     * Efface l'équipe de la base de données
     * @return void
     */
    public function delete() : void {
        $stmt = PGSQLConnection::instance()->prepare("DELETE FROM equipe WHERE nom = :name");
        $stmt->bindParam(":name", $this->name);
        $stmt->execute();
    }

    /**
     * Vérifie si un utilisateur est leader de l'équipe
     * @return true si l'utilisateur est leader, false sinon
     */
    public function isTeamLeader(User $user) : bool
    {
        return $this->creator->getUsername() === $user->getUsername();
    }

    /**
     * Retourne les événements dont l'équipe participe
     * @return un tableau d'événements
     */
    public function getEventsParticipating() {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT E.*
            FROM evenement E
                INNER JOIN evenement_equipe EE ON E.id = EE.eve_id
            WHERE EE.nom = :teamName");
        $teamName = $this->getName();
        $stmt->bindParam(":teamName", $teamName);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $events = [];
        foreach ($results as $event) {
            $events[] = new Event(
                $event['id'],
                $event['nom'],
                $event['estenligne'],
                new DateTime($event['datecreation']),
                new DateTime($event['datefin']),
                $event['estjeopardy'],
                User::getUserFromName($event['crt_pseudo']));
        }
        return $events;
    }

    /**
     * Vérifie si l'équipe participe à un événement
     * @return true si l'équipe participe, false sinon
     */
    public function participationToEvent(int $eventID) : bool {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT COUNT(*) FROM evenement_equipe
            WHERE nom = :teamName AND eve_id = :eventID");
        $stmt->bindParam(":teamName", $this->name);
        $stmt->bindParam(":eventID", $eventID);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Retourne les types d'équipe
     * @return un tableau de types d'équipe
     */
    public static function getTeamTypes() : array {
        $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM type_equipe ORDER BY nomtype");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $types = array();
        foreach($result as $type) {
            $types[] = $type['nomtype'];
        }
        return $types;
    }

    /**
     * Rajoute un type d'équipe dans la base de données
     * @return void
     */
    public static function addTeamType(string $type) : void {
        $stmt = PGSQLConnection::instance()->prepare("INSERT INTO type_equipe (nomtype) VALUES (:type) ON CONFLICT DO NOTHING");
        $stmt->bindParam(":type", $type);
        $stmt->execute();
    }

    /**
     * Efface un type d'équipe de la base de données
     * @return void
     */
    public static function deleteTeamType(string $type) : void {
        $stmt = PGSQLConnection::instance()->prepare("DELETE FROM type_equipe WHERE nomtype = :type");
        $stmt->bindParam(":type", $type);
        $stmt->execute();
    }


}