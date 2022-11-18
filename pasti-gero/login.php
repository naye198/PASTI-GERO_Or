<?php

require_once "connection.php";
require_once "jwt.php";

if(isset($_REQUEST['email']) && isset($_REQUEST['password'])){
    $u = $_REQUEST['email'];
    $p = $_REQUEST['email'];
    $c = connection();
    $s = $c->prepare("SELECT name,role FROM users WHERE email=:e AND password=:p");
    $s->bindValue(":e", $e);
    $s->bindValue(":p", md5($p));
    $s->execute();
    $s->setFetchMode(PDO::FETCH_ASSOC);
    $r = $s->fetch();
    if($r){
        $r = [
            "status" => "ok",
            "jwt" => JWT::create($r, "12345678")
        ];
    }else{
        $r = ["status" => "error"];
    }
    echo json_encode($r);
}else{
    header(("HTTP/1.1 400 Bad Request"));
}

//hola