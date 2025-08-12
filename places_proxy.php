<?php
// places_proxy.php

header('Content-Type: application/json');

// 1. Set your Google Places API key here
$apiKey = 'AIzaSyDc6H_CR595gpiLSAGpcBbHb1fUGb15ICk'; // Replace with your actual API key

// 2. Get the category from the query string, default to 'plumber'
$category = isset($_GET['category']) ? $_GET['category'] : 'plumber';

// 3. Set the city to Nandyal
$city = 'Nandyal';

// 4. Build the Google Places API URL
$query = urlencode("$category in $city");
$url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$query&key=$apiKey";

// 5. Initialize cURL to fetch the data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
if ($result === false) {
    echo json_encode(['error' => curl_error($ch)]);
    exit;
}
curl_close($ch);

// 6. Output the result as JSON
echo $result;
?>