<!DOCTYPE html>
<html>
<head>
    <title>Détails du match</title>
    <style>
        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .card:hover {
            background-color: #f0f0f0;
        }

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
        $matchId = $_GET['id'];

        $apiUrl = "https://www.balldontlie.io/api/v1/games/{$matchId}";
        $response = file_get_contents($apiUrl);
        $matchData = json_decode($response, true);

        if ($matchData && isset($matchData['home_team']['abbreviation'], $matchData['visitor_team']['abbreviation'], $matchData['date'])) {
            $homeTeamAbbreviation = $matchData['home_team']['abbreviation'];
            $visitorTeamAbbreviation = $matchData['visitor_team']['abbreviation'];
            $date = date('d/m/Y', strtotime($matchData['date']));

            echo "<h1>Détails du match</h1>";
            echo "<p>Équipe à domicile : {$homeTeamAbbreviation}</p>";
            echo "<p>Équipe visiteuse : {$visitorTeamAbbreviation}</p>";
            echo "<p>Date du match : {$date}</p>";
            echo "<p>Status : {$matchData['status']}</p>";
            echo "<p>Période : {$matchData['period']}</p>";
            echo "<p>Score Équipe à domicile : {$matchData['home_team_score']}</p>";
            echo "<p>Score Équipe visiteuse : {$matchData['visitor_team_score']}</p>";
            echo "<p>Saison : {$matchData['season']}</p>";
            echo "<p>Match de playoffs : " . ($matchData['postseason'] ? 'Oui' : 'Non') . "</p>";

            echo "<h2>Équipes :</h2>";
            echo "<div class='card' onclick='redirectToTeam({$matchData['home_team']['id']})'>";
            echo "<p>{$homeTeamAbbreviation}</p>";
            echo "</div>";
            echo "<div class='card' onclick='redirectToTeam({$matchData['visitor_team']['id']})'>";
            echo "<p>{$visitorTeamAbbreviation}</p>";
            echo "</div>";
        } else {
            echo "<h1>Erreur : Match non trouvé</h1>";
        }
    } else {
        echo "<h1>Erreur : Identifiant du match manquant</h1>";
    }
    ?>

    <script>
        function redirectToTeam(teamId) {
            window.location.href = `team.php?id=${teamId}`;
        }
    </script>
    <div id="button_home">
        <a href="home.php">
            <button> 
                Accueil
            </button>
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
