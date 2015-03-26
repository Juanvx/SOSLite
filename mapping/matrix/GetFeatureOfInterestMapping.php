<?php

/* 
 */

namespace SOSLite\mapping\matrix;

/**
 * Description of GetFeatureOfInterestMapping
 *
 * @author juanvx
 */

class GetFeatureOfInterestMapping {

    public static function map($xml_string) {
        $mapping = array();
        
        $dom = new \DOMDocument();
        $dom->loadXML($xml_string, LIBXML_NOBLANKS);
        
        $procedures = array();
        $procedureNodes = $dom->getElementsByTagName("procedure");
        foreach ($procedureNodes as $procedureNode) {
            $procedures[] =  $procedureNode->nodeValue;
        } 
        $mapping['procedures'] =  $procedures;
        
        $observedProperties = array();
        $observedPropertyNodes = $dom->getElementsByTagName("observedProperty");
        foreach ($observedPropertyNodes as $observedPropertyNode) {
            $observedProperties[] =  $observedPropertyNode->nodeValue;
        } 
        $mapping['observedProperties'] =  $observedProperties;
        
        $featuresOfInterest = array();
        $featureOfInterestNodes = $dom->getElementsByTagName("featureOfInterest");
        foreach ($featureOfInterestNodes as $featureOfInterestNode) {
            $featuresOfInterest[] =  $featureOfInterestNode->nodeValue;
        } 
        $mapping['featuresOfInterest'] =  $featuresOfInterest;
        
        //Spatial filters
        $spatialBBOXFilter = array();
        $spatialFilterNodes = $dom->getElementsByTagName("spatialFilter");
        foreach ($spatialFilterNodes as $spatialFilterNode) {
            $BBOXFilter = array();
            $BBOXNodes = $spatialFilterNode->getElementsByTagName("BBOX");
            foreach ($BBOXNodes as $BBOXNode) { 
                //BBOX -> lowerCorner, upperCorner: <longitude latitude>
                $lowerCornerCoordinates = explode(" ", $BBOXNode->getElementsByTagName("lowerCorner")->item(0)->nodeValue); 
                $upperCornerCoordinates = explode(" ", $BBOXNode->getElementsByTagName("upperCorner")->item(0)->nodeValue); 
                
                $BBOXFilter['lowerCornerLongitude'] = floatval($lowerCornerCoordinates[0]);
                $BBOXFilter['lowerCornerLatitude'] = floatval($lowerCornerCoordinates[1]);
                $BBOXFilter['upperCornerLongitude'] = floatval($upperCornerCoordinates[0]);
                $BBOXFilter['upperCornerLatitude'] = floatval($upperCornerCoordinates[1]);
                
                $spatialBBOXFilter[] = $BBOXFilter;
            } 
        }
        $mapping['spatialBBOXFilters'] =  $spatialBBOXFilter;

        return $mapping;
    }

}
