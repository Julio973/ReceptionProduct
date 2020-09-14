<?php
include('archivos.php');
include_once ('controlador.php');
include('connectwoo.php');
include_once('dbmodel.php');
class Tienda{
	function homeDepot($product){
        $cw = new Connectwoo();
        $database = new ConexionBaseDeDatos();
        $database->setDataBase('wp_prueba');
        $database->conectarWoo();
        $check = true;
        $archivo = '';
		$data_temp = '';
        $empezar_de_cero = true;
        $archivo_cambio_precio = '';
        $control_mongo = new Controlador('mongodb');
        $control_auxiliar = new Controlador('mongodb');
        $control_wordpress_market = new Controlador('mongodb');
        $control_mongo->setDataBaseMongo('productos');
        $control_wordpress_market->setDataBaseMongo('wordpress_market');
        $productos_wordpress = array();
        $total_productos = count($product->skus);
        if($empezar_de_cero){
            $productos_cambio_precios = array();
            for($x=0;$x<$total_productos;$x++){
                @$product->skus[$x]->categoryID = $product->metadata->categoryID;
                if($product->skus[$x]->info->brandName == 'Samsung'){
                    $productos_wordpress[] = $product->skus[$x]; 
                }
            }
            $control_mongo->setDatos($product->skus);
            //$control_wordpress_market->setDatos($productos_wocategoriaId
            //$control_mongo->insertarDatos();
            //$control_wordpress_market->insertarDatos();
            //$control_mongo->eliminarCollection();
            //$control_wordpress_market->eliminarCollection();*/
            $query = array('categoryID' => "".$product->metadata->categoryID );
            $result = $control_mongo->consultar($query);
            $cont = 0;
            foreach ( $result as $product)
            {
                $query = array('productId' => "".$product->metadata->$product->skus);
                $control_mongo->consultar($query);
                $con++;
            }
            //var_dump($result);
            //echo "Total = ".$result['categoryID']." ";
            /*foreach ( $result as $categoryID => $valor )
            {
                $total = count($valor);
                for($q = 0;$q<$total;$q++){
                    if(isset($product->skus[$q]->productId)){
                        echo $valor[$q]->productId.', ';
                        
                        $precio_json = $product->skus[$q]->storeSku->pricing->originalPrice;
                        $precio_database = $valor[$q]->storeSku->pricing->originalPrice;
                        if(isset($product->skus[$q]->storeSku->pricing->specialPrice)){ 
                            $precio_json = $product->skus[$q]->storeSku->pricing->specialPrice; 
                        }
                        if(isset($valor[$q]->storeSku->pricing->specialPrice)){ 
                            $precio_database = $valor[$q]->storeSku->pricing->specialPrice; 
                        }
                        if($precio_json != $precio_database){
                            $productos_cambio_precios[] = $product->skus[$q];
                        }
                    }
                }
            }*/
        }      
        return $check;
    }
}
?>