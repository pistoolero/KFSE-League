<?php
  require_once "includes/header.php";

  $matches = $db -> query('SELECT * FROM matches ORDER BY `date`');
  $matches = $matches -> fetchAll(PDO::FETCH_ASSOC);

  echo '<div class="container">
    <h1>Mecze</h1>
    <div class="wrap">';
      echo '<table width="100%">
        <tr>
          <th>mecz</th>
          <th>kto</th>
          <th>data</th>
        </tr>';

        foreach ($matches as $match) 
        {
          echo '<tr>
            <td align="center">#' . $match['id'] . '</td>
            <td align="center">' . $match['team_name_1'] . ' (' . $match['score1']. ':' . $match['score2'] . ') ' . $match['team_name_2'] . '</td>
            <td align="center">' . $match['date'] . '</td>
          </tr>
          ';
        }
      echo '</table>';
  echo '</div></div>';   

  require_once "includes/footer.php";