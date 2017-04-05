<?php
  require_once "includes/header.php";

  if (!isset($_SESSION['username']))
  {
    header("Location: register.php");
    exit;
  }

if (isset($_POST['register-team'])) 
{

  if (empty($_POST['team-name']) || empty($_POST['team-tag']) || empty($_POST['game-id']) || empty($_POST['czlonek1']) || empty($_POST['czlonek2']) || empty($_POST['czlonek3']) || empty($_POST['czlonek4']) || empty($_POST['czlonek5']))
  {// czy istnieja
    header("Location: createteam.php?errortd=1");
    exit;    
  }
  if($_POST['czlonek1']==$_SESSION['username'] || $_POST['czlonek2']==$_SESSION['username'] || $_POST['czlonek3']==$_SESSION['username'] || $_POST['czlonek4']==$_SESSION['username'] || $_POST['czlonek5']==$_SESSION['username'])
  {
    header("Location: createteam.php?errortd=8");
    exit;
  }

  $players_list = array(strtoupper($_POST['czlonek1']), strtoupper($_POST['czlonek2']), strtoupper($_POST['czlonek3']), strtoupper($_POST['czlonek4']), strtoupper($_POST['czlonek5']));
  if(count(array_unique($players_list)) != 5)
  {
    header("Location: createteam.php?errortd=13");
    exit;
  }

  $teamname       = trim($_POST['team-name']);
  $teamtag        = trim($_POST['team-tag']);
  $captain        = trim($_SESSION['username']);
  $game           = trim($_POST['game-id']);
  $czlonek1       = trim($_POST['czlonek1']);
  $czlonek2       = trim($_POST['czlonek2']);
  $czlonek3       = trim($_POST['czlonek3']);
  $czlonek4       = trim($_POST['czlonek4']);
  $czlonek5       = trim($_POST['czlonek5']);
  $akceptuje      = $_POST['akceptuje'];
  $teamtag_length = strlen($teamtag);
    
  if ($teamtag_length != 3)
  {
    header("Location: createteam.php?errortd=12");
    exit;
  }

  $captain_verify =  $db -> Prepare('SELECT * FROM teams where captain_name = :captain AND game = :game');
  $captain_verify -> bindParam(":captain", $captain, PDO::PARAM_STR);
  $captain_verify -> bindParam(":game", $game, PDO::PARAM_INT);
  $captain_verify -> execute();
  $captain_verify =  $captain_verify ->fetch(PDO::FETCH_ASSOC);
  if (!empty($captain_verify)) {
    header("Location: createteam.php?errortd=11");
    exit;   
  }
    
  $team =  $db -> Prepare('SELECT team_name FROM `teams` WHERE `team_name`=:teamname AND game=:game');
  $team -> bindParam(":teamname", $teamname, PDO::PARAM_STR);
  $team -> bindParam(":game", $game, PDO::PARAM_INT);
  $team -> Execute();
  $team =  $team ->fetch(PDO::FETCH_ASSOC);

  if (!empty($team))
  {
    header("Location: createteam.php?errortd=2");
    exit;
  }

  $tag =  $db -> Prepare('SELECT id FROM `teams` WHERE `team_tag`=:teamtag');
  $tag -> bindParam(":teamtag", $teamtag, PDO::PARAM_STR);
  $tag -> Execute();
  $tag =  $tag -> fetch(PDO::FETCH_ASSOC);

  if (!empty($tag))
  {
    header("Location: createteam.php?errortd=9");
    exit;
  }

  $existplayer1 =  $db -> Prepare('SELECT id FROM users WHERE login = :login');
  $existplayer1 -> bindParam(":login", $czlonek1, PDO::PARAM_STR);

  $existplayer2 =  $db -> Prepare ('SELECT id FROM users WHERE login = :login2');
  $existplayer2 -> bindParam(":login2", $czlonek2, PDO::PARAM_STR);

  $existplayer3 =  $db -> Prepare ('SELECT id FROM users WHERE login = :login3');
  $existplayer3 -> bindParam(":login3", $czlonek3, PDO::PARAM_STR);

  $existplayer4 =  $db -> Prepare ('SELECT id FROM users WHERE login = :login4');
  $existplayer4 -> bindParam(":login4", $czlonek4, PDO::PARAM_STR);
 
  $existplayer5 =  $db -> Prepare ('SELECT id FROM users WHERE login = :login5');
  $existplayer5 -> bindParam(":login5", $czlonek5, PDO::PARAM_STR);


  $existplayer1 -> Execute();
  $existplayer1 =  $existplayer1->fetch(PDO::FETCH_ASSOC);

  $existplayer2 -> Execute();
  $existplayer2 =  $existplayer2->fetch(PDO::FETCH_ASSOC);

  $existplayer3 -> Execute();
  $existplayer3 =  $existplayer3->fetch(PDO::FETCH_ASSOC);

  $existplayer4 -> Execute();
  $existplayer4 =  $existplayer4->fetch(PDO::FETCH_ASSOC);

  $existplayer5 -> Execute();
  $existplayer5 =  $existplayer5->fetch(PDO::FETCH_ASSOC);

  if (empty($existplayer1)) // jezeli  ktorys z uzytkownikow nie istnieje
  {
    header("Location: createteam.php?errortd=3");
    exit;
  }
  if (empty($existplayer2)) // jezeli  ktorys z uzytkownikow nie istnieje
  {
    header("Location: createteam.php?errortd=4");
    exit;
  }
  if (empty($existplayer3)) // jezeli  ktorys z uzytkownikow nie istnieje
  {
    header("Location: createteam.php?errortd=5");
    exit;
  }
  if (empty($existplayer4)) // jezeli  ktorys z uzytkownikow nie istnieje
  {
    header("Location: createteam.php?errortd=6");
    exit;
  }
  if (empty($existplayer5)) // jezeli  ktorys z uzytkownikow nie istnieje
  {
    header("Location: createteam.php?errortd=10");
    exit;
  }
  if (!$akceptuje)
  {
    header("Location: createteam.php?errortd=7");
    exit;
  }
  $date_of_create = date("Y-m-j");

  $add_team = $db -> Prepare('INSERT INTO `teams`(`team_name`, `team_tag`, `date_of_create`, `captain_name`, `player1_name`, `player2_name`, `player3_name`, `player4_name`, `player5_name`, `game`) VALUES (:name, :tag, :date_of_create, :captain, :player1, :player2, :player3, :player4, :player5, :game)'); // dodaj wpis do mysql

  $add_team -> bindParam(":name", $teamname, PDO::PARAM_STR);
  $add_team -> bindParam(":tag", $teamtag, PDO::PARAM_STR);
  $add_team -> bindParam(":date_of_create", $date_of_create, PDO::PARAM_STR);
  $add_team -> bindParam(":captain", $captain, PDO::PARAM_STR);
  $add_team -> bindParam(":player1", $czlonek1, PDO::PARAM_STR);
  $add_team -> bindParam(":player2", $czlonek2, PDO::PARAM_STR);
  $add_team -> bindParam(":player3", $czlonek3, PDO::PARAM_STR);
  $add_team -> bindParam(":player4", $czlonek4, PDO::PARAM_STR);
  $add_team -> bindParam(":player5", $czlonek5, PDO::PARAM_STR);
  $add_team -> bindParam(":game", $game, PDO::PARAM_INT);
  $add_team -> execute();

  header("Location: createteam.php?successtd=1");
  exit;
}

  echo '<div class="container">
    <h1>Zarejestruj swoją drużynę !</h1>
    <div class="wrap after">';

  if (isset($_GET['errortd'])) 
  {
    switch ($_GET['errortd']) 
    {
      case 1:
        echo '<h3 class="register-error">Uzupełnij wszystkie pola!</h3>';
        break;

      case 2:
        echo '<h3 class="register-error">Istnieje już drużyna o takiej nazwie do tej gry!</h3>';
        break;
      
      case 3:
        echo '<h3 class="register-error">Pierwszy członek twojej drużyny nie jest zarejestrowany w naszej bazie danych!</h3>';
        break;

      case 4:
        echo '<h3 class="register-error">Drugi członek twojej drużyny nie jest zarejestrowany w naszej bazie danych!</h3>';
        break;

      case 5:
        echo '<h3 class="register-error">Trzeci członek twojej drużyny nie jest zarejestrowany w naszej bazie danych!</h3>';
        break;
      
      case 6:
        echo '<h3 class="register-error">Czwarty członek twojej drużyny nie jest zarejestrowany w naszej bazie danych!</h3>';
        break;
      
      case 7:
        echo '<h3 class="register-error">Nie zaakceptowałeś regulaminu!</h3>';
        break;

      case 8:
        echo '<h3 class="register-error">Nie podawaj swojego loginu w polach na loginy graczy, zostaniesz dodany automatycznie jako kapitan drużyny.</h3>';
        break;

      case 9:
        echo '<h3 class="register-error">Istnieje już drużyna o takim TAGU!</h3>';
        break;

      case 10:
        echo '<h3 class="register-error">Piąty członek twojej drużyny nie jest zarejestrowany w naszej bazie danych!</h3>';
        break;

      case 11:
        echo '<h3 class="register-error">Jesteś już kapitanem innej drużyny w tej grze!</h3>';
        break;
      case 12:
        echo '<h3 class="register-error">TAG drużyny może posiadać tylko 3 znaki!</h3>';
        break;
      case 13:
        echo '<h3 class="register-error">Członkowie drużyny nie mogą się powtarzać!</h3>';
        break;

      default:
        echo "Coś poszło nie tak jak powinno!";
        break;
    }
  }

  if (isset($_GET['successtd'])) 
  {
    switch ($_GET['successtd']) 
    {
      case 1:
        echo '<h3 class="register-success">Poprawnie zarejestrowałeś drużyne !</h3>';
        break;
    }
  }

  echo '<form method="POST" action="" id="register_form">
          <div class="list">
            <label>Nazwa drużyny
              <span class="desc">( Pełna nazwa drużyny )</span>
            </label>
            <input class="text" maxlength="32" type="text" name="team-name" />
          </div>
          <div class="list">
            <label>TAG drużyny
              <span class="desc">( Skrócona nazwa drużyny 3 litery )</span>
            </label>
            <input class="text" maxlength="3" type="text" name="team-tag" />
          </div>
          <div class="list">
            <label>Gra
              <span class="desc">Do jakiej gry ma działać drużyna</span>
            </label>
            <select name="game-id" class="text">
              <option value="1">Counter Strike Global Offensive</option>
              <option value="2">League of Legends</option>
            </select>
          </div>
          <div class="list">
            <label>Członek 1:
              <span class="desc">( Login użytkownika zarejestrowanego na tej stronie )</span>
            </label>
            <input class="text" maxlength="32" type="text" name="czlonek1" />
          </div>
          <div class="list">
            <label>Członek 2:
              <span class="desc">( Login użytkownika zarejestrowanego na tej stronie )</span>
            </label>
            <input class="text" maxlength="32" type="text" name="czlonek2" />
          </div>
          <div class="list">
            <label>Członek 3:
              <span class="desc">( Login użytkownika zarejestrowanego na tej stronie )</span>
            </label>
            <input class="text" maxlength="32" type="text" name="czlonek3" />
          </div>
          <div class="list">
            <label>Członek 4:
              <span class="desc">( Login użytkownika zarejestrowanego na tej stronie )</span>
            </label>
            <input class="text" maxlength="32" type="text" name="czlonek4" />
          </div>
          <div class="list">
            <label>Członek 5:
              <span class="desc">( Login użytkownika zarejestrowanego na tej stronie )</span>
            </label>
            <input class="text" maxlength="32" type="text" name="czlonek5" />
          </div>
          <div class="list">
            <label class="rejestracja-label" for="checkbox-agree">Akcpetuje <a href="rules.php" target="_blank" class="special">regulamin</a> panujący w tym serwisie: *</label>
            <input type="checkbox" name="akceptuje" id="checkbox-agree" />
          </div>
        <input type="submit" class="button" value="Zarejestruj drużynę" name="register-team" />
        <input type="reset" class="button" value="Wyczyść Formularz" />
      </form>      
    </div>
  </div>';

  require_once "includes/footer.php";