<?php

require_once "autoload.php";
require_once "session.php";

use models\Event;
use util\PGSQLConnection;

if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
/**
 * Permet à une équipe de rejoindre un événement
 */
$user = $_SESSION['user'];
$team = $user->getTeam();
$event = $_GET['event'] ?? null;
if ($event == null || !Event::exists($event)) {
    header("Location: /index.php");
    exit;
}

if ($team == null) {
    header("Location: /index.php");
    die("You are not in a team");
    exit;
}

if ($team->getCreator()->getUsername() == $user->getUsername()) {
    $stmt = PGSQLConnection::instance()->prepare("INSERT INTO evenement_equipe (eve_id, nom) VALUES (:event, :team)");
    $teamName = $team->getName();
    $stmt->bindParam(":event", $event);
    $stmt->bindParam(":team", $teamName);
    $stmt->execute();
}

header("Location: /displayEvents.php");