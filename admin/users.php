<?php
  require_once "includes/header.php";

  if (isset($_GET['id']))
  {
    $id = $_GET['id'];

    $usun =  $db -> Prepare('DELETE FROM users WHERE id = :id');
    $usun -> bindParam(":id", $id, PDO::PARAM_STR);
    $usun -> execute();

    header("Location : users.php");
    ob_end_flush();
  }

  $users_list =  $db -> Prepare('SELECT * FROM users');
  $users_list -> Execute();
  $users_list =  $users_list -> fetchAll(PDO::FETCH_ASSOC);
?>
    
<h1 class="name">KFSE <small>statystyki strony</small></h1>
<div class="wrap">
  <div class="table-responsive">
  <table class="display" id="dataTables-example">
    <thead>
      <tr>
        <th>Lp</th>
        <th>Login</th>
        <th>Ranga</th>
        <th>IP</th>
        <th>Email</th>
        <th>Data stworzenia</th>
        <th>Usun</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $ile_druzyn=0;
      foreach ($users_list as $row)
      {
        $ile_druzyn++;
    ?>
        <tr class="odd gradeA">
          <td class="center"><?php echo $ile_druzyn; ?></td>
          <td class="center"><a href="../profil.php?user=<?php echo $row['login']; ?>"><?php echo $row['login']; ?></a></td>
          <td class="center">
            <?php
              switch ($row['admin'])
              {
                case '0':
                  echo "Użytkownik";
                  break;
                case '1':
                  echo "Właściciel";
                  break;
                case '2':
                  echo "HeadAdmin";
                  break;
                case '3':
                  echo "Technik";
                  break;

                default:
                  echo "O co tutaj ochodzi?";
                  break;
              }
            ?>
          </td>
          <td class="center"><?php echo $row['ip'];?></td>
          <td class="center"><?php echo $row['email'];?></td>
          <td class="center"><?php echo $row['date_of_entry'];?></td>
          <td class="center"><a href="users.php?id=<?php echo $row['id']; ?>">Usuń</a></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  </div>
</div>
  
<?php
  require_once "includes/footer.php";
  require_once "includes/foot-js.php";