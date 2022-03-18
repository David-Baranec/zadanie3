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
require_once 'vendor/autoload.php';

$client = new Google\Client();
//$client->setApplicationName("webte2-2022");
//$client->setDeveloperKey("Y423414604158-oh68r0p5ojtlo9khjqimth1a6s8risij.apps.googleusercontent.com");
$client->setAuthConfig('client_secret_423414604158-oh68r0p5ojtlo9khjqimth1a6s8risij.apps.googleusercontent.com.json');


if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    // var_dump($token);
    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    // $name =  $google_account_info->name;
    //var_dump($google_account_info);
    //var_dump($email);
    //var_dump($name);
    $login = $email;
    $psw = $google_account_info->getId();
    $meno = $google_account_info->given_name;
    $priezvisko = $google_account_info->family_name;
    $email = $google_account_info->email;

    $sql = "SELECT * FROM user WHERE login=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($user);
    session_start();
    if ($user == null) {

        $psw_hash = password_hash($psw, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `user` (login,password,name, surname, email) VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$login, $psw_hash, $meno, $priezvisko, $email]);
    }
    $sql = "SELECT * FROM user WHERE login=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($user);
    if (password_verify($psw, $user['password'])) {


        $sql = "INSERT INTO loginlog (id_user,type, time_stamp) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user['id'], 'google', date('Y-m-d H:i:s')]);
        $_SESSION['username'] = $user['login'];
        header('Location:index.php');
    }





    $_SESSION['username'] = $login;
    header('Location:index.php');
}
