<?php
include_once 'jwt.php';
require_once 'dbmodel.php';
require_once 'mongodb.php';
class Controlador{ 
	private $database = '';
	private $auth = '';
	private $datos = '';
	private $database_mongo = '';
	private $nombre_db_mongo = '';
	function __construct($tipo,$database_name = null) {
        if($tipo === 'mysql'){
        	$this->conectarSQL();
        }
        if($tipo === 'mongodb'){
        	if($database_name === null){
        		$this->setDataBaseMongo('productos');
        		$this->conectarMongo();
        	}else{
        		$this->setDataBaseMongo($database_name);
        		$this->conectarMongo();
        	}
        }
    }
    private function conectarSQL(){
    	$this->auth = new Autenticacion();
        $this->database = new ConexionBaseDeDatos();
        $this->database->setDatabase("receptionproduct_bswapp");
		$this->database->conectar();
    }
	function signIn($token){
		$check = false;
		$array = array();
		try{
			$array = $this->auth->getData($token);
			$check = true;
		}catch(Exception $e){
			$check = false;
		}
		if($check){
			if($this->database->signIn($array->usuario,$array->password)){
				$check = true;
			}
			$this->database->autoCerrar();
		}
		return $check;
	}
	function setDataBaseMongo($nombre_db){
		$this->nombre_db_mongo = $nombre_db;
	}
	function getDataBaseMongo(){
		return $this->nombre_db_mongo;
	}
	private function conectarMongo(){
		$this->database_mongo = new MongoDataBase();
		$this->database_mongo->setDataBase($this->getDataBaseMongo());
		$this->database_mongo->conectar();
		$this->database_mongo->accederCollection();
	}
	function setDatos($datos){
		$this->datos = $datos;
	}
	function getDatos(){
		return $this->datos;
	}
	function insertarDatos(){
		$this->database_mongo->setDatos($this->getDatos());
		$this->database_mongo->insertar();
	}
	function consultar($array = null){
		$coleccion = '';
		if($array === null){
			$coleccion = $this->database_mongo->consultar();
		}else{
			$coleccion = $this->database_mongo->consultar($array);
		}
		return $coleccion;
	}
	function eliminarCollection(){
		return $this->database_mongo->eliminarColection();	
	}
}
?>