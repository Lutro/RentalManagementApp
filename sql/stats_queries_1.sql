DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLockersInUse`()
    NO SQL
    COMMENT 'number of storage lockers in use and total'
SELECT COUNT(*) as inUse, 25 as total
FROM occupant
WHERE storageLockerNumber IS NOT NULL$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAvgRepairLength`()
    NO SQL
    COMMENT 'average length of repair (days)'
SELECT ROUND(AVG(DATEDIFF(endDate,startDate)),1) as avgDays
FROM generatesrepairorder$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAvgParkingRent`()
    NO SQL
    COMMENT 'average rent of a parking stall (in dollars)'
SELECT AVG(rentAmount) as avgRent
FROM hasparkingstall$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAvgSuiteRent`()
    NO SQL
    COMMENT 'average rent price of suite- 1bed/2beds/overall'
SELECT bedrooms, AVG(rentAmount) as avgRent
FROM suite
GROUP BY bedrooms WITH ROLLUP$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAvgQuoteAmount`()
    NO SQL
    COMMENT 'average quote amount for repair orders (overall + per type)'
SELECT generatesrepairorder.type, ROUND(AVG(quote.quoteAmount),2) as AverageQuote
FROM generatesrepairorder
INNER JOIN quote ON generatesrepairorder.type = quote.type AND generatesrepairorder.priority = quote.priority
GROUP BY type WITH ROLLUP$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAvgTenancy`()
    NO SQL
    COMMENT 'computed from leaseStart to current date'
SELECT ROUND(AVG(livingHere)/365,0) as years,  ROUND(AVG(livingHere)%365,0) as days
FROM
(
-- get occupants that are tenants that have current lease
SELECT occupant.ID, leaseStart, DATEDIFF(CURRENT_DATE(),leaseStart) as livingHere
FROM occupant
INNER JOIN tenant
ON occupant.ID = tenant.ID
WHERE leaseEnd > CURRENT_DATE()

-- add to occupants that are not tenants but are living with someone who does have a current lease
UNION

(SELECT nontenantID as ID, leaseStart, DATEDIFF(CURRENT_DATE(),leaseStart) as livingHere
FROM
(SELECT nontenantID, tenantB as tenantID
FROM
-- occupants that are not a tenant (on the lease)
(SELECT occupant.ID as nontenantID
FROM `tenant` 
RIGHT JOIN occupant ON tenant.ID = occupant.ID
WHERE tenant.ID is null) AS nontenants 
INNER JOIN cohabitateswith
ON nontenants.nontenantID = cohabitateswith.tenantA

UNION

SELECT nontenantID, tenantA as tenantID
FROM
-- occupants that are not a tenant (on the lease)
(SELECT occupant.ID as nontenantID
FROM `tenant` 
RIGHT JOIN occupant ON tenant.ID = occupant.ID
WHERE tenant.ID is null) AS nontenants 
INNER JOIN cohabitateswith
ON nontenants.nontenantID = cohabitateswith.tenantB) as pairs

INNER JOIN tenant
ON pairs.tenantID = tenant.ID
WHERE leaseEnd > CURRENT_DATE() 
) 
) 
as final$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getParkingAvailability`()
    NO SQL
    COMMENT 'get total number of parking stalls free/taken'
SELECT COUNT(*) AS total,
	SUM(case when tenantID IS NOT NULL then 1 else 0 end) AS inUse,
    SUM(case when tenantID IS NULL then 1 else 0 end) AS available
FROM hasparkingstall$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getResidentAges`()
    NO SQL
    COMMENT 'ages of current residents of the building'
SELECT ID, FLOOR(DATEDIFF(CURRENT_DATE,birthdate)/365) as age
FROM

