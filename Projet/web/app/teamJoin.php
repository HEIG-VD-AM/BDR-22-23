<?php

require_once "autoload.php";
require_once "session.php";

use models\Team;
use models\User;
use util\PGSQLConnection;


// Vérification du formulaire de création d'équipe
$submit = isset($_POST['submit']);

if ($submit && isset($_SESSION['loggedin']) && $_SESSION['user']->getTeam() == null && Team::exists($_GET["team"])) {

    $password = $_POST["password"] ?? "";
    $team = Team::getTeamFromName($_GET["team"]);

    if ($team->countMembers() < 3 && $password === $team->getPassword()) {

        $team->addMember($_SESSION['user']);
        $_SESSION['user']->setTeam($team);

        header("Location: displayTeams.php?successMessage=Team joined successfully");

    } else {
        header("Location: displayTeams.php");
    }

} else{
    header("Location: displayTeams.php");
}
exit;