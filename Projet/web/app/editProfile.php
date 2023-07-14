<?php
require_once 'autoload.php';
require_once "session.php";

/**
 * Permet de créer un utilisateur
 */
$submit = isset($_POST['submit']);
if ($submit) {
    $email = $_POST["email"] ?? -1;
    $website = $_POST["website"] ?? -1;
    $description = $_POST["description"] ?? -1;

    if ($email != -1 && $email != $_SESSION['user']->getEmail()) {
        $_SESSION['user']->setEmail($email);
    }

    if ($website != -1 && $website != $_SESSION['user']->getWebsite()) {
        $_SESSION['user']->setWebsite($website);
    }

    if ($description != -1 && $description != $_SESSION['user']->getDescription()) {
        $_SESSION['user']->setDescription($description);
    }

    $_SESSION['user']->save();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Profile</title>
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

    <h1 class="mb-5">Edit profile</h1>

    <div class="row py-4">
        <div class="col-md-8 order-md-1">
            <form class="needs-validation" method="post">
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="my-address@mail.com" value="<?php echo $_SESSION['user']->getEmail() ?>">
                </div>

                <div class="mb-3">
                    <label for="website">Website <span class="text-muted">(Optional)</span></label>
                    <input type="website" name="website" class="form-control" id="website" placeholder="my-website.com" value="<?php echo $_SESSION['user']->getWebsite() ?>">
                </div>

                <div class="mb-3">
                    <label for="description">Short description <span class="text-muted">(Optional)</span></label>
                    <input type="description" name='description' class="form-control" id="website" placeholder="Describe yourself!" value="<?php echo $_SESSION['user']->getDescription() ?>">
                </div>

                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit" value="button">Save modifications</button>
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

