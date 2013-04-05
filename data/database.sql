CREATE TABLE `emailaddresses` (
  `emailaddress_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` BIGINT(20) UNSIGNED NOT NULL,
  `emailaddress` VARCHAR(255) NOT NULL,
  `passwordcrypt` CHAR(60) BINARY NOT NULL,
  PRIMARY KEY (`emailaddress_id`),
  UNIQUE KEY `emailaddress` (`emailaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;