<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blockchain_nodes";

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
if (isset($_SESSION['user_id'])) {
    // Wenn der Benutzer eingeloggt ist, wird er zur newsboard.php weitergeleitet
    header("Location: newsboard.php.php");
    exit();
}

// Prüfen, ob das Formular abgesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // E-Mail in der Datenbank suchen
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Prüfen, ob das Passwort korrekt ist
        if (password_verify($password, $user['password'])) {
            // Login erfolgreich - Session starten
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            // Weiterleitung zur Dashboard-Seite
            header("Location: newsboard.php");
            exit();
        } else {
            // Falsches Passwort
            echo "Falsches Passwort.";
        }
    } else {
        // E-Mail nicht gefunden
        echo "Kein Nutzer mit dieser E-Mail-Adresse gefunden.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlackChain Login</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="css/iofrm-theme33.css">
    <link rel="stylesheet" type="text/css" href="css/own-style.css">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="form-body">
        <div class="iofrm-layout">
            <div class="img-holder">

                <div class="bg"></div>
                <div class="info-holder">
                    <img src="images/logo.png" alt="">
                </div>
            </div>
            <div class="form-holder">
                <div class="form-content">
                    <div class="form-items with-bg">
                        <div class="website-logo-inside logo-normal">
                            <a class="anchor-title-text" href="index.html">
                                <h2 class="title-text">BlackChain</h2>
                            </a>
                        </div>
                        <h3 class="font-md">Hier kannst du dich in deinen BlackChain Account einloggen.</h3>
                        <p>Erhalte News und Updates zu allem was die BlackChain betrifft.</p>

                        <!-- Erfolgsbenachrichtigung nach Registrierung -->
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php
                                    echo $_SESSION['success_message'];
                                    unset($_SESSION['success_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <input class="form-control" type="email" id="email" name="email" placeholder="E-Mail Adresse" required>
                            <input class="form-control" type="password" id="password" name="password" placeholder="Passwort" required>
                            <div class="form-button d-flex">
                                <button id="submit" type="submit" class="btn btn-primary">Einloggen</button>
                                <a href="./register.php" class="btn btn-outline-primary">Voranmelden!</a>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="index.html" class="btn btn-secondary">Zurück zur Hauptseite</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script src="js/plugins.min.js"></script>
<script src="js/functions.bundle.js"></script>w

<!-- SLIDER REVOLUTION 5.x SCRIPTS  -->
<script src="include/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
<script src="include/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
<script src="include/rs-plugin/js/addons/revolution.addon.particles.min.js"></script>

<script src="include/rs-plugin/js/extensions/revolution.extension.actions.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.carousel.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.kenburn.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.layeranimation.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.migration.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.navigation.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.parallax.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.slideanims.min.js"></script>
<script src="include/rs-plugin/js/extensions/revolution.extension.video.min.js"></script>
</body>
</html>
