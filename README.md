# Renty RentalManagementApp

This project built a property management application called Renty, intended to be used to manage the rental activities of a property. A building manager can access information about each suite and tenant, as well as maintenance-related information including suite inspections, repair orders, and contractors. The building manager can add, remove, or update this information through the interface. In addition, there is a section for rent administration which displays the rent payment status of each suite (paid/overdue/etc.) and another section for reports which details some statistics regarding the tenants, contractors, and maintenance, which can help to inform decisions like which contractor to hire for a repair or when to inspect a suite next. 

# Subtitle: Additional information


# Overview: Instructions
Follow the link above to the GitHub repository  

1) Clone the repository to your htdocs directory within XAMPP on your computer (https://docs.github.com/en/free-pro-team@latest/github/creating-cloning-and-archiving-repositories/cloning-a-repository) 

2) In the scripts folder, edit the file program_constants.php to update the database username and password to your own (for phpMyAdmin): 

(line 3) define("DBPWD","root"); 

(line 5) define("DBUser","root"); 

3) In the sql folder, find the file propertymanagement.sql (will be used next) 

4) In phpMyAdmin, create a new database called propertymanagement 

5) Navigate to the Import tab and import the propertymanagement.sql file to create all of the database tables and accompanying data 

6) If error occurs with #1558 – Column count of mysql.proc is wrong....Please use mysql_upgrade to fix this error. 

7) Go to xampp directory: cd /opt/lampp/bin 

    mysql_upgrade –u root –p 

8) Restart xampp and you are good to go! 

9) In your internet browser, go to localhost/RentalManagementApp to see the application! 

# Table of contents: Internal navigation


# Features: Each section of the document
Projection query – view contractors  

nav: Contractors > View Contractors 

stored procedure: getContractors() 

Selection query – view suite 

nav: Suites > View [suite] 

stored procedure: getSuiteBySuiteNum() 

Join query – view tenants 

nav: Tenants > View Tenants 

stored procedure: getTenants() 

Division query – suites all contractors have worked on 

nav: Reports > Contractor Stats > Suites All Worked On 

stored procedure: getSuitesAllWorkedOn() 

Aggregation query – contractor work stats, last inspection of suites 

nav: Reports > Contractor Stats > Work Stats 

  Reports > Inspection Stats > Last Inspection of Suites 

stored procedure: getContractorWorkStats() 

getSuitesRequiringInspection() 

Nested aggregation with group-by – average quote per quite 

nav: Reports > Repair Stats > Average Quote Per Suite 

stored procedure: getSuiteAvgSpent() 

Deletion operation – delete tenant 

nav:  Tenants > View Tenants > Delete [tenant] 

stored procedure: removeTenant() 

Update operation – edit tenant 

nav:  Tenants > View Tenants > Edit [tenant] 

stored procedure: updateTenantInfo() 

Extra features 

Used bootstrap to implement the interface 

Triggers – activity logging (upon adding, deleting, or updating a tenant the action will be logged and stored in the activity log, located at Reports > Activity Log) 
