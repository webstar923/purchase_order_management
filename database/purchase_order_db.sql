/*
SQLyog Community v12.4.2 (64 bit)
MySQL - 10.4.24-MariaDB : Database - purchase_order_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`purchase_order_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `purchase_order_db`;

/*Table structure for table `category_list` */

DROP TABLE IF EXISTS `category_list`;

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `category_list` */

insert  into `category_list`(`id`,`name`,`date_created`) values 
(1,'Category1','2022-04-12 06:07:09');

/*Table structure for table `costcode_list` */

DROP TABLE IF EXISTS `costcode_list`;

CREATE TABLE `costcode_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT ' 1 = Active, 0 = Inactive',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `costcode_list` */

insert  into `costcode_list`(`id`,`name`,`description`,`status`,`date_created`) values 
(1,'012233','Sample costcode',1,'2022-04-11 18:20:38');

/*Table structure for table `item_list` */

DROP TABLE IF EXISTS `item_list`;

CREATE TABLE `item_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `category_id` int(30) NOT NULL,
  `costcode_id` int(30) NOT NULL,
  `uom_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Data for the table `item_list` */

insert  into `item_list`(`id`,`code`,`name`,`category_id`,`costcode_id`,`uom_id`,`date_created`) values 
(1,'item0001','Item1',1,1,1,'2022-04-13 12:41:10'),
(2,'item0002','Item2',1,1,2,'2022-04-13 12:41:32');

/*Table structure for table `order_items` */

DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `po_id` int(30) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `costcode` varchar(50) NOT NULL,
  `unit_price` float NOT NULL,
  `quantity` float NOT NULL,
  `taxcode_id` int(11) NOT NULL,
  KEY `po_id` (`po_id`),
  KEY `item_no` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `order_items` */

insert  into `order_items`(`po_id`,`item_id`,`unit`,`costcode`,`unit_price`,`quantity`,`taxcode_id`) values 
(1,1,'boxes','012',100,10,1),
(1,2,'pcs','011',50,5,0),
(2,1,'boxes','012233',50,10,1),
(3,1,'boxes','test',10,1,1),
(4,2,'pcs','012233',100,1,0);

/*Table structure for table `po_list` */

DROP TABLE IF EXISTS `po_list`;

CREATE TABLE `po_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `po_no` varchar(100) NOT NULL,
  `project_id` int(30) NOT NULL,
  `supplier_id` int(30) NOT NULL,
  `discount_percentage` float NOT NULL,
  `discount_amount` float NOT NULL,
  `tax_percentage` float NOT NULL,
  `tax_amount` float NOT NULL,
  `notes` text NOT NULL,
  `delivery_address` text NOT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending, 1= Approved, 2 = Denied',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `po_list_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `po_list` */

insert  into `po_list`(`id`,`po_no`,`project_id`,`supplier_id`,`discount_percentage`,`discount_amount`,`tax_percentage`,`tax_amount`,`notes`,`delivery_address`,`delivery_date`,`status`,`date_created`,`date_updated`) values 
(1,'0001-0001',1,2,50,790,10,158,'Test Project 1 Description','123 Amapola Ave, Pacifica, CA 94044, USA','2022-04-12 00:00:00',1,'2022-04-14 09:48:46','2022-04-14 11:44:36'),
(2,'0001-0002',1,2,0,0,0,0,'Test Project 1 Description','123 Amapola Ave, Pacifica, CA 94044, USA','2022-04-13 00:00:00',0,'2022-04-14 11:46:26',NULL),
(3,'0002-abc',2,1,0,0,0,0,'wf','df','2022-03-29 00:00:00',0,'2022-04-14 11:47:15',NULL),
(4,'0002-0001',2,2,50,50,0,0,'wf','df','2022-04-06 00:00:00',1,'2022-04-14 11:48:01',NULL);

/*Table structure for table `project_list` */

DROP TABLE IF EXISTS `project_list`;

CREATE TABLE `project_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `project_no` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` text NOT NULL,
  `contact` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Data for the table `project_list` */

insert  into `project_list`(`id`,`project_no`,`name`,`address`,`contact`,`description`,`date_created`) values 
(1,'0001','Project 1','123 Amapola Ave, Pacifica, CA 94044, USA','1,3','Test Project 1 Description','2022-04-13 12:33:41'),
(2,'0002','Project 2','df','1','wf','2022-04-14 07:17:06');

/*Table structure for table `receive_order_items` */

DROP TABLE IF EXISTS `receive_order_items`;

