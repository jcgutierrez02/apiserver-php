<?php

include 'conexion.php';

/*=============================================
CORS
=============================================*/
$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
   
   header('Access-Control-Allow-Origin: *');
   header("Access-Control-Allow-Headers: X-API-KEY,Origin,X-Requested-With, Content-Type, Accept,Access-Control-Request-Method,Access-Request-Headers,Authorization");
   header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
   header('content-type: application/json; charset=utf-8');
   header('HTTP/1.1 200 OK');
   die();
}


$json = file_get_contents('php://input'); // RECIBE EL DATO JSON DESDE UN CLIENTE (JavaScript, Angular, React, etc.)

$params = json_decode($json); // DECODIFICA EL JSON Y LO GUARDA EN UNA VARIABLE


// CREA UNA INSTANCIA DE CONEXIÓN A LA BD
$pdo = new Conexion();

// OBTENER TODOS LOS DATOS Y OBTENER UN SÓLO DATO CUANDO HAYA UN ID POR PARÁMETRO
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (isset($_GET['id'])) {   // EL ID SE ENVÍA COMO PARÁMETRO EN LA URL

        $sql = $pdo->prepare("SELECT * FROM contactosdb WHERE id = :id");
        $sql->bindValue(':id', $_GET['id']);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $datos = $sql->fetchAll();

        if ( count($datos) > 0 ) {  // El registro existe
           header("HTTP/1.1 200 OK");
           echo json_encode($datos);
        }   
        else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode('El registro solicitado no existe');
        }   
       
        exit;
        
    }
    else {  // NO HAY ID COMO PARÁMETRO EN LA URL. OBTENER TODOS LOS DATOS

        $sql = $pdo->prepare("SELECT * FROM contactosdb");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        
        exit;
    }

}

// REGISTRAR DATOS

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $sql = "INSERT INTO contactosdb (nombre, telefono, email, imagen) 
                        VALUES (:nombre, :telefono, :email,:imagen)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nombre', $params->nombre);
    $stmt->bindValue(':telefono', $params->telefono);
    $stmt->bindValue(':email', $params->email);
    $stmt->bindValue(':imagen', $params->imagen);
    $stmt->execute();
    $idPost = $pdo->lastInsertId();

    if ( $idPost ) {
        header("HTTP/1.1 200 OK");
        echo json_encode('El registro se agregó correctamente');
        exit;
    }

}

// MODIFICAR REGISTRO

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    $sql = "UPDATE contactosdb SET nombre=:nombre, telefono=:telefono, email=:email, 
                imagen=:imagen WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':nombre',$params->nombre);
    $stmt->bindValue(':telefono',$params->telefono);
    $stmt->bindValue(':email',$params->email);
    $stmt->bindValue(':imagen',$params->imagen);
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        header("HTTP/1.1 200 OK");
        echo json_encode('El registro se actualizó correctamente');
    }
    else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode('El registro solicitado no existe');
    }    
    exit;
}

// ELIMINAR REGISTRO

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    $sql = "DELETE FROM contactosdb WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $_GET['id']);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        header("HTTP/1.1 200 OK");
        echo json_encode('El registro se eliminó correctamente');
    }    
    else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode('El registro solicitado no existe');
    }    
    exit;
}

//Si no corresponde a ninguna opcion 
header("HTTP/1.1 400 Bad Request");