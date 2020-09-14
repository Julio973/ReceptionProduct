<?php
//recepcion de productos 
include("tiendas.php");
class ReceptionProduct{
    private $data= "";
    private  $cw ;
    private $tienda;
    function __construct() {
        $this->cw = new Connectwoo();
    }
    function setData($d_json){
            $this->data = $d_json;

        }
    function getData(){
            return $this->data;
        }
    function processData(){
        $tienda = new Tienda();
        $listproduct2 = Array();
        $listproduct = $this->data;
        $check = $tienda->homeDepot($listproduct);  
        if($check == true){
            echo '201 Created';
        }else{
            echo '400 Bad Request';
        } 
    }
    function sendDataToWoocomerce($product){
        return $this->cw->send($product);
    }
    function getProduct(){
        $cw = new Connectwoo();
        return json_encode($cw->getProduct());
    }
    function getCategoria(){
        $categoria = array();
        $cat = array();
        $cw = new Connectwoo();
        $categoria = $cw->getCategoria();
        $total_categoria = count($categoria);
        $descripcion = '';
        for($x =0; $x<$total_categoria;$x++){
            $descripcion = json_decode($categoria[$x]->description);
            if(isset($descripcion->tienda)){
                $cat[] = (object)[
                    "nombre" => $categoria[$x]->name,
                    "url_origen" => $descripcion
                ];
            }
        } 
        return json_encode($cat); 
    }
    function getAtributos(){
        $atributo = array();
        $cw = new Connectwoo();
        $atributo = $cw->getAtributo();
        return json_encode($atributo);
    } 
}
?>