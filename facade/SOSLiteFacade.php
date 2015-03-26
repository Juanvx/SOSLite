<?php

/*
 */

namespace SOSLite\facade;

require_once './mapping/matrix/InsertSensorMapping.php';
require_once './dao/SensorDao.php';
require_once './mapping/matrix/InsertObservationMapping.php';
require_once './mapping/matrix/GetObservationMapping.php';
require_once './dao/ObservationDao.php';
require_once './mapping/matrix/GetFeatureOfInterestMapping.php';

use SOSLite\mapping\matrix\InsertSensorMapping as InsertSensorMapping;
use SOSLite\dao\SensorDao as SensorDao;
use SOSLite\mapping\matrix\InsertObservationMapping as InsertObservationMapping;
use SOSLite\mapping\matrix\GetObservationMapping as GetObservationMapping;
use SOSLite\dao\ObservationDao as ObservationDao;
use SOSLite\mapping\matrix\GetFeatureOfInterestMapping as GetFeatureOfInterestMapping;


/**
 * Description of SOSLiteFacade
 *
 * @author juanvx
 */
class SOSLiteFacade {
        
    function GetCapabilities200 ($GetCapabilities) {
        //error_log("GetCapabilities200 - SOSLiteFacade // ");
        //error_log(print_r($sectionNode, true ));
        
        $sections = array();
        $response = "<sos:Capabilities version=\"2.0.0\" xmlns:ows=\"http://www.opengis.net/ows/1.1\" "
                . "xmlns:gml=\"http://www.opengis.net/gml/3.0\" xmlns:fes=\"http://www.opengis.net/fes/2.0\" "
                . "xmlns:swes=\"http://www.opengis.net/swes/2.0\" xmlns:sos=\"http://www.opengis.net/sos/2.0\">";
        
        $sectionsNodes = $GetCapabilities->Sections;
        foreach ($sectionsNodes as $sectionsNode) {
            foreach ($sectionsNode as $sectionNode) {
                $sections[$sectionNode] = true;
            }
        }
        
        if (array_key_exists('ServiceIdentification', $sections)) {
            $serviceIdentification = "
                <ows:ServiceIdentification>
                    <ows:Title>SOSLite</ows:Title> 
                    <ows:Abstract>SOSLite Sensor Observation Service - Data Access for the Sensor Web</ows:Abstract>
                    <ows:ServiceType codeSpace=\"http://opengeospatial.net\">OGC:SOS</ows:ServiceType> 
                    <ows:ServiceTypeVersion>2.0.0</ows:ServiceTypeVersion> 
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/gfoi</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/obsByIdRetrieval</ows:Profile>
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/sensorInsertion</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/sensorDeletion</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/obsInsertion</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/resultInsertion</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/resultRetrieval</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/spatialFilteringProfile</ows:Profile>
                    <ows:Profile>http://www.opengis.net/spec/SOS/2.0/conf/soap</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SWE/2.0/conf/uml-block-components</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SWE/2.0/conf/uml-record-components</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SWE/2.0/conf/xsd-record-components</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/SWE/2.0/conf/xsd-block-components</ows:Profile>
                    <ows:Profile>http://www.opengis.net/spec/OMXML/2.0/conf/samplingPoint</ows:Profile> 
                    <ows:Profile>http://www.opengis.net/spec/OMXML/2.0/conf/observation</ows:Profile>
                    <ows:Fees>NONE</ows:Fees> 
                    <ows:AccessConstraints>NONE</ows:AccessConstraints>
                </ows:ServiceIdentification>";
            
            $response .= $serviceIdentification;
        }
        
        if (array_key_exists('ServiceProvider', $sections)) {
            $serviceProvider = "
                <ows:ServiceProvider>
                    <ows:ProviderName>SOSLite</ows:ProviderName>
                    <ows:ProviderSite xlink:href=\"http://soslite.com/\"/>
                    <ows:ServiceContact>
                        <ows:IndividualName>Nombre persona de contacto</ows:IndividualName>
                        <ows:PositionName>Service Maintainer</ows:PositionName>
                        <ows:ContactInfo>
                            <ows:Phone>
                                <ows:Voice>+49(0)251/396 371-0</ows:Voice>
                            </ows:Phone>
                            <ows:Address>
                                <ows:DeliveryPoint>Martin-Luther-King-Weg 24</ows:DeliveryPoint>
                                <ows:City>Valencia</ows:City>
                                <ows:PostalCode>48155</ows:PostalCode>
                                <ows:Country>Spain</ows:Country>
                                <ows:ElectronicMailAddress>info@soslite.com</ows:ElectronicMailAddress>
                            </ows:Address>
                        </ows:ContactInfo>
                    </ows:ServiceContact>
                </ows:ServiceProvider>";
            $response .= $serviceProvider;
        }
        
        if (array_key_exists('FilterCapabilities', $sections)) {
            $filterCapabilities = "
                <sos:filterCapabilities> 
                    <fes:Filter_Capabilities> 
                            <fes:Conformance>
                                    <fes:Constraint name=\"ImplementsQuery\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsAdHocQuery\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsFunctions\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsResourceld\">
                                        <ows:NoValues/>
                                        <ows:DefaultValue>false</ows:DefaultValue>
                                    </fes:Constraint>
                                    <fes:Constraint name=\"ImplementsMinStandardFilter\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsStandardFilter\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsMinSpatialFilter\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>true</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsSpatialFilter\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>true</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsMinTemporalFilter\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>true</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsTemporalFilter\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>true</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsVersionNav\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsSorting\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint> 
                                    <fes:Constraint name=\"ImplementsExtendedOperators\"> 
                                            <ows:NoValues/> 
                                            <ows:DefaultValue>false</ows:DefaultValue> 
                                    </fes:Constraint>
                                    <fes:Constraint name=\"ImplementsMinimumXPath\">
                                        <ows:NoValues/>
                                        <ows:DefaultValue>false</ows:DefaultValue>
                                    </fes:Constraint>
                                    <fes:Constraint name=\"ImplementsSchemaElementFunc\">
                                        <ows:NoValues/>
                                        <ows:DefaultValue>false</ows:DefaultValue>
                                    </fes:Constraint>
                                </fes:Conformance>
                            <fes:Spatial_Capabilities> 
                                    <fes:GeometryOperands> 
                                            <fes:GeometryOperand name=\"gml:Point\"/> 
                                            <fes:GeometryOperand name=\"gml:Polygon\"/> 
                                    </fes:GeometryOperands> 
                                    <fes:SpatialOperators> 
                                            <fes:SpatialOperator name=\"BBOX\"/> 
                                    </fes:SpatialOperators> 
                            </fes:Spatial_Capabilities> 
                            <fes:Temporal_Capabilities> 
                                    <fes:TemporalOperands> 
                                            <fes:TemporalOperand name=\"gml:TimePeriod\"/> 
                                            <fes:TemporalOperand name=\"gml:TimeInstant\"/> 
                                    </fes:TemporalOperands> 
                                    <fes:TemporalOperators> 
                                            <fes:TemporalOperator name=\"During\"/> 
                                            <fes:TemporalOperator name=\"TEquals\"/> 
                                    </fes:TemporalOperators> 
                            </fes:Temporal_Capabilities> 
                    </fes:Filter_Capabilities> 
                </sos:filterCapabilities>"; 
            
            $response .= $filterCapabilities;
        }

        if (array_key_exists('InsertionCapabilities', $sections)) {
            $insertionCapabilities = " 
                <sos:extension>
                    <sos:InsertionCapabilities>
                        <sos:procedureDescriptionFormat>http://www.opengis.net/sensorML/1.0.1</sos:procedureDescriptionFormat>
                        <sos:featureOfInterestType>http://www.opengis.net/def/samplingFeatureType/OGC-OM/2.0/SF_SamplingPoint</sos:featureOfInterestType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_Measurement</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_CountObservation</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_TruthObservation</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_CategoryObservation</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_TextObservation</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/ComplexObservation</sos:observationType>
                        <sos:supportedEncoding>http://www.opengis.net/swe/2.0/TextEncoding</sos:supportedEncoding>
                    </sos:InsertionCapabilities>
                </sos:extension>"; 
            
            $response .= $insertionCapabilities;
        }
        
        if (array_key_exists('Contents', $sections)) {
            $observablePropertText = "";
            $offeringText = "";
            $sensors = SensorDao::getInstance()->findAll();
            foreach ($sensors as $sensor) {
                if (array_key_exists('observableProperty', $sensor)) {
                    $observableProperties = $sensor['observableProperty'];
                    foreach ($observableProperties as $observableProperty) {
                        $observablePropertText .= "<swes:observableProperty>" . $observableProperty . "</swes:observableProperty>";
                    }
                }

                $phenomenonTime = ObservationDao::getInstance()->getPhenomenonTime($sensor['offering']);
                $phenomenonTimeText = "<gml:beginPosition/><gml:endPosition/>";
                if (array_key_exists('beginPosition', $phenomenonTime)) {
                    $phenomenonTimeText = "<gml:beginPosition>" . $phenomenonTime["beginPosition"] . "</gml:beginPosition>
                   <gml:endPosition>" . $phenomenonTime["endPosition"] . "</gml:endPosition>";
                }

                $offeringText .= "
                    <sos:ObservationOffering>
                        <swes:identifier>" . $sensor['offering'] . "</swes:identifier>
                        <swes:name>Offering for sensor " . substr($sensor['procedure'], -1) . "</swes:name>
    ￼                   <swes:procedure>" . $sensor['procedure'] . "</swes:procedure>
                        <swes:procedureDescriptionFormat>http://www.opengis.net/sensorML/1.0.1</swes:procedureDescriptionFormat>
                        <sos:observedArea>
                            <gml:Envelope srsName=\"http://www.opengis.net/def/crs/EPSG/0/4326\">
                                <gml:lowerCorner>" . $sensor['sensorLocation']['coordinates'][0] . $sensor['sensorLocation']['coordinates'][1] . "</gml:lowerCorner>
                                <gml:upperCorner>" . $sensor['sensorLocation']['coordinates'][0] . $sensor['sensorLocation']['coordinates'][1] . "</gml:upperCorner>
                            </gml:Envelope>
                        </sos:observedArea>
                        <sos:phenomenonTime>
                            <gml:TimePeriod gml:id=\"phenomenonTime\">"
                            . $phenomenonTimeText .    
                            "</gml:TimePeriod>
                        </sos:phenomenonTime>
                    </sos:ObservationOffering>";

            }

            if ($offeringText != "") {
                $offeringText = "<swes:offering>" . $offeringText . "</swes:offering>";
            }

            $contents = "            
                <sos:contents>
                    <sos:Contents>
                        <swes:procedureDescriptionFormat>http://www.opengis.net/sensorML/2.0</swes:procedureDescriptionFormat>
                        <sos:responseFormat>http://www.opengis.net/om/2.0</sos:responseFormat>
                        <sos:featureOfInterestType>http://www.opengis.net/def/samplingFeatureType/OGC-OM/2.0/SF_SamplingPoint</sos:featureOfInterestType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_Measurement</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_CountObservation</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_TruthObservation</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_CategoryObservation</sos:observationType>
                        <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/OM_TextObservation</sos:observationType>
                       <sos:observationType>http://www.opengis.net/def/observationType/OGC-OM/2.0/ComplexObservation</sos:observationType>"
                        .$observablePropertText
                        .$offeringText 
                        ///multiples generados
                    ."</sos:Contents>
                </sos:contents>";
        
            $response .= $contents;
        }
        
        $response .= "</sos:Capabilities>";
        
        return new \SoapVar($response, XSD_ANYXML);
    }

