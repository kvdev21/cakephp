CREATE DATABASE IF NOT EXISTS `jukebox` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `jukebox`;

DROP TABLE IF EXISTS `cake_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `cake_sessions` (
  `id` varchar(255) NOT NULL default '',
  `data` text,
  `expires` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

delimiter $$

DROP TABLE IF EXISTS `music_entity_config_dates`$$
CREATE TABLE IF NOT EXISTS `music_entity_config_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `channel` varchar(255) NOT NULL,
  `entity_id` int(11) NOT NULL DEFAULT '0',
  `company_music_entity_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

DROP TABLE IF EXISTS `tracks`$$
CREATE TABLE IF NOT EXISTS `tracks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artist_id` int(11) NOT NULL,
  `track_name` varchar(255) NOT NULL,
  `bpm` int(11) NOT NULL DEFAULT '0',
  `length` time NOT NULL DEFAULT '00:00:00',
  `file` varchar(255) NOT NULL,
  `rating` float(5,4) NOT NULL,
  `year` int(11) NOT NULL DEFAULT '0',
  `high` int(11) NOT NULL DEFAULT '0',
  `track_review_count` int(11) NOT NULL DEFAULT '0',
  `track_rating_count` int(11) NOT NULL DEFAULT '0',
  `uid` varchar(38) NOT NULL,
  `added_on_config_date_id` int(11) NOT NULL DEFAULT '0',
  `genre_1_id` int(11) NOT NULL,
  `genre_2_id` int(11) NOT NULL,
  `genre_3_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `track_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

DROP TABLE IF EXISTS `track_artists`$$
CREATE TABLE IF NOT EXISTS `track_artists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

DROP TABLE IF EXISTS `genres`$$
CREATE TABLE IF NOT EXISTS `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `od_id` mediumint(9) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

DROP TABLE IF EXISTS `playlists`$$
CREATE TABLE IF NOT EXISTS `playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Untitled',
  `author` varchar(255) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `day_part_id` int(11) NOT NULL DEFAULT '0',
  `week_part_id` int(11) NOT NULL DEFAULT '0',
  `times_played` int(11) NOT NULL DEFAULT '0',
  `file` varchar(255) NOT NULL,
  `rating` float(5,4) NOT NULL,
  `length` time NOT NULL DEFAULT '00:00:00',
  `playlist_review_count` int(11) NOT NULL DEFAULT '0',
  `playlist_rating_count` int(11) NOT NULL DEFAULT '0',
  `indate` date DEFAULT NULL,
  `outdate` date DEFAULT NULL,
  `starttime` time DEFAULT NULL,
  `finishtime` time DEFAULT NULL,
  `dayofweek` int(11) NOT NULL DEFAULT '-1',
  `uid` varchar(38) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

