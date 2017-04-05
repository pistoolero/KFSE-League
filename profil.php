<?php 
  require_once "includes/header.php";

  if (!isset($_GET['user'])) 
  {
    header('Location: ./index.php');
    exit;
  }

  $user   =  $_GET['user'];
  $player =  $db -> Prepare('SELECT * FROM users WHERE login= :login');
  $player -> bindParam(":login", $user, PDO::PARAM_STR);
  $player -> Execute();
  $player =  $player -> fetch(PDO::FETCH_ASSOC);

  if (empty($player))
  {
    header('Location: ./index.php');
    exit;     
  }

  echo '<div class="container">
    <h1>Profil ' . $player['login'] . '</h1>
    <div class="wrap profil-all">
      <img class="profil-user-image" src="' . get_gravatar($player['email'], 160) . '" align="left" />
      <div class="profil-content">
        <h2 class="profil-h2">
          ' . $player['login'] . '
        </h2>
        <ul class="profil-ul">
          <li class="profil-content-li">
            <p class="profil-dolaczyl">Dołączył ' . $player['date_of_entry'] . '</p>
            <h3 class="profil-h3">Opis:</h3>
            <p class="profil-opis">';

              if(!empty($player['description']))
                echo $player['description'];
              else echo "---";

          echo '</p>
          </li>
          <li class="profil-content-li">
            <h3 class="profil-h3">Data urodzenia:</h3>
            <p class="profil-data profil-li">';

              if(isset($player['date_of_birth']))
              {
                $age = time() - strtotime($player['date_of_birth']);
                echo $player['date_of_birth'] . ' (' . floor($age / (365 * 24 * 60 * 60)) . ')';
              }
              else echo "---";

          echo '</p>
            </li>
            <li class="profil-content-li">
              <h3 class="profil-h3">Drużyna:</h3>
              <p class="profil-data profil-li">';

              $teams =  $db -> Prepare('SELECT team_name, team_tag, game FROM teams WHERE captain_name = :c OR player1_name = :c OR player2_name = :c OR player3_name = :c OR player4_name = :c OR player5_name = :c');
              $teams -> bindParam(":c", $player['login'], PDO::PARAM_STR);
              $teams -> Execute();
              $teams =  $teams-> fetchAll(PDO::FETCH_ASSOC);

              if(!$teams) echo '---';
              else
                foreach ($teams as $team)
                {
                  echo $team['team_name'] . ' [' . $team['team_tag'] . ']' . ' (' . get_game($team['game']) . ')</br>';
                }

            echo '</p>
          </li>
          <li class="profil-content-li">
            <h3 class="profil-h3">Profil Steam:</h3>
            <p class="profil-data profil-li">http://steamcommunity.com/id/' . $player['steamid'] . '</p>
          </li>
        </ul>
      </div>
    </div>
  </div>';

  function get_game($game)
  {
    switch ($game) {
        case 1: return 'CS:GO'; break;
        case 2: return 'LoL'; break;
    }
  }

  require_once "includes/footer.php";