CREATE TABLE `receive_order_items` (
  `ro_id` int(30) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `received_qty` float NOT NULL,
  KEY `item_no` (`item_id`),
  KEY `ro_id` (`ro_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `receive_order_items` */

insert  into `receive_order_items`(`ro_id`,`item_id`,`quantity`,`received_qty`) values 
(1,1,10,10),
(1,2,5,5),
(2,1,10,3);

/*Table structure for table `ro_list` */

DROP TABLE IF EXISTS `ro_list`;

CREATE TABLE `ro_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `ro_no` varchar(250) NOT NULL,
  `packing_slip_no` varchar(250) NOT NULL,
  `po_id` int(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Not Received, 1=Partially Received,\r\n2=Fully Received',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Data for the table `ro_list` */

insert  into `ro_list`(`id`,`ro_no`,`packing_slip_no`,`po_id`,`status`,`date_created`,`date_updated`) values 
(1,'0001-0001-0001','',1,2,'2022-04-14 16:55:19','2022-04-14 17:48:40'),
(2,'0001-0002-0001','test123333',2,1,'2022-04-14 17:40:59','2022-04-14 17:50:03');

/*Table structure for table `sc_list` */

DROP TABLE IF EXISTS `sc_list`;

CREATE TABLE `sc_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `item_id` int(30) NOT NULL,
  `supplier_id` int(30) NOT NULL,
  `sku` varchar(250) NOT NULL,
  `price` varchar(250) NOT NULL,
  `price_expiry_date` datetime NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `sc_list` */

insert  into `sc_list`(`id`,`item_id`,`supplier_id`,`sku`,`price`,`price_expiry_date`,`date_created`) values 
(1,1,2,'dsa222','123.22','2022-04-15 00:00:00','2022-04-12 09:27:40');

/*Table structure for table `supplier_list` */

DROP TABLE IF EXISTS `supplier_list`;

CREATE TABLE `supplier_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `address` text NOT NULL,
  `contact_person` text NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT ' 0 = Inactive, 1 = Active',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Data for the table `supplier_list` */

insert  into `supplier_list`(`id`,`name`,`address`,`contact_person`,`contact`,`email`,`status`,`date_created`) values 
(1,'Supplier 101','Sample Address Only','George Wilson','09123459879','supplier101@gmail.com',1,'2021-09-08 09:46:45'),
(2,'Supplier 102','Supplier 102 Address, 23rd St, Sample City, Test Province, ####','Samantha Lou','09332145889','sLou@supplier102.com',1,'2021-09-08 10:25:12');

/*Table structure for table `system_info` */

DROP TABLE IF EXISTS `system_info`;

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

/*Data for the table `system_info` */

insert  into `system_info`(`id`,`meta_field`,`meta_value`) values 
(1,'name','Purchase Order Management System - PHP'),
(6,'short_name','POMS-PHP'),
(11,'logo','uploads/1631064180_sample_compaby_logo.jpg'),
(13,'user_avatar','uploads/user_avatar.jpg'),
(14,'cover','uploads/1631064360_sample_bg.jpg'),
(15,'company_name','My Sample Company Co.'),
(16,'company_email','info@sampleco.com'),
(17,'company_address','Sample Address, 23rd St., Sample City, ####');

/*Table structure for table `taxcode_list` */

DROP TABLE IF EXISTS `taxcode_list`;

CREATE TABLE `taxcode_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) NOT NULL,
  `percentage` float NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Data for the table `taxcode_list` */

insert  into `taxcode_list`(`id`,`code`,`percentage`,`date_created`) values 
(1,'Test Tax',33,'2022-04-12 10:03:36');

/*Table structure for table `uom_list` */

DROP TABLE IF EXISTS `uom_list`;

CREATE TABLE `uom_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Data for the table `uom_list` */

insert  into `uom_list`(`id`,`name`,`date_created`) values 
(1,'boxes','2022-04-14 16:51:49'),
(2,'pcs','2022-04-14 16:51:54');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `users` */

insert  into `users`(`id`,`firstname`,`lastname`,`username`,`password`,`avatar`,`last_login`,`type`,`date_added`,`date_updated`) values 
(1,'Adminstrator','Admin','admin','0192023a7bbd73250516f069df18b500','uploads/1624240500_avatar.png',NULL,1,'2021-01-20 14:02:37','2021-06-21 09:55:07'),
(3,'Mike ','Williams','mwilliams','a88df23ac492e6e2782df6586a0c645f','uploads/1630999200_avatar5.png',NULL,2,'2021-09-07 15:20:40',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
