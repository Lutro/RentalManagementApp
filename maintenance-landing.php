<?php
require_once 'scripts/helper.php';

render_page("maint-dashboard.twig",["nav"=>navList(), 
"address" =>address(), 
"title"=>title(),
"page_title"=>"Maintenance", 
"heading"=>"Maintenance",
"address" =>address()]);
