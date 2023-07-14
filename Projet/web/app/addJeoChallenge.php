<?php

use models\Event;
use models\Jeopardy;
use models\User;
use util\PGSQLConnection;

require_once "session.php";

if(!isset($_SESSION['loggedin']) || !$_SESSION['user']->isAdmin()){
    header("Location: /index.php");
    exit();
}

/**
 * Permet d'ajouter un challenge de type Jeopardy
 */
if (isset($_POST['submit'])) {
    $name = $_POST["nom"];
    $description = $_POST["description"] ?? "";
    $type = $_POST["type"];
    $author = $_POST["author"];
    $creationDate = $_POST["start-date"];
    $endDate = $_POST["end-date"];
    $Jeopardy = new Jeopardy(0,$name, $_GET["id"], $description, $type, $author, new DateTime($creationDate), new DateTime($endDate));
    $Jeopardy->save();

    header("Location: /displayEvents.php");
}

/**
 * Permet de récupérer un event selon son id
 */
$stmt = PGSQLConnection::instance()->prepare("SELECT * FROM Evenement WHERE id = :id");
$stmt->bindParam(":id", $_GET["id"]);
$stmt->execute();
$result = $stmt->fetch();
if(empty($result))
{
    header("Location: displayEvents.php");
    exit();
}
$event = new Event($result["id"], $result["nom"], $result["estenligne"], new DateTime($result["datecreation"]),new DateTime($result["datefin"]), $result["estjeopardy"], User::getUserFromName($result["crt_pseudo"]));

$challengeTypes = Jeopardy::getChallengeTypes();
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

<div class="container py-5 mb5">

    <h1 class="mb-5">Add new Jeopardy challenge</h1>

    <div class="row py-4">
        <div class="col-md-8 order-md-1">
            <form class="needs-validation" method="post" action="addJeoChallenge.php?id=<?php echo $_GET["id"]?>" >
                <div class="mb-3">
                    <label for="nom">Name</label>
                    <input type="text" class="form-control" name="nom" id="nom" placeholder="Challenge name" required>
                    <div class="invalid-feedback" style="width: 100%;">
                        Your challenge name is required.
                </div>

                <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" placeholder="Add a description"></textarea>
                </div>

                <div class="mb-3">
                    <label for="type" >Associated type</label>
                        <select class="custom-select" name="type" id="type" required>
                            <option disabled selected="">Select a type of challenge</option>
                            <?php
                            foreach ($challengeTypes as $challengeType) {
                                echo "<option value=\"".$challengeType."\">".$challengeType."</option>";
                            }
                            ?>
                        </select>
                </div>

                <div class="mb-3">
                    <label for="author">Author</label>
                    <input type="text" name="author" class="form-control" id="author" placeholder="author's name">
                </div>

                <div class="mb-3">
                    <label for="start-date">Start date</label>
                    <input class="form-control" name="start-date" type="date" value="<?php echo $event->getStartDate()->format("Y-m-d"); ?>" id="start-date" max="<?php echo $event->getEndDate()->format("Y-m-d"); ?>" min="<?php echo $event->getStartDate()->format("Y-m-d"); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="end-date">End date</label>
                    <input class="form-control" name="end-date" type="date" value="<?php echo $event->getEndDate()->format("Y-m-d")?>" id="end-date" max="<?php echo $event->getEndDate()->format("Y-m-d")?>" min="<?php echo $event->getStartDate()->format("Y-m-d")?>" required>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit">add this challenge</button>
            </form>
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

