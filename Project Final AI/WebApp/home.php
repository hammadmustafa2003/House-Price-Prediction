<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>House Price Prediction</title>
    <link href="style.css" rel="stylesheet">
</head>

<body>

    <h1>House Price Prediction</h1>

    <!-- A form with location (dropdown), 
        city (dropdown), 
        province_name (dropdown), 
        latitude, longtitude, 
        baths, 
        areas_sqft, 
        bedrooms
    -->

    <label for="province_name">Province Name</label>
    <select name="province_name" id="province_name">
        <option value="">Select Province</option>
        <?php
        $url = "http://192.168.0.111:9999/provinces";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);
        foreach ($data['provinces'] as $province) {
            // province is plain string
            echo "<option value='$province'>$province</option>";
        }
        ?>
    </select>
    <br>
    <label for="city">City</label>
    <select name="city" id="city">
        <option value="">Select City</option>
    </select>
    <br>
    <label for="location">Location</label>
    <select name="location" id="location">
        <option value="">Select Location</option>
    </select>
    <br>
    <label for="latitude">Latitude</label>
    <input type="text" name="latitude" id="latitude">
    <br>
    <label for="longitude">Longitude</label>
    <input type="text" name="longitude" id="longitude">
    <br>
    <label for="baths">Baths</label>
    <input type="text" name="baths" id="baths">
    <br>
    <label for="areas_sqft">Areas (sqft)</label>
    <input type="text" name="areas_sqft" id="areas_sqft">
    <br>
    <label for="bedrooms">Bedrooms</label>
    <input type="text" name="bedrooms" id="bedrooms">
    <br>
    <button id="predict">Predict</button>


    <script>
        var latitudeInput = document.getElementById("latitude");
        var longitudeInput = document.getElementById("longitude");
        latitudeInput.readOnly = true;
        longitudeInput.readOnly = true;


        var cityDropdown = document.getElementById("city");
        var locationDropdown = document.getElementById("location");
        var provinceDropdown = document.getElementById("province_name");

        provinceDropdown.onchange = function () {
            var selectedProvince = provinceDropdown.value;
            var url = "http://192.168.0.111:9999/cities?province_name=" + selectedProvince;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    var cities = data.cities;
                    cityDropdown.innerHTML = "";
                    for (var i = 0; i < cities.length; i++) {
                        var option = document.createElement("option");
                        option.value = cities[i];
                        option.text = cities[i];
                        cityDropdown.appendChild(option);
                    }
                }
            }
            xhr.send();
        }

        cityDropdown.onchange = function () {
            var selectedCity = cityDropdown.value;
            var url = "http://192.168.0.111:9999/locations?city=" + selectedCity;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    var locations = data.locations;
                    locationDropdown.innerHTML = "";
                    for (var i = 0; i < locations.length; i++) {
                        var option = document.createElement("option");
                        option.value = locations[i];
                        option.text = locations[i];
                        locationDropdown.appendChild(option);
                    }
                }
            }
            xhr.send();
        }

        locationDropdown.onchange = function () {
            var selectedLocation = locationDropdown.value;
            var url = "http://192.168.0.111:9999/coordinates?city=" + cityDropdown.value + "&location=" + selectedLocation;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    latitudeInput.value = data.latitude;
                    longitudeInput.value = data.longitude;
                }
            }
            xhr.send();
        }


        var predictButton = document.getElementById("predict");
        predictButton.onclick = function () {
            var latitude = latitudeInput.value;
            var longitude = longitudeInput.value;
            var baths = document.getElementById("baths").value;
            var areas_sqft = document.getElementById("areas_sqft").value;
            var bedrooms = document.getElementById("bedrooms").value;
            // http://192.168.0.111:9999/predict?location=0&city=2&province_name=0&latitude=31.456&longtitude=74.4143&baths=1&areas_sqft=5600&bedrooms=10
            var url = "http://192.168.0.111:9999/predict?location=" + locationDropdown.value + "&city=" + cityDropdown.value + "&province_name=" + provinceDropdown.value + "&latitude=" + latitude + "&longtitude=" + longitude + "&baths=" + baths + "&areas_sqft=" + areas_sqft + "&bedrooms=" + bedrooms;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    alert("Predicted Price: " + data.predicted_price + " PKR");
                }
            }
            xhr.send();
        }

    </script>



</body>

</html>