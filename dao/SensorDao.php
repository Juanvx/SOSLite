<?php

/*
 */

namespace SOSLite\dao;

/**
 * Description of SensorDao
 *
 * @author juanvx
 */
final class SensorDao {

    private static $instance;
    private $sensors;

    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        $dbhost = 'localhost';
        $dbname = 'SOSLite';
        // connect to mongodb
        $connection = new \MongoClient("mongodb://$dbhost");
        // select a database
        $soslite = $connection->$dbname;
        // select/create a collection
        $this->sensors = $soslite->sensors;
    }

    private function __clone() {
        
    }

    private function __wakeup() {
        
    }

    public function count() {
        return $this->sensors->count();
    }

    public function save($sensor) {
        $this->sensors->insert($sensor);
    }

    public function find($procedure) {
        return $this->sensors->findOne(array('procedure' => $procedure, "delete" => array('$ne' => true)));  
    }
    
    public function delete($procedure) {
        //$sensor = $this->sensors->findOne(['procedure' => $procedure]);
        $this->sensors->update(array("procedure" => $procedure), array('$set' => array("delete" => true)));
    }
    
    /*public function getObservableProperties() {
        return $this->sensors->find(array("delete" => array('$ne' => true)), array('observableProperty'));  
    }*/
    
    public function getFeatureOfInterest($mapping) {
        $query = array();
        $query['$and'] = array();
        
        $query['$and'][] = array('delete' => array('$ne' => true));
        
        if (count($mapping["procedures"]) != 0) {
            $query['$and'] [] = array('procedure' => array('$in' => $mapping["procedures"]));
        }
        
        if (count($mapping["observedProperties"]) != 0) {
            $query['$and'] [] = array('observableProperty' => array('$in' => $mapping["observedProperties"]));
        }
        
        /*
        if (count($mapping["featuresOfInterest"]) != 0) {
            $query['$and'] [] = array('????' => array('$in' => $mapping["featuresOfInterest"]));
        }
        
        */
        
        //Spatial filters
        foreach ($mapping["spatialBBOXFilters"] as $filter) {
            $lowerCornerLongitude = $filter['lowerCornerLongitude'];
            $lowerCornerLatitude = $filter['lowerCornerLatitude'];
            $upperCornerLongitude = $filter['upperCornerLongitude'];
            $upperCornerLatitude = $filter['upperCornerLatitude'];
            
            $query['$and'] [] = array('sensorLocation' => array(
                '$geoWithin' => array(
                    '$geometry' => array(
                        'type' => 'Polygon',
                        'coordinates' => array(
                            array(
                                array($lowerCornerLongitude, $lowerCornerLatitude),
                                array($upperCornerLongitude, $lowerCornerLatitude),
                                array($upperCornerLongitude, $upperCornerLatitude),
                                array($lowerCornerLongitude, $upperCornerLatitude),
                                array($lowerCornerLongitude, $lowerCornerLatitude)
                            )
                        )
                    )
                )
            ));
        }
        
       
        $sensorsTmp = $this->sensors->find($query);
        
        
        return $sensorsTmp;
    }

    public function findAll() {
        return $this->sensors->find(array("delete" => array('$ne' => true)));
    }

}
