<?php namespace models;

use util\PGSQLConnection;
use PDO;

/**
 * Classe concernant les utilisateurs
 */
class User
{
    private string $username;
    private string $email;
    private string $description;
    private string $website;
    private bool $isAdmin;
    private string $password;
    private ?Team $team = null;
    private $writeups;

    /**
     * Constructeur de la classe User
     */
    public function __construct($pseudo, $email, $description, $website, $isAdmin, $password)
    {
        $this->username = $pseudo;
        $this->email = $email;
        $this->description = $description;
        $this->website = $website;
        $this->isAdmin = $isAdmin;
        $this->password = $password;
    }

    /**
     * Enregistre un utilisateur dans la base de données
     * @return void
     */
    public function save() : void
    {
        $stmt = PGSQLConnection::instance()->prepare("
            INSERT INTO 
                utilisateur (pseudo, adresseemail, descriptionutilisateur, siteinternet, estadministrateur, motdepasse)
                VALUES (:username, :email, :description, :website, :isAdmin, :password)
                ON CONFLICT (pseudo) DO UPDATE
                SET adresseemail = :email, descriptionutilisateur = :description, siteinternet = :website, estadministrateur = :isAdmin, motdepasse = :password");
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":website", $this->website);
        $stmt->bindParam(":isAdmin", $this->isAdmin, PDO::PARAM_BOOL);
        $stmt->bindParam(":password", $this->password);
        $stmt->execute();
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function setAdmin(bool $isAdmin) : void
    {
        $this->isAdmin = $isAdmin;
        $stmt = PGSQLConnection::instance()->prepare("
            UPDATE utilisateur
            SET estadministrateur = :isAdmin
            WHERE pseudo = :username");
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":isAdmin", $this->isAdmin, PDO::PARAM_BOOL);
        $stmt->execute();
    }

    public function isAdmin() : bool
    {
        return $this->isAdmin;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getWebsite() : string
    {
        return $this->website;
    }

    public function setEmail(string $email) : void
    {
        $this->email = $email;
    }

    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }

    public function setWebsite(string $website) : void
    {
        $this->website = $website;
    }

    public function getTeam() : ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team) : void
    {
        $this->team = $team;
    }

    /**
     * Vérifie si un utilisateur existe déjà dans la base de données
     * @return true si l'utilisateur existe, false sinon
     */
    public static function exists(string $username, string $mail) : bool
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT COUNT(*) FROM utilisateur WHERE pseudo = :username OR adresseemail = :mail");
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":mail", $mail);
        $stmt->execute();
        return $stmt->fetchColumn() != 0;
    }

    /**
     * Récupère un utilisateur à partir de son pseudo
     * @return l'utilisateur si il existe, null sinon
     */
    public static function getUserFromName(string $username) : ?User
    {
        $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM utilisateur WHERE pseudo = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (!empty($result)) {
            return new User(
                $result[0]['pseudo'],
                $result[0]['adresseemail'],
                $result[0]['descriptionutilisateur'],
                $result[0]['siteinternet'],
                $result[0]['estadministrateur'],
                $result[0]['motdepasse']);
        }
        return null;
    }

    /**
     * Efface un utilisateur de la base de données
     * @return void
     */
    public static function deleteUser(string $username) {
        $stmt = PGSQLConnection::instance()->prepare("DELETE FROM utilisateur WHERE pseudo = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
    }

    /**
     * Récupère l'équipe d'un utilisateur
     * @return l'équipe de l'utilisateur si il en a une, null sinon
     */
    public function loadTeam() : ?Team
    {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT E.* FROM equipe E
            LEFT OUTER JOIN membre_equipe ME on E.nom = ME.equ_nom
            WHERE ME.uti_pseudo = :username 
            OR E.crt_pseudo = :username");
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if (!empty($result)) {
            return new Team(
                $result[0]['nom'],
                $result[0]['motdepasse'],
                $result[0]['typeequipe'],
                User::getUserFromName($result[0]['crt_pseudo']));
        }
        return null;
    }

    public function __toString()
    {
        return "User: " . $this->username . " " . $this->email . " " . $this->description . " " . $this->website . " " . $this->isAdmin;
    }
}
