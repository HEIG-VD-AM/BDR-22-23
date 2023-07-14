<?php
require_once 'session.php';
?>

<div class="navbar-dark text-white">
    <div class="container">
        <nav class="navbar px-0 navbar-expand-lg navbar-dark">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a href="../index.php" class="pl-md-0 p-3 text-light">Home</a>
                    <a href="../displayTeams.php" class="p-3 text-decoration-none text-light">Teams</a>
                    <a href="../displayEvents.php" class="p-3 text-decoration-none text-light">Events</a>
                    <?php
                    if(isset($_SESSION['loggedin']) && $_SESSION['user']->isAdmin()) {
                        echo "<a href='../adminPage.php' class='p-3 text-decoration-none text-light'>Admin</a>";
                    }
                    ?>
                </div>
            </div>

            <?php
            if(isset($_SESSION['loggedin'])) {
                echo "<a href='../profile.php?user=" . $_SESSION['user']->getUsername() . "' class='p-3 text-decoration-none text-light'>" . $_SESSION['user']->getUsername() . "</a>";
            }

            if(isset($_SESSION['loggedin'])) {
                echo "<a href='../logout.php' class='btn btn-sm btn-outline-light ml-auto js-ht-download-link' data-type='theme' data-id='95'>Logout</a>";
            } else {
                echo "<a href='../login.php' class='btn btn-sm btn-outline-light ml-auto js-ht-download-link' data-type='theme' data-id='95'>Login</a>";
            }
            ?>
        </nav>
    </div>
</div>