<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
// Categories in your system
$categories = [
    "Restaurant",
    "Hotel",
    "Hospital",
    "Electrician",
    "Pet Care",
    "Shopping Mall",
    "Mechanics",
    "Beauty Parlour"
];

// User input (with typo, e.g. "restarant")
if(isset($_GET['category'])){
  $input = strtolower($_GET['category']); // you can replace with $_GET['q'] or $_POST['q']

$closest = null;
$shortest = -1;

foreach ($categories as $category) {
    // Calculate Levenshtein distance
    $distance = levenshtein($input, strtolower($category));

    // Check for exact match first
    if ($distance == 0) {
        $closest = $category;
        $shortest = 0;
        break;
    }

    // If this distance is less than the shortest distance found so far
    if ($distance < $shortest || $shortest < 0) {
        $closest = $category;
        $shortest = $distance;
    }
}

if ($shortest == 0) {
    echo "Exact match found: " . $closest;
} else {
    echo "Did you mean: " . $closest . "?";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <form action="test_service.php" method="get">
    <input type="text" name="category">
    <input type="submit" value="search" name="search">
  </form>
</body>
</html>

