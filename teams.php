<?php
  require_once "includes/header.php";

$tm_id = (isset($_GET['t']) ? $_GET['t'] : false);

if($tm_id)
{
  $tm =  $db -> Prepare('SELECT id FROM teams WHERE id = :id');
  $tm -> bindParam(":id", $tm_id, PDO::PARAM_INT);
  $tm -> Execute();
  $tm =  $tm -> fetch(PDO::FETCH_ASSOC);
}
else $tm = false;

if( $tm )  //czy jest konkretny team
{
  $team =  $db -> Prepare('SELECT team_name, team_tag, captain_name, player1_name, player2_name, player3_name, player4_name, player5_name FROM teams WHERE id = :id');
  $team -> bindParam(":id", $tm['id'], PDO::PARAM_INT);
  $team -> Execute();
  $team =  $team -> fetch(PDO::FETCH_ASSOC);


  echo '<div class="container">
    <div class="wrap">
      <h1 class="text-center">' . $team['team_name'] . ' <small>[' . $team['team_tag'] . ']</small></h1>
      
      <div class="left-box">
        <p class="team-captain">Kapitan(założyciel drużyny): <a class = "special" href="profil.php?user='.$team['captain_name'].'"> '.$team['captain_name'].'</a></p>
        <p class="team-members-list">Członkowie drużyny: </p>
        <ul>
            <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$team['player1_name'].'">'.$team['player1_name'].'</a></li>
            <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$team['player2_name'].'">'.$team['player2_name'].'</a></li>   
            <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$team['player3_name'].'">'.$team['player3_name'].'</a></li>   
            <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$team['player4_name'].'">'.$team['player4_name'].'</a></li>   
            <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$team['player5_name'].'">'.$team['player5_name'].'</a></li>   
        </ul>
      </div>

      <div class="right-box">
        <h4 class="text-center">Mecze</h4>


<table class="match_table">
  <tr><th>DATA</th><th>DRUŻYNA 1</th><th>P1</th><th>P2</th><th>DRUŻYNA 2</th><th>WYGRYWA</th></tr>';
$gr_match =  $db -> Prepare('SELECT * FROM `matches` WHERE team_name_1 = :name OR team_name_2 = :name ORDER BY `date` DESC');
$gr_match -> bindParam(":name", $team['team_name'], PDO::PARAM_INT);
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


      </div>

      <div class="clear"></div>

    </div>
  </div>';

?>
<style>
  .left-box {
    width: 390px;
    background-color: #024349;
    padding: 5px 25px;
    margin:10px;
    float:left;
  }
  .right-box {
    width:760px;
    background-color: #024349;
    padding: 5px 5px;
    margin:10px;
    float:right;
  }

</style>
<?php
}
else // gdy nie to wyświetl wszystkie
{

  $game_list = array(1, 2); // 1 => CS, 2 => Lol
  $game = (isset($_GET['g']) ? (in_array($_GET['g'], $game_list) ? $_GET['g']: 1) : 1);

  switch ($game) {
    case 1:
      $game_name = 'Counter Strike Global Offensive (' . ($teams[($game-1)]['game'] ? $teams[($game-1)]['game'] : 0 ) . ')';
      break;
    
    case 2:
      $game_name = 'League of Legends (' . ($teams[($game-1)]['game'] ? $teams[($game-1)]['game'] : 0 ) . ')';
      break;
  }

  echo '<div class="container">
    <br /><br />
    <center>
      <a href="teams.php?g=1"><img class="game_logo" src="img/games/game1.png" alt="CS:GO"></a>
      <a href="teams.php?g=2"><img class="game_logo" src="img/games/game2.png" alt="LoL"></a>
    </center>

    <h1 class="newsy-h1">Lista zarejestrowanych drużyn <?php echo $game_name; ?></h1>
    <hr style="clear:both;" />';

  $stmt =  $db -> Prepare('SELECT id, team_name, team_tag, captain_name, player1_name, player2_name, player3_name, player4_name, player5_name FROM teams WHERE game = :game');
  $stmt -> bindParam(":game", $game, PDO::PARAM_INT);
  $stmt -> Execute();
  $stmt =  $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach ($stmt as $row) 
  {
    echo'<div class="team">
    <p class="nazwa-druzyny-list">Nazwa drużyny: <strong class="pogrubienie"><a class="special" href="teams.php?t=' . $row['id'] . '">'.$row['team_name'].'</a></strong> ('.$row['team_tag'].')</p>
    <p class="team-captain">Kapitan(założyciel drużyny): <a class = "special" href="profil.php?user='.$row['captain_name'].'"> '.$row['captain_name'].'</a></p>
    <p class="team-members-list">Członkowie drużyny: </p>
    <ul>
        <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$row['player1_name'].'">'.$row['player1_name'].'</a></li>
        <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$row['player2_name'].'">'.$row['player2_name'].'</a></li>   
        <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$row['player3_name'].'">'.$row['player3_name'].'</a></li>   
        <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$row['player4_name'].'">'.$row['player4_name'].'</a></li>   
        <li class="team-members-li"><a class="special team-members" href="profil.php?user='.$row['player5_name'].'">'.$row['player5_name'].'</a></li>   
    </ul>
    </div>';
  }

  echo '</div>';

}

  require_once "includes/footer.php"; 
?>
  <style>
    .game_logo {
      width:200px;
      filter:invert(100%);
      opacity:.7;
      transition-duration: 1s;
    }
    .game_logo:hover {opacity:1;}
    .team {
      background-color: #f3f3f3;
      display: inline-block;
      width: 33%;
      margin-bottom: 5px;
      padding: 20px;
      border-radius: 9px
      -webkit-box-shadow: 0px -1px 37px -14px rgba(0,0,0,0.5);
      -moz-box-shadow: 0px -1px 37px -14px rgba(0,0,0,0.5);
      box-shadow: 0px -1px 37px -14px rgba(0,0,0,0.5);
      -webkit-border-radius: 9px;
      -moz-border-radius: 9px;
      font-size: 13px;
      color: rgb(96, 94, 94);
    }
    .team-captain {margin-top: 20px;}
    .team-members-list {margin-top: 20px;}
    .pogrubienie {font-weight: bold;}
    .team-members {font-weight: bold;}
    .team-members-li {margin-top: 5px;}
  </style>