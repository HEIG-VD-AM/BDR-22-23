<?php namespace models;

use util\PGSQLConnection;


/**
 * Classe concernant les writups
 */
class Writeup
{
    private int $id;
    private string $title;
    private string $content;
    private User $author;
    private int $challengeId;

    /**
     * Constructeur de la classe Writeup
     */
    public function __construct(string $title, string $content, User $author, int $challengeId)
    {
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->challengeId = $challengeId;
        $this->id = -1;
    }

    /**
     * Enregistre un writeup dans la base de données
     * @return void
     */
    public function save()
    {
        if ($this->id == -1) {
            $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                writeup (titre, contenu, pseudo, challengeid)
                VALUES (:title, :content, :author, :id)");
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":content", $this->content);
            $username = $this->author->getUsername();
            $stmt->bindParam(":author", $username);
            $stmt->bindParam(":id", $this->challengeId);
            $stmt->execute();
            $this->id = PGSQLConnection::instance()->lastInsertId();
        } else {
            $stmt = PGSQLConnection::instance()->prepare("
            UPDATE writeup SET titre = :title, contenu = :content, pseudo = :author WHERE id = :id");
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":content", $this->content);
            $username = $this->author->getUsername();
            $stmt->bindParam(":author", $username);
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();
        }
    }

    /**
     * Efface un writeup de la base de données
     * @return void
     */
    public static function delete(int $id)
    {
        $stmt = PGSQLConnection::instance()->prepare("
            DELETE FROM writeup WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getChallengeId(): int
    {
        return $this->challengeId;
    }

    public function getRawContent(): string
    {
        return $this->content;
    }

    /**
     * Récupérer le writeup d'un challenge d'un utilisateur
     * @return le writeup s'il existe
     */
    public static function getWriteupFromUsername($challengeId, $username): ?Writeup
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT * FROM writeup WHERE challengeid = :id AND pseudo = :username");
        $stmt->bindParam(":id", $challengeId);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            $writeup = new Writeup($row['titre'], $row['contenu'], User::getUserFromName($row['pseudo']), $row['challengeid']);
            $writeup->setId($row['id']);
            return $writeup;
        }
        return null;
    }

    /**
     * Récupérer le writeup par son id
     * @return le writeup s'il existe
     */
    public static function getById($id, $username): ?Writeup
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT * FROM writeup WHERE id = :id AND pseudo = :username");
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            $writeup = new Writeup($row['titre'], $row['contenu'], User::getUserFromName($row['pseudo']), $row['challengeid']);
            $writeup->setId($row['id']);
            return $writeup;
        }
        return null;
    }

    /**
     * Récupère les writeups d'un utilisateur
     * @return un tableau de writeups
     */
    public static function getWriteupsFromUser($username) {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT * FROM writeup WHERE pseudo = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $writeups = [];
        while ($row = $stmt->fetch()) {
            $writeup = new Writeup($row['titre'], $row['contenu'], User::getUserFromName($row['pseudo']), $row['challengeid']);
            $writeup->setId($row['id']);
            $writeups[] = $writeup;
        }
        return $writeups;
    }

    /**
     * Récupère les writeups des challenges finis d'un utilisateur
     * @return un tableau de writeups
     */
    public static function getWriteupsFromUsernameForFinishedChallenge($username) {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT *
            FROM writeup W
            INNER JOIN challenge_jeopardy CJ on W.challengeid = CJ.challengeid
            WHERE pseudo = :username
            AND CJ.datefin < NOW();");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $writeups = [];
        while ($row = $stmt->fetch()) {
            $writeup = new Writeup($row['titre'], $row['contenu'], User::getUserFromName($row['pseudo']), $row['challengeid']);
            $writeup->setId($row['id']);
            $writeups[] = $writeup;
        }
        return $writeups;
    }

    public function getAuthor() : User {
        return $this->author;
    }

}