    function DescribeSensor200($DescribeSensor) {               
        //error_log("DescribeSensor200 - SOSLiteFacade // " );
        
        $procedure = $DescribeSensor->procedure;
        $sensor = SensorDao::getInstance()->find($procedure);
        $descriptionFile = $sensor["descriptionFile"];
        $procedureDescription = file_get_contents($descriptionFile);
        
        $response = "
        <swes:DescribeSensorResponse xmlns:swes=\"http://www.opengis.net/swes/2.0\">
            <swes:procedureDescriptionFormat>http://www.opengis.net/sensorml/2.0</swes:procedureDescriptionFormat>
            <swes:description>
                <swes:data>"
                    .$procedureDescription.
                "</swes:data>
            </swes:description>
        </swes:DescribeSensorResponse>";
        return new \SoapVar($response, XSD_ANYXML);
    }

    function InsertSensor200($InsertSensor) {
        //error_log("InsertSensor200 - SOSLiteFacade // ");
        
        $xml_string = file_get_contents("php://input");
        $mapping = InsertSensorMapping::map($xml_string);
        
        $sensorCount = SensorDao::getInstance()->count();
        $mapping["procedure"] = "http://localhost/SOSLite/sensors/Sensor" . $sensorCount;
        $mapping["offering"] = "http://localhost/SOSLite/offerings/Offering" . $sensorCount;
        
        SensorDao::getInstance()->save($mapping);
 
        $response = "
            <swes:InsertSensorResponse xmlns:swes=\"http://www.opengis.net/swes/2.0\">
                <!-- identifier assigned by the SOS for this procedure -->
                <swes:assignedProcedure>"
                    .$mapping["procedure"].
                "</swes:assignedProcedure>
                <swes:assignedOffering>"
                    .$mapping["offering"].
                "</swes:assignedOffering>
            </swes:InsertSensorResponse>";
        
        return new \SoapVar($response, XSD_ANYXML);
    }
    
