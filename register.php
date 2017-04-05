<?php
  require_once "includes/header.php";

  if (isset($_SESSION['username']))
  {
    header('Location: ./');
    exit;
  }

  require_once "includes/menu.php";

  echo '
  <div class="container">
    <h1>Rejestracja konta</h1>
    <div class="wrap after">';

    if (isset($_GET['error'])) 
    {
      switch ($_GET['error']) 
      {
        case 0:
          echo '<h3 class="register-error">Uzupełnij wszystkie pola!</h3>';
          break;

        case 1:
          echo '<h3 class="register-error">Podane hasła nie są identyczne!</h3>';
          break;
        
        case 2:
          echo '<h3 class="register-error">Podany Użytkownik już istnieje!</h3>';
          break;

        case 3:
          echo '<h3 class="register-error">Nie zaakceptowałeś regulaminu!</h3>';
          break;

        case 4:
          echo '<h3 class="register-error">Podane emaile nie są identyczne!</h3>';
          break;

        case 5:
          echo '<h3 class="register-error">Podany email jest nieprawidłowy!</h3>';
          break;
        case 6:
          echo '<h3 class="register-error">Błąd przy identyfikacji captcha!</h3>';
          break;

        default:
          echo "Coś poszło nie tak jak powinno!";
          break;
      }
    }

    if (isset($_GET['success'])) 
    {
      switch ($_GET['success']) 
      {
        case 0:
          echo '<h3 class="register-success">Poprawnie Utworzyłeś konto !</h3>';
          break;
      }
    }

  echo '<form action="login.php" method="POST" id="register_form">
        <div class="list">
          <label>Nazwa użytkownika</label>    
          <input class="text" maxlength="32" type="text" name="username_register" />
        </div>
        <div class="list">
          <label>http://steamcommunity.com/id/</label>    
          <input class="text" maxlength="32" type="text" name="steamid" />
        </div>
        <div class="list">
          <label>Hasło</label>    
          <input class="text" maxlength="32" type="password" name="password_register" />
        </div>
        <div class="list">
          <label>Hasło (ponownie)</label>    
          <input class="text" maxlength="32" type="password" name="password_register_replace" />
        </div>
        <div class="list">
          <label>Email</label>    
          <input class="text" maxlength="50" type="email" name="email" />
        </div class="list">
        <div class="list">
          <label>Email (ponownie)</label>    
          <input class="text" maxlength="50" type="email" name="email_replace" />
        </div>
        <div class="list">
          <label>Jesteś robotem?</label>  
          <div class="g-recaptcha" data-sitekey="6Le23hkUAAAAAOWdsYY7yZp1kpiARzbcHkxiOOcw"></div>
        </div>
        <div class="list">
          <label>Akcpetuje <a href="rules.php" target="_blank" class="special">regulamin</a> panujący w tym serwisie *</label>    
          <input type="checkbox" name="accept" />
        </div>
        <input type="submit" class="button" value="Zarejestruj" name="register" />
        <input type="reset" class="button" value="Wyczyść Formularz" />
      </form>
    </div>
  </div>';

  require_once "includes/footer.php";