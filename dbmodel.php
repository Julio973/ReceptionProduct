<?php
class ConexionBaseDeDatos{ 
	private $database = "";		
	private $link = '';
	private $t_marca= 'wp_terms';
	private $t_term_taxonomy = 'wp_term_taxonomy';
	private $t_relationship = 'wp_term_relationships';
	private $t_post = 'wp_posts';
	private $id_marca = '';
	function conectar(){
		$this->link = mysqli_connect("localhost","root","",$this->database)
        or die("No se puede conectar al servidor ").mysql_error();
		$db = @mysqli_select_db($this->link,$this->database)  
        or die("No se puede seleccionar la base de datos"); 
		return $this->link;
	}
	function conectarWoo(){
		$this->link = mysqli_connect("localhost","root","",$this->database)
        or die("No se puede conectar al servidor ").mysql_error();
		$db = @mysqli_select_db($this->link,$this->database)  
        or die("No se puede seleccionar la base de datos"); 
		return $this->link;
	}
	function cerrar($link){
		$link->close();
	}
	function autoCerrar(){
		$this->link->close();
	}
	function setDataBase($db){
		$this->database = $db;
	}
	function signIn($usuario, $password){
		$check = false;
		$password = md5($password);
		$sql = "SELECT id FROM usuarios WHERE usuario = '".$usuario."' AND password = '".$password."' ";
		$handle = mysqli_query($this->link,$sql);
		while ($row = mysqli_fetch_array($handle)){
		  $imagen = $row[0];
		}
		if($handle->num_rows !== 0){
			$check = true;
		}
		return $check;
	}
	function crearMarca($marca){
		$check = false;
		$campos_inserccion = "name,slug";
		$slug = strtolower(str_replace(' ','_', $marca));
		$valores_campos_inserccion = "'".$marca."','".$slug."'";
		$sql = "INSERT INTO ".$this->t_marca." (".$campos_inserccion.") VALUES (".$valores_campos_inserccion.")";
		$handle = mysqli_query($this->link,$sql);
		if($handle){
		    $msg = "Contrato guardado. Puede proceder a generar un archivo PDF.";
		    $tipo_msg = "success";
		    $id_term = mysqli_insert_id($this->link);
		    $this->id_marca = $id_term; 
		    $campos_inserccion = "term_taxonomy_id,term_id,taxonomy";
		    $valores_campos_inserccion = "'".$id_term."','".$id_term."','product_brand'";
		    $sql = "INSERT INTO ".$this->t_term_taxonomy." (".$campos_inserccion.") VALUES (".$valores_campos_inserccion.")";
			$handle = mysqli_query($this->link,$sql);
			if($handle){
			    $tipo_msg = "success";
			    $check = true;
			}else{
			    $sql = "DELETE FROM ".$this->t_marca." WHERE term_id = '".$id_term."'";
				$handle = mysqli_query($this->link,$sql);
			    $msg = "Problemas al generar contrato. Contacte a servicio técnico.";
			    $tipo_msg = "danger";
			}
		}else{
		    //echo $link->error;
		    $msg = "Problemas al generar contrato. Contacte a servicio técnico.";
		    $tipo_msg = "danger";
		}
		return $check;
	}
	function getMarcas(){
		$result = array();
		$sql = "SELECT * FROM ".$this->t_marca." ";
		$handle = mysqli_query($this->link,$sql);
		while ($row = mysqli_fetch_assoc($handle)){
			$result[] = (object)$row;
		}
		return $result;
	}
	/*function obtenerUltimoId(){
		$max_id = 0;
		$sql = "SELECT IFNULL(MAX(id),'0') AS id FROM ".$this->t_post." ";
		$handle = mysqli_query($this->link,$sql);
		while ($row = mysqli_fetch_array($handle)){
		  $max_id = $row[0];
		}
		if($handle->num_rows == 0){
			$max_id = 0;
		}
		return $max_id;	
	}*/
	function getIdMarca(){
		return $this->id_marca;
	}
	function relacionProductoMarca($id_producto,$id_marca){
		$check = false;
		$campos_inserccion = "object_id,term_taxonomy_id";
		$valores_campos_inserccion = "'".$id_producto."','".$id_marca."'";
		$sql = "INSERT INTO ".$this->t_relationship." (".$campos_inserccion.") VALUES (".$valores_campos_inserccion.")";
		$handle = mysqli_query($this->link,$sql);
		if($handle){
			$check = true;
		}
		return $check;
	}
}
?>