<?php
  require_once "includes/header.php";

  if (isset($_POST['add_news'])) {
    if (empty($_POST['title']) || empty($_POST['liga']) || empty($_POST['content'])) 
    {
      header("Location: addnews.php?&error=0");
      exit;
    }


    $date_of_entry = date("Y-m-j");
    $images = '0';
    $add_news =  $db -> Prepare('INSERT INTO `news`(`title`, `author`, `content`, `category`, `date_of_create`, `news_image`) VALUES (:title, :author, :content, :category, :date_of_create, :img)');

    $add_news -> bindParam(":title", $_POST['title'], PDO::PARAM_STR);
    $add_news -> bindParam(":author", $admin['login'], PDO::PARAM_STR);
    $add_news -> bindParam(":content", $_POST['content'], PDO::PARAM_STR);
    $add_news -> bindParam(":category", $_POST['liga'], PDO::PARAM_STR);
    $add_news -> bindParam(":date_of_create", $date_of_entry, PDO::PARAM_STR);
    $add_news -> bindParam(":img", $images, PDO::PARAM_INT);
    $add_news -> Execute();
      
    header("Location: addnews.php?&success");
    exit;

  }
?>

<h1 class="name">KFSE <small>dodaj newsa</small></h1>

<div class="row">
  <div class="s-12">
    <?php
      if (isset($_GET['success']))
      {
        echo '<div class="alert alert-success">News został dodany pomyślnie!</div>';
      }
      if (isset($_GET['error']))
      {
        switch ($_GET['error'])
        {
          case '0':
            echo '<div class="alert alert-danger">Wypełnij wszystkie pola!</div>';
            break;
        }
      }
    ?>
  </div>

  <div class="s-12 wrap" style="padding:10px;">
    <form method="post" action="addnews.php"  enctype="multipart/form-data">
      <div class="row">
        <div class="input-field col s12">
          <input name="title" type="text" class="validate" />
          <label for="email">Tytuł</label>
        </div>
      </div>
      <div class="row">
        <div class="input-field col s6">
          <input type="text" name="liga" value="KFSE league" class="validate" />
          <label>Liga</label>
        </div>
        <!-- <div class="file-field input-field col s6">
          <div class="btn">
            <span>File</span>
            <input type="file" name="photo" size="25">
          </div>
          <div class="file-path-wrapper">
            <input class="file-path validate" name="photoss" type="text">
          </div>
        </div> -->
      </div>
      <div class="row">
        <div class="col s12">
          <label>Tresć newsa</label>
          <textarea name="content" class="materialize-textarea"></textarea>
        </div>
      </div>

      <button type="submit" class="btn btn-default" name="add_news">Dodaj</button>
      <button type="reset" class="btn btn-default">Wyczyść</button>
    </form>
  </div>

  <style>
    .input-field input {margin: 0px;}
  </style>
</div>
<?php
  require_once "includes/footer.php";