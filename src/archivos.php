<?php

class ArchivoTm{
    private $contenido= "";
    private $nombrearchivo ="default.txt" ;


    function __construct() {
      //  $cw = new ArchivoTm();
    }
    function setNombreArchivo($nombrearchivo){  //recibo
        $this->nombrearchivo=$nombrearchivo;

    }
    function getNombreArchivo(){  //obtengo
        return $this->nombrearchivo;
    }
    function setContenido($contenido){  //recibo
        $this->contenido = $contenido;

    }
    function getContenido(){
        return $this->contenido;
    }
    function leerArchivo(){
        $slinea = '';
        if(file_exists($this->getNombreArchivo())){
            $re = fopen($this->getNombreArchivo(), "r");
            while(!feof($re)){
               $obtener = fgets($re);
               $slinea .= $obtener;//para ver los salto de liena
            }
            fclose($re);
        }else{
            $slinea = false;  
        }
        return $slinea;
    }
    function escribir(){
        $ar = fopen($this->getNombreArchivo(),"w");//or die("Error al crear");
        if($ar != false){
            fwrite($ar, $this->getContenido());
            fclose($ar); //cerrar
            return true;
        }else{
            return false;
        }
    }
    function adicionarContenido(){
        $ar = fopen($this->getNombreArchivo(),"a");//or die("Error al crear");
        if($ar != false){
            fwrite($ar, $this->getContenido());
            fclose($ar); //cerrar
            return true;
        }else{
            return false;
        }    
    }
 }
?>
