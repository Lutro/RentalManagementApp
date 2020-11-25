<?php
require_once 'program_constants.php';
require_once 'vendor/autoload.php';

function render_page($templateName, $parameters){

    $loader = new \Twig\Loader\FilesystemLoader(TemplateDirectory);
    $twig = new \Twig\Environment($loader, [ ]);
    
    echo $twig->render($templateName, $parameters);
}
//Create a new sql connection object, with the paramters
//for the application
function valueIfExists($key,$array){
    if(array_key_exists($key, $array)){
        return $array[$key];
    } else  {
        return "";
    }
}

//Constants for pages
function navList(){
    return [[ "link" => 'list_suites.php', "text"=>'Suites'],
            [ "link" => 'list_tenants.php', "text"=> 'Tenants'],
            [ "link" => 'list_contractors.php', "text"=>'Contractors'],
	    [ "link" => 'list_repair_orders.php', "text"=>'Maintenance'],
	    [ "link" => 'mgmt_stats.php', "text"=>'Admin'],
            //[ "link" => 'availability.php', "text"=>'Availability'],
            //[ "link" => 'mgmt_stats.php', "text"=>'Reports']];
	    [ "link" => 'user_profile.php', "text"=>'Reports']];
}

// Defines the address element for a page
function address(){
    return "Golden Otters Apartments<br>".
            "123 Margarita Avenue<br>".
            "Vancouver B.C.<br>".
            "Canada V5Y 2Z6<br>".
            "604.555.5511<br>";

}

function title(){
    return "Renty: Easier Suite Management on the go";
}

//Create a new sql connection object, with the paramters
//for the application
function get_database_object(){
    return new mysqli(DBHost,DBUser,DBPWD,DBName);
}
//Check SQL connect status, and terminate if there is an error
function terminate_on_connect_error(){
    if (mysqli_connect_errno()) {
        render_page("error.twig", array( "message"=> mysqli_connect_error()));
        exit();
    } 
}
//check if query if successful, terminate if there is an error
function terminate_on_query_error($success){
    if(!$success){                
        render_page("error.twig",array("message" => "Query error"));
        exit();
    }   
}
