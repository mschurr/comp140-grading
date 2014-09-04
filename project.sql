
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `graders`;
CREATE TABLE `graders` (
  `userid` int(11) NOT NULL,
  `studentid` int(11) NOT NULL,
  PRIMARY KEY (`studentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `graders_override`;
CREATE TABLE `graders_override` (
  `userid` int(11) NOT NULL,
  `studentid` int(11) NOT NULL,
  `assignmentid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`studentid`,`assignmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
  `studentid` int(11) NOT NULL,
  `assignmentid` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  PRIMARY KEY (`studentid`,`assignmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `netid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `section` int(10) unsigned NOT NULL,
  `table` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_netid_unique` (`netid`),
  UNIQUE KEY `students_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `section` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

