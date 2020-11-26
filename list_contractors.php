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

$tenantQuery = $dbh->prepare('call getContractors()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);


for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["Delete"=>"<a href='delete_contractor.php?name=".$resultArray[$x]["Name"]."&phone=".$resultArray[$x]["Phone Number"]."'>Delete</a>"];
}
$keys = ["Name","Phone Number","Company","Address","Email", "Work Type", "Delete"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"List of Contractors", 
                 "heading"=>"Contractors",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("contractors-table.twig", $renderParams);
