-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: fullstack
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `kepemilikan_voucher`
--

DROP TABLE IF EXISTS `kepemilikan_voucher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kepemilikan_voucher` (
  `kode_member` char(9) NOT NULL,
  `kode_voucher` int(11) NOT NULL,
  `kode_unik` varchar(20) DEFAULT NULL,
  `tanggal_terpakai` datetime DEFAULT NULL,
  PRIMARY KEY (`kode_member`,`kode_voucher`),
  KEY `fk_member_has_voucher_voucher1_idx` (`kode_voucher`),
  KEY `fk_member_has_voucher_member1_idx` (`kode_member`),
  CONSTRAINT `fk_member_has_voucher_member1` FOREIGN KEY (`kode_member`) REFERENCES `member` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_member_has_voucher_voucher1` FOREIGN KEY (`kode_voucher`) REFERENCES `voucher` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kepemilikan_voucher`
--

LOCK TABLES `kepemilikan_voucher` WRITE;
/*!40000 ALTER TABLE `kepemilikan_voucher` DISABLE KEYS */;
INSERT INTO `kepemilikan_voucher` VALUES ('m17073011',29,'voc1751158249316a',NULL);
/*!40000 ALTER TABLE `kepemilikan_voucher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `kode` char(9) NOT NULL,
  `iduser` varchar(20) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `url_foto` varchar(100) DEFAULT NULL,
  `isaktif` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`kode`),
  KEY `fk_member_users1_idx` (`iduser`),
  CONSTRAINT `fk_member_users1` FOREIGN KEY (`iduser`) REFERENCES `users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES ('m17073011','m1','m1','2025-04-17','https://cdn.oneesports.gg/cdn-data/2024/05/honkai_star_rail_firefly_and_trailblazer-1024x576.jpg',1),('m17073037','m2','m2','2025-04-17','https://cdn.oneesports.gg/cdn-data/2024/05/honkai_star_rail_firefly_and_trailblazer-1024x576.jpg',0),('m17073701','m3','m3','2025-04-17','https://cdn.oneesports.gg/cdn-data/2024/05/honkai_star_rail_firefly_and_trailblazer-1024x576.jpg',0),('m17073717','m4','m4','2025-04-17','https://cdn.oneesports.gg/cdn-data/2024/05/honkai_star_rail_firefly_and_trailblazer-1024x576.jpg',0),('m17073818','m5','m5','2025-04-17','https://cdn.oneesports.gg/cdn-data/2024/05/honkai_star_rail_firefly_and_trailblazer-1024x576.jpg',0),('m17073847','m6','m6','2025-04-17','https://cdn.oneesports.gg/cdn-data/2024/05/honkai_star_rail_firefly_and_trailblazer-1024x576.jpg',0),('m17073909','m7','m7','2025-04-17','https://cdn.oneesports.gg/cdn-data/2024/05/honkai_star_rail_firefly_and_trailblazer-1024x576.jpg',0),('m28050640','m9','member9','2025-06-28','https://preview.redd.it/okay-lets-face-reality-when-will-we-see-firefly-again-v0-xikjdmsmmaad1.jpeg?',1),('m29014316','m10','m10','2025-06-29','https://i.ytimg.com/vi/eFXQqY3ia6k/maxresdefault.jpg',0),('m29063937','calvin','calvin chou','2003-12-16','1',1),('m29065956','bebek','bebek','2025-04-28','2',0);
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `kode` int(11) NOT NULL AUTO_INCREMENT,
  `kode_jenis` int(11) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `harga_jual` double DEFAULT 0,
  `url_gambar` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`kode`),
  KEY `fk_menu_menu_jenis_idx` (`kode_jenis`),
  CONSTRAINT `fk_menu_menu_jenis` FOREIGN KEY (`kode_jenis`) REFERENCES `menu_jenis` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (47,23,'Appetizers',11000,'images/1749191703_appetizer.jpg'),(49,21,'caesar salad',14000,'images/1749191751_caesar salad.jpg'),(50,15,'caramel ice cream',15000,'images/1749191773_caramel ice cream.jpg'),(52,15,'chocolatte ice cream',17000,'images/1749191977_chocolatte ice cream.jpg'),(54,18,'japanese curry',19000,'images/1749192009_curry.jpg'),(58,11,'matcha cake',23000,'images/1749192077_matcha cake.jpeg'),(59,21,'mediteranian salad',24000,'images/1749192110_mediteranian salad.jpg'),(60,16,'milk shake',25000,'images/1749192128_milkshake.jpg'),(61,16,'red velvet',26000,'images/1749192167_red velvet.jpg'),(62,21,'shrimp salad',27000,'images/1749192195_shrimp salad.jpeg'),(63,15,'strawberry ice cream',28000,'images/1749192221_strawberry ice cream.jpeg'),(64,18,'sushi',29000,'images/1749192241_sushi.jpg'),(65,19,'platter',30000,'images/1749192327_platter.jpeg'),(66,15,'vanilla ice cream',31000,'images/1749192373_vanilla ice cream.jpeg'),(67,20,'snack',32000,'images/1749192426_snack.jpeg'),(70,16,'Test Menu 1.2',234567,'images/1751162197_chocolatte ice cream.jpg');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_jenis`
--

DROP TABLE IF EXISTS `menu_jenis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_jenis` (
  `kode` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_jenis`
--

LOCK TABLES `menu_jenis` WRITE;
/*!40000 ALTER TABLE `menu_jenis` DISABLE KEYS */;
INSERT INTO `menu_jenis` VALUES (11,'Kue'),(15,'Ice Cream'),(16,'Milk Shake'),(18,'Main Course'),(19,'Platter'),(20,'Snacks'),(21,'Salad'),(23,'Appetizers'),(28,'Jenis Menu Baru');
/*!40000 ALTER TABLE `menu_jenis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `iduser` varchar(20) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profil` enum('Admin','Member') DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('admin','$2y$10$gYZQ9XFkCN1Y2bcDVtif6ezwMLSu.K0fJcMGV9LBp0n2NjIzd8RJm','Admin'),('bebek','$2y$10$PpKWKNlxfjiNxtpQwym36uXEdCqh4Eo0YS.H01c1gM36zVn4C8a56','Member'),('calvin','$2y$10$8LUCkAcF.ZYbxeAGR0VpvOOdd9PgjwqU2fLdGfcwzLWM6S3GnyDyi','Member'),('m1','$2y$10$BZJ7IlmgLXov3amEWWNpR.WiOamktZadczGGfJM6LQuJqNi0i75/2','Member'),('m10','$2y$10$uLxzNpR0Htk24hzi9YxhDeFRwY01XvmLkFFxh7MmoMzYD9GYae1Om','Member'),('m2','$2y$10$RC7e0QwIZZcADGTHQ0jJF.lCWMxPPGlWO0hYcYNZHaT7xSn0rgCd.','Member'),('m3','$2y$10$/.m/6h7MRo299rx12i1bS.fNjVBW8ZHpjraO5NPHINabPxhgJyRUC','Member'),('m4','$2y$10$ySt4IVsZxHrn1z9Tk2pge.V9FF/dAXWs/QwYAWqbnR9g6e3FeklSO','Member'),('m5','$2y$10$XRdgbTCJIpKgST.BXwoaT.BqDDdveoFoWRTGeLl1k5tud6m4lBCo6','Member'),('m6','$2y$10$fmYrloySOGEZQTpi5U9hq.UforbHvYwUTHQojxHVF46/sWmPH.KlC','Member'),('m7','$2y$10$Jk0ksuSL1AW1znwWZcchtuQumPXXuSZk2QwRXKmp6iqjnD7Z9mUdi','Member'),('m9','$2y$10$DDGl7me0RdAt2fSBnZ58hO9TdSEHeA5lxqdyvI3Z5nwVrZ9fyRSUW','Member');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher`
--

DROP TABLE IF EXISTS `voucher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voucher` (
  `kode` int(11) NOT NULL AUTO_INCREMENT,
  `kode_menu` int(11) DEFAULT NULL,
  `kode_jenis` int(11) DEFAULT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `mulai_berlaku` datetime DEFAULT NULL,
  `akhir_berlaku` datetime DEFAULT NULL,
  `kuota_max` int(11) DEFAULT NULL,
  `kuota_sisa` int(11) DEFAULT 0,
  `persen_diskon` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`kode`),
  KEY `fk_voucher_menu1_idx` (`kode_menu`),
  KEY `fk_voucher_menu_jenis1_idx` (`kode_jenis`),
  CONSTRAINT `fk_voucher_menu1` FOREIGN KEY (`kode_menu`) REFERENCES `menu` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_voucher_menu_jenis1` FOREIGN KEY (`kode_jenis`) REFERENCES `menu_jenis` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher`
--

LOCK TABLES `voucher` WRITE;
/*!40000 ALTER TABLE `voucher` DISABLE KEYS */;
INSERT INTO `voucher` VALUES (29,NULL,11,'Voucher 1','2025-06-29 00:00:00','2025-07-29 00:00:00',1,0,100),(31,47,11,'Voucher 3','2025-06-29 00:00:00','2025-07-29 00:00:00',1,1,100),(36,NULL,15,'Voucher 4.2','2025-06-29 00:00:00','2025-07-29 00:00:00',2,2,50);
/*!40000 ALTER TABLE `voucher` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-29  9:25:40
