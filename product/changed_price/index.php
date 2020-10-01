<?php
include("../../src/receptionproducts.php");
include_once '../../src/controlador.php'; 
if(isset($_GET['token'])){
    $token = $_GET['token'];
    $control = new Controlador('mysql');
    $rp = new ReceptionProduct();
    if($control->signIn($token)){
      if(file_get_contents('php://input') !== ''){
        $data = file_get_contents('php://input'); //atrapa desde postman 
        $data = json_decode($data);
        $rp->setNewPrice($data);
      }else{
        echo $rp->getChangedPrice(array('estado'=>'pendiente'));
      }
    }else{
        echo 'No Authorized';
    }
}else{
    echo 'No token exist';
}
?>