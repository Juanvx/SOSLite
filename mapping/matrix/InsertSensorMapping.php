<?php

/*
 */

namespace SOSLite\mapping\matrix;

/**
 * Description of InsertSensorMapping
 *
 * @author juanvx
 */

class InsertSensorMapping {

    public static function map($xml_string) {
        $mapping = array();
        
        $dom = new \DOMDocument();
        $dom->loadXML($xml_string, LIBXML_NOBLANKS);
        
        $descriptionFile = "./data/sensors/" . md5($xml_string) . rand(0, 99999) . ".xml"; 

        $procedureDescriptionNodes = $dom->getElementsByTagName("procedureDescription");
        foreach ($procedureDescriptionNodes as $procedureDescriptionNode) {
            $mapping['descriptionFile'] =  $descriptionFile;
            file_put_contents($descriptionFile, $dom->saveXML($procedureDescriptionNode->firstChild));
            
            $longitude = 0;
            $latitude = 0;
            $featuresOfInterestNodes = $procedureDescriptionNode->getElementsByTagName("featuresOfInterest");
            foreach ($featuresOfInterestNodes as $featuresOfInterestNode) {
                $coordinates = explode(" ", $featuresOfInterestNode->getElementsByTagName("pos")->item(0)->nodeValue);
                $longitude = floatval($coordinates[0]);
                $latitude = floatval($coordinates[1]);
                //WGS84 datum http://spatialreference.org/ref/epsg/4326/ 
                $mapping["sensorLocation"] = array("type" => "Point", "coordinates" => array($longitude, $latitude));
                $mapping["featuresOfInterest"] = $dom->saveXML($featuresOfInterestNode->getElementsByTagName("SF_SpatialSamplingFeature")->item(0));
            }
        } 
        
        $observablePropertyNodes = $dom->getElementsByTagName("observableProperty");
        $observableProperties = array();
        foreach ($observablePropertyNodes as $observablePropertyNode) {
             $observableProperties[] =  $observablePropertyNode->nodeValue;
        } 
        
        $mapping['observableProperty'] = $observableProperties;

        return $mapping;
    }

}
