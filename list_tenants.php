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
$sql = "SELECT occupant.id, occupant.name as 'Name', occupant.phone as 'Phone', occupant.email as 'Email', occupant.numberOfBikes as 'Number of Bikes', occupant.storageLockerNumber as 'Storage Locker Number', tenant.numberOfPets as 'Number of Pets', tenant.leaseStart as 'Lease Start', tenant.leaseEnd as 'Lease End'
FROM occupant
INNER JOIN tenant ON occupant.ID = tenant.ID";

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
    $resultArray[$x] += ["Edit"=>"<a href='edit_tenant.php?id=".$resultArray[$x]["id"]."'>edit</a>"];
}
for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["Delete"=>"<a href='delete_tenant.php?id=".$resultArray[$x]["id"]."'>delete</a>"];
}
$keys = ["Name","Phone","Email","Number of Bikes","Storage Locker Number","Number of Pets", "Lease Start", "Lease End","Edit","Delete"];
$options = ["Name","PhoneNumber"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Tenant List", 
                 "heading"=>"Tenants",
                 "table" => $resultArray,
                 "keys" => $keys                ,
                 "options" => $options ];


render_page("tenants-table.twig", $renderParams);
