<?php

/*
 */

namespace SOSLite\dao;

/**
 * Description of ObservationDao
 *
 * @author juanvx
 */
final class ObservationDao {

    private static $instance;
    private $observations;

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
        $this->observations = $soslite->observations;
    }

    private function __clone() {
        
    }

    private function __wakeup() {
        
    }

    public function count() {
        return $this->observations->count();
    }

    public function save($observation) {
        $observation['phenomenonTime'] =  new \MongoDate(strtotime($observation['phenomenonTime']));
        $observation['resultTime'] =  new \MongoDate(strtotime($observation['resultTime']));
        $this->observations->insert($observation);
    }

    public function getPhenomenonTime($offering) {
        $phenomenonTime = array();
        
        $observationsTmp = $this->observations->find(array("offering" => $offering));
        $observationsTmp->sort(array("phenomenonTime" => 1))->limit(1);
        foreach ($observationsTmp as $observation) {
           $phenomenonTime['beginPosition'] = date(DATE_ATOM, $observation["phenomenonTime"]->sec);
        }
        
        $observationsTmp = $this->observations->find(array("offering" => $offering));
        $observationsTmp->sort(array("phenomenonTime" => -1))->limit(1);
        foreach ($observationsTmp as $observation) {
            $phenomenonTime['endPosition'] = date(DATE_ATOM, $observation["phenomenonTime"]->sec);
        }

        return $phenomenonTime;
    }
    
    public function find($mapping) {
        $query = array();
        $sort = array();
        //Criteria query
        $query['$and'] = array();
        if (count($mapping["procedures"]) != 0) {
            $query['$and'] [] = array('procedure' => array('$in' => $mapping["procedures"]));
        }
        if (count($mapping["offerings"]) != 0) {
            $query['$and'] [] = array('offering' => array('$in' => $mapping["offerings"]));
        }
        if (count($mapping["observedProperties"]) != 0) {
            $query['$and'] [] = array('observedProperty' => array('$in' => $mapping["observedProperties"]));
        }
        if (count($mapping["featuresOfInterest"]) != 0) {
            $query['$and'] [] = array('featureOfInterest' => array('$in' => $mapping["featuresOfInterest"]));
        }
        //Temporal filters
        foreach ($mapping["phenomenonTemporalDuringFilters"] as $filter) {
            $start = new \MongoDate(strtotime($filter['beginPosition']));
            $end = new \MongoDate(strtotime($filter['endPosition']));
            $query['$and'] [] = array("phenomenonTime" => array('$gt' => $start, '$lte' => $end));
        }
        foreach ($mapping["resultTemporalDuringFilters"] as $filter) {
            $start = new \MongoDate(strtotime($filter['beginPosition']));
            $end = new \MongoDate(strtotime($filter['endPosition']));
            $query['$and'] [] = array("resultTime" => array('$gt' => $start, '$lte' => $end));
        }
        foreach ($mapping["phenomenonTemporalEqualsFilters"] as $filter) {
            $time = $filter['timePosition'];
            if ($time == "first") {
                $sort ["phenomenonTime"] = 1;
            } else {
                if ($time == "latest") {
                    $sort ["phenomenonTime"] = -1;
                } else {
                    $query['$and'] [] = array("phenomenonTime" => new \MongoDate(strtotime($time))); 
                }
            }
        }
        foreach ($mapping["resultTemporalEqualsFilters"] as $filter) {
            $time = $filter['timePosition'];
            if ($time == "first") {
                $sort ["resultTime"] = 1;
            } else {
                if ($time == "latest") {
                    $sort ["resultTime"] = -1;
                } else {
                    $query['$and'] [] = array("resultTime" => new \MongoDate(strtotime($time))); 
                }
            }
        }
        
        //Spatial filters
        foreach ($mapping["spatialBBOXFilters"] as $filter) {
            $lowerCornerLongitude = $filter['lowerCornerLongitude'];
            $lowerCornerLatitude = $filter['lowerCornerLatitude'];
            $upperCornerLongitude = $filter['upperCornerLongitude'];
            $upperCornerLatitude = $filter['upperCornerLatitude'];
            
            $query['$and'] [] = array('samplingPoint' => array(
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

        if (count($query['$and']) == 0){
            $observationsTmp = $this->observations->find();
        } else {
            $observationsTmp = $this->observations->find($query);
        } 
        
        if (count($sort) != 0){
            $observationsTmp->sort($sort)->limit(1);
        }

        return $observationsTmp;
    }

    public function findAll() {
        return $this->observations->find();
    }

}
