<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="player.css">
    <title>Détails du joueur</title>
    <style>
        body {
            position: relative;
        }

        .popup-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.94);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            text-align: center;
        }

        .popup p {
            margin-bottom: 20px;
        }

        .popup a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .popup a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_GET['id'])) {
        $playerId = $_GET['id'];
        $apiUrl = "https://www.balldontlie.io/api/v1/players/{$playerId}";
        $response = file_get_contents($apiUrl);
        $playerData = json_decode($response, true);

        if ($playerData && isset($playerData['first_name'], $playerData['last_name'], $playerData['team']['full_name'])) {
            $firstName = $playerData['first_name'];
            $lastName = $playerData['last_name'];
            $teamName = $playerData['team']['full_name'];
            $teamId = $playerData['team']['id'];
            $position = $playerData['position'];
            $heightFeet = $playerData['height_feet'];
            $heightInches = $playerData['height_inches'];
            $weightPounds = $playerData['weight_pounds'];

            echo "<h1>Détails du joueur</h1>";
            echo "<h2>Nom : {$firstName} {$lastName}</h2>";
            echo "<p>Équipe : {$teamName}</p>";

            if ($position) {
                echo "<p>Position : {$position}</p>";
            } else {
                echo "<p>Position : Non renseignée</p>";
            }

            if ($heightFeet && $heightInches) {
                echo "<p>Taille : {$heightFeet} pieds {$heightInches} pouces</p>";
            } else {
                echo "<p>Taille : Non disponible</p>";
            }

            if ($weightPounds) {
                echo "<p>Poids : {$weightPounds} livres</p>";
            } else {
                echo "<p>Poids : Non disponible</p>";
            }

            echo "<div class='card' onclick='redirectToTeam({$teamId})'>";
            echo "<p>{$teamName}</p>";
            echo "</div>";
        } else {
            echo "<h1>Erreur : Joueur non trouvé</h1>";
        }
    } else {
        echo "<h1>Erreur : Identifiant du joueur manquant</h1>";
    }
    ?>

    <script>
        function redirectToTeam(teamId) {
            window.location.href = `team.php?id=${teamId}`;
        }
    </script>
    <div id="button_home">
        <a href="home.php">
            <button class="button-1" role="button">Accueil</button>
        </a>
    </div>

    <div class="popup-container">
        <div class="popup">
            <p>Pour voir le contenu de cette page, veuillez vous connecter ou vous inscrire.</p>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        </div>
    </div>
</body>
</html>
