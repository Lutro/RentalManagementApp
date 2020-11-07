DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRepairOrderStats`()
    NO SQL
    COMMENT 'number of repair orders per work type'
SELECT type, COUNT(repairID) as repairOrders
FROM generatesrepairorder
GROUP BY type$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getNumSuitesByBeds`()
    NO SQL
    COMMENT '# of suites per #bedrooms (note: 0 bedrooms = studio)'
SELECT bedrooms, COUNT(suiteNumber) as numSuites
FROM suite
GROUP BY bedrooms$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getIncompleteRepairOrders`()
    NO SQL
    COMMENT 'repair orders with incomplete status'
SELECT generatesrepairorder.repairID, suiteNumber, priority, type, startDate, endDate, inspectionDate
FROM `generatesrepairorder`
INNER JOIN assignedto ON generatesrepairorder.repairID = assignedto.repairID
WHERE status LIKE 'in progress'$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getInspectionStats`()
    NO SQL
    COMMENT 'number of inspections per reason'
SELECT reason, COUNT(reason) as inspections
FROM hasinspection
GROUP BY reason$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuiteOccupancy`()
    NO SQL
    COMMENT 'suite info (number of occupants, pets, etc)'
SELECT livesin.suiteNumber, suite.bedrooms AS Bedrooms, suite.bathrooms as Bathrooms, COUNT(cohabitateswith.tenantA) AS Occupants, tenant.numberOfPets AS Pets, tenant.leaseStart AS LeaseStart, tenant.leaseEnd AS LeaseEnd
FROM occupant
INNER JOIN livesin ON livesin.tenantID = occupant.ID
INNER JOIN cohabitateswith ON cohabitateswith.tenantA = occupant.ID
INNER JOIN tenant ON tenant.ID = occupant.ID
INNER JOIN suite ON suite.suiteNumber = livesin.suiteNumber
GROUP BY livesin.suiteNumber$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getParkingStalls`()
    NO SQL
    COMMENT 'get parking stalls with corresponding tenant info'
SELECT stallNumber, rentAmount, isAccessible, occupant.name, occupant.phone, occupant.email, livesin.suiteNumber
FROM `hasparkingstall` 
INNER JOIN occupant ON occupant.ID = hasparkingstall.tenantID
INNER JOIN livesin ON livesin.tenantID = occupant.ID$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuitesAllWorkedOn`()
    NO SQL
    COMMENT 'suites that have had all contractors work on them (division)'
SELECT sx.suiteNumber, COUNT(generatesrepairorder.repairID) as TotalRepairs
FROM suite as sx
INNER JOIN generatesrepairorder ON generatesrepairorder.suiteNumber = sx.suiteNumber
INNER JOIN assignedto ON assignedto.repairID = generatesrepairorder.repairID
WHERE NOT EXISTS (
(SELECT p.name, p.phoneNumber 
 FROM contractor as p )
EXCEPT
(SELECT assignedto.contractorName, assignedto.contractorPhone 
 FROM assignedto 
 INNER JOIN generatesrepairorder ON generatesrepairorder.repairID = assignedto.repairID 
 INNER JOIN suite ON suite.suiteNumber = generatesrepairorder.suiteNumber 
 INNER JOIN contractor ON contractor.phoneNumber = assignedto.contractorPhone 
 WHERE suite.suiteNumber = sx.suiteNumber ) 
)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuitesRequiringInspection`()
    NO SQL
    COMMENT 'suites with inspections more than 6 months ago'
SELECT hasinspection.suiteNumber, dateOfInspection, reason, description
FROM hasinspection
INNER JOIN suite ON suite.suiteNumber = hasinspection.suiteNumber
WHERE DATE(hasinspection.dateOfInspection) < DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRepairOrdersPerSuite`()
    NO SQL
    COMMENT 'number of repair orders per suite'
SELECT suite.suiteNumber, COUNT(repairID) as repairOrders
FROM suite
INNER JOIN generatesrepairorder ON generatesrepairorder.suiteNumber = suite.suiteNumber
GROUP BY suiteNumber$$
DELIMITER ;

