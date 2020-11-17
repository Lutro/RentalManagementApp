-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2020 at 12:47 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `propertymanagement`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `assignParkingStall` (IN `tid` INT, IN `snum` INT)  NO SQL
    COMMENT 'assign parking stall to tenant'
UPDATE hasparkingstall
SET tenantID = tid
WHERE stallNumber = snum$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `assignRepairOrder` (IN `rid` INT, IN `cn` VARCHAR(100), IN `cp` VARCHAR(50))  NO SQL
    COMMENT 'assign contractor to repair order'
UPDATE assignedto
SET contractorName = cn,
	contractorPhone = cp,
    assignedOn = CURRENT_DATE()
WHERE repairID = rid$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getAverageQuotePerSuite` ()  NO SQL
    COMMENT 'average cost of repair orders per suite'
SELECT suiteNumber, AVG(quote.quoteAmount) as averageQuote
FROM generatesrepairorder
INNER JOIN quote ON generatesrepairorder.type = quote.type AND generatesrepairorder.priority = quote.priority
GROUP BY suiteNumber$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getContractors` ()  NO SQL
SELECT *
FROM contractor$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getIncompleteRepairOrders` ()  NO SQL
    COMMENT 'repair orders with incomplete status'
SELECT generatesrepairorder.repairID, suiteNumber, priority, type, startDate, endDate, inspectionDate
FROM `generatesrepairorder`
INNER JOIN assignedto ON generatesrepairorder.repairID = assignedto.repairID
WHERE status LIKE 'in progress'$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getInspectionStats` ()  NO SQL
    COMMENT 'number of inspections per reason'
SELECT reason, COUNT(reason) as inspections
FROM hasinspection
GROUP BY reason$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getManager` ()  NO SQL
SELECT occupant.name, occupant.email, occupant.phone, manager.managerSince
FROM occupant
INNER JOIN manager ON occupant.ID = manager.ID
WHERE manager.managerUntil IS NULL$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getParkingStalls` ()  NO SQL
    COMMENT 'get parking stalls with corresponding tenant info'
SELECT stallNumber, rentAmount, isAccessible, occupant.name, occupant.phone, occupant.email, livesin.suiteNumber
FROM `hasparkingstall` 
INNER JOIN occupant ON occupant.ID = hasparkingstall.tenantID
INNER JOIN livesin ON livesin.tenantID = occupant.ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getRepairOrders` ()  NO SQL
SELECT repairID,suiteNumber, priority, type, startDate, endDate, inspectionDate
FROM generatesrepairorder$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getRepairOrdersPerSuite` ()  NO SQL
    COMMENT 'number of repair orders per suite'
SELECT suite.suiteNumber, COUNT(repairID) as repairOrders
FROM suite
INNER JOIN generatesrepairorder ON generatesrepairorder.suiteNumber = suite.suiteNumber
GROUP BY suiteNumber$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getRepairOrderStats` ()  NO SQL
    COMMENT 'number of repair orders per work type'
SELECT type, COUNT(repairID) as repairOrders
FROM generatesrepairorder
GROUP BY type$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuiteOccupancy` ()  NO SQL
    COMMENT 'suite info (number of occupants, pets, etc)'
SELECT livesin.suiteNumber, suite.bedrooms AS Bedrooms, suite.bathrooms as Bathrooms, COUNT(cohabitateswith.tenantA) AS Occupants, tenant.numberOfPets AS Pets, tenant.leaseStart AS LeaseStart, tenant.leaseEnd AS LeaseEnd
FROM occupant
INNER JOIN livesin ON livesin.tenantID = occupant.ID
INNER JOIN cohabitateswith ON cohabitateswith.tenantA = occupant.ID
INNER JOIN tenant ON tenant.ID = occupant.ID
INNER JOIN suite ON suite.suiteNumber = livesin.suiteNumber
GROUP BY livesin.suiteNumber$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuites` ()  NO SQL
SELECT suite.suiteNumber, suite.bedrooms, suite.bathrooms, suite.rentAmount, suitesize.size, suite.hasMasterKey
FROM suite
INNER JOIN suitesize ON suite.bedrooms = suitesize.bedrooms 
AND suite.bathrooms = suitesize.bathrooms$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuitesAllWorkedOn` ()  NO SQL
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuitesRent` ()  NO SQL
SELECT suite.suiteNumber, occupant.name, suite.rentAmount
FROM suite
INNER JOIN livesin ON suite.suiteNumber = livesin.suiteNumber
INNER JOIN occupant ON livesin.tenantID = occupant.ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getSuitesRequiringInspection` ()  NO SQL
    COMMENT 'suites with inspections more than 6 months ago'
