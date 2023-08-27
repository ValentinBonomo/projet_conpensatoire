<!DOCTYPE html>
<html>
<head>
    <title>Page d'enregistrement</title>
</head>
<body>
    <div id="button_login">
        <div id="login">
            <a href="login.php">
                Déjà un compte ?
            </a>
        </div>
        <a href="home.php">
            <button> 
                Accueil
            </button>
        </a>
    </div>

    <h2>S'enregistrement</h2>
    <form action="register.php" method="POST">
        <label for="pseudo">Pseudo:</label>
        <input type="text" name="pseudo" required><br><br>
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="S'inscrire">
    </form>

    <?php
    include 'index.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pseudo = $_POST['pseudo'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!$conn) {
            die("Erreur de connexion à la base de données: " . mysqli_connect_error());
        }

        $query = "SELECT * FROM info WHERE pseudo = '$pseudo'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "Le pseudo est déjà utilisé.";
        } else {
            $query = "SELECT * FROM info WHERE email = '$email'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "L'email est déjà utilisé.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insertQuery = "INSERT INTO info (pseudo, email, password) VALUES ('$pseudo', '$email', '$hashedPassword')";

                if (mysqli_query($conn, $insertQuery)) {
                    setcookie('connected', 'true', time() + 3600, '/');
                    echo "Enregistrement réussi.";
                } else {
                    echo "Erreur lors de l'enregistrement.";
                }
            }
        }
        mysqli_close($conn);
    }
    ?>
</body>
</html>
