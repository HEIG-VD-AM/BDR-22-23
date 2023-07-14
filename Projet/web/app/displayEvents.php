<?php
require_once 'session.php';
require_once 'autoload.php';

use models\Event;
use models\User;
use models\Room;
use util\PGSQLConnection;

header("Cache-Control: no cache");

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}
/**
 * Permet de créer un nouveau Evenement
 */
if (isset($_POST['action']) && $_SESSION['user']->isAdmin()) {
    $eventName = $_POST['name'];
    $endDate = $_POST['endDate'];
    $is_online = boolval($_POST['is_online']);
    $is_jeopardy = $_POST['is_jeopardy'];
    $newEvent = new Event(0, $eventName, $is_online,
        new DateTime(),
        new DateTime($endDate),
        boolval($is_jeopardy),
        $_SESSION['user']);

    $newEvent->save();
}
/**
 * Permet de récupérer tous les Evenements et utilisateurs d'un événement
 */
$events = [];
$stmt = PGSQLConnection::instance()->prepare("SELECT E.*, CRT.* FROM evenement E 
                  INNER JOIN utilisateur CRT on E.crt_pseudo = CRT.pseudo
                  WHERE datefin > now()");
$stmt->execute();
$result = $stmt->fetchAll();
foreach ($result as $row) {
    $events[] = new Event(
        $row['id'],
        $row['nom'],
        $row['estenligne'],
        new DateTime($row['datecreation']),
        new DateTime($row['datefin']),
        $row['estjeopardy'],
        new User($row['pseudo'],
            $row['adresseemail'],
            $row['descriptionutilisateur'],
            $row['siteinternet'],
            $row['estadministrateur'],
            $row['motdepasse']));
}

$rooms = Room::getRooms();

/**
 * Permet de rajouter une salle à un événement
 */
if (isset($_POST['addroom'])) {
    $eventId = $_POST['event_id'] ?? -1;
    $room = $_POST['room'] ?? -1;
    $event = null;

    foreach ($events as $e) {
        if ($e->getId() == $eventId) {
            $event = $e;
            break;
        }
    }
    if ($room != -1 && $eventId != -1 && $event != null && $event->getCreator()->getUsername() == $_SESSION['user']->getUsername()) {
        $roomNumber = (int)substr($room, 1, strlen($room));
        $roomFloor = substr($room, 0, 1);
        $stmt = PGSQLConnection::instance()->prepare("
        INSERT INTO salle_evenement(nosalle, etage, eve_id)
        VALUES (:number, :floor, :event_id)
        ON CONFLICT DO NOTHING");
        $stmt->bindParam(":number", $roomNumber);
        $stmt->bindParam(":floor", $roomFloor);
        $stmt->bindParam(":event_id", $eventId);
        $stmt->execute();
    }
}

$userTeam = $_SESSION['user']->loadTeam();
function array_any(array $array, callable $fn) {
    foreach ($array as $value) {
        if($fn($value)) {
            return true;
        }
    }
    return false;
}
$hasCreatedEvent = array_any($events, function($event) {
    return $event->getCreator()->getUsername() == $_SESSION['user']->getUsername();
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Events</title>
    <link rel="icon" href=
    "https://capturetheflag.withgoogle.com/img/Flag.png"
          type="image/x-icon">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <link rel="stylesheet" href="css/bootstrap4-neon-glow.min.css">


    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel='stylesheet' href='//cdn.jsdelivr.net/font-hack/2.020/css/hack.min.css'>

</head>
<body>

<?php
include "include/navbar.inc.php";
?>

<div class="container py-5 mb5">
    <h1 class="mb-5">Jeopardy events</h1>

    <div class="row">
        <?php if (isset($_SESSION['user']) && $_SESSION['user']->isAdmin()) { ?>

        <div class="col-md-3">

            <button data-toggle="modal" data-target="#createJeopardy" data-whatever="@mdo" type="button"
                    class="btn btn-success mt-2 btn-block">Create new event
            </button>

            <div class="modal fade" id="createJeopardy" tabindex="-1" role="dialog"
                 aria-labelledby="createJeopardyLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createJeopardyLabel">Create new event</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Events's name</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Super new event">

                                    <label for="endDate" class="col-form-label">End date</label>
                                    <input type="date" name="endDate" class="form-control" id="endDate">
                                    <input type="hidden" name="is_jeopardy" value="1">

                                    <label for="name" class="col-form-label">Is this event online?</label><br>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-primary">
                                            <input type="radio" name="is_online" value="1" id="yes" autocomplete="off" checked="">
                                            Yes
                                        </label>
                                        <label class="btn btn-primary active">
                                            <input type="radio" name="is_online" value="0" id="no" autocomplete="off"> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" name="action" class="btn btn-success">Validate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="col-md">
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">End date</th>
                    <th scope="col">Online</th>
                    <?php
                    if ($hasCreatedEvent || ($userTeam != null && $userTeam->isTeamLeader($_SESSION['user']))) {
                            echo "<th scope=\"col\">Actions</th>";
                        }
                    ?>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($events as $e) {
                    if (!$e->isJeopardy()) continue; ?>
                    <tr>
                        <th scope="row"><?php echo $e->getId() ?></th>
                        <td>
                            <a href="jeoEvent.php?id=<?php echo $e->getId() ?>">
                                <?php echo $e->getName() ?>
                            </a>
                        </td>
                        <th scope="row"><?php echo $e->getEndDate()->format("Y/m/d") ?></th>
                        <th scope="row"><?php echo $e->getOnline() ? "Yes" : "No" ?></th>
                        <?php
                        if ($hasCreatedEvent || ($e->getCreator()->getUsername() == $_SESSION["user"]->getUsername() || ($userTeam != null && $userTeam->isTeamLeader($_SESSION['user'])))) {
                        ?>
                        <td>
                            <?php if (isset($_SESSION['user'])
                                && $_SESSION['user']->getTeam() != null
                                && $_SESSION['user']->getTeam()->getCreator()->getUsername() == $_SESSION['user']->getUsername()
                                && !$e->teamJoinedEvent($_SESSION['user']->getTeam()->getName())) { ?>

                            <button type="button" class="btn btn-primary" onclick="window.location.href='joinEvent.php?event=<?php echo $e->getId() ?>'">
                                Join event
                            </button>
                            <?php } ?>

                            <?php
                            if (isset($_SESSION['user']) && $_SESSION['user']->getUsername() == $e->getCreator()->getUsername()) { ?>

                                <button type="button" class="btn btn-primary"
                                        onclick="window.location.href='addJeoChallenge.php?id=<?php echo $e->getId() ?>'">Add challenges
                                </button>

                                <?php if (!$e->getOnline()) { ?>

                                    <button data-toggle="modal" data-target="#addRoomToEvent<?php echo $e->getId() ?>" data-whatever="@mdo"
                                            type="button"
                                            class="btn btn-primary">Add rooms
                                    </button>
                                <?php } ?>

                            <?php } ?>
                            <div class="modal fade" id="addRoomToEvent<?php echo $e->getId() ?>" tabindex="-1" role="dialog"
                                 aria-labelledby="addRoomToEvent<?php echo $e->getId() ?>Label" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addRoomToEvent<?php echo $e->getId() ?>Label">Add room</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="name" class="col-form-label">Rooms</label>
                                                    <select name="room" class="custom-select">
                                                        <option selected="">Open this select menu</option>
                                                        <?php foreach ($rooms as $r) { ?>
                                                            <option value="<?php echo $r->getEtage() . $r->getNumero()?>"><?php echo $r->getEtage() . $r->getNumero() ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <input type="hidden" name="event_id" value="<?php echo $e->getId() ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close
                                                </button>
                                                <button type="submit" name="addroom" class="btn btn-success">Validate</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="container py-5 mb5">
    <h1 class="mb-5">AttackDef events</h1>
    <div class="row">
        <?php if (isset($_SESSION['user']) && $_SESSION['user']->isAdmin()) { ?>
        <div class="col-md-3">

            <button data-toggle="modal" data-target="#createAD" data-whatever="@mdo" type="button"
                    class="btn btn-success mt-2 btn-block">Create new event
            </button>

            <div class="modal fade" id="createAD" tabindex="-1" role="dialog" aria-labelledby="createADLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createADLabel">Create new event</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Events's name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Super new event">

                                    <label for="endDate" class="col-form-label">End date</label>
                                    <input type="date" name="endDate" class="form-control" id="endDate">
                                    <input type="hidden" name="is_jeopardy" value="0">
                                    <!-- si oui est coché, redirigé vers la page des salles -->
                                    <label for="name" class="col-form-label">Is this event online ?</label><br>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-primary">
                                            <input type="radio" name="is_online" value="1" id="yes" autocomplete="off" checked="">
                                            Yes
                                        </label>
                                        <label class="btn btn-primary active">
                                            <input type="radio" name="is_online" value="0" id="no" autocomplete="off"> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" name="action" class="btn btn-success">Validate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="col-md">
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">End date</th>
                    <th scope="col">Online</th>
                    <?php
                    if ($hasCreatedEvent || ($userTeam != null && $userTeam->isTeamLeader($_SESSION['user']))) {
                        echo "<th scope=\"col\">Actions</th>";
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $e) {
                    if ($e->isJeopardy()) continue; ?>
                    <tr>
                        <th scope="row"><?php echo $e->getId() ?></th>
                        <td>
                            <a href="ADEvent.php?id=<?php echo $e->getId() ?>">
                                <?php echo $e->getName() ?>
                            </a>
                        </td>
                        <th scope="row"><?php echo $e->getEndDate()->format("Y/m/d"); ?></th>
                        <th scope="row"><?php echo $e->getOnline() ? "Yes" : "No"; ?></th>
                        <?php if ($hasCreatedEvent || ($userTeam != null && $userTeam->isTeamLeader($_SESSION['user']))) { ?>
                        <td>
                            <?php if (isset($_SESSION['user'])
                                && $_SESSION['user']->getTeam() != null
                                && $_SESSION['user']->getTeam()->getCreator()->getUsername() == $_SESSION['user']->getUsername()
                                && !$e->teamJoinedEvent($_SESSION['user']->getTeam()->getName())) { ?>
                                <!-- Display only if you are a team leader -->
                                <button type="button" class="btn btn-primary" onclick="window.location.href='joinEvent.php?event=<?php echo $e->getId() ?>'">
                                    Join event
                                </button>
                            <?php } ?>

                            <?php
                            if (isset($_SESSION['user']) && $_SESSION['user']->getUsername() == $e->getCreator()->getUsername()) { ?>
                                <!-- display only if you are the event's creator -->
                                <button type="button" class="btn btn-primary"
                                        onclick="window.location.href='addADChallenge.php?id=<?php echo $e->getId() ?>'">
                                    Add challenges
                                </button>

                                <?php if (!$e->getOnline()) { ?>
                                    <!-- display only if you are the event's creator + IRL event -->
                                    <button data-toggle="modal" data-target="#addRoomToEvent<?php echo $e->getId() ?>" data-whatever="@mdo"
                                            type="button"
                                            class="btn btn-primary">Add rooms
                                    </button>
                                <?php } ?>

                            <?php } ?>

                            <div class="modal fade" id="addRoomToEvent<?php echo $e->getId() ?>" tabindex="-1" role="dialog"
                                 aria-labelledby="addRoomToEvent<?php echo $e->getId() ?>Label" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addRoomToEvent<?php echo $e->getId() ?>Label">Add room</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="name" class="col-form-label">Rooms</label>
                                                    <select name="room" class="custom-select">
                                                        <option selected="">Open this select menu</option>
                                                        <?php foreach ($rooms as $r) {
                                                            $name = $r->getEtage() . $r->getNumero();
                                                            echo "<option value=\"$name\">$name</option>";
                                                        } ?>
                                                    </select>
                                                    <input type="hidden" name="event_id" value="<?php echo $e->getId() ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close
                                                </button>
                                                <button type="submit" name="addroom" class="btn btn-success">Validate</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>
    </div>

</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>

</body>
</html>
