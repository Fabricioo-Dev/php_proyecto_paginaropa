<?php
session_start();


if(isset($_SESSION['carrito'])){
    array_push($_SESSION['carrito'],$_POST['id']);
}
else{
    $_SESSION['carrito'] = array();
    array_push($_SESSION['carrito'],$_POST['id']);
}



?>