<?php

/*
 */

require_once './facade/SOSLiteFacade.php';
use SOSLite\facade\SOSLiteFacade as SOSLiteFacade;

date_default_timezone_set("UTC");
    
$serverOptions = array(
    'soap_version' => SOAP_1_2,
    'encoding' => "utf-8"
);

$server = new SoapServer("SOSLite.wsdl", $serverOptions);
$server->setObject(new SOSLiteFacade());
$server->handle();
?>