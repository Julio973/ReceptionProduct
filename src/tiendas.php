<?php
include_once ('controlador.php');
include('connectwoo.php');
include_once('dbmodel.php');
include ('email_plantilla/plantilla_email.php');
class Tienda{
    function homeDepot($product,bool $emp_de_cero = false,String $clear = ''){
        $cw = new Connectwoo();
        $database = new ConexionBaseDeDatos();
        $database->setDataBase('wp_prueba');
        //$database->setDataBase('dzf443548506536');
        $notificacion = new PlantillaEmail();
        $database->conectarWoo();
        $check = true;
        $archivo = '';
        $data_temp = '';
        $empezar_de_cero = $emp_de_cero;
        $archivo_cambio_precio = '';
        $control_mongo = new Controlador('mongodb');
        $control_auxiliar = new Controlador('mongodb');
        $control_wordpress_market = new Controlador('mongodb');
        $control_mongo->setDataBaseMongo('bodega');
        $control_mongo->setCollection('productos');
        $control_mongo->conectarMongo();
        $productos_wordpress = array();
        $total_productos = count($product->skus);
        if($empezar_de_cero === false){ 
            /*
            $buscar = ['productId' => '206944035'];
            $result = $control_mongo->consultar($buscar);
            foreach ( $result as $test){
                echo 'producto: '.$test->info->productLabel.'<br>';
            }
            $control_mongo->eliminarCollection($buscar);
            $control_mongo->setCollection('productos_nuevos');
            $control_mongo->conectarMongo();
            $result = $control_mongo->consultar();
            foreach ( $result as $test){
                echo 'producto nuevo: '.$test->info->productLabel.'<br>';
            }
            $control_mongo->eliminarCollection($buscar);
            $control_mongo->setCollection('tiendas_productos');
            $control_mongo->conectarMongo();
            $query = array('$and' => array(array('producto_id' => '206944035'),array('tienda' => 'homedepot')));
            $control_mongo->eliminarCollection($query);
            exit();*/
            $total_productos_recividos = 0;
            $total_productos_recividos = count($product->skus);
            $array_producto_nuevo = array();
            $total_cambio_de_precio = 0;
            for($x=0;$x<$total_productos_recividos;$x++){
                $nombre_marca = '';
                if(isset($product->skus[$x]->info->brandName)){
                    $nombre_marca = trim($product->skus[$x]->info->brandName);
                }
                if(strtoupper($nombre_marca) == 'SAMSUNG' || strtoupper($nombre_marca) == 'LG ELECTRONICS' || strtoupper($nombre_marca) == 'AMANA' || strtoupper($nombre_marca) == 'BOSCH' || strtoupper($nombre_marca) == 'WHIRLPOOL' || strtoupper($nombre_marca) == 'GE'){
                    @$product->skus[$x]->categoryID = $product->metadata->categoryID;
                    $cont = 0;
                    $check_producto_nuevo = false;
                    $control_mongo->setCollection('tiendas_productos');
                    $control_mongo->conectarMongo();
                    $query = array('$and' => array(array('producto_id' => $product->skus[$x]->productId),array('tienda' => 'homedepot')));
                    $result = $control_mongo->consultar($query);
                    $check_repetido = false;
                    foreach ( $result as $temp){
                        $check_repetido = true;
                    }
                    if($check_repetido){
                        $control_mongo->setCollection('productos');
                        $control_mongo->conectarMongo();
                        $query = array('productId' => $product->skus[$x]->productId);
                        $result = $control_mongo->consultar($query);
                        $product_temp = array();
                        foreach($result as $temp){
                            $product_temp = $temp;
                        }
                        $precio = $product->skus[$x]->storeSku->pricing->originalPrice;
                        if(isset($product->skus[$x]->storeSku->pricing->specialPrice)){
                            $precio = $product->skus[$x]->storeSku->pricing->specialPrice;
                        }
                        $precio_temp = $product_temp->storeSku->pricing->originalPrice;
                        if(isset($product_temp->storeSku->pricing->specialPrice)){
                            $precio_temp = $product_temp->storeSku->pricing->specialPrice;
                        }
                        //echo 'x='.$x.' '.$precio.' * '.$precio_temp.'<br>';
                        if($precio != $precio_temp){   
                            echo ' x: '.$x.' '.$product->skus[$x]->info->storeSkuNumber.' precio: '.$product->skus[$x]->storeSku->pricing->specialPrice.' preciotemp: '.$product_temp->storeSku->pricing->specialPrice.'<br>';
                            $control_mongo->setCollection('productos_cambio_precios');
                            $control_mongo->conectarMongo();
                            $query = array('productId' => $product->skus[$x]->productId);
                            $result = $control_mongo->consultar($query);
                            $check_repetido_cambio_precio = false;
                            foreach ( $result as $temp){
                                $check_repetido_cambio_precio = true;
                            }
                            if($check_repetido_cambio_precio){
                                $actualiza = array('$set'=> array('storeSku.pricing.specialPrice' => $precio,'estado' => 'pendiente'));
                                $encuentra = array('productId' => $product->skus[$x]->productId);
                                $control_mongo->actualizar($encuentra,$actualiza);
                            }else{
                                @$product_temp->storeSku->pricing->specialPrice = $precio;
                                @$product_temp->estado = 'pendiente';
                                $control_mongo->insertarDatos([$product_temp]);
                            }
                            $total_cambio_de_precio++;
                        }
                    }else{
                        //$check_producto_nuevo = true;
                        $control_mongo->setCollection('productos_nuevos');
                        $control_mongo->conectarMongo();  
                        $query = array('productId' => $product->skus[$x]->productId);
                        $result = $control_mongo->consultar($query);
                        $check_existe = false;
                        foreach ( $result as $marca_temp){
                            $check_existe = true;
                        }
                        if(!$check_existe){
                            $check_producto_nuevo = true;
                            $nuevo_producto = $product->skus[$x];
                            @$nuevo_producto->estado = 'pendiente';
                            @$nuevo_producto->tiendas = array();
                            $nuevo_producto->tiendas[] = 'homedepot';
                            $array_producto_nuevo[] = $nuevo_producto;
                        }                  
                    }
                }
            }
            $total_producto_nuevo = count($array_producto_nuevo);
            if($total_cambio_de_precio > 0 || $total_producto_nuevo > 0){
                if( $total_producto_nuevo > 0){
                    $control_mongo->setCollection('productos_nuevos');
                    $control_mongo->conectarMongo();  
                    $control_mongo->setDatos($array_producto_nuevo);
                    $control_mongo->insertarDatos();
                }
                $para = 'residente007@yahoo.es';
                $asunto = 'Sometime products changes';
                $body = $notificacion->plantillaUno($total_cambio_de_precio, $total_producto_nuevo);
                $header = 'From: julio.herazo@admintaxi.com';
                echo $body;
                //mail($para, $asunto, $body,$header);
            }  
        }else{
            $control_wordpress_market->setDataBaseMongo('wp_market');
            $productos_para_insertar_mongo = array();
            $control_wordpress_market->setCollection('productos');
            $control_wordpress_market->conectarMongo();
            /*$control_mongo->setCollection('tiendas_productos');
            $control_mongo->conectarMongo();
            $query = array('bodega_id' => new MongoDB\BSON\ObjectID('5f638dcc924b00008e00534c'));
            $query = array('bodega_id' => '5f638dcc924b00008e00534c');
            $result = $control_mongo->consultar();
            foreach ( $result as $marca_temp){
                //echo $marca_temp->name.': ['.implode(',',(array)$marca_temp->term).'], ';
                echo $marca_temp->producto_id.', ';
            }*/
            if($clear === 'empty'){
        
                $control_wordpress_market->eliminarCollection();
                $control_wordpress_market->setCollection('marcas');
                $control_wordpress_market->conectarMongo();
                $result = $control_wordpress_market->consultar();
                foreach ( $result as $test){
                    echo 'marcas : '.$test->brandName;
                }
                $control_wordpress_market->eliminarCollection();
                $control_wordpress_market->setCollection('atributos');
                $control_wordpress_market->conectarMongo();
                $result = $control_wordpress_market->consultar();
                foreach ( $result as $test){
                    echo 'atributo: '.$test->name.' id:'.$test->wp_id;
                }
                $control_wordpress_market->eliminarCollection();
                $control_mongo->setCollection('productos');
                $control_mongo->conectarMongo();
                $result = $control_mongo->consultar();
                foreach ( $result as $test){
                    echo 'bodega: '.$test->productId;
                }
                $control_mongo->eliminarCollection();
                $control_mongo->setCollection('tiendas_productos');
                $control_mongo->conectarMongo();
                $result = $control_mongo->consultar();
                foreach ( $result as $test){
                    echo 'tien-prod: '.$test->tienda;
                }
                $control_mongo->eliminarCollection();
                $control_mongo->setCollection('productos_cambio_precios');
                $control_mongo->conectarMongo();
                $result = $control_mongo->consultar();
                foreach ( $result as $test){
                    echo 'cambio-precio: '.$test->info->productLabel;
                }
                $control_mongo->eliminarCollection();
                $control_mongo->setCollection('productos_nuevos');
                $control_mongo->conectarMongo();
                $result = $control_mongo->consultar();
                foreach ( $result as $test){
                    echo 'Producto Nuevo'.$test->info->productLabel;
                }
                $control_mongo->eliminarCollection();
                exit();
            }
            for($x=0;$x<$total_productos;$x++){
                $control_mongo->setCollection('tiendas_productos');
                $control_mongo->conectarMongo();
                $query = array('$and' => array(array('producto_id' => $product->skus[$x]->productId),array('tienda' => 'homedepot')));
                $result = $control_mongo->consultar($query);
                $check_repetido = false;
                foreach ( $result as $marca_temp){
                    $check_repetido = true;
                }
                if(!$check_repetido){
                    $control_mongo->setCollection('productos');
                    $control_mongo->conectarMongo();
                    $id_categoria = '';
                    if(isset($product->skus[$x]->_id)){
                        unset($product->skus[$x]->_id);
                        //$product->skus[$x] = array_values($product->skus[$x]);
                    }
                    if(isset($product->metadata->categoryID)){
                        $id_categoria = $product->metadata->categoryID;
                        @$product->skus[$x]->categoryID = $id_categoria;
                    }else{
                        $id_categoria = $product->skus[$x]->categoryID;
                    }
                    $control_wordpress_market->setCollection('marcas');
                    $control_wordpress_market->conectarMongo();
                    if(isset($product->skus[$x]->info->brandName)){
                        $nombre_marca = trim($product->skus[$x]->info->brandName);
                        if(strtoupper($nombre_marca) == 'SAMSUNG' || strtoupper($nombre_marca) == 'LG ELECTRONICS' || strtoupper($nombre_marca) == 'AMANA' || strtoupper($nombre_marca) == 'BOSCH' || strtoupper($nombre_marca) == 'WHIRLPOOL' || strtoupper($nombre_marca) == 'GE'){
                            $query = array('brandName' => $nombre_marca);
                            $result = $control_wordpress_market->consultar($query);
                            $check_marcas = false;
                            $id_marca = '';
                            foreach ( $result as $marca_temp){
                                $check_marcas = true;
                                $id_marca = $marca_temp->wp_id;
                            }
                            if(!$check_marcas){
                                $database->crearMarca($nombre_marca);
                                $id_marca = $database->getIdMarca();
                                $insertar = [array('brandName' => $nombre_marca,'wp_id' => "".$id_marca)];
                                $control_wordpress_market->setDatos($insertar);
                                $control_wordpress_market->insertarDatos($insertar);
                            }
                            $precio = $product->skus[$x]->storeSku->pricing->originalPrice;
                            if(isset($product->skus[$x]->storeSku->pricing->specialPrice)){
                                $precio = $product->skus[$x]->storeSku->pricing->specialPrice;
                            }
                            $topAttributes = $product->skus[$x]->info->topAttributes;
                            $total_topAttributes = count($topAttributes);
                            $control_wordpress_market->setCollection('atributos');
                            $control_wordpress_market->conectarMongo();
                            $att = array();
                            $value_term = array();
                            //aquí se verifica los atributos de cada producto
                            $descripcion = '<ul>';
                            for($at=0;$at < $total_topAttributes; $at++){
                                $nombre_atributo = trim("".$topAttributes[$at]->name);
                                $query = array('name' => $nombre_atributo);
                                $result = $control_wordpress_market->consultar($query);
                                $check_atributo = false;
                                $valor_atributo = '';
                                foreach ( $result as $atributo_temp){
                                    $check_atributo = true;
                                    $wp_atributo_id = $atributo_temp->wp_id;
                                    $valor_atributo = '-';
                                    if(isset($topAttributes[$at]->attributeValues[0]->attributeValue)){
                                        $valor_atributo = "".$topAttributes[$at]->attributeValues[0]->attributeValue;
                                    }
                                    $indice = array_search($valor_atributo, (array)$atributo_temp->term);
                                    //array_search devuelve un false o un número. El siguiente if valida si $indice es un boleano, lo que se asume que es false, de lo contrario $indice sería número
                                    $term = ['name' => $valor_atributo];
                                    if(is_bool($indice)){                                
                                        if(!$cw->setTermAtributo($wp_atributo_id,$term)){
                                            echo "Problemas con guardar Term";
                                            exit();
                                        }
                                        $actualiza = (array)$atributo_temp->term;
                                        array_push($actualiza, $valor_atributo);
                                        $actualiza = array('$set'=>array('term' => $actualiza));
                                        $encuentra = array('name' => $atributo_temp->name);
                                        $control_wordpress_market->actualizar($encuentra,$actualiza,array('upsert'=>true));
                                    }
                                    $term = [$valor_atributo];
                                    $att[] = ["id"=> "".$wp_atributo_id,"options"=> $term];
                                }
                                //el siguiente if crea un atributo en caso no exista
                                if(!$check_atributo){
                                    if($cw->setAtributo(array("name"=> substr($nombre_atributo,0,28)))){
                                        $wp_atributo_id = $cw->getIdAtributo();
                                        $valor_atributo = '-';
                                        if(isset($topAttributes[$at]->attributeValues[0]->attributeValue)){
                                            $valor_atributo = "".$topAttributes[$at]->attributeValues[0]->attributeValue;
                                            $term = [
                                               "name" =>$valor_atributo,
                                            ];
                                        }
                                        $term = ['name' => $valor_atributo];
                                        if(!$cw->setTermAtributo($wp_atributo_id,$term)){
                                            echo "Problemas con guardar Term";
                                            exit();
                                        }
                                        $term = [$valor_atributo];
                                        $att[] = ["id"=> "".$wp_atributo_id,"options"=> $term];
                                        $actualiza = array('$set' => array('term' => $term, 'wp_id' => $wp_atributo_id));
                                        $encuentra = array('name' => $nombre_atributo);
                                        $control_wordpress_market->actualizar($encuentra,$actualiza,array('upsert' => true));
                                    }else{
                                        echo '500 No se guardó el atributo';
                                        exit();
                                    }
                                }
                                $descripcion .= '<li>'.$nombre_atributo.": <strong>".$valor_atributo."</strong></li>";
                            }
                            $descripcion .= '</ul>';
                            //preparamos para insertar producto
                            $array_img = array();
                            //condicion para que analice las imagenes de cada producto
                            $imagen_url = array("src"=>str_replace('<SIZE>','400',$product->skus[$x]->info->imageUrl)); //se declara la variable y el tamaño para la imagen
                            array_push($array_img, $imagen_url);
                            if(isset($product->skus[$x]->info->secondaryimageUrl)){ //se verifa la existenia de segunda imagen , si esta que la muestre y sino que lo salte
                                $imagen_url = array("src"=>str_replace('<SIZE>','400',$product->skus[$x]->info->secondaryimageUrl));
                                array_push($array_img, $imagen_url); //relacion entre array
                            }
                            $producto = array(); //array por cada product
                            $nombre_producto = $product->skus[$x]->info->brandName.' '.$product->skus[$x]->info->productLabel;
                            
                            if(isset($product->skus[$x]->info->storeSkuNumber)){
                                $producto = [
                                'display'=> $product->skus[$x]->info->brandName,
                                'name' => $nombre_producto,
                                'type' => 'simple',
                                'price'=> "".$precio,
                                'regular_price'=> "".$precio,
                                'sale_price'=> "".($precio * 0.85),
                                'description' => $descripcion,
                                'short_description' => $descripcion,
                                'sku' => "".$product->skus[$x]->info->storeSkuNumber,
                                'categories' => [
                                    [
                                      'id' => $id_categoria,
                                    ],
                                 ],
                                 'attributes' => $att,
                                 'images' => $array_img,
                                ];
                            }else{
                                $producto = [
                                'display'=> $product->skus[$x]->info->brandName,
                                'name' => $nombre_producto,
                                'type' => 'simple',
                                'price'=> "".$precio,
                                'regular_price'=> "".$precio,
                                'sale_price'=> "".($precio * 0.85),
                                'description' => $descripcion,
                                'short_description' => $descripcion,
                                'categories' => [
                                    [
                                      'id' => $id_categoria,
                                    ],
                                 ],
                                 'attributes' => $att,
                                 'images' => $array_img,
                                ];
                            }
                            if(!$cw->send($producto)){
                                $check = false;
                                break;
                            }else{
                                $id_product =  "".$cw->getIdProduct();
                                @$product->skus[$x]->id_universal = $nombre_marca.$product->skus[$x]->info->modelNumber;
                                @$product->skus[$x]->woocomerce_id = $id_product;
                                $unidad = [$product->skus[$x]];
                                $_id = $control_mongo->insertarDatos($unidad);
                                $_id = $_id->getInsertedId();
                                $control_mongo->setCollection('tiendas_productos');
                                $control_mongo->conectarMongo();
                                $unidad = [['tienda' => 'homedepot','bodega_id' => $_id, 'producto_id' => $product->skus[$x]->productId]];
                                $control_mongo->insertarDatos($unidad);
                                $producto["wp_id"] = $id_product;
                                $product->bodega_id = $_id;
                                $database->relacionProductoMarca($id_product,$id_marca);
                            }
                            $productos_wordpress[] = $producto; 
                        }
                    }
                }
            }
            if(count($productos_wordpress)!= 0){
                $control_wordpress_market->setCollection('productos');
                $control_wordpress_market->conectarMongo();
                $control_wordpress_market->insertarDatos($productos_wordpress);
            }
        }      
        return $check;
    }
}
?>