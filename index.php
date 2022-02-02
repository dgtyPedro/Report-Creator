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
        ['N煤mero de Candidatos', 'N煤mero de Empregadores', 'Novos Candidatos', 'Novos Empregadores'],
        [$candidatos, $empregadores, $novoc, $novoe]
    ];
    $xlsx = SimpleXLSXGen::fromArray( $books );
    $xlsx->downloadAs('Report.xlsx'); 
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
html {
    background: #083c64;
    color:white;
}

input[type="password"] {
    font-size:30px;
}

body {
  width: 500px;
  margin: 0 auto;
  padding-top: 30vh;
  font-family: Tahoma, sans-serif;
  
}

fieldset {
  position: relative;
  display: inline-block;
  padding: 0 0 0 40px;
  background: #fff;
  border: none;
  border-radius: 5px;
  
}

input,
button {
  position: relative;
  
  height: 50px;
  padding: 0;
  display: inline-block;
  float: left;
  
}

input {
    width: 300px;
  color: #666;
  z-index: 2;
  border: 0 none;
}
input:focus {
  outline: 0 none;
}


button {
  z-index: 1;
  width: 200px;
  border: 0 none;
  background: #fcb434;
  cursor: pointer;
  border-radius: 0 5px 5px 0;  
  color: #083c64;
  font-size:20px;
  font-weight: bolder;
  /* -webkit-transform: translate(-50px, 0);
      -ms-transform: translate(-50px, 0);
          transform: translate(-50px, 0);
  -webkit-transition-duration: 0.3s;
          transition-duration: 0.3s; */
}
a{
    text-decoration: none;
    color: white;
}

a:visited{
    text-decoration: none;
    color: white;
}

</style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relat贸rio Varejo Contrata</title>
</head>
<body>
<h1>Relat贸rios Varejo Contrata</h1>
    <form method="post">
        
        <p>Insira a senha / <a href="https://varejocontrata.com.br/wp-admin/">Voltar</a></p>
        
        <input type="password" name="password"/>
        <button type="submit">Gerar</button>
    </form>
</body>
</html>