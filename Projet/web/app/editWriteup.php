<?php
require_once "autoload.php";
require_once "session.php";

use models\Writeup;

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: /index.php");
}

$challId = $_GET['chall_id'] ?? -1;

if ($challId == -1 && !isset($_POST['submit'])) {
    header("Location: /index.php");
}

$writeup = Writeup::getWriteupFromUsername($challId, $_SESSION['user']->getUsername());
/**
 * Permet de créer un writeup
 */
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $challengeId = $_POST['chall_id'];
    $id = $_POST['id'] ?? -1;

    $writeup = new Writeup($title, $content, $_SESSION['user'], $challengeId);
    if ($id != -1) {
        $writeup->setId($id);
    }
    $writeup->save();
    header("Location: /writeup.php?id=" . $writeup->getId() . "&user_id=" . $_SESSION['user']->getUsername());
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Writeup</title>
    <link rel="icon" href="https://capturetheflag.withgoogle.com/img/Flag.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/highlight.js/latest/styles/github.min.css">
    <style>
        .CodeMirror {
            background-color: white;
        }
        .editor-toolbar {
            background-color: white;
        }
    </style>

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

    <h1 class="mb-5">Edit writeup</h1>

    <div class="row">
        <div class="col">
            <form method="post" class="needs-validation">
                <div class="form-group">
                    <div class="mb-3">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Title" required value="<?php echo $writeup != null ? $writeup->getTitle() : "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="content">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="3" required><?php echo $writeup != null ? $writeup->getRawContent() : "" ?></textarea>
                    </div>
                    <input type="hidden" name="chall_id" value="<?php echo $challId ?>">
                    <input type="hidden" name="id" value="<?php echo $writeup != null ? $writeup->getId() : -1 ?>">
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">Publish writeup</button>

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
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="https://cdn.jsdelivr.net/highlight.js/latest/highlight.min.js"></script>

<script>
    var simplemde = new SimpleMDE({
        element: document.getElementById("content"),
        renderingConfig: {
            codeSyntaxHighlighting: true,
        }
    });
    simplemde.codemirror.on("change", function(){
        document.getElementById("content").value = simplemde.value();
    });
</script>

</body>
</html>

