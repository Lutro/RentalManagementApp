<?php
require_once 'scripts/helper.php';

/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

// just stub values for pagination
// calculate your own values
$offset = 0;
$limit = 25;

// always initialize a variable before use!
$conditions = [];
$parameters = [];

// conditional statements
if (!empty($_GET['Bedrooms']))
{
    // here we are using LIKE with wildcard search
    // use it ONLY if really need it
    $conditions[] = 's.bedrooms = ?';
    $parameters[] = $_GET['Bedrooms'];
}

if (!empty(valueIfExists('Bathrooms', $_GET)))
{
    // here we are using equality
    $conditions[] = 's.bathrooms = ?';
    $parameters[] = $_GET['Bathrooms'];
}

// if (!empty($_GET['car']))
// {

//     // here we are using not equality
//     $conditions[] = 'car != ?';
//     $parameters[] = $_GET['car'];
// }

// if (!empty($_GET['date_start']) && $_GET['date_end'])
// {

//     // BETWEEN
//     $conditions[] = 'date BETWEEN ? AND ?';
//     $parameters[] = $_GET['date_start'];
//     $parameters[] = $_GET['date_end'];
// }

// the main query
$sql = "SELECT s.suiteNumber as 'Suite Number', s.bedrooms as 'Bedrooms', s.bathrooms as 'Bathrooms', s.rentAmount as 'Rent Amount',
 ss.size as Size FROM suite s INNER JOIN suitesize ss ON s.bedrooms = ss.bedrooms AND
        s.bathrooms = ss.bathrooms";

// a smart code to add all conditions, if any
if ($conditions)
{
    $sql .= " WHERE ".implode(" AND ", $conditions);
}

// a search query always needs at least a `LIMIT` clause, 
// especially if no filters were used. so we have to add it to our query:
$sql .= " LIMIT ?,?";
$parameters[] = $offset;
$parameters[] = $limit;

// the usual prepare/bind/execute/fetch routine
$stmt = $dbh->prepare($sql);

$stmt->bind_param(str_repeat("s", count($parameters)), ...$parameters);
$stmt->execute();
$resultArray = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["View Suite"=>"<a href='generate_suite_card.php?suiteNumber=".$resultArray[$x]["Suite Number"]."'>View</a>"];
} 
$keys = ["Suite Number","Bedrooms","Bathrooms","Rent Amount","Size", "View Suite"];
$options = ["Bedrooms","Bathrooms"];
$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Apartment Suites", 
                 "heading"=>"Apartment Suites",
                 "inner_options"=>[0,1,2],
                 "table" => $resultArray,
                 "keys" => $keys,
                 "options" => $options ];


render_page("generic-table.twig", $renderParams);
                                     