<?php
  require_once "includes/header.php";

  if(!isset($_SESSION['username']))
  {
    header("Location: index.php");
    exit;
  }

  if (isset($_POST['change_password']))
  {
    if (empty($_POST['old_password']) || empty($_POST['new_password']) || empty($_POST['new_password_replace'])) 
    {
      header("Location: editprofile.php?errorp=0");
      exit;
    }

    $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $password_replace = $_POST['new_password_replace'];

    if (!password_verify($password_replace, $password)) // sprawdza czy hasla sa takie same
    {
      header("Location: editprofile.php?errorp=1");
      exit;
    }

    $check_password =  $db -> Prepare('SELECT password FROM users WHERE login= :login'); // dodaj wpis do mysql
    $check_password -> bindParam(":login", $_SESSION['username'], PDO::PARAM_STR);
    $check_password -> execute();
    $check_password =  $check_password -> fetch(PDO::FETCH_ASSOC);
    
    if (!password_verify($_POST['old_password'], $check_password['password'])) // sprawdza czy hasla sa takie same
    {
      header("Location: editprofile.php?errorp=2");
      exit;
    }

    $change_password =  $db -> Prepare('UPDATE users SET password = :password WHERE login = :login');
    $change_password -> bindParam(":password", $password, PDO::PARAM_STR);
    $change_password -> bindParam(":login", $_SESSION['username'], PDO::PARAM_STR);
    $change_password -> execute();

    header("Location: editprofile.php?successp=0");
    exit;

  }

  if (isset($_POST['change_description']))
  {
    if (empty($_POST['check_password']) || empty($_POST['new_description']) ) 
    {
      header("Location: editprofile.php?errorp=0");
      exit;
    }

    $check_password =  $db -> Prepare('SELECT password FROM users WHERE login= :login'); // dodaj wpis do mysql
    $check_password -> bindParam(":login", $_SESSION['username'], PDO::PARAM_STR);
    $check_password -> execute();
    $check_password =  $check_password -> fetch(PDO::FETCH_ASSOC);
    
    if (!password_verify($_POST['check_password'], $check_password['password'])) // sprawdza czy hasla sa takie same
    {
      header("Location: editprofile.php?errorp=2");
      exit;
    }

    $description = trim($_POST['new_description']);

    $set_new_description =  $db -> Prepare('UPDATE users SET description = :description WHERE login = :login');
    $set_new_description -> bindParam(":description", $description, PDO::PARAM_STR);
    $set_new_description -> bindParam(":login", $_SESSION['username'], PDO::PARAM_STR);
    $set_new_description -> execute();

    header("Location: editprofile.php?successp=1");
    exit;

  }


  echo '<div class="container">
    <h1>Zmiana Hasła</h1>
    <div class="wrap after">'; 

  if (isset($_GET['errorp'])) 
  {
    switch ($_GET['errorp']) 
    {
      case 0:
        echo '<h3 class="register-error">Uzupełnij wszystkie pola!</h3>';
        break;

      case 1:
        echo '<h3 class="register-error">Podane hasła nie są identyczne!</h3>';
        break;
      
      case 2:
        echo '<h3 class="register-error">Podane hasło jest błędne!</h3>';
        break;

      case 3:
        echo '<h3 class="register-error">Nie zaakceptowałeś regulaminu!</h3>';
        break;

      case 4:
        echo '<h3 class="register-error">Podane emaile nie są identyczne!</h3>';
        break;

      default:
        echo "Coś poszło nie tak jak powinno!";
        break;
    }
  }

  if (isset($_GET['successp'])) 
  {
    switch ($_GET['successp']) 
    {
      case 0:
        echo '<h3 class="register-success">Poprawnie zmieniłeś hasło!</h3>';
        break;
  
      case 1:
        echo '<h3 class="register-success">Poprawnie zmieniłeś opis!</h3>';
        break;
    }
  }

  echo '<form method="post" action="editprofile.php" id="register_form">
      <div class="list">
        <label>Stare hasło:</label>
        <input class="text" maxlength="32" type="text" name="old_password" />
      </div>
      <div class="list">
        <label>Nowe Hasło:</label>
        <input class="text" maxlength="32" type="password" name="new_password" />
      </div>
      <div class="list">
        <label>Nowe hasło (ponownie):</label>
        <input class="text" maxlength="32" type="password" name="new_password_replace" />
      </div>
      <input type="submit" class="button" value="Zmień hasło" name="change_password">
      <input type="reset" class="button" value="Wyczyść Formularz">
    </form>
  </div>

  <h1>Zmień opis</h1>
  <div class="wrap after">  
  <form action="editprofile.php" method="POST" id="register_form">
    <div class="list">
      <label>Obecne hasło</label>
      <input class="text" maxlength="32" type="password" name="check_password" />
    </div>
      <div class="list">
        <label>Opis</label>';

          $select_desc =  $db -> Prepare('SELECT description FROM users WHERE login= :login'); // dodaj wpis do mysql
          $select_desc -> bindParam(":login", $_SESSION['username'], PDO::PARAM_STR);
          $select_desc -> execute();
          $select_desc =  $select_desc -> fetch(PDO::FETCH_ASSOC);

          echo '<textarea maxlength="255" name="new_description">' . $select_desc['description'] . '</textarea>               
      </div>
    <input type="submit" class="button" value="Zmień opis" name="change_description">
  </form>
  </div>
 </div>';
 
  require_once "includes/footer.php"; 