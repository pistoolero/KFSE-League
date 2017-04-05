<?php
session_start();
require_once "includes/database.php";

if(isset($_POST['login']))
{

  if (empty($_POST['username_login']) || empty($_POST['password_login'])) {
    header("Location: index.php?error=0&#login_form");
    exit;
  }

  $username = $_POST['username_login'];
  $password = $_POST['password_login'];

  $user =  $db -> Prepare('SELECT * FROM `users` WHERE `login`=:username');
  $user -> bindParam(":username", $username, PDO::PARAM_STR);
  $user -> Execute();
  $user =  $user -> fetch(PDO::FETCH_ASSOC);

  if (empty($user)) // czy istnieje uzytkownik
  {
    header("Location: index.php?error=3#login_form");
    exit;
  }

  if (!password_verify($password, $user['password']))// czy haslo jest poprawne
  {
    header("Location: index.php?error=4#login_form");
    exit;
  }

  $_SESSION['username'] = $user['login'];
  header("Location: index.php");
  exit;
}

if (isset($_GET['logout']))
{
  $_SESSION['username'] = '';
  session_unset();
  session_destroy();
  header("Location: index.php?success=2#login_form");
  exit;
}


if (isset($_POST['register'])) // sprawdza czy istnieje formularz
{

  if(empty($_POST['username_register']) || empty($_POST['password_register']) || empty($_POST['password_register_replace']) || empty($_POST['steamid']) || empty($_POST['email']) || empty($_POST['email_replace'])  || $_POST['g-recaptcha-response'] == "") //sprawdza czy istnieja puste pola
  {
    header("Location: register.php?error=0");
    exit;
  }

  $secret = '6Le23hkUAAAAAKi0DfEGAhPR_0MwL79uOAFYaw7P';
  $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
  $responseData = json_decode($verifyResponse);
  if($responseData->success)
  {

    $username         = trim($_POST['username_register']);
    $password         = password_hash($_POST['password_register'], PASSWORD_DEFAULT);
    $password_replace = $_POST['password_register_replace'];
    $steam_id         = trim($_POST['steamid']);
    $email            = trim($_POST['email']);
    $email_replace    = trim($_POST['email_replace']);
    $accept           = $_POST['accept'];
      
    if (!password_verify($password_replace, $password)) // sprawdza czy hasla sa takie same
    {
      header("Location: register.php?error=1");
      exit;
    }
    if ($email != $email_replace)
    {
      header("Location: register.php?error=4");
      exit;
    }

    $check = '/^[a-zA-Z0-9.\-_]+@[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,4}$/';
    if (!preg_match($check, $email)) 
    {
      header("Location: register.php?error=5");
      exit;
    }

    $user =  $db -> Prepare('SELECT `login` FROM `users` WHERE `login`=:username');
    $user -> bindParam(":username", $username, PDO::PARAM_STR);
    $user -> Execute(); 
    $user =  $user -> fetch(PDO::FETCH_ASSOC);

    if (!empty($user))
    {
      header("Location: register.php?error=2");
      exit;
    }
    if (!$accept)
    {
      header("Location: register.php?error=3");
      exit;
    }
      
    $brak= "brak";
    $date_of_entry = date("Y-m-j");

    $ip = $_SERVER['REMOTE_ADDR'];

    $add_user =  $db -> Prepare("INSERT INTO `users`(`login`, `password`, `email`, `ip`, `steamid`, `date_of_entry`) VALUES (:username, :password, :email, :ip, :steamid, :date_of_entry)"); // dodaj wpis do mysql
    $add_user -> bindParam(":username", $username, PDO::PARAM_STR);
    $add_user -> bindParam(":password", $password, PDO::PARAM_STR);
    $add_user -> bindParam(":email", $email, PDO::PARAM_STR);
    $add_user -> bindParam(":ip", $ip, PDO::PARAM_STR);
    $add_user -> bindParam(":steamid", $steam_id, PDO::PARAM_STR);
    $add_user -> bindParam(":date_of_entry", $date_of_entry, PDO::PARAM_STR);
    $add_user -> Execute();

    header("Location: register.php?success=0");
    exit;
  }
  else
  {
    header("Location: register.php?error=6");
    exit;
  }
}

if (!isset($_POST['login']))
{
  header("Location: index.php");
  exit;
}