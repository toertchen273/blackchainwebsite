<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Überprüfen, ob der Nutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blockchain_nodes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// Abrufen der Nutzerdaten
$user_id = $_SESSION['user_id'];
$sql = "SELECT email, sol_address,tokens FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$sol_address = $user['sol_address'];
$email = $user['email'];
$tokens = $user['tokens'];

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlackChain Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="css/iofrm-theme33.css">
    <link rel="stylesheet" type="text/css" href="css/own-style.css">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <style>
      .form-content .form-items.with-bg{
          max-width: 900px;
      }
    </style>
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
            <div class="form-holder" style="margin-top: -150px;">
                <div class="form-content">
                    <div class="form-items with-bg">
                        <div class="website-logo-inside logo-normal">
                            <a class="anchor-title-text" href="index.html">
                                <h2 class="title-text"> <span style="font-size:20px;"><?php echo $email?></span> <br> BlackChain Dashboard</h2>
                            </a>
                        </div>
                        <!-- Start des News-Sliders -->
                        <div id="newsCarousel" class="carousel slide mt-4" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <h4>Staking-Anbindung des BlackChain Tokens wird aktualisiert</h4>
                                    <p>Die Staking-Anbindung des BlackChain Tokens wird derzeit überarbeitet. Nutzer können mit einer Kompensation rechnen.</p>
                                    <button class="btn btn-primary show-full-news" data-bs-toggle="modal" data-bs-target="#newsModal" data-news="Die Staking-Anbindung des BlackChain Tokens befindet sich aktuell in einem wichtigen Update-Prozess. Aufgrund von Wartungsarbeiten und einem Service-Update wird das Staking für diesen Token für die kommenden Tage pausiert. Das technische Team arbeitet intensiv daran, das Staking-System zu verbessern und die Stabilität zu erhöhen. Nach Abschluss der Arbeiten wird das Staking für den BlackChain Token sofort wieder hochgefahren.
                                    Wir verstehen, dass solche Unterbrechungen unangenehm sein können. Daher erhalten alle Nutzer, die das Staking im Zusammenhang mit diesem Update verwenden, eine Kompensation in Form von zusätzlichen Token in Form eines AirDrops. Dieser Prozess stellt sicher, dass die Nutzer für die entstandene Unannehmlichkeit entschädigt werden. Das Team von BlackChain bedankt sich für eure Geduld. Das Update ist voraussichtlich innerhalb der nächsten Tage abgeschlossen, und wir freuen uns darauf, euch das Staking in einer optimierten Form wieder zur Verfügung zu stellen.">Erfahre mehr</button>
                                </div>
                                <div class="carousel-item">
                                    <h4>Die Rolle von Nodes und Supernodes im BlackChain Netzwerk</h4>
                                    <p>Im BlackChain Netzwerk spielen Nodes und Supernodes eine entscheidende Rolle für die Sicherheit und Effizienz des Netzwerks. Erfahre hier mehr über ihre Funktionen.</p>
                                    <button class="btn btn-primary show-full-news" data-bs-toggle="modal" data-bs-target="#newsModal" data-news="BlackChain setzt auf ein fortschrittliches Delegated Proof of Stake (DPoS) Konsensmodell, bei dem Nodes und Supernodes das Herzstück des Netzwerks bilden. Während normale Nodes Transaktionen validieren und Smart Contracts umsetzen, übernehmen Supernodes zusätzlich wichtige Aufgaben wie die Blockerzeugung und die Governance des Netzwerks. Es gibt insgesamt 51 Supernodes im Netzwerk, die eine Mindestmenge von 5.000.000 BlackChain-Token staken müssen, um als Supernode fungieren zu können. Diese hohen Anforderungen garantieren die Sicherheit und das Engagement der Teilnehmer. Die Supernodes sind verantwortlich für die Validierung und Bestätigung von Transaktionen sowie die Erzeugung neuer Blöcke auf der BlackChain. Normale Nodes, die zwischen 1.000.000 und 2.500.000 BlackChain-Token halten, spielen ebenfalls eine wesentliche Rolle, insbesondere bei der Umsetzung von Smart Contracts und Mikrozahlungen. Sie wählen die Supernodes und tragen zur dezentralen Steuerung des Netzwerks bei. Um die Skalierbarkeit zu verbessern, setzt BlackChain auf moderne Technologien wie Sharding, bei dem Supernodes in Gruppen aufgeteilt werden, um die Effizienz und Ausfallsicherheit zu erhöhen. Der Betrieb eines Supernodes ist zeitlich begrenzt, und alle drei Monate erfolgt eine Rotation der Supernodes, um die Dezentralisierung und Beteiligung der Community zu gewährleisten. Diese dynamische Struktur fördert ein hohes Maß an Sicherheit und reduziert das Risiko, dass Einzelpersonen zu viel Kontrolle über das Netzwerk erlangen. BlackChain ermöglicht es somit nicht nur technikaffinen Nutzern, das Netzwerk zu sichern, sondern belohnt auch diejenigen, die sich aktiv durch Staking und Governance beteiligen. Wichtig ist, dass sich Nutzer zu regulären Nodes zusammenschließen können, wenn sie nicht über genug eigene Token verfügen, um dann zusammen einen Node zu bilden.">Erfahre mehr</button>
                                </div>
                                <div class="carousel-item">
                                    <h4>Namensänderung: BlackRock Token wird zum BlackChain Token</h4>
                                    <p>Der BlackRock Token wird in Kürze in den BlackChain Token umbenannt. Erfahren Sie hier, was sich ändern wird und welche neuen Funktionen auf Sie zukommen.</p>
                                    <button class="btn btn-primary show-full-news" data-bs-toggle="modal" data-bs-target="#newsModal" data-news="In den kommenden Wochen wird der BlackRock Token offiziell in den BlackChain Token umbenannt. Diese Umbenennung ist ein entscheidender Schritt, um die Funktionen des Tokens klarer zu kommunizieren und die Marke mit der BlackChain Plattform zu verbinden. Der BlackChain Token wird alle bisherigen Funktionen des BlackRock Tokens übernehmen und weiterhin die Abbildung von Finanzdienstleistungen auf der Blockchain unterstützen. Diese Änderung sorgt dafür, dass der Zweck des Tokens noch deutlicher hervorgehoben wird und es für Nutzer einfacher wird, die Verbindung zwischen der Blockchain und den angebotenen Finanzdienstleistungen zu verstehen. Die Umbenennung in den BlackChain Token wurde sorgfältig gewählt, um sicherzustellen, dass der Name die umfassenden Fähigkeiten und Anwendungen des Tokens besser widerspiegelt. Das BlackChain Team arbeitet derzeit an der finalen Umstellung, und es wird erwartet, dass der neue Token in den kommenden Wochen veröffentlicht wird. Nutzer müssen nichts weiter tun, da die Umstellung automatisch erfolgt. Alle bestehenden BlackRock Token werden nahtlos in den neuen BlackChain Token überführt. Weitere Updates zu diesem Thema werden in Kürze veröffentlicht.">Erfahre mehr</button>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Vorherige</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Nächste</span>
                            </button>
                        </div>
                        <!-- Ende des News-Sliders -->
                        <div class="text-center mt-3">
                            <a href="index.html" class="btn btn-secondary">Zurück zur Hauptseite</a>
                        </div>

                        <!-- Modales Fenster zum Anzeigen der vollständigen Nachricht -->
                        <div class="modal fade" id="newsModal" tabindex="-1" aria-labelledby="newsModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="newsModalLabel">BlackChain News</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="full-news-text"></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Token, Adresse und Logout-Felder rechts unten -->
                        <div class="info-container">
                            <div class="info-box token-box">
                                <h4>Deine BCT Tokenmenge</h4>
                                <p><?php echo number_format($tokens); ?> BCT</p>
                            </div>

                            <div class="info-box address-box">
                                <h4>Deine Solana Adresse</h4>
                                <p><?php echo $sol_address; ?></p> <!-- Hier kannst du die Adresse dynamisch ausgeben -->
                            </div>

                            <form method="POST" action="logout.php">
                                <button type="submit" name="logout" class="btn btn-primary">Ausloggen</button>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript-Dateien für Bootstrap 5 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Optional, da Bootstrap 5 kein jQuery benötigt -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Ihre vorhandenen JS-Dateien -->
    <script src="js/main.js"></script>

    <script>
        // jQuery-Skript zur Anzeige des vollständigen Textes in der Modalbox
        $(document).ready(function() {
            $('.show-full-news').on('click', function() {
                var fullText = $(this).data('news');
                $('#full-news-text').text(fullText);
            });
        });
    </script>
</body>
</html>
