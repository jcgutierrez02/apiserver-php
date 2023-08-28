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


$json = file_get_contents('php://input'); // RECIBE EL DATO JSON DE ANGULAR

$params = json_decode($json); // DECODIFICA EL JSON Y LO GUARDA EN UNA VARIABLE


// ESTABLECE CONEXION CON UNA NUEVA INSTANCIA
$pdo = new Conexion();

// OBTENER TODOS LOS DATOS Y OBTENER UN SOLO DATO CUANDO HAYA UN ID POR PARAMETRO
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if (isset($_GET['id'])) {

       $sql = $pdo -> prepare("SELECT * FROM contactosdb WHERE id = :id");
       $sql-> bindValue(':id', $_GET['id']);
       $sql ->execute();
       $sql->setFetchMode(PDO::FETCH_ASSOC);

       header("HTTP/1.1 200 OK");
       echo json_encode($sql-> fetchAll());
       exit;
        
    }
    else {

        $sql = $pdo -> prepare("SELECT * FROM contactosdb");
        $sql ->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        echo json_encode($sql-> fetchAll());
        exit;
    }

}

// REGISTRAR DATOS

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $sql = "INSERT INTO contactosdb (nombre, telefono, email, imagen) VALUES (:nombre, :telefono, :email,:imagen)";
    $stmt = $pdo-> prepare($sql);
    $stmt -> bindValue(':nombre', $params->nombre);
    $stmt -> bindValue(':telefono', $params->telefono);
    $stmt -> bindValue(':email', $params->email);
    $stmt -> bindValue(':imagen', $params->imagen);
    $stmt -> execute();
    $idPost = $pdo -> lastInsertId();

    if ( $idPost ) {
        header("HTTP/1.1 200 OK");
        echo json_encode('El registro se agregó correctamente');
        exit;
    }

}

// ACTUALIZAR REGISTRO

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    $sql = "UPDATE contactosdb SET nombre=:nombre, telefono=:telefono, email=:email, 
                imagen=:imagen WHERE id=:id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> bindValue(':nombre',$params->nombre);
    $stmt -> bindValue(':telefono',$params->telefono);
    $stmt -> bindValue(':email',$params->email);
    $stmt -> bindValue(':imagen',$params->imagen);
    $stmt -> bindValue(':id', $_GET['id']);
    $stmt -> execute();

    header("HTTP/1.1 200 OK");
    echo json_encode('El registro se actualizó correctamente');
    exit;
}

// ELIMINAR REGISTRO

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    $sql = "DELETE FROM contactosdb WHERE id=:id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> bindValue(':id', $_GET['id']);
    $stmt -> execute();
    
    header("HTTP/1.1 200 OK");
    echo json_encode('El registro se eliminó correctamente');
    exit;
}

//Si no corresponde a ninguna opcion 
header("HTTP/1.1 400 Bad Request");