SELECT hasinspection.suiteNumber, dateOfInspection, reason, description
FROM hasinspection
INNER JOIN suite ON suite.suiteNumber = hasinspection.suiteNumber
WHERE DATE(hasinspection.dateOfInspection) < DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getTenants` ()  NO SQL
SELECT occupant.name, occupant.phone, occupant.email, occupant.numberOfBikes, occupant.storageLockerNumber, tenant.numberOfPets, tenant.leaseStart, tenant.leaseEnd
FROM occupant
INNER JOIN tenant ON occupant.ID = tenant.ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getUnassignedRepairOrders` ()  NO SQL
    COMMENT 'repair orders that need to be assigned'
SELECT generatesrepairorder.repairID, suiteNumber, priority, type, inspectionDate
FROM `generatesrepairorder`
INNER JOIN assignedto ON generatesrepairorder.repairID = assignedto.repairID
WHERE contractorName IS NULL AND contractorPhone IS NULL$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `removeContractor` (IN `n` VARCHAR(100), IN `phone` VARCHAR(50))  NO SQL
    COMMENT 'remove contractor'
DELETE FROM contractor WHERE name = n AND phoneNumber = phone$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `removeSuite` (IN `sid` INT)  NO SQL
    COMMENT 'remove suite'
DELETE FROM suite WHERE suiteNumber = sid$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTenant` (IN `tid` INT)  NO SQL
    COMMENT 'remove tenant'
DELETE FROM tenant WHERE ID = tid$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateOccupantInfo` (IN `oid` INT, IN `n` VARCHAR(50), IN `p` VARCHAR(20), IN `e` VARCHAR(50), IN `bd` DATE, IN `nob` INT, IN `sln` INT)  NO SQL
    COMMENT 'update occupant information'
UPDATE occupant
SET name = n,
	phone = p,
    email = e,
    birthdate = bd,
    numberOfBikes = nob,
    storageLockerNumber = sln
WHERE ID = oid$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateSuiteInfo` (IN `sn` INT, IN `beds` INT, IN `baths` INT, IN `rent` FLOAT, IN `mkey` BOOLEAN)  NO SQL
    COMMENT 'update suite information'
UPDATE suite
SET bedrooms = beds,
	bathrooms = baths,
    rentAmount = rent,
    hasMasterKey = mkey
WHERE suiteNumber = sn$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `assignedto`
--

CREATE TABLE `assignedto` (
  `repairID` int(11) NOT NULL COMMENT 'id of repair order',
  `contractorName` varchar(100) DEFAULT NULL COMMENT 'name of contractor',
  `contractorPhone` varchar(50) DEFAULT NULL COMMENT 'phone number of contractor',
  `assignedOn` date DEFAULT NULL COMMENT 'date of assignment',
  `status` varchar(100) NOT NULL COMMENT 'status of assignment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assignedto`
--

