<?php

use models\Jeopardy;
use models\Room;
use models\Team;
use models\User;
use models\Server;

require_once "session.php";

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if(!$_SESSION['user']->isAdmin()) {
    header("Location: index.php");
    exit();
}
if(isset($_POST['promote'])) {
    $user = User::getUserFromName($_POST['username']);
    $user->setAdmin(true);
    header("Location: adminPage.php");
}

if(isset($_POST['demote'])) {
    $user = User::getUserFromName($_POST['username']);
    $user->setAdmin(false);
    header("Location: adminPage.php");
}

if(isset($_POST['delete'])) {
    User::deleteUser($_POST['username']);
    header("Location: adminPage.php");
}

if(isset($_POST['addRoom'])) {
    $numero = $_POST['numero'];
    $etage = $_POST['etage'];
    $room = new Room($numero, $etage);
    $room->save();
    header("Location: adminPage.php");
}

if(isset($_POST['addTeamType'])) {
    Team::addTeamType($_POST['teamType']);
    header("Location: adminPage.php");
}

if(isset($_POST['deleteTeamType'])) {
    Team::deleteTeamType($_POST['teamType']);
    header("Location: adminPage.php");
}

if(isset($_POST['addChallengeType'])) {
    Jeopardy::addChallengeType($_POST['challengeType']);
    header("Location: adminPage.php");
}

if(isset($_POST['deleteChallengeType'])) {
    Jeopardy::deleteChallengeType($_POST['challengeType']);
    header("Location: adminPage.php");
}

if(isset($_POST['addServer'])) {
    $ip = $_POST['ip'];
    $room = $_POST['room'];
    $email = $_POST['email'];
    $server = new Server(0, $ip, (int)substr($room, 1, strlen($room)), $room[0], $email);
    $server->save();
    header("Location: adminPage.php");
}

$rooms = Room::getRooms();

$teamTypes = Team::getTeamTypes();
$index = array_search('Other',$teamTypes);
if($index !== FALSE){
    unset($teamTypes[$index]);
}

$challengeTypes = Jeopardy::getChallengeTypes();
$index = array_search('Misc',$challengeTypes);
if($index !== FALSE){
    unset($challengeTypes[$index]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin</title>
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
        <div class="row mt-3">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Promote user</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">@</span>
                                        </div>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required="">
                                        <div class="invalid-feedback" style="width: 100%;">
                                            Your username is required.
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="promote" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Demote user</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">@</span>
                                        </div>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required="">
                                        <div class="invalid-feedback" style="width: 100%;">
                                            Your username is required.
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="demote" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Add room</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="input-group col-md-6">
                                            <input type="text" class="form-control" id="username" name="etage" placeholder="Room floor" required="">
                                            <div class="invalid-feedback" style="width: 100%;">
                                                The room floor is required.
                                            </div>
                                        </div>
                                        <div class="input-group col-md-6"><input type="text" class="form-control" id="username" name="numero" placeholder="Room number" required="">
                                            <div class="invalid-feedback" style="width: 100%;">
                                                The room number is required.
                                            </div>
                                        </div>
                                    </div>
                                    <small id="emailHelp" class="form-text text-muted">This operation is irreversible</small>
                                </div>
                                <button type="submit" name="addRoom" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Delete user</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">@</span>
                                        </div>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required="">
                                        <div class="invalid-feedback" style="width: 100%;">
                                            Your username is required.
                                        </div>
                                    </div>
                                    <small id="emailHelp" class="form-text text-muted">This operation is irreversible</small>
                                </div>
                                <button type="submit" name="delete" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Add new team type</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="teamType" name="teamType" placeholder="Team's type" required="">
                                    </div>
                                </div>
                                <button type="submit" name="addTeamType" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Delete team type</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <select class="custom-select" id="teamType" name="teamType">
                                        <option disabled selected="">Team's type</option>
                                        <?php
                                        foreach ($teamTypes as $teamType) {
                                            echo "<option value=\"".$teamType."\">".$teamType."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" name="deleteTeamType" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Add new Jeopardy type</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="challengeType" name="challengeType" placeholder="Challenge's type" required="">
                                    </div>
                                </div>
                                <button type="submit" name="addChallengeType" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Delete Jeopardy type</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <select class="custom-select" id="challengeType" name="challengeType">
                                        <option disabled selected="">Jeopardy's type</option>
                                        <?php
                                        foreach ($challengeTypes as $challengeType) {
                                            echo "<option value=\"".$challengeType."\">".$challengeType."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" name="deleteChallengeType" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Add server</h6>
                        <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
                            <form class="needs-validation" method="post" action="adminPage.php">
                                <div class="form-group">
                                    <div class="input-group">
                                    </div>
                                    <input type="text" class="form-control" id="ip" name="ip" placeholder="IP" required="">
                                    <div class="invalid-feedback" style="width: 100%;">
                                        The IP is required.
                                    </div>
                                    </div>
                                <div class="form-group">
                                    <div class="input-group">
                                    </div>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="email maintainer" required="">
                                    <div class="invalid-feedback" style="width: 100%;">
                                        An email is required
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Room</label>
                                    <select class="custom-select" id="room" name="room">
                                        <option selected="">Select a Room</option>
                                        <?php foreach ($rooms as $r) { ?>
                                            <option value="<?php echo $r->getEtage() . $r->getNumero()?>"><?php echo $r->getEtage() . $r->getNumero() ?></option>
                                        <?php } ?>
                                    </select>
                                    <small id="emailHelp" class="form-text text-muted">This operation is irreversible</small>
                                </div>
                                <button type="submit" name="addServer" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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

