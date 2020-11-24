<?php
require_once 'scripts/helper.php';

/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$options = ["bedrooms", "bathrooms"];
for ()
$options_sql = "SELECT DISTINCT " $option "FROM suite"


$filterQuery = $dbh->prepare('call getSuiteBedroomNumbers()');
$success = $filterQuery->execute();
terminate_on_query_error($success); //program will terminate on error
$bedroomsArray = $filterQuery->get_result()->fetch_all(MYSQLI_BOTH);

$filterQuery->close();


// just stub values for pagination
// calculate your own values
$offset = 0;
$limit = 10; 

// always initialize a variable before use!
$conditions = [];
$parameters = [];

// conditional statements
if (!empty($_GET['bedrooms']))
{
    // here we are using LIKE with wildcard search
    // use it ONLY if really need it
    $conditions[] = 'suite.bedrooms LIKE ?';
    $parameters[] = '%'.$_GET['bedrooms']."%";
}

if (!empty($_GET['bathrooms']))
{
    // here we are using equality
    $conditions[] = 'suite.bathrooms = ?';
    $parameters[] = $_GET['bathrooms'];
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
$sql = "SELECT * FROM suite INNER JOIN suitesize ON suite.bedrooms = suitesize.bedrooms AND
        suite.bathrooms = suitesize.bathrooms";

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

echo $sql;
// the usual prepare/bind/execute/fetch routine
$stmt = $dbh->prepare($sql);
$stmt->bind_param(str_repeat("s", count($parameters)), ...$parameters);
$stmt->execute();
$resultArray = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);



// $tenantQuery = $dbh->prepare('call getSuites()');
// $success = $tenantQuery->execute();

// terminate_on_query_error($success); //program will terminate on error

// $resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


$keys = ["suiteNumber","bedrooms","bathrooms","rentAmount","size", "hasMasterKey"];
$options = ["bedrooms","bathrooms","hasMasterKey"];
$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Apartment Suites", 
                 "heading"=>"Apartment Suites",
                 "inner_options"=>$bedroomsArray,
                 "table" => $resultArray,
                 "keys" => $keys,
                 "options" => $options ];


render_page("generic-table.twig", $renderParams);