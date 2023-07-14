<?php
require_once "autoload.php";
require_once "session.php";
require_once "util/Parsedown.php";

use models\Writeup;

$writeupId = $_GET['id'] ?? -1;
$userId = $_GET['user_id'] ?? -1;
/**
 * Permet de supprimer un writeup
 */
if (isset($_POST['delete']) || isset($_POST['edit'])) {
    $userId = $_POST['user_id'];
    $writeupId = $_POST['id'];
    $challId = $_POST['chall_id'];
    if (isset($_POST['delete']) && isset($_SESSION['loggedin']) && ($_SESSION['user']->getUsername() == $userId || $_SESSION['user']->isAdmin())) {
        Writeup::delete($writeupId);
        header("Location: /profile.php?user=$userId");
    } else if (isset($_POST['edit']) && isset($_SESSION['loggedin']) && $_SESSION['user']->getUsername() == $userId) {
        header("Location: /editWriteup.php?chall_id=$challId");
    }
}

if ($writeupId == -1 || $userId == -1) {
    header("Location: index.php");
    exit();
}

$writeup = Writeup::getById($writeupId, $userId);

if ($writeup == null) {
    header("Location: index.php");
    exit();
}
$Parsedown = new Parsedown();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Writeup</title>
    <link rel="icon" href="https://capturetheflag.withgoogle.com/img/Flag.png" type="image/x-icon">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <link rel="stylesheet" href="css/bootstrap4-neon-glow.min.css">


    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel='stylesheet' href='//cdn.jsdelivr.net/font-hack/2.020/css/hack.min.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-okaidia.min.css"
          integrity="sha256-zzHVEO0xOoVm0I6bT9v5SgpRs1cYNyvEvHXW/1yCgqU=" crossorigin="anonymous">
</head>
<body>

<?php
include "include/navbar.inc.php";
?>

<div class="container py-5 mb5">

    <h1><?php echo $writeup->getTitle() ?></h1>
    <p class="mb-5">Author: <?php echo $writeup->getAuthor()->getUsername() ?></p>
    <div class="row">
        <div class="col">
            <?php echo $Parsedown->text($writeup->getRawContent()); ?>
        </div>
    </div>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $writeupId ?>">
        <input type="hidden" name="user_id" value="<?php echo $userId ?>">
        <input type="hidden" name="chall_id" value="<?php echo $writeup->getChallengeId() ?>">
        <input type="submit" name="edit" class="btn btn-primary" value="Edit">
        <input type="submit" name="delete" class="btn btn-danger" value="Delete">
    </form>
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
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.js"
        integrity="sha256-S5mU/F9EHUxP/yPe4lNcCQEL+TsdkMLHKwQww9PxAI4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"
        integrity="sha256-AjM0J5XIbiB590BrznLEgZGLnOQWrt62s3BEq65Q/I0=" crossorigin="anonymous"></script>
</body>
</html>

