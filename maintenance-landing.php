<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getRepairOrders()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);
$tenantQuery->close();

// Get list of contractors available
$contractorsQuery = $dbh->prepare('call getContractors()');
$success = $contractorsQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$contractorArray = $contractorsQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Create select contractor menu
$options = '';
for($x=0; $x < count($contractorArray); $x += 1){
    $options .= "<option value='".$contractorArray[$x]['Name']."'>".$contractorArray[$x]['Company']."</option>\n";
}
// $company_options = "<form action='assign_contractor.php'><label for='company_options'>Choose a contractor:</label><select name=\"name\">\n$options\n</select><input type='submit' value='Assign Contractor'></form>";
$company_options = "<label for='company_options'>Choose a contractor:</label><select name=\"name\">\n$options\n</select>";

// Create assign link
for($x=0; $x < count($resultArray); $x += 1){
    //$resultArray[$x] += ["Assign Contractor"=>"<input type='submit' value='Assign Contractor'>"];
    //["Assign Contractor"=>"<a href='assign_contractor.php?repairID=".$resultArray[$x]["repairID"]."&phone=".$resultArray[$x]["phoneNumber"]."'>Assign</a>"];
    $resultArray[$x] += ["Assign Contractor"=>$company_options];
}

$keys = ["Repair ID","Suite Number", "Priority", "Type", "Start Date","End Date","Inspection Date","Assign Contractor"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Repair Orders", 
                 "heading"=>"Repair Orders",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("maint-dashboard.twig", $renderParams);

