-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `euroharmony_ng`;
CREATE DATABASE `euroharmony_ng` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `euroharmony_ng`;

DROP TABLE IF EXISTS `acars`;
CREATE TABLE `acars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(4) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aggregate_id` varchar(40) NOT NULL DEFAULT '',
  `origin` varchar(4) NOT NULL DEFAULT '',
  `destination` varchar(4) NOT NULL DEFAULT '',
  `lat` double NOT NULL DEFAULT '0',
  `lon` double NOT NULL DEFAULT '0',
  `bearing` smallint(3) NOT NULL DEFAULT '0',
  `altitude` mediumint(6) NOT NULL DEFAULT '0',
  `ias` smallint(3) NOT NULL DEFAULT '0',
  `fuel` mediumint(6) NOT NULL DEFAULT '0',
  `aircraft` smallint(3) unsigned NOT NULL DEFAULT '0',
  `propilot_flight` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ACARS_IDX_PILOTID` (`username`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `afas_pilots`;
CREATE TABLE `afas_pilots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(4) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `num_flights` tinyint(4) NOT NULL DEFAULT '0',
  `last_assigned_date` date NOT NULL DEFAULT '0000-00-00',
  `time_interval` varchar(10) NOT NULL DEFAULT '',
  `prefered_ranks` varchar(20) NOT NULL DEFAULT '',
  `prefered_divisions` varchar(20) NOT NULL DEFAULT '',
  `prefered_hubs` varchar(20) NOT NULL DEFAULT '',
  `return` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `pilotid` (`username`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `aircraft`;
CREATE TABLE `aircraft` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `clss` tinyint(1) unsigned DEFAULT NULL,
  `pax` smallint(3) unsigned DEFAULT NULL,
  `cargo` int(10) NOT NULL COMMENT 'lbs',
  `division` tinyint(1) unsigned DEFAULT NULL,
  `in_fleet` tinyint(1) unsigned DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `charter` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `icao_code` varchar(4) NOT NULL DEFAULT '',
  `variant` varchar(6) DEFAULT NULL COMMENT 'Fleet variant code',
  `page_name` varchar(50) NOT NULL DEFAULT '',
  `aircraft_type` char(1) DEFAULT NULL,
  `description` text,
  `length` int(6) DEFAULT NULL COMMENT 'm',
  `wingspan` int(6) DEFAULT NULL COMMENT 'm',
  `height` int(6) DEFAULT NULL COMMENT 'm',
  `engine` varchar(25) DEFAULT NULL,
  `engine_manufacturer` varchar(25) NOT NULL,
  `cruise_speed` int(6) DEFAULT NULL COMMENT 'kts',
  `service_ceiling` int(8) DEFAULT NULL COMMENT 'ft',
  `gross_weight` int(6) DEFAULT NULL COMMENT 't',
  `crew` varchar(25) DEFAULT NULL,
  `price` int(20) DEFAULT NULL COMMENT 'EUR',
  `manufacturer` varchar(25) DEFAULT NULL,
  `oew` int(10) DEFAULT NULL COMMENT 'lbs',
  `mtow` int(10) DEFAULT NULL COMMENT 'lbs',
  `fuel_capacity` int(6) DEFAULT NULL COMMENT 'gal',
  `fuel_weight` int(8) DEFAULT NULL COMMENT 'lbs',
  `long_range_altitude` varchar(20) DEFAULT NULL,
  `long_range_speed` int(6) DEFAULT NULL COMMENT 'kts',
  `max_speed` int(6) DEFAULT NULL COMMENT 'kts',
  `range_mload` int(6) DEFAULT NULL COMMENT 'nm',
  `range_mfuel` int(6) DEFAULT NULL COMMENT 'nm',
  `engine_thrust` varchar(20) DEFAULT NULL,
  `to_rwy_length_min` int(5) DEFAULT NULL COMMENT 'ft',
  `to_rwy_length_max` int(5) DEFAULT NULL COMMENT 'ft',
  `land_rwy_length` int(5) DEFAULT NULL COMMENT 'ft',
  `v_rotate` int(4) DEFAULT NULL COMMENT 'kts',
  `v_approach` int(4) DEFAULT NULL COMMENT 'kts',
  `flaps_rotate` varchar(10) DEFAULT NULL,
  `flaps_approach` varchar(10) DEFAULT NULL,
  `maximum_climb_rate` int(6) DEFAULT '3600',
  `maximum_desc_rate` int(6) DEFAULT '3000',
  `submitted` datetime NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `IDX_ICAO_CODE` (`icao_code`),
  KEY `division` (`division`),
  KEY `class` (`clss`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `aircraft_downloads`;
CREATE TABLE `aircraft_downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aircraft_id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `flight_sim_id` int(11) NOT NULL,
  `model` varchar(200) DEFAULT NULL,
  `location` varchar(400) NOT NULL,
  `payware` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(1000) NOT NULL,
  `submitted` datetime NOT NULL,
  `submitted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aircraft_id` (`aircraft_id`),
  KEY `flight_sim_id` (`flight_sim_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `aircraft_downloads_type`;
CREATE TABLE `aircraft_downloads_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `airports`;
CREATE TABLE `airports` (
  `ICAO` varchar(4) NOT NULL DEFAULT '',
  `Name` varchar(40) NOT NULL DEFAULT '',
  `Country` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`ICAO`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `airports_data`;
CREATE TABLE `airports_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ICAO` varchar(4) NOT NULL DEFAULT '',
  `lat` double NOT NULL DEFAULT '0',
  `long` double NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `icao` (`ICAO`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `awards_assigned`;
CREATE TABLE `awards_assigned` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(4) NOT NULL DEFAULT '',
  `user_id` int(11) DEFAULT NULL,
  `awards_index_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `notes` text NOT NULL,
  `assigned_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_PILOTID` (`username`),
  KEY `user_id` (`user_id`),
  KEY `awards_index_id` (`awards_index_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `awards_index`;
CREATE TABLE `awards_index` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `awardtype` varchar(50) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `award_name` varchar(30) NOT NULL DEFAULT '',
  `automatic` char(1) NOT NULL DEFAULT 'N',
  `tour` tinyint(1) NOT NULL DEFAULT '0',
  `event` tinyint(1) NOT NULL DEFAULT '0',
  `aggregate_award_name` varchar(100) NOT NULL DEFAULT '',
  `aggregate_award_rank` int(3) DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `config_codesets`;
CREATE TABLE `config_codesets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(25) NOT NULL,
  `code_id` varchar(100) NOT NULL,
  `code_description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`code_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='SQL encoded enumerations';

DROP TABLE IF EXISTS `config_featured`;
CREATE TABLE `config_featured` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT NULL,
  `uri` varchar(500) DEFAULT NULL,
  `enabled` int(1) NOT NULL DEFAULT '1',
  `order` int(6) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `country` char(2) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `alt_name` varchar(50) NOT NULL,
  `europe` int(1) DEFAULT NULL,
  PRIMARY KEY (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `divisions`;
CREATE TABLE `divisions` (
  `id` int(11) NOT NULL DEFAULT '0',
  `division_shortname` varchar(100) NOT NULL DEFAULT '',
  `division_longname` varchar(100) NOT NULL DEFAULT '',
  `description` text,
  `colour` varchar(6) NOT NULL DEFAULT '000000',
  `text` varchar(6) NOT NULL DEFAULT 'FFFFFF',
  `prefix` varchar(4) DEFAULT NULL,
  `primary` tinyint(1) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `missions` tinyint(1) NOT NULL DEFAULT '0',
  `tours` tinyint(1) NOT NULL DEFAULT '0',
  `events` tinyint(4) NOT NULL DEFAULT '0',
  `charters` tinyint(1) NOT NULL DEFAULT '0',
  `blurb` text NOT NULL,
  `submitted` datetime NOT NULL,
  `submitted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prefix` (`prefix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ehm_sessions`;
CREATE TABLE `ehm_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(1000) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fg_aircraft`;
CREATE TABLE `fg_aircraft` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `fg_id` mediumint(5) NOT NULL,
  `division_id` mediumint(5) NOT NULL,
  `classes` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fg_divisions`;
CREATE TABLE `fg_divisions` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `description` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fg_types`;
CREATE TABLE `fg_types` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `description` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `flight_sim_series`;
CREATE TABLE `flight_sim_series` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL DEFAULT '',
  `windows` tinyint(1) NOT NULL DEFAULT '0',
  `mac` tinyint(1) NOT NULL DEFAULT '0',
  `linux` tinyint(1) NOT NULL DEFAULT '0',
  `supported` tinyint(1) NOT NULL DEFAULT '1',
  `display` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `flight_sim_versions`;
CREATE TABLE `flight_sim_versions` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `series_id` int(20) NOT NULL,
  `version_number` int(2) DEFAULT NULL,
  `version_name` varchar(20) DEFAULT NULL,
  `flogger_name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fuel_burn`;
CREATE TABLE `fuel_burn` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `pilot_id` int(7) DEFAULT NULL,
  `pilot_username` varchar(4) DEFAULT NULL,
  `aircraft_id` int(20) NOT NULL,
  `aircraft_title` varchar(150) DEFAULT NULL,
  `gcd` int(20) NOT NULL COMMENT 'nm',
  `fuel_burnt` int(11) NOT NULL COMMENT 'lbs',
  `duration` int(20) NOT NULL,
  `cruise_alt` int(20) NOT NULL,
  `cruise_spd` int(20) DEFAULT NULL,
  `propilot` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aircraft_id` (`aircraft_id`),
  KEY `gcd` (`gcd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `hub`;
CREATE TABLE `hub` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `hub_icao` varchar(4) NOT NULL DEFAULT '',
  `hub_name` varchar(200) DEFAULT NULL,
  `connection_centre` int(1) NOT NULL DEFAULT '0',
  `hub_captain_id` int(20) DEFAULT NULL,
  `hub_description` text NOT NULL,
  `hub_opened` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hub_icao` (`hub_icao`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `icao_airports_sim_fix`;
CREATE TABLE `icao_airports_sim_fix` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `icao_code` char(4) NOT NULL,
  `flight_sim_id` int(11) NOT NULL,
  `flight_sim_code` char(4) NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `icao_code` (`icao_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Fix bug where sims report different codes';

DROP TABLE IF EXISTS `management_departments`;
CREATE TABLE `management_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `order` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `management_ranks`;
CREATE TABLE `management_ranks` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pips` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mission_index`;
CREATE TABLE `mission_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `flightnumber` varchar(4) NOT NULL DEFAULT '',
  `start_icao` varchar(4) DEFAULT NULL,
  `end_icao` varchar(4) DEFAULT NULL,
  `dep_time` time DEFAULT NULL,
  `arr_time` time DEFAULT NULL,
  `division` int(2) NOT NULL DEFAULT '0',
  `class` int(2) DEFAULT NULL,
  `passengers` int(5) DEFAULT NULL,
  `cargo` int(10) DEFAULT NULL,
  `aircraft_id` int(11) DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date DEFAULT NULL,
  `dep_weather` text,
  `arr_weather` text,
  PRIMARY KEY (`id`),
  KEY `start_icao` (`start_icao`),
  KEY `end_icao` (`end_icao`),
  KEY `aircraft_id` (`aircraft_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `networks`;
CREATE TABLE `networks` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(255) NOT NULL DEFAULT '',
  `news_text` text NOT NULL,
  `news_image_name` varchar(255) DEFAULT NULL,
  `news_start_date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `news_end_date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `branch_type` int(11) NOT NULL DEFAULT '0',
  `context` varchar(100) DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pilots`;
CREATE TABLE `pilots` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `username` varchar(4) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `usergroup` int(3) DEFAULT NULL,
  `department` int(3) DEFAULT NULL,
  `management_pips` int(3) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `fname` varchar(25) NOT NULL,
  `sname` varchar(25) NOT NULL,
  `country` varchar(2) NOT NULL,
  `date_of_birth` date NOT NULL DEFAULT '0000-00-00',
  `location` varchar(25) NOT NULL DEFAULT '0',
  `hub` tinyint(1) NOT NULL DEFAULT '0',
  `hub_last_change` datetime NOT NULL,
  `pp_location` varchar(4) DEFAULT NULL,
  `pp_lastflight` datetime DEFAULT NULL,
  `deadhead_dest` varchar(4) DEFAULT NULL,
  `deadhead_direct` tinyint(1) NOT NULL DEFAULT '0',
  `deadhead_set` datetime DEFAULT NULL,
  `pilotname_DELETE` varchar(50) NOT NULL DEFAULT '',
  `emailaddress` varchar(60) NOT NULL DEFAULT '',
  `email_valid` tinyint(1) NOT NULL DEFAULT '0',
  `email_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `email_verify_code` varchar(10) NOT NULL,
  `signupdate` date NOT NULL DEFAULT '0000-00-00',
  `experience` varchar(30) NOT NULL DEFAULT '',
  `otherva` varchar(10) NOT NULL DEFAULT '',
  `prefjet` varchar(25) NOT NULL DEFAULT '0',
  `prefprop` varchar(25) NOT NULL DEFAULT '0',
  `refered` mediumtext NOT NULL,
  `knowva` tinyint(1) NOT NULL DEFAULT '0',
  `comments` mediumtext NOT NULL,
  `lastflight` date NOT NULL DEFAULT '0000-00-00',
  `flighthours` int(11) NOT NULL DEFAULT '0',
  `flightmins` tinyint(2) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `rank` tinyint(2) NOT NULL DEFAULT '0',
  `fsversion` tinyint(1) NOT NULL DEFAULT '0',
  `iplog` varchar(15) NOT NULL DEFAULT '',
  `lastactive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remarks` mediumtext NOT NULL,
  `letter_sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vatsim_uid` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `ivao_uid` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `almost_removed` int(11) DEFAULT NULL,
  `almost_inactive` int(11) DEFAULT NULL,
  `send_why_email` tinyint(4) DEFAULT '0',
  `fac` decimal(11,2) NOT NULL DEFAULT '0.00',
  `override_prune` tinyint(4) NOT NULL DEFAULT '0',
  `curr_location` varchar(4) NOT NULL DEFAULT '',
  `travelling_mode` tinyint(4) DEFAULT '1',
  `suspension_mode` tinyint(4) DEFAULT '0',
  `suspension_start_date` date DEFAULT NULL,
  `suspension_days_interval` tinyint(4) DEFAULT NULL,
  `suspension_penalty_id` int(11) DEFAULT NULL,
  `suspension_aggregate_id` varchar(40) DEFAULT NULL,
  `have_final_id` char(1) NOT NULL DEFAULT 'N',
  `authCode` varchar(40) NOT NULL DEFAULT '',
  `receive_emails` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Opt-out from bulk emails?',
  PRIMARY KEY (`id`),
  KEY `IDX_PILOTID` (`username`),
  KEY `usergroup` (`usergroup`),
  KEY `pp_location` (`pp_location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

DROP TABLE IF EXISTS `pilots_promotion`;
CREATE TABLE `pilots_promotion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pilots_id` int(11) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `promoted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pilots_id` (`pilots_id`,`rank_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pirep`;
CREATE TABLE `pirep` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(4) NOT NULL DEFAULT '',
  `hub` varchar(4) NOT NULL DEFAULT '',
  `aircraft` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `onoffline` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `flightnumber` varchar(5) NOT NULL DEFAULT '0',
  `start_icao` varchar(4) NOT NULL DEFAULT '',
  `end_icao` varchar(4) NOT NULL DEFAULT '',
  `passengers` smallint(3) unsigned NOT NULL DEFAULT '0',
  `cargo` int(10) DEFAULT NULL,
  `cruisealt` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `cruisespd` varchar(10) NOT NULL DEFAULT '',
  `approach` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fuelburnt` varchar(13) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `submitdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL,
  `checked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `engine_start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `engine_stop_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `departure_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `landing_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocktime_mins` int(6) DEFAULT NULL,
  `pausetime_mins` int(8) DEFAULT NULL,
  `comments_mt` text NOT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `circular_distance` decimal(10,0) NOT NULL DEFAULT '0',
  `from_fl` tinyint(4) NOT NULL DEFAULT '0',
  `act_different` tinyint(4) NOT NULL DEFAULT '0',
  `fl_version` varchar(20) DEFAULT '0',
  `aggregate_id` varchar(40) DEFAULT NULL,
  `pp_score` decimal(11,2) NOT NULL DEFAULT '0.00',
  `pp_score_ng` int(13) DEFAULT NULL,
  `aircraft_tech_name` varchar(150) DEFAULT NULL,
  `propilot_flight` tinyint(4) NOT NULL DEFAULT '0',
  `route` text,
  `award_id` int(11) DEFAULT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `tour_leg_id` int(11) DEFAULT NULL,
  `mission_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `event_leg_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AGGID2` (`aggregate_id`),
  KEY `IDX_TAIRPORT` (`start_icao`),
  KEY `IDX_AAIRPORT` (`end_icao`),
  KEY `checked` (`checked`),
  KEY `user_id` (`user_id`),
  KEY `username` (`username`),
  KEY `event_leg_id` (`event_leg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pirep_assigned`;
CREATE TABLE `pirep_assigned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `start_icao` varchar(4) NOT NULL,
  `end_icao` varchar(4) NOT NULL,
  `gcd` int(11) NOT NULL COMMENT 'nm',
  `aircraft_id` int(25) NOT NULL,
  `passengers` int(5) NOT NULL,
  `cargo` int(10) NOT NULL,
  `dep_time` time DEFAULT NULL,
  `group_id` datetime DEFAULT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `tour_leg_id` int(11) DEFAULT NULL,
  `event_id` int(11) NOT NULL,
  `event_leg_id` int(11) NOT NULL,
  `mission_id` int(11) DEFAULT NULL,
  `fs_version` int(11) DEFAULT NULL,
  `group_order` int(4) DEFAULT NULL,
  `created` datetime NOT NULL,
  `award_completion` int(1) NOT NULL DEFAULT '0',
  `award_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `start_icao` (`start_icao`),
  KEY `end_icao` (`end_icao`),
  KEY `aircraft_id` (`aircraft_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pirep_queries`;
CREATE TABLE `pirep_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pirep_id` int(11) NOT NULL,
  `from_pilot` int(1) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `submitted` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pirep_id` (`pirep_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `propilot_aircraft`;
CREATE TABLE `propilot_aircraft` (
  `aircraft_id` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tail_id` varchar(15) DEFAULT NULL,
  `state_id` int(11) NOT NULL DEFAULT '1',
  `location` varchar(4) NOT NULL DEFAULT 'EGLL',
  `destination` varchar(4) DEFAULT NULL,
  `gcd` int(11) DEFAULT NULL,
  `observations` varchar(255) DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  `rollout` datetime DEFAULT NULL,
  `last_maintenance` datetime DEFAULT NULL,
  `reserved` datetime DEFAULT NULL,
  `reserved_by` int(11) DEFAULT NULL,
  `pax` int(11) DEFAULT NULL,
  `cargo` int(11) DEFAULT NULL,
  `last_flown` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tail_id` (`tail_id`),
  KEY `reserved_by` (`reserved_by`),
  KEY `location` (`location`),
  KEY `owner` (`owner`),
  KEY `aircraft_id` (`aircraft_id`),
  KEY `state_id` (`state_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `propilot_aircraft_crash`;
CREATE TABLE `propilot_aircraft_crash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aircraft_unique_id` int(11) NOT NULL DEFAULT '0',
  `username` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `datetime_crash` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `report_given` text,
  `report_insertion_datetime` datetime DEFAULT NULL,
  `aggregate_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aircraft_unique_id` (`aircraft_unique_id`),
  KEY `username` (`username`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=1;

DROP TABLE IF EXISTS `propilot_aircraft_state`;
CREATE TABLE `propilot_aircraft_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL DEFAULT '0',
  `state_name` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `state_id` (`state_id`),
  UNIQUE KEY `state_name` (`state_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `propilot_event_index`;
CREATE TABLE `propilot_event_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `aircraft_id` int(11) DEFAULT NULL,
  `difficulty` text,
  `description` text,
  `start_date` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `submitted` datetime DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `propilot_event_legs`;
CREATE TABLE `propilot_event_legs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `start_icao` varchar(4) DEFAULT NULL,
  `end_icao` varchar(4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `award_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `propilot_extra_penalty`;
CREATE TABLE `propilot_extra_penalty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penalty_id` int(11) DEFAULT NULL,
  `pilot_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `propilot_params`;
CREATE TABLE `propilot_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `value` varchar(50) NOT NULL DEFAULT '',
  `comments` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `propilot_penalty`;
CREATE TABLE `propilot_penalty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penalty_id` int(11) NOT NULL DEFAULT '0',
  `penalty_name` varchar(255) NOT NULL DEFAULT '',
  `penalty_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `penalty_description` text,
  PRIMARY KEY (`id`),
  KEY `IDX_PENALTYID` (`penalty_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ranks`;
CREATE TABLE `ranks` (
  `id` tinyint(2) NOT NULL DEFAULT '0',
  `rank` char(3) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `stats_order` tinyint(4) DEFAULT NULL,
  `class` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `class` (`class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `id` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `obs` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `timetable`;
CREATE TABLE `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flightnumber` varchar(5) NOT NULL DEFAULT '',
  `hub` int(11) DEFAULT NULL,
  `dep_airport` varchar(4) NOT NULL DEFAULT '',
  `arr_airport` varchar(4) NOT NULL DEFAULT '0',
  `dep_time` time NOT NULL DEFAULT '00:00:00',
  `arr_time` time NOT NULL DEFAULT '00:00:00',
  `days_DELETE` varchar(7) NOT NULL DEFAULT '',
  `sun` tinyint(1) NOT NULL DEFAULT '0',
  `mon` tinyint(1) NOT NULL DEFAULT '0',
  `tue` tinyint(1) NOT NULL DEFAULT '0',
  `wed` tinyint(1) NOT NULL DEFAULT '0',
  `thu` tinyint(1) NOT NULL DEFAULT '0',
  `fri` tinyint(1) NOT NULL DEFAULT '0',
  `sat` tinyint(1) NOT NULL DEFAULT '0',
  `season_month_start` int(2) DEFAULT NULL,
  `season_month_end` int(2) DEFAULT NULL,
  `class` tinyint(1) unsigned zerofill DEFAULT NULL,
  `division` tinyint(1) unsigned DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `flightnumber` (`flightnumber`),
  KEY `arr_airport` (`arr_airport`),
  KEY `dep_airport` (`dep_airport`),
  KEY `class` (`class`),
  KEY `division` (`division`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tour_aircraft`;
CREATE TABLE `tour_aircraft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) NOT NULL,
  `aircraft_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `aircraft_id` (`aircraft_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tour_index`;
CREATE TABLE `tour_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `author` varchar(80) DEFAULT NULL,
  `length` varchar(100) DEFAULT NULL,
  `difficulty` varchar(150) DEFAULT NULL,
  `description` text,
  `class` int(2) DEFAULT NULL,
  `detail_info` text,
  `requirements` text,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `submitted` datetime NOT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class` (`class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tour_legs`;
CREATE TABLE `tour_legs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tour_id` int(11) DEFAULT NULL,
  `flight_sim` int(11) DEFAULT NULL,
  `sequence` int(4) DEFAULT NULL,
  `start_icao` varchar(4) DEFAULT NULL,
  `end_icao` varchar(4) DEFAULT NULL,
  `altitude` int(6) DEFAULT NULL,
  `award_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `start_icao` (`start_icao`),
  KEY `tour_id` (`tour_id`),
  KEY `end_icao` (`end_icao`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `usergroup_index`;
CREATE TABLE `usergroup_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `management` int(1) NOT NULL DEFAULT '0',
  `admin_cp` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `usergroup_permissions`;
CREATE TABLE `usergroup_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup_id` int(11) DEFAULT NULL,
  `page` varchar(40) DEFAULT NULL,
  `read` int(1) DEFAULT NULL,
  `write` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `work_airport`;
CREATE TABLE `work_airport` (
  `icao` varchar(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `country` varchar(10) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `alias` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `work_tour`;
CREATE TABLE `work_tour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icao1` varchar(10) DEFAULT NULL,
  `name1` varchar(50) DEFAULT NULL,
  `icao2` varchar(10) DEFAULT NULL,
  `name2` varchar(50) DEFAULT NULL,
  `dist` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 2018-05-09 09:05:22
