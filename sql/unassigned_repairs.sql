DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUnassignedRepairOrders`()
    NO SQL
    COMMENT 'repair orders that need to be assigned'
SELECT generatesrepairorder.repairID, suiteNumber, priority, type, inspectionDate
FROM `generatesrepairorder`
INNER JOIN assignedto ON generatesrepairorder.repairID = assignedto.repairID
WHERE contractorName IS NULL AND contractorPhone IS NULL$$
DELIMITER ;
