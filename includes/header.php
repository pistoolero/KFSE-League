<?php
  ob_start();
  session_start();

function getTime() { 
  list($usec, $sec) = explode(" ", microtime()); 
  return ((float)$usec + (float)$sec); 
} 
$startTime = getTime();

  require_once "database.php";

  $teams =  $db -> Prepare('SELECT COUNT(game) AS game FROM `teams` GROUP BY game');
  $teams -> execute();
  $teams =  $teams -> fetchAll(PDO::FETCH_ASSOC);

  $tour =  $db -> Prepare('SELECT COUNT(id) AS count FROM `tournament`');
  $tour -> execute();
  $tour =  $tour -> fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
  <title>KFSE League</title>
  <link rel="stylesheet" href="css/normalize.min.css" type="text/css" />
  <link rel="stylesheet" href="css/style.css" />
  <link async href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css' />
  <link async href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'  />
  
  <link rel="shortcut icon" href='img/favicon.png' />

  <?php
    if(!( $_SESSION['username'] ))
      echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
  ?>
</head>
<body>

<nav id="top">
  <div class="container">
    <ul class="topmenu navmenu">
      <li><a href="index.php" title="Strona główna">Strona główna</a></li>
      <li><a href="#nowhere" title="Forum">Forum</a></li>
      <li><a href="teams.php" title="Lista drużyn">Drużyny (<?php echo $teams[0]['game'] + $teams[1]['game']; ?>)</a></li>
      <li><a href="tournament.php" title="Turnieje">Turnieje (<?php echo $tour['count']; ?>)</a></li>
      <li><a href="rules.php" title="Regulamin turnieju">Regulamin</a></li>
      <li><a href="#nowhere" title="">Pomoc</a></li>
    </ul>

<?php 
  /**
   * Get either a Gravatar URL or complete image tag for a specified email address.
   *
   * @param string $email The email address
   * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
   * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
   * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
   * @param boole $img True to return a complete IMG tag False for just the URL
   * @param array $atts Optional, additional key/value attributes to include in the IMG tag
   * @return String containing either just a URL or a complete image tag
   * @source https://gravatar.com/site/implement/images/php/
   */
  function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url  = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
      $url = '<img src="' . $url . '"';
      foreach ( $atts as $key => $val )
        $url .= ' ' . $key . '="' . $val . '"';
      $url .= ' />';
    }
    return $url;
  }

  if(isset( $_SESSION['username'] ))
  {
    $user =  $db -> Prepare('SELECT admin, email FROM `users` WHERE `login`=:username');
    $user -> bindParam(":username", $_SESSION['username'], PDO::PARAM_STR);
    $user -> Execute(); 
    $user =  $user -> fetch(PDO::FETCH_ASSOC);
    ?>
    <ul class="topmenu user-menu">
      <li class="menu-user-li"><a href="profil.php?user=<?php echo $_SESSION['username']; ?>" class="menu-user-li-a"><?php echo $_SESSION['username']; ?></a></li>
      <li class="menu-user-li"><a href="editprofile.php" class="menu-user-li-a">Edytuj profil</a></li>
      <li class="menu-user-li"><a href="createteam.php" class="menu-user-li-a">Stwórz drużyne</a></li>
      <?php 
      if($user['admin']>0)
        echo '<li class="menu-user-li"><a target="_blank" href="./admin" class="menu-user-li-a">Panel Admina</a></li>';
      ?>
      <li class="menu-user-li"><a href="login.php?logout" class="menu-user-li-a">wyloguj się</a></li>
    </ul>
    <img class="user-avatar-top" src="<?php echo get_gravatar($user['email'], 80); ?>" alt="avatar" />

 
 <?php
  }
  else
  {
    echo '<ul class="topmenu user-menu"><li><a href="#login_form" class="special">Zaloguj</a> się lub <a class="special" href="register.php">zarejestruj</a> jeśli jeszcze nie masz konta.</li></ul>';
  }
?>


  </div>
</nav>


<header id="header">
  <div class="container">
    <div class="flex-row">
      <div class="column text-left">
        <img src="img/logo-top.png" alt="KFSE.PL" />
      </div>
      <div class="column text-right">
        <table class="next-tour">
          <tr>
            <td><h3 class="tour">ZDZ Rush Tournament Ciechanów już wkrótce!</h3></td>
            <td><img src="img/lol-game-header.png" alt="LOL" /></td>
          </tr>
          <tr>
            <td><h2 class="save">Zapisz się!</h2></td>
            <td><img src="img/cs-game-header.png" alt="CS:GO" /></td>
          </tr>
        </table>
        
        <div class="partners">
          <img class="partner" src="img/partners/header/ae.png"  alt="CS:GO" />
          <img class="partner" src="img/partners/header/piast.png" alt="CS:GO" />
          <img class="partner" src="img/partners/header/ae.png"  alt="CS:GO" />
          <img class="partner" src="img/partners/header/piast.png" alt="CS:GO" />
          <img class="partner" src="img/partners/header/ae.png"  alt="CS:GO" />
          <img class="partner" src="img/partners/header/piast.png" alt="CS:GO" />
          <img class="partner" src="img/partners/header/ae.png"  alt="CS:GO" />
          <img class="partner" src="img/partners/header/piast.png" alt="CS:GO" />
        </div>
      </div>
    </div>
  </div>
</header>



<a href="#x" class="overlay" id="login_form"></a>
<div class="popup">
<?php 
if (isset($_GET['success'])) 
{
  switch ($_GET['success']) 
  {
    case 2:
      echo '<p class="success-logout">Pomyślnie wylogowałeś się!</p>';
      break;
  }
}
if (isset($_GET['error'])) 
{
  switch ($_GET['error']) 
  {
    case 0:
      echo '<p class="error-logout">Uzupełnij wszystkie pola!</p>';
      break;
    case 3:
      echo '<p class="error-logout">Nie ma takiego użytkownika!</p>';
      break;
    case 4:
      echo '<p class="error-logout">Podane hasło jest niepoprawne!</p>';
      break;
  }
}
?>
  <form action="login.php" method="POST">
    <div>
      <label for="login">Login</label>
      <input type="text" name="username_login" id="login" value="" />
    </div>
    <div>
      <label for="password">Hasło</label>
      <input type="password" name="password_login" id="password" value="" />
    </div>
    <input type="submit" name="login" value="Zaloguj" />
  </form>

  <a class="close" href="#close"></a></div>

</div>