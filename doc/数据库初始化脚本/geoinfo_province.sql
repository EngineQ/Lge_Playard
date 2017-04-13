/* 全国省市区县乡镇信息第一级：省 */
DROP TABLE IF EXISTS `lge_geoinfo_province`;

CREATE TABLE `lge_geoinfo_province` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `extra` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8 COMMENT='地理全省信息';



LOCK TABLES `lge_geoinfo_province` WRITE;
INSERT INTO `lge_geoinfo_province` VALUES (10,'北京市','1'),(20,'天津市','1'),(30,'重庆市','1'),(35,'上海市','1'),(40,'四川省','1'),(50,'贵州省','1'),(60,'云南省','1'),(70,'湖南省','1'),(80,'湖北省','1'),(90,'河南省','1'),(100,'河北省','1'),(110,'安徽省','1'),(120,'山西省','1'),(130,'甘肃省','1'),(140,'陕西省','1'),(150,'江西省','1'),(160,'青海省','1'),(170,'浙江省','1'),(180,'江苏省','1'),(190,'广东省','1'),(200,'福建省','1'),(210,'海南省','1'),(215,'山东省','1'),(220,'吉林省','1'),(230,'辽宁省','1'),(250,'黑龙江省','1'),(260,'西藏自治区','1'),(270,'内蒙古自治区','1'),(280,'广西壮族自治区','1'),(290,'宁夏回族自治区','1'),(310,'新疆维吾尔自治区','1'),(315,'香港特别行政区','1'),(320,'澳门特别行政区','1');
UNLOCK TABLES;