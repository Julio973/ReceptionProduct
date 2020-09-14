<?php
include('archivos.php');
//include_once ('controlador.php');
include('email_plantilla/plantilla_email.php');
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
        /*$control_mongo = new Controlador('mongodb');
        $query = $control_mongo->setDatos($product->skus);
        //$control_mongo->insertarDatos();
        //$control_mongo->eliminarCollection();
        //$product->skus[$x]->storeSku->pricing->specialPrice;
        $query = array('productId'=> array('$exists' => true));
        //$control_mongo->eliminarCollection(array('skus.productId'=>'311372548'));
        $result = $control_mongo->consultar($query);
        foreach ( $result as $id => $valor )
        {
            //echo "$id: ";
            var_dump($valor);
            $total = count($valor);
            echo "Total = ".$total." ";
            for($q=0;$q<$total;$q++){
                echo( $valor[$q]->productId.', ' );
            }
        }
        //print_r($result);
        //var_dump($result);
        /*

        for($q=0;$q<$total;$q++){
            echo $result[$q]->productId.'<br>';
        }
        echo 'Total en mongo '.count($valor).' Total en Home '.count($product->skus).' '; */
        $archivo = new ArchivoTm();
        $empezar_de_cero = true;
        $productos = false;
        $archivo->setNombreArchivo('../data/cambio_precio.txt');
        $archivo_cambio_precio = $archivo->leerArchivo();
        $archivo->setNombreArchivo('../data/productos_nuevos.txt');
        $archivo_producto_nuevo = $archivo->leerArchivo();
        $archivo->setNombreArchivo('../data/cat_'.$product->metadata->categoryID.'_homedepot.txt');
        $lee_archivo = $archivo->leerArchivo();
        $data_temp_full = array();
        if($lee_archivo == false){
            @$product->metadata->lote = $lote;
            @$product->metadata->numero_datos = "1";
            $total_product = count($product->skus);
        }else{
            $empezar_de_cero = false;//modificar a false despues desde la primera vez
            $check_cambio_precio = false;
            $super_check_cambio_precio = false;
            $total_cambio_precio = 0;
            $total_productos_nuevos = 0;
            $numero_datos = 1; 
            $temp_cambio_precio = array();
            $temp_productos_nuevos = array();
            if($archivo_cambio_precio){
                $temp_cambio_precio = json_decode($archivo_cambio_precio);
                $total_cambio_precio = count($temp_cambio_precio);
            }
            if($archivo_producto_nuevo){
                $temp_productos_nuevos = json_decode($archivo_producto_nuevo);
                $total_productos_nuevos = count($temp_productos_nuevos);
            }
            $total_product = count($product->skus);
            $data_temp_full = json_decode($lee_archivo);
            //print_r($data_temp_full->skus[0]->productId);
            //exit();
            if($empezar_de_cero === false){ 
                $nueva_notificacion = array();
    	        $notificacion_producto_nuevo = '';
    	        $validar_nuevo_producto = false;
                $total_product_database = count($data_temp_full->skus);
                for($x=0;$x<$total_product;$x++){
    	        	$check = false;
                    for($y=0;$y<$total_product_database;$y++){
                        if($product->skus[$x]->productId === $data_temp_full->skus[$y]->productId){ 
                            @$product->skus[$x]->woocomerce_id = $data_temp_full->skus[$y]->woocomerce_id;
                            $check = true;
                            $originalPriceProduct = $product->skus[$x]->storeSku->pricing->specialPrice;
                            if(isset($product->skus[$x]->storeSku->pricing->specialPrice)){
                                $originalPrice = $product->skus[$x]->storeSku->pricing->specialPrice;
                            }
                            $originalPriceDataBase = $data_temp_full->skus[$y]->storeSku->pricing->specialPrice;
                            if(isset($data_temp_full->skus[$y]->storeSku->pricing->specialPrice)){
                                $originalPrice = $data_temp_full->skus[$y]->storeSku->pricing->specialPrice;
                            }
                            if($originalPriceProduct != $originalPriceDataBase){
                                $check_cambio_precio = true;
                                $super_check_cambio_precio = true;
                            }
                        }
                    }
                    
    	        	if($check == false){
                        $validar_nuevo_producto = true;
                        $check_exist_en_nuevo_producto = false;
                        for($c=0;$c<$total_productos_nuevos;$c++){
                            if($temp_productos_nuevos[$c]->productId === $product->skus[$x]->productId){
                                $check_exist_en_nuevo_producto = true;
                                break;
                            }
                        }
                        if(!$check_exist_en_nuevo_producto){
                            array_push($temp_productos_nuevos,$product->skus[$x]);
                            @$temp_productos_nuevos[(count($temp_productos_nuevos) - 1)]->categoryID = $product->metadata->categoryID;
                        }
    	        	}
                    if($check_cambio_precio){
                        $check_exist_product_cambio_precio = false;
                        for($b=0;$b<$total_cambio_precio;$b++){
                            if($temp_cambio_precio[$b]->productId === $product->skus[$x]->productId){
                                $temp_cambio_precio[$b]->storeSku->pricing->specialPrice = $product->skus[$x]->storeSku->pricing->specialPrice;
                                $check_exist_product_cambio_precio = true;
                                break;
                            }
                        }
                        if($total_cambio_precio === 0 || !$check_exist_product_cambio_precio){
                            array_push($temp_cambio_precio,$product->skus[$x]);
                        }
                        $check_cambio_precio = false;
                    }
    	        }
                if($super_check_cambio_precio){
                    $archivo->setNombreArchivo('../data/cambio_precio.txt');
                    $archivo->setContenido(json_encode($temp_cambio_precio));
                    $archivo->escribir();
                }
                if($validar_nuevo_producto){
                    $archivo->setNombreArchivo('../data/productos_nuevos.txt');
                    $archivo->setContenido(json_encode($temp_productos_nuevos));
                    $archivo->escribir();   
                }
                if($super_check_cambio_precio == true || $validar_nuevo_producto == true){
                    
                    $plantilla_email = new PlantillaEmail();
                    $body = $plantilla_email->plantillaUno('5','4');
                    $email = 'residente007@yahoo.es';
                    $headers = "From: info@admintaxi.com\r\n";
                    $headers .= "Reply-To: info@admintaxi.com.com\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $subject = "Algunos productos cambiaron de precios o son nuevos";
                    //$body = 'Revice los productos <a href="https://bestwayapp.market/applEditor">aquí</a>';
                    if(mail($email,$subject,$body,$headers)){

                    }
                }
            }
        }
        //$total_product = 3;
        if($empezar_de_cero === true){
            $productos = array();
            $woo_atribute = $cw->getAtributo();
            $marcas = $database->getMarcas();
            for($x=0;$x<$total_product;$x++){ 
                $nombre_marca = strtoupper(trim($product->skus[$x]->info->brandName));
                if($nombre_marca === 'SAMSUNG' || $nombre_marca === 'LG ElECTRONICS' ){
                    $array_img = array();
                    //condicion para que analice las imagenes de cada producto
                    $imagen_url = array("src"=>str_replace('<SIZE>','400',$product->skus[$x]->info->imageUrl)); //se declara la variable y el tamaño para la imagen
                    array_push($array_img, $imagen_url);
                    if(isset($product->skus[$x]->info->secondaryimageUrl)){ //se verifa la existenia de segunda imagen , si esta que la muestre y sino que lo salte
                        $imagen_url = array("src"=>str_replace('<SIZE>','400',$product->skus[$x]->info->secondaryimageUrl));
                        array_push($array_img, $imagen_url); //relacion entre array
                    }
                    $originalPrice = $product->skus[$x]->storeSku->pricing->originalPrice;
                    $specialPrice = $originalPrice;
                    if(isset($product->skus[$x]->storeSku->pricing->specialPrice)){
                        $originalPrice = $product->skus[$x]->storeSku->pricing->specialPrice; 
                        $specialPrice = $originalPrice;
                    }
                    $specialPrice = ($specialPrice * 0.85);
                    //veridica los campos de top
    	            $topAttributes = $product->skus[$x]->info->topAttributes; //TopAttributes
    	            $total_topAttributes = count($topAttributes); //se cuenta cuantos modulos tiene topattributes
    	            $top="<ul>"; //se declara la variable donde se va a guardar el resultado de las iteraciones del for dentro del modulo de Topattributes
                    $att = array();
                    $total_wooatributo = count($woo_atribute);
                    $woo_term = array();
                    $term = array();
                    //inicia la comprobación si el atributo existe
                   	for($i=0;$i<$total_topAttributes;$i++){
                        $check_atributo = false;
                        for($j=0;$j<$total_wooatributo;$j++){
                            if(strtoupper(trim($woo_atribute[$j]->name)) == strtoupper(trim($product->skus[$x]->info->topAttributes[$i]->name))){
                                $check_atributo = true;
                                $woo_atributo_term = $cw->getTerminoAtributo($woo_atribute[$j]->id);
                                $total_woo_atributo_term = count($woo_atributo_term);
                                //echo 'Total term: '.$total_woo_atributo_term;
                                $check_term_atributo = false;
                                //Inicia comprobación si los terminos de atributo existen
                                if(isset($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue)){
                                    $valor_atributo = substr(trim($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue),0,28);
                                    for($k=0;$k<$total_woo_atributo_term;$k++){
                                        if(strtoupper($woo_atributo_term[$k]->name) === strtoupper($valor_atributo)){
                                            $term = ["".$valor_atributo];
                                            $check_term_atributo = true;
                                            break;
                                        }
                                    }
                                    
                                    //si no existen el term entonces aquí se crean
                                    if($check_term_atributo == false){
                                        $term = [
                                             "name" => "".$valor_atributo,
                                        ];
                                        if(!$cw->setTermAtributo($woo_atribute[$j]->id,$term)){
                                            echo "Problemas con guardar Term";
                                            exit();
                                        }
                                        $term = [
                                             "".trim($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue),
                                        ];
                                    }
                                    $att[] = [
                                        "id"=> "".$woo_atribute[$j]->id,
                                        "options"=> $term
                                    ];

                                }    
                                if(isset($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue)){
                                    $top .= '<li>'.$product->skus[$x]->info->topAttributes[$i]->name.": <strong>".$product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue."</strong></li>";
                                }else{
                                    $top .= '<li>'.$product->skus[$x]->info->topAttributes[$i]->name.": ";       
                                }
                                break;
                            }
                        }
                        //aqui identifica que el atributo no existe y lo crea
                        if($check_atributo === false){
                            $att_new = [
                                "name"=> "".trim($product->skus[$x]->info->topAttributes[$i]->name)
                            ];
                            
                            if(isset($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue)){
                                $term = ["name" => "".trim($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue),];          
                            }else{
                                $term = ["name" => "-"];   
                            }
                            if($cw->setAtributo($att_new)){

                                if(!$cw->setTermAtributo($cw->getIdAtributo(),$term)){
                                        echo "Problemas con guardar Term con Atributo";
                                        exit();
                                    }
                                $woo_atribute = $cw->getAtributo();
                            }else{
                                echo "Problemas con guardar Atributo";
                                exit();
                            }
                            if(isset($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue)){
                                $term = ["".trim($product->skus[$x]->info->topAttributes[$i]->attributeValues[0]->attributeValue),];
                                    $att[] = [
                                    "id"=> $cw->getIdAtributo(),
                                    "options"=> $term
                                ];
                            }
                        }   
                    }
                    $top .= "</ul>";
                    $total_marca = count($marcas);
                    $check_marca = false;
                    for($l =0; $l<$total_marca;$l++){
                    	if($marcas[$l]->name == $product->skus[$x]->info->brandName){
                    		$id_marca = $marcas[$l]->term_id;
                    		$check_marca = true;
                    		break;
                    	}	
                    }
                    //print_r($marca[0]['id']);
                    if($check_marca === false){
                    	$database->crearMarca($product->skus[$x]->info->brandName);
                    	$id_marca = $database->getIdMarca();
                        $marcas = $database->getMarcas(); 
                    }
                    $producto = array(); //array por cada product
                    if(isset($product->skus[$x]->info->storeSkuNumber)){
                        $producto = [
                        'display'=> $product->skus[$x]->info->brandName,
                        'name' => $product->skus[$x]->info->productLabel,
                        'type' => 'simple',
                        'price'=> "".$originalPrice,
                        'regular_price'=> "".$originalPrice,
                        'sale_price'=> "".$specialPrice,
                        'description' => $top,
                        'short_description' => $top,
                        'sku' => "".$product->skus[$x]->info->storeSkuNumber,
                        'categories' => [
                            [
                              'id' => $product->metadata->categoryID,
                            ],
                         ],
                         'attributes' => $att,
                         'images' => $array_img,
                        ];
                    }else{
                        $producto = [
                        'display'=> $product->skus[$x]->info->brandName,
                        'name' => $product->skus[$x]->info->productLabel,
                        'type' => 'simple',
                        'price'=> "".$originalPrice,
                        'regular_price'=> "".$originalPrice,
                        'sale_price'=> "".$specialPrice,
                        'description' => $top,
                        'short_description' => $top,
                        'categories' => [
                            [
                              'id' => $product->metadata->categoryID,
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
                    	$id_product =  $cw->getIdProduct();
                    	@$product->skus[$x]->woocomerce_id = $id_product;
                        $database->relacionProductoMarca($id_product,$id_marca);
                        if($lee_archivo == false){
                            array_push($productos, $product->skus[$x]);
                        }else{
                            array_push($data_temp_full->skus, $product->skus[$x]);
                        }
                    }
                    //array_push($productos, $producto);
                }
                if($lee_archivo == false){
                    $archivo->setContenido(json_encode($product));
                    $archivo->escribir();
                }else{
                    $archivo->setContenido(json_encode($data_temp_full));
                    $archivo->escribir();
                }
            }
        }
        return $check;
    }
}
?>