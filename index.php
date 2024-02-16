<?php
session_start();
define ('FPAG',10); // Número de filas por página

require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';

//---- PAGINACIÓN ----
$midb = AccesoDatos::getModelo();
$totalfilas = $midb->numClientes();
if ( $totalfilas % FPAG == 0){
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if ( !isset($_SESSION['posini']) ){
  $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//------------
if(!isset($_SESSION['acceso'])){
    $_SESSION['acceso']=false;
}

if(!isset($_SESSION['intentos'])){
    $_SESSION['intentos']=0;
}
// Borro cualquier mensaje "
$_SESSION['msg']=" ";

if($_SESSION['acceso']==true&&$_SESSION['intentos']<3){
    ob_start(); // La salida se guarda en el bufer
    if ($_SERVER['REQUEST_METHOD'] == "GET" ){
        
        // Proceso las ordenes de navegación
        if ( isset($_GET['nav'])) {
            switch ( $_GET['nav']) {
                case "Primero"  : $posAux = 0; break;
                case "Siguiente": $posAux +=FPAG; if ($posAux > $posfin) $posAux=$posfin; break;
                case "Anterior" : $posAux -=FPAG; if ($posAux < 0) $posAux =0; break;
                case "Ultimo"   : $posAux = $posfin;
            }
            $_SESSION['posini'] = $posAux;
        }


        // Proceso las ordenes de navegación en detalles
        if ( isset($_GET['nav-detalles']) && isset($_GET['id']) ) {
            switch ( $_GET['nav-detalles']) {
                case "Siguiente": crudDetallesSiguiente($_GET['id']); break;
                case "Anterior" : crudDetallesAnterior($_GET['id']); break;
                
            }
        }

        // Proceso las ordenes de navegación en modificar
        if ( isset($_GET['nav-modificar']) && isset($_GET['id'])) {
            switch ( $_GET['nav-modificar']){
                case "Siguiente": crudModificarSiguiente($_GET['id']); break;
                case "Anterior" : crudModificarAnterior($_GET['id']); break;
            }
        }
        
        // Proceso de ordenes de CRUD clientes
        if ( isset($_GET['orden'])){
            switch ($_GET['orden']) {
                case "Nuevo"    : crudAlta(); break;
                case "Borrar"   : crudBorrar   ($_GET['id']); break;
                case "Modificar": crudModificar($_GET['id']); break;
                case "Detalles" : crudDetalles ($_GET['id']);break;
                case "Terminar" : crudTerminar(); break;
            }
        }
    } 
    // POST Formulario de alta o de modificación
    else {
        if (  isset($_POST['orden'])){
            switch($_POST['orden']) {
                case "Nuevo"    : crudPostAlta(); break;
                case "Modificar": crudPostModificar(); break;
                case "Detalles":; // No hago nada
                case "Imprimir":; Imprimir($_POST['id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['gender'], $_POST['ip_address'], $_POST['telefono']);
            }
        }
    }
    // Si no hay nada en la buffer 
    // Cargo genero la vista con la lista por defecto
    if ( ob_get_length() == 0){{
        $db = AccesoDatos::getModelo();
        $posini = $_SESSION['posini'];
        if(isset($_GET['id'])){
            if($_GET['id']=="↑"){
                $_SESSION['ordenarpor']=" ORDER BY id ASC";
            }else{
                $_SESSION['ordenarpor']=" ORDER BY id DESC";
            }  
        }else
            if(isset($_GET['first_name'])){
                if($_GET['first_name']=="↑") {
                    $_SESSION['ordenarpor']=" ORDER BY first_name ASC";
                }else{
                    $_SESSION['ordenarpor']=" ORDER BY first_name DESC";
                }
            }else
            if(isset($_GET['email'])){
                if($_GET['email']=="↑") {
                    $_SESSION['ordenarpor']=" ORDER BY email ASC";
                }else{
                    $_SESSION['ordenarpor']=" ORDER BY email DESC";
                }
            }else
            if(isset($_GET['gender'])){
                if($_GET['gender']=="↑") {
                    $_SESSION['ordenarpor']=" ORDER BY gender ASC";
                }else{
                    $_SESSION['ordenarpor']=" ORDER BY gender DESC";
                }
            }else
            if(isset($_GET['ip_address'])){
                if($_GET['ip_address']=="↑") {
                    $_SESSION['ordenarpor']=" ORDER BY ip_address ASC";
                }else{
                    $_SESSION['ordenarpor']=" ORDER BY ip_address DESC";
                }
            }
            if(isset($_SESSION["ordenarpor"])){
                $ordenarpor=$_SESSION["ordenarpor"];
            }else{
                $ordenarpor="";
            }
            $tvalores = $db->getClientes($posini,FPAG,$ordenarpor);
            require_once "app/views/list.php";
        }    
    }
}else{
    require_once "app/views/login.php";
    if($_SERVER['REQUEST_METHOD']=="POST"){
        if(!empty($_POST['login'])||!empty($_POST['pass'])){
            if(verificarUsuario($_POST['login'],$_POST['pass'])){
                $_SESSION['acceso']=true;
                header("Location: index.php");
            }else{
                $_SESSION['intentos']++;
                $_SESSION["msg"]="Vuelva a intentarlo.";
            }
        }
    }

    if($_SESSION['intentos']>2){
        exit("Maximo numero de intentos realizados.");
    }
}
$contenido = ob_get_clean();
$msg = $_SESSION['msg'];
// Muestro la página principal con el contenido generado
require_once "app/views/principal.php";