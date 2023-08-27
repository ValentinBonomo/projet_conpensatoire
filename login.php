<!DOCTYPE html>
<html>
<head>
    <title>Page de connexion</title>
</head>
<body>
    <div id="button_login">
        <div id="register">
            <a href="register.php">
                Pas de comtpe ?
            </a>
        </div>
        <a href="home.php">
            <button> 
                Accueil
            </button>
        </a>
    </div>
    



    <h2>Connexion</h2>
    <form action="login.php" method="POST">
        <label for="pseudo_email">Pseudo/Email</label>
        <input type="text" name="pseudo_email" required><br><br>
        
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" value="Se connecter">
    </form>

    <?php
    include 'index.php';

    if (isset($_COOKIE['connected'])) {
        header('Location: home.php');
        exit;
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pseudoEmail = $_POST['pseudo_email'];
        $password = $_POST['password'];
        
        
        if (!$conn) {
            die("Erreur de connexion à la base de données: " . mysqli_connect_error());
        }
        
        $pseudoEmail = mysqli_real_escape_string($conn, $pseudoEmail);
        
        $query = "SELECT * FROM info WHERE pseudo = '$pseudoEmail' OR email = '$pseudoEmail'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password'];
            
            if (password_verify($password, $hashedPassword)) {
                setcookie('connected', 'true', time() + 3600, '/');
                echo "Connexion réussie.";
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Pseudo ou email non trouvé.";
        }
        
        mysqli_close($conn);
    }
    ?>

    
</body>
</html>
