<?php

/* 
 */

namespace SOSLite\mapping\matrix;

/**
 * Description of GetObservationMapping
 *
 * @author juanvx
 */

class GetObservationMapping {
    
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
        
        $offerings = array();
        $offeringNodes = $dom->getElementsByTagName("offering");
        foreach ($offeringNodes as $offeringNode) {
            $offerings[] =  $offeringNode->nodeValue;
        } 
        $mapping['offerings'] =  $offerings;
        
        $observedProperties = array();
        $observedPropertyNodes = $dom->getElementsByTagName("observedProperty");
        foreach ($observedPropertyNodes as $observedPropertyNode) {
            $observedProperties[] =  $observedPropertyNode->nodeValue;
        } 
        $mapping['observedProperties'] =  $observedProperties;
        
        $featureOfInterest = array();
        $featureOfInterestNodes = $dom->getElementsByTagName("featureOfInterest");
        foreach ($featureOfInterestNodes as $featureOfInterestNode) {
            $featureOfInterest[] =  $featureOfInterestNode->nodeValue;
        } 
        $mapping['featuresOfInterest'] =  $featureOfInterest;
        
        //Temporal filters
        $resultTemporalDuringFilter = array();
        $phenomenonTemporalDuringFilter = array();
        $resultTemporalEqualsFilter = array();
        $phenomenonTemporalEqualsFilter = array();
        $temporalFilterNodes = $dom->getElementsByTagName("temporalFilter");
        foreach ($temporalFilterNodes as $temporalFilterNode) {
            $duringFilter = array();
            $duringNodes = $temporalFilterNode->getElementsByTagName("During");
            foreach ($duringNodes as $duringNode) { 
                //TimePeriod -> beginPosition, endPosition: <date>
                $duringFilter['type'] = "During";
                $duringFilter['valueReference'] = $duringNode->getElementsByTagName("ValueReference")->item(0)->nodeValue;
                $duringFilter['beginPosition'] = $duringNode->getElementsByTagName("beginPosition")->item(0)->nodeValue;
                $duringFilter['endPosition'] = $duringNode->getElementsByTagName("endPosition")->item(0)->nodeValue;
                
                if ($duringNode->getElementsByTagName("ValueReference")->item(0)->nodeValue == "resultTime"){
                    $resultTemporalDuringFilter[] =  $duringFilter;
                } else {
                    $phenomenonTemporalDuringFilter[] = $duringFilter;
                }
            } 
            
            $equalsFilter = array();
            $equalsNodes = $temporalFilterNode->getElementsByTagName("TEquals");
            foreach ($equalsNodes as $equalsNode) {            
                //TimeInstant -> timePosition: first, latest or <date>
                $equalsFilter['type'] = "TEquals";
                $equalsFilter['valueReference'] = $equalsNode->getElementsByTagName("ValueReference")->item(0)->nodeValue;
                $equalsFilter['timePosition'] =  $equalsNode->getElementsByTagName("timePosition")->item(0)->nodeValue;
                
                if ($equalsFilter['valueReference'] == "resultTime"){
                    $resultTemporalEqualsFilter[] =  $equalsFilter;
                } else {
                    $phenomenonTemporalEqualsFilter[] = $equalsFilter;
                }
            } 
        } 
        $mapping['resultTemporalDuringFilters'] =  $resultTemporalDuringFilter;
        $mapping['phenomenonTemporalDuringFilters'] =  $phenomenonTemporalDuringFilter;
        $mapping['resultTemporalEqualsFilters'] =  $resultTemporalEqualsFilter;
        $mapping['phenomenonTemporalEqualsFilters'] =  $phenomenonTemporalEqualsFilter;
        
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