<?php
  require_once "includes/header.php";

  $get_news = (isset($_GET['id']) ? $_GET['id'] : 0);

  $news =  $db -> Prepare('SELECT * FROM news WHERE id = :id');
  $news -> bindParam(":id", $get_news, PDO::PARAM_INT);
  $news -> Execute();
  $news =  $news -> fetch(PDO::FETCH_ASSOC);

  if (!empty($_GET['delete']))
  {
    $delete_news =  $db -> Prepare('DELETE FROM `news` WHERE id = :id');
    $delete_news -> bindParam(":id", $_GET['delete'], PDO::PARAM_INT);
    $delete_news -> Execute();

    header("Location: editnews.php?success=0");
    exit;
  }

  if($news)
  {
    if (isset($_POST['edit_news'])) {
      if (empty($_POST['title']) || empty($_POST['liga']) || empty($_POST['content'])) 
      {
        header("Location: editnews.php?id=" . $get_news . "&error=0");
        exit;
      }

      $update_news =  $db -> Prepare('UPDATE news SET title = :title, category = :liga, content = :content WHERE id = :id');
      $update_news -> bindParam(":title", $_POST['title'], PDO::PARAM_STR);
      $update_news -> bindParam(":liga", $_POST['liga'], PDO::PARAM_STR);
      $update_news -> bindParam(":content", $_POST['content'], PDO::PARAM_STR);
      $update_news -> bindParam(":id", $get_news, PDO::PARAM_INT);
      $update_news -> Execute();

      header("Location: editnews.php?id=" . $get_news . "&success=0");
      exit;
    }
    
    echo '<h1 class="name">Edit news <small>' . $news['title'] . '</small></h1>';

    if (isset($_GET['error'])) 
    {
      switch ($_GET['error']) 
      {
        case 0:
          echo '<h3 class="register-serror">Uzupełnij wszystkie pola</h3>';
          break;
      }
    }

    if (isset($_GET['success'])) 
    {
      switch ($_GET['success']) 
      {
        case 0:
          echo '<h3 class="register-success">Poprawnie zaktualizowałeś newsa!</h3>';
          break;
      }
    }

    echo '<div class="wrap">
      <form method="post" action="">
        <div class="row">
          <div class="input-field col s12">
            <input name="title" type="text" value="' . $news['title'] . '" />
            <label>Tytuł</label>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12">
            <input type="text" name="liga" value="' . $news['category'] . '" />
            <label>Liga</label>
          </div>
        </div>
        <div class="row">
          <div class="col s12">
            <label>Tresć newsa</label>
            <textarea rows="4" name="content" style="height: initial;">' . $news['content'] . '</textarea>
          </div>
        </div>

        <button type="submit" class="btn btn-default" name="edit_news">Aktualizuj</button>
      </form>
    </div>';
  }
  else
  {
    echo '<h1 class="name">Newsy <small>lista</small></h1>';
    // news list
    $all_news =  $db -> Prepare('SELECT * FROM news');
    $all_news -> Execute();
    $all_news =  $all_news -> fetchAll();

    if (isset($_GET['success'])) 
    {
      switch ($_GET['success']) 
      {
        case 0:
          echo '<h3 class="register-success">Poprawnie usunięto newsa!</h3>';
          break;
      }
    }

    if(!$all_news) echo '<div class="wrap">Brak newsów do wyświetlenia</div>';
    else
    {
      echo '<div class="wrap">
        <div class="table-responsive">
          <table class="display" id="dataTables-example">
            <thead>
              <tr>
                <th>Lp</th>
                <th>Tytuł</th>
                <th>Data</th>
                <th>Akcja</th>
              </tr>
            </thead>
            <tbody>';
              $ile_druzyn=0;
              foreach ($all_news as $row)
              {
                $ile_druzyn++;
                echo '<t><td>' . $ile_druzyn . '</td><td>' . $row['title'] . '</td><td>' . $row['date_of_create'] . '</td><td>
                  <a href="editnews.php?id=' . $row['id'] . '">edytuj</a> / <a href="editnews.php?delete=' . $row['id'] . '">usuń</a>
                </td></tr>';
              }
      echo '</tbody>
          </table>
        </div>
      </div>';
    }

  }

  require_once "includes/footer.php";
  require_once "includes/foot-js.php";