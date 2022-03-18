<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("config.php");


session_start();

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "SELECT * FROM user WHERE login=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_POST["uname"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($user);
    if (password_verify($_POST['psw'], $user['password'])) {
        $_SESSION['username'] = $user['login'];
        $_SESSION['secret']=$user['secret'];

        $sql = "INSERT INTO loginlog (id_user,type, time_stamp) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user['id'], 'registracia', date('Y-m-d H:i:s')]);
        header('Location:check.php');
    }
}


require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('client_secret_423414604158-oh68r0p5ojtlo9khjqimth1a6s8risij.apps.googleusercontent.com.json');
$redirect_uri = 'https://site38.webte.fei.stuba.sk/zadanie3/redirect.php';
$client->addScope("email");
$client->addScope("profile");
$client->setRedirectUri($redirect_uri);
?>






<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <form action="login.php" method="post">
            <div class="container">
                <label for="uname"><b>Username</b></label>
                <input type="text" placeholder="Enter Username" name="uname" required>

                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>

                <button type="submit">Login</button>
            </div>

            <div class="container">

              
                <span> <?php echo "<a href='" . $client->createAuthUrl() . "'>Google Login</a>"; ?></span>
            </div>
            <div class="container">
                <span > <a href="register.php">Registracia </a></span>
            </div>
        </form>



        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </div>
</body>

</html>