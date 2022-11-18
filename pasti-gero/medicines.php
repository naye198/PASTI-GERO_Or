<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization");

require_once "connection.php";

if($_SERVER["REQUEST_METHOD"]=="OPTIONS") exit(0);
$jwt = apache_request_headers()["Authorization"];
if(strstr($jwt,"Bearer")) $jwt=substr($jwt,7);
if(JWT::verify($jwt, "12345678")){
header(("HTTP/1.1 401 Unauthorized"));
exit();
}
$metodo = $_SERVER["REQUEST_METHOD"];

switch($metodo){
    case 'GET':
        //consulta
        $c = connection();
        if(isset($_GET['id_medicine'])){
          $s = $c->prepare("SELECT * FROM medicines where id_medicine=:pid");
          $s->bindValue(":pid", $id_medicine);
          $s->execute();
          $s->setFetchMode(PDO::FETCH_ASSOC);
          $r = $s->fetch();
        }else{
          $s = $c->prepare("SELECT * FROM medicines");
          $s->execute();
          $s->setFetchMode(PDO::FETCH_ASSOC);
          $r = $s->fetchAll();
        }
        echo json_encode($r);
        break;

    case 'POST':
        //insertar
        if(!isset($_POST['name_medicine']) || !isset($_POST['hour'])){
        header("HTTP/1.1 400 Bad Request");
        return;
        }
        $c = connection();
        $s = $c->prepare("INSERT INTO medicine(name_medicine, hour) VALUES(:n, :h)");
        $s->bindValue(":n", $_POST['name_medicine']);
        $s->bindValue(":h", $_POST['hour']);
        $s->execute();
        //if($s->rowCount()==0){
          //  header("HTTP/1.1 400 Bad Request");
            //return;
        //}
        echo json_encode(["status"=>"ok", "id_medicine"=>$c->lastInsertId()]);


        break;

    case 'PUT':
         //actualizar
         if(!isset($_GET['name_medicine']) || !isset($_GET['hour'])|| !isset($_GET['id_medicine'])){
          header("HTTP/1.1 400 Bad Request");
          return;
          }
          $c = connection();
          $s = $c->prepare("UPDATE medicines SET name_medicine=:n, hour=:h WHERE id_medicine=id_medicine");
          $s->bindValue(":id", $_GET["id_medicine"]);
          $s->bindValue(":n", $_GET['name_medicine']);
          $s->bindValue(":h", $_GET['hour']);
          $s->execute();
          //if($s->rowCount()==0){
            //  header("HTTP/1.1 400 Bad Request");
              //return;
          //}
          echo json_encode(["status"=>"ok"]);


         break;

    case 'DELETE':
        //eliminar
        if(!isset($_GET['id_medicine'])){
          header("HTTP/1.1 400 Bad Request");
          return;
          }
          $c = connection();
          $s = $c->prepare("DELETE FROM medicines WHERE id_medicine=id_medicine");
          $s->bindValue(":id", $_GET["id_medicine"]);
          $s->execute();
          echo json_encode(["status"=>"ok"]);
            break;
}