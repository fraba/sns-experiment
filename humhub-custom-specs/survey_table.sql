-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.14 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table polithumhub._user_surveys
DROP TABLE IF EXISTS `_user_surveys`;
CREATE TABLE IF NOT EXISTS `_user_surveys` (
  `user_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pol_op` int(11) NOT NULL,
  `pol_op_abo` int(11) NOT NULL,
  `pol_op_imm` int(11) NOT NULL,
  `pol_op_gay` int(11) NOT NULL,
  `pol_op_eco` int(11) NOT NULL,
  `int_abo_sur` int(11) NOT NULL,
  `int_gay_sur` int(11) NOT NULL,
  `int_eco_sur` int(11) NOT NULL,
  `int_imm_sur` int(11) NOT NULL,
  `int_abo_obs` int(11) NOT NULL,
  `int_gay_obs` int(11) NOT NULL,
  `int_eco_obs` int(11) NOT NULL,
  `int_imm_obs` int(11) NOT NULL,
  PRIMARY KEY (`user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table polithumhub._user_surveys: 3 rows
DELETE FROM `_user_surveys`;
/*!40000 ALTER TABLE `_user_surveys` DISABLE KEYS */;
INSERT INTO `_user_surveys` (`user_email`, `pol_op`, `pol_op_abo`, `pol_op_imm`, `pol_op_gay`, `pol_op_eco`, `int_abo_sur`, `int_gay_sur`, `int_eco_sur`, `int_imm_sur`, `int_abo_obs`, `int_gay_obs`, `int_eco_obs`, `int_imm_obs`) VALUES
	('somebody@somemail.com', 1, 1, 2, 2, 3, 3, 5, 5, 4, 1, 2, 3, 4),
	('another@somemail.com', 1, 1, 2, 2, 3, 3, 5, 5, 4, 4, 3, 2, 1),
	('andathird@somemail.com', 1, 1, 2, 2, 3, 3, 5, 5, 4, 1, 2, 3, 4);
/*!40000 ALTER TABLE `_user_surveys` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
