<?php
  require_once "includes/header.php";

  $tours =  $db -> Prepare('SELECT COUNT(id) AS count FROM tournament');
  $tours -> Execute();
  $tours =  $tours -> fetch(PDO::FETCH_ASSOC);

  $news  =  $db -> Prepare('SELECT COUNT(id) AS count FROM news');
  $news  -> Execute();
  $news  =  $news -> fetch(PDO::FETCH_ASSOC);

  $users =  $db -> Prepare('SELECT COUNT(id) AS count FROM users');
  $users -> Execute();
  $users =  $users -> fetch(PDO::FETCH_ASSOC);

  $teams =  $db -> Prepare('SELECT COUNT(id) AS count FROM teams');
  $teams -> Execute();
  $teams =  $teams -> fetch(PDO::FETCH_ASSOC);
?>
<h1 class="name">KFSE <small>statystyki strony</small></h1>
<div class="row">
  <div class="col m3 s6">
    <div class="wrap green-text panel-info">
      <i class="large material-icons">assessment</i>
      <h3><?php echo $tours['count']; ?></h3>
      <div class="panel-info-footer" style="background-color: #4CAF50;">Turnieje</div>
    </div>
  </div>
  <div class="col m3 s6">
    <div class="wrap cyan-text panel-info">
      <i class="large material-icons">message</i>
      <h3><?php echo $news['count']; ?></h3>
      <div class="panel-info-footer" style="background-color: #00bcd4;">Newsy</div>
    </div>
  </div>
  <div class="col m3 s6">
    <div class="wrap red-text panel-info">
      <i class="large material-icons">perm_identity</i>
      <h3><?php echo $users['count']; ?></h3>
      <div class="panel-info-footer" style="background-color: #f44336;">Użytkowników</div>
    </div>
  </div>
  <div class="col m3 s6">
    <div class="wrap orange-text panel-info">
      <i class="large material-icons">supervisor_account</i>
      <h3><?php echo $teams['count']; ?></h3>
      <div class="panel-info-footer" style="background-color: #ff9800;">Drużyn</div>
    </div>
  </div>
</div>


<?php
  $new_users =  $db -> Prepare('SELECT id, login, ip, email, date_of_entry FROM users ORDER BY id DESC Limit 5');
  $new_users -> Execute();
  $new_users =  $new_users -> fetchAll();
?>
<div class="row">
  <div class="col m4 s12">
    <div class="wrap">
      <h6 class="head">Turnieje</h6>
      <div id="chart_div"></div>
    </div>
  </div>
  <div class="col m8 s12">
    <div class="wrap">
      <h6 class="head">Ostatnio zarejestrowani użytkownicy</h6>
      <table class="striped">
        <thead>
          <tr>
            <th data-field="Lp">Lp</th>
            <th data-field="Login">Login</th>
            <th data-field="IP">IP</th>
            <th data-field="Email">Email</th>
            <th data-field="Rejestracja">Data stworzenia</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $i=1;
          foreach ($new_users as $new_u) {
            echo '<tr>
              <td>' . $i . '</td>
              <td>' . $new_u['login'] . '</td>
              <td>' . $new_u['ip'] . '</td>
              <td>' . $new_u['email'] . '</td>
              <td>' . $new_u['date_of_entry'] . '</td>
            </tr>';
            $i++;
          }
        ?>
            
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .chart {
    width: 100%; 
    min-height: 350px;
  }
</style>



<?php 
$tour0 =  $db -> Prepare('SELECT COUNT(id) AS i FROM tournament WHERE state = 0');
$tour0 -> execute();
$tour0 =  $tour0 ->fetch(PDO::FETCH_ASSOC);
echo '<script>var x0 = ' . $tour0['i'] . ';</script>';

$tour1 =  $db -> Prepare('SELECT COUNT(id) AS i FROM tournament WHERE state = 1');
$tour1 -> execute();
$tour1 =  $tour1 ->fetch(PDO::FETCH_ASSOC);
echo '<script>var x1 = ' . $tour1['i'] . ';</script>';

$tour2 =  $db -> Prepare('SELECT COUNT(id) AS i FROM tournament WHERE state = 2');
$tour2 -> execute();
$tour2 =  $tour2 ->fetch(PDO::FETCH_ASSOC);
echo '<script>var x2 = ' . $tour2['i'] . ';</script>';

$tour3 =  $db -> Prepare('SELECT COUNT(id) AS i FROM tournament WHERE state = 3');
$tour3 -> execute();
$tour3 =  $tour3 ->fetch(PDO::FETCH_ASSOC);
echo '<script>var x3 = ' . $tour3['i'] . ';</script>';

?>
<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

  // Load the Visualization API and the corechart package.
  google.charts.load('current', {'packages':['corechart']});

  // Set a callback to run when the Google Visualization API is loaded.
  google.charts.setOnLoadCallback(drawChart);

  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  function drawChart() {

    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Topping');
    data.addColumn('number', 'Slices');
    data.addRows([
      ['Zbieranie uczestników', x0],
      ['Rozgrywki grupowe', x1],
      ['Rozgrywki finałowe', x2],
      ['Zakończone', x3]
    ]);

    // Set chart options
    var options = {
      'legend':'bottom',
      'is3D':true
    }

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
</script>

<?php
  require_once "includes/footer.php";