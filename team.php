<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="team.css">
    <title>Détails de l'équipe</title>
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
        $teamId = $_GET['id'];

        $apiUrl = "https://www.balldontlie.io/api/v1/teams/{$teamId}";
        $response = file_get_contents($apiUrl);
        $teamData = json_decode($response, true);

        if ($teamData && isset($teamData['abbreviation'], $teamData['city'], $teamData['conference'], $teamData['division'], $teamData['full_name'], $teamData['name'])) {
            $abbreviation = $teamData['abbreviation'];
            $city = $teamData['city'];
            $conference = $teamData['conference'];
            $division = $teamData['division'];
            $fullName = $teamData['full_name'];
            $name = $teamData['name'];

            echo "<h1>Détails de l'équipe</h1>";
            echo "<p>Abbréviation : {$abbreviation}</p>";
            echo "<p>Ville : {$city}</p>";
            echo "<p>Conférence : {$conference}</p>";
            echo "<p>Division : {$division}</p>";
            echo "<p>Nom complet : {$fullName}</p>";
            echo "<p>Nom : {$name}</p>";

            $playersApiUrl = "https://www.balldontlie.io/api/v1/players?per_page=100";
            $allPlayers = [];
            $currentPage = 1;

            while (true) {
                $response = file_get_contents($playersApiUrl . "&page={$currentPage}");
                $playersData = json_decode($response, true);

                if (empty($playersData['data'])) {
                    break;
                }

                $allPlayers = array_merge($allPlayers, $playersData['data']);
                $currentPage++;
            }

            $teamPlayers = array_filter($allPlayers, function ($player) use ($teamId) {
                return isset($player['team']['id']) && $player['team']['id'] == $teamId;
            });

            if (!empty($teamPlayers)) {
                echo "<h2>Joueurs de l'équipe :</h2>";
                echo "<ul>";
                foreach ($teamPlayers as $player) {
                    echo "<li>";
                    echo "<div class='card' onclick='redirectToPlayer({$player['id']})'>";
                    echo "<p>{$player['first_name']} {$player['last_name']}</p>";
                    echo "</div>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Aucun joueur trouvé pour cette équipe.</p>";
            }
        } else {
            echo "<h1>Erreur : Équipe non trouvée</h1>";
        }
    } else {
        echo "<h1>Erreur : Identifiant de l'équipe manquant</h1>";
    }
    ?>

    <script>
        function redirectToPlayer(playerId) {
            window.location.href = `player.php?id=${playerId}`;
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
