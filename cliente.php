<?php
error_reporting(E_ALL);


/* Obtener el puerto para el servicio WWW. */
$service_port = 10000;

/* Obtener la dirección IP para el host objetivo. */
$address = '127.0.0.1';

/* Crear un socket TCP/IP. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$result = socket_connect($socket, $address, $service_port);

$in = "HEAD / HTTP/1.1\r\n";
$in .= "Host: 127.0.0.1\r\n";
$out = '';
socket_write($socket, $in, strlen($in));

$out = socket_read($socket, 2048) or die("Problemas");
  //vamos añadiendo el lineas con bucle;
    $flujo = fopen('marca.xml', 'w+');//creamos el fichero.
    fputs($flujo, $out);//volcamos el contenido de cadena al fichero
    fclose($flujo);//cerramos el flujo
    
    $doc = new DOMDocument();
$doc->load('marca.xml');
$is_valid_xml = $doc->schemaValidate('marca.xsd');

$xml = simplexml_load_file('marca.xml');

$xsl = new DOMDocument;
$xsl->load('marca.xsl');

// Configure the transformer
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl); // attach the xsl rules

$proc->transformToURI($xml, 'file:///var/www/html/out.html');

echo file_get_contents('/var/www/html/out.html');

$con = mysql_connect("localhost","root","compaq");
mysql_select_db("zend2", $con) or die (mysql_error());
foreach($xml->registro as $row)
{

$sql = "INSERT INTO `cat_marcas`(`id_marca`, `marca`) VALUES (".$row->id_marca.",'".$row->marca."')";
$res = mysql_query($sql);
if(!$res)
{
		echo mysql_error();
}
}
?>
