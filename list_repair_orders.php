<?php
require_once 'scripts/helper.php';


/*get the tenant information, to populate the tenant list from the database */
$dbh = get_database_object();
/* check connection */
terminate_on_connect_error();

$tenantQuery = $dbh->prepare('call getUnassignedRepairOrders()');
$success = $tenantQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$resultArray = $tenantQuery->get_result()->fetch_all(MYSQLI_ASSOC);
$tenantQuery->close();

// Get list of contractors available
$contractorsQuery = $dbh->prepare('call getContractors()');
$success = $contractorsQuery->execute();

terminate_on_query_error($success); //program will terminate on error

$contractorArray = $contractorsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
//var_dump($contractorArray);
// $company_names = array_column($contractorArray, 'company');
// var_dump($company_names);
// echo $company_names[0];
// echo $company_names[2];


// $company_options = "<select name=\"select\">";
// for($x=0; $x < count($company_names); $x += 1){
//     $company_options += ("<option value=\"\">".
//                             htmlspecialchars($company_names[$x]).
//                             "</option>")
//                         ;
// }
// $company_options += "</select>";


// <label for=\"contractor\">Contractor</label>
// <select class=\"\" name=\"contractor\">
//     {% for contractor in ".$contractorArray[2][] %}
//     <option value=\"".{{prov}}." {% if prov == contractor %} selected {% endif %} > {{prov}} </option> 
//    {% endfor %}
// </select>
// "];

//         echo '<select name="select">';
// while($row=mysql_fetch_array($contractorArray))
// {
    //     echo '<option value="' . htmlspecialchars($row["company"]) . '">' 
//         . htmlspecialchars($row[2]["company"]) 
//         . '</option>';
// }
// echo '</select>';

// Create select contractor menu
$options = '';
for($x=0; $x < count($contractorArray); $x += 1){
    $options .= "<option value='".$contractorArray[$x]['name']."'>".$contractorArray[$x]['company']."</option>\n";
}
$company_options = "<form action='assign_contractor.php'><label for='company_options'>Choose a contractor:</label><select name=\"name\">\n$options\n</select><input type='submit' value='Assign Contractor'></form>";

// Create assign link
for($x=0; $x < count($resultArray); $x += 1){
    //$resultArray[$x] += ["Assign Contractor"=>"<input type='submit' value='Assign Contractor'>"];
    //["Assign Contractor"=>"<a href='assign_contractor.php?repairID=".$resultArray[$x]["repairID"]."&phone=".$resultArray[$x]["phoneNumber"]."'>Assign</a>"];
    $resultArray[$x] += ["contractors"=>$company_options];
}

$keys = ["repairID","suiteNumber", "priority", "type", "inspectionDate","Assign Contractor","contractors"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Unassigned Repair Orders", 
                 "heading"=>"Unassigned Repair Orders",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("repair-orders-stats.twig", $renderParams);