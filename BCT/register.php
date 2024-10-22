<?php
// Fehlerberichterstattung aktivieren
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
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}
if (isset($_SESSION['user_id'])) {
    // Wenn der Benutzer eingeloggt ist, wird er zur newsboard.php weitergeleitet
    header("Location: newsboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $sol_address = $_POST['sol_address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tokens = intval($_POST['tokens']);
    $share_node = $_POST['share_node'];

    // Passwortüberprüfung
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwörter stimmen nicht überein.";
        header("Location: register.php");
        exit();
    }

    // Passwort hashen
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Überprüfen, ob die E-Mail bereits existiert
    $sql_check_email = "SELECT id FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check_email);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $_SESSION['error_message'] = "Diese E-Mail-Adresse ist bereits registriert.";
        header("Location: register.php");
        exit();
    }

    // SQL-Abfrage zur Einfügung der Daten
    $sql = "INSERT INTO users (email, sol_address, password, tokens, share_node) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $_SESSION['error_message'] = "Fehler bei der Vorbereitung der SQL-Anweisung: " . $conn->error;
        header("Location: register.php");
        exit();
    }

    $stmt->bind_param("sssis", $email, $sol_address, $hashed_password, $tokens, $share_node);

    if ($stmt->execute()) {
        // Erfolgreiche Registrierung - Erfolgsnachricht setzen
        $_SESSION['success_message'] = "Erfolgreich registriert! Bitte melden Sie sich an.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Fehler bei der Registrierung: " . $stmt->error;
        header("Location: register.php");
        exit();
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
    <title>Voranmeldung BlackChain</title>

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
                        <h3 class="font-md">Voranmeldung zur <br>BlackChain Node.</h3>

                        <!-- Erfolgs- oder Fehlermeldung anzeigen -->
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php
                                    echo $_SESSION['error_message'];
                                    unset($_SESSION['error_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="register.php">
                            <input class="form-control" type="email" id="email" name="email" placeholder="Deine E-Mail Adresse" required>
                            <input class="form-control" type="text" id="sol_address" name="sol_address" placeholder="Deine Solana Adresse" required>
                            <input class="form-control" type="password" id="password" name="password" placeholder="Passwort" required>
                            <input class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="Passwort wiederholen" required>
                            <input class="form-control" type="number" id="tokens" name="tokens" min="0" placeholder="Deine Tokenmenge" required>
                            <div class="input-group">
                                <label for="share_node">Node teilen:</label>
                                <select id="share_node" name="share_node" required>
                                    <option value="yes">Ja</option>
                                    <option value="no">Nein</option>
                                </select>
                            </div>
                            <p class="confirm-registration" style="font-size:12px;text-align:left">Mit dem Registrieren, stimmen Sie zu, dass wir Sie bezüglich der BlackChain per E-Mail kontaktieren dürfen.</p>
                            <div class="form-button d-flex">
                                <button id="submit" type="submit" class="btn btn-primary">Registrieren</button>
                                <a href="./login.php" class="btn btn-outline-primary">Zum Login</a>
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
</body>
</html>
