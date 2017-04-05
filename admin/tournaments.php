<?php
  require_once "includes/header.php";

  if (isset($_POST['add_tour'])) 
  {
    if (empty($_POST['tour_name']) || empty($_POST['game-id']))
    {
      header("Location: tournaments.php?error=1");
      exit;  
    }
    $tour_verify =  $db -> Prepare('SELECT * FROM tournament WHERE name = :name AND game = :game');
    $tour_verify -> bindParam(":name", $_POST['tour_name'], PDO::PARAM_STR);
    $tour_verify -> bindParam(":game", $_POST['game-id'], PDO::PARAM_INT);
    $tour_verify -> execute();
    $tour_verify =  $tour_verify ->fetch(PDO::FETCH_ASSOC);
    if (!empty($captain_verify)) {
      header("Location: tournaments.php?error=2");
      exit;   
    }

    if ($_POST['open'] == 'on') $open = 1;
    else $open = 0;

    $add_tour =  $db -> Prepare('INSERT INTO `tournament`(`name`, `game`, `type`) VALUES (:name, :game, :type)');
    $add_tour -> bindParam(":name", $_POST['tour_name'], PDO::PARAM_STR);
    $add_tour -> bindParam(":game", $_POST['game-id'], PDO::PARAM_INT);
    $add_tour -> bindParam(":type", $open, PDO::PARAM_INT);
    $add_tour -> execute();

    header("Location: tournaments.php?success=1");
    exit;
  }

  $get_tour = (isset($_GET['g']) ? $_GET['g'] : 0);
 
  $tournament =  $db -> Prepare('SELECT * FROM tournament WHERE id = :id');
  $tournament -> bindParam(":id", $get_tour, PDO::PARAM_INT);
  $tournament -> Execute();
  $tournament =  $tournament -> fetch(PDO::FETCH_ASSOC);

  if ($tournament) {
    echo '<h1 class="name">' . $tournament['name'] . ' <small>' . tour_status($tournament['state']) . '</small></h1>';
  
  /* ZBIERANIE DRUŻYN BEGIN */
  if($tournament['state'] == 0) {

    if (isset($_GET['delete']))
    {
      $id = $_GET['delete'];

      $tournament = 0;
      $usun =  $db -> Prepare('DELETE FROM `group_team` WHERE id = :id AND tournament = :tournament');
      $usun -> bindParam(":id", $id, PDO::PARAM_INT);
      $usun -> bindParam(":tournament", $get_tour, PDO::PARAM_STR);
      $usun -> execute();

      header('Location: tournaments.php?g=' . $get_tour);
      ob_end_flush();
      exit();
    }
    if (isset($_GET['accept']))
    {
      $id = $_GET['accept'];
      $acc_team =  $db -> Prepare('UPDATE `group_team` SET accept = 1 WHERE id = :id AND tournament = :tournament');
      $acc_team -> bindParam(":id", $id, PDO::PARAM_INT);
      $acc_team -> bindParam(":tournament", $get_tour, PDO::PARAM_STR);
      $acc_team -> execute();

      header('Location: tournaments.php?g=' . $get_tour);
      ob_end_flush();
      exit();
    }

      $count_team =  $db -> Prepare('SELECT count(id) as i FROM group_team WHERE tournament = :tournament AND accept = 1');
      $count_team -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
      $count_team -> execute();
      $count_team =  $count_team -> fetch(PDO::FETCH_ASSOC);
      echo 'Klubów w turnieju ' . $count_team['i'] . ' <small>32 - 128 potrzebnych do wylosowania grup</small><br />';

      if($count_team['i'] >= 32 ) 
      {
        if(isset($_GET['rand'])) // losowanie grup dla drużyn
        {
          $r_team =  $db -> Prepare('SELECT * FROM group_team WHERE group_id = 0 AND tournament = :tournament AND accept = 1 ORDER BY RAND()');
          $r_team -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
          $r_team -> execute();
          $r_team =  $r_team -> fetchAll();

          $group_number = 1;
          $max_group = int_group( $count_team['i'] );

          foreach ($r_team as $row) {
            $team_update =  $db -> Prepare('UPDATE `group_team` SET group_id = :g_id WHERE id = :id');
            $team_update -> bindParam(":g_id", $group_number, PDO::PARAM_INT);
            $team_update -> bindParam(":id", $row['id'], PDO::PARAM_INT);
            $team_update -> Execute();
            if ($group_number == $max_group) $group_number = 1; else $group_number++;
          }

          $tour_update =  $db -> Prepare('UPDATE `tournament` SET state = 1, int_group = :int_group WHERE id = :id');
          $tour_update -> bindParam(":int_group", $max_group, PDO::PARAM_INT);
          $tour_update -> bindParam(":id", $get_tour, PDO::PARAM_INT);
          $tour_update -> Execute();

          // add match
          for ($a=1; $a <= $max_group; $a++) { 
            $s_team =  $db -> Prepare('SELECT * FROM group_team WHERE group_id = :group_id AND tournament = :tournament ORDER BY RAND()');
            $s_team -> bindParam(":group_id", $a, PDO::PARAM_INT);
            $s_team -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
            $s_team -> execute();
            $s_team =  $s_team -> fetchAll();

            for ($i = count( $s_team ) -1; $i >= 1; $i--) { 
              $first_gr = $s_team[$i];

              for ($j=$i-1; $j >= 0; $j--) { 
                $second_gr = $s_team[$j];
                $rand_team = rand(0,1);

                if($rand_team) {
                  $team_a = $first_gr;
                  $team_b = $second_gr;
                } else {
                  $team_b = $first_gr;
                  $team_a = $second_gr;
                }
                
                $date = date('Y-m-d H:i:s', time() + rand(86400,345600));
                
                $insert_match = $db -> Prepare('INSERT INTO `matches`(`team_name_1`, `team_name_2`, `game`, `tournament`, `group_id`, `date`) VALUES (:team1, :team2, :game, :tour, :group_id, :data)');
                $insert_match -> bindParam(":team1", $team_a['team_name'], PDO::PARAM_STR);
                $insert_match -> bindParam(":team2", $team_b['team_name'], PDO::PARAM_STR);
                $insert_match -> bindParam(":game", $tournament['game'], PDO::PARAM_INT);
                $insert_match -> bindParam(":tour", $get_tour, PDO::PARAM_INT);
                $insert_match -> bindParam(":group_id", $a, PDO::PARAM_INT);
                $insert_match -> bindParam(":data", $date, PDO::PARAM_STR);
                $insert_match -> execute();

              }
            }
          }
          // add match end

          // update tournament na wyższy poziom
          header('Location: tournaments.php?g=' . $get_tour);
        }
        echo '<center><a href="tournaments.php?g=' . $get_tour . '&rand"><button class="btn waves-effect waves-light" type="submit" name="action">Wylosuj grupy<i class="material-icons right">send</i></button></a></center>';
      }
      else
      {
        echo 'musisz poczekać na więcej teamów';
      }



// lista drużyn do zaakceptowania
?>
<div class="table-responsive wrap">
  <table class="display" id="dataTables-example">
    <thead>
      <tr>
        <th>Lp</th>
        <th class="center">Nazwa Drużyny</th>
        <th class="center">Kapitan Drużyny</th>
        <th class="center">Członkowie</th>
        <th class="center">Data stworzenia</th>
        <!-- <?php if($tournament['type'] == 1) echo '<th>Akceptuj</th>'; ?> -->
        <th class="center">Akceptuj</th>
        <th class="center">Odrzuć</th>
      </tr>   
    </thead>
    <tbody>
    <?php
      // $team_list =  $db -> Prepare('SELECT g.id, g.team_name, g.accept, t.captain_name, t.player1_name, t.player2_name, t.player3_name, t.player4_name, t.player5_name, t.date_of_create FROM group_team g, teams t, tournament l WHERE t.game = l.game AND l.id = :tournament AND g.team_name = t.team_name AND g.tournament = :tournament AND g.group_id = 0 ORDER BY g.accept ASC');
      $team_list =  $db -> Prepare('SELECT g.id, g.team_name, g.accept, t.captain_name, t.player1_name, t.player2_name, t.player3_name, t.player4_name, t.player5_name, t.date_of_create FROM group_team g, teams t, tournament l WHERE t.game = l.game AND l.id = :tournament AND g.tournament = :tournament AND g.group_id = 0 ORDER BY g.accept ASC');
      $team_list -> bindParam(":tournament", $get_tour, PDO::PARAM_STR);
      $team_list -> Execute();
      $team_list =  $team_list -> fetchAll();

      $ile_druzyn=0;
      foreach ($team_list as $row)
      {
        $ile_druzyn++;
    ?>
        <tr class="odd gradeA">
          <td><?php echo $ile_druzyn; ?></td>
          <td class="center"><?php echo $row['team_name']; ?></td>
          <td class="center"><a target="_blank" href="../profil.php?user=<?php echo $row['captain_name'];?>"><?php echo $row['captain_name']; ?></a></td>
          <td class="center">
            <a target="_blank" href="../profil.php?user=<?php echo $row['player1_name'];?>"><?php echo $row['player1_name']; ?></a>,
            <a target="_blank" href="../profil.php?user=<?php echo $row['player2_name'];?>"><?php echo $row['player2_name']; ?></a>,
            <a target="_blank" href="../profil.php?user=<?php echo $row['player3_name'];?>"><?php echo $row['player3_name']; ?></a>,
            <a target="_blank" href="../profil.php?user=<?php echo $row['player4_name'];?>"><?php echo $row['player4_name']; ?></a>,
            <a target="_blank" href="../profil.php?user=<?php echo $row['player5_name'];?>"><?php echo $row['player5_name']; ?></a>

          </td>
          <td class="center"><?php echo $row['date_of_create'];?></td>
          <td class="center">
            <?php
              if($row['accept'] == 0)
              {
                if($count_team['i'] < 128) echo '<a href="tournaments.php?g=' . $get_tour . '&accept=' . $row['id']. '">Akceptuj</a>';
                else echo 'Pełna lista drużyn';
              }
              else echo '---';
            ?>
          </td>
          <td class="center"><a href="tournaments.php?g=<?php echo $get_tour; ?>&delete=<?php echo $row['id']; ?>">Odrzuć</a></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <style>
    .col-sm-5 {width:41.5%;float:left;}
    .col-sm-7 {width:57.5%;float:left;}
    .col-sm-6 {width:40%;margin:0 5%;float:left;}
    .col-sm-6 select, .col-sm-6 input {width:inherit;display:inherit;}
    input.form-control.input-sm, select.form-control.input-sm {margin-left:10px;font-size:14px;height:30px;margin-bottom:0px;}
    div#dataTables-example_info, ul.pagination {margin:0;text-align: center;}
    #dataTables-example_wrapper div.row {margin-left:0px;margin-right:0px;}
  </style>
</div>

<?php

  }
  /* ZBIERANIE DRUŻYN END */
  /* CZĘŚĆ GRUPOWA BEGIN */
  else if($tournament['state'] == 1) {

    if(isset($_GET['endgroup']))
    {

      $ban_team_names = '(';
      for ($i=1; $i <= $tournament['int_group']; $i++)
      { 
        $tem =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND group_id = ' . $i . ' ORDER BY `W` DESC, `P` DESC,`GD` DESC Limit 1');
        $tem -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
        $tem -> execute();
        $tem =  $tem -> fetch(PDO::FETCH_ASSOC);
        $ban_team_names = $ban_team_names . "'" . $tem['team_name'] . "',";
        $acc_team_list[] = $tem;
      }
      $ban_team_names = $ban_team_names . "'9')";

      if( (8 - $tournament['int_group']) < 0 ) $lim = 16 - $tournament['int_group'];
      else $lim = 8 - $tournament['int_group'];

      $tems =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND team_name NOT IN '.$ban_team_names.' ORDER BY `W` DESC, `P` DESC,`GD` DESC Limit ' . $lim);
      $tems -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
      $tems -> execute();
      $tems =  $tems -> fetchAll();

      $leader_teams = array_merge($acc_team_list, $tems);



      //druzyny do części finałowej
      if( (8 - $tournament['int_group']) < 0 )
      {
        $max_num = 16;
        $gr = 17;
      }
      else
      {
        $max_num = 8;
        $gr = 18;
      }

      shuffle($leader_teams);

      $pos = 1;
      for ($i=0; $i < $max_num; $i++) {
        $sel_tem =  $leader_teams[ $i ];

        $insert_team =  $db -> Prepare('INSERT INTO `group_team`(`group_id`, `tournament`, `team_name`, `GD`, `accept`, `position`) VALUES (:gr, :tournament, :team_name, 0, 1, :position)');
        $insert_team -> bindParam(":gr", $gr, PDO::PARAM_INT);
        $insert_team -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
        $insert_team -> bindParam(":team_name", $sel_tem['team_name'], PDO::PARAM_INT);
        $insert_team -> bindParam(":position", $pos, PDO::PARAM_INT);
        $insert_team -> execute();

        $pos++;
        unset($bar[$i]);
      }

// generowanie mechy

      $tmatch =  $db -> Prepare('SELECT * FROM group_team WHERE tournament = :tournament AND group_id = :gr ORDER BY position ASC');
      $tmatch -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
      $tmatch -> bindParam(":gr", $gr, PDO::PARAM_INT);
      $tmatch -> execute();
      $tmatch =  $tmatch -> fetchAll();

      for ($i=0; $i < $max_num; $i+=2) { 
        $date = date('Y-m-d H:i:s', time() + rand(86400,345600));
          
        $insert_match = $db -> Prepare('INSERT INTO `matches`(`team_name_1`, `team_name_2`, `game`, `tournament`, `group_id`, `date`) VALUES (:team1, :team2, :game, :tour, :group_id, :data)');
        $insert_match -> bindParam(":team1", $tmatch[ $i ]['team_name'], PDO::PARAM_STR);
        $insert_match -> bindParam(":team2", $tmatch[ $i + 1 ]['team_name'], PDO::PARAM_STR);
        $insert_match -> bindParam(":game", $tournament['game'], PDO::PARAM_INT);
        $insert_match -> bindParam(":tour", $get_tour, PDO::PARAM_INT);
        $insert_match -> bindParam(":group_id", $gr, PDO::PARAM_INT);
        $insert_match -> bindParam(":data", $date, PDO::PARAM_STR);
        $insert_match -> execute();
      }




      // przejście w tryb finałowy
      $tour_update =  $db -> Prepare('UPDATE `tournament` SET state = 2 WHERE id = :id');
      $tour_update -> bindParam(":id", $get_tour, PDO::PARAM_INT);
      $tour_update -> Execute();
      
      header('Location: tournaments.php?g=' . $get_tour);
    }

    echo '<center><a href="tournaments.php?g=' . $get_tour . '&endgroup"><button class="btn waves-effect waves-light" type="submit" name="action">Zakończ część grupowa <i class="material-icons right">send</i></button></a></center>';
    // wypisz liderów grup

    echo '<ul class="collapsible wrap" data-collapsible="accordion">
      <li>
        <div class="collapsible-header"><i class="material-icons">polymer</i>Liderzy grup</div>
        <div class="collapsible-body" style="padding:.2em 2em;">';

        if ( $tournament['int_group'] <= 8 ) $ls = 8;
        else $ls = 16; 
        echo '<center><small>Lista ' . $ls . ' drużyn, które przechodzą do fazy finałowej</small></center>';

        echo '<table><thead><tr><th>Grupa</th><th>Nazwa</th><th>GD</th></tr></thead><tbody>';

        $ban_team_names = '(';
        for ($i=1; $i <= $tournament['int_group']; $i++)
        { 
          $tem =  $db -> Prepare('SELECT * FROM group_team WHERE tournament = :tournament AND group_id = ' . $i . ' ORDER BY `W` DESC, `P` DESC,`GD` DESC Limit 1');
          $tem -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
          $tem -> execute();
          $tem =  $tem -> fetch(PDO::FETCH_ASSOC);
          $ban_team_names = $ban_team_names . "'" . $tem['team_name'] . "',";
          #echo '<tr><td>Grupa '.$i.'</td><td>' . $tem['team_name'] . '</td><td>' . $tem['GD'] .'</td></tr>';
          $acc_team_list[] = $tem;
        }

        $ban_team_names = $ban_team_names . "'9')";

        if( (8 - $tournament['int_group']) < 0 )
          $lim = 16 - $tournament['int_group'];
        else
          $lim = 8 - $tournament['int_group'];

        
        $tems =  $db -> Prepare('SELECT * FROM group_team WHERE tournament = :tournament AND team_name NOT IN '.$ban_team_names.' ORDER BY `W` DESC, `P` DESC,`GD` DESC Limit ' . $lim);
        $tems -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
        $tems -> execute();
        $tems =  $tems -> fetchAll();

        $leader_teams = array_merge($acc_team_list, $tems);

        for ($i=1; $i <= $tournament['int_group']; $i++)
        {
          foreach ($leader_teams as $tm)
          {
            if( $tm['group_id'] != $i ) continue;
            echo '<tr><td>Grupa '.$i.'</td><td>' . $tm['team_name'] . '</td><td>' . $tm['GD'] .'</td></tr>';
          }
        }

    echo '</tbody></table></div></li>
    <li>
      <div class="collapsible-header"><i class="material-icons">polymer</i>Mecze grupowe</div>
        <div class="collapsible-body" style="padding:.2em 2em;">';

          $match_list =  $db -> Prepare('SELECT * FROM matches WHERE tournament = :tour AND group_id < 17 ORDER BY group_id DESC');
          $match_list -> bindParam(":tour", $get_tour, PDO::PARAM_INT);
          $match_list -> Execute();
          $match_list =  $match_list -> fetchAll();

          echo '<div class="wrap center">
            <span>Lista meczy do edycji</span>
            <div class="table-responsive">
              <table class="display" id="dataTables-example">
                <thead>
                  <tr>
                    <th class="center">ID</th>
                    <th class="center">Data</th>
                    <th class="center">Drużyna 1</th>
                    <th class="center">P1</th>
                    <th class="center">P2</th>
                    <th class="center">Drużyna 1</th>
                    <th class="center">Grupa</th>
                    <th class="center">Akcja</th>
                  </tr>
                </thead>
                <tbody>';

                  foreach ($match_list as $row)
                  {
                    echo '<tr class="odd gradeA">
                      <td class="center">#' . $row['id'] . '</a></td>
                      <td class="center">' . $row['date'] . '</a></td>
                      <td class="center">';
                      if( $row['score1'] > $row['score2'] )
                        echo '<b>'.$row['team_name_1'].'</b>';
                      else
                        echo $row['team_name_1'];
                      echo '</td>
                      <td class="center">' . $row['score1'] . '</a></td>
                      <td class="center">' . $row['score2'] . '</a></td>
                      <td class="center">';
                        if( $row['score2'] > $row['score1'] )
                          echo '<b>'.$row['team_name_2'].'</b>';
                        else
                          echo $row['team_name_2'];
                      echo '</td>
                      <td class="center">' . group_name($row['group_id']) . '</a></td>';
                      echo '<td class="center"><a href="matches.php?m=' . $row['id'] . '">';
                        if( $row['score1'] == 0 AND $row['score2'] == 0 )
                          echo 'Edytuj';
                        else
                          echo '<small>Edytuj</small>';
                      echo '</a></td>
                    </tr>';
                  }

              echo '</tbody>
              </table>
            </div>
          </div>';

        echo '</div>
      </li>
    </ul>';

  }
  /* CZĘŚĆ GRUPOWA END */
  /* CZĘŚĆ FINAŁOWA BEGIN */
  else /*if($tournament['state'] == 2) {
    ECHO 'czesc finałowa';

  }*/
  {
    echo '<h4 class="center"><a target="_blank" href="../tournament.php?g=' . $get_tour . '">Zobacz tabele turnieju</a></h4>';
    if($tournament['state'] == 2)
    {

    $match_list =  $db -> Prepare('SELECT * FROM matches WHERE tournament = :tour AND group_id > 16 ORDER BY group_id DESC');
    $match_list -> bindParam(":tour", $get_tour, PDO::PARAM_INT);
    $match_list -> Execute();
    $match_list =  $match_list -> fetchAll();

    echo '
    <div class="wrap center">
      <span>Lista meczy do edycji</span>
      <div class="table-responsive">
      <table class="display" id="dataTables-example">
        <thead>
          <tr>
            <th class="center">ID</th>
            <th class="center">Data</th>
            <th class="center">Drużyna 1</th>
            <th class="center">P1</th>
            <th class="center">P2</th>
            <th class="center">Drużyna 1</th>
            <th class="center">Grupa</th>
            <th class="center">Akcja</th>
          </tr>
        </thead>
        <tbody>';

          foreach ($match_list as $row)
          {
            echo '<tr class="odd gradeA">
              <td class="center">#' . $row['id'] . '</a></td>
              <td class="center">' . $row['date'] . '</a></td>
              <td class="center">' . $row['team_name_1'] . '</a></td>
              <td class="center">' . $row['score1'] . '</a></td>
              <td class="center">' . $row['score2'] . '</a></td>
              <td class="center">' . $row['team_name_2'] . '</a></td>
              <td class="center">' . group_name($row['group_id']) . '</a></td>';
              if( $row['score1'] == 0 AND $row['score2'] == 0 )
                echo '<td class="center"><a href="matches.php?m=' . $row['id'] . '">Edytuj</a></td>';
              else echo '<td class="center">---</td>';
            echo '</tr>';
          }

        echo '</tbody>
      </table>
      </div>
    </div>';

    }

  }
  /* CZĘŚĆ FINAŁOWA BEGIN */

//
    }
    else
    {
        $tournament =  false;
        $tournament =  $db -> Prepare('SELECT id, name, game, state, type FROM tournament');
        $tournament -> bindParam(":id", $get_tour, PDO::PARAM_INT);
        $tournament -> Execute();
        $tournament =  $tournament -> fetchAll();

        echo '<h1 class="name">KFSE <small>lista turnieji</small></h1>';

        if (isset($_GET['error'])) 
        {
          switch ($_GET['error']) 
          {
            case 1:
              echo '<h4 class="register-error">Uzupełnij wszystkie pola!</h4>';
              break;

            case 2:
              echo '<h4 class="register-error">Istnieje już turniej o takiej nazwie do podanej gry!</h4>';
              break;

            default:
              echo '<h4 class="register-error">Coś poszło nie tak!</h4>';
              break;
          }
        }

        if (isset($_GET['success'])) 
        {
          switch ($_GET['success']) 
          {
            case 1:
              echo '<h4 class="register-success">Dodano nowy turniej</h4>';
              break;
          }
        }

        echo '<ul class="collapsible popout" data-collapsible="accordion">
          <li>
            <div class="collapsible-header center"><b>Dodaj nowy turniej</b></div>
            <div class="collapsible-body grey lighten-5">
              <form class="col s12 text-center" action="tournaments.php" method="POST">
                <div class="row">
                  <div class="input-field col s4">
                    <input class="text validate" data-length="25" type="text" name="tour_name" />
                    <label>Nazwa turnieju</label>
                  </div>
                  <div class="input-field col s4">
                    <select class="select" name="game-id">
                      <option value="" disabled selected>Choose your option</option>
                      <option value="1">Counter Strike Global Offensive</option>
                      <option value="2">League of Legends</option>
                    </select>
                    <label>Typ gry</label>
                  </div>
                  <div class="input-field col s2">
                    <div class="switch">
                      <label>
                        Free
                        <input type="checkbox" name="open" />
                        <span class="lever"></span>
                        Paid
                      </label>
                    </div>
                  </div>
                  <div class="input-field col s2">
                    <button class="btn waves-effect waves-light" type="submit" name="add_tour">Submit
                      <i class="material-icons right">send</i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </li>
        </ul>';

        if (!$tournament) {
            echo 'Brak turniejów :(';
        }
        else
        {
            echo '<table class="highlight wrap">
                    <thead>
                        <tr>
                            <th data-field="name">Nazwa</th>
                            <th data-field="game">Gra</th>
                            <th data-field="status">Status</th>
                            <th data-field="type">Typ</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($tournament as $simple) {
                switch ($simple['game']) {
                    case 1:
                        $game_name = 'Counter Strike Global Offensive';
                        break;
                    
                    case 2:
                        $game_name = 'League of Legends';
                        break;
                }
                echo '<tr><td><a href="tournaments.php?g=' . $simple['id'] . '">' . $simple['name'] . '</a></td><td>' . $game_name .'</td><td>' . tour_status($simple['state']) . '</td><td>' . tour_type($simple['type']) . '</td></tr>';
            }
            echo '</tbody></table>';
        }

    }


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
  function group_name($st)
  {
    switch ($st) {
      case 1:  return 'A'; break;
      case 2:  return 'B'; break;
      case 3:  return 'C'; break;
      case 4:  return 'D'; break;
      case 5:  return 'E'; break;
      case 6:  return 'F'; break;
      case 7:  return 'G'; break;
      case 8:  return 'H'; break;
      case 9:  return 'I'; break;
      case 10: return 'J'; break;
      case 11: return 'K'; break;
      case 12: return 'L'; break;
      case 13: return 'M'; break;
      case 14: return 'N'; break;
      case 15: return 'O'; break;
      case 16: return 'P'; break;

      case 17: return '1/8'; break;
      case 18: return '1/4'; break;
      case 19: return '1/2'; break;
      case 20: return 'Finał'; break;
      default: return $st; break;
    }
  }
  function int_group($st)
  {
    if     ( ( $st >= 32 ) AND ( $st <= 36  ) ) return 6;
    elseif ( ( $st >= 37 ) AND ( $st <= 42  ) ) return 7;
    elseif ( ( $st >= 43 ) AND ( $st <= 64  ) ) return 8;
    elseif ( ( $st >= 65 ) AND ( $st <= 72  ) ) return 12;
    elseif ( ( $st >= 73 ) AND ( $st <= 78  ) ) return 13;
    elseif ( ( $st >= 79 ) AND ( $st <= 88  ) ) return 14;
    elseif ( ( $st >= 89 ) AND ( $st <= 128 ) ) return 16;
    else return false;
  }


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
  require_once "includes/foot-js.php";

  require_once "includes/footer.php";
