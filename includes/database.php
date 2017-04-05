<?php
try
{
  $db = new PDO("mysql:host=localhost;dbname=watergfx_liga2;charset=utf8","watergfx_liga2","dupa123", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
  echo 'Połączenie nie mogło zostać utworzone: ' . $e->getMessage();
}