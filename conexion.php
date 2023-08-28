<?php

class Conexion extends PDO {

private $hostBd = 'db4free.net';
private $nombreBd = 'iesdaw';
private $usuarioBd = 'jcarlos';
private $passwordBd = 'Jcarl0sJcarl0s';
private $charset = 'utf8';

public function __construct()
{

    try{
        parent::__construct(
            
            'mysql:host=' . $this->hostBd . 
            ';dbname=' . $this->nombreBd . ';charset=utf8', 
            $this->usuarioBd, 
            $this->passwordBd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        
        } catch(PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        exit;
    }

    
}



} 