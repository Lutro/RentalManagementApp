DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `assignParkingStall`(IN `tid` INT, IN `snum` INT)
    NO SQL
    COMMENT 'assign parking stall to tenant'
UPDATE hasparkingstall
SET tenantID = tid
WHERE stallNumber = snum$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateOccupantInfo`(IN `oid` INT, IN `n` VARCHAR(50), IN `p` VARCHAR(20), IN `e` VARCHAR(50), IN `bd` DATE, IN `nob` INT, IN `sln` INT)
    NO SQL
    COMMENT 'update occupant information'
UPDATE occupant
SET name = n,
	phone = p,
    email = e,
    birthdate = bd,
    numberOfBikes = nob,
    storageLockerNumber = sln
WHERE ID = oid$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateSuiteInfo`(IN `sn` INT, IN `beds` INT, IN `baths` INT, IN `rent` FLOAT, IN `mkey` BOOLEAN)
    NO SQL
    COMMENT 'update suite information'
UPDATE suite
SET bedrooms = beds,
	bathrooms = baths,
    rentAmount = rent,
    hasMasterKey = mkey
WHERE suiteNumber = sn$$
DELIMITER ;


