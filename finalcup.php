<?php
  require_once "includes/header.php";

  $get_tour = (isset($_GET['g']) ? $_GET['g'] : 0);

  $tournament =  $db -> Prepare('SELECT * FROM tournament WHERE id = :id AND state >= 2');
  $tournament -> bindParam(":id", $get_tour, PDO::PARAM_INT);
  $tournament -> Execute();
  $tournament =  $tournament -> fetch(PDO::FETCH_ASSOC);
  if(!$tournament)
  {
    header('Location: tournament.php?g=' . $get_tour);
    exit();
  }

echo '<div class="container">
  <h1>Drabinka </h1>
  <span><a href="tournament.php?g=<?php echo $get_tour; ?>">powrót</a></span>
  <div class="wrap">
    <div id="drab">';

        if( (8 - $tournament['int_group']) < 0 )
        {
          echo '<div class="col">';
            $grp = 17;
            $sel_tem =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND group_id = :group_id ORDER BY position ASC');
            $sel_tem -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
            $sel_tem -> bindParam(":group_id", $grp, PDO::PARAM_INT);
            $sel_tem -> execute();
            $sel_tem =  $sel_tem -> fetchAll();

            if($sel_tem) {
              $i = 1;
              foreach ($sel_tem as $key) {
                if($i%2 == 1) echo '<div class="ttm"><div class="tm">' . $key['team_name'] . '</div>';
                else if($i%2 == 0) echo '<div class="tm">' . $key['team_name'] . '</div></div>';
                $i++;
              }
            }
            else
            {
              for ($i=0; $i < 8; $i++) { 
                echo '<div class="ttm"><div class="tm">---</div><div class="tm">---</div></div>';
              }
            }
          echo '</div>';
        }

      echo '<div class="col">';

        $grp = 18;
        for ($i=1; $i <= 8; $i++) { 
          $sel_tem =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND group_id = :group_id AND position = :position');
          $sel_tem -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
          $sel_tem -> bindParam(":group_id", $grp, PDO::PARAM_INT);
          $sel_tem -> bindParam(":position", $i, PDO::PARAM_INT);
          $sel_tem -> execute();
          $sel_tem =  $sel_tem -> fetch(PDO::FETCH_ASSOC);

          if($i%2 == 1)
          {
            if($sel_tem) echo '<div class="ttm"><div class="tm">' . $sel_tem['team_name'] . '</div>';
            else echo '<div class="ttm"><div class="tm">---</div>';
          }
          else if($i%2 == 0)
          {
            if($sel_tem) echo '<div class="xd">01-03-2016</div><div class="tm">' . $sel_tem['team_name'] . '</div></div>';
            else echo '<div class="tm">---</div></div>';
          }

        }

      echo '</div>
      <div class="col">';

        $grp = 19;
        for ($i=1; $i <= 4; $i++) { 
          $sel_tem =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND group_id = :group_id AND position = :position');
          $sel_tem -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
          $sel_tem -> bindParam(":group_id", $grp, PDO::PARAM_INT);
          $sel_tem -> bindParam(":position", $i, PDO::PARAM_INT);
          $sel_tem -> execute();
          $sel_tem =  $sel_tem -> fetch(PDO::FETCH_ASSOC);

          if($i%2 == 1)
          {
            if($sel_tem) echo '<div class="ttm"><div class="tm">' . $sel_tem['team_name'] . '</div>';
            else echo '<div class="ttm"><div class="tm">---</div>';
          }
          else if($i%2 == 0)
          {
            if($sel_tem) echo '<div class="xd">01-03-2016</div><div class="tm">' . $sel_tem['team_name'] . '</div></div>';
            else echo '<div class="tm">---</div></div>';
          }

        }

      echo '</div>
      <div class="col">';

        $grp = 20;
        for ($i=1; $i <= 2; $i++) { 
          $sel_tem =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND group_id = :group_id AND position = :position');
          $sel_tem -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
          $sel_tem -> bindParam(":group_id", $grp, PDO::PARAM_INT);
          $sel_tem -> bindParam(":position", $i, PDO::PARAM_INT);
          $sel_tem -> execute();
          $sel_tem =  $sel_tem -> fetch(PDO::FETCH_ASSOC);

          if($i%2 == 1)
          {
            if($sel_tem) echo '<div class="ttm"><div class="tm">' . $sel_tem['team_name'] . '</div>';
            else echo '<div class="ttm"><div class="tm">---</div>';
          }
          else if($i%2 == 0)
          {
            if($sel_tem) echo '<div class="xd">01-03-2016</div><div class="tm">' . $sel_tem['team_name'] . '</div></div>';
            else echo '<div class="tm">---</div></div>';
          }

        }

      echo '</div>
      <div class="col">
        <div class="ttm">';

          $grp = 21;
          $sel_tem =  $db -> Prepare('SELECT team_name FROM group_team WHERE tournament = :tournament AND group_id = :group_id AND position = 1');
          $sel_tem -> bindParam(":tournament", $get_tour, PDO::PARAM_INT);
          $sel_tem -> bindParam(":group_id", $grp, PDO::PARAM_INT);
          $sel_tem -> execute();
          $sel_tem =  $sel_tem -> fetch(PDO::FETCH_ASSOC);

          if($sel_tem) echo '<div class="tm">' . $sel_tem['team_name'] . '</div>';
          else echo '<div class="tm">---</div>';

        echo '</div>
      </div>
    </div>
  </div>
</div>';

  require_once "includes/footer.php";
?>

<style>
div#drab {display:flex;align-items: stretch;width:100%;}
div.col {
  display:flex;
  flex: 1 0;
  flex-direction: column;
  align-self: center;
  height:672px;
  justify-content: space-around;
  overflow: hidden;
}
div.ttm {
  position: relative;
  display: flex;
  flex-direction: column;
  height: 100%;
  justify-content: space-around;
}
div.ttm:after {
  content: '';
  position: absolute;
  right: 0;
  width: 2px;
  background: #CCC none repeat scroll 0% 0%;
  top: 25%;
  bottom: 25%;
}
div.tm {
  width:167px;
  height: 32px;
  background-color: #343434;
  color:#EEE;
  line-height: 32px;
  text-align: center;
  margin:5px auto;
  font-size: 90%;
}
div.tm:before, div.tm:after {
  content: '';
  position: relative;
  display: block;
  height: 2px;
  width:64px;
  margin-top: -1px;
  background: #cccccc;
}
div.tm:before {top:50%;left:-64px;}
div.tm:after {top:-50%;left:100%;}

div.col:first-child div.tm:before, div.col:last-child div.tm:after {display:none;}
div.col:last-child div.ttm:after {display:none;}
.xd {width: 100px;height: 32px;margin: -35% auto; display:none;}
</style>