INSERT INTO `assignedto` (`repairID`, `contractorName`, `contractorPhone`, `assignedOn`, `status`) VALUES
(55238, 'Tim Bruce', '6043308521', '2017-03-20', 'complete'),
(55239, 'Nicolas Reese', '6043554383', '2010-08-03', 'complete'),
(55240, 'Caleb Mcneill', '6046496425', '2002-12-23', 'complete'),
(55241, NULL, NULL, '2020-01-27', 'in progress'),
(55242, 'Caleb Mcneill', '6046496425', '2008-02-24', 'complete'),
(55243, 'Tim Bruce', '6043308521', '2020-03-05', 'complete'),
(55244, 'Tim Bruce', '6043308521', '2020-03-25', 'complete'),
(55245, 'Nicolas Reese', '6043554383', '2015-08-03', 'complete'),
(55246, 'Caleb Mcneill', '6046496425', '2003-01-09', 'complete'),
(55247, 'James Kenedy', '6041525588', '2012-02-03', 'complete'),
(55248, 'Johnny Dean', '6046177097', '2016-03-19', 'complete');

-- --------------------------------------------------------

--
-- Table structure for table `cohabitateswith`
--

CREATE TABLE `cohabitateswith` (
  `tenantA` int(11) NOT NULL COMMENT 'Tenant ID',
  `tenantB` int(11) DEFAULT NULL COMMENT 'tenant B ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cohabitateswith`
--

INSERT INTO `cohabitateswith` (`tenantA`, `tenantB`) VALUES
(1, 3),
(2, 8),
(6, 10),
(7, 4),
(9, 5),
(11, 14),
(13, 16),
(15, 22),
(18, 21);

-- --------------------------------------------------------

--
-- Table structure for table `contractor`
--

CREATE TABLE `contractor` (
  `name` varchar(100) NOT NULL COMMENT 'contractor''s name',
  `phoneNumber` varchar(50) NOT NULL COMMENT 'contractor''s phone number',
  `company` varchar(100) NOT NULL COMMENT 'name of company',
  `address` varchar(100) NOT NULL COMMENT 'address',
  `email` varchar(100) NOT NULL COMMENT 'email',
  `workType` varchar(100) NOT NULL COMMENT 'type of work'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contractor`
--

INSERT INTO `contractor` (`name`, `phoneNumber`, `company`, `address`, `email`, `workType`) VALUES
('Caleb Mcneill', '6046496425', 'Burnaby Pest Control', '701 Gilley Street, Burnaby BC, V5Z 9K1', 'calebm@bpestcontrol.com', 'extermination'),
('James Kenedy', '6041525588', 'The Plumbing Kings', '626 Victoria Drive, Vancouver BC, V5T 3K4', 'jamesk@plumbingkings.com', 'plumbing'),
('Johnny Dean', '6046177097', 'Home Construct & Renovations', '4155 Maywood Avenue, Vancouver BC, V5T 3K4', 'johndean@homerenov.com', 'renovation'),
('Nicolas Reese', '6043554383', 'Window Repair', '156 Broadway Street, Vancouver BC, P7T 3V8', 'nicReese@windowrepair.com', 'window replacement'),
('Tim Bruce', '6043308521', 'PuroShine Restoration', '54 Avenue, Surrey BC, V5P 7L9', 'tbruce@puroshine.com', 'mold cleanup');

-- --------------------------------------------------------

--
-- Table structure for table `deposit`
--

CREATE TABLE `deposit` (
  `numberOfPets` int(11) NOT NULL,
  `depositAmount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `deposit`
--

INSERT INTO `deposit` (`numberOfPets`, `depositAmount`) VALUES
(0, 700),
(1, 800),
(2, 875),
(3, 925),
(4, 950);

-- --------------------------------------------------------

--
-- Table structure for table `generatesrepairorder`
--

CREATE TABLE `generatesrepairorder` (
  `repairID` int(11) NOT NULL COMMENT 'repair order id',
  `type` varchar(50) NOT NULL COMMENT 'type of work',
  `startDate` date DEFAULT NULL COMMENT 'start date',
  `endDate` date DEFAULT NULL COMMENT 'end date',
  `priority` int(11) NOT NULL COMMENT 'Priority of job',
  `suiteNumber` int(11) NOT NULL,
  `inspectionDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `generatesrepairorder`
--

INSERT INTO `generatesrepairorder` (`repairID`, `type`, `startDate`, `endDate`, `priority`, `suiteNumber`, `inspectionDate`) VALUES
(55238, 'mold cleanup', '2017-03-30', '2017-04-03', 4, 103, '2017-03-11 00:00:00'),
(55239, 'window replacement', '2010-08-15', '2010-08-20', 3, 101, '2010-07-31 00:00:00'),
(55240, 'extermination', '2002-12-30', '2002-12-31', 1, 105, '2002-12-21 00:00:00'),
(55241, 'renovation', NULL, NULL, 5, 102, '2020-01-04 00:00:00'),
(55242, 'extermination', '2008-02-25', '2008-02-26', 2, 104, '2008-02-23 00:00:00'),
(55243, 'mold cleanup', '2020-03-30', '0000-00-00', 4, 402, '2020-03-11 00:00:00'),
(55244, 'mold cleanup', '2020-04-15', '0000-00-00', 4, 305, '2020-03-21 00:00:00'),
(55245, 'window replacement', '2015-08-09', '0000-00-00', 1, 305, '2015-07-31 00:00:00'),
(55246, 'extermination', '2003-01-15', '0000-00-00', 1, 305, '2002-12-21 00:00:00'),
(55247, 'plumbing', '2012-02-05', '2012-02-06', 1, 305, '2012-02-02 00:00:00'),
(55248, 'renovation', '2016-06-10', '2016-06-29', 4, 305, '2016-02-25 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `hasinspection`
--

CREATE TABLE `hasinspection` (
  `suiteNumber` int(11) NOT NULL COMMENT 'suite number',
  `dateOfInspection` datetime NOT NULL COMMENT 'date and time of inspection',
  `reason` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hasinspection`
--

INSERT INTO `hasinspection` (`suiteNumber`, `dateOfInspection`, `reason`, `description`) VALUES
(101, '2010-07-31 00:00:00', 'tenant request', 'broken window'),
(102, '2020-01-04 00:00:00', 'renovation', 'kitchen appliances outdated/old'),
(103, '2017-03-11 00:00:00', 'routine inspection', 'mold on bathroom ceiling'),
(105, '2002-12-21 00:00:00', 'complaint', 'insect infestation'),
(105, '2014-06-27 00:00:00', 'routine inspection', 'no issues'),
(204, '2020-06-27 09:30:00', 'routine inspection', 'no issues'),
(305, '2002-12-21 15:45:00', 'complaint', 'insect infestation'),
(305, '2012-02-02 16:30:00', 'complaint', 'sink blocked'),
(305, '2015-07-31 13:15:00', 'tenant request', 'broken window'),
(305, '2016-02-05 11:30:00', 'renovation', 'floor tiles old'),
(305, '2020-03-21 11:45:00', 'routine inspection', 'mold on bathroom ceiling'),
(402, '2020-03-11 10:30:00', 'routine inspection', 'mold on bathroom ceiling'),
(403, '2020-04-11 10:30:00', 'routine inspection', 'mold on bathroom ceiling');

-- --------------------------------------------------------

--
-- Table structure for table `hasparkingstall`
--

CREATE TABLE `hasparkingstall` (
  `stallNumber` int(11) NOT NULL COMMENT 'parking stall number',
  `rentAmount` float NOT NULL COMMENT 'Price to rent',
  `isAccessible` tinyint(1) NOT NULL COMMENT 'Whether stall is accessible',
  `tenantID` int(11) DEFAULT NULL COMMENT 'tenant ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hasparkingstall`
--

INSERT INTO `hasparkingstall` (`stallNumber`, `rentAmount`, `isAccessible`, `tenantID`) VALUES
(1, 150, 0, 4),
(2, 125, 0, NULL),
(3, 125, 1, 2),
(4, 150, 0, 1),
(5, 100, 1, NULL),
(6, 125, 1, 11),
(7, 125, 1, NULL),
(8, 100, 0, 19);

-- --------------------------------------------------------

--
-- Table structure for table `livesin`
--

CREATE TABLE `livesin` (
  `tenantID` int(11) NOT NULL,
  `suiteNumber` int(11) NOT NULL,
  `moveInDate` date NOT NULL COMMENT 'move in date',
  `moveOutDate` date DEFAULT NULL COMMENT 'move out date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `livesin`
--

INSERT INTO `livesin` (`tenantID`, `suiteNumber`, `moveInDate`, `moveOutDate`) VALUES
(1, 105, '1992-02-17', NULL),
(2, 103, '2015-01-25', NULL),
(3, 105, '1992-02-17', NULL),
(4, 101, '2008-06-01', NULL),
(7, 101, '2008-06-01', NULL),
(11, 304, '1997-01-31', NULL),
(12, 202, '1999-03-31', NULL),
(13, 405, '1992-04-30', NULL),
(15, 201, '2020-03-31', NULL),
(17, 402, '2015-02-01', NULL),
(19, 204, '2000-06-30', NULL),
(21, 305, '2000-04-30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `ID` int(11) NOT NULL,
  `managerSince` date NOT NULL,
  `managerUntil` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`ID`, `managerSince`, `managerUntil`) VALUES
(1, '1993-12-01', '1997-10-25'),
(4, '1997-10-25', '1999-05-25'),
(5, '1999-05-25', '2005-12-01'),
(8, '2005-12-01', '2016-08-31'),
(10, '2016-08-31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `occupant`
--

CREATE TABLE `occupant` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `numberOfBikes` int(11) DEFAULT NULL,
  `storageLockerNumber` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `occupant`
--

INSERT INTO `occupant` (`ID`, `name`, `phone`, `email`, `birthdate`, `numberOfBikes`, `storageLockerNumber`) VALUES
(1, 'John Smith', '6045079833', 'jsmith325@gmail.com', '1972-03-25', 1, 1),
(2, 'Linda Cho', '7781235893', 'lindac12@hotmail.com', '1988-07-12', 0, 2),
(3, 'Shelly Wills', '6045826315', 'shellyw2@gmail.com', '1982-02-26', 0, 3),
(4, 'Gordan Guo', '2365584771', 'gordongg@gmail.com', '1979-05-12', 2, 4),
(5, 'Mike Powell', '2361259983', 'mikepw@hotmail.com', '1979-03-08', 0, 5),
(6, 'Murray Wilkins', '2364528184', 'murwilk55@gmail.com', '1985-01-15', 0, 6),
(7, 'Mia Macleod', '6042654815', 'miamac28@hotmail.com', '1990-12-28', 0, 7),
(8, 'Inez Oneil', '7781564125', 'inezone98@gmail.com', '1980-02-23', 1, 8),
(9, 'Anna Young', '2365545648', 'annay11@gmail.com', '1992-11-11', 1, 9),
(10, 'Lana Mccabe', '7782314258', 'lanamcc@hotmail.com', '1987-05-25', 2, 10),
(11, 'Francisco Woods', '6045098833', 'franswoods@gmail.com', '1972-03-25', 1, 11),
(12, 'Brogan Wiley', '7789651236', 'broganwiley@gmail.com', '1973-08-15', 2, 12),
(13, 'Aleena Graves', '2361523695', 'aleenagrave94@gmail.com', '1994-09-25', 0, 13),
(14, 'Saniyah Harrel', '2361852153', 'sanyharrel71@gmail.com', '1971-06-12', 3, 14),
(15, 'Lillian Marquez', '7787235698', 'lillianmq93@gmail.com', '1993-08-11', 2, 15),
(16, 'Roy Christian', '2361523123', 'roychris92@gmail.com', '1992-01-24', 1, 16),
(17, 'Eva Romero', '7781524125', 'evaromero@gmail.com', '1991-06-19', 1, 17),
(18, 'Meadow Harrison', '7782541525', 'meadowharrison@gmail.com', '1987-05-22', 1, 18),
(19, 'Abram Reeves', '2361524788', 'abramree85@gmail.com', '1985-04-09', 1, 19),
(20, 'Tara Simon', '2368521169', 'tarasimons@gmail.com', '1988-11-24', 1, 20),
(21, 'Carmelo Barnes', '2361254559', 'carmelob32@gmail.com', '1995-10-25', 0, 21),
(22, 'Malachi Marquez', '7787325698', 'malachimq22@gmail.com', '2003-08-24', 1, 22);

-- --------------------------------------------------------

--
-- Table structure for table `quote`
--

CREATE TABLE `quote` (
  `type` varchar(50) NOT NULL COMMENT 'type of work',
  `priority` int(11) NOT NULL COMMENT 'Priority of job',
  `quoteAmount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `quote`
--

INSERT INTO `quote` (`type`, `priority`, `quoteAmount`) VALUES
('extermination', 1, 500),
('extermination', 2, 450),
('extermination', 3, 400),
('mold cleanup', 4, 150),
('plumbing', 1, 250),
('window replacement', 1, 350),
('window replacement', 3, 200);

-- --------------------------------------------------------

--
-- Table structure for table `suite`
--

CREATE TABLE `suite` (
  `suiteNumber` int(11) NOT NULL COMMENT 'Suite number',
  `bedrooms` int(11) NOT NULL COMMENT 'Number of bedrooms',
  `bathrooms` int(11) NOT NULL COMMENT 'Number of bathrooms',
  `rentAmount` float NOT NULL COMMENT 'Price of rent',
  `hasMasterKey` tinyint(1) NOT NULL COMMENT 'Whether the suite has a master key'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `suite`
--

INSERT INTO `suite` (`suiteNumber`, `bedrooms`, `bathrooms`, `rentAmount`, `hasMasterKey`) VALUES
(101, 2, 2, 2200, 1),
(102, 1, 1, 1250, 1),
(103, 2, 1, 1775, 1),
(104, 1, 1, 1300, 1),
(105, 2, 2, 2500, 0),
(201, 2, 2, 2500, 1),
(202, 1, 1, 1850, 0),
(203, 2, 1, 2200, 1),
(204, 1, 1, 1500, 0),
(205, 2, 2, 2750, 1),
(301, 2, 2, 2700, 1),
(302, 1, 1, 2000, 0),
(303, 2, 1, 2500, 1),
(304, 1, 1, 1850, 0),
(305, 2, 2, 3000, 1),
(401, 2, 2, 3000, 1),
(402, 1, 1, 2250, 0),
(403, 2, 1, 2750, 1),
(404, 1, 1, 2100, 0),
(405, 2, 2, 3250, 1);

-- --------------------------------------------------------

--
-- Table structure for table `suitesize`
--

CREATE TABLE `suitesize` (
  `bedrooms` int(11) NOT NULL COMMENT 'Number of bedrooms',
  `bathrooms` int(11) NOT NULL COMMENT 'Number of bathrooms',
  `size` float NOT NULL COMMENT 'Square footage'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `suitesize`
--

INSERT INTO `suitesize` (`bedrooms`, `bathrooms`, `size`) VALUES
(1, 1, 500),
(1, 2, 575),
(2, 1, 600),
(2, 2, 750),
(3, 2, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

CREATE TABLE `tenant` (
  `ID` int(11) NOT NULL,
  `numberOfPets` int(11) DEFAULT NULL,
  `leaseStart` date NOT NULL,
  `leaseEnd` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tenant`
--

INSERT INTO `tenant` (`ID`, `numberOfPets`, `leaseStart`, `leaseEnd`) VALUES
(1, 1, '1992-02-17', '2022-02-17'),
(2, 0, '2015-01-25', '2025-01-25'),
(4, 0, '1995-01-25', '2025-01-25'),
(5, 1, '1998-06-01', '2028-06-01'),
(7, 1, '2008-06-01', '2028-06-01'),
(8, 0, '2005-06-01', '2028-06-01'),
(9, 0, '2010-04-29', '2030-04-29'),
(10, 1, '2000-12-31', '2025-12-31'),
(11, 1, '1997-01-31', '2022-01-31'),
(12, 1, '1999-03-31', '2024-03-31'),
(13, 2, '1992-04-30', '2022-04-30'),
(15, 1, '2020-03-31', '2045-03-31'),
(17, 1, '2015-02-01', '2040-02-01'),
(19, 1, '2000-06-30', '2030-06-30'),
(21, 0, '2000-04-30', '2030-04-30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignedto`
--
ALTER TABLE `assignedto`
  ADD PRIMARY KEY (`repairID`);

--
-- Indexes for table `cohabitateswith`
--
ALTER TABLE `cohabitateswith`
  ADD PRIMARY KEY (`tenantA`);

--
-- Indexes for table `contractor`
--
ALTER TABLE `contractor`
  ADD PRIMARY KEY (`name`,`phoneNumber`),
  ADD UNIQUE KEY `address` (`address`,`email`);

--
-- Indexes for table `deposit`
--
ALTER TABLE `deposit`
  ADD PRIMARY KEY (`numberOfPets`);

--
-- Indexes for table `generatesrepairorder`
--
ALTER TABLE `generatesrepairorder`
  ADD PRIMARY KEY (`repairID`);

--
-- Indexes for table `hasinspection`
--
ALTER TABLE `hasinspection`
  ADD PRIMARY KEY (`suiteNumber`,`dateOfInspection`),
  ADD UNIQUE KEY `dateOfInspection` (`dateOfInspection`);

--
-- Indexes for table `hasparkingstall`
--
ALTER TABLE `hasparkingstall`
  ADD PRIMARY KEY (`stallNumber`),
  ADD UNIQUE KEY `tenantID` (`tenantID`);

--
-- Indexes for table `livesin`
--
ALTER TABLE `livesin`
  ADD PRIMARY KEY (`tenantID`,`suiteNumber`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `occupant`
--
ALTER TABLE `occupant`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name_phone_email` (`phone`,`email`,`name`);

--
-- Indexes for table `quote`
--
ALTER TABLE `quote`
  ADD PRIMARY KEY (`type`,`priority`);

--
-- Indexes for table `suite`
--
ALTER TABLE `suite`
  ADD PRIMARY KEY (`suiteNumber`),
  ADD KEY `bathrooms_FK` (`bedrooms`,`bathrooms`);

--
-- Indexes for table `suitesize`
--
ALTER TABLE `suitesize`
  ADD PRIMARY KEY (`bedrooms`,`bathrooms`);

--
-- Indexes for table `tenant`
--
ALTER TABLE `tenant`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `occupant`
--
ALTER TABLE `occupant`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignedto`
--
ALTER TABLE `assignedto`
  ADD CONSTRAINT `assignedto_ibfk_1` FOREIGN KEY (`repairID`) REFERENCES `generatesrepairorder` (`repairID`);

--
-- Constraints for table `hasinspection`
--
ALTER TABLE `hasinspection`
  ADD CONSTRAINT `hasinspection_ibfk_1` FOREIGN KEY (`suiteNumber`) REFERENCES `suite` (`suiteNumber`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hasparkingstall`
--
ALTER TABLE `hasparkingstall`
  ADD CONSTRAINT `hasparkingstall_ibfk_1` FOREIGN KEY (`tenantID`) REFERENCES `occupant` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `manager`
--
ALTER TABLE `manager`
  ADD CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `occupant` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `suite`
--
ALTER TABLE `suite`
  ADD CONSTRAINT `bathrooms_FK` FOREIGN KEY (`bedrooms`,`bathrooms`) REFERENCES `suitesize` (`bedrooms`, `bathrooms`);

--
-- Constraints for table `tenant`
--
ALTER TABLE `tenant`
  ADD CONSTRAINT `tenant_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `occupant` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
