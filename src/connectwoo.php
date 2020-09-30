<?php
require 'D:/xampp7/htdocs/dev/receptionProduct/ReceptionProduct/vendor/autoload.php';

use Automattic\WooCommerce\Client;

class Connectwoo{
    private $check = false;
    private $woocommerce = "";
    private $id_product = "";
    private $id_atributo = "";
    private $id_term_atributo ="";
    function credencialToWoo(){
        $url = 'http://localhost/dev/wordpress/wp-prueba/';
        $clave_publica = 'ck_57b29457153284daf48d9b5b5a95f8dc52a7f7cf';
        $clave_privada = 'cs_3d806288847f048940f31a70ed7cf5d23bc2a114';
       /* $url = 'http://localhost/pruebapaginawordpres/';
        $clave_publica = 'ck_0d5fef5c02ca4fc8c94362a8be12e5fe794d8c71';
        $clave_privada = 'cs_e697b042675b891fde23587d140b8ee3683fbec6';*/
        /*$url = 'https://bestwayapp.market/';
        $clave_publica = 'ck_0115b3456e91f20ecdfc7a671ba85aa73db845a7';
        $clave_privada = 'cs_bb2a54d3a485aa0add74acdfbc51ef7717e1fbba';*/
        $this->woocommerce = new Client($url, $clave_publica, $clave_privada, ['wp_api'=>true,'version'=>'wc/v3','timeout' => 400] );
    }
    function send($list_product_json){
        try{
            $this->credencialToWoo();
            //print_r($this->woocommerce->get('products'));
            $this->id_product = $this->woocommerce->post('products',$list_product_json);
            $this->id_product = $this->id_product->id;
            $this->check = true;
        }catch(WC_API_Client_Exception $e){
            print_r($list_product_json);
            $this->check = false;
            //echo $e->getMessage() . PHP_EOL;
            //echo $e->getCode() . PHP_EOL;

            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
               // print_r( $e->get_request() );
               // print_r( $e->get_response() );
            }
        }
        return $this->check;
    }
    function getIdProduct(){
        return $this->id_product;
    }
    function getProduct($id = null){
        $this->credencialToWoo();
        if($id === null){
            return $this->woocommerce->get('products');
        }else{
            return $this->woocommerce->get('products/'.$id);
        }
    }
    function setProduct($id,$data){
         $this->credencialToWoo();
        return $this->woocommerce->put('products/'.$id,$data);
    }
    function getCategoria(){
        $this->credencialToWoo();
        return $this->woocommerce->get('products/categories/');
    }
    function getAtributo(){
        $this->credencialToWoo();
        return $this->woocommerce->get('products/attributes/');
    }
    function getTerminoAtributo($id){
        $this->credencialToWoo();
        return $this->woocommerce->get('products/attributes/'.$id.'/terms/');
    }
    function setAtributo($data){
        $check = false;
        try{
            $this->credencialToWoo();
                /*$data = [
                'name' => 'Color',
                'slug' => 'pa_color',
                'type' => 'select',
                'order_by' => 'menu_order',
                'has_archives' => true
            ];*/
            $this->id_atributo = $this->woocommerce->post('products/attributes', $data);
            $this->id_atributo = $this->id_atributo->id; 
            $check = true;
        }catch(WC_API_Client_Exception $e){
            $this->check = false;
            echo $e->getMessage() . PHP_EOL;
            echo $e->getCode() . PHP_EOL;

            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
                print_r( $e->get_request() );
                print_r( $e->get_response() );
            }
        }
        return $check;
    }
    function getIdAtributo(){
        return $this->id_atributo;
    }
    function setTermAtributo($id,$data){
        $check = false;
        try{
            $this->credencialToWoo();
            $term = $this->woocommerce->post('products/attributes/'.$id.'/terms', $data);
            $this->id_term_atributo = $term->id;
            $check = true;
        }catch(WC_API_Client_Exception $e){
            $check = false;
            //echo $e->getMessage() . PHP_EOL;
            //echo $e->getCode() . PHP_EOL;

            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
               // print_r( $e->get_request() );
               // print_r( $e->get_response() );
            }
        }
        return $check;
    }
    function updatePrices(array $prices){
        $check = false;
        try{
            $this->credencialToWoo();
            $products = $this->woocommerce->post('products/batch', $prices);
            $check = true;
        }catch(WC_API_Client_Exception $e){
            $check = false;
            //echo $e->getMessage() . PHP_EOL;
            //echo $e->getCode() . PHP_EOL;

            if ( $e instanceof WC_API_Client_HTTP_Exception ) {
               // print_r( $e->get_request() );
               // print_r( $e->get_response() );
            }
        }
        return $check;
    }
 }
?>