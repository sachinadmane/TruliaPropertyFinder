<?php
require(dirname(__FILE__) . '/LatitudeLongitudeValidator.php');

class PropertySearch implements LatitudeLongitudeValidator
{

    /**
     * Calculates the distance between latitude and longitude pairs
     * Uses the haversine formula
     * @param float $latitudeFrom Latitude of start point
     * @param float $longitudeFrom Longitude of start point
     * @param float $latitudeTo Latitude of target point
     * @param float $longitudeTo Longitude of target point
     * @param int $earthRadius in miles
     * @return float Distance between coordinates in miles
     */

    function getPropertiesByCoordinates(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959)
    {

        //edge case check
        if (!$this->isValidLatitude($latitudeFrom) || !$this->isValidLongitude($longitudeFrom) || !$this->isValidLatitude($latitudeTo) || !$this->isValidLongitude($longitudeTo)) {

            return -1;
        }
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * Calculates distance between an address and the set of property listings
     * Uses the google maps API to get coordinates for the address entered by the user
     * Returns -1 on error
     * @param string $addressFrom address entered by the user
     * @param float $latitudeTo latitude of property listing from the sample file [deg decimal]
     * @param float $longitudeTo longitude of property listing from the sample file [deg decimal]
     *
     * @return float Distance in miles between the address entered by the user and the property listings
     */

    function getPropertiesByAddress($addressFrom, $latitudeTo, $longitudeTo)
    {
        //Change address format
        $formattedAddrFrom = urlencode($addressFrom);


        //Send request and receive json data
        $geocodeFrom = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $formattedAddrFrom . '&sensor=false');
        $outputFrom = json_decode($geocodeFrom);


        //if we receive latitude and longitude matches for the address entered by the user
        if (isset($outputFrom->results[0]) && $outputFrom->status === "OK") {

            $latitudeFrom = is_object($outputFrom->results[0]->geometry) ? $outputFrom->results[0]->geometry->location->lat : null;
            $longitudeFrom = is_object($outputFrom->results[0]->geometry) ? $outputFrom->results[0]->geometry->location->lng : null;


        } else {

            $latitudeFrom = NULL;

            $longitudeFrom = NULL;
        }


        //if there is no match found for latitude and longitude entered by the user return -1
        if (!$latitudeFrom || !$longitudeFrom) {

            return -1;
        }


        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles;
    }


    function isValidLatitude($latitude)
    {

        return (boolean)preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $latitude);
    }

    function isValidLongitude($longitude)
    {

        return (boolean)preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $longitude);
    }


}


if (isset($_REQUEST['submitcoordinates'])) {


    //sanitize our request variables to make sure they are safe

    $coordinates = filter_var(($_POST['coordinates']), FILTER_SANITIZE_STRING);


    //get the latitude and longitude values from the form submitted by the user
    if (strpos($coordinates, ",") !== false) {

        $coordinates = explode(",", $coordinates);
    }

    $latitude = trim($coordinates[0]);

    $longitude = trim($coordinates[1]);

    //retrieve the sample property listings from the json file
    $properties = file_get_contents(dirname(__FILE__) . '/Properties.json');


    //decode the json into an array
    $properties = json_decode($properties, 1);

    //create an object of this class

    $propertySearch = new PropertySearch();

    //these would be our search results that fall within 20 miles

    $matchingProperties = array();

    //loop through the sample listings file and get all properties that are within 20 miles

    foreach ($properties as $key => $value) {


        $listingLatitude = $value['lat'];

        $listingLongitude = $value['long'];


        $distanceInMiles = $propertySearch->getPropertiesByCoordinates($latitude, $longitude, $listingLatitude, $listingLongitude);


        if ($distanceInMiles !== -1 && $distanceInMiles >= 0 && $distanceInMiles < 20) {

            //add these properties to the matching array
            $value["distanceInMiles"] = $distanceInMiles;
            $matchingProperties[] = $value;
        }

    }


} else if (isset($_REQUEST['submitaddress'])) {

    //sanitize our request variables
    $inputAddress = trim($_POST['inputAddress']);
    $inputAddress = filter_var($inputAddress, FILTER_SANITIZE_STRING);
    //retrieve the sample property listings from the json file
    $properties = file_get_contents(dirname(__FILE__) . '/Properties.json');


    //list of properties as an array
    $properties = json_decode($properties, 1);

    $propertySearch = new PropertySearch();

    //this will give us search results that are within the 20 mile radius

    $matchingProperties = array();


    foreach ($properties as $key => $value) {


        //latitude value from the sample file
        $listingLatitude = $value['lat'];

        //longitude value from the sample file

        $listingLongitude = $value['long'];

        $distanceInMiles = $propertySearch->getPropertiesByAddress($inputAddress, $listingLatitude, $listingLongitude);

        //valid match

        if ($distanceInMiles !== -1 && $distanceInMiles >= 0 && $distanceInMiles < 20) {

            //add these properties to the matching array
            $value["distanceInMiles"] = $distanceInMiles;
            $matchingProperties[] = $value;
        }

    }
}

?>

<html>

<head>

    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container">

    <?
    if (count($matchingProperties) > 0) { ?>

    <!--    Display search results     !-->

    <div class="row">

        <div class="col-lg-12">

            <h3> Showing All property listing within 20 miles</h3>
        </div>

        <? foreach ($matchingProperties as $key => $value) {


            $thumbnail = $value['image_url'];

            $caption = $value['address'];

            $distanceInMiles = $value["distanceInMiles"];

            $distanceInMiles = number_format($distanceInMiles, 2);


            ?>

            <!--    Property images     !-->
            <div class="col-lg-3 col-md-4 col-sm-3 col-xs-3">
                <div class="thumbnail">
                    <h3><?= $caption ?></h3>
                    <img src="<?= $thumbnail ?>" class="img-thumbnail img-responsive">
                </div>
                <div class="caption"><b><?= $distanceInMiles . " " . "miles away" ?> </b></div>
            </div>


        <? } ?>

    </div>

</div>
<?
} else { ?>

    <!--    Display No results found     !-->
    <div class="page-header">
        <h1>No property listings found within 20 miles of that search criteria.</h1>
    </div


<? } ?>


</body>
</html>
