<?php
require_once "session.php";
require_once 'autoload.php';

use models\User;
/*
 * permet de créer un compte utilisateur
 */
$submit = isset($_POST['submit']);
if ($submit) {
    $username = $_POST["username"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";
    $website = $_POST["website"] ?? "";
    $description = $_POST["description"] ?? "";

    if ($password !== $confirmPassword) {
        header("Location: accountCreation.php");
        exit;
    }

    if (strlen($username) > 0 && strlen($email) > 0 && strlen($password) > 0) {
        if (!User::exists($username, $email)) {
            $user = new User($username, $email, $description, $website, false, $password);
            $user->save();
            header("Location: login.php?successMessage=Account created successfully");
        } else {
            header("Location: accountCreation.php");
        }
    } else {
        header("Location: accountCreation.php");
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Create Account</title>
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

    <h1 class="mb-5">Account creation</h1>

    <div class="row py-4">
        <div class="col-md-8 order-md-1">
            <form class="needs-validation" method="post" action="accountCreation.php">

                <div class="mb-3">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">@</span>
                        </div>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                        <div class="invalid-feedback" style="width: 100%;">
                            Your username is required.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com" value="" required>
                </div>

                <div class="mb-3">
                    <label for="website">Website <span class="text-muted">(Optional)</span></label>
                    <input type="website" name="website" class="form-control" id="website" placeholder="my-website.com">
                </div>

                <div class="mb-3">
                    <label for="description">Short description <span class="text-muted">(Optional)</span></label>
                    <input type="description" name="description" class="form-control" id="description" placeholder="Describe yourself :)">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="****************" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="confirmPassword">Confirm password</label>
                        <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="****************" required>
                    </div>
                </div>

                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" name="submit">Continue to create account</button>
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

