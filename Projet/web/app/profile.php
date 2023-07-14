<?php

use models\User;
use models\Writeup;

require_once 'autoload.php';
require_once "session.php";

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: /index.php");
}

if (isset($_GET['user'])) {
    $user = User::getUserFromName($_GET['user']);
    if ($user == null) {
        header("Location: /index.php");
    }
} else {
    header("Location: /index.php");
}
$user->setTeam($user->loadTeam());

$writeups = ($_GET['user'] == $_SESSION['user']->getUsername())
    ? Writeup::getWriteupsFromUser($user->getUsername())
    : Writeup::getWriteupsFromUsernameForFinishedChallenge($user->getUsername());

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My profile</title>
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
                                <h4><?php echo $user->getUsername() ?></h4>
                                <?php if ($user->getTeam() != null) { ?>
                                    <a href="team.php?name=<?php echo $user->getTeam()->getName() ?>"
                                       class="text-secondary mb-1"><?php echo $user->getTeam()->getName() ?></a>
                                <?php } ?>
                                <?php if ($user->getUsername() == $_SESSION['user']->getUsername()) { ?>
                                    <p class="text-muted font-size-sm"><?php echo $user->getEmail() ?></p>
                                    <button onclick="window.location.href='editProfile.php'" type="button"
                                            class="btn btn-primary">Edit profile
                                    </button>
                                    <button onclick="window.location.href='editPassword.php'" type="button"
                                            class="btn btn-primary">Edit password
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-globe mr-2 icon-inline">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                                Website
                            </h6>
                            <a href="<?php echo $user->getWebsite() ?>"><span
                                        class="text-secondary"><?php echo $user->getWebsite() ?></span></a>
                        </li>
                    </ul>
                </div>
                <div class="card mt-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                     class="bi bi-card-text mr-2 icon-inline" viewBox="0 0 16 16">
                                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"></path>
                                    <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"></path>
                                </svg>
                                Description
                            </h6>
                            <p class="mb-0"><?php echo $user->getDescription() ?></p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0 align-content-center align-items-center">WRITEUPS</h6>
                            </div>
                        </div>
                        <hr>
                        <?php foreach ($writeups as $writeup) { ?>
                            <div class="row">
                                <div class="col-sm-3">
                                    <a href="writeup.php?<?php echo "id=" . $writeup->getId() . "&user_id=" . $writeup->getAuthor()->getUsername() ?>"
                                       class="text-secondary"><h6
                                                class="mb-0"><?php echo $writeup->getTitle() ?></h6></a>
                                </div>
                                <div class="col-sm-9">
                                    <?php
                                    $string = $writeup->getRawContent();
                                    $string = strip_tags($string);
                                    if (strlen($string) > 70) {
                                        $stringCut = substr($writeup->getRawContent(), 0, 70);
                                        $endPoint = strrpos($stringCut, ' ');
                                        $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                        $string .= '...';
                                    }
                                    echo $string;
                                    ?>
                                </div>
                            </div>
                            <hr>
                        <?php } ?>
                        <?php
                        if (count($writeups) == 0) {
                            echo "No writeups";
                        }
                        ?>
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

