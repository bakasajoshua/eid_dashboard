DROP PROCEDURE IF EXISTS `proc_get_vl_poc_county_trends`;
DELIMITER //
CREATE PROCEDURE `proc_get_vl_poc_county_trends`
(IN filter_year INT(11), IN from_month INT(11), IN to_year INT(11), IN to_month INT(11))
BEGIN
  SET @QUERY =    "SELECT 
                    `vf`.`countyname`, 
                    SUM(`edta`) AS `edta`,
                    SUM(`dbs`) AS `dbs`,
                    SUM(`plasma`) AS `plasma`,
                    SUM(`alledta`) AS `alledta`,
                    SUM(`alldbs`) AS `alldbs`,
                    SUM(`allplasma`) AS `allplasma`,
                    SUM(`vcs`.`undetected`) AS `undetected`,
                    SUM(`vcs`.`less1000`) AS `less1000`,
                    (SUM(`vcs`.`undetected`)+SUM(`vcs`.`less1000`)) AS `suppressed`,
                    (SUM(`vcs`.`less5000`)+SUM(`vcs`.`above5000`)) AS `nonsuppressed`,
                    (SUM(`vcs`.`undetected`)+SUM(`vcs`.`less1000`)+SUM(`vcs`.`less5000`)+SUM(`vcs`.`above5000`)) AS `total`
                  FROM `vl_site_summary_poc` `ssp`
                  LEFT JOIN `view_facilitys` `vf` ON `ssp`.`facility` = `vf`.`ID` 
                  WHERE 1 ";
   


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vf`.`countyname` ORDER BY `ssp`.`alltests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END //
DELIMITER ;
