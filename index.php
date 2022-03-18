<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("config.php");

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
session_start();
?>




<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style3.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <br>
    <div class="container">
        <h1>Prihlaseny : <?php echo " " . $_SESSION['username']; ?></h1>
        <br><br><br>

        <?php


        if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
            echo "Vitaj  " . $_SESSION['username'];
            echo "<br>";
            echo "  tu je Odhlasenie";
            $sql = "SELECT id FROM `user` WHERE login like :login;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':login', $_SESSION['username'], PDO::PARAM_STR);
            $stmt->execute();
            $id = $stmt->fetch(PDO::FETCH_ASSOC);
            //echo "<>"var_dump($id);

            $sql = "SELECT * FROM `loginlog` WHERE id_user=:id;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id['id'], PDO::PARAM_INT);
            $stmt->execute();
            $resultList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $user='';
          //  var_dump($resultList);
        ?>
            <a href="logout.php">Odhlasenie</a>
        <?php
        } else {
            header("Location:login.php");
        }
        ?>


        <br><br><br>
        <h2>Your past logins</h2>
        <table class="table table-bordered table-striped ">
            <thead>
                <td>type</td>
                <td>date</td>
            </thead>

            <tbody>
                <?php

                if ($resultList != 0) {
                    foreach ($resultList as $value) {
                        echo "<tr><td>" . $value['type'] . "</td><td>" . $value['time_stamp'] . "</td><tr>";
                        $user=$value['type'];
                    }
                } else echo "<tr><td>-</td><td>-</td><tr>";
                ?>
            </tbody>
        </table>
        <?php
        $sql = "SELECT COUNT(type) FROM loginlog;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $allUsers = $stmt->fetch(PDO::FETCH_NUM);
       // var_dump($allUsers[0]*3);
        $sql = "SELECT COUNT(type) FROM `loginlog` WHERE type LIKE :type;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':type', $user, PDO::PARAM_STR);
        $stmt->execute();
        $userNumber = $stmt->fetch(PDO::FETCH_NUM);
       // var_dump($user);
        //var_dump($userNumber);
        $percentage=intval(($userNumber[0]/$allUsers[0])*100);
       // var_dump($percentage);
        ?>
        <h3>Statics of signed in users</h3>
        <div class="single-chart">
            <svg viewBox="0 0 36 36" class="circular-chart blue">
                <path class="circle-bg" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path class="circle" stroke-dasharray="<?php echo"$percentage";?>, 100" d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831" />
                <text x="18" y="20.35" class="percentage"><?php echo"$percentage";?>%</text>
            </svg>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>