-- get occupants that are tenants that have current lease
(SELECT occupant.ID, birthdate
FROM occupant
INNER JOIN tenant
ON occupant.ID = tenant.ID
WHERE leaseEnd > CURRENT_DATE()

-- add to occupants that are not tenants but are living with someone who does have a current lease
UNION

(SELECT nontenantID as ID, birthdate
FROM
(SELECT nontenantID, tenantB as tenantID, birthdate
FROM
-- occupants that are not a tenant (on the lease)
(SELECT occupant.ID as nontenantID, birthdate
FROM `tenant` 
RIGHT JOIN occupant ON tenant.ID = occupant.ID
WHERE tenant.ID is null) AS nontenants 
INNER JOIN cohabitateswith
ON nontenants.nontenantID = cohabitateswith.tenantA

UNION

SELECT nontenantID, tenantA as tenantID, birthdate
FROM
-- occupants that are not a tenant (on the lease)
(SELECT occupant.ID as nontenantID, birthdate
FROM `tenant` 
RIGHT JOIN occupant ON tenant.ID = occupant.ID
WHERE tenant.ID is null) AS nontenants 
INNER JOIN cohabitateswith
ON nontenants.nontenantID = cohabitateswith.tenantB) as pairs

INNER JOIN tenant
ON pairs.tenantID = tenant.ID
WHERE leaseEnd > CURRENT_DATE() 
) 
) as f$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuiteAvailability`()
    NO SQL
    COMMENT 'number of suites occupied and available'
SELECT 
	 COUNT(*) as total,
	 SUM(case when num IS NOT NULL then 1 else 0 end) AS occupied,
     SUM(case when num IS NULL then 1 else 0 end) AS available
FROM (

    SELECT suite.suiteNumber, COUNT(suite.suiteNumber) as num
	FROM suite
	LEFT JOIN livesin ON suite.suiteNumber = livesin.suiteNumber 
    WHERE tenantID IS NOT NULL
    GROUP BY suite.suiteNumber
    
    UNION
    
    SELECT suite.suiteNumber, tenantID as num
    FROM suite
    LEFT JOIN livesin ON suite.suiteNumber = livesin.suiteNumber
    WHERE tenantID IS NULL
) as f$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuiteAvgSpent`()
    NO SQL
    COMMENT 'how much money was spent on a suite (in repair costs)'
SELECT suite.suiteNumber, COALESCE(avgSpent,0) as avgSpent
FROM
(SELECT suiteNumber, AVG(quoteAmount) as avgSpent
FROM generatesrepairorder
INNER JOIN quote
ON generatesrepairorder.type = quote.type AND generatesrepairorder.priority = quote.priority
GROUP BY suiteNumber) as x 
RIGHT JOIN suite ON x.suiteNumber = suite.suiteNumber
ORDER BY suiteNumber ASC$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuitesByBeds`()
    NO SQL
    COMMENT 'how many suites grouped by number of bedrooms'
SELECT bedrooms, COUNT(bedrooms) as suites
FROM suite
GROUP BY bedrooms$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalCurrentOccupants`()
    NO SQL
    COMMENT 'number of occupants currently living in building'
SELECT COUNT(*) as currentOccupants
FROM
(
-- get occupants that are tenants that have current lease
SELECT occupant.ID, leaseEnd
FROM occupant
INNER JOIN tenant
ON occupant.ID = tenant.ID
WHERE leaseEnd > CURRENT_DATE()

-- add to occupants that are not tenants but are living with someone who does have a current lease
UNION

(SELECT nontenantID as ID, leaseEnd
FROM
(SELECT nontenantID, tenantB as tenantID
FROM
-- occupants that are not a tenant (on the lease)
(SELECT occupant.ID as nontenantID
FROM `tenant` 
RIGHT JOIN occupant ON tenant.ID = occupant.ID
WHERE tenant.ID is null) AS nontenants 
INNER JOIN cohabitateswith
ON nontenants.nontenantID = cohabitateswith.tenantA

UNION

SELECT nontenantID, tenantA as tenantID
FROM
-- occupants that are not a tenant (on the lease)
(SELECT occupant.ID as nontenantID
FROM `tenant` 
RIGHT JOIN occupant ON tenant.ID = occupant.ID
WHERE tenant.ID is null) AS nontenants 
INNER JOIN cohabitateswith
ON nontenants.nontenantID = cohabitateswith.tenantB) as pairs

INNER JOIN tenant
ON pairs.tenantID = tenant.ID
WHERE leaseEnd > CURRENT_DATE() 
) 
) as final$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalQuoteAmount`()
    NO SQL
    COMMENT 'total quote amount for repair orders (overall + per type)'
SELECT generatesrepairorder.type, ROUND(SUM(quote.quoteAmount),2) as total
FROM generatesrepairorder
INNER JOIN quote ON generatesrepairorder.type = quote.type AND generatesrepairorder.priority = quote.priority
GROUP BY type WITH ROLLUP$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getContractorWorkStats`()
    NO SQL
    COMMENT 'contractor repair order stats'
SELECT contractor.name, contractor.phoneNumber, workType, SUM(case when status='complete' then 1 else 0 end) AS completed, SUM(case when status='in progress' then 1 else 0 end) AS inProgress, ROUND(AVG(DATEDIFF(endDate,startDate)),1) as avgDays, ROUND(AVG(quoteAmount),2) as avgQuote
FROM generatesrepairorder
INNER JOIN quote ON generatesrepairorder.type = quote.type AND generatesrepairorder.priority = quote.priority
INNER JOIN assignedto ON generatesrepairorder.repairID = assignedto.repairID
RIGHT JOIN contractor ON contractor.name = assignedto.contractorName AND contractor.phoneNumber = assignedto.contractorPhone
GROUP BY contractor.name, contractor.phoneNumber$$
DELIMITER ;
