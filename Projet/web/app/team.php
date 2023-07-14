<?php
require_once "autoload.php";
require_once "session.php";

use models\Team;

if(!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['name'])) {

    if (!Team::exists($_GET['name'])) {
        header("Location: displayTeams.php");
        exit();
    }

    $team = Team::getTeamFromName($_GET['name']);
    $eventsParticipating = $team->getEventsParticipating();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My team</title>
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

<div class="container">
    <div class="main-body">

        <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <div class="mt-3">
                                <h4><?php echo $team->getName(); ?></h4>
                                <p class="text-muted font-size-sm"><?php echo $team->getType(); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']->getTeam() != null and $_SESSION['user']->getTeam()->getName() == $_GET['name']) { ?>
                <button data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo" type="button"
                        class="btn btn-danger mt-2 btn-block">Quit team
                </button>
                <?php } ?>
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Quit team</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="post" action="quitTeam.php">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <p>Are you sure you want to leave this team ?</p>
                                        <p>Press "yes" to validate !</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                    <button type="submit" class="btn btn-success" name="submit">Yes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0 align-content-center align-items-center">MEMBERS</h6>
                            </div>
                        </div>
                        <hr>
                        <!-- Team's creator row -->
                        <div class="row align-items-center">
                            <div class="col-sm-9 py-2">
                                <a href="profile.php?user=<?php echo $team->getCreator()->getUsername(); ?>"
                                   class="text-secondary"><h6
                                            class="mb-0"><?php echo $team->getCreator()->getUsername(); ?> <span
                                                class="text-muted">(Owner)</span></h6></a>
                            </div>
                        </div>

                        <!-- Team's members -->
                        <?php foreach ($team->getMembers() as $member) { ?>
                            <hr>
                            <div class="row align-items-center">
                                <div class="col-sm-3">
                                    <a href="profile.php?user=<?php echo $member->getUsername(); ?>"
                                       class="text-secondary"><h6
                                                class="mb-0"><?php echo $member->getUsername(); ?></h6></a>
                                </div>
                                <?php
                                if (isset($_SESSION['loggedin']) && $_SESSION['user']->getUsername() == $team->getCreator()->getUsername()) {
                                    echo "<div class='col-sm-9 text-right'>";
                                    echo "<button onclick=\"window.location.href='quitTeam.php?user=" . $member->getUsername() . "&team=" . $team->getName() . "'\" type='button' class='btn btn-outline-danger'>Kick member</button>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="mb-3">Participations</h1>
        <div class="">
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th style="color: white; background-color: #32334a" scope="col">#</th>
                    <th style="color: white; background-color: #32334a" scope="col">Name</th>
                    <th style="color: white; background-color: #32334a" scope="col">Type</th>
                    <th style="color: white; background-color: #32334a" scope="col">Results</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($eventsParticipating as $e) { ?>

                    <tr>
                        <th scope="row"><?php echo $e->getId() ?></th>
                        <td>
                            <a href="displayEvents.php">
                                <?php echo $e->getName() ?>
                            </a>
                        </td>
                        <th scope="row"><?php echo $e->isJeopardy() ? "Jeopardy" : "Attack defense" ?></th>
                        <th scope="row"><?php
                            if ($e->isJeopardy()) {
                                echo $e->getPointsForATeam($team->getName()) . " points";
                            } else {
                                echo $e->getServerCountForADEventPerTeam($team->getName()) . "/" . $e->getServerCountForADEvent() . " servers";
                            }
                            ?></th>
                        </th>
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

