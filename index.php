<?php


?>

<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <title> Trulia Property Finder</title>

    <link href="assets/css/docs.css" rel="stylesheet">
    <link href="assets/css/pygments-manni.css" rel="stylesheet">
</head>

<body>

<div class="container">

    <div class="jumbotron"><h3>Search Properties below by using either the latitude/longitude text field or address field</h3></div>
    <div class="bs-docs-section">

    <div class="bs-example">
<form id="form1" data-toggle="validator" role="form" action="PropertySearch.php" method="post">
    <div class="form-group">
        <label for="coordinates" class="control-label"> Search by Coordinates(Latitude/Longitude)</label>
        <input type="text" name="coordinates" class="form-control" id="coordinates" placeholder="Format:Latitude,Longitude" required >
    </div>

    <div class="form-group">
        <button type="submit" name="submitcoordinates" id="submitcoordinates" class="btn btn-primary">Submit</button>
    </div>

    </form>

        <form id="form2" data-toggle="validator" role="form" action="PropertySearch.php" method="post">
    <div class="form-group">
        <label for="inputAddress" class="control-label">Search by Address</label>
        <input type="text" name="inputAddress" class="form-control" id="inputAddress" placeholder=" Format: XYZ street Tempe AZ 85282" required>
    </div>

    <div class="form-group">
        <button type="submit" name="submitaddress" id="submitaddress" class="btn btn-primary">Submit</button>
    </div>


</form>
        </div>
        </div>

</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="assets/js/validator.js"></script>
<script src="assets/js/latitude_longitude.js"></script>
<script src="http://platform.twitter.com/widgets.js"></script>

</body>




</html>