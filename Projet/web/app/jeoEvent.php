<?php
require_once "session.php";
require_once "util/PGSQLConnection.php";

use models\Event;
use models\User;
use util\PGSQLConnection;

if (!isset($_SESSION['loggedin'])) {
    header("Location: /login.php");
    exit;
}

if(!isset($_GET["id"]))
{
    header("Location: displayEvent.php");
    exit();
}


// permet de récupérer les données de l'événement
$stmt = PGSQLConnection::instance()->prepare("SELECT * FROM Evenement WHERE id = :id");
$stmt->bindParam(":id", $_GET["id"]);
$stmt->execute();
$result = $stmt->fetch();
if(empty($result) || $result["estjeopardy"] == false)
{
    header("Location: index.php");
    exit();
}

$event = new Event(
             $result["id"],
             $result["nom"],
             $result["estenligne"],
             new DateTime($result["datecreation"]),
             new DateTime($result["datefin"]),
             $result["estjeopardy"],
             User::getUserFromName($result["crt_pseudo"])
);

$jeopardyChallenges = $event->getChallenges();

if($event->getCreator()->getUsername() != $_SESSION['user']->getUsername()) {

    $jeopardyChallenges = array_filter($jeopardyChallenges, function($challenge) {
        // on veut uniquement les challenges qui ont deja commencé
        return $challenge->getCreationDate() <= new DateTime();
    });
}

$team = $_SESSION['user']->loadTeam();
$leaderBoard = Event::teamLeaderBoard($event->getId());

$rooms = $event->getRooms();

?>

<!-- Cette page représente un événement JeoPardy -->
<!-- Il faut y afficher tous les challenges de l'événement, ainsi que les points de sur ce challenge pour l'éequipe du joueur connecté.
s'il n'est pas connecté, juste afficher les challenges. En cliquant sur un challenge, on peut voir ses étapes dans la page jeoChallenge.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Challenges</title>
    <link rel = "icon" href =
    "https://capturetheflag.withgoogle.com/img/Flag.png"
          type = "image/x-icon">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <link rel="stylesheet" href="css/bootstrap4-neon-glow.min.css">


    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel='stylesheet' href='//cdn.jsdelivr.net/font-hack/2.020/css/hack.min.css'>

</head>
<body>

<?php
include "include/navbar.inc.php";
?>

<div class="container">
    <div class="main-body">
        <h1 class="mb-3"><?php echo $event->getName(); ?>'s challenges</h1>
        <?php if (!empty($rooms) || !$event->getOnline()) { ?>
        <div class="card mb-3 mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <h6 class="mb-0 align-content-center align-items-center">EVENT INFORMATION</h6>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p>Available rooms: </p>
                        <?php if (!empty($rooms)) { ?>
                        <ul>
                            <?php
                            foreach ($rooms as $room) {
                                echo "<li>" . $room->getEtage() . $room->getNumero() . "</li>";
                            }
                            ?>
                        </ul>
                        <?php } else { ?>
                        <p>None right now...</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div>
            <table class="table mb-5">
                <thead class="thead-light">
                <tr>
                    <th style="color: white; background-color: #32334a" scope="col">Name</th>
                    <th style="color: white; background-color: #32334a" scope="col">Type</th>
                    <th style="color: white; background-color: #32334a" scope="col">End date</th>
                    <?php if ($team != null && $team->participationToEvent($event->getId())) { ?>
                    <th style="color: white; background-color: #32334a" scope="col">Points</th>
                    <th style="color: white; background-color: #32334a" scope="col">Status</th>
                    <?php } ?>

                </tr>
                </thead>
                <tbody>
                <?php foreach ($jeopardyChallenges as $challenge) { ?>
                    <tr>
                        <th scope="row"><a href="jeoChallenge.php?id=<?php echo $challenge->getChallengeId(); ?>"><?php echo $challenge->getName(); ?></a><span> </span></spam><a href="editWriteup.php?chall_id=<?php echo $challenge->getChallengeId() ?>" data-toggle="tooltip" title="Edit or publish a new writeup">[+]</a></th>
                        <td><?php echo $challenge->getCategory(); ?></td>
                        <td><?php echo $challenge->getEndDate()->format("Y/m/d"); ?></td>

                        <!-- Condition qui vérifie si le challenge a déjà été validé par l'équipe -->
                        <?php if ($team != null && $team->participationToEvent($event->getId())) { ?>
                        <td><?php echo $challenge->getPointsForATeam($team->getName()); ?></td>

                        <?php
                            if($challenge->hasDoneAllSteps($team->getName())) { echo "<th scope='row'>Done</th>"; }
                            else { echo "<th scope='row'>Not done</th>";}
                        }
                        ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <h1 class="mb-3">Leaderboard</h1>
            <table class="table mb-5">
                <thead class="thead-light">
                <tr>
                    <th style="color: white; background-color: #32334a" scope="col">Rank</th>
                    <th style="color: white; background-color: #32334a" scope="col">Team's name</th>
                    <th style="color: white; background-color: #32334a" scope="col">Points</th>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach ($leaderBoard as $key => $team) { ?>
                    <tr>
                        <th scope="row"><?php echo ($key + 1) ?></th>
                        <th scope="row"><a href="team.php?name=<?php echo $team['equ_nom']; ?>"><?php echo $team['equ_nom'] ?></a></th>
                        <th scope="row"><?php echo $team['sum'] ?></th>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>