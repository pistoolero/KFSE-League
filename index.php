<?php
  require_once "includes/header.php";

echo '<div class="wrapper container">
    <div id="news-list">
      <header class="news-list">
        Ostatnie aktualnosci
      </header>';
    
      $page = (int)$_GET['p'];
      if (!$page) $page = 1;

      $limitPageNews = 5; // ile newsów na stronie, aktualny layout dopuszcza 5

      do {
        $startCountNews = (int)(($page - 1) * $limitPageNews);

        $stmt = $db   -> query('SELECT * FROM news ORDER BY id DESC Limit ' . $startCountNews . ', '. $limitPageNews);
        $stmt = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        
        if ($page == 1) break;
        elseif ($stmt) break;
        else $page = 1;

      } while(!$stmt);


      function cutMe($text, $length, $sufix = '...') {
        if(strlen($text) > $length) return substr($text, 0, $length) . $sufix;
        else return $text;
      }

      foreach ($stmt as $row) 
      {
        echo '<div class="simple-news">
          <img class="news-image" src="img/news/' .$row['news_image'] . '.png" />
          <div class="news-right">
            <span class="news-title">'. $row['title'] . ' | ' . $row['category'] . '</span>
            <span class="autor">Napisano ' . $row['date_of_create'] . ' Autor: ' . $row['author'] . '</span>
            <span class="news-content">' . cutMe($row['content'], 240, '...') . '</span>
            <a class="read-more" href="news.php?id=' . $row['id'] . '">Czytaj więcej...</a>
          </div>
        </div>';
      }

      echo '<div class="news-btn">
        <a href="?p=' . ($page - 1) . '" class="newsPage arrow-left"><i class="arrow left"></i>poprzednia strona</a>
  
        <span class="page">strona ' . $page . '</span>

        <a href="?p=' . ($page + 1) . '" class="newsPage arrow-right">następna strona<i class="arrow right"></i></a>
      </div>
    </div>';


    echo '<div class="panel">
      <h3 class="title">Ostatnie mecze</h3>';

        $now_time   = date('Y-m-d H:i:s');
        $last_match = $db -> query('SELECT `team_name_1`, `team_name_2`, `score1`, `score2`, `game`, `date` FROM `matches` WHERE date < "'.$now_time.'" ORDER BY date DESC Limit 2');
        $last_match = $last_match -> fetchAll(PDO::FETCH_ASSOC);

        if($last_match) {
          echo '<table class="last_matches">';
          
          foreach ($last_match as $match )
          {
            echo '<tr>
              <th colspan="3" align="center">
                <img style="filter:invert(100%);" class="game_match" src="img/games/game' . $match['game'] . '.png" alt="game logo">
              </th>
            </tr>
            <tr>
              <td width="45%" align="right"><a href="#" class="special">' . $match['team_name_1'] . '</a></td>
              <td width="10%" align="center">' . $match['score1'] . ':' . $match['score2'] . '</td>
              <td width="45%" align="left"><a href="#" class="special">' . $match['team_name_2'] . '</a></td>
            </tr>
            <tr>
              <td colspan="3" class="date" align="center">' . $match['date'] . '</td>
            </tr>';
          }

          echo '</table>';
        }
        else echo "<h3>Nie odbył się jeszcze żaden mecz :(</h3>";

    echo '</div>
    <div class="panel">
      <h3 class="title">Ranking</h3>
    </div>
    <div class="panel">
      <h3 class="title">Streamy</h3>
      <div class="streamers">

        <div class="stream">
          <a href="https://www.youtube.com/user/MrKOKOSfly">
            <img class="strm" src="img/stream.png" alt="stream screen" />
            Makerio
          </a>
        </div>
        <div class="stream">
          <a href="https://www.youtube.com/user/MrKOKOSfly">
            <img class="strm" src="img/stream.png" alt="stream screen" />
            Makerio
          </a>
        </div>
        <div class="stream">
          <a href="https://www.youtube.com/user/MrKOKOSfly">
            <img class="strm" src="img/stream.png" alt="stream screen" />
            Makerio
          </a>
        </div>

        <div class="clear"></div>
        
      </div>
    </div>
  </div>';

  require_once "includes/footer.php";