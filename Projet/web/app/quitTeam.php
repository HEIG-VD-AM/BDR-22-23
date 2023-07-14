<?php

use models\Team;
use models\User;

require_once "session.php";

// Permet de quitter une équipe
if(!isset($_GET['user']) && !isset($_GET['team'])) {

    if (isset($_SESSION['loggedin']) && $_SESSION['user']->getTeam() != null) {

        $team = $_SESSION['user']->getTeam();
        $team->loadTeamMembers();
        if ($team->getCreator()->getUsername() == $_SESSION['user']->getUsername()) {
            if (!$team->countMembers()) {
                $team->delete();
            } else {
                header("Location: displayTeams.php");
                exit;
            }
        } else {
            $team->removeMember($_SESSION['user']);
        }

        $_SESSION['user']->setTeam(null);
        header("Location: displayTeams.php?successMessage=Team left successfully");
    }
    else {
        header("Location: displayTeams.php");
    }

// Permet de retirer un utilisateur d'une équipe
} elseif (isset($_GET['user']) && isset($_GET['team'])) {

    $user = User::getUserFromName($_GET['user']);
    $user->setTeam($user->loadTeam());
    $team = Team::getTeamFromName($_GET['team']);

    if($user->getTeam()->getName() == $team->getName()) {

        if($team->getCreator()->getUsername() == $_SESSION['user']->getUsername()) {

            $team->removeMember($user);
            header("Location: displayTeams.php?successMessage=Member kicked successfully");

        } else {
            header("Location: displayTeams.php");
        }

    } else {
        header("Location: displayTeams.php");
    }


} else {
    header("Location: displayTeams.php");
}
exit;