    function DeleteSensor200($DeleteSensor) {
        $procedure = $DeleteSensor->procedure;
        SensorDao::getInstance()->delete($procedure);
        $response = "<swes:DeleteSensorResponse>
            <swes:deletedProcedure>" . $procedure . "</swes:deletedProcedure>
        </swes:DeleteSensorResponse>";
        
        return new \SoapVar($response, XSD_ANYXML);
    }
    
    function GetObservation200 ($GetObservation) {        
        $xml_string = file_get_contents("php://input");
        $mapping = GetObservationMapping::map($xml_string);
        
        $observations = ObservationDao::getInstance()->find($mapping);
        
        $observationsData = "";
        foreach ($observations as $observation) {
            $observationsData .= "<sos:observationData>";
            $observationsData .= file_get_contents($observation["observationFile"]);
            $observationsData .= "</sos:observationData>";
        }
        
        $response = "
            <sos:GetObservationResponse xmlns:sos=\"http://www.opengis.net/sos/2.0\">"
                .$observationsData.
            "</sos:GetObservationResponse>";
        
        return new \SoapVar($response, XSD_ANYXML);
    }
    
    function InsertObservation200 ($InsertObservation) {        
        $xml_string = file_get_contents("php://input");
        $mapping = InsertObservationMapping::map($xml_string);
         
        $observationCount = ObservationDao::getInstance()->count();
        
        $observationsIds = "";
        foreach ($mapping as $observation) {
            $observation["observationId"] = "http://localhost/SOSLite/observations/obs" . $observationCount++;
            ObservationDao::getInstance()->save($observation);
            
            $observationsIds .= "<sos:observation>";
            $observationsIds .= $observation["observationId"];
            $observationsIds .= "</sos:observation>";
        }
        
        $response = "
            <sos:InsertObservationResponse xmlns:sos=\"http://www.opengis.net/sos/2.0\"/>"
                .$observationsIds. 
            "</sos:InsertObservationResponse>";
        
        return new \SoapVar($response, XSD_ANYXML);
    }
    
    function GetFeatureOfInterest200 ($GetFeatureOfInterest) {
        //error_log("GetFeatureOfInterest200 - SOSLiteFacade // ");
        
        $xml_string = file_get_contents("php://input");
        $mapping = GetFeatureOfInterestMapping::map($xml_string);
        
        $sensors = SensorDao::getInstance()->getFeatureOfInterest($mapping);
        
        $featureOfInterestText = "";
        foreach ($sensors as $sensor) {
            $featureOfInterestText .= "<sos:featureMember>" . $sensor["featuresOfInterest"] . "</sos:featureMember>";
        }

        $response = "
            <sos:GetFeatureOfInterestResponse xmlns:sos=\"http://www.opengis.net/sos/2.0\" xmlns:gml=\"http://www.opengis.net/gml/3.2\">"
                . $featureOfInterestText .
            "</sos:GetFeatureOfInterestResponse>";
        
        return new \SoapVar($response, XSD_ANYXML);
    }
}
