<?php

require 'vendor/autoload.php';
require 'config.php';

if ($_POST && $_POST['password'] == $senha){
    
    $candidatos = 0;
    $empregadores = 0;
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn2 = new mysqli($servername, $username, $password, $dbname2);
        
    $sql = "SELECT * FROM wpm7_users";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {

        $id = $row['ID'];
        $sql = "SELECT meta_value FROM wpm7_usermeta WHERE user_id = '$id' AND meta_key='wpm7_capabilities'";
        $cargo = $conn->query($sql);  

        while($c = $cargo->fetch_assoc()) {
           if ($c['meta_value'] == 'a:1:{s:9:"candidate";b:1;}'){
               $candidatos ++;
           }
           if ($c['meta_value'] == 'a:1:{s:8:"employer";b:1;}'){
               $empregadores ++;
           }
           
        }

      }
    } else {
    }
    $conn->close();
    $novoc = 0;
    $novoe = 0;
    $sql2 = "SELECT * from numeros";
    $action = $conn2->query($sql2);

    while($row = $action->fetch_assoc()) {
         $novoc = $candidatos - $row['candidatos'];
         $novoe = $empregadores - $row['empregadores'];
         $dia = $row['data'];
    }

    $sql2 = "TRUNCATE TABLE numeros";
    $action = $conn2->query($sql2);  
    $sql2 = "INSERT INTO numeros (candidatos, empregadores) VALUES ($candidatos, $empregadores)";
   
    $action = $conn2->query($sql2);  
    
    $books = [
        ['Número de Candidatos', 'Número de Empregadores', 'Novos Candidatos', 'Novos Empregadores'],
        [$candidatos, $empregadores,  $novoc, $novoe]
    ];
    $xlsx = SimpleXLSXGen::fromArray( $books );
    $xlsx->downloadAs('Relatório.xlsx'); 
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Varejo Contrata</title>
</head>
<body>
    <form method="post">
        <p>Insert password</p>
        <input type="text" name="password"/>
        <input type="submit"/>
    </form>
</body>
</html>