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
$company_names = array_column($contractorArray, 'company');
var_dump($company_names);
echo $company_names[0];
echo $company_names[2];


$company_options = "<select name=\"select\">";
for($x=0; $x < count($company_names); $x += 1){
    $company_options += ("<option value=\"\">".
                            htmlspecialchars($company_names[$x]).
                            "</option>")
                        ;
}
$company_options += "</select>";


for($x=0; $x < count($resultArray); $x += 1){
    $resultArray[$x] += ["Assign Contractor"=>"<a href='list_contractors.php?rid=".$resultArray[$x]["repairID"]."'>Assign</a>
    <select name=\"select\"></select>"];
    $resultArray[$x] += ["Contractors"=>$company_options];
}
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

$keys = ["repairID","suiteNumber", "priority", "type", "inspectionDate","Assign Contractor","Contractors"];

$renderParams = ["nav"=>navList(), 
                 "address" =>address(), 
                 "title"=>title(),
                 "page_title"=>"Unassigned Repair Orders", 
                 "heading"=>"Unassigned Repair Orders",
                 "table" => $resultArray,
                 "keys" => $keys ];


render_page("generic-table.twig", $renderParams);