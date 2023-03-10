-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.0.38-MariaDB-1~xenial - mariadb.org binary distribution
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             12.4.0.6659
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for trivia
CREATE DATABASE IF NOT EXISTS `trivia` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `trivia`;

-- Dumping structure for table trivia.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `correct_answer` int(3) unsigned DEFAULT NULL,
  `choices` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Dumping data for table trivia.questions: ~5 rows (approximately)
INSERT INTO `questions` (`id`, `question`, `correct_answer`, `choices`) VALUES
	(1, 'Do you like jazz?', 1, '["correct","choice 2","choice 3","choice 4"]'),
	(2, 'Where does the general keep his armies?', 2, '["base","sleevies","military","home"]'),
	(3, 'Today is?', 4, '["never","now","tomorrow","the day"]'),
	(4, 'test 3', 3, '["1","2","3","4"]'),
	(5, 'Question 5', 4, '["incorrect","incorrect","incorrect","correct"]');

-- Dumping structure for table trivia.saved
CREATE TABLE IF NOT EXISTS `saved` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
  `progress` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dumping data for table trivia.saved: ~1 rows (approximately)
INSERT INTO `saved` (`id`, `ip`, `progress`) VALUES
	(14, '172.23.0.1', '{"progress":[null,"1","3","4","1","1"]}');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
