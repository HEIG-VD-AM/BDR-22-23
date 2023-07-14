<?php
require_once "session.php";

use models\Server;
use models\AttackDefense;

if (!isset($_SESSION['loggedin']) || !$_SESSION['user']->isAdmin()) {
    header("Location: /index.php");
    exit;
}

/**
 * Permet d'ajouter un challenge de type attaque défense
 */
if (isset($_POST['submit'])) {
    $eventId = intval($_POST['eventId']) ?? 1;
    $challengeName = $_POST['name'] ?? "";
    $flag = $_POST['flag'] ?? "";
    $server = intval($_POST['server']) ?? 1;
    $challenge = new AttackDefense(0, $flag, $server, $challengeName, $eventId);
    $challenge->save();

    header("Location: /displayEvents.php");
}


$servers = Server::getServers();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Create Challenge</title>
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

    <h1 class="mb-5">Add new attack-defense challenge</h1>

    <div class="row py-4">
        <div class="col-md-8 order-md-1">
            <form class="needs-validation" method="post" action="addADChallenge.php">
                <input type="hidden" name="eventId" value="<?php echo $_GET['id'] ?? 1 ?>"/>
                <div class="mb-3">
                    <label for="nom">Name</label>
                    <input type="text" class="form-control" name="name" id="text" placeholder="ChallengeName" required>
                </div>

                <div class="mb-3">
                    <label for="flag">Flag</label>
                    <input type="text" name="flag" class="form-control" id="flag" placeholder="MysteriousFlag">
                </div>

                <div class="mb-3">
                    <label for="flag">Associated server</label>
                    <select name="server" class="custom-select">
                        <option selected="">Select a server</option>
                        <?php foreach ($servers as $server) { ?>
                            <option value="<?php echo $server->getId() ?>"><?php echo $server->getLocalAddress() ?>
                                (<?php echo $server->getLocation() ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit">Add new challenge</button>
            </form>
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

