<?php
class Product{
    private $name;
    private $price;
    private $desc;
    private $size;
    function __constructor($name){
        $this->$name = $name;
    }
// setter
    function set_price($price){
        $this->$price = $price;
    }
    function set_name($name){
        $this->$name = $name;
    }
    function set_desc($desc){
        $this->$desc = $desc;
    }
    function set_size($size){
        $this->$size = $size;
    }
//getter
    function get_name(){
        return $this->$name;
    }
    function get_price(){
        return $this->$price;
    }
    function get_desc(){
        return $this->$desc;
    }
    function get_size(){
        return $this->$size;
    }
}
?>