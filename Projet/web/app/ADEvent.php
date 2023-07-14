<?php
require_once "session.php";

use models\AttackDefense;
use models\Event;
use models\User;
use util\PGSQLConnection;

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: displayEvent.php");
    exit;
}

/**
 * Permet de récupérer un event selon son id
 */
$stmt = PGSQLConnection::instance()->prepare("SELECT * FROM Evenement WHERE id = :id");
$stmt->bindParam(":id", $_GET["id"]);
$stmt->execute();
$result = $stmt->fetch();

if (empty($result) || $result["estjeopardy"]) {
    header("Location: index.php");
    exit;
}
$event = new Event(
        $result["id"],
        $result["nom"],
        $result["estenligne"],
        new DateTime($result["datecreation"]),
        new DateTime($result["datefin"]),
        $result["estjeopardy"],
        User::getUserFromName($result["crt_pseudo"]));
$challenges = $event->getChallenges();
$team = $_SESSION['user']->loadTeam();

if (isset($_POST['flag']) && $team != null) {

    $flag = $_POST['flag'];
    $id = intval($_POST['challengeid']);

    if (!AttackDefense::isChallengeCompleted($id, $team->getName()) && AttackDefense::checkFlag($id, $flag)) {
        AttackDefense::validateFlag($id, $team->getName());
    }
}

$rooms = $event->getRooms();

?>
<!-- Cette page représente un événement attaque défense. On y retrouve tous ses challenge, que l'on peut valider. -->
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
        <h1 class="mb-3"><?php echo $event->getName() ?>'s challenges</h1>

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
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th style="color: white; background-color: #32334a" scope="col">#</th>
                    <th style="color: white; background-color: #32334a" scope="col">Name</th>
                    <th style="color: white; background-color: #32334a" scope="col">Server's address</th>
                    <?php if ($team != null && $team->participationToEvent($event->getId())) { ?>
                        <th style="color: white; background-color: #32334a" scope="col">Validation</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($challenges as $challenge) { ?>
                <tr>
                    <th scope="row"><?php echo $challenge->getChallengeId() ?></th>
                    <th scope="row"><?php echo $challenge->getName() ?></th>
                    <th scope="row"><?php echo $challenge->getServerFromId()->getLocalAddress() ?></th>

                    <!-- Condition qui vérifie si le challenge a déjà été validé par l'équipe -->
                    <?php
                    if($team != null && $team->participationToEvent($event->getId()) && !$challenge->isCompleted($team->getName())) { ?>
                    <th scope="row">
                        <button data-toggle="modal" data-target="#validateX" data-whatever="@mdo" type="button" class="btn btn-success">Validate</button>

                        <div class="modal fade" id="validateX" tabindex="-1" role="dialog" aria-labelledby="validateXLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="validateXLabel">Validation</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="post">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name" class="col-form-label">Flag</label>
                                            <input type="text" class="form-control" name="flag" id="flag" placeholder="SecretFlag">
                                            <input type="hidden" name="challengeid" value="<?php echo $challenge->getChallengeId() ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Validate</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </th>
                        <?php
                    } else if($team != null && $team->participationToEvent($event->getId())) {
                        ?>
                        <th>
                        Validated
                        </th>
                        <?php
                    }
                    ?>
                </tr scope="row">
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

