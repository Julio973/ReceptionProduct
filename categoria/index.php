<?php 
include("../src/receptionproducts.php");
include '../src/controlador.php';
if(isset($_POST['key_secury'])){
    $auth = new Autenticacion();
    $key_secury = $_POST['key_secury'];
    $control = new Controlador();
    if($control->signIn($key_secury)){
        $rp = new  ReceptionProduct();
        echo(json_encode($rp->getCategoria()));
    }else{
        echo 'No Autherized'; 
    }
}else{
    echo 'No Autherized';
}

 //se logea, para dar permisoss de autorizacion 
/*if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="reciveProduct"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'No estas autorizado para entrar';
    exit;
} else {
    $usuario = 'dayana';
    $pass = 'php';
    if ($_SERVER['PHP_AUTH_USER'] == $usuario && $_SERVER['PHP_AUTH_PW'] == $pass)
    {

        $rp = new  ReceptionProduct();
        $rp->getCategoria();
        //print_r(json_encode($rp->getCategoria()));
    }else{
        echo 'Acceso incorrecto';
    }
}*/

?>