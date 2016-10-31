<?php


interface LatitudeLongitudeValidator{
    
    public function isValidLongitude($longitude);
    
    public function isValidLatitude($latitude);
}