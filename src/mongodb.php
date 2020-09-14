<?php
require '../vendor/autoload.php';

class MongoDataBase{
	private $connection = '';
	private $database = '';
	private $collection = '';
	private $datos = '';
	private $database_name = '';
	function __construct(){
		$this->connection = new MongoDB\Client;
	}
	function conectar(){
		try{
			if($this->database_name == 'productos'){
				$this->database = $this->connection->productos;
			}
			if($this->database_name == 'wordpress_market'){
				$this->database = $this->connection->wordpress_market;
			}
		}catch(MongoConnectionException $e){
			echo 'No se conectó a la base de datos especificada'; 
			exit();
		}
		return $this->database;
	}
	function setDataBase($nombre_database_mongo = null){
		if($nombre_database_mongo === null){
			$this->database_name = 'productos';
		}else{
			$this->database_name = $nombre_database_mongo;
		}
	}
	function setCollection($nombre_coleccion){
		$this->collection = $nombre_coleccion;
	}
	function accederCollection(){
		if($this->database_name == 'productos'){
			$this->collection = $this->database->homedepot;
		}
		if($this->database_name == 'wordpress_market'){
			$this->collection = $this->database->productos_wordpress;
		}
		return $this->collection;
	}
	function setDatos($datos){
		$this->data = $datos;
	}
	function getDatos(){
		return $this->data;
	}
	function insertar(){
		$this->collection->insertMany($this->getDatos());
	} 
	function consultar($array = null){
		$coleccion = '';
		if($array === null){
			$coleccion = $this->collection->find();
		}else{
			$coleccion = $this->collection->find($array);
		}
		return $coleccion;	
	}
	function eliminarColection($array = null){
		if($array === null){
			$this->collection->deleteMany([]);
		}else{
			$this->collection->deleteMany($array);
		}	
	}
	function getListaBasedeDatos(){
		return $this->connection->listDatabases();
	}
	function example(){
	// Connecting specifying host and port
		$connection = new MongoDB\Client('mongodb://localhost:27017');
		$database = $connection->ggvdTest;
		$collection = $database->actor;

		/*$document = array( 'first_name' => 'Elisabeth', 'last_name' => 'Taylor', 'country' => 'UK', 'born' => 1932, 'sex' => 'female' );
		$collection->insertOne($document);

		$document = array( 'first_name' => 'James', 'last_name' => 'Dean', 'country' => 'USA', 'born' => 1931, 'sex' => 'male' );
		$collection->insertOne($document);

		// A mistake is introduced in the first_name of Rock Hudson to update it later
		$document = array( 'first_name' => 'Rod', 'last_name' => 'Hudson', 'country' => 'USA', 'born' => 1925, 'sex' => 'male' );
		$collection->insertOne($document);
		$documents = array(
		  array('first_name' => 'Caroll', 'last_name' => 'Baker', 'country' => 'USA', 'born' => 1931, 'sex' => 'female' ),
		  array( 'first_name' => 'Princess', 'last_name' => 'Leia', 'country' => 'USA', 'sex' => 'female' )
		);
		$collection->insertMany($documents);
		$collection->updateMany(
			array('last_name' => 'Hudson'), 
			array('$set' => array('first_name' => 'Rock'))
		);*/

		/*
		$this->collection->deleteOne(array('last_name' => 'Leia'));

		$result = $collection->find(array('sex' => 'female'));

		echo '<h2>Actresses after updating and deleting bad data</h2>';

		foreach ($result as $document) {
			echo $document['first_name'] . " " . $document['last_name'] . '</br>';
		}
		*/
		$result = $collection->find(array('sex' => 'female'));

		echo '<h2>Actors born before 1930</h2>';
			
		foreach ($result as $document) {
			echo $document['first_name'] . " " . $document['last_name'] . " " . $document['born'] , '</br>';
		}	
	}
}

?>