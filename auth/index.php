<?php
include '../src/jwt.php';
include '../src/dbmodel.php';
$usuario  = $_POST["usuario"];
$password = $_POST["password"];
$database = new ConexionBaseDeDatos();
$database->setDatabase("receptionproduct_bswapp");
$link = $database->conectar();
if($database->signIn($usuario,$password))
{
    $aut = new Autenticacion();
    $token =  $aut->SignIn([
        'usuario' => $usuario,
        'password'=>$password
    ]);
    $result = ['token'=>$token];
    echo json_encode($result);
}else{
	echo 'No authorized';
}
$database->cerrar($link);
/*if(isset($_GET['t'])){
	$token = $_GET['t'];
	echo '<br>';
	var_dump(
	    $aut->getData(
	        $token
	    )
	);
}*/
?>