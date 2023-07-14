<?php

use models\Event;
use models\Statistics;

require_once "session.php";

$leaderBoard = Event::globalLeaderBoard();

/**
 * Permet de transformer le temps dans les stats en un format plus lisible
 */
function process_duration($durationString)
{
    preg_match('/(\d+):(\d+):(\d+)\.(\d+)/', $durationString, $matches);

    $days = 0;
    $hours = $matches[1] ?? 0;
    $minutes = $matches[2] ?? 0;
    $seconds = $matches[3] ?? 0;
    $microseconds = $matches[4] ?? 0;
    $mostSignificantDuration = "";

    if ($days > 0) {
        $mostSignificantDuration .= "$days days ";
    }elseif ($hours > 0) {
        $mostSignificantDuration .= "$hours hours ";
    } elseif ($minutes > 0) {
        $mostSignificantDuration .= "$minutes minutes ";
    } elseif ($seconds > 0) {
        $mostSignificantDuration .= "$seconds seconds ";
    } else {
        $mostSignificantDuration .= "$microseconds microseconds ";
    }

    return $mostSignificantDuration;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CTF</title>
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

<div class="jumbotron bg-transparent mb-0 radius-0">
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <h1 class="display-2">CTF Projec<span class="vim-caret">t</span></h1>
                <div class="lead mb-3 text-mono text-success">Participate to various events and solve challenges</div>
            </div>
        </div>
        <?php if (!isset($_SESSION['loggedin'])) { ?>
            <div class="btn">
                <a href="login.php" class="btn btn-primary btn-shadow">Login</a>
            </div>
            <div class="btn">
                <a href="accountCreation.php" class="btn btn-primary btn-shadow">Create an account</a>
            </div>
        <?php } ?>

        <h1 class="mb-3 mt-5">Global Leaderboard</h1>
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
                    <th scope="row"><?php echo($key + 1) ?></th>
                    <th scope="row"><a
                                href="team.php?name=<?php echo $team['equ_nom']; ?>"><?php echo $team['equ_nom'] ?></a>
                    </th>
                    <th scope="row"><?php echo $team['sum'] ?></th>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="row mt-3">
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">Most participated events</h6>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Event</th>
                                <th scope="col">Participations</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach (Statistics::get3MostHighlyParticipatedEvents() as $count => $event) { ?>
                                <tr>
                                    <td><?php echo $event['nom'] ?></td>
                                    <td><?php echo $event['nb_participants'] ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Average member participating to events</h6>
                        <p class="h1">
                            <?php echo round(Statistics::getAverageParticipatingMembersToEvents(), 1) ?> members
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Average time to complete a step</h6>
                        <p class="h1">
                            <?php echo process_duration(Statistics::getAverageTimeToCompleteStep());?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Average time to complete a challenge</h6>
                        <p class="h1">
                            <?php echo process_duration(Statistics::getAverageTimeToCompleteChallenge()) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Average points per team</h6>
                        <p class="h1">
                            <?php echo round(Statistics::getAveragePointPerTeamPerEvent(), 1) ?> points
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Average words per writeup</h6>
                        <p class="h1">
                            <?php echo round(Statistics::getAverageWordsPerWriteup(), 1) ?> words
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Participation rate to events per team</h6>
                        <p class="h1">
                            <?php echo round(Statistics::getParticipationRateToEventPerTeam(), 1) ?> %
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Most completed challenge type</h6>
                        <p class="h1">
                            <?php echo Statistics::getMostCompletedChallengeType() ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card h-100">
                    <div class="card-body align-items-center d-flex flex-column justify-content-center">
                        <h6 class="card-title">Fastest challenge type to complete</h6>
                        <p class="h1">
                            <?php echo Statistics::getFastestCompletedType() ?>
                        </p>
                    </div>
                </div>
            </div>
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
