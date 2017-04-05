<?php
  require_once "includes/header.php";

  $get_tour = (isset($_GET['g']) ? $_GET['g'] : 0);
?>

<div class="container">

  <?php 
  $tournament =  $db -> Prepare('SELECT * FROM tournament WHERE id = :id');
  $tournament -> bindParam(":id", $get_tour, PDO::PARAM_INT);
  $tournament -> Execute();
  $tournament =  $tournament -> fetch(PDO::FETCH_ASSOC);

  if ($tournament) {

    echo '<div class="wrap">
      <center>
          <h2>' . $tournament['name'] . '</h2>
          <p>' . tour_type($tournament['type']) . '</p>';

                switch ($tournament['state']) {
                  case 0:
                    # wyświetl zaakceptowane zgłoszenia
                    $team_name =  $db -> Prepare('SELECT team_name, team_tag FROM teams WHERE captain_name=:captain AND game = :game'); // Pobierz wszystkie rekordy gdzie login == login, INJECTION SQL
                    $team_name -> bindParam(":captain", $_SESSION['username'], PDO::PARAM_STR);
                    $team_name -> bindParam(":game", $tournament['game'], PDO::PARAM_INT);
                    $team_name -> Execute();
                    $team_name =  $team_name -> fetch(PDO::FETCH_ASSOC);

                    $count_team =  $db -> Prepare('SELECT count(id) as i FROM group_team WHERE tournament = :tournament AND accept = 1');
                    $count_team -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
                    $count_team -> execute();
                    $count_team =  $count_team -> fetch(PDO::FETCH_ASSOC);

                    if(isset($team_name['team_name'])) {
                      if (isset($_GET['error'])) 
                      {
                        switch ($_GET['error']) 
                        {
                          case 1:
                            echo '<h3 class="register-error">Akceptacja regulaminu obowiązkowa!</h3>';
                            break;
                          case 2:
                            echo '<h3 class="register-error">Maksymalna ilość drużyn w turnieju!</h3>';
                            break;

                          default:
                            echo '<h3 class="register-error">Coś poszło nie tak jak powinno!</h3>';
                            break;
                        }
                      }

                      if (isset($_GET['success']))
                      {
                        switch ($_GET['success']) 
                        {
                          case 1:
                            echo '<h3 class="register-success">Poprawnie zarejestrowałeś drużyne !</h3>';
                            break;
                        }
                      }

                      if (isset($_POST['save_team']))
                      {
                        $accept = (isset($_POST['accept']) ? true : false);
                        if (!$accept)
                        {
                          header('Location: tournament.php?g='.$get_tour.'&error=1');
                          exit;
                        }

                        if( $count_team['i'] >= 128 ) 
                        {
                          header('Location: tournament.php?g='.$get_tour.'&error=2');
                          exit;
                        }

                        if($tournament['type'] == 1) $st = 0;
                        else $st = 1;
                        $add_team =  $db -> Prepare("INSERT INTO `group_team`(`tournament`, `team_name`, `accept`) VALUES (:tour, :team_name, :accept)" ); // dodaj wpis do mysql
                        $add_team -> bindParam(":tour", $get_tour, PDO::PARAM_INT);
                        $add_team -> bindParam(":team_name", $team_name['team_name'], PDO::PARAM_STR);
                        $add_team -> bindParam(":accept", $st, PDO::PARAM_INT);
                        $add_team -> execute();
                        header('Location: tournament.php?g='.$get_tour.'&success=1');
                        exit;
                      }

                      echo 'Twoja drużyna to: <b>' . $team_name['team_name'] . ' ('. $team_name['team_tag'] .')</b><br />';
                      $team_play =  $db -> Prepare('SELECT accept FROM group_team WHERE team_name=:team_name AND tournament = :tournament'); // Pobierz wszystkie rekordy gdzie login == login, INJECTION SQL
                      $team_play -> bindParam(":team_name", $team_name['team_name'], PDO::PARAM_STR);
                      $team_play -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
                      $team_play -> Execute();
                      $team_play =  $team_play -> fetch(PDO::FETCH_ASSOC);

                      if ( !$team_play )
                      {
                        if ( $count_team['i'] < 128 )
                        {
                        echo '<form method="POST" action="tournament.php?g='.$get_tour.'">
                          <label class="rejestracja-label" for="checkbox-agree">Akcpetuje <a href="regulamin.php" target="_blank" class="special">regulamin</a> panujący w tym serwisie: *</label>
                          <input type="checkbox" name="accept" /><br />
                          <input type="submit" value="Zapisz drużynę" name="save_team" />
                        </form>';
                        }
                        else
                        {
                          echo '<small>W turnieju jest już maksysalna ilość drużyn</small>';
                        }
                      }
                      else
                      {
                        if ( $team_play['accept'] == 0 )
                        {
                          echo '<small>oczekuje na akceptację</small>';
                        }
                        else
                        {
                          echo '<small>bierzesz udział w turnieju</small>';
                        }
                      }

                    }
                    else echo '<h3><a href="createteam.php">Załóż drużynę</a>, aby dołączyć do turnieju!</h3>';
                    
                    echo '<hr />';
                    
                    $team_list =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = ' . $get_tour . ' AND accept = 1 Limit 128');
                    $team_list -> Execute();
                    $team_list =  $team_list -> fetchAll();

                    if ( !$team_list )
                    {
                      echo '<h3>Brak drużyn w turnieju</h3>';
                    }
                    else
                    {
                      echo '<ol>';
                      foreach ( $team_list as $row ) 
                      {
                        $team =  $db -> Prepare('SELECT team_tag FROM teams WHERE team_name = :team_name');
                        $team -> bindParam(":team_name", $row['team_name'], PDO::PARAM_STR);
                        $team -> Execute();
                        $team =  $team -> fetch(PDO::FETCH_ASSOC);

                        echo '<li>' . $row['team_name'] . ' (' . $team['team_tag'] . ')';
                      }
                      echo '</ol>';
                    }
                    break;
                  case 1:
                  case 2:
                  case 3:
                  default:
                    # wyświetlanie grup
                    echo 'Rozgrywki grupowe';
                    if($tournament['state'] > 1) echo ' | <a href="finalcup.php?g=' . $get_tour . '">Faza pucharowa</a>';
                    echo '<hr>';

                    echo '
                    <div id="tabs">
                      <ul class="tabs-list">';

                        if( $tournament['int_group'] >= 6 )
                          echo '<li><a href="#tabs-1">A</a></li>
                                <li><a href="#tabs-2">B</a></li>
                                <li><a href="#tabs-3">C</a></li>
                                <li><a href="#tabs-4">D</a></li>
                                <li><a href="#tabs-5">E</a></li>
                                <li><a href="#tabs-6">F</a></li>';
                        if( $tournament['int_group'] >= 7 )
                          echo '<li><a href="#tabs-7">G</a></li>';
                        if( $tournament['int_group'] >= 8 )
                          echo '<li><a href="#tabs-8">H</a></li>';
                        if( $tournament['int_group'] >= 12 )
                          echo '<li><a href="#tabs-9">I</a></li>
                                <li><a href="#tabs-10">J</a></li>
                                <li><a href="#tabs-11">K</a></li>
                                <li><a href="#tabs-12">L</a></li>';
                        if( $tournament['int_group'] >= 13 )
                          echo '<li><a href="#tabs-13">M</a></li>';
                        if( $tournament['int_group'] >= 14 )
                          echo '<li><a href="#tabs-14">N</a></li>';
                        if( $tournament['int_group'] >= 16 )
                          echo '<li><a href="#tabs-15">O</a></li>
                                <li><a href="#tabs-16">P</a></li>';

                      echo '</ul>';

                        for ($i=1; $i <= $tournament['int_group']; $i++)
                        {
                          $gr_team =  $db -> Prepare('SELECT * FROM `group_team` WHERE tournament = :tournament AND group_id = :group_id ORDER BY `W` DESC, `P` DESC,`GD` DESC');
                          $gr_team -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
                          $gr_team -> bindParam(":group_id", $i, PDO::PARAM_INT);
                          $gr_team -> Execute();
                          $gr_team =  $gr_team -> fetchAll();

                          echo '<div id="tabs-' . $i . '">
                          <table>
                              <tr style="vertical-align: top;">
                                  <td width="45%">
                                      <table class="team_table">
                                      <tr><th>Drużyna</th><th title="Mecze">M</th><th title="Wygrane">W</th><th title="Przegrane">P</th><th title="statystyka rund">GD</th>';

                          foreach ($gr_team as $row) {

                            $tm_id =  $db -> Prepare('SELECT id FROM teams WHERE team_name = :name');
                            $tm_id -> bindParam(":name", $row['team_name'], PDO::PARAM_STR);
                            $tm_id -> Execute();
                            $tm_id =  $tm_id -> fetch(PDO::FETCH_ASSOC);

                            echo '<tr>
                              <td class="team_td"><a href="teams.php?t=' . $tm_id['id'] . '">' . $row['team_name'] . '</a></td>
                              <td class="team_td">' . ( $row['W'] + $row['P'] ) . '</td>
                              <td class="team_td">' . $row['W'] . '</td>
                              <td class="team_td">' . $row['P'] . '</td>
                              <td class="team_td">' . $row['GD'] . '</td>
                            </tr>';
                          }

                          echo '</table>
                          </td>
                          <td>
                            <table class="match_table">
                              <tr><th>DATA</th><th>DRUŻYNA 1</th><th>P1</th><th>P2</th><th>DRUŻYNA 2</th><th>WYGRYWA</th></tr>';
                            $gr_match =  $db -> Prepare('SELECT * FROM `matches` WHERE tournament = :tournament AND group_id = :group_id ORDER BY `date` DESC');
                            $gr_match -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
                            $gr_match -> bindParam(":group_id", $i, PDO::PARAM_INT);
                            $gr_match -> Execute();
                            $gr_match =  $gr_match -> fetchAll();
                            if(empty($gr_match)) echo '<tr><td>X</td><td colspan="4">brak meczy</td><td>X</td></tr>';
                            else
                            {
                              foreach ($gr_match as $mtch)
                              {
                                echo '<tr>
                                  <td class="team_td">' . $mtch['date'] . '</td>
                                  <td class="team_td">';
                                if( $mtch['score1'] > $mtch['score2'] ) echo '<b>'.$mtch['team_name_1'].'</b>';
                                else echo $mtch['team_name_1'];
                                  echo '</td>
                                  <td class="team_td">' . $mtch['score1'] . '</td>
                                  <td class="team_td">' . $mtch['score2'] . '</td>
                                  <td class="team_td">';
                                if( $mtch['score2'] > $mtch['score1'] ) echo '<b>'.$mtch['team_name_2'].'</b>';
                                else echo $mtch['team_name_2'];
                                  echo '</td>
                                  <td class="team_td">';
                                if( $mtch['score1'] == 0 AND $mtch['score2'] == 0 ) echo '<small>nie rozegrano</small>';
                                else if( $mtch['score1'] > $mtch['score2'] ) echo $mtch['team_name_1'];
                                else echo $mtch['team_name_2'];
                                echo '</td></tr>';
                              }
                            }
                            echo '</table>
                                  </td>
                                </tr>
                              </table>
                            </div>';
                        }

                    echo '</div>';

                    # ostatnie mecze
                    break;
                  }

      echo '</center>
      <br />
    </div>';

  }
  else
  {
    $tournament =  false;
    $tournament =  $db -> Prepare('SELECT id, name, game, state, type FROM tournament');
    $tournament -> bindParam(":id", $get_tour, PDO::PARAM_INT);
    $tournament -> Execute();
    $tournament =  $tournament -> fetchAll();

    if (!$tournament)
    {
      echo '<h1 class="newsy-h1">Game league</h1>
        <hr style="clear:both;" />
        <div class="wrap"><h3>Brak lig do wyświetlenia :(</h3></div>';
    }
    else
    {
      echo '<h1 class="newsy-h1">Lista turniejów</h1>
      <div class="wrap">
      <table id="tour_list">
        <tr><th>Nazwa</th><th>Typ Gry</th><th>Status</th><th>Rodzaj turnieju</th></tr>';
        foreach ($tournament as $simple)
        {
          switch ( $simple['game'] )
          {
            case 1:
              $game_name = 'Counter Strike Global Offensive';
              break;
            
            case 2:
              $game_name = 'League of Legends';
              break;
          }
            echo '<tr><td><a href="tournament.php?g=' . $simple['id'] . '" style="font-weight:bold;">' . $simple['name'] . '</a></td><td>' . $game_name .'</td><td>' . tour_status($simple['state']) . '</td><td>' . tour_type($simple['type']) . '</td></tr>';
        }
      echo '</table></div>';
    }
  }

echo '</div>';
?>

<style>
table#tour_list {
  width:100%;
  margin:auto;
}
table#tour_list th {
  width: 25%;
  height: 30px;
  line-height: 30px;
  font-size: 16px;
}
table#tour_list td {
  height: 30px;
  line-height: 30px;
  font-size: 14px;
  text-align: center;
}
table#tour_list tr:nth-child(odd) {
  /*background-color: #F1F1F1; */
  background-color: #024349;
}
.ui-widget-content {
  border: 1px solid #0b4c52;
  background: #0b4c52;
}
.ui-widget.ui-widget-content {
  border: 1px solid #014248;
}
li.ui-tabs-active, li.ui-state-active {
  background-color: #052e32;
}
.ui-tabs .ui-tabs-nav li {
  list-style: none;
  float: left;
  position: relative;
  border: 2px solid #014248;
  top: 0;
  margin: 1px .2em 0 0;
  border-bottom-width: 0;
  padding: 5px;
  white-space: nowrap;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
}
.ui-tabs .ui-tabs-nav .ui-tabs-anchor {
  float: left;
  padding: .5em 1em;
  text-decoration: none;
}
.ui-widget-header {
  border: 1px solid #014248;
  height: 47px;
  background-color: #0e484e;
  font-weight: bold;
}
.ui-tabs .ui-tabs-nav {
  margin: 0;
  padding: .2em .2em 0;
}

</style>

<?php
  function tour_type($st)
  {
    switch ($st) {
      case 0:
        return 'Otwarty';
        break;
      case 1:
        return 'Płatny';
        break;
    }
  }
  function tour_status($st)
  {
    switch ($st) {
      case 0:
        return 'Zbieranie uczestników';
        break;
      case 1:
        return 'Część grupowa';
        break;
      case 2:
        return 'Część finałowa';
        break;
      case 3:
        return 'Zakończony';
        break;
      default:
        return 'Brak danych';
        break;
    }
  }

  require_once "includes/footer.php"; 

/*
 * @druzyny @grupy
 * 32-36    6
 * 37-42    7
 * 43-64    8
 * 65-72    12
 * 73-78    13
 * 79-88    14
 * 89-128   16
 */