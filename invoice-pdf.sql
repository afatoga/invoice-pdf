-- --------------------------------------------------------
-- Hostitel:                     localhost
-- Verze serveru:                10.1.37-MariaDB - mariadb.org binary distribution
-- OS serveru:                   Win32
-- HeidiSQL Verze:               9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportování struktury databáze pro
CREATE DATABASE IF NOT EXISTS `invoice-pdf` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci */;
USE `invoice-pdf`;

-- Exportování struktury pro tabulka invoice-pdf.vm_order
CREATE TABLE IF NOT EXISTS `vm_order` (
  `Id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `CustomerId` mediumint(9) NOT NULL,
  `StatusId` tinyint(6) NOT NULL DEFAULT '1',
  `InsertTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `fk_order_status` (`StatusId`),
  KEY `fk_order_customer` (`CustomerId`),
  CONSTRAINT `fk_order_customer` FOREIGN KEY (`CustomerId`) REFERENCES `vm_user` (`Id`),
  CONSTRAINT `fk_order_status` FOREIGN KEY (`StatusId`) REFERENCES `vm_orderStatus` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Exportování dat pro tabulku invoice-pdf.vm_order: ~16 rows (přibližně)
/*!40000 ALTER TABLE `vm_order` DISABLE KEYS */;
INSERT INTO `vm_order` (`Id`, `CustomerId`, `StatusId`, `InsertTime`) VALUES
	(1, 2, 1, '2019-05-31 22:22:35'),
	(2, 2, 3, '2019-05-31 18:43:30'),
	(3, 1, 1, '2019-05-31 18:42:01'),
	(4, 1, 1, '2019-05-31 12:12:11'),
	(5, 6, 2, '2019-05-31 18:42:02'),
	(6, 6, 1, '2019-05-31 12:55:07'),
	(7, 4, 1, '2019-05-31 12:56:44'),
	(8, 4, 1, '2019-05-31 12:57:39'),
	(9, 7, 1, '2019-06-02 13:36:00'),
	(10, 1, 1, '2019-06-02 13:55:42'),
	(11, 1, 1, '2019-06-02 13:57:19'),
	(12, 1, 1, '2019-06-02 13:58:04'),
	(13, 1, 1, '2019-06-02 13:59:18'),
	(14, 1, 1, '2019-06-02 13:59:39'),
	(15, 8, 1, '2019-06-03 00:14:47'),
	(16, 5, 1, '2019-06-03 01:07:37');
/*!40000 ALTER TABLE `vm_order` ENABLE KEYS */;

-- Exportování struktury pro tabulka invoice-pdf.vm_orderDetails
CREATE TABLE IF NOT EXISTS `vm_orderDetails` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderId` mediumint(9) NOT NULL,
  `ProductId` mediumint(9) NOT NULL,
  `Quantity` tinyint(3) unsigned NOT NULL,
  `Price` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_order_oderId` (`OrderId`),
  KEY `fk_order_productId` (`ProductId`),
  CONSTRAINT `fk_order_oderId` FOREIGN KEY (`OrderId`) REFERENCES `vm_order` (`Id`),
  CONSTRAINT `fk_order_productId` FOREIGN KEY (`ProductId`) REFERENCES `vm_product` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Exportování dat pro tabulku invoice-pdf.vm_orderDetails: ~10 rows (přibližně)
/*!40000 ALTER TABLE `vm_orderDetails` DISABLE KEYS */;
INSERT INTO `vm_orderDetails` (`Id`, `OrderId`, `ProductId`, `Quantity`, `Price`) VALUES
	(53, 2, 1, 1, 100),
	(58, 2, 2, 5, 1000),
	(59, 1, 1, 5, 1000),
	(60, 1, 2, 1, 200),
	(61, 7, 1, 1, 1),
	(62, 9, 3, 3, 3000),
	(63, 15, 1, 2, 500),
	(64, 3, 1, 1, 200),
	(65, 16, 1, 1, 1),
	(66, 6, 1, 1, 1);
/*!40000 ALTER TABLE `vm_orderDetails` ENABLE KEYS */;

-- Exportování struktury pro tabulka invoice-pdf.vm_orderStatus
CREATE TABLE IF NOT EXISTS `vm_orderStatus` (
  `Id` tinyint(6) NOT NULL AUTO_INCREMENT,
  `Title` varchar(120) COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Exportování dat pro tabulku invoice-pdf.vm_orderStatus: ~2 rows (přibližně)
/*!40000 ALTER TABLE `vm_orderStatus` DISABLE KEYS */;
INSERT INTO `vm_orderStatus` (`Id`, `Title`) VALUES
	(1, 'Čeká na platbu'),
	(2, 'Zaplaceno'),
	(3, 'Stornováno');
/*!40000 ALTER TABLE `vm_orderStatus` ENABLE KEYS */;

-- Exportování struktury pro tabulka invoice-pdf.vm_product
CREATE TABLE IF NOT EXISTS `vm_product` (
  `Id` mediumint(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(300) COLLATE utf8mb4_czech_ci NOT NULL,
  `Description` text COLLATE utf8mb4_czech_ci,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Exportování dat pro tabulku invoice-pdf.vm_product: ~4 rows (přibližně)
/*!40000 ALTER TABLE `vm_product` DISABLE KEYS */;
INSERT INTO `vm_product` (`Id`, `Title`, `Description`) VALUES
	(1, 'preklad japonstina', '<html>ahoj</html>\n<script>alert(\'a\');</script>'),
	(2, 'Tlumočení', 'Tlumočení na klientem vybraném místě. Jazyky: čeština, angličtina'),
	(3, 'preklad do nemciny', 'testuji novy formular'),
	(4, 'preklad do italistiny', 'overeny'),
	(17, 'preklad cinstina', 'preklad');
/*!40000 ALTER TABLE `vm_product` ENABLE KEYS */;

-- Exportování struktury pro tabulka invoice-pdf.vm_user
CREATE TABLE IF NOT EXISTS `vm_user` (
  `Id` mediumint(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(250) COLLATE utf8mb4_czech_ci NOT NULL,
  `HashedPassword` varchar(250) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `RoleId` tinyint(6) NOT NULL DEFAULT '2',
  `GoogleId` varchar(250) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `Name` varchar(150) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `Surname` varchar(150) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `Address` varchar(300) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `City` varchar(100) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  `Zip` int(11) DEFAULT NULL,
  `Country` varchar(20) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_user_role` (`RoleId`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`RoleId`) REFERENCES `vm_userRole` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Exportování dat pro tabulku invoice-pdf.vm_user: ~7 rows (přibližně)
/*!40000 ALTER TABLE `vm_user` DISABLE KEYS */;
INSERT INTO `vm_user` (`Id`, `Email`, `HashedPassword`, `RoleId`, `GoogleId`, `Name`, `Surname`, `Address`, `City`, `Zip`, `Country`) VALUES
	(1, 'mryv00@vse.cz', '$2y$10$Uz8SqbBQ3r8R/LvhnEkhZ.LzsG1QXBiOv556Qld9qeyJ0zzlvLclq', 1, NULL, 'Vladimir', 'Tester', 'Novotneho 33', 'Praha', 12300, 'Czechia'),
	(2, 'afatoga@gmail.com', '$2y$10$Uz8SqbBQ3r8R/LvhnEkhZ.LzsG1QXBiOv556Qld9qeyJ0zzlvLclq', 2, NULL, '', '', '', '', 0, NULL),
	(4, 'adam@novak.cz', '$2y$10$Uz8SqbBQ3r8R/LvhnEkhZ.LzsG1QXBiOv556Qld9qeyJ0zzlvLclq', 2, NULL, '', '', '', '', 0, NULL),
	(5, 'nereg@seznam.cz', NULL, 2, NULL, 'User', 'Nereg', '', '', 0, NULL),
	(6, 'stary@seznam.cz', '$2y$10$ZAADgC/A8gIdOkzr4zTs8e9VMrmOFpp48sPkgfrlqMYsHbWqT45GO', 2, NULL, 'Adam', 'Novak', 'Stara 235', 'Praha', 12300, 'Czechia'),
	(7, 'testuji@seznam.cz', '$2y$10$Jc0Yyhtv8UiW06puAenu1OIvoslNg1wjGW3xc.226tNuCEIO1Zg92', 2, NULL, 'Testuji', 'Tohle', NULL, NULL, NULL, NULL),
	(8, 'testuji@centrum.cz', '$2y$10$RtNe/q8zLWInUgD7w87yeuwebbdy3Gv3uF7QfYFFqLOp9Bu2AXtuu', 2, NULL, 'Adam', 'Novak', NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `vm_user` ENABLE KEYS */;

-- Exportování struktury pro tabulka invoice-pdf.vm_userRole
CREATE TABLE IF NOT EXISTS `vm_userRole` (
  `Id` tinyint(6) NOT NULL AUTO_INCREMENT,
  `Title` varchar(120) COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Exportování dat pro tabulku invoice-pdf.vm_userRole: ~2 rows (přibližně)
/*!40000 ALTER TABLE `vm_userRole` DISABLE KEYS */;
INSERT INTO `vm_userRole` (`Id`, `Title`) VALUES
	(1, 'Správce'),
	(2, 'Zákazník');
/*!40000 ALTER TABLE `vm_userRole` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
