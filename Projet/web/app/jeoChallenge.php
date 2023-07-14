<?php
require_once "session.php";

use models\Jeopardy;
use models\Step;
use util\PGSQLConnection;

if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
/**
 * Permet de créer une nouvelle étape
 */
if (isset($_POST['action']) && $_SESSION['user']->isAdmin()) {
    $stepName = $_POST['name'];
    $stepDescription = $_POST['description'];
    $stepDifficulty = $_POST['difficulty'];

    if(!is_numeric($stepDifficulty)) {
        header("Location: /jeoChallenge.php?id=" . $_GET["id"]);
        exit;
    }

    $stepFlag = $_POST['flag'];
    $newStep = new Step($stepDescription, 0, $stepDifficulty, $stepFlag, $_GET["id"], $stepName);

    $newStep->save();
    header("Location: /jeoChallenge.php?id=" . $_GET["id"]);
}

$id = $_GET["id"];
if ($id == null) {
    header("Location: /index.php");
    exit;
}

if (!Jeopardy::exists(intval($id))) {
    header("Location: /index.php");
    exit;
}
/**
 * Permet de récupérer les étapes d'un challenge
 */
$stmt = PGSQLConnection::instance()->prepare("SELECT * FROM challenge_jeopardy CJ 
         INNER JOIN challenge C on C.challengeid = CJ.challengeid
         WHERE CJ.challengeid = :id");
$stmt->bindParam(":id", $id);
$stmt->execute();
$result = $stmt->fetchAll()[0];
$challenge = new Jeopardy(
        intval($result['challengeid']),
        $result['nom'],
        intval($result['eve_id']),
        $result['descriptionjeopardy'],
        $result['typejeopardy'],
        $result['auteur'],
        new DateTime($result['datecreation']),
        new DateTime($result['datefin']));

$team = $_SESSION['user']->getTeam();

if (isset($_POST['flag']) && $team != null) {

    $flag = $_POST['flag'];
    $stepName = $_POST['step'];

    if (!Step::isStepCompleted($stepName, $team->getName()) && Step::checkFlag($stepName, $flag)) {
        Step::validateFlag($stepName, $challenge->getChallengeId(),  $team->getName());
    }
}

$steps = $challenge->getSteps();
$leaderBoard = Jeopardy::teamLeaderBoard($challenge->getChallengeId());

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Create Challenge</title>
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
        <h1 class="mb-3"><?php echo $challenge->getName() ?>'s steps</h1>
        <p>Author: <?php echo $challenge->getAuthor() ?></p>
        <p>End date: <?php echo $challenge->getEndDate()->format("Y/m/d") ?></p>
        <p>Category: <?php echo $challenge->getCategory() ?></p>
        <p>Description: <?php echo $challenge->getDescription() ?></p>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']->isAdmin()) { ?>
            <button data-toggle="modal" data-target="#createStep" data-whatever="@mdo" type="button"
                    class="btn btn-success mb-3">Create new step
            </button>
        <?php } ?>

        <div class="modal fade" id="createStep" tabindex="-1" role="dialog"
             aria-labelledby="createStepLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createStepLabel">Create new step</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="col-form-label">Step name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Super new step">

                                <label for="name" class="col-form-label">Step description</label>
                                <textarea type="text" class="form-control" name="description" id="description" placeholder="Step description"></textarea>

                                <label for="name" class="col-form-label">Step difficulty</label>
                                <input type="number" min='1' max='10' class="form-control" name="difficulty" id="difficulty" placeholder="Step difficulty">

                                <label for="name" class="col-form-label">Step flag</label>
                                <input type="text" class="form-control" name="flag" id="flag" placeholder="Step flag">
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
        <div>
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th style="color: white; background-color: #32334a" scope="col">Name</th>
                    <th style="color: white; background-color: #32334a" scope="col">Difficulty</th>
                    <th style="color: white; background-color: #32334a" scope="col">Points</th>
                    <?php if($team != null && $team->participationToEvent($challenge->getEveId())) { ?>
                    <th style="color: white; background-color: #32334a" scope="col">Validation</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($steps as $step) { ?>
                <tr>
                    <th scope="row"><a data-target="#descr<?php echo str_replace(' ', '',$step->getName())?>" data-toggle="modal" class="MainNavText" id="MainNavHelp"
                                       href="#descr<?php echo str_replace(' ', '',$step->getName())?>"><?php echo $step->getName() ?></a></th>
                    <div class="modal fade" id="descr<?php echo str_replace(' ', '',$step->getName())?>" tabindex="-1" role="dialog"
                         aria-labelledby="seeDescrLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="seeDescrLabel">Description</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php echo $step->getDescription() ?>
                            </div>
                        </div>
                    </div>
                    <th scope="row"><?php echo $step->getDifficulty()  ?>/10</th>
                    <th scope="row"><?php echo $step->getPoints() ?></th>

                    <?php if ($team != null && $team->participationToEvent($challenge->getEveId())) { ?>
                    <th scope="row">
                        <?php
                        if(!$step->isCompleted($team->getName())) {
                            ?>
                            <button data-toggle="modal" data-target="#validate<?php echo str_replace(' ', '',$step->getName())?>" data-whatever="@mdo" type="button" class="btn btn-success">Validate</button>
                            <div class="modal fade" id="validate<?php echo str_replace(' ', '',$step->getName())?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Validation</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="name" class="col-form-label">Flag</label>
                                                    <input type="text" class="form-control" id="name" name="flag" placeholder="SecretFlag">
                                                    <input type="hidden" class="form-control" id="name" name="step" value="<?php echo $step->getName() ?>">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                <button type="submit" value="flag" class="btn btn-success">Validate</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            Validated
                            <?php
                        }
                        ?>
                    </th>
                    <?php } ?>
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
