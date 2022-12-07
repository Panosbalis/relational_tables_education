<?php
$pdo=new PDO('mysql:host=localhost;port=3306;dbname=misc2','panos','zip');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>