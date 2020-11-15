DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `assignRepairOrder`(IN `rid` INT, IN `cn` VARCHAR(100), IN `cp` VARCHAR(50))
    NO SQL
    COMMENT 'assign contractor to repair order'
UPDATE assignedto
SET contractorName = cn,
	contractorPhone = cp,
    assignedOn = CURRENT_DATE()
WHERE repairID = rid$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeContractor`(IN `n` VARCHAR(100), IN `phone` VARCHAR(50))
    NO SQL
    COMMENT 'remove contractor'
DELETE FROM contractor WHERE name = n AND phoneNumber = phone$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAverageQuotePerSuite`()
    NO SQL
    COMMENT 'average cost of repair orders per suite'
SELECT suiteNumber, AVG(quote.quoteAmount) as averageQuote
FROM generatesrepairorder
INNER JOIN quote ON generatesrepairorder.type = quote.type AND generatesrepairorder.priority = quote.priority
GROUP BY suiteNumber$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeTenant`(IN `tid` INT)
    NO SQL
    COMMENT 'remove tenant'
DELETE FROM tenant WHERE ID = tid$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `removeSuite`(IN `sid` INT)
    NO SQL
    COMMENT 'remove suite'
DELETE FROM suite WHERE suiteNumber = sid$$
DELIMITER ;