DROP TABLE IF EXISTS `playlist_tracks`$$
CREATE TABLE IF NOT EXISTS `playlist_tracks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

DROP TABLE IF EXISTS `config_date_tracks`$$
CREATE TABLE IF NOT EXISTS `config_date_tracks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `music_entity_config_date_id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

delimiter ;

INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (1,0,'Blues');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (2,1,'Classic Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (3,2,'Country');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (4,3,'Commercial Dance');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (5,4,'Disco');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (6,5,'Funk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (7,6,'Grunge');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (8,7,'Hip-Hop');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (9,8,'Jazz');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (10,9,'Metal');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (11,10,'New Age');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (12,11,'Oldies');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (13,12,'Other');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (14,13,'Pop');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (15,14,'R&amp;B');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (16,15,'Rap');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (17,16,'Reggae');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (18,17,'Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (19,18,'Techno');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (20,19,'Industrial');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (21,20,'Alternative');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (22,21,'Ska');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (23,22,'Death Metal');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (24,23,'Pranks');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (25,24,'Soundtrack');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (26,25,'Euro-Techno');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (27,26,'Ambient');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (28,27,'Trip-Hop');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (29,28,'Vocal');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (30,29,'Jazz+Funk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (31,30,'Fusion');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (32,31,'Trance');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (33,32,'Classical');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (34,33,'Instrumental');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (35,34,'Acid');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (36,35,'House');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (37,36,'Game');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (38,37,'Sound Clip');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (39,38,'Gospel');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (40,39,'Noise');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (41,40,'AlternRock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (42,41,'Bass');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (43,42,'Soul');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (44,43,'Punk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (45,44,'Space');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (46,45,'Meditative');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (47,46,'Instrumental Pop');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (48,47,'Instrumental Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (49,48,'Ethnic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (50,49,'Gothic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (51,50,'Darkwave');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (52,51,'Techno-Industrial');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (53,52,'Electronic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (54,53,'Pop-Folk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (55,54,'Eurodance');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (56,55,'Dream');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (57,56,'Southern Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (58,57,'Comedy');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (59,58,'Cult');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (60,59,'Gangsta');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (61,60,'Top 40');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (62,61,'Christian Rap');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (63,62,'Pop/Funk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (64,63,'Jungle');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (65,64,'Native American');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (66,65,'Cabaret');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (67,66,'New Wave');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (68,67,'Psychedelic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (69,68,'Rave');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (70,69,'Showtunes');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (71,70,'Trailer');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (72,71,'Lo-Fi');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (73,72,'Tribal');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (74,73,'Acid Punk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (75,74,'Acid Jazz');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (76,75,'Polka');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (77,76,'Retro');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (78,77,'Musical');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (79,78,'Rock &amp; Roll');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (80,79,'Hard Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (81,80,'Folk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (82,81,'Folk/Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (83,82,'National Folk');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (84,83,'Swing');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (85,84,'Bebob');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (86,85,'Latin');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (87,86,'Revival');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (88,87,'Celtic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (89,88,'Bluegrass');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (90,89,'Avantgarde');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (91,90,'Gothic Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (92,91,'Progressive Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (93,92,'Psychedelic Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (94,93,'Symphonic Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (95,94,'Slow Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (96,95,'Big Band');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (97,96,'Chorus');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (98,97,'Easy Listening');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (99,98,'Acoustic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (100,99,'Humour');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (101,100,'Speech');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (102,101,'Chanson');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (103,102,'Opera');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (104,103,'Chamber Music');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (105,104,'Sonata');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (106,105,'Symphony');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (107,106,'Booty Bass');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (108,107,'Primus');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (109,108,'Porn Groove');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (110,109,'Satire');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (111,110,'Slow Jam');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (112,111,'Tango');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (113,112,'Club');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (114,113,'Samba');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (115,114,'Folklore');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (116,115,'Ballad');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (117,116,'Motown');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (118,117,'Glam');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (119,118,'M. O. R.');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (120,119,'Singalong');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (121,120,'Garage');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (122,121,'Christmas');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (123,122,'Old School');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (124,123,'Football');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (125,124,'Party');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (126,125,'French');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (127,126,'Cheese');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (128,127,'AOR');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (129,128,'Boy Band');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (130,129,'Girl Band');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (131,130,'Salsa');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (132,131,'Hed Kandi');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (133,132,'Classic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (134,133,'Latin House');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (135,134,'Karaoke');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (136,135,'Cafe del Mar');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (137,136,'Funky House');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (138,137,'Dance');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (139,138,'Action');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (140,139,'New Romantic');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (141,140,'Novelty');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (142,141,'Soft Rock');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (143,142,'Beat');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (144,143,'Irish');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (145,144,'Welsh');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (146,145,'Scottish');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (147,146,'English');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (148,147,'Fantasy');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (149,148,'Science Fiction');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (150,149,'Thriller');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (151,150,'Horror');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (152,151,'Romance');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (153,152,'Film Clip');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (154,153,'X***X');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (155,154,'Montage');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (156,155,'Marketing');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (157,156,'Theme');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (158,157,'Japanese');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (159,158,'Rugby');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (160,159,'Halloween');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (161,160,'Northern Soul');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (162,161,'Soul (60s)');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (163,162,'Soul (70s)');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (164,163,'Soul (80s)');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (165,164,'Advert');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (166,165,'TV Theme');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (167,166,'Film Theme');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (168,167,'Australia');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (169,168,'New Zealand');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (170,169,'CRC CoolWall');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (171,170,'CRC Early');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (172,171,'CRC Mid');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (173,172,'CRC Late');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (174,173,'New Orleans Jazz');
INSERT INTO `genres` (`id`,`od_id`,`title`) VALUES (175,174,'Brazilian');

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

DROP TABLE IF EXISTS `track_tags`;
CREATE TABLE IF NOT EXISTS `track_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `track_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `track_id` (`track_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL,
  `current_track_id` int(11) NOT NULL DEFAULT '0',
  `current_track_type` varchar(5) NOT NULL,
  `current_track_start_time` bigint(20) NOT NULL DEFAULT '0',
  `last_video_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `current_track_id`, `current_track_type`, `current_track_start_time`, `last_video_update`) VALUES
(1, 0, 'none', '315529200000', '1980-01-01 00:00:00');