<!-- /**
 * Lists all contractors on a table.(no period for file headers)
 *
 * Gets array of values from sql using the keys to access the result array and displays values on a table.
 *
 * @see Function/method/class relied on
 * @link http://localhost/CPSC2221_Project/
 * @global type $varname Description.
 * @global type $varname Description.
 *
 * @param type $var Description.
 * @param type $var Optional. Description. Default.
 * @return type Description.
 Arrays
 * @type type $key Description. Default 'value'. Accepts 'value', 'value'.
*                     (aligned with Description, if wraps to a new line)
*/ -->
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
if (!empty($_GET['Name']))
{
    // here we are using LIKE with wildcard search
    // use it ONLY if really need it
    $conditions[] = 'Name LIKE ?';
    $parameters[] = '%'.$_GET['Name']."%";
}

if (!empty(valueIfExists('PhoneNumber', $_GET)))
{
    
    $conditions[] = 'Phone LIKE ?';
    $parameters[] = '%'.$_GET['PhoneNumber']."%";
}

// the main query
$sql = "SELECT name as 'Name', phoneNumber as 'Phone Number', company as 'Company', address as 'Address', email as 'Email', workType as 'Work Type'
FROM contractor";

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
    $resultArray[$x] += ["Delete"=>"<a href='delete_contractor.php?name=".$resultArray[$x]["Name"]."&phone=".$resultArray[$x]["Phone Number"]."'>Delete</a>"];
}
$keys = ["Name","Phone Number","Company","Address","Email", "Work Type", "Delete"];
$options = ["Name","PhoneNumber"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"List of Contractors", 
                 "heading"=>"Contractors",
                 "table" => $resultArray,
                 "keys" => $keys,
                "options" => $options ];


render_page("contractors-table.twig", $renderParams);
