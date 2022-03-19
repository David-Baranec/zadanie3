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
/*
$login = "baranecx";
$psw = "nbu123";
$meno = "david";
$priezvisko = "baranec";
$email = "xbaranecd@stuba.sk";


$psw_hash = password_hash($psw, PASSWORD_DEFAULT);
*/
function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
$emailErr = $nameErr = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = test_input($_POST["email"]);
  // check if e-mail address is well-formed
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $emailErr = "Invalid email format";
  } else {

    $sql = "SELECT * FROM user WHERE login=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_POST["login"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // var_dump($user);
    //header('Location:register.php');
    //var_dump($user);
    if ($user['id'] != null) {
      $nameErr = "Login is already used, try other and submit your choice";
      $name = test_input($_POST["login"]);
      // check if name only contains letters and whitespace
      if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $nameErr = "Only letters and white space allowed";
      }

      if (empty($_POST["email"])) {
        $emailErr = "Email is required";
      } else {
        $email = test_input($_POST["email"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $emailErr = "Invalid email format";
        }
      }
    } else {
      $login = $_POST["login"];
      $psw = $_POST["psw"];
      $meno = $_POST["name"];
      $priezvisko = $_POST["surname"];
      $email = $_POST["email"];
      require_once 'PHPGangsta/GoogleAuthenticator.php';

      $websiteTitle = 'MyWebsite';

      $ga = new PHPGangsta_GoogleAuthenticator();

      $secret = $ga->createSecret();

      $psw_hash = password_hash($psw, PASSWORD_DEFAULT);
      $sql = "INSERT INTO `user` (login,password,name, surname, email,secret) VALUES (?,?,?,?,?,?)";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$login, $psw_hash, $meno, $priezvisko, $email, $secret]);
      session_start();
      $_SESSION['id']=$conn->lastInsertID();
      $_SESSION['secret'] = $secret;
      $_SESSION['username'] = $_POST['login'];
      $_SESSION['qa'] = $ga->getQRCodeGoogleUrl($websiteTitle, $secret);
      header('Location:log.php');
    }
  }
}
//echo $psw_hash;
/*
$sql = "INSERT INTO `user` (login,password,name, surname, email) VALUES (?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$login, $psw_hash, $meno, $priezvisko, $email]);
*/
?>

<!doctype html>
<html lang="en">

<head>
  <title>Title</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="style2.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body class="p-3 mb-2 bg-dark ">
  <div class="container">

    <form class="modal-content" action="register.php" method="post">
      <div class="container">
        <h1>Sign Up</h1>
        <p>Please fill in this form to create an account.</p>
        <hr>
        <label for="login"><b>Login</b></label>
        <input type="text" placeholder="Enter login" name="login" required autocomplete="off">
        <span class="error text-danger"> <?php echo $nameErr; ?></span>
        <br>
        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                ...
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
          </div>
        </div>
        <label for="psw" id="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" required autocomplete="off">

        <label for="meno"><b>Name</b></label>
        <input type="text" placeholder="Enter Name" name="name" required>


        <label for="priezvisko"><b>Surname</b></label>
        <input type="text" placeholder="Enter Surname" name="surname" required>

        <label for="email"><b>Email</b></label>
        <input type="text" placeholder="Enter Email" name="email" required>
        <span class="error text-danger"> <?php echo $emailErr; ?></span>
        <br><br>


        <button type="submit" class="signupbtn">Sign Up</button>

      </div>
      <div class="container">
                <span> <a href="login.php" class='btn btn-primary'>Back Log in</a></span>
            </div>
    </form>



  </div>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>