<?php

/* 
 */

namespace SOSLite\mapping\matrix;

/**
 * Description of InsertObservationMapping
 *
 * @author juanvx
 */

class InsertObservationMapping {

    public static function map($xml_string) {
        $mapping = array();
        
        $dom = new \DOMDocument();
        $dom->loadXML($xml_string, LIBXML_NOBLANKS);
        
        $offeringNodes = $dom->getElementsByTagName("offering");
        $offerings = array();
        foreach ($offeringNodes as $offeringNode) {
             $offerings[] =  $offeringNode->nodeValue;
        } 
        
        $observationNodes = $dom->getElementsByTagName("observation");
        foreach ($observationNodes as $observationNode) {
            $observationTmp = array();
             
            $observationFile = "./data/observations/" . md5($xml_string) . rand(0, 99999) . ".xml"; 
            $observationTmp['observationFile'] =  $observationFile;
            file_put_contents($observationFile, $dom->saveXML($observationNode->firstChild));
            
            $observationTmp['offering'] = $offerings;
            
            $procedureNodes = $observationNode->getElementsByTagName("procedure");
            foreach ($procedureNodes as $procedureNode) {            
                $observationTmp['procedure'] =  $procedureNode->getAttribute("xlink:href");
            } 

            $observedPropertyNodes = $observationNode->getElementsByTagName("observedProperty");
            foreach ($observedPropertyNodes as $observedPropertyNode) {            
                $observationTmp['observedProperty'] =  $observedPropertyNode->getAttribute("xlink:href");
            } 

            $phenomenonTimeNodes = $observationNode->getElementsByTagName("phenomenonTime");
            $phenomenonTimeLink = "";   
            foreach ($phenomenonTimeNodes as $phenomenonTimeNode) {   
                $phenomenonTimeLink =  $phenomenonTimeNode->getAttribute("xlink:href");
                $timePositionNodes =  $phenomenonTimeNode->getElementsByTagName("timePosition");
                foreach ($timePositionNodes as $timePositionNode) { 
                    $observationTmp['phenomenonTime'] =  $timePositionNode->nodeValue;
                }
            }
            
            $resultTimeNodes = $observationNode->getElementsByTagName("resultTime");
            $resultTimeLink = "";
            foreach ($resultTimeNodes as $resultTimeNode) {            
                $resultTimeLink =  $resultTimeNode->getAttribute("xlink:href");
                $timePositionNodes =  $resultTimeNode->getElementsByTagName("timePosition");
                foreach ($timePositionNodes as $timePositionNode) { 
                    $observationTmp['resultTime'] = $timePositionNode->nodeValue;
                }
                
            }
            
            if($phenomenonTimeLink != null && $phenomenonTimeLink != ""){
                $observationTmp['phenomenonTime'] = $observationTmp['resultTime'];
            }
            if($resultTimeLink != null && $resultTimeLink != ""){
                $observationTmp['resultTime'] = $observationTmp['phenomenonTime'];
            }
            
            $longitude = 0;
            $latitude = 0;
            $featureOfInterestNodes = $observationNode->getElementsByTagName("featureOfInterest");
            foreach ($featureOfInterestNodes as $featureOfInterestNode) {            
                $observationTmp['featureOfInterest'] =  $featureOfInterestNode->getElementsByTagName("identifier")->item(0)->nodeValue;
                $coordinates = explode(" ", $featureOfInterestNode->getElementsByTagName("pos")->item(0)->nodeValue); 
                $longitude = floatval($coordinates[0]);
                $latitude = floatval($coordinates[1]);
                $observationTmp["samplingPoint"] =  array("type" => "Point", "coordinates" => array($longitude, $latitude));
            }
            
            $mapping[] = $observationTmp;
        } 
        
        return $mapping;
    }
}