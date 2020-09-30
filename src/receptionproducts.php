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
    function getChangedPrice($array = null){
       $control_mongo = new Controlador('mongodb'); 
       $control_mongo->setDataBaseMongo('bodega');
       $control_mongo->setCollection('productos_cambio_precios');
       $control_mongo->conectarMongo();
       $result = array();
       if($array === null){
          $result = $control_mongo->consultar();       
       }else{
          $result = $control_mongo->consultar($array);
       }
       $productos = array();
       foreach ( $result as $temp){
          $productos[] = $temp;
       }
       return json_encode($productos);
    }
    function discard(String $wp_id){
       $check = false;
       $control_mongo = new Controlador('mongodb'); 
       $control_mongo->setDataBaseMongo('bodega');
       $control_mongo->setCollection('productos_cambio_precios');
       $control_mongo->conectarMongo();
       $encuentra = array('woocomerce_id' => $wp_id);
       $control_mongo->eliminarCollection($encuentra);
       return $check;
    }
    function setNewPrice(array $datos){
        $manager = ['update' => $datos];
        $check = $this->cw->updatePrices($manager);
        if($check){
            $control_market = new Controlador('mongodb');
            $control_market->setDataBaseMongo('wp_market');
            $control_market->setCollection('productos');
            $control_market->conectarMongo();
            $control_mongo = new Controlador('mongodb'); 
            $control_mongo->setDataBaseMongo('bodega');
            $control_mongo->setCollection('productos_cambio_precios');
            $control_mongo->conectarMongo();
            $total_cambios = count($datos);
            for($x =0;$x < $total_cambios; $x++){
                $encuentra = array('wp_id' => $datos[$x]['id']);
                $data = ['sale_price' => $datos[$x]['sale_price'],'regular_price' => $datos[$x]['regular_price']];
                $actualiza = array('$set'=> $data);
                $control_market->actualizar($encuentra,$actualiza);
                $encuentra = array('woocomerce_id' => $datos[$x]['id']);
                $control_mongo->eliminarCollection($encuentra);
            }
        }
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