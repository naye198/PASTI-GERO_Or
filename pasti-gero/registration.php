<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
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
        if(isset($_GET['id_regis'])){
          $s = $c->prepare("SELECT * FROM registration where id_regis=:pid");
          $s->bindValue(":pid", $id_regis);
          $s->execute();
          $s->setFetchMode(PDO::FETCH_ASSOC);
          $r = $s->fetch();
        }else{
          $s = $c->prepare("SELECT * FROM registration");
          $s->execute();
          $s->setFetchMode(PDO::FETCH_ASSOC);
          $r = $s->fetchAll();
        }
        echo json_encode($r);
        break;

    case 'POST':
        //insertar
        if(!isset($_POST['hour_regis']) || !isset($_POST['medicine_regis'])|| !isset($_POST['confirmation'])){
        header("HTTP/1.1 400 Bad Request");
        return;
        }
        $c = connection();
        $s = $c->prepare("INSERT INTO registration(hour_regis, medicine_regis, confirmation) VALUES(:h, :m, :c)");
        $s->bindValue(":h", $_POST['hour_regis']);
        $s->bindValue(":m", $_POST['medicine_regis']);
        $s->bindValue(":c", date("confirmation"));
        $s->execute();
        //if($s->rowCount()==0){
          //  header("HTTP/1.1 400 Bad Request");
            //return;
        //}
        echo json_encode(["status"=>"ok", "id_regis"=>$c->lastInsertId()]);
        break;
}