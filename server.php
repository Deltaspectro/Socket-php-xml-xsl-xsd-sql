#!/usr/local/bin/php -q
<?php

/* 
 * Creamos la conexion con la base de datos mediante PDO
 */


error_reporting(E_ALL);

/* Permitir al script esperar para conexiones. */
set_time_limit(0);

/* Activar el volcado de salida implícito, así veremos lo que estamo obteniendo
 * mientras llega. */
ob_implicit_flush();

$address = '127.0.0.1';
$port = 10000;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() falló: razón: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "socket_accept() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    $opc = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
$dsn = "mysql:host=localhost;dbname=rene";//datos conexion bbdd
$usuario = 'rene';
$contrasena = 'compaq';

$dwes = new PDO($dsn, $usuario, $contrasena, $opc);//conexion

/* 
 * Preparamos la consulta para obtener las tablas.
 */
$sql="SHOW TABLES";

if (isset($dwes)){
    $resultado = $dwes->query($sql);//obtenemos las tablas de la base de datos
    $xml="<?xml version=\"1.0\"?>\n";//variable que contendra el codigo xml

    while($row = $resultado->fetch()){

        $sql2="SELECT * FROM ".$row[0];//consulta las tablas dinamicamente
        
        $xml .= "\t<".$row[0].">\n";//tabla de la base de datos
        $result= $dwes->query($sql2);//obtenemos todos los campos de la  tabla

        while($fila = $result->fetch(PDO::FETCH_ASSOC)){

            $xml .= "\t\t<registro>\n";//por cada registro de la tabla

            foreach ($fila as $k => $v) {//recorremos las claves y los valores de cada registro
               

                $xml .= "\t\t\t<".$k.">".$v."</".$k.">\n";//los almacenamos en el xml
            }
            $xml .= "\t\t</registro>\n";//cerramos el registro
        }
        $xml .= "\t</".$row[0].">\n";//cerramos la tabla
    }
    //echo $xml;

    
}

        "Para salir, escriba 'quit'. Para cerrar el servidor escriba 'shutdown'.\n";
    socket_write($msgsock, $xml, strlen($xml));

    do {
        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
            echo "socket_read() falló: razón: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }
        if (!$buf = trim($buf)) {
            continue;
        }
        if ($buf == 'quit') {
            break;
        }
        if ($buf == 'shutdown') {
            socket_close($msgsock);
            break 2;
        }
        $talkback = "PHP: Usted dijo '$buf'.\n";
        socket_write($msgsock, $talkback, strlen($talkback));
        echo "$buf\n";
    } while (true);
    socket_close($msgsock);
} while (true);

socket_close($sock);
?>
