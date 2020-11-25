<?php
require_once 'scripts/helper.php';

render_page("admin-dashboard.twig",["nav"=>navList(), 
"address" =>address(), 
"title"=>title(),
"page_title"=>"Management", 
"heading"=>"Management",
"address" =>address()]);
