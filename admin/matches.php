<?php
  require_once "includes/header.php";

  $get_match = (isset($_GET['m']) ? $_GET['m'] : 0);
  
  $match =  $db -> prepare('SELECT * FROM matches WHERE id = :id');
  $match -> bindParam(":id", $get_match, PDO::PARAM_INT);
  $match -> execute();
  $match =  $match -> fetch(PDO::FETCH_ASSOC);

  if($match)
  {
    $mtour =  $db -> prepare('SELECT state, game FROM tournament WHERE id = :id');
    $mtour -> bindParam(":id", $match['tournament'], PDO::PARAM_INT);
    $mtour -> execute();
    $mtour =  $mtour -> fetch(PDO::FETCH_ASSOC);



    if(isset($_POST['save_match']))
    {
      if( $mtour['state'] == 3 )
      {
        header("Location: matches.php?m=".$get_match."&err=1");
        exit;
      }
      if( $mtour['state'] == 2 AND $match['group_id'] < 17 )
      {
        header("Location: matches.php?m=".$get_match."&err=2");
        exit;
      }

      $_POST['score1'] = (int)$_POST['score1'];
      $_POST['score2'] = (int)$_POST['score2'];

      if( $_POST['score1'] == $_POST['score2'] )
      {
        header("Location: matches.php?m=".$get_match."&err=3");
        exit;
      }
      

      $up_match =  $db -> Prepare('UPDATE `matches` SET `score1` = :score1, `score2` = :score2 WHERE id = :m');
      $up_match -> bindParam(":score1", $_POST['score1'], PDO::PARAM_INT);
      $up_match -> bindParam(":score2", $_POST['score2'], PDO::PARAM_INT);
      $up_match -> bindParam(":m", $get_match, PDO::PARAM_INT);
      $up_match -> Execute();

      if( $match['group_id'] < 17)
      {
        // team1
        $win_match =  $db -> Prepare('SELECT COUNT(id) AS w FROM `matches` WHERE (team_name_1 = :name AND group_id = :group_id AND tournament = :tournament AND score1 > score2) OR (team_name_2 = :name AND group_id = :group_id AND tournament = :tournament AND score2 > score1)');
        $win_match -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $win_match -> bindParam(":name", $match['team_name_1'], PDO::PARAM_STR);
        $win_match -> bindParam(":group_id", $match['group_id'], PDO::PARAM_INT);
        $win_match -> Execute();
        $win_match =  $win_match -> fetch(PDO::FETCH_ASSOC);

        $lose_match =  $db -> Prepare('SELECT COUNT(id) AS l FROM `matches` WHERE (team_name_1 = :name AND group_id = :group_id AND tournament = :tournament AND score1 < score2) OR (team_name_2 = :name AND group_id = :group_id AND tournament = :tournament AND score2 < score1)');
        $lose_match -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $lose_match -> bindParam(":name", $match['team_name_1'], PDO::PARAM_STR);
        $lose_match -> bindParam(":group_id", $match['group_id'], PDO::PARAM_INT);
        $lose_match -> Execute();
        $lose_match =  $lose_match -> fetch(PDO::FETCH_ASSOC);

        $state_match =  $db -> Prepare('SELECT COALESCE(SUM(score1 - score2), 0) AS a1, (SELECT COALESCE(SUM(score2 - score1), 0) FROM `matches` WHERE team_name_2 = :name AND group_id = :group_id AND tournament = :tournament AND (score1 > 0 OR score2 > 0)) AS a2 FROM `matches` WHERE team_name_1 = :name AND group_id = :group_id AND tournament = :tournament AND(score1 > 0 OR score2 > 0)');
        $state_match -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $state_match -> bindParam(":name", $match['team_name_1'], PDO::PARAM_STR);
        $state_match -> bindParam(":group_id", $match['group_id'], PDO::PARAM_INT);
        $state_match -> Execute();
        $state_match =  $state_match -> fetch(PDO::FETCH_ASSOC);
        $scr = $state_match['a1'] + $state_match['a2'];

        $up_grp =  $db -> Prepare('UPDATE `group_team` SET `W` = :w, `P` = :p, `GD` = :gd WHERE team_name = :team_name AND tournament = :tour AND group_id = :group');
        $up_grp -> bindParam(":w", $win_match['w'], PDO::PARAM_INT);
        $up_grp -> bindParam(":p", $lose_match['l'], PDO::PARAM_INT);
        $up_grp -> bindParam(":gd", $scr, PDO::PARAM_INT);
        $up_grp -> bindParam(":team_name", $match['team_name_1'], PDO::PARAM_STR);
        $up_grp -> bindParam(":tour", $match['tournament'], PDO::PARAM_INT);
        $up_grp -> bindParam(":group", $match['group_id'], PDO::PARAM_INT);
        $up_grp -> Execute();

        // team2
        $win_match =  $db -> Prepare('SELECT COUNT(id) AS w FROM `matches` WHERE (team_name_1 = :name AND group_id = :group_id AND tournament = :tournament AND score1 > score2) OR (team_name_2 = :name AND group_id = :group_id AND tournament = :tournament AND score2 > score1)');
        $win_match -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $win_match -> bindParam(":name", $match['team_name_2'], PDO::PARAM_STR);
        $win_match -> bindParam(":group_id", $match['group_id'], PDO::PARAM_INT);
        $win_match -> Execute();
        $win_match =  $win_match -> fetch(PDO::FETCH_ASSOC);

        $lose_match =  $db -> Prepare('SELECT COUNT(id) AS l FROM `matches` WHERE (team_name_1 = :name AND group_id = :group_id AND tournament = :tournament AND score1 < score2) OR (team_name_2 = :name AND group_id = :group_id AND tournament = :tournament AND score2 < score1)');
        $lose_match -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $lose_match -> bindParam(":name", $match['team_name_2'], PDO::PARAM_STR);
        $lose_match -> bindParam(":group_id", $match['group_id'], PDO::PARAM_INT);
        $lose_match -> Execute();
        $lose_match =  $lose_match -> fetch(PDO::FETCH_ASSOC);

        $state_match =  $db -> Prepare('SELECT COALESCE(SUM(score1 - score2), 0) AS a1, (SELECT COALESCE(SUM(score2 - score1), 0) FROM `matches` WHERE team_name_2 = :name AND group_id = :group_id AND tournament = :tournament AND (score1 > 0 OR score2 > 0)) AS a2 FROM `matches` WHERE team_name_1 = :name AND group_id = :group_id AND tournament = :tournament AND(score1 > 0 OR score2 > 0)');
        $state_match -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $state_match -> bindParam(":name", $match['team_name_2'], PDO::PARAM_STR);
        $state_match -> bindParam(":group_id", $match['group_id'], PDO::PARAM_INT);
        $state_match -> Execute();
        $state_match =  $state_match -> fetch(PDO::FETCH_ASSOC);
        $scr = $state_match['a1'] + $state_match['a2'];

        $up_grp =  $db -> Prepare('UPDATE `group_team` SET `W` = :w, `P` = :p, `GD` = :gd WHERE team_name = :team_name AND tournament = :tour AND group_id = :group');
        $up_grp -> bindParam(":w", $win_match['w'], PDO::PARAM_INT);
        $up_grp -> bindParam(":p", $lose_match['l'], PDO::PARAM_INT);
        $up_grp -> bindParam(":gd", $scr, PDO::PARAM_INT);
        $up_grp -> bindParam(":team_name", $match['team_name_2'], PDO::PARAM_STR);
        $up_grp -> bindParam(":tour", $match['tournament'], PDO::PARAM_INT);
        $up_grp -> bindParam(":group", $match['group_id'], PDO::PARAM_INT);
        $up_grp -> Execute();

      }
      elseif ( $match['group_id'] < 21 )
      {
        echo __LINE__.'<br/>';
        if( $_POST['score1'] > $_POST['score2'] )
          $tm_name = $match['team_name_1'];
        else
          $tm_name = $match['team_name_2'];

        $gr = $match['group_id'] + 1;
        echo __LINE__.'<br/>';
        $pos_team =  $db -> Prepare('SELECT position FROM group_team WHERE tournament = :tournament AND group_id = :gr AND team_name = :name Limit 1');
        $pos_team -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $pos_team -> bindParam(":gr", $match['group_id'], PDO::PARAM_INT);
        $pos_team -> bindParam(":name", $tm_name, PDO::PARAM_STR);
        $pos_team -> execute();
        $pos_team =  $pos_team -> fetch(PDO::FETCH_ASSOC);
echo __LINE__.'<br/>';
        $pos = ceil($pos_team['position'] / 2);
echo __LINE__.'<br/>';
        $insert_team =  $db -> Prepare('INSERT INTO `group_team`(`group_id`, `tournament`, `team_name`, `GD`, `accept`, `position`) VALUES (:gr, :tournament, :team_name, 0, 1, :position)');
        $insert_team -> bindParam(":gr", $gr, PDO::PARAM_INT);
        $insert_team -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $insert_team -> bindParam(":team_name", $tm_name, PDO::PARAM_INT);
        $insert_team -> bindParam(":position", $pos, PDO::PARAM_INT);
        $insert_team -> execute();
echo __LINE__.'<br/>';
        if ( $match['group_id'] == 20 )
        {
          $tour_update =  $db -> Prepare('UPDATE `tournament` SET state = 3 WHERE id = :id');
          $tour_update -> bindParam(":id", $match['tournament'], PDO::PARAM_INT);
          $tour_update -> Execute();

          header("Location: matches.php?m=".$get_match."&success&endtournament");
          exit;
        }
echo __LINE__.'<br/>';
        if( $pos%2 == 0 ) $pos2 = $pos - 1;
        else $pos2 = $pos + 1;
echo __LINE__.'<br/>';
        $pteam2 =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND group_id = :gr AND position = :pos Limit 1');
        $pteam2 -> bindParam(":tournament", $match['tournament'], PDO::PARAM_INT);
        $pteam2 -> bindParam(":gr", $gr, PDO::PARAM_INT);
        $pteam2 -> bindParam(":pos", $pos2, PDO::PARAM_STR);
        $pteam2 -> execute();
        $pteam2 =  $pteam2 -> fetch(PDO::FETCH_ASSOC);
echo __LINE__.'<br/>';
        if( $pteam2 )
        {
          echo __LINE__.'<br/>';
          // add match
          if( $pos%2 == 0 ) {
            $team_a = ( ($tm_name == $match['team_name_1']) ? $pteam2['team_name'] : $match['team_name_1'] );
            $team_b = $tm_name;
          } else {
            $team_a = $tm_name;
            $team_b = ( ($tm_name == $match['team_name_1']) ? $pteam2['team_name'] : $match['team_name_1'] );
          }
echo __LINE__.'<br/>';
          $date = date('Y-m-d H:i:s', time() + rand(86400,200000));
                echo __LINE__.'<br/>';
          $insert_match = $db -> Prepare('INSERT INTO `matches`(`team_name_1`, `team_name_2`, `game`, `tournament`, `group_id`, `date`) VALUES (:team1, :team2, :game, :tour, :group_id, :data)');
          $insert_match -> bindParam(":team1", $team_a, PDO::PARAM_STR);
          $insert_match -> bindParam(":team2", $team_b, PDO::PARAM_STR);
          $insert_match -> bindParam(":game", $mtour['game'], PDO::PARAM_INT);
          $insert_match -> bindParam(":tour", $match['tournament'], PDO::PARAM_INT);
          $insert_match -> bindParam(":group_id", $gr, PDO::PARAM_INT);
          $insert_match -> bindParam(":data", $date, PDO::PARAM_STR);
          $insert_match -> execute();
          echo __LINE__.'<br/>';
        }
echo __LINE__.'<br/>';
      }


      header("Location: matches.php?m=".$get_match."&success");
      exit;
    }


    // edit match
    $tour =  $db -> prepare('SELECT name FROM tournament WHERE id = :id');
    $tour -> bindParam(":id", $match['tournament'], PDO::PARAM_INT);
    $tour -> execute();
    $tour =  $tour -> fetch(PDO::FETCH_ASSOC);
# TODO add map to CS:GO

    $msg = 'edycja meczu';
    if (isset($_GET['err'])) 
    {
      switch ($_GET['err']) 
      {
        case 1:
          $msg = '<font color="red">Turniej zakończony!</font>';
          break;
        case 2:
          $msg = '<font color="red">Turniej jest już w dalszej fazie!</font>';
          break;
        case 3:
          $msg = '<font color="red">Remis jest niedopuszczalny!</font>';
          break;
      }
    }
    if (isset($_GET['success'])) 
    {
      $msg = '<font color="green">zaktualizowano mecz</font>';
    }

    echo '<h1 class="name">' . $tour['name'] . ' <small>' . $msg . '</small></h1>
    <span><a href="tournaments.php?g=' . $match['tournament'] . '">powrót</a></span>
    <form class="col s12 text-center wrap" action="matches.php?m=' . $get_match . '" method="POST">

    <table style="margin:auto; width:80%;">
      <tbody>
        <tr>
          <th width="30%" class="text-right">Drużyna 1</th><th width="10%" class="text-center">Score 1</th><th width="20%" class="text-center"><small>' . $match['date'] . '</small></th><th width="10%" class="text-center">Score 2</th><th width="30%" class="text-left">Drużyna 2</th>
        </tr>

        <tr>
          <td class="text-right"><b>' . $match['team_name_1'] . '</b></td>
          <td class="text-center"><input class="text text-center" style="width:35px" type="number" min="0" max="16" name="score1" value="' . $match['score1'] . '" /></td>
          <td class="text-center">vs</td>
          <td class="text-center"><input class="text text-center" style="width:35px" type="number" min="0" max="16" name="score2" value="' . $match['score2'] . '" /></td>
          <td class="text-left"><b>' . $match['team_name_2'] . '</b></td>
        </tr>
        <tr>
          <td colspan="5" class="text-center">
            <button class="btn waves-effect waves-light" type="submit" name="save_match">Zapisz
            <i class="material-icons right">send</i>
            </button>
          </td>
        </tr> 
      </tbody>
    </table>     
</form>';


  }
  else
  {    
    $get_tour = (isset($_GET['t']) ? $_GET['t'] : 0);
    $get_group = (isset($_GET['g']) ? $_GET['g'] : 0);

    $tour =  $db -> prepare('SELECT * FROM tournament WHERE id = :id');
    $tour -> bindParam(":id", $get_tour, PDO::PARAM_INT);
    $tour -> execute();
    $tour =  $tour -> fetch(PDO::FETCH_ASSOC);

    if(!$tour) {
      header("Location: ./tournaments.php");
      exit;
    }

    if($get_group < 1 OR $get_group > 21) {
      header("Location: ./tournaments.php");
      exit;
    }

    $match_list =  $db -> prepare('SELECT * FROM matches WHERE tournament = :tour AND group_id = :group');
    $match_list -> bindParam(":tour", $get_tour, PDO::PARAM_INT);
    $match_list -> bindParam(":group", $get_group, PDO::PARAM_INT);
    $match_list -> execute();
    $match_list =  $match_list -> fetchAll();

    if(!$match_list) {
      header("Location: ./tournaments.php");
      exit;
    }

  }

  require_once "includes/footer.php";
  require_once "includes/foot-js.php";