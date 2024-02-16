<?php

function verificarUsuario($login,$password){
    $db = AccesoDatos::getModelo();
    $pass=password_verify($password,$db->getPass($login));
    $user = $db->getLogin($login);
 
    return $user;
    return $pass;
}

function crudBorrar ($id){    
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ( $resu){
         $_SESSION['msg'] = " El usuario ".$id. " ha sido eliminado.";
    } else {
         $_SESSION['msg'] = " Error al eliminar el usuario ".$id.".";
    }

}

function crudTerminar(){
    AccesoDatos::closeModelo();
    session_destroy();
}
 
function crudAlta(){
    $cli = new Cliente();
    $orden= "Nuevo";
    include_once "app/views/formulario.php";
}

function crudDetalles($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $bandera=getBandera($cli->ip_address);
    $imagen=getImagen($id,$cli->ip_address);
    include_once "app/views/detalles.php";
}

function crudDetallesSiguiente($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    $bandera=getBandera($cli->ip_address);
    $imagen=getImagen($id+1,$cli->ip_address);
    include_once "app/views/detalles.php";
}

function crudDetallesAnterior($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    $bandera=getBandera($cli->ip_address);
    $imagen=getImagen($id-1,$cli->ip_address);
   include_once "app/views/detalles.php";
}


function crudModificar($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden="Modificar";
    include_once "app/views/formulario.php";
}

function crudModificarSiguiente($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    $orden="Modificar";
    include_once "app/views/formulario.php";
}

function crudModificarAnterior($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    $orden="Modificar";
    include_once "app/views/formulario.php";
}

function crudPostAlta(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    // !!!!!! No se controlan que los datos sean correctos 
    $cli = new Cliente();
    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    $db = AccesoDatos::getModelo();

    $check=true;

    // if(existeMailMod($cli->email,$cli->ip)){
    //     $_SESSION['msg'] .= "Existe un usuario con este email.";
    //     $check=false;
    // }
    if(existeIp($cli->ip)){
        $_SESSION['msg'] .= "Ip no valida";
        $check=false;
    }
    if(!checkTlf($cli->telefono)){
        $_SESSION['msg'] .= "Formato de telefono no valido";
        $check=false;
    }

    if ( $check) {
        // if(!empty($_FILES["image"])){
        //     subirImagen($cli->id);
        // }
        $db->addCliente($cli);
        $_SESSION['msg'] = " El usuario ".$cli->first_name." se ha dado de alta ";
    } else {
        $_SESSION['msg'] .= " Error al dar de alta al usuario ".$cli->first_name."."; 
    }
}

function crudPostModificar(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $cli = new Cliente();

    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    $db = AccesoDatos::getModelo();

    $check=true;

    if(existeMailMod($cli->email,$cli->id)){
        $_SESSION['msg'] .= "Existe un usuario con este email.";
        $check=false;
    }
    if(existeIp($cli->ip)){
        $_SESSION['msg'] .= "Ip no valida";
        $check=false;
    }
    if(!checkTlf($cli->telefono)){
        $_SESSION['msg'] .= "Formato de telefono no valido";
        $check=false;
    }
    
    if ($check){
        $db->modCliente($cli);
        crudModificar($cli->id);
        header('Location: index.php');
        $_SESSION['msg'] = "Usuario modificado";
    } else {
        crudModificar($cli->id);
    }
    
}

function getBandera($ip){
    $pais=unserialize(file_get_contents("http://ip-api.com/php/".$ip."?fields=countryCode"));
        if(empty($pais)){
            $bandera="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Globe_icon.svg/1200px-Globe_icon.svg.png";
        }else{
            $siglas=strtolower($pais["countryCode"]);
            $bandera="https://flagcdn.com/192x144/".$siglas.".png";
        }
        return $bandera;
}

function getImagen($id,$ip){
    $file=substr('00000000',0,-(strlen($id))).$id;
    $src="app/uploads/".$file;
    if(file_exists($src.".jpg")){
        $imagen=$src.".jpg";
    }else if(file_exists($src.".png")){
        $imagen=$src.".png";
    }else if(file_exists($src.".jpeg")){
        $imagen=$src.".jpeg";
    }
    else{
        $imagen="https://robohash.org/".$ip.".png";
    }
    return $imagen;
}

function subirImagen($id){
    $nombre=substr('00000000',0,-(strlen($id))).$id;

    $formato=pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION);

    $src="app/uploads/$nombre.$formato";

    if($_FILES["image"]["size"]>$_POST["max_size"]){
        $_SESSION["msg"].="Tamaño maximo 500kb";
    }else{
        move_uploaded_file(($_FILES["image"]["tmp_name"]),$src);
    }
}

function existeMailNew($email){
    $db = AccesoDatos::getModelo();
    $mail=$db->existsEmail($email);
    if(!empty($db->existsEmail($email))){
        return true;
    }else{
        return false;
    }
}
function existeMailMod($email,$id){
    $db = AccesoDatos::getModelo();
    $cli=$db->existsEmail($email);
    if($cli){
        if($id==$cli->id){
            $resu= false;
        }else{
            $resu= true;
        }
    }else{
        $resu= false;
    }
    return $resu;
}
function existeIp($ip){
    filter_var($ip,FILTER_VALIDATE_IP);
}
function checkTlf($tlf){
    $strtlf="/\d{3}-\d{3}-\d{4}/i";
    if(preg_match($strtlf,$tlf)==1){
        return true;
    }else{
        return false;
    }
}

function Imprimir($id,$fname,$lname,$mail,$gender,$ip,$telefono){
    require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

        $pdf = new TCPDF();
        
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 12);

        $html = '
            <h1>Informacion personal</h1>
            <p><strong>ID:</strong> ' . $id . '</p>
            <p><strong>First Name:</strong> ' . $fname . '</p>
            <p><strong>Last Name:</strong> ' . $lname . '</p>
            <p><strong>Email:</strong> ' . $mail . '</p>
            <p><strong>Gender:</strong> ' . $gender . '</p>
            <p><strong>IP Address:</strong> ' . $ip . '</p>
            <p><strong>Phone Number:</strong> ' . $telefono . '</p>
        ';

        $pdf->writeHTML($html);
        $pdf->Output('Datos personales.pdf', 'I');
}

 