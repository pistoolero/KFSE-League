<?php
  ob_start();
  session_start();
  require_once __DIR__ . "/../../includes/database.php";

  $admincheck =  $db -> Prepare('SELECT admin FROM users WHERE login= :login');
  $admincheck -> bindParam(":login", $_SESSION['username'], PDO::PARAM_STR);
  $admincheck -> execute();
  $admincheck =  $admincheck -> fetch(PDO::FETCH_ASSOC);

  $checkadmin = $admincheck['admin'];
  if ($checkadmin <= 0)
  {
    header("Location: ../index.php");
    exit;
    ob_end_flush();
  }
  $admin =  $db -> Prepare('SELECT * FROM users WHERE login= :login');
  $admin -> bindParam(":login", $_SESSION['username'], PDO::PARAM_STR);
  $admin -> execute();
  $admin =  $admin -> fetch(PDO::FETCH_ASSOC);

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
?>

<!DOCTYPE html>
<html>
<head>
  <!--Import Google Icon Font-->
  <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <title>Cocpit KFSE</title>
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>

  <header>
    <div class="navbar-fixed">
      <nav>
        <div class="nav-wrapper">
          <a href="./" class="brand-logo">KFSE</a>
          <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
        </div>
      </nav>
    </div>

    <ul id="nav-mobile" class="side-nav fixed">
      <li>
        <div class="userView">
          <div class="background">
            <img src="assets/img/office.jpg" style="filter: invert(100%)" />
          </div>
          <a href="#!user"><img class="circle materialboxed" src="<?php echo get_gravatar($admin['email'], 512); ?>" data-caption="<?php echo $admin['login']; ?> avatar" /></a>
          <a href="#!name"><span class="white-text name"><?php echo $admin['login']; ?></span></a>
          <a href="#!email"><span class="white-text email"><?php echo $admin['email']; ?></span></a>
        </div>
      </li>
      <li>
        <a href="index.php" class="white-text"><i class="material-icons">web</i>Dashboard</a>
      </li>
      <li>
        <a href="users.php" class="white-text"><i class="material-icons">perm_identity</i>Użytkownicy</a>
      </li>
      <li>
        <a href="tournaments.php" class="white-text"><i class="material-icons">group_work</i>Turnieje</a>
      </li>
      <li>
        <ul class="collapsible" data-collapsible="accordion">
          <li>
            <div class="collapsible-header"><i class="material-icons">message</i>Newsy</div>
            <div class="collapsible-body">
              <ul>
                <l><a href="addnews.php" class="white-text">Dodaj Newsa</a></l>
                <l><a href="editnews.php" class="white-text">Edytuj Newsa</a></l>
              </ul>
            </div>
          </li>
          <li>
            <div class="collapsible-header"><i class="material-icons">supervisor_account</i>Drużyny</div>
            <div class="collapsible-body">
              <ul>
                <l><a href="#" class="white-text">Spis wszystkich drużyn</a></l>
                <l><a href="#" class="white-text">Spis zapisanych drużyn</a></l>
                <l><a href="#" class="white-text">Spis oczekujących drużyn</a></l>
              </ul>
            </div>
          </li>
        </ul>
      </li>            
      <li><div class="divider"></div></li>
      <li><a class="subheader">Opcje</a></li>
      <li><a class="waves-effect" href="login.php?logout">Wyloguj się</a></li>
    </ul>
  </header>

  <main>
    <div class="container">