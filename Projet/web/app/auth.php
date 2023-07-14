<?php

include_once "autoload.php";

use util\PGSQLConnection;
use models\User;

/**
 * Permet d'autentifier un utilisateur
 * @return true si l'utilisateur est authentifié, false sinon
 */
function authenticate(string $username, string $password) {
    $stmt = PGSQLConnection::instance()->prepare("SELECT * FROM utilisateur WHERE pseudo = :username AND motdepasse = :password");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $password);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if (!empty($result)) {
        $_SESSION['loggedin'] = true;
        $user = new User(
            $result[0]['pseudo'],
            $result[0]['adresseemail'],
            $result[0]['descriptionutilisateur'],
            $result[0]['siteinternet'],
            $result[0]['estadministrateur'],
            $result[0]['motdepasse']);
        if($user->loadTeam() != null) {
            $user->setTeam($user->loadTeam());
        }
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

session_start();

$username = $_POST['username'] ?? "";
$password = $_POST['password'] ?? "";


if (authenticate($username,$password)) {
    header("Location: /index.php");
} else {
    header("Location: /login.php?failedMessage=true");
}
