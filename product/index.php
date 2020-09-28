<?php
include("../src/receptionproducts.php");
include_once '../src/controlador.php';/*
if(isset($_POST['key_secury'])){
    $auth = new Autenticacion();
    $key_secury = $_POST['key_secury'];
    $control = new Controlador();
    if($control->signIn($key_secury)){
        $rp = new ReceptionProduct();
        if(file_get_contents('php://input') !== ''){
            $data = file_get_contents('php://input'); //atrapa desde postman 
            $data = json_decode($data);
            $rp->setData($data);   //se hace el procedicimiento
            $rp->processData();
        }else{
            //echo '500 Error server';
            echo(json_encode($rp->getProduct()));
        }
    }else{
        echo 'No authorized';
    }
}else{
    echo 'No data authentication';
}*/ 
/*$token = null;
$headers = apache_request_headers();
if(isset($headers['Authorization'])){
    $matches = array();
    preg_match('/Token token="(.*)"/', $headers['Authorization'], $matches);
    if(isset($matches[1])){
      $token = $matches[1];
    }
}*/
$rp = new ReceptionProduct();
if(file_get_contents('php://input') !== ''){
    $data = file_get_contents('php://input'); //atrapa desde postman 
    $data = json_decode($data);
    $key_secury = $data->token;
    $control = new Controlador('mysql');
    
    if($control->signIn($key_secury)){
        $control = null;
        $rp->setData($data);   //se hace el procedicimiento
        $rp->processData();
    }else{
        echo 'No Authorized';
    }
}else{
    //echo '500 Error server';
    echo(json_encode($rp->getProduct()));
}
?>