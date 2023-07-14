<?php

require_once "autoload.php";
require_once "session.php";

use models\Team;
use models\User;
use util\PGSQLConnection;


if(!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

// Vérification du formulaire de création d'équipe
$submit = isset($_POST['submit']);

    if ($submit && isset($_SESSION['loggedin']) && $_SESSION['user']->getTeam() == null) {

        $name = $_POST["name"] ?? "";
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirmPassword"] ?? "";
        $type = $_POST["type"] ?? "";

        if ($password !== $confirmPassword) {
            header("Location: displayTeams.php");
            exit;
        }

        if (strlen($name) > 0 && strlen($password) > 0 && strlen($type) > 0) {
            if (!Team::exists($name)) {
                $team = new Team($name, $password, $type , $_SESSION['user']);
                $team->save();
                $team->loadTeamMembers();
                $_SESSION['user']->setTeam($team);
                header("Location: displayTeams.php?successMessage=Team created successfully");
            } else {
                header("Location: displayTeams.php");
            }
        } else {
            header("Location: displayTeams.php");
        }
        exit;
    }

// Affichage des équipes
$teams = [];
$stmt = PGSQLConnection::instance()->prepare("SELECT * FROM Equipe");
$stmt->execute();
$result = $stmt->fetchAll();
foreach ($result as $row) {
    $teams[] = new Team(
        $row['nom'],
        $row['motdepasse'],
        $row['typeequipe'],
        User::getUserFromName($row['crt_pseudo']));
}

$teamTypes = Team::getTeamTypes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Teams</title>
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

<div class="container py-5 mb5">
    <h1 class="mb-5">Teams list</h1>

    <div class="row">
        <div class="col-md-3">

            <?php if($_SESSION['user']->getTeam() == null) { ?>
            <button data-toggle="modal" data-target="#createTeam" data-whatever="@mdo" type="button" class="btn btn-success mt-2 btn-block">Create my team</button>
            <div class="modal fade" id="createTeam" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create a new team</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="needs-validation" method="post" action="displayTeams.php">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Team's name</label>
                                    <input type="text" class="form-control" name="name" placeholder="My super team">

                                    <div class="mt-2">
                                        <label for="type">Team's type</label>
                                        <select class="custom-select" name="type">
                                            <option disabled selected="">Team's type</option>
                                            <?php
                                            foreach ($teamTypes as $teamType) {
                                                echo "<option value=\"".$teamType."\">".$teamType."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <label for="password" class="col-form-label">Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="****************">

                                    <label for="confirmPassword" class="col-form-label">Confirm password</label>
                                    <input type="password" class="form-control" name="confirmPassword" placeholder="****************">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success" name="submit">Validate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php } else if (isset($_SESSION['user']) && $_SESSION['user']->getTeam() != null) { ?>
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
        <div class="col-md-9">
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <?php if(isset($_SESSION['loggedin']) && $_SESSION['user']->getTeam() == null) { ?>
                    <th scope="col">Actions</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($teams as $team) {?>

                <tr>
                    <td>
                        <a href="team.php?name=<?php echo $team->getName();?>"><?php echo $team->getName(); ?></a>
                    </td>
                    <th><?php echo $team->getType();?></th>

                    <?php if(isset($_SESSION['loggedin']) && $_SESSION['user']->getTeam() == null) { ?>
                    <td>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#joinTeam<?php echo $team->getName();?>" data-whatever="@mdo">Join team</button>
                    </td>

                    <div class="modal fade" id="joinTeam<?php echo $team->getName();?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Join a new team</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form class="needs-validation" method="post" action="teamJoin.php?team=<?php echo $team->getName();?>">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="password" class="col-form-label">Password</label>
                                            <input type="password" class="form-control" name="password" placeholder="****************">
                                            <br>
                                            <p>Enter the secret password to join this team !</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success" name="submit">Validate</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tr>

                <?php }?>

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
