-- Host: localhost
-- Generation Time: Nov 09, 2007 at 01:34 PM
-- Server version: 5.0.44
-- PHP Version: 5.2.4_p20070914-pl2-gentoo
-- 
-- Database: `uta_stats`
-- 
DROP DATABASE IF EXISTS `uta_stats`;
CREATE DATABASE `uta_stats` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE uta_stats;

-- --------------------------------------------------------

-- 
-- Table structure for table `ipToCountry`
-- 
-- Creation: Jan 22, 2006 at 04:36 PM
-- Last update: Nov 01, 2007 at 12:40 AM
-- Last check: Mar 03, 2007 at 03:00 AM
-- 

DROP TABLE IF EXISTS `ipToCountry`;
CREATE TABLE IF NOT EXISTS `ipToCountry` (
  `ip_from` double NOT NULL default '0',
  `ip_to` double NOT NULL default '0',
  `country_code2` char(2) NOT NULL default '',
  `country_code3` char(3) NOT NULL default '',
  `country_name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`ip_from`,`ip_to`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_games`
-- 
-- Creation: Jan 22, 2006 at 04:36 PM
-- Last update: Jun 14, 2007 at 08:16 PM
-- Last check: Mar 03, 2007 at 03:00 AM
-- 

DROP TABLE IF EXISTS `uts_games`;
CREATE TABLE IF NOT EXISTS `uts_games` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `gamename` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- AUTO_INCREMENT for table `uts_games`
--
ALTER TABLE `uts_games`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

-- 
-- Dumping data for table `uts_games`
-- 

INSERT INTO `uts_games` VALUES (1, 'Assault', 'Assault'),
(2, 'Assault (insta)', 'Assault (insta)'),
(3, 'Tournament DeathMatch', 'Tournament DeathMatch'),
(4, 'Assault (pro)', 'Assault (pro)'),
(5, 'Assault (insta) (pro)', 'Assault (insta) (pro)'),
(6, 'Capture the Flag', 'Capture the Flag'),
(7, 'Tournament DeathMatch (insta)', 'Tournament DeathMatch (insta)'),
(8, 'Slave Master', 'Slave Master'),
(9, 'Tournament Team Game', 'Tournament Team Game'),
(10, 'Capture the Flag (insta)', 'Capture the Flag (insta)'),
(11, 'Soccer Tournament', 'Soccer Tournament'),
(12, 'Rocket Arena', 'Rocket Arena'),
(13, 'Rocket Arena: All Maps', 'Rocket Arena: All Maps'),
(14, 'Tournament Team Game (pro)', 'Tournament Team Game (pro)'),
(15, 'Tournament Team Game (insta)', 'Tournament Team Game (insta)'),
(16, 'Domination', 'Domination'),
(17, 'Last Man Standing', 'Last Man Standing'),
(18, 'BunnyTrack', 'BunnyTrack'),
(19, 'BunnyTrack (insta)', 'BunnyTrack (insta)'),
(20, 'Rocket Arena (insta)', 'Rocket Arena (insta)');

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_gamestype`
-- 
-- Creation: Jan 22, 2006 at 04:36 PM
-- Last update: Mar 03, 2007 at 03:00 AM
-- Last check: Mar 03, 2007 at 03:00 AM
-- 

DROP TABLE IF EXISTS `uts_gamestype`;
CREATE TABLE IF NOT EXISTS `uts_gamestype` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `serverip` varchar(21) NOT NULL default '',
  `gamename` varchar(100) NOT NULL default '',
  `mutator` varchar(100) NOT NULL default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- AUTO_INCREMENT for table `uts_gamestype`
--
ALTER TABLE `uts_gamestype`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_ip2country`
-- 
-- Creation: Jan 22, 2006 at 04:36 PM
-- Last update: Mar 03, 2007 at 03:00 AM
-- Last check: Mar 03, 2007 at 03:00 AM
-- 

DROP TABLE IF EXISTS `uts_ip2country`;
CREATE TABLE IF NOT EXISTS `uts_ip2country` (
  `ip_from` int(10) unsigned NOT NULL default '0',
  `ip_to` int(10) unsigned NOT NULL default '0',
  `country` char(2) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_killsmatrix`
-- 
-- Creation: Jan 22, 2006 at 04:36 PM
-- Last update: Nov 09, 2007 at 01:15 PM
-- Last check: Nov 09, 2007 at 11:46 AM
-- 

DROP TABLE IF EXISTS `uts_killsmatrix`;
CREATE TABLE IF NOT EXISTS `uts_killsmatrix` (
  `matchid` mediumint(8) unsigned NOT NULL default '0',
  `killer` tinyint(4) NOT NULL default '0',
  `victim` tinyint(4) NOT NULL default '0',
  `kills` tinyint(3) unsigned NOT NULL default '0',
  KEY `matchid` (`matchid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- INDEX for table `uts_killsmatrix`
--
ALTER TABLE `uts_killsmatrix`
  ADD INDEX `matchid` (`matchid`);

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_match`
-- 
-- Creation: Sep 09, 2006 at 12:15 AM
-- Last update: Nov 09, 2007 at 01:15 PM
-- Last check: Nov 09, 2007 at 11:46 AM
-- 

DROP TABLE IF EXISTS `uts_match`;
CREATE TABLE IF NOT EXISTS `uts_match` (
  `id` int(11) NOT NULL auto_increment,
  `time` varchar(14) default NULL,
  `servername` varchar(100) NOT NULL default '',
  `serverip` varchar(21) NOT NULL default '0',
  `gamename` varchar(100) NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `gametime` float NOT NULL default '0',
  `mutators` longtext NOT NULL,
  `insta` tinyint(1) NOT NULL default '0',
  `tournament` varchar(5) NOT NULL default '',
  `teamgame` varchar(5) NOT NULL default '',
  `mapname` varchar(100) NOT NULL default '',
  `mapfile` varchar(100) NOT NULL default '',
  `serverinfo` mediumtext NOT NULL,
  `gameinfo` mediumtext NOT NULL,
  `firstblood` int(10) unsigned NOT NULL default '0',
  `frags` mediumint(5) NOT NULL default '0',
  `deaths` mediumint(5) NOT NULL default '0',
  `kills` mediumint(5) NOT NULL default '0',
  `suicides` mediumint(5) NOT NULL default '0',
  `teamkills` mediumint(5) NOT NULL default '0',
  `assaultid` varchar(10) NOT NULL default '',
  `ass_att` tinyint(1) NOT NULL default '0',
  `ass_win` tinyint(4) NOT NULL default '0',
  `t0` tinyint(1) NOT NULL default '0',
  `t1` tinyint(1) NOT NULL default '0',
  `t2` tinyint(1) NOT NULL default '0',
  `t3` tinyint(1) NOT NULL default '0',
  `t0score` mediumint(5) NOT NULL default '0',
  `t1score` mediumint(5) NOT NULL default '0',
  `t2score` mediumint(5) NOT NULL default '0',
  `t3score` mediumint(5) NOT NULL default '0',
  `friendlyfirescale` tinyint(4) NOT NULL default '0',
  `timedilation` float NOT NULL default '0',
  `startstamp` float NOT NULL default '0',
  `matchtime` float NOT NULL default '0',
  `matchmode` tinyint(3) NOT NULL default '0',
  `matchcode` varchar(8) NOT NULL default '',
  `mapsequence` int(11) NOT NULL default '0',
  `matchlength` tinyint(4) unsigned NOT NULL default '0',
  `mapsleft` tinyint(4) unsigned NOT NULL default '0',
  `teamname0` varchar(40) NOT NULL default '',
  `teamname1` varchar(40) NOT NULL default '',
  `score0` tinyint(4) NOT NULL default '0',
  `score1` tinyint(4) NOT NULL default '0',
  `att_teamsize_avg` tinyint(4) NOT NULL default '0',
  `def_teamsize_avg` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `serverip` (`serverip`),
  KEY `matchcode` (`matchcode`),
  KEY `mapfile` (`mapfile`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- AUTO_INCREMENT for table `uts_match`
--
ALTER TABLE `uts_match`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- INDEX for table `uts_match`
--
ALTER TABLE `uts_match`
  ADD INDEX `matchcode` (`matchcode`);

ALTER TABLE `uts_match`
  ADD INDEX `mapfile` (`mapfile`);

ALTER TABLE `uts_match`
  ADD INDEX `serverip` (`serverip`);

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_pinfo`
-- 
-- Creation: Sep 04, 2006 at 07:10 PM
-- Last update: Nov 09, 2007 at 01:15 PM
-- Last check: Nov 09, 2007 at 11:46 AM
-- 

DROP TABLE IF EXISTS `uts_pinfo`;
CREATE TABLE IF NOT EXISTS `uts_pinfo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `country` char(2) NOT NULL default '',
  `banned` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- AUTO_INCREMENT for table `uts_pinfo`
--
ALTER TABLE `uts_pinfo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- INDEX for table `uts_pinfo`
--
ALTER TABLE `uts_pinfo`
  ADD INDEX `name` (`name`);


-- --------------------------------------------------------

-- 
-- Table structure for table `uts_player`
-- 
-- Creation: Sep 09, 2006 at 12:25 AM
-- Last update: Nov 09, 2007 at 01:15 PM
-- Last check: Nov 09, 2007 at 11:46 AM
-- 

DROP TABLE IF EXISTS `uts_player`;
CREATE TABLE IF NOT EXISTS `uts_player` (
  `id` mediumint(10) NOT NULL auto_increment,
  `matchid` int(11) NOT NULL default '0',
  `insta` tinyint(1) NOT NULL default '0',
  `playerid` tinyint(3) NOT NULL default '0',
  `pid` int(10) unsigned NOT NULL default '0',
  `team` tinyint(2) NOT NULL default '0',
  `isabot` tinyint(1) NOT NULL default '0',
  `country` char(2) NOT NULL default '',
  `ip` int(10) unsigned NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `gametime` float NOT NULL default '0',
  `gamescore` smallint(5) unsigned NOT NULL default '0',
  `lowping` smallint(5) unsigned default '0',
  `highping` smallint(5) unsigned default '0',
  `avgping` smallint(5) unsigned default '0',
  `frags` smallint(5) unsigned NOT NULL default '0',
  `deaths` smallint(5) unsigned NOT NULL default '0',
  `kills` smallint(5) unsigned NOT NULL default '0',
  `suicides` smallint(5) unsigned NOT NULL default '0',
  `teamkills` smallint(5) unsigned NOT NULL default '0',
  `eff` float NOT NULL default '0',
  `accuracy` float NOT NULL default '0',
  `ttl` float NOT NULL default '0',
  `headshots` tinyint(3) unsigned NOT NULL default '0',
  `flag_taken` smallint(5) unsigned NOT NULL default '0',
  `flag_dropped` smallint(5) unsigned NOT NULL default '0',
  `flag_return` smallint(5) unsigned NOT NULL default '0',
  `flag_capture` tinyint(3) unsigned NOT NULL default '0',
  `flag_cover` smallint(5) unsigned NOT NULL default '0',
  `flag_seal` smallint(5) unsigned NOT NULL default '0',
  `flag_assist` smallint(5) unsigned NOT NULL default '0',
  `flag_kill` mediumint(5) unsigned NOT NULL default '0',
  `flag_pickedup` smallint(5) unsigned NOT NULL default '0',
  `dom_cp` smallint(5) unsigned NOT NULL default '0',
  `ass_obj` smallint(5) unsigned NOT NULL default '0',
  `ass_h_launch` tinyint(4) unsigned NOT NULL default '0',
  `ass_r_launch` tinyint(4) unsigned NOT NULL default '0',
  `ass_h_launched` tinyint(3) unsigned NOT NULL default '0',
  `ass_r_launched` tinyint(3) unsigned NOT NULL default '0',
  `ass_h_jump` tinyint(4) unsigned NOT NULL default '0',
  `ass_assist` tinyint(4) unsigned NOT NULL default '0',
  `ass_suicide_coop` tinyint(4) unsigned NOT NULL default '0',
  `spree_double` smallint(5) unsigned NOT NULL default '0',
  `spree_triple` smallint(5) unsigned NOT NULL default '0',
  `spree_multi` smallint(5) unsigned NOT NULL default '0',
  `spree_mega` tinyint(3) unsigned NOT NULL default '0',
  `spree_ultra` tinyint(3) unsigned NOT NULL default '0',
  `spree_monster` tinyint(3) unsigned NOT NULL default '0',
  `spree_kill` smallint(5) unsigned NOT NULL default '0',
  `spree_rampage` smallint(5) unsigned NOT NULL default '0',
  `spree_dom` tinyint(3) unsigned NOT NULL default '0',
  `spree_uns` tinyint(3) unsigned NOT NULL default '0',
  `spree_god` smallint(5) unsigned NOT NULL default '0',
  `pu_pads` tinyint(3) unsigned NOT NULL default '0',
  `pu_armour` tinyint(3) unsigned NOT NULL default '0',
  `pu_keg` tinyint(3) unsigned NOT NULL default '0',
  `pu_invis` tinyint(3) unsigned NOT NULL default '0',
  `pu_belt` tinyint(3) unsigned NOT NULL default '0',
  `pu_amp` tinyint(3) unsigned NOT NULL default '0',
  `rank` float NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `matchid` (`matchid`,`team`),
  KEY `pid` (`pid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- AUTO_INCREMENT for table `uts_player`
--
ALTER TABLE `uts_player`
  MODIFY `id` mediumint(10) NOT NULL AUTO_INCREMENT;

--
-- INDEX for table `uts_player`
--
ALTER TABLE `uts_player`
  ADD INDEX `name` (`name`);

ALTER TABLE `uts_player`
  ADD INDEX `match_team` (`matchid`,`team`);

ALTER TABLE `uts_player`
  ADD INDEX `pid` (`pid`);

ALTER TABLE `uts_player`
  ADD INDEX `gid` (`gid`);

ALTER TABLE `uts_player`
  ADD INDEX `playerid` (`playerid`);

ALTER TABLE `uts_player`
  ADD INDEX `ip` (`ip`);

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_rank`
-- 
-- Creation: Sep 08, 2006 at 10:39 PM
-- Last update: Nov 09, 2007 at 01:15 PM
-- Last check: Nov 09, 2007 at 11:46 AM
-- 

DROP TABLE IF EXISTS `uts_rank`;
CREATE TABLE IF NOT EXISTS `uts_rank` (
  `id` mediumint(10) NOT NULL auto_increment,
  `time` float unsigned NOT NULL default '0',
  `pid` int(10) unsigned NOT NULL default '0',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `rank` float NOT NULL default '0',
  `prevrank` float NOT NULL default '0',
  `matches` mediumint(5) NOT NULL default '0',
  `year` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `name` (`pid`,`gid`),
  KEY `rank` (`rank`),
  KEY `gamename` (`gid`,`rank`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- AUTO_INCREMENT for table `uts_rank`
--
ALTER TABLE `uts_rank`
  MODIFY `id` mediumint(10) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_smartass_objs`
-- 
-- Creation: Jan 22, 2006 at 04:38 PM
-- Last update: Nov 04, 2007 at 01:46 AM
-- Last check: Mar 03, 2007 at 03:02 AM
-- 

DROP TABLE IF EXISTS `uts_smartass_objs`;
CREATE TABLE IF NOT EXISTS `uts_smartass_objs` (
  `id` mediumint(10) NOT NULL auto_increment,
  `mapfile` varchar(50) NOT NULL default '',
  `objnum` tinyint(4) NOT NULL default '0',
  `objname` varchar(50) default NULL,
  `objmsg` varchar(50) default NULL,
  `defensepriority` mediumint(5) NOT NULL default '0',
  `defensetime` tinyint(4) NOT NULL default '0',
  `rating` float NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `matchid` (`mapfile`,`objnum`,`rating`)
) ENGINE=MyISAM AUTO_INCREMENT=878 DEFAULT CHARSET=latin1 AUTO_INCREMENT=878 ;

--
-- AUTO_INCREMENT for table `uts_smartass_objs`
--
ALTER TABLE `uts_smartass_objs`
  MODIFY `id` mediumint(10) NOT NULL AUTO_INCREMENT;

-- 
-- Dumping data for table `uts_smartass_objs`
-- 

INSERT INTO `uts_smartass_objs` VALUES (1, 'AS-Riverbed]l[AL.unr', 0, 'Compressor', 'was destroyed!', 90, 10, 1),
(2, 'AS-Riverbed]l[AL.unr', 1, 'Outpost', 'breached!', 100, 10, 0.2),
(3, 'AS-Riverbed]l[AL.unr', 2, 'Tunnels', 'entered!', 80, 10, 1.5),
(4, 'AS-Riverbed]l[AL.unr', 3, 'Charge', 'placed!', 60, 10, 1.5),
(5, 'AS-Riverbed]l[AL.unr', 4, 'Final base', 'was destroyed!', 50, 11, 1),
(6, 'AS-Riverbed]l[AL.unr', 5, 'Left Cavern Entrace', 'was destroyed!', 70, 10, 0.8),
(7, 'AS-Riverbed]l[AL.unr', 6, 'Right Cavern Entrace', 'was destroyed!', 70, 10, 0.8),
(8, 'AS-HiSpeed.unr', 0, 'Control cabin', 'has been taken over!!', 0, 6, 1),
(9, 'AS-HiSpeed.unr', 1, 'CAR 3', 'is about to be breached!!', 4, 7, 0.8),
(10, 'AS-HiSpeed.unr', 2, 'CAR 2', 'is about to be breached!!', 3, 7, 0.8),
(11, 'AS-HiSpeed.unr', 3, 'CAR 1', 'is about to breached!!', 2, 7, 0.8),
(12, 'AS-HiSpeed.unr', 4, 'control cabin access switch', 'has been activated!!', 1, 7, 1),
(13, 'AS-Siege][.unr', 0, 'The gatehouse door', 'is now open!', 200, 1, 1),
(14, 'AS-Siege][.unr', 1, 'Tnega', 'is a Love god!', 255, 5, 0.1),
(15, 'AS-Siege][.unr', 2, 'The doors of the 2nd tower', 'are now open!', 172, 5, 0.8),
(16, 'AS-Siege][.unr', 3, 'The main tower doors', 'are now open!', 142, 5, 1),
(17, 'AS-Siege][.unr', 4, 'The Tower', 'is now under enemy control!', 97, 10, 1),
(18, 'AS-Siege][.unr', 5, 'The main door', 'is now open!', 241, 5, 0.5),
(19, 'AS-AutoRIP.unr', 0, 'Despatch Doors', 'have been opened!', 15, 7, 1),
(20, 'AS-AutoRIP.unr', 1, 'Bomb', 'was planted!', 5, 7, 1),
(21, 'AS-AutoRIP.unr', 2, 'Development Area', 'is accessible!', 10, 7, 1.5),
(22, 'AS-AutoRIP.unr', 3, 'Defence Door Control', 'was destroyed!', 12, 10, 0.1),
(23, 'AS-Bridge.unr', 0, 'Second assault entrance', 'has been opened!', 80, 10, 1.5),
(24, 'AS-Bridge.unr', 1, 'Explosive charge 1', 'has been placed.', 50, 10, 0.5),
(25, 'AS-Bridge.unr', 10, 'The bridge', 'has been reached!', 55, 10, 0.2),
(26, 'AS-Bridge.unr', 2, 'Explosive charge 4', 'has been placed.', 50, 10, 0.5),
(27, 'AS-Bridge.unr', 3, 'Explosive charge 2', 'has been placed.', 50, 10, 0.5),
(28, 'AS-Bridge.unr', 4, 'Explosive charge 3', 'has been placed.', 50, 10, 0.5),
(29, 'AS-Bridge.unr', 5, 'Detonator', 'has been used!', 40, 16, 1),
(30, 'AS-Bridge.unr', 6, 'Main gates', 'were destroyed!', 100, 10, 0.2),
(31, 'AS-Bridge.unr', 7, 'The base', 'has been entered!', 90, 10, 0.2),
(32, 'AS-Bridge.unr', 8, 'Final assault entrance', 'has been discovered!', 60, 10, 1.5),
(33, 'AS-Bridge.unr', 9, 'Door pressure controller', 'was manipulated!', 70, 10, 1),
(34, 'AS-Ballistic.unr', 0, 'Main Gate', 'was targeted, Strike on way!', 25, 10, 1),
(35, 'AS-Ballistic.unr', 1, 'Generator', 'was destroyed!', 20, 10, 1.5),
(36, 'AS-Ballistic.unr', 2, 'Nuclear strike', 'was destroyed!', 10, 10, 1),
(37, 'AS-Ballistic.unr', 3, 'Laser Field', 'Control Accessed.....', 30, 10, 0.1),
(38, 'AS-Ballistic.unr', 4, 'Warhead Loader', 'sequence initiated!', 10, 10, 1.5),
(39, 'AS-TheScarabv2_beta.unr', 0, 'Hanger Doors', 'opening!', 25, 10, 1),
(40, 'AS-TheScarabv2_beta.unr', 1, 'Enemy Base', 'entered!', 35, 10, 1),
(41, 'AS-TheScarabv2_beta.unr', 2, 'Ships Engines', 'activated!', 15, 10, 1),
(42, 'AS-TheScarabv2_beta.unr', 3, 'Tower Door', 'unlocked!', 0, 10, 1),
(43, 'AS-TheScarabv2_beta.unr', 4, 'The Scarab', 'has been stolen!', 5, 10, 1),
(44, 'AS-TheScarabv2_beta.unr', 5, 'Shield Control', 'has been activated!', 40, 10, 1),
(45, 'AS-Golgotha][AL.unr', 0, 'Gates', 'opening!', 0, 10, 1),
(46, 'AS-Golgotha][AL.unr', 1, 'Huron Sword', 'stolen!', 0, 10, 1),
(47, 'AS-Golgotha][AL.unr', 2, 'Lifts', 'are reached!', 0, 10, 1.5),
(48, 'AS-Golgotha][AL.unr', 3, 'Top Entrance', 'open!', 0, 10, 0.5),
(49, 'AS-Golgotha][AL.unr', 4, 'Temple', 'breached!', 0, 10, 1),
(50, 'AS-Golgotha][AL.unr', 5, 'Holy Cross', 'was destroyed!', 0, 10, 1),
(51, 'AS-Asthenosphere.unr', 0, 'Docking Bay doors', 'have been opened!', 100, 10, 0.1),
(52, 'AS-Asthenosphere.unr', 1, 'Cooling fan #1', 'has been destroyed!', 75, 10, 1),
(53, 'AS-Asthenosphere.unr', 2, 'Escape pod', 'has been hijacked!', 0, 10, 1),
(54, 'AS-Asthenosphere.unr', 3, 'Reactor room', 'has been breached!', 90, 10, 1),
(55, 'AS-Asthenosphere.unr', 4, 'Air vents', 'have been breached!', 50, 10, 1.5),
(56, 'AS-Asthenosphere.unr', 5, 'Cooling fan #2', 'has been destroyed!', 75, 10, 1),
(57, 'AS-Asthenosphere.unr', 7, 'Observation Lounge access', 'has been granted!', 25, 10, 0.8),
(58, 'AS-Rook.unr', 0, 'Chain 2', '', 4, 4, 0.5),
(59, 'AS-Rook.unr', 1, 'Chain 1', '', 4, 4, 0.5),
(60, 'AS-Rook.unr', 2, 'Escape!', '', 1, 4, 1),
(61, 'AS-Rook.unr', 3, 'The Main Doors', 'are now open!', 3, 4, 1),
(62, 'AS-Rook.unr', 4, 'The Library', 'is now Open!', 5, 4, 1),
(63, 'AS-GolgothaAL.unr', 0, 'Temple', 'Doors Now Opening!', 92, 10, 1),
(64, 'AS-GolgothaAL.unr', 1, 'Undead', 'Temple Breached!', 0, 10, 1),
(65, 'AS-GolgothaAL.unr', 2, 'HuronsPath', 'breached!', 71, 10, 1.5),
(66, 'AS-GolgothaAL.unr', 3, 'Siegetower', 'breached!', 65, 10, 1.5),
(67, 'AS-GolgothaAL.unr', 4, 'Heart', 'was destroyed!', 20, 12, 1),
(68, 'AS-GolgothaAL.unr', 5, 'Dungeon', 'Doors Opening!', 50, 10, 1),
(69, 'AS-GolgothaAL.unr', 6, 'Attackers', 'Inside Dungeon!', 0, 10, 1),
(70, 'AS-GolgothaAL.unr', 7, 'Hurons', 'temple entered by attackers!', 121, 10, 0.8),
(71, 'AS-Frigate.unr', 0, 'The Ship', 'has been entered!', 50, 6, 0.5),
(72, 'AS-Frigate.unr', 1, 'Missiles', 'were launched!', 10, 6, 1),
(73, 'AS-Frigate.unr', 2, 'The hydraulic compressor', 'was destroyed!', 25, 6, 1),
(74, 'AS-Overlord.unr', 0, 'Main Gun Control', 'was destroyed!', 0, 7, 1),
(75, 'AS-Overlord.unr', 1, 'The Boiler Room', 'has been breached.', 1, 7, 1),
(76, 'AS-Overlord.unr', 2, 'The Beachhead', 'has been breached.', 2, 10, 0.5),
(77, 'AS-Desolate][.unr', 0, 'Lower Level', 'Access Granted', 4, 10, 1),
(78, 'AS-Desolate][.unr', 1, 'Generator Room', 'has been reached', 3, 10, 1),
(79, 'AS-Desolate][.unr', 2, 'Guard Room', 'opened', 2, 10, 1.2),
(80, 'AS-Desolate][.unr', 3, 'East Bridge', 'has been crossed', 1, 10, 1),
(81, 'AS-Desolate][.unr', 4, 'West Bridge', 'has been crossed', 1, 10, 1),
(82, 'AS-Desolate][.unr', 5, 'Guns', 'Disabled!!', 0, 10, 1),
(83, 'AS-Desolate][.unr', 6, 'Outer Base', 'Has Been Breached!', 5, 10, 1),
(84, 'AS-Desolate][.unr', 7, 'Explosives', 'Have Been Placed!', 6, 10, 0.8),
(85, 'AS-Submarinebase][.unr', 0, 'Attackers', 'entered the base!', 0, 10, 0.1),
(86, 'AS-Submarinebase][.unr', 1, 'Gate', 'is open!', 0, 10, 1),
(87, 'AS-Submarinebase][.unr', 2, 'Docks', 'are reached!', 0, 10, 1),
(88, 'AS-Submarinebase][.unr', 3, 'Panel', 'was destroyed!', 0, 10, 1),
(89, 'AS-Submarinebase][.unr', 4, 'Submarine', 'entered!', 0, 10, 1.5),
(90, 'AS-Submarinebase][.unr', 5, 'Ventilation', 'access granted!', 0, 10, 1),
(91, 'AS-Guardia.unr', 0, 'Fuse', 'has been ignited!', 4, 5, 0.1),
(92, 'AS-Guardia.unr', 1, 'Garage Door', 'was breached!', 1, 5, 1.5),
(93, 'AS-Guardia.unr', 2, 'Tank Turret', 'was destroyed!', 0, 5, 1),
(94, 'AS-Guardia.unr', 3, 'Lava Bridge', 'has been crossed!', 2, 5, 1),
(95, 'AS-Guardia.unr', 4, 'Cavern', 'has been breached!', 3, 5, 0.8),
(96, 'AS-ColderSteel.unr', 0, 'Laser fence', 'deactivated', 90, 15, 1),
(97, 'AS-ColderSteel.unr', 1, 'Hanger door release 1', 'engaged', 80, 15, 1),
(98, 'AS-ColderSteel.unr', 2, 'Hanger door release 2', 'engaged', 80, 15, 1),
(99, 'AS-ColderSteel.unr', 3, 'The crew quarters', 'are now accessable', 70, 15, 1),
(100, 'AS-ColderSteel.unr', 4, 'The Snowfield', 'has been reached', 60, 15, 1.5),
(101, 'AS-ColderSteel.unr', 5, 'C1 Reactor', 'shutdown.', 50, 15, 1),
(102, 'AS-ColderSteel.unr', 6, 'C2 Reactor', 'shutdown.', 50, 15, 1),
(103, 'AS-ColderSteel.unr', 7, 'C3 Reactor', 'shutdown.', 50, 15, 1),
(104, 'AS-ColderSteel.unr', 8, 'Glass', 'is now broken.', 99, 10, 0.8),
(105, 'AS-ColderSteel.unr', 9, 'Hydraulic Pump', 'deactivated', 98, 10, 1),
(106, 'AS-Desertstorm.unr', 0, 'Scud', 'Missiles Fired!', 80, 10, 1),
(107, 'AS-Desertstorm.unr', 1, 'Bunker', 'entered!', 100, 10, 0.5),
(108, 'AS-Desertstorm.unr', 2, 'Samsite', 'Disabled!', 60, 10, 1.5),
(109, 'AS-Desertstorm.unr', 3, 'Underground', 'Passage Breached!', 50, 10, 1),
(110, 'AS-Desertstorm.unr', 4, 'Generator', 'was destroyed!', 12, 10, 1),
(111, 'AS-SnowDunes.unr', 0, 'Ship', 'Captured', 0, 10, 1),
(112, 'AS-SnowDunes.unr', 1, 'Second Level Doors', 'Unlocked!', 4, 10, 1),
(113, 'AS-SnowDunes.unr', 2, 'Tower Door', 'Opening', 2, 10, 1),
(114, 'AS-SnowDunes.unr', 3, 'Ship Dock Lasers', 'Deactivated!!', 3, 10, 1),
(115, 'AS-SnowDunes.unr', 4, 'Radar', 'Disabled!', 5, 10, 1),
(116, 'AS-SnowDunes.unr', 5, 'Energy Cell 1', 'was destroyed!', 1, 10, 1),
(117, 'AS-SnowDunes.unr', 6, 'Energy Cell 2', 'was destroyed!', 1, 10, 1),
(118, 'AS-VampireSE_Beta5.unr', 0, 'The Switch', 'was pushed!', 253, 10, 1),
(119, 'AS-VampireSE_Beta5.unr', 1, 'Water Wheel', 'started rotating! Hatch will open in a few seconds', 254, 10, 1),
(120, 'AS-VampireSE_Beta5.unr', 2, 'Barricade', 'was destroyed!', 255, 10, 1),
(121, 'AS-VampireSE_Beta5.unr', 3, 'The Chain', 'was destroyed!', 251, 10, 1),
(122, 'AS-VampireSE_Beta5.unr', 4, 'Door Lock', 'was destroyed!', 252, 10, 1),
(123, 'AS-Mazon.unr', 0, 'Chain 1', 'was destroyed!', 100, 10, 0.8),
(124, 'AS-Mazon.unr', 1, 'Crystal', 'was destroyed!', 0, 10, 1),
(125, 'AS-Mazon.unr', 2, 'Reactor Room doors', 'are opened!', 25, 10, 1.5),
(126, 'AS-Mazon.unr', 3, 'The front doors', 'are opened!', 50, 10, 0.8),
(127, 'AS-Mazon.unr', 4, 'Chain 2', 'was destroyed!', 75, 10, 0.8),
(128, 'AS-OceanFloor.unr', 0, 'Terminal 1', 'has been destroyed!', 0, 6, 1),
(129, 'AS-OceanFloor.unr', 1, 'Terminal 3', 'has been destroyed!!', 0, 6, 1),
(130, 'AS-OceanFloor.unr', 2, 'Terminal 2', 'has been destroyed!!', 0, 6, 1),
(131, 'AS-OceanFloor.unr', 3, 'Terminal 4', 'has been destroyed!', 0, 6, 1),
(132, 'AS-RocketCommandSE.unr', 0, 'Fort Entrance', 'has been breached!', 100, 14, 1),
(133, 'AS-RocketCommandSE.unr', 1, 'First gate', 'is opening!', 90, 14, 1),
(134, 'AS-RocketCommandSE.unr', 10, 'Rocket thrust controller', 'was destroyed!', 5, 14, 1),
(135, 'AS-RocketCommandSE.unr', 11, 'Rocket control station', 'has been breached!', 20, 14, 1),
(136, 'AS-RocketCommandSE.unr', 12, 'The Rocket Platform', 'has been reached!', 15, 14, 1),
(137, 'AS-RocketCommandSE.unr', 13, 'Base entrance terminal', 'was destroyed!', 70, 14, 1),
(138, 'AS-RocketCommandSE.unr', 2, 'Second gate', 'will open in 10 seconds!', 90, 14, 1),
(139, 'AS-RocketCommandSE.unr', 3, 'Anti-air base 1', 'has been scrambled!', 80, 14, 1),
(140, 'AS-RocketCommandSE.unr', 4, 'Anti-air base 2', 'has been scrambled!', 80, 14, 1),
(141, 'AS-RocketCommandSE.unr', 5, 'The sewers', 'have been breached!', 80, 14, 1),
(142, 'AS-RocketCommandSE.unr', 6, 'The sliding doors', 'are opening!', 50, 14, 1),
(143, 'AS-RocketCommandSE.unr', 7, 'Depot door', 'panel hacked!', 30, 14, 1),
(144, 'AS-RocketCommandSE.unr', 8, 'Ventilation shaft spawnpoints', 'have been opened!', 40, 14, 1),
(145, 'AS-RocketCommandSE.unr', 9, 'Rocket entrance', 'has been breached!', 10, 14, 1),
(146, 'AS-AsthenosphereAL.unr', 0, 'Docking Bay doors', 'have been opened!', 100, 10, 0.1),
(147, 'AS-AsthenosphereAL.unr', 1, 'Cooling fan #1', 'has been destroyed!', 75, 10, 1),
(148, 'AS-AsthenosphereAL.unr', 2, 'Escape pod', 'has been hijacked!', 0, 10, 1),
(149, 'AS-AsthenosphereAL.unr', 3, 'Reactor room', 'has been breached!', 90, 10, 1),
(150, 'AS-AsthenosphereAL.unr', 4, 'Air vents', 'have been breached!', 50, 10, 1.2),
(151, 'AS-AsthenosphereAL.unr', 5, 'Cooling fan #2', 'has been destroyed!', 75, 10, 1),
(152, 'AS-AsthenosphereAL.unr', 6, 'Observation Lounge access', 'has been granted!', 25, 10, 1.2),
(153, 'AS-RiverbedAL.unr', 0, 'The cavern passage', 'has been breached!', 100, 9, 0.8),
(154, 'AS-RiverbedAL.unr', 1, 'Main Computer', 'was destroyed!', 10, 9, 1),
(155, 'AS-RiverbedAL.unr', 2, 'Control Panel', 'was destroyed, Bridge is open!', 153, 5, 1),
(156, 'AS-RiverbedAL.unr', 3, 'The Main Entrance', 'has been reached!', 50, 9, 1),
(157, 'AS-BioAssaultSE_preview.unr', 0, 'Main Gate', 'Lock has been destroyed.', 0, 10, 1),
(158, 'AS-BioAssaultSE_preview.unr', 1, 'Bridge Entrance', 'has been breached!', 0, 10, 1),
(159, 'AS-BioAssaultSE_preview.unr', 2, 'Second Gate', 'Unlocked!', 0, 10, 1),
(160, 'AS-BioAssaultSE_preview.unr', 3, 'The Bridge', 'has been crossed!', 0, 10, 1),
(161, 'AS-BioAssaultSE_preview.unr', 4, 'Science corridor', 'has been reached.', 0, 10, 1),
(162, 'AS-BioAssaultSE_preview.unr', 5, 'Shooting Range', 'is Secure!', 0, 10, 1),
(163, 'AS-BioAssaultSE_preview.unr', 6, 'The Tunnel', 'Is Secure!', 0, 10, 1),
(164, 'AS-BioAssaultSE_preview.unr', 7, 'Bio-chamber', 'has been reached!', 0, 10, 1),
(165, 'AS-BioAssaultSE_preview.unr', 8, 'Bio-chamber', 'has become unstable.', 0, 11, 1),
(166, 'AS-BioAssaultSE_preview.unr', 9, 'Escape!', '', 0, 12, 1),
(167, 'AS-LavaFort][.unr', 0, 'Fuse 1', 'has been destroyed!', 60, 10, 0.8),
(168, 'AS-LavaFort][.unr', 1, 'Lava energy generator', 'was destroyed!', 20, 10, 1),
(169, 'AS-LavaFort][.unr', 2, 'The entrance cave', 'has been breached!', 80, 10, 0.5),
(170, 'AS-LavaFort][.unr', 3, 'The lift', 'has been passed!', 40, 10, 1),
(171, 'AS-LavaFort][.unr', 4, 'Fuse 2', 'has been destroyed!', 60, 10, 0.8),
(172, 'AS-LavaFort][.unr', 5, 'The fort', 'has been entered!', 50, 10, 1),
(173, 'AS-LavaFort][.unr', 6, 'The lava cave', 'has been entered!', 75, 10, 1),
(174, 'AS-OceanFloorAL.unr', 0, 'Terminal 3', 'has been disabled!', 0, 6, 1),
(175, 'AS-OceanFloorAL.unr', 1, 'Terminal 2', 'has been disabled!', 0, 6, 1),
(176, 'AS-OceanFloorAL.unr', 2, 'Terminal 4', 'has been disabled!', 0, 6, 1),
(177, 'AS-OceanFloorAL.unr', 3, 'Terminal 1', 'has been disabled!', 0, 6, 1),
(178, 'AS-ArrivalV2.unr', 0, 'Tractorbeam', '', 90, 10, 1),
(179, 'AS-ArrivalV2.unr', 1, 'Security Control Panel', '', 50, 10, 1),
(180, 'AS-ArrivalV2.unr', 2, 'Bridge', '', 0, 10, 1),
(181, 'AS-ArrivalV2.unr', 3, 'Force field generator', 'was destroyed!', 10, 10, 1),
(182, 'AS-GuardiaXE.unr', 0, 'Fuse', 'has been ignited!', 4, 5, 1),
(183, 'AS-GuardiaXE.unr', 1, 'Lava Bridge', 'has been crossed!', 2, 5, 1),
(184, 'AS-GuardiaXE.unr', 2, 'Cavern', 'has been breached!', 3, 5, 1),
(185, 'AS-GuardiaXE.unr', 3, 'Garage Door', 'was breached!', 1, 5, 1),
(186, 'AS-GuardiaXE.unr', 4, 'Tank Turret', 'was destroyed!', 0, 5, 1),
(187, 'AS-HWF_CE_finalbeta.unr', 0, 'Barracks Doors', 'Opened!', 1, 10, 1),
(188, 'AS-HWF_CE_finalbeta.unr', 1, 'Front Gates', 'Are Open!', 1, 10, 1),
(189, 'AS-HWF_CE_finalbeta.unr', 2, 'Control Panel', 'Hacked!', 1, 10, 1),
(190, 'AS-HWF_CE_finalbeta.unr', 3, 'Atomic bomb', 'was stolen!', 1, 10, 1),
(191, 'AS-HWF_CE_finalbeta.unr', 4, 'Main Gates', 'Are Open!', 0, 10, 1),
(192, 'AS-HWF_CE_finalbeta.unr', 5, 'Right Hallway', 'Is Secure!', 0, 10, 1),
(193, 'AS-HWF_CE_finalbeta.unr', 6, 'Enginering Room', 'Entered!', 0, 10, 1),
(194, 'AS-HWF_CE_finalbeta.unr', 7, 'Left Hallway', 'Is Secured', 0, 10, 1),
(195, 'AS-Hellraiser-[a-009].unr', 0, 'The Fortress', 'was breached!', 0, 0, 1),
(196, 'AS-Hellraiser-[a-009].unr', 1, 'The lament configuration puzzlebox', 'was taken!', 0, 0, 1),
(197, 'AS-Hellraiser-[a-009].unr', 2, 'Lament door lock', 'was released!', 30, 0, 1),
(198, 'AS-Hellraiser-[a-009].unr', 3, 'The higher grounds', 'were reached!', 0, 0, 1),
(199, 'AS-Hellraiser-[a-009].unr', 5, 'Puzzlebox', 'was placed !', 0, 0, 1),
(200, 'AS-Hellraiser-[a-009].unr', 6, 'Chain', 'was destroyed!', 0, 0, 1),
(201, 'AS-Hellraiser-[a-009].unr', 7, 'Outside', 'is reached!', 0, 0, 1),
(202, 'AS-GuardiaAL.unr', 0, 'Fuse', 'has been ignited!', 4, 5, 0.1),
(203, 'AS-GuardiaAL.unr', 1, 'Garage Door', 'was breached!', 1, 5, 1),
(204, 'AS-GuardiaAL.unr', 2, 'Tank Turret', 'was destroyed!', 0, 5, 1),
(205, 'AS-GuardiaAL.unr', 3, 'Bridge', 'has been crossed!', 2, 5, 1),
(206, 'AS-GuardiaAL.unr', 4, 'Cavern', 'has been breached!', 3, 5, 1),
(207, 'AS-SaqqaraLE_beta.unr', 0, 'Green chamber switch', 'has been activated!', 2, 3, 1),
(208, 'AS-SaqqaraLE_beta.unr', 1, 'Yellow chamber switch', 'has been activated!', 2, 3, 1),
(209, 'AS-SaqqaraLE_beta.unr', 2, 'Red chamber switch', 'has been activated!', 1, 7, 1),
(210, 'AS-SaqqaraLE_beta.unr', 3, 'Blue chamber switch', 'has been activated!', 1, 7, 1),
(211, 'AS-SaqqaraLE_beta.unr', 4, 'Waterfall', 'was breached!', 0, 9, 1),
(212, 'AS-SaqqaraLE_beta.unr', 5, 'Scarab Doors', 'has been breached!', 0, 7, 1),
(213, 'AS-SaqqaraLE_beta.unr', 6, 'Pyramid', 'has been breached!', 2, 3, 1),
(214, 'AS-SaqqaraLE_beta.unr', 7, 'Waterfall', 'was breached!', 0, 9, 1),
(215, 'AS-LaserV3.unr', 0, 'Terminal', 'codes entered!', 9, 20, 1),
(216, 'AS-LaserV3.unr', 1, 'Power node', 'was destroyed!', 8, 20, 1),
(217, 'AS-LaserV3.unr', 2, 'Cave', 'was entered!', 2, 20, 1),
(218, 'AS-LaserV3.unr', 3, 'Code book', 'was found!', 10, 20, 1),
(219, 'AS-LaserV3.unr', 4, 'Gate control', 'overridden!', 3, 20, 1),
(220, 'AS-LaserV3.unr', 5, 'Turbo lift', 'enabled!', 7, 20, 1),
(221, 'AS-LaserV3.unr', 7, 'Circuit boards', 'were destroyed!', 1, 10, 1),
(222, 'AS-OrbitalCE.unr', 0, 'Blast Door Fuse 1', 'was destroyed!', 220, 10, 1),
(223, 'AS-OrbitalCE.unr', 1, 'Reactor Control 1', 'was de-activated!', 150, 10, 1),
(224, 'AS-OrbitalCE.unr', 2, 'Reactor Control 2', 'was de-activated!', 150, 10, 1),
(225, 'AS-OrbitalCE.unr', 3, 'Reactor Control 3', 'was de-activated!', 150, 10, 1),
(226, 'AS-OrbitalCE.unr', 4, 'Reactor Control 4', 'was de-activated!', 150, 10, 1),
(227, 'AS-OrbitalCE.unr', 5, 'Blast Door Fuse 2', 'was destroyed!', 220, 10, 1),
(228, 'AS-OrbitalCE.unr', 6, 'centreblock1', '', 180, 10, 1),
(229, 'AS-OrbitalCE.unr', 7, 'centreblock1', '', 160, 10, 1),
(230, 'AS-OrbitalCE.unr', 8, 'centreblock1', '', 120, 10, 1),
(231, 'AS-OrbitalCE.unr', 9, 'firstfort', '', 240, 10, 1),
(232, 'AS-SaturdayNight.unr', 0, 'The Bridge', 'have been taken!!!', 0, 10, 1),
(233, 'AS-SaturdayNight.unr', 1, 'Left Guard Tower', 'have been overrunned!!!', 10, 10, 1),
(234, 'AS-SaturdayNight.unr', 2, 'Right Guard Tower', 'have been overunned!!!', 11, 10, 1),
(235, 'AS-SaturdayNight.unr', 3, 'The Castle', 'Have been entered!!!', 9, 10, 1),
(236, 'AS-SaturdayNight.unr', 4, 'The Gold', 'have been stolen!!!', 0, 10, 1),
(237, 'AS-SaturdayNight.unr', 5, 'The Vault', 'have been opened!!!', 8, 10, 1),
(238, 'AS-SaturdayNight.unr', 6, 'The Aimboter & Radaruser', 'have been executed!!', 0, 10, 1),
(239, 'AS-IndefiniteAL.unr', 2, 'The attackers', 'have entered the unknown.', 255, 10, 1),
(240, 'AS-IndefiniteAL.unr', 3, 'The bridge', 'was taken!', 202, 10, 1),
(241, 'AS-IndefiniteAL.unr', 5, 'Security lasers', 'disabled.', 195, 15, 1),
(242, 'AS-IndefiniteAL.unr', 6, 'The attackers have', 'jumped off the waterfall!', 170, 10, 1),
(243, 'AS-IndefiniteAL.unr', 7, 'Dynamite', 'was found!', 45, 10, 1),
(244, 'AS-IndefiniteAL.unr', 8, 'Explosive charge', 'was set!', 20, 15, 1),
(245, 'AS-IndefiniteAL.unr', 9, 'Indefinite', 'was destroyed!', 0, 20, 1),
(246, 'AS-GekokujouAL][.unr', 0, 'The first structure''s rear entrance', 'was opened!', 4, 10, 1),
(247, 'AS-GekokujouAL][.unr', 1, 'The door into the castle tower', 'was opened!', 2, 10, 1),
(248, 'AS-GekokujouAL][.unr', 2, 'The lower levels', 'have been breached!', 1, 10, 1),
(249, 'AS-GekokujouAL][.unr', 3, 'Treasure', 'was stolen!', 0, 10, 1),
(250, 'AS-GekokujouAL][.unr', 4, 'The second structure', 'has been breached!', 3, 10, 1),
(251, 'AS-GekokujouAL][.unr', 5, 'The first structure', 'has been breached!', 3, 10, 1),
(252, 'AS-GekokujouAL][.unr', 6, 'The main door', 'has been opened!', 0, 10, 1),
(253, 'AS-SiegeXtrem.unr', 0, 'Primary Button', 'reached!', 200, 8, 1),
(254, 'AS-Sacrifice.unr', 0, 'Attackers', 'are attacking !', 10, 8, 1),
(255, 'AS-Sacrifice.unr', 1, 'Trophy has been', 'taken !', 7, 8, 1),
(256, 'AS-Sacrifice.unr', 2, 'Attackers', 'are breaking in the trophy room', 8, 8, 1),
(257, 'AS-Sacrifice.unr', 7, 'car', 'is lost..(wtf?)', 0, 8, 1),
(258, 'AS-The_Dungeon][.unr', 0, 'The Portal', 'has been entered!', 0, 10, 1),
(259, 'AS-The_Dungeon][.unr', 1, 'The castle', 'has been entered!', 0, 10, 1),
(260, 'AS-The_Dungeon][.unr', 2, 'The Crypt', 'has been reached!', 0, 10, 1),
(261, 'AS-The_Dungeon][.unr', 3, 'The Attackers', 'have reached the graveyard!', 0, 10, 1),
(262, 'AS-Evolution.unr', 1, 'Second tower lock 1', 'was disabled!', 15, 12, 1),
(263, 'AS-Evolution.unr', 2, 'Second tower lock 2', 'was disabled!', 15, 12, 1),
(264, 'AS-Evolution.unr', 3, 'Main Tower doors', 'are open!', 20, 12, 1),
(265, 'AS-Austria][.unr', 0, 'The Generator', 'has been Started!!!', 50, 10, 1),
(266, 'AS-Austria][.unr', 1, 'Lower Entrance', 'was discovered and destroyed !!!', 0, 10, 1),
(267, 'AS-Austria][.unr', 2, 'The Security door', 'has been opened !!!', 1, 5, 1),
(268, 'AS-Austria][.unr', 3, 'The Warehouse', 'has  been  conquered by the Attackers.', 1, 7, 1.5),
(269, 'AS-Austria][.unr', 4, 'The Computer', 'has been manipulated.', 80, 10, 1),
(270, 'AS-Austria][.unr', 5, 'Escape', '', 1, 10, 1),
(271, 'AS-Austria][.unr', 6, 'The Warehouse Doors', 'has been opened !!!', 1, 15, 1),
(272, 'AS-Austria][.unr', 7, 'Mine', 'spawnpoint has been activated !', 0, 10, 1),
(273, 'AS-ForBidden.unr', 4, 'The hydraulic compressor', 'was destroyed!', 25, 6, 1),
(274, 'AS-ForBidden.unr', 5, 'Missiles', 'were launched!', 10, 6, 1),
(275, 'AS-ForBidden.unr', 6, 'Missiles', 'were launched!', 10, 6, 1),
(276, 'AS-ForBidden.unr', 7, 'Missiles', 'were launched!', 10, 6, 1),
(277, 'AS-IndefiniteV3.unr', 3, 'The bridge', 'was crossed.', 202, 10, 1),
(278, 'AS-HeavyWaterFactoryCE_beta.unr', 0, 'Barracks Doors', 'Opened!', 1, 10, 1),
(279, 'AS-HeavyWaterFactoryCE_beta.unr', 1, 'Front Gates', 'Are Open!', 1, 10, 1),
(280, 'AS-HeavyWaterFactoryCE_beta.unr', 2, 'Control Panel', 'Hacked!', 1, 10, 1),
(281, 'AS-HeavyWaterFactoryCE_beta.unr', 3, 'Atomic bomb', 'was stolen!', 1, 10, 1),
(282, 'AS-HeavyWaterFactoryCE_beta.unr', 4, 'Main Gates', 'Are Open!', 0, 10, 1),
(283, 'AS-HeavyWaterFactoryCE_beta.unr', 5, 'Heavy Water Station', 'Is Secure!', 0, 10, 1),
(284, 'AS-HeavyWaterFactoryCE_beta.unr', 6, 'Enginering Rooms', 'Entered!', 0, 10, 1),
(285, 'AS-Charlemagne.unr', 0, 'Jump Pad to Ship', 'has been reached!', 15, 10, 1),
(286, 'AS-Charlemagne.unr', 1, 'Missle pack', 'was detached!', 25, 10, 1),
(287, 'AS-Charlemagne.unr', 2, 'Missle pack', 'was detached!', 25, 10, 1),
(288, 'AS-Charlemagne.unr', 3, 'The right-side reactor shutdown override', 'was triggered!', 2, 10, 1),
(289, 'AS-Charlemagne.unr', 4, 'The bottom-leftreactor shutdown override', 'was triggered!', 2, 10, 1),
(290, 'AS-Charlemagne.unr', 5, 'The top-left reactor shutdown override', 'was triggered!', 2, 10, 1),
(291, 'AS-SnowDunesAL.unr', 0, 'Ship', 'Captured', 0, 0, 1),
(292, 'AS-SnowDunesAL.unr', 1, 'Second Level Doors', 'Unlocked!', 4, 0, 1),
(293, 'AS-SnowDunesAL.unr', 2, 'Tower Door', 'Opening', 2, 0, 1),
(294, 'AS-SnowDunesAL.unr', 3, 'Ship Dock Lasers', 'Deactivated!!', 3, 0, 1),
(295, 'AS-SnowDunesAL.unr', 4, 'Radar', 'Disabled!', 5, 0, 1),
(296, 'AS-SnowDunesAL.unr', 5, 'Energy Cell 1', 'was destroyed!', 1, 0, 1),
(297, 'AS-SnowDunesAL.unr', 6, 'Energy Cell 2', 'was destroyed!', 1, 0, 1),
(298, 'AS-GrandCanyonML2.unr', 0, 'Lift Entrance', 'Door Was Opend', 95, 10, 1),
(299, 'AS-GrandCanyonML2.unr', 1, 'Dynamite', 'was Placed', 100, 10, 1),
(300, 'AS-GrandCanyonML2.unr', 11, 'Harbour Door', 'Is Opening', 15, 35, 1),
(301, 'AS-GrandCanyonML2.unr', 12, 'Attackers', 'Has Escaped', 0, 35, 1),
(302, 'AS-GrandCanyonML2.unr', 2, 'Power Generator', 'was destroyed!', 55, 20, 1),
(303, 'AS-GrandCanyonML2.unr', 3, 'Fort3', '', 85, 10, 1),
(304, 'AS-GrandCanyonML2.unr', 4, 'Fort3', '', 85, 10, 1),
(305, 'AS-GrandCanyonML2.unr', 5, 'Lift', 'is Accessible', 65, 10, 1),
(306, 'AS-GrandCanyonML2.unr', 6, 'Lift Door', 'Was opened', 75, 10, 1),
(307, 'AS-GrandCanyonML2.unr', 7, 'Cave Lock 1', 'Was Disabled', 45, 30, 1),
(308, 'AS-GrandCanyonML2.unr', 8, 'Cave Lock 2', 'Was Disabled', 45, 30, 1),
(309, 'AS-GrandCanyonML2.unr', 9, 'Attackers has', 'Reached Canyon', 35, 30, 1),
(310, 'AS-TheFactory.unr', 0, 'Cavern entrance', 'is breached!', 30, 1, 1),
(311, 'AS-TheFactory.unr', 1, 'Inside doors', 'are opening!', 26, 4, 1),
(312, 'AS-TheFactory.unr', 2, 'Right terminal', 'has been hacked!', 28, 10, 1),
(313, 'AS-TheFactory.unr', 3, 'Left terminal', 'has been hacked!', 28, 10, 1),
(314, 'AS-TheFactory.unr', 4, 'Barricade', 'has been blown in pieces!', 40, 1, 1),
(315, 'AS-TheFactory.unr', 5, 'Computer', 'was destroyed!', 0, 12, 1),
(316, 'AS-TheFactory.unr', 6, 'Testarea', 'entered!', 18, 14, 1),
(317, 'AS-TheFactory.unr', 7, 'Panel destroyed,', 'first lower doors are opening!', 20, 10, 1),
(318, 'AS-TheFactory.unr', 8, 'Factory', 'is shut down!', 16, 10, 1),
(319, 'AS-Resurrection.unr', 0, 'First Gate', 'has been opened!', 30, 12, 1),
(320, 'AS-Resurrection.unr', 1, 'The Warlock', 'has arisen!', 0, 12, 1),
(321, 'AS-Resurrection.unr', 2, 'Left bell', 'has been rung!', 20, 12, 1),
(322, 'AS-Resurrection.unr', 3, 'Right bell', 'has been rung!', 20, 12, 1),
(323, 'AS-Resurrection.unr', 4, 'An Idol', 'has been awakened!', 1, 12, 1),
(324, 'AS-Resurrection.unr', 5, 'An Idol', 'has been awakened!', 1, 12, 1),
(325, 'AS-Resurrection.unr', 6, 'An Idol', 'has been awakened!', 1, 12, 1),
(326, 'AS-Resurrection.unr', 7, 'An Idol', 'has been awakened!', 1, 12, 1),
(327, 'AS-Resurrection.unr', 8, 'The Tower', 'has been reached!', 15, 12, 1),
(328, 'AS-ManticoreLE.unr', 0, 'Computer lab doors', 'are opening!', 20, 10, 1),
(329, 'AS-ManticoreLE.unr', 1, 'Ventilation gate', 'was destroyed!', 90, 10, 1),
(330, 'AS-ManticoreLE.unr', 10, 'Explosive charge', 'was placed!', 110, 10, 1),
(331, 'AS-ManticoreLE.unr', 11, 'Autocannon', 'disabled!', 0, 10, 1),
(332, 'AS-ManticoreLE.unr', 12, 'Lifts', 'powering up...', 50, 10, 1),
(333, 'AS-ManticoreLE.unr', 13, 'The attackers are', 'in the lower passage!', 0, 10, 1),
(334, 'AS-ManticoreLE.unr', 2, 'The attackers have', 'entered the base!', 100, 10, 1),
(335, 'AS-ManticoreLE.unr', 3, 'The big doors', 'are opening!', 80, 10, 1),
(336, 'AS-ManticoreLE.unr', 4, 'The attackers are', 'in the lower ventilation!', 0, 10, 1),
(337, 'AS-ManticoreLE.unr', 5, 'Second ventilation entrance', 'has been opened!', 70, 10, 1),
(338, 'AS-ManticoreLE.unr', 6, 'Tech-crystals', 'were destroyed!', 10, 10, 1),
(339, 'AS-ManticoreLE.unr', 7, 'Second ventilation entrance', 'has been opened!', 70, 10, 1),
(340, 'AS-ManticoreLE.unr', 8, 'Computer lock 1', 'is being hacked...', 15, 10, 1),
(341, 'AS-ManticoreLE.unr', 9, 'Computer lock 2', 'is being hacked...', 15, 10, 1),
(342, 'AS-Solstice.unr', 0, 'The lock on the right', 'was depressurized!', 5, 0, 1),
(343, 'AS-Solstice.unr', 1, 'The lock on the left', 'was depressurized!', 4, 0, 1),
(344, 'AS-Solstice.unr', 11, '0', '0', 7, 0, 1),
(345, 'AS-Solstice.unr', 2, 'The pipes', 'were destroyed! Steam pressure is down, hydraulic', 6, 0, 1),
(346, 'AS-Solstice.unr', 3, 'All Locks', 'Disengaged: Drawbidge Lowering, Tower Access Grant', 5, 0, 1),
(347, 'AS-Solstice.unr', 4, 'Assaulting Team', 'has Conquered the Base!', 1, 10, 1),
(348, 'AS-Solstice.unr', 5, 'The Rear Caves', 'have been discovered!!', 11, 0, 1),
(349, 'AS-Solstice.unr', 6, 'The left Pump Generator', 'was shut down!', 3, 0, 1),
(350, 'AS-Solstice.unr', 7, 'The right Pump Generator', 'was shut down!', 2, 0, 1),
(351, 'AS-Tydium][AL_preview.unr', 0, 'Cave-door key', 'has been stolen!', 4, 10, 1),
(352, 'AS-Tydium][AL_preview.unr', 1, 'Cannonball', 'has been taken.', 3, 10, 1),
(353, 'AS-Tydium][AL_preview.unr', 2, 'Underground', 'breached!', 1, 10, 1),
(354, 'AS-Tydium][AL_preview.unr', 3, 'The Catapault', 'is under enemy control', 2, 10, 1),
(355, 'AS-Tydium][AL_preview.unr', 4, 'Crystal', 'was destroyed!', 0, 10, 1),
(356, 'AS-Tydium][AL_preview.unr', 5, 'Crystal Room doors', 'are about to open...', 0, 10, 1),
(357, 'AS-Zeppelin.unr', 0, 'FinalTarget', 'Oh the humanity!', 10, 10, 1.5),
(358, 'AS-Zeppelin.unr', 1, 'Engine#1', 'was destroyed!', 25, 0, 1),
(359, 'AS-Zeppelin.unr', 2, 'Engine#2', 'was destroyed!', 25, 0, 1),
(360, 'AS-Zeppelin.unr', 3, 'Engine#3', 'was destroyed!', 25, 0, 1),
(361, 'AS-Zeppelin.unr', 4, 'Engine#4', 'was destroyed!', 25, 0, 1),
(362, 'AS-Zeppelin.unr', 5, 'Zeppelin Ramp', 'has been lowered!', 50, 0, 1),
(363, 'AS-BioAssaultTE_Final_Beta.unr', 0, 'Main Gate', 'was opened ! ! !', 0, 10, 1),
(364, 'AS-BioAssaultTE_Final_Beta.unr', 1, 'Bridge Entrance', 'has been breached!', 0, 10, 1),
(365, 'AS-BioAssaultTE_Final_Beta.unr', 2, 'Second Gate', 'Unlocked!', 0, 10, 1),
(366, 'AS-BioAssaultTE_Final_Beta.unr', 3, 'The Bridge', 'has been crossed!', 0, 10, 1),
(367, 'AS-BioAssaultTE_Final_Beta.unr', 4, 'Science corridor', 'has been reached.', 0, 10, 1),
(368, 'AS-BioAssaultTE_Final_Beta.unr', 5, 'Shooting Range', 'is in Reach!!', 0, 10, 1),
(369, 'AS-BioAssaultTE_Final_Beta.unr', 6, 'The Tunnel', 'Is Secure!', 0, 10, 1),
(370, 'AS-BioAssaultTE_Final_Beta.unr', 7, 'Bio-chamber', 'has been reached!', 0, 10, 1),
(371, 'AS-BioAssaultTE_Final_Beta.unr', 8, 'Bio-chamber', 'has become unstable.', 0, 11, 1),
(372, 'AS-BioAssaultTE_Final_Beta.unr', 9, 'Escape!', '', 0, 12, 1),
(373, 'AS-SnowDunesAL_preview.unr', 0, 'Ship', 'Captured', 0, 10, 1),
(374, 'AS-SnowDunesAL_preview.unr', 1, 'Second Level Doors', 'Unlocked!', 4, 10, 1),
(375, 'AS-SnowDunesAL_preview.unr', 2, 'Tower Door', 'Opening', 2, 10, 1),
(376, 'AS-SnowDunesAL_preview.unr', 3, 'Ship Dock Lasers', 'Deactivated!!', 3, 10, 1),
(377, 'AS-SnowDunesAL_preview.unr', 4, 'Radar', 'Disabled!', 5, 10, 1),
(378, 'AS-SnowDunesAL_preview.unr', 5, 'Energy Cell 1', 'was destroyed!', 1, 10, 1),
(379, 'AS-SnowDunesAL_preview.unr', 6, 'Energy Cell 2', 'was destroyed!', 1, 10, 1),
(380, 'AS-ColderSteel.unr', 10, 'Final Objective', '', 0, 10, 0.1),
(381, 'AS-GuardiaSVbeta3.unr', 0, 'Fuse', 'has been ignited!', 4, 5, 1),
(382, 'AS-GuardiaSVbeta3.unr', 1, 'Garage Door', 'is about to open!', 1, 5, 1),
(383, 'AS-GuardiaSVbeta3.unr', 2, 'Tank Turret', 'was destroyed!', 0, 5, 1),
(384, 'AS-GuardiaSVbeta3.unr', 3, 'Lava Bridge', 'has been crossed!', 2, 5, 1),
(385, 'AS-GuardiaSVbeta3.unr', 4, 'Cavern', 'has been breached!', 3, 5, 1),
(386, 'AS-Siege]I[AL.unr', 0, 'Main door', 'is open!', 255, 11, 1),
(387, 'AS-Siege]I[AL.unr', 1, 'Security Doors', 'destroyed! Main Tower accessible!', 180, 11, 1),
(388, 'AS-Siege]I[AL.unr', 2, 'Roof', 'taken!', 150, 11, 1),
(389, 'AS-Siege]I[AL.unr', 3, 'Center Doors', 'are open!', 255, 11, 1),
(390, 'AS-Siege]I[AL.unr', 4, 'Direction-finding transmitter', 'activated! Reinforcements have arrived!', 0, 11, 1),
(391, 'AS-Siege]I[AL.unr', 5, 'Tower hatch', 'was destroyed!', 0, 11, 1),
(392, 'AS-CTC-Dimensionbeta6.unr', 0, 'Prison door control', 'was destroyed!', 100, 30, 1),
(393, 'AS-CTC-Dimensionbeta6.unr', 1, 'The Main bridge', 'was activated!', 95, 30, 1),
(394, 'AS-CTC-Dimensionbeta6.unr', 11, 'Supply room', 'located!', 90, 30, 1),
(395, 'AS-CTC-Dimensionbeta6.unr', 12, 'Teleporter room', 'has been entered!', 65, 30, 1),
(396, 'AS-CTC-Dimensionbeta6.unr', 2, 'Gorge doors', 'were activated', 81, 30, 1),
(397, 'AS-CTC-Dimensionbeta6.unr', 3, 'Dimensional attack', 'under way!', 77, 30, 1),
(398, 'AS-CTC-Dimensionbeta6.unr', 4, 'Teleporter doors', 'are activated!', 57, 30, 1),
(399, 'AS-CTC-Dimensionbeta6.unr', 5, 'Dimensional gate', 'entered!', 56, 30, 1),
(400, 'AS-CTC-Dimensionbeta6.unr', 6, 'Energyblocker1', 'was destroyed!', 75, 30, 1),
(401, 'AS-CTC-Dimensionbeta6.unr', 7, 'Energyblocker2', 'was destroyed!', 75, 30, 1),
(402, 'AS-CTC-Dimensionbeta6.unr', 8, 'Inner base', 'is about to be entered (Main)', 85, 30, 1),
(403, 'AS-UMS-Public3f.unr', 0, 'Missile Safety Switch Two', 'has been deactivated!', 0, 14, 1),
(404, 'AS-UMS-Public3f.unr', 1, 'Missile Safety Switch One', 'has been deactivated!', 0, 14, 1),
(405, 'AS-UMS-Public3f.unr', 2, 'The Attackers', 'are approaching the missile!', 0, 14, 1),
(406, 'AS-UMS-Public3f.unr', 3, 'The Storage Door', 'has been opened!', 0, 14, 1),
(407, 'AS-UMS-Public3f.unr', 4, 'The Missile Silo Door Lock', 'has been disabled!', 0, 14, 1),
(408, 'AS-UMS-Public3f.unr', 5, 'The Defense''s Teleporter', 'has been reprogrammed!', 0, 14, 1),
(409, 'AS-UMS-Public3f.unr', 6, 'The submarine remote lock', 'has been destroyed.  The submarine will open short', 0, 14, 1),
(410, 'AS-UMS-Public3f.unr', 7, 'The docks', 'have been reached!', 0, 14, 1),
(411, 'AS-UMS-Public3f.unr', 8, 'The Escape Vessle', 'has been stolen!', 0, 14, 1),
(412, 'AS-UMS-Public2.unr', 0, 'Missile Safety Switch Two', 'has been deactivated!', 0, 14, 1),
(413, 'AS-UMS-Public2.unr', 1, 'Missile Safety Switch One', 'has been deactivated!', 0, 14, 1),
(414, 'AS-UMS-Public2.unr', 2, 'The Attackers', 'are approaching the missile!', 0, 14, 1),
(415, 'AS-UMS-Public2.unr', 3, 'The Storage Door', 'has been opened!', 0, 14, 1),
(416, 'AS-UMS-Public2.unr', 4, 'The Missile Silo Door Lock', 'has been disabled!', 0, 14, 1),
(417, 'AS-UMS-Public2.unr', 5, 'The Defense''s Teleporter', 'has been reprogrammed!', 0, 14, 1),
(418, 'AS-UMS-Public2.unr', 6, 'The submarine remote lock', 'has been destroyed.  The submarine will open short', 0, 14, 1),
(419, 'AS-UMS-Public2.unr', 7, 'The docks', 'have been reached!', 0, 14, 1),
(420, 'AS-UMS-Public2.unr', 8, 'The Escape Vessle', 'has been stolen!', 0, 14, 1),
(421, 'AS-(BETA2)OrionsCursePart1.unr', 0, 'Bay Door Switch 1', 'was activated!', 100, 10, 1),
(422, 'AS-(BETA2)OrionsCursePart1.unr', 1, 'Bay Door Switch 2', 'was activated!', 95, 10, 1),
(423, 'AS-(BETA2)OrionsCursePart1.unr', 2, 'Control Panel 2', 'was destroyed!', 85, 10, 1),
(424, 'AS-(BETA2)OrionsCursePart1.unr', 3, 'Control Panel 3', 'was destroyed!', 80, 10, 1),
(425, 'AS-(BETA2)OrionsCursePart1.unr', 4, 'Control Panel 4', 'was destroyed!', 75, 10, 1),
(426, 'AS-(BETA2)OrionsCursePart1.unr', 5, 'Control Panel 1', 'was destroyed!', 90, 10, 1),
(427, 'AS-(BETA2)OrionsCursePart1.unr', 6, 'Defense Grid Master Control', 'is disabled !!!', 0, 10, 1),
(428, 'AS-(BETA2)OrionsCursePart1.unr', 7, 'Security System 1', 'was destroyed!', 20, 10, 1),
(429, 'AS-(BETA2)OrionsCursePart1.unr', 8, 'Security System 2', 'was destroyed!', 10, 10, 1),
(430, 'AS-LaserV3.unr', 6, 'Final Objective', 'was destroyed!', 0, 25, 1),
(431, 'AS-IndefiniteV3.unr', 9, 'Final Objective', 'Indefinite was destroyed!', 0, 15, 1),
(432, 'AS-Evolution.unr', 0, 'Final Objective', 'was destroyed!', 50, 12, 1),
(433, 'AS-OffworldassaultV3Upd.unr', 0, 'The Elevator Power Cell', 'Has Been Found!', 90, 10, 1),
(434, 'AS-OffworldassaultV3Upd.unr', 1, 'The Mines', 'Have Been Taken!', 75, 10, 1),
(435, 'AS-OffworldassaultV3Upd.unr', 10, 'The Offworld Gate', 'Has Been Entered!', 49, 10, 1),
(436, 'AS-OffworldassaultV3Upd.unr', 11, 'New spawnpoint', 'reached!', 0, 10, 1),
(437, 'AS-OffworldassaultV3Upd.unr', 12, 'Village is now', 'secured!', 0, 10, 1),
(438, 'AS-OffworldassaultV3Upd.unr', 2, 'The Elevator Power', 'Has Been Restored!', 80, 10, 1),
(439, 'AS-OffworldassaultV3Upd.unr', 3, 'The Main Facility Gates', 'Have Been Breached!', 65, 10, 1),
(440, 'AS-OffworldassaultV3Upd.unr', 4, 'Area One Plasmafield', 'Has Been Shut Down!', 60, 10, 1),
(441, 'AS-OffworldassaultV3Upd.unr', 5, 'The Autolock System', 'Has Been Shut Down!', 55, 10, 1),
(442, 'AS-OffworldassaultV3Upd.unr', 6, 'The Stargate', 'Has Been Activated!', 50, 10, 1),
(443, 'AS-OffworldassaultV3Upd.unr', 7, 'The Iris', 'Has Been Closed!', 40, 15, 1),
(444, 'AS-OffworldassaultV3Upd.unr', 8, 'Mine Enterance Teleporter Controller', 'was destroyed!', 95, 10, 1),
(445, 'AS-OffworldassaultV3Upd.unr', 9, 'The Mine Elevator Teleporter Controller', 'was destroyed!', 85, 10, 1),
(446, 'AS-Siege]I[.unr', 0, 'Main door', 'is open!', 255, 11, 1),
(447, 'AS-Siege]I[.unr', 1, 'Security Doors', 'destroyed! Main Tower accessible!', 180, 11, 1),
(448, 'AS-Siege]I[.unr', 2, 'Roof', 'taken!', 150, 11, 1),
(449, 'AS-Siege]I[.unr', 3, 'Center Doors', 'are open!', 255, 11, 1),
(450, 'AS-Siege]I[.unr', 4, 'Direction-finding transmitter', 'activated! Reinforcements have arrived!', 0, 11, 1),
(451, 'AS-Siege]I[.unr', 5, 'Tower hatch', 'was destroyed!', 0, 11, 1),
(452, 'AS-Skydiving2.unr', 0, 'Tunnel Exit', 'you got it', 10, 10, 1),
(453, 'AS-FD.unr', 0, 'Tunnel Exit', 'Is reached!', 0, 20, 1),
(454, 'AS-FD.unr', 1, 'Level ]|[', 'is entered!', 0, 20, 1),
(455, 'AS-FD.unr', 2, 'The MainGate', 'is open', 0, 20, 1),
(456, 'AS-FD.unr', 3, 'LEVEL IV', 'is entered!', 0, 20, 1),
(457, 'AS-FD.unr', 4, 'Chessboard', 'is entered!', 0, 20, 1),
(458, 'AS-FD.unr', 5, 'the U door', 'is open', 0, 20, 1),
(459, 'AS-FD.unr', 6, 'the kick', 'has been past', 0, 20, 1),
(460, 'AS-FD.unr', 7, 'Level ||', 'is entered', 0, 20, 1),
(461, 'AS-FD.unr', 8, 'the split door', 'is open', 0, 20, 1),
(462, 'AS-BlueWhale_beta.unr', 0, 'Submarine Tunnel', 'has been opened!', 5, 60, 1),
(463, 'AS-BlueWhale_beta.unr', 1, 'MainAccessHallway', 'has been infiltrated!', 4, 60, 1),
(464, 'AS-BlueWhale_beta.unr', 2, 'Labratory', 'has been invaded!', 3, 60, 1),
(465, 'AS-BlueWhale_beta.unr', 3, 'SnipersNest', 'under enemy control!', 2, 60, 1),
(466, 'AS-BlueWhale_beta.unr', 4, 'Storage', 'has been penetrated!', 1, 60, 1),
(467, 'AS-SiegEasy.unr', 0, 'Sacrifices', '', 0, 5, 1),
(468, 'AS-Progress.unr', 0, 'The Roof', 'is opening.', 20, 7, 1),
(469, 'AS-Progress.unr', 1, 'The Crane Lever', 'was pushed!', 70, 7, 1),
(470, 'AS-Progress.unr', 2, 'The Front Door Crystal', 'was destroyed!', 101, 7, 1),
(471, 'AS-Progress.unr', 3, 'The Craft', 'was destroyed!', 0, 7, 1),
(472, 'AS-Gladiator_b7.unr', 0, 'Bridge', 'is reached!', 16, 10, 1),
(473, 'AS-Gladiator_b7.unr', 1, 'The wall', 'is reached!', 15, 10, 1),
(474, 'AS-Gladiator_b7.unr', 10, 'The Roman Temple', 'was entered!', 1, 10, 1),
(475, 'AS-Gladiator_b7.unr', 11, 'Ancient Warrior', 'was killed and a key found!', 13, 10, 1),
(476, 'AS-Gladiator_b7.unr', 2, 'Sword Gladius', 'was found!', 14, 10, 1),
(477, 'AS-Gladiator_b7.unr', 3, 'Box Barricade', 'was destroyed!', 11, 10, 1),
(478, 'AS-Gladiator_b7.unr', 4, 'Switch One', 'was pushed!', 8, 10, 1),
(479, 'AS-Gladiator_b7.unr', 5, 'Switch Two', 'was pushed!', 9, 10, 1),
(480, 'AS-Gladiator_b7.unr', 6, 'Final Objective', 'was destroyed!', 0, 25, 1),
(481, 'AS-Gladiator_b7.unr', 7, 'Lava Bridge', 'was crossed!', 10, 10, 1),
(482, 'AS-Gladiator_b7.unr', 8, 'The First Gate', 'was opened!', 12, 10, 1),
(483, 'AS-Gladiator_b7.unr', 9, 'Radio', 'was used to call for air support!', 2, 10, 1),
(484, 'AS-CastleArrrggghhh.unr', 0, 'The_Holy_Grail', 'Has Been stolen!', 0, 15, 1),
(485, 'AS-CastleArrrggghhh.unr', 1, 'Electron_Ram', 'Firing', 197, 10, 1),
(486, 'AS-CastleArrrggghhh.unr', 2, 'Electron_Ram_Power', 'Activated', 255, 10, 1),
(487, 'AS-CastleArrrggghhh.unr', 3, 'Hall_of_Babes', 'Open', 149, 10, 1),
(488, 'AS-CastleArrrggghhh.unr', 4, 'Blackadder_Hall', 'Open', 100, 10, 1),
(489, 'AS-Coldsteel2.unr', 0, 'Turret Gun', 'was destroyed! Bunker doors released.', 95, 20, 1),
(490, 'AS-Coldsteel2.unr', 1, 'Inner complex gate', 'opened!', 85, 20, 1),
(491, 'AS-Coldsteel2.unr', 10, 'Final Objective', 'Galaxy Cruiser Captured!', 35, 20, 1),
(492, 'AS-Coldsteel2.unr', 2, 'Complex', 'security door opened!', 75, 20, 1),
(493, 'AS-Coldsteel2.unr', 3, 'Fusion Reactor 1', 'disabled', 65, 20, 1),
(494, 'AS-Coldsteel2.unr', 4, 'Fusion Reactor 2', 'disabled', 65, 20, 1),
(495, 'AS-Coldsteel2.unr', 5, 'Fusion Power System', 'disabled', 0, 20, 1),
(496, 'AS-Coldsteel2.unr', 6, 'Fusion Transformers 3', 'was destroyed!', 55, 20, 1),
(497, 'AS-Coldsteel2.unr', 7, 'Fusion Transformers 2', 'was destroyed!', 55, 20, 1),
(498, 'AS-Coldsteel2.unr', 8, 'Fusion Transformers 1', 'was destroyed!', 55, 20, 1),
(499, 'AS-Coldsteel2.unr', 9, 'Ship', 'will be accessable in 10 seconds.', 45, 20, 1),
(500, 'AS-DoubleHi-speed.unr', 0, 'Carriage 3', 'has been entered!', 3, 10, 1),
(501, 'AS-DoubleHi-speed.unr', 1, 'Carriage2', 'has been entered!', 2, 10, 1),
(502, 'AS-DoubleHi-speed.unr', 2, 'Engine', 'has been entered!', 1, 10, 1),
(503, 'AS-DoubleHi-speed.unr', 3, 'Switch', 'in control cabin has been activated!', 0, 15, 1),
(504, 'AS-Vesuvius-SE.unr', 0, 'The Safety Doors', 'have been opened!', 0, 10, 1),
(505, 'AS-Vesuvius-SE.unr', 1, 'Vesuvius', 'has been entered!', 0, 10, 1),
(506, 'AS-Vesuvius-SE.unr', 2, 'The Lava Bridge', 'has been crossed!', 0, 10, 1),
(507, 'AS-Vesuvius-SE.unr', 3, 'The Left Door', 'has been opened!', 0, 10, 1),
(508, 'AS-Vesuvius-SE.unr', 4, 'The Right Door', 'has been opened!', 0, 10, 1),
(509, 'AS-Vesuvius-SE.unr', 5, 'The Gatehouse', 'has been overtaken!', 0, 10, 1),
(510, 'AS-UMS-Public3o.unr', 0, 'Missile Safety Switch Two', 'has been deactivated!', 0, 14, 1),
(511, 'AS-UMS-Public3o.unr', 1, 'Missile Safety Switch One', 'has been deactivated!', 0, 14, 1),
(512, 'AS-UMS-Public3o.unr', 2, 'Attackers', 'are approaching the missile!', 0, 14, 1),
(513, 'AS-UMS-Public3o.unr', 3, 'Storage Door', 'has been opened!', 0, 14, 1),
(514, 'AS-UMS-Public3o.unr', 4, 'Security panel', 'has been destroyed!', 0, 14, 1),
(515, 'AS-UMS-Public3o.unr', 5, 'Defense''s Teleporter', 'has been reprogrammed!', 0, 14, 1),
(516, 'AS-UMS-Public3o.unr', 6, 'Submarine remote lock', 'has been destroyed.  The submarine will open short', 0, 14, 1),
(517, 'AS-UMS-Public3o.unr', 7, 'Docks', 'have been reached!', 0, 14, 1),
(518, 'AS-UMS-Public3o.unr', 8, 'The Escape Vessle', 'has been stolen!', 0, 14, 1),
(519, 'AS-Football.unr', 0, 'They''re at the 20', '!', 10, 1, 1),
(520, 'AS-Football.unr', 1, '...the 30', '!', 9, 1, 1),
(521, 'AS-Football.unr', 2, '...the 40', '!', 8, 1, 1),
(522, 'AS-Football.unr', 3, 'They''re at', 'midfield!', 7, 1, 1),
(523, 'AS-Football.unr', 4, '...the 40', '!', 6, 1, 1),
(524, 'AS-Football.unr', 5, '...the 30', '!', 5, 1, 1),
(525, 'AS-Football.unr', 6, '...the 20', '!!', 4, 1, 1),
(526, 'AS-Football.unr', 7, '...the 10', '!!!', 3, 1, 1),
(527, 'AS-Football.unr', 8, 'TOUCHDOWN', '!!!!!!!', 2, 1, 1),
(528, 'AS-Football.unr', 9, 'And the kick', 'is good.', 1, 1, 1),
(529, 'AS-Hellraiser-_a-011_.unr', 0, 'The Fortress', 'was breached!', 0, 10, 1),
(530, 'AS-Hellraiser-_a-011_.unr', 1, 'The lament configuration puzzlebox', 'was taken!', 0, 10, 1),
(531, 'AS-Hellraiser-_a-011_.unr', 2, 'Lament door lock', 'was released!', 30, 10, 1),
(532, 'AS-Hellraiser-_a-011_.unr', 4, 'Puzzlebox', 'was placed !', 0, 10, 1),
(533, 'AS-Hellraiser-_a-011_.unr', 5, 'Second Chain', 'was destroyed!', 0, 10, 1),
(534, 'AS-Hellraiser-_a-011_.unr', 6, 'Outside', 'is reached!', 0, 10, 1),
(535, 'AS-Hellraiser-_a-011_.unr', 7, 'First chain', 'was destroyed!', 0, 10, 1),
(536, 'AS-Hellraiser-_a-011_.unr', 8, 'Highergrounds', 'Reached', 0, 10, 1),
(537, 'AS-Desert][.unr', 0, 'Main gate', '', 0, 0, 1),
(538, 'AS-Desert][.unr', 1, 'Last tank', '', 0, 0, 1),
(539, 'AS-Desert][.unr', 2, 'Doorway', '', 0, 0, 1),
(540, 'AS-Hellraiser-[a-012].unr', 0, 'The Fortress', 'was breached!', 0, 13, 1),
(541, 'AS-Hellraiser-[a-012].unr', 1, 'The lament configuration puzzlebox', 'was taken!', 0, 13, 1),
(542, 'AS-Hellraiser-[a-012].unr', 2, 'Lament door lock', 'was released!', 30, 13, 1),
(543, 'AS-Hellraiser-[a-012].unr', 3, 'Puzzlebox', 'was placed !', 0, 13, 1),
(544, 'AS-Hellraiser-[a-012].unr', 4, 'Second Chain', 'was destroyed!', 0, 13, 1),
(545, 'AS-Hellraiser-[a-012].unr', 5, 'Outside', 'is reached!', 0, 13, 1),
(546, 'AS-Hellraiser-[a-012].unr', 6, 'First chain', 'was destroyed!', 0, 13, 1),
(547, 'AS-Hellraiser-[a-012].unr', 7, 'Highergrounds', 'Reached', 0, 13, 1),
(548, 'AS-Hellraiser-[a-012].unr', 8, 'Death, distruction, pain and torture', 'is now ended', 0, 13, 1),
(549, 'AS-Hellraiser-[a-012].unr', 9, 'Closer Attackerspawn', 'Enabled', 0, 13, 1),
(550, 'AS-RiverbedALse14.unr', 0, 'The cavern passage', 'has been breached!', 0, 9, 1),
(551, 'AS-RiverbedALse14.unr', 1, 'Main Computer', 'was destroyed!', 10, 9, 1),
(552, 'AS-RiverbedALse14.unr', 2, 'Control Panel', '', 153, 5, 1),
(553, 'AS-RiverbedALse14.unr', 3, 'The Main Entrance', 'has been reached!', 0, 9, 1),
(554, 'AS-GuardiaFUN.unr', 0, 'Fuse', 'has been ignited!', 4, 5, 1),
(555, 'AS-GuardiaFUN.unr', 1, 'Garage Door', 'was breached!', 1, 5, 1),
(556, 'AS-GuardiaFUN.unr', 2, 'Tank Turret', 'was destroyed!', 0, 5, 1),
(557, 'AS-GuardiaFUN.unr', 3, 'Lava Bridge', 'has been crossed!', 2, 5, 1),
(558, 'AS-GuardiaFUN.unr', 4, 'Cavern', 'has been breached!', 3, 5, 1),
(559, 'AS-Bridge][.unr', 0, 'Second assault entrance', 'has been opened!', 80, 10, 1),
(560, 'AS-Bridge][.unr', 1, 'Explosive charge 1', 'has been placed.', 50, 10, 1),
(561, 'AS-Bridge][.unr', 10, 'The attackers have reached', 'the Bridge!', 55, 10, 1),
(562, 'AS-Bridge][.unr', 2, 'Explosive charge 4', 'has been placed.', 50, 10, 1),
(563, 'AS-Bridge][.unr', 3, 'Explosive charge 2', 'has been placed.', 50, 10, 1),
(564, 'AS-Bridge][.unr', 4, 'Explosive charge 3', 'has been placed.', 50, 10, 1),
(565, 'AS-Bridge][.unr', 5, 'Detonator', 'has been used!', 40, 16, 1),
(566, 'AS-Bridge][.unr', 6, 'Main gates', 'were destroyed!', 100, 10, 1),
(567, 'AS-Bridge][.unr', 7, 'The attackers have entered', 'the Base!', 90, 10, 1),
(568, 'AS-Bridge][.unr', 8, 'Final assault entrance', 'has been discovered!', 60, 10, 1),
(569, 'AS-Bridge][.unr', 9, 'Door pressure controller', 'was manipulated!', 70, 10, 1),
(570, 'AS-GrandCanyon.unr', 0, 'Lift Entrance', 'Door Was Opend', 95, 10, 1),
(571, 'AS-GrandCanyon.unr', 1, 'Dynamite', 'was Placed', 100, 10, 1),
(572, 'AS-GrandCanyon.unr', 11, 'Harbour Door', 'Is Opening', 15, 35, 1),
(573, 'AS-GrandCanyon.unr', 12, 'Attackers', 'Has Escaped', 0, 35, 1),
(574, 'AS-GrandCanyon.unr', 2, 'Power Generator', 'was destroyed!', 55, 20, 1),
(575, 'AS-GrandCanyon.unr', 3, 'Fort3', '', 85, 10, 1),
(576, 'AS-GrandCanyon.unr', 4, 'Fort3', '', 85, 10, 1),
(577, 'AS-GrandCanyon.unr', 5, 'Lift', 'is Accessible', 65, 10, 1),
(578, 'AS-GrandCanyon.unr', 6, 'Lift Door', 'Was opend', 75, 10, 1),
(579, 'AS-GrandCanyon.unr', 7, 'Cave Lock 1', 'Was Disabled', 45, 30, 1),
(580, 'AS-GrandCanyon.unr', 8, 'Cave Lock 2', 'Was Disabled', 45, 30, 1),
(581, 'AS-GrandCanyon.unr', 9, 'Attackers has', 'Reached Canyon', 35, 30, 1),
(582, 'AS-RookEX.unr', 0, 'Chain 2', '', 4, 7, 1),
(583, 'AS-RookEX.unr', 1, 'Chain 1', '', 4, 7, 1),
(584, 'AS-RookEX.unr', 2, 'Escape', '', 1, 7, 1),
(585, 'AS-RookEX.unr', 3, 'The Main Doors', 'are now open!', 3, 7, 1),
(586, 'AS-RookEX.unr', 4, 'The Library', 'is now Open!', 5, 7, 1),
(587, 'AS-RookEX.unr', 5, 'GateChain 1', '', 4, 7, 1),
(588, 'AS-RookEX.unr', 6, 'GateChain 2', '', 4, 7, 1),
(589, 'AS-LaserV2.unr', 0, 'Terminal', 'codes entered!', 9, 20, 1),
(590, 'AS-LaserV2.unr', 1, 'Power node', 'was destroyed!', 8, 20, 1),
(591, 'AS-LaserV2.unr', 2, 'Cave', 'was entered!', 2, 20, 1),
(592, 'AS-LaserV2.unr', 3, 'Code book', 'was found!', 10, 20, 1),
(593, 'AS-LaserV2.unr', 4, 'Gate control', 'overridden!', 3, 20, 1),
(594, 'AS-LaserV2.unr', 5, 'Turbo lift', 'enabled!', 7, 20, 1),
(595, 'AS-LaserV2.unr', 6, 'Final Objective', 'was destroyed!', 0, 25, 1),
(596, 'AS-LaserV2.unr', 7, 'Circuit boards', 'were destroyed!', 1, 10, 1),
(597, 'AS-Planeshifter.unr', 0, 'The Hive Crystal', 'has been activated!', 0, 40, 1),
(598, 'AS-Planeshifter.unr', 1, 'The Stone Crystal', 'was activated!', 0, 40, 1),
(599, 'AS-Planeshifter.unr', 10, 'A Gate Crystal', 'was activated!', 0, 40, 1),
(600, 'AS-Planeshifter.unr', 11, 'The Material Gate', 'has been opened!', 0, 40, 1),
(601, 'AS-Planeshifter.unr', 2, 'The Air Crystal', 'has been activated!', 0, 40, 1),
(602, 'AS-Planeshifter.unr', 3, 'The Temple Crystal', 'has been activated!', 0, 40, 1),
(603, 'AS-Planeshifter.unr', 4, 'A Gate Crystal', 'was activated!', 0, 40, 1),
(604, 'AS-Planeshifter.unr', 5, 'A Gate Crystal', 'was activated!', 0, 40, 1),
(605, 'AS-Planeshifter.unr', 6, 'A Gate Crystal', 'was activated!', 0, 40, 1),
(606, 'AS-Planeshifter.unr', 7, 'A Gate Crystal', 'was activated!', 0, 40, 1),
(607, 'AS-Planeshifter.unr', 8, 'A Gate Crystal', 'was activated!', 0, 40, 1),
(608, 'AS-Planeshifter.unr', 9, 'A Gate Crystal', 'was activated!', 0, 40, 1),
(609, 'AS-BridgeSE_beta][.unr', 0, 'Second assault entrance', 'has been opened!', 80, 10, 1),
(610, 'AS-BridgeSE_beta][.unr', 1, 'Explosive charge 1', 'has been placed.', 50, 10, 1),
(611, 'AS-BridgeSE_beta][.unr', 10, 'The bridge', 'has been reached!', 55, 10, 1),
(612, 'AS-BridgeSE_beta][.unr', 2, 'Explosive charge 4', 'has been placed.', 50, 10, 1),
(613, 'AS-BridgeSE_beta][.unr', 3, 'Explosive charge 2', 'has been placed.', 50, 10, 1),
(614, 'AS-BridgeSE_beta][.unr', 4, 'Explosive charge 3', 'has been placed.', 50, 10, 1),
(615, 'AS-BridgeSE_beta][.unr', 5, 'Detonator', 'has been used!', 40, 16, 1),
(616, 'AS-BridgeSE_beta][.unr', 6, 'Main gates', 'were destroyed!', 100, 10, 1),
(617, 'AS-BridgeSE_beta][.unr', 7, 'The base', 'has been entered!', 90, 10, 1),
(618, 'AS-BridgeSE_beta][.unr', 8, 'Final assault entrance', 'has been discovered!', 60, 10, 1),
(619, 'AS-BridgeSE_beta][.unr', 9, 'Door pressure controller', 'was manipulated!', 70, 10, 1),
(620, 'AS-Portal_betarelease3of6.unr', 0, 'Gate II', 'will open and Chains can be destroyed', 0, 10, 1),
(621, 'AS-Portal_betarelease3of6.unr', 1, 'Portal', 'has been entered', 0, 10, 1),
(622, 'AS-Portal_betarelease3of6.unr', 2, 'Chain 1', 'was destroyed!', 100, 10, 1),
(623, 'AS-Portal_betarelease3of6.unr', 3, 'Chain 2', 'was destroyed!', 75, 10, 1),
(624, 'AS-Portal_betarelease3of6.unr', 4, 'Gate III', 'will open', 0, 10, 1),
(625, 'AS-Portal_betarelease3of6.unr', 5, 'Assault Team arrived', '- Maingate will open', 0, 10, 1),
(626, 'AS-Portal_betarelease3of6.unr', 6, 'Weaponpassage', 'will open - New Spawnpoints activated', 0, 10, 1),
(627, 'AS-Capacitance-Beta1.unr', 0, 'Main Power Crystal', 'was destroyed!', 0, 10, 1),
(628, 'AS-Capacitance-Beta1.unr', 1, 'The Attackers Have', 'Gained Control of the Ship!', 0, 12, 1),
(629, 'AS-Capacitance-Beta1.unr', 2, 'Access Panel 1', 'was hacked!', 0, 10, 1),
(630, 'AS-Capacitance-Beta1.unr', 3, 'Access Panel 2', 'was hacked!', 0, 10, 1),
(631, 'AS-SaqqaraLE_sobo2.unr', 0, 'Green chamber switch', 'has been activated!', 2, 3, 1),
(632, 'AS-SaqqaraLE_sobo2.unr', 1, 'Yellow chamber switch', 'has been activated!', 2, 3, 1),
(633, 'AS-SaqqaraLE_sobo2.unr', 2, 'Red chamber switch', 'has been activated!', 1, 7, 1),
(634, 'AS-SaqqaraLE_sobo2.unr', 3, 'Blue chamber switch', 'has been activated!', 1, 7, 1),
(635, 'AS-SaqqaraLE_sobo2.unr', 4, 'Waterfall', 'was breached!', 0, 9, 1),
(636, 'AS-SaqqaraLE_sobo2.unr', 5, 'Scarab Doors', 'has been breached!', 0, 7, 1),
(637, 'AS-SaqqaraLE_sobo2.unr', 6, 'Pyramid', 'has been breached!', 2, 3, 1),
(638, 'AS-SaqqaraLE_sobo2.unr', 7, 'Waterfall', 'was breached!', 0, 9, 1),
(639, 'AS-TheDungeon]l[beta2.unr', 0, 'Right Wall', 'has been destroyed!', 0, 10, 1),
(640, 'AS-TheDungeon]l[beta2.unr', 1, 'Left Wall', 'has been destroyed!', 0, 10, 1),
(641, 'AS-TheDungeon]l[beta2.unr', 2, 'Lake', 'has been reached!', 0, 10, 1),
(642, 'AS-TheDungeon]l[beta2.unr', 3, 'Falls', 'have been reached!', 0, 10, 1),
(643, 'AS-TheDungeon]l[beta2.unr', 4, 'Skull Lock', 'has been destroyed!', 0, 10, 1),
(644, 'AS-TheDungeon]l[beta2.unr', 5, 'Escape', 'WINNAR!', 0, 10, 1),
(645, 'AS-RiverbedSE_preview.unr', 0, 'The cavern passage', 'has been breached!', 0, 9, 1),
(646, 'AS-RiverbedSE_preview.unr', 1, 'Main Computer', 'was destroyed!', 10, 9, 1),
(647, 'AS-RiverbedSE_preview.unr', 2, 'Control Panel', '', 153, 5, 1),
(648, 'AS-RiverbedSE_preview.unr', 3, 'The Main Entrance', 'has been reached!', 0, 9, 1),
(649, 'AS-RiverbedSE_preview.unr', 4, 'The Main Entrance', 'has been reached', 10, 9, 1),
(650, 'AS-RealAssault.unr', 0, 'The enemys documents', 'have been captured', 0, 10, 1),
(651, 'AS-RealAssault.unr', 1, 'The enemys ammunition', 'was blown up', 0, 10, 1),
(652, 'AS-RealAssault.unr', 2, 'The enemys radio', 'was destroyed!', 0, 10, 1),
(653, 'AS-RiverbedSE_preview2.unr', 0, 'The cavern passage', 'has been breached!', 0, 9, 1),
(654, 'AS-RiverbedSE_preview2.unr', 1, 'Main Computer', 'was destroyed!', 10, 9, 1),
(655, 'AS-RiverbedSE_preview2.unr', 2, 'Control Panel', '', 153, 5, 1),
(656, 'AS-RiverbedSE_preview2.unr', 3, 'The Main Entrance', 'has been reached!', 0, 9, 1),
(657, 'AS-RiverbedSE_preview2.unr', 4, 'The Main Entrance', 'has been reached', 10, 9, 1),
(658, 'AS-TheDungeon]l[beta2_e.unr', 0, 'Right Wall', 'has been destroyed!', 0, 10, 1),
(659, 'AS-TheDungeon]l[beta2_e.unr', 1, 'Left Wall', 'has been destroyed!', 0, 10, 1),
(660, 'AS-TheDungeon]l[beta2_e.unr', 2, 'Lake', 'has been reached!', 0, 10, 1),
(661, 'AS-TheDungeon]l[beta2_e.unr', 3, 'Falls', 'have been reached!', 0, 10, 1),
(662, 'AS-TheDungeon]l[beta2_e.unr', 4, 'Skull Lock', 'has been destroyed!', 0, 10, 1),
(663, 'AS-TheDungeon]l[beta2_e.unr', 5, 'Escape', 'WINNAR!', 0, 10, 1),
(664, 'AS-TheDungeon]l[beta2_e.unr', 6, 'Entrance hall', 'has passed!', 0, 10, 1),
(665, 'AS-iSEAL.unr', 0, 'Button 1', 'has been activated. Lock 1 Released!', 245, 10, 1),
(666, 'AS-iSEAL.unr', 1, 'Final Objective', 'was destroyed!', 100, 10, 1),
(667, 'AS-iSEAL.unr', 2, 'Button 2', 'has been activated. Lock 2 Released!', 200, 10, 1),
(668, 'AS-iSEAL.unr', 3, 'Energy Core 1', 'was destroyed!', 255, 10, 1),
(669, 'AS-iSEAL.unr', 4, 'Energy Core 2', 'was destroyed!', 250, 10, 1),
(670, 'AS-LavaFort][SE_preview.unr', 0, 'Fuse 1', 'has been destroyed!', 60, 10, 1),
(671, 'AS-LavaFort][SE_preview.unr', 1, 'Lava energy generator', 'was destroyed!', 10, 10, 1),
(672, 'AS-LavaFort][SE_preview.unr', 2, 'The attackers have arrived at', 'the Fort!', 80, 10, 1),
(673, 'AS-LavaFort][SE_preview.unr', 3, 'Fuse 2', 'has been destroyed!', 60, 10, 1),
(674, 'AS-LavaFort][SE_preview.unr', 4, 'The attackers have entered', 'the fort!', 50, 10, 1),
(675, 'AS-LavaFort][SE_preview.unr', 5, 'The attackers have entered', 'the lava cave!', 75, 10, 1),
(676, 'AS-AsthenosphereSE_preview.unr', 0, 'Docking Bay doors', 'have been opened!', 100, 10, 1),
(677, 'AS-AsthenosphereSE_preview.unr', 1, 'Cooling fan #1', 'has been destroyed!', 75, 10, 1),
(678, 'AS-AsthenosphereSE_preview.unr', 2, 'Escape pod', 'has been hijacked!', 0, 10, 1),
(679, 'AS-AsthenosphereSE_preview.unr', 3, 'Reactor room', 'has been breached!', 90, 10, 1),
(680, 'AS-AsthenosphereSE_preview.unr', 4, 'Air vents', 'have been breached!', 50, 10, 1),
(681, 'AS-AsthenosphereSE_preview.unr', 5, 'Cooling fan #2', 'has been destroyed!', 75, 10, 1),
(682, 'AS-AsthenosphereSE_preview.unr', 6, 'Observation Lounge access', 'has been granted!', 25, 10, 1),
(683, 'AS-BioassaultNA.unr', 0, 'Main Gate', 'was opened ! ! !', 0, 10, 1),
(684, 'AS-BioassaultNA.unr', 1, 'Attackers Entered', 'The Bridge!', 0, 10, 1),
(685, 'AS-BioassaultNA.unr', 2, 'Second Gate', 'Unlocked!', 0, 10, 1),
(686, 'AS-BioassaultNA.unr', 3, 'The Tunnel', 'has been reached!', 0, 10, 1),
(687, 'AS-BioassaultNA.unr', 4, 'Science corridor', 'has been reached.', 0, 10, 1),
(688, 'AS-BioassaultNA.unr', 5, 'Shooting Range', 'is Secure!', 0, 10, 1),
(689, 'AS-BioassaultNA.unr', 6, 'The Tunnel', 'Is Secure!', 0, 10, 1),
(690, 'AS-BioassaultNA.unr', 7, 'Bio-chamber', 'has been reached!', 0, 10, 1),
(691, 'AS-BioassaultNA.unr', 8, 'Bio-chamber', 'has become unstable.', 0, 11, 1),
(692, 'AS-LavaFort][SE_preview2.unr', 0, 'Fuse 1', 'has been destroyed!', 60, 10, 1),
(693, 'AS-LavaFort][SE_preview2.unr', 1, 'Lava energy generator', 'was destroyed!', 10, 10, 1),
(694, 'AS-LavaFort][SE_preview2.unr', 2, 'The attackers have arrived at', 'the Fort!', 80, 10, 1),
(695, 'AS-LavaFort][SE_preview2.unr', 3, 'Fuse 2', 'has been destroyed!', 60, 10, 1),
(696, 'AS-LavaFort][SE_preview2.unr', 4, 'The attackers have entered', 'the fort!', 50, 10, 1),
(697, 'AS-LavaFort][SE_preview2.unr', 5, 'The attackers have entered', 'the lava cave!', 75, 10, 1),
(698, 'AS-AsthenosphereSE_preview2c.unr', 0, 'Docking Bay doors', 'have been opened!', 100, 10, 1),
(699, 'AS-AsthenosphereSE_preview2c.unr', 1, 'Cooling fan #1', 'has been destroyed!', 75, 10, 1),
(700, 'AS-AsthenosphereSE_preview2c.unr', 2, 'Escape pod', 'has been hijacked!', 0, 10, 1),
(701, 'AS-AsthenosphereSE_preview2c.unr', 3, 'Reactor room', 'has been breached!', 90, 10, 1),
(702, 'AS-AsthenosphereSE_preview2c.unr', 4, 'Air vents', 'have been breached!', 50, 10, 1),
(703, 'AS-AsthenosphereSE_preview2c.unr', 5, 'Cooling fan #2', 'has been destroyed!', 75, 10, 1),
(704, 'AS-AsthenosphereSE_preview2c.unr', 6, 'Observation Lounge access', 'has been granted!', 25, 10, 1),
(705, 'AS-Tydium][AL_preview2.unr', 0, 'Cave-door key', 'has been stolen!', 4, 10, 1),
(706, 'AS-Tydium][AL_preview2.unr', 1, 'Cannonball', 'has been taken.', 3, 10, 1),
(707, 'AS-Tydium][AL_preview2.unr', 2, 'Underground', 'breached!', 1, 10, 1),
(708, 'AS-Tydium][AL_preview2.unr', 3, 'The Catapault', 'is under enemy control', 2, 10, 1),
(709, 'AS-Tydium][AL_preview2.unr', 4, 'Crystal', 'was destroyed!', 0, 10, 1),
(710, 'AS-Tydium][AL_preview2.unr', 5, 'Crystal Room doors', 'are about to open...', 0, 10, 1),
(711, 'AS-Tydium][AL_preview2.unr', 6, 'Underpass', 'has been passed!', 0, 10, 1),
(712, 'AS-Vampire.unr', 0, 'The Switch', 'was pushed!', 253, 10, 1),
(713, 'AS-Vampire.unr', 1, 'Water Wheel', 'started rotating!', 254, 10, 1),
(714, 'AS-Vampire.unr', 2, 'Barricade', 'was destroyed!', 255, 10, 1),
(715, 'AS-Vampire.unr', 3, 'The Chain', 'was destroyed!', 251, 10, 1),
(716, 'AS-Vampire.unr', 4, 'Door Lock', 'was destroyed!', 252, 10, 1),
(717, 'AS-300kTheHill.unr', 0, 'target1', 'target 1 reached', 0, 10, 1),
(718, 'AS-300kTheHill.unr', 1, 'target2', 'target2 was reached', 0, 10, 1),
(719, 'AS-300kTheHill.unr', 2, 'target3', 'target3 was reached', 0, 10, 1),
(720, 'AS-300kTheHill.unr', 3, 'target4', 'finall target reached', 0, 10, 1),
(721, 'AS-300k-OrangeJuice.unr', 0, 'Prison Door', 'was opened!', 0, 20, 1),
(722, 'AS-300k-OrangeJuice.unr', 1, 'The Prisoners', 'have escaped!', 0, 20, 1),
(723, 'AS-300k-OrangeJuice.unr', 2, 'Lift Controls', 'were activated!', 0, 20, 1),
(724, 'AS-UrbanWars2.unr', 0, 'Final Objective', 'was destroyed!', 1, 10, 1),
(725, 'AS-UrbanWars2.unr', 1, 'Electrical fence', 'deactivated!', 5, 10, 1),
(726, 'AS-UrbanWars2.unr', 2, 'The cargo crane', 'is alredy operating! You''ve come too late!', 2, 10, 1),
(727, 'AS-UrbanWars2.unr', 3, 'Crane', 'was manipulated!', 7, 10, 1),
(728, 'AS-UrbanWars2.unr', 4, 'Subway Station', 'was entered!', 6, 10, 1),
(729, 'AS-UrbanWars2.unr', 5, 'Satellite information', 'was saved to communicator!', 4, 10, 1),
(730, 'AS-UrbanWars2.unr', 6, 'The Dockland Tunnel', 'was crossed!', 3, 10, 1),
(731, 'AS-300k-Areals.unr', 0, 'Deity', 'destroyed!', 9, 10, 1),
(732, 'AS-300k-Areals.unr', 1, 'Deity', 'destroyed!', 9, 10, 1),
(733, 'AS-300k-Areals.unr', 10, 'lever6', 'pulled!', 7, 10, 1),
(734, 'AS-300k-Areals.unr', 11, 'lever5', 'pulled!', 7, 10, 1),
(735, 'AS-300k-Areals.unr', 12, 'Final Objective', 'was destroyed!', 0, 15, 1),
(736, 'AS-300k-Areals.unr', 13, 'It', 'has been beckoned!!!', 0, 10, 1),
(737, 'AS-300k-Areals.unr', 2, 'Deity', 'destroyed!', 9, 10, 1),
(738, 'AS-300k-Areals.unr', 3, 'Deity', 'destroyed!', 9, 10, 1),
(739, 'AS-300k-Areals.unr', 4, 'Deity', 'destroyed!', 9, 10, 1),
(740, 'AS-300k-Areals.unr', 5, 'Diamond Chamber', 'door has been opened!', 10, 10, 1),
(741, 'AS-300k-Areals.unr', 6, 'Lever 1', 'pulled!', 7, 10, 1),
(742, 'AS-300k-Areals.unr', 7, 'Lever 2', 'pulled!', 7, 10, 1),
(743, 'AS-300k-Areals.unr', 8, 'lever3', 'pulled!', 7, 10, 1),
(744, 'AS-300k-Areals.unr', 9, 'lever4', 'pulled!', 7, 10, 1),
(745, 'AS-Wolf.unr', 0, 'Front Bunker', 'door is open!', 0, 10, 1),
(746, 'AS-Wolf.unr', 1, 'Prototype Eagle', 'Is Stolen!', 0, 10, 1),
(747, 'AS-Wolf.unr', 2, 'Transport Central', 'Door Is Open!', 0, 10, 1),
(748, 'AS-Wolf.unr', 3, 'Transport Central', 'Is Secure!', 0, 10, 1),
(749, 'AS-Wolf.unr', 4, 'Big Bertha', 'Is Secure!', 0, 10, 1),
(750, 'AS-Wolf.unr', 5, 'BattleTank Wolf', 'In Sight!', 0, 10, 1),
(751, 'AS-Wolf.unr', 6, 'Wolf', 'Is Stolen!', 0, 16, 1),
(752, 'AS-300k-FNBXYZ.unr', 0, 'Frag', 'was Fraged!', 240, 5, 1),
(753, 'AS-300k-FNBXYZ.unr', 1, 'X', 'was captured!', 255, 5, 1),
(754, 'AS-300k-FNBXYZ.unr', 2, 'ASTrophy', 'was taken!', 230, 5, 1),
(755, 'AS-300k-FNBXYZ.unr', 3, 'Champion', 'was taken!', 230, 10, 1),
(756, 'AS-UrbanWars.unr', 0, 'Atomic Warhead', 'was deactivared!', 1, 10, 1),
(757, 'AS-UrbanWars.unr', 1, 'Keycode', 'was discovered!', 6, 10, 1),
(758, 'AS-UrbanWars.unr', 2, 'Second park gate', 'was opened!', 5, 10, 1),
(759, 'AS-UrbanWars.unr', 3, 'Power', 'was turned down!', 4, 10, 1),
(760, 'AS-UrbanWars.unr', 4, 'Laptop', 'was cracked!', 3, 10, 1),
(761, 'AS-UrbanWars.unr', 5, 'Disc', 'was stolen!', 2, 10, 1),
(762, 'AS-hellraiser[beta1].unr', 0, 'The Fortress', 'was breached!', 40, 13, 1),
(763, 'AS-hellraiser[beta1].unr', 1, 'The lament configuration puzzlebox', 'was taken!', 30, 13, 1),
(764, 'AS-hellraiser[beta1].unr', 10, 'Death, distruction, pain and torture', 'is now ended', 5, 13, 1),
(765, 'AS-hellraiser[beta1].unr', 2, 'Lament door lock', 'was released!', 35, 13, 1),
(766, 'AS-hellraiser[beta1].unr', 3, 'Puzzlebox', 'was placed !', 25, 13, 1),
(767, 'AS-hellraiser[beta1].unr', 4, 'Second Chain', 'was destroyed!', 49, 13, 1),
(768, 'AS-hellraiser[beta1].unr', 5, 'Outside', 'is reached!', 20, 13, 1),
(769, 'AS-hellraiser[beta1].unr', 6, 'First chain', 'was destroyed!', 50, 13, 1),
(770, 'AS-hellraiser[beta1].unr', 7, 'Highergrounds', 'Reached', 45, 13, 1),
(771, 'AS-hellraiser[beta1].unr', 8, 'Closer Attackerspawn', 'Enabled', 15, 13, 1),
(772, 'AS-hellraiser[beta1].unr', 9, 'Defenders wall', 'released', 10, 13, 1),
(773, 'AS-TheDungeon]l[AL_beta2.unr', 0, 'Right Wall', 'has been destroyed!', 0, 10, 1),
(774, 'AS-TheDungeon]l[AL_beta2.unr', 1, 'Left Wall', 'has been destroyed!', 0, 10, 1),
(775, 'AS-TheDungeon]l[AL_beta2.unr', 2, 'Lake', 'has been reached!', 0, 10, 1),
(776, 'AS-TheDungeon]l[AL_beta2.unr', 3, 'Falls', 'have been reached!', 0, 10, 1),
(777, 'AS-TheDungeon]l[AL_beta2.unr', 4, 'Skull Lock', 'has been destroyed!', 0, 10, 1),
(778, 'AS-TheDungeon]l[AL_beta2.unr', 5, 'Escape', 'WINNAR!', 0, 10, 1),
(779, 'AS-TheDungeon]l[AL_beta2.unr', 6, 'Entrance hall', 'has passed!', 0, 10, 1),
(780, 'AS-SaqqaraSE.unr', 0, 'Green chamber switch', 'has been activated!', 2, 3, 1),
(781, 'AS-SaqqaraSE.unr', 1, 'Yellow chamber switch', 'has been activated!', 2, 3, 1),
(782, 'AS-SaqqaraSE.unr', 2, 'Red chamber switch', 'has been activated!', 1, 7, 1),
(783, 'AS-SaqqaraSE.unr', 3, 'Blue chamber switch', 'has been activated!', 1, 7, 1),
(784, 'AS-SaqqaraSE.unr', 4, 'Waterfall', 'was breached!', 0, 9, 1),
(785, 'AS-SaqqaraSE.unr', 5, 'Scarab Doors', 'has been breached!', 0, 7, 1),
(786, 'AS-SaqqaraSE.unr', 6, 'Pyramid', 'has been breached!', 2, 3, 1),
(787, 'AS-SaqqaraSE.unr', 7, 'Waterfall', 'was breached!', 0, 9, 1),
(788, 'AS-OverlordDE.unr', 0, 'Gun control', 'destroyed!', 0, 5, 1),
(789, 'AS-OverlordDE.unr', 1, 'Boiler room', 'reached!', 1, 7, 1),
(790, 'AS-OverlordDE.unr', 2, 'Beachhead', 'breached!', 2, 10, 1),
(791, 'AS-LostTempleBetaV1.unr', 0, 'Bridge', 'Has Been Crossed', 2, 10, 1),
(792, 'AS-LostTempleBetaV1.unr', 1, 'Chain 2', 'Has Been Destroyed', 4, 10, 1),
(793, 'AS-LostTempleBetaV1.unr', 2, 'Chain 1', 'Has Been Destroyed', 3, 10, 1),
(794, 'AS-LostTempleBetaV1.unr', 3, 'Temple', 'Has Been Entered', 1, 10, 1),
(795, 'AS-LostTempleBetaV1.unr', 4, 'The tomb', 'is opening!', 5, 10, 1),
(796, 'AS-LostTempleBetaV1.unr', 5, 'All the beer', 'belongs to Des!', 7, 10, 1),
(797, 'AS-LostTempleBetaV1.unr', 6, 'Treasure Chest', 'has been stolen!', 6, 10, 1),
(798, 'AS-LostTempleBetaV2.unr', 0, 'Bridge', 'Has Been Crossed', 6, 10, 1),
(799, 'AS-LostTempleBetaV2.unr', 1, 'Chain 2', 'Has Been Destroyed', 4, 10, 1),
(800, 'AS-LostTempleBetaV2.unr', 2, 'Chain 1', 'Has Been Destroyed', 5, 10, 1),
(801, 'AS-LostTempleBetaV2.unr', 3, 'Temple', 'Has Been Entered', 7, 10, 1),
(802, 'AS-LostTempleBetaV2.unr', 4, 'The tomb', 'is opening!', 3, 10, 1),
(803, 'AS-LostTempleBetaV2.unr', 5, 'All the beer', 'belongs to Des!', 1, 10, 1),
(804, 'AS-LostTempleBetaV2.unr', 6, 'Treasure Chest', 'has been stolen!', 2, 10, 1),
(805, 'AS-BioassaultSE_preview2.unr', 0, 'Main gate', 'Lock has been destroyed.', 0, 10, 1),
(806, 'AS-BioassaultSE_preview2.unr', 1, 'Second gate', 'unlocked!', 0, 9, 1),
(807, 'AS-BioassaultSE_preview2.unr', 10, 'Bridge Entrance', 'has been breached!', 0, 14, 1),
(808, 'AS-BioassaultSE_preview2.unr', 2, 'Bridge', 'under control !', 0, 12, 1),
(809, 'AS-BioassaultSE_preview2.unr', 3, 'Science corridor', 'has been reached !', 0, 8, 1),
(810, 'AS-BioassaultSE_preview2.unr', 4, 'Shooting range', 'is secure!', 0, 7, 1),
(811, 'AS-BioassaultSE_preview2.unr', 5, 'The tunnel', 'is secure!', 0, 11, 1),
(812, 'AS-BioassaultSE_preview2.unr', 6, 'Bio-chamber', 'has been reached!', 0, 6, 1),
(813, 'AS-BioassaultSE_preview2.unr', 7, 'Bio-chamber', 'has become unstable.', 0, 5, 1),
(814, 'AS-BioassaultSE_preview2.unr', 8, 'Escape!', '', 0, 3, 1),
(815, 'AS-BioassaultSE_preview2.unr', 9, 'Escape', 'tunnel reached!', 0, 4, 1),
(816, 'AS-Tydium][AL_preview4.unr', 0, 'Cave-door key', 'has been stolen!', 4, 10, 1),
(817, 'AS-Tydium][AL_preview4.unr', 1, 'Cannonball', 'has been taken.', 3, 10, 1),
(818, 'AS-Tydium][AL_preview4.unr', 2, 'Underground', 'breached!', 1, 10, 1),
(819, 'AS-Tydium][AL_preview4.unr', 3, 'The Catapault', 'is under enemy control', 2, 10, 1),
(820, 'AS-Tydium][AL_preview4.unr', 4, 'Crystal', 'was destroyed!', 0, 10, 1),
(821, 'AS-Tydium][AL_preview4.unr', 5, 'Crystal Room doors', 'are about to open...', 0, 10, 1),
(822, 'AS-Tydium][AL_preview4.unr', 6, 'Underpass', 'has been passed!', 88, 10, 1),
(823, 'AS-Subbase][SE_preview.unr', 0, 'Attackers', 'entered the base!', 0, 10, 1),
(824, 'AS-Subbase][SE_preview.unr', 1, 'Gate', 'is open!', 0, 10, 1),
(825, 'AS-Subbase][SE_preview.unr', 2, 'Docks', 'are reached!', 0, 10, 1),
(826, 'AS-Subbase][SE_preview.unr', 3, 'Panel', 'was destroyed!', 0, 10, 1),
(827, 'AS-Subbase][SE_preview.unr', 4, 'Submarine', 'entered!', 0, 10, 1),
(828, 'AS-Subbase][SE_preview.unr', 5, 'Ventilation', 'access granted!', 0, 10, 1),
(829, 'AS-DustbowlALbeta.unr', 0, 'Gate Switch', 'has been pushed.', 0, 10, 1),
(830, 'AS-DustbowlALbeta.unr', 1, 'Fuses', 'have been destroyed!', 60, 10, 1),
(831, 'AS-DustbowlALbeta.unr', 2, 'Innerbase', 'perimeters have been reached!', 50, 10, 1),
(832, 'AS-DustbowlALbeta.unr', 3, 'Perimeter lasers', 'have been disabled.', 0, 10, 1),
(833, 'AS-DustbowlALbeta.unr', 4, 'Generator 2', 'has been disabled!', 60, 10, 1),
(834, 'AS-DustbowlALbeta.unr', 5, 'Generator 1', 'has been disabled!', 60, 10, 1),
(835, 'AS-DustbowlALbeta.unr', 6, 'Laser control', 'has been pushed.', 0, 10, 1),
(836, 'AS-DustbowlALbeta.unr', 7, 'Base control', 'has been breached!', 50, 10, 1),
(837, 'AS-DustbowlALbeta.unr', 8, 'Main Gate', 'lasers shutting down!', 0, 10, 1),
(838, 'AS-DustbowlALbeta.unr', 9, 'Control Centre', 'has been disabled!', 0, 12, 1),
(839, 'AS-DustbowlALbeta2.unr', 0, 'Gate Switch', 'has been pushed.', 0, 10, 1),
(840, 'AS-DustbowlALbeta2.unr', 1, 'Fuses', 'have been destroyed!', 60, 10, 1),
(841, 'AS-DustbowlALbeta2.unr', 2, 'Innerbase', 'perimeters have been reached!', 50, 10, 1),
(842, 'AS-DustbowlALbeta2.unr', 3, 'Perimeter lasers', 'have been disabled.', 0, 10, 1),
(843, 'AS-DustbowlALbeta2.unr', 4, 'Generator 2', 'has been disabled!', 60, 10, 1),
(844, 'AS-DustbowlALbeta2.unr', 5, 'Generator 1', 'has been disabled!', 60, 10, 1),
(845, 'AS-DustbowlALbeta2.unr', 6, 'Laser control', 'has been pushed.', 0, 10, 1),
(846, 'AS-DustbowlALbeta2.unr', 7, 'Base control', 'has been breached!', 50, 10, 1),
(847, 'AS-DustbowlALbeta2.unr', 8, 'Main Gate', 'lasers shutting down!', 0, 10, 1),
(848, 'AS-DustbowlALbeta2.unr', 9, 'Control Centre', 'has been disabled!', 0, 12, 1),
(849, 'AS-TheDungeon]l[AL.unr', 0, 'Right Wall', 'has been destroyed!', 0, 10, 1),
(850, 'AS-TheDungeon]l[AL.unr', 1, 'Left Wall', 'has been destroyed!', 0, 10, 1),
(851, 'AS-TheDungeon]l[AL.unr', 2, 'Lake', 'has been reached!', 0, 10, 1),
(852, 'AS-TheDungeon]l[AL.unr', 3, 'Falls', 'have been reached!', 0, 10, 1),
(853, 'AS-TheDungeon]l[AL.unr', 4, 'Skull Lock', 'has been destroyed!', 0, 10, 1),
(854, 'AS-TheDungeon]l[AL.unr', 5, 'Escape', 'WINNAR!', 0, 10, 1),
(855, 'AS-TheDungeon]l[AL.unr', 6, 'Entrance hall', 'has passed!', 0, 10, 1),
(856, 'AS-DustbowlALbeta3.unr', 0, 'Gate Switch', 'has been pushed.', 0, 10, 1),
(857, 'AS-DustbowlALbeta3.unr', 1, 'Fuses', 'have been destroyed!', 60, 10, 1),
(858, 'AS-DustbowlALbeta3.unr', 2, 'Innerbase', 'perimeters have been reached!', 50, 10, 1),
(859, 'AS-DustbowlALbeta3.unr', 3, 'Perimeter lasers', 'have been disabled.', 0, 10, 1),
(860, 'AS-DustbowlALbeta3.unr', 4, 'Generator 2', 'has been disabled!', 60, 10, 1),
(861, 'AS-DustbowlALbeta3.unr', 5, 'Generator 1', 'has been disabled!', 60, 10, 1),
(862, 'AS-DustbowlALbeta3.unr', 6, 'Laser control', 'has been pushed.', 0, 10, 1),
(863, 'AS-DustbowlALbeta3.unr', 7, 'Base control', 'has been breached!', 50, 10, 1),
(864, 'AS-DustbowlALbeta3.unr', 8, 'Main Gate', 'lasers shutting down!', 0, 10, 1),
(865, 'AS-DustbowlALbeta3.unr', 9, 'Control Centre', 'has been disabled!', 0, 12, 1),
(866, 'AS-Navarone_019.unr', 0, 'Charge 3', 'Placed!', 0, 12, 1),
(867, 'AS-Navarone_019.unr', 1, 'Demolition Charge 1', 'placed!', 0, 10, 1),
(868, 'AS-Navarone_019.unr', 2, 'Demolition Charge 2', 'placed!', 0, 10, 1),
(869, 'AS-Navarone_019.unr', 3, 'Lower Passage', 'Unlocked!', 0, 10, 1),
(870, 'AS-Navarone_019.unr', 4, 'Supplies', 'collected!', 0, 10, 1),
(871, 'AS-Navarone_019.unr', 5, 'Elevator', 'reached!', 0, 10, 1),
(872, 'AS-Navarone_020.unr', 0, 'Charge 3', 'Placed!', 0, 12, 1),
(873, 'AS-Navarone_020.unr', 1, 'Demolition Charge 1', 'placed!', 0, 10, 1),
(874, 'AS-Navarone_020.unr', 2, 'Demolition Charge 2', 'placed!', 0, 10, 1),
(875, 'AS-Navarone_020.unr', 3, 'Lower Passage', 'Unlocked!', 0, 10, 1),
(876, 'AS-Navarone_020.unr', 4, 'Supplies', 'collected!', 0, 10, 1),
(877, 'AS-Navarone_020.unr', 5, 'Elevator', 'reached!', 0, 10, 1),
(878, 'AS-SnowDunesAL_pre-Final2.unr', 0, 'Ship', 'Captured', 0, 10, 1),
(879, 'AS-SnowDunesAL_pre-Final2.unr', 1, 'Second Level Doors', 'Unlocked!', 4, 10, 1),
(880, 'AS-SnowDunesAL_pre-Final2.unr', 2, 'Tower Door', 'Opening', 2, 10, 1),
(881, 'AS-SnowDunesAL_pre-Final2.unr', 3, 'Ship Dock Lasers', 'Are About To Be Deactivated!!', 3, 10, 1),
(882, 'AS-SnowDunesAL_pre-Final2.unr', 4, 'Radar', 'Disabled!', 5, 10, 1),
(883, 'AS-SnowDunesAL_pre-Final2.unr', 5, 'Energy Cell 1', 'was destroyed!', 1, 10, 1),
(884, 'AS-SnowDunesAL_pre-Final2.unr', 6, 'Energy Cell 2', 'was destroyed!', 1, 10, 1),
(885, 'AS-AutoRipSE_preview.unr', 0, 'Despatch Doors', 'have been opened!', 15, 7, 1),
(886, 'AS-AutoRipSE_preview.unr', 1, 'Bomb', 'was planted!', 5, 7, 1),
(887, 'AS-AutoRipSE_preview.unr', 2, 'Development Area', 'is accessible!', 10, 7, 1),
(888, 'AS-AutoRipSE_preview.unr', 3, 'Defence Door Control', 'was destroyed!', 12, 10, 1),
(889, 'AS-FrigateS_preview.unr', 0, 'Ship 1', 'has been entered!', 50, 6, 1),
(890, 'AS-FrigateS_preview.unr', 1, 'Missiles', 'were launched!', 10, 10, 1),
(891, 'AS-FrigateS_preview.unr', 2, 'The hydraulic compressor', 'was destroyed!', 25, 6, 1),
(892, 'AS-FrigateS_preview.unr', 3, 'First guns', 'was activated!', 0, 6, 1),
(893, 'AS-FrigateS_preview.unr', 4, 'Ship 2', 'has been entered!', 0, 8, 1),
(894, 'AS-AutoRipSE_preview2.unr', 0, 'Despatch Doors', 'have been opened!', 15, 7, 1),
(895, 'AS-AutoRipSE_preview2.unr', 1, 'Bomb', 'was planted!', 5, 7, 1),
(896, 'AS-AutoRipSE_preview2.unr', 2, 'Development Area', 'is accessible!', 10, 7, 1),
(897, 'AS-AutoRipSE_preview2.unr', 3, 'Defence Door Control', 'was destroyed!', 12, 10, 1),
(898, 'AS-RocketCommandFUN.unr', 0, 'Fort Entrance', 'has been breached!', 100, 14, 1),
(899, 'AS-RocketCommandFUN.unr', 1, 'First gate', 'is opening!', 90, 14, 1),
(900, 'AS-RocketCommandFUN.unr', 2, 'Second gate', 'will open in 10 seconds!', 90, 14, 1),
(901, 'AS-RocketCommandFUN.unr', 3, 'Anti-air base 1', 'has been scrambled!', 80, 14, 1),
(902, 'AS-RocketCommandFUN.unr', 4, 'Anti-air base 2', 'has been scrambled!', 80, 14, 1),
(903, 'AS-RocketCommandFUN.unr', 5, 'The sewers', 'have been breached!', 80, 14, 1),
(904, 'AS-RocketCommandFUN.unr', 6, 'Base entrance terminal', 'was destroyed!', 70, 14, 1),
(905, 'AS-FrigateS_preview3.unr', 0, 'Ship 1', 'has been entered!', 50, 6, 1),
(906, 'AS-FrigateS_preview3.unr', 1, 'Missiles', 'were launched!', 10, 10, 1),
(907, 'AS-FrigateS_preview3.unr', 2, 'The hydraulic compressor', 'was destroyed!', 25, 6, 1),
(908, 'AS-FrigateS_preview3.unr', 3, 'First guns', 'was activated!', 0, 6, 1),
(909, 'AS-FrigateS_preview3.unr', 4, 'Ship 2', 'has been entered!', 0, 8, 1),
(910, 'AS-DustbowlALRev03.unr', 0, 'Gate Switch', 'has been pushed.', 0, 10, 1),
(911, 'AS-DustbowlALRev03.unr', 1, 'Fuses', 'have been destroyed!', 60, 10, 1),
(912, 'AS-DustbowlALRev03.unr', 2, 'Innerbase', 'perimeters have been reached!', 50, 10, 1),
(913, 'AS-DustbowlALRev03.unr', 3, 'Perimeter lasers', 'have been disabled.', 0, 10, 1),
(914, 'AS-DustbowlALRev03.unr', 4, 'Generator 2', 'has been disabled!', 60, 10, 1),
(915, 'AS-DustbowlALRev03.unr', 5, 'Generator 1', 'has been disabled!', 60, 10, 1),
(916, 'AS-DustbowlALRev03.unr', 6, 'Laser control', 'has been pushed.', 0, 10, 1),
(917, 'AS-DustbowlALRev03.unr', 7, 'Base control', 'has been breached!', 50, 10, 1),
(918, 'AS-DustbowlALRev03.unr', 8, 'Main Gate', 'lasers shutting down!', 0, 10, 1),
(919, 'AS-DustbowlALRev03.unr', 9, 'Control Centre', 'has been disabled!', 0, 12, 1),
(920, 'AS-Gladiator][.unr', 0, 'Bridge', 'is reached!', 16, 10, 1),
(921, 'AS-Gladiator][.unr', 1, 'The wall', 'is reached!', 15, 10, 1),
(922, 'AS-Gladiator][.unr', 10, 'The Roman Temple', 'was entered!', 1, 10, 1),
(923, 'AS-Gladiator][.unr', 11, 'Ancient Warrior', 'was killed and a key found!', 13, 10, 1),
(924, 'AS-Gladiator][.unr', 2, 'Sword Gladius', 'was found!', 14, 10, 1),
(925, 'AS-Gladiator][.unr', 3, 'Box Barricade', 'was destroyed!', 11, 10, 1),
(926, 'AS-Gladiator][.unr', 4, 'Switch One', 'was pushed!', 8, 10, 1),
(927, 'AS-Gladiator][.unr', 5, 'Switch Two', 'was pushed!', 9, 10, 1),
(928, 'AS-Gladiator][.unr', 6, 'Final Objective', 'was destroyed!', 0, 25, 1),
(929, 'AS-Gladiator][.unr', 7, 'Lava Bridge', 'was crossed!', 10, 10, 1),
(930, 'AS-Gladiator][.unr', 8, 'The First Gate', 'was opened!', 12, 10, 1),
(931, 'AS-Gladiator][.unr', 9, 'Radio', 'was used to call for air support!', 2, 10, 1),
(932, 'AS-TheScarabSE_betaIV.unr', 0, 'Hanger Doors', 'opening!', 25, 10, 1),
(933, 'AS-TheScarabSE_betaIV.unr', 1, 'Enemy Base', 'entered!', 35, 10, 1),
(934, 'AS-TheScarabSE_betaIV.unr', 2, 'Ships Engines', 'activated!', 15, 10, 1),
(935, 'AS-TheScarabSE_betaIV.unr', 3, 'Tower Door', 'unlocked!', 0, 10, 1),
(936, 'AS-TheScarabSE_betaIV.unr', 4, 'The Scarab', 'has been stolen!', 5, 10, 1),
(937, 'AS-TheScarabSE_betaIV.unr', 5, 'Shield Control', 'has been activated!', 40, 10, 1),
(938, 'AS-TheScarabSE_betaV.unr', 0, 'Hanger Doors', 'opening!', 25, 10, 1),
(939, 'AS-TheScarabSE_betaV.unr', 1, 'Enemy Base', 'entered!', 35, 10, 1),
(940, 'AS-TheScarabSE_betaV.unr', 2, 'Ships Engines', 'activated!', 15, 10, 1),
(941, 'AS-TheScarabSE_betaV.unr', 3, 'Tower Door', 'unlocked!', 0, 10, 1),
(942, 'AS-TheScarabSE_betaV.unr', 4, 'The Scarab', 'has been stolen!', 5, 10, 1),
(943, 'AS-TheScarabSE_betaV.unr', 5, 'Shield Control', 'has been activated!', 40, 10, 1),
(1127, 'AS-WorseThings_043.unr', 5, 'Control Pump', 'deactivated!', 40, 10, 1),
(1126, 'AS-WorseThings_043.unr', 4, 'Attackers Spawn Point', 'has advanced!', 30, 10, 1),
(1125, 'AS-WorseThings_043.unr', 3, 'Drilling Machine', 'activated!', 15, 10, 1),
(1124, 'AS-WorseThings_043.unr', 2, 'Oil Pit', 'has been drained!', 20, 10, 1),
(1123, 'AS-WorseThings_043.unr', 1, 'Lower Side Hatch', 'has been opened!', 0, 10, 1),
(1122, 'AS-WorseThings_043.unr', 0, 'Borehole', 'was destroyed!', 10, 10, 1),
(1121, 'AS-WorseThings_030.unr', 7, 'Attackers', 'escaped!', 0, 10, 1),
(1120, 'AS-WorseThings_030.unr', 6, 'Top Side Hatch', 'has been opened!', 31, 10, 1),
(1119, 'AS-WorseThings_030.unr', 5, 'Control Pump', 'deactivated!', 40, 10, 1),
(1118, 'AS-WorseThings_030.unr', 4, 'Attackers Spawn Point', 'has advanced!', 30, 10, 1),
(1117, 'AS-WorseThings_030.unr', 3, 'Drilling Machine', 'activated!', 15, 10, 1),
(1116, 'AS-WorseThings_030.unr', 2, 'Oil Pit', 'has been drained!', 20, 10, 1),
(1115, 'AS-WorseThings_030.unr', 1, 'Lower Side Hatch', 'has been opened!', 0, 10, 1),
(1114, 'AS-WorseThings_030.unr', 0, 'Borehole', 'was destroyed!', 10, 10, 1),
(1113, 'AS-DustbowlALRev04.unr', 9, 'Control Centre', 'has been disabled!', 0, 12, 1),
(1112, 'AS-DustbowlALRev04.unr', 8, 'Main Gate', 'lasers shutting down!', 0, 10, 1),
(1111, 'AS-DustbowlALRev04.unr', 7, 'Base control', 'has been breached!', 50, 10, 1),
(1110, 'AS-DustbowlALRev04.unr', 6, 'Laser control', 'has been pushed.', 0, 10, 1),
(1109, 'AS-DustbowlALRev04.unr', 5, 'Generator 1', 'has been disabled!', 60, 10, 1),
(1108, 'AS-DustbowlALRev04.unr', 4, 'Generator 2', 'has been disabled!', 60, 10, 1),
(1107, 'AS-DustbowlALRev04.unr', 3, 'Perimeter lasers', 'have been disabled.', 0, 10, 1),
(1106, 'AS-DustbowlALRev04.unr', 2, 'Innerbase', 'perimeters have been reached!', 50, 10, 1),
(1105, 'AS-DustbowlALRev04.unr', 1, 'Fuses', 'have been destroyed!', 60, 10, 1),
(1104, 'AS-DustbowlALRev04.unr', 0, 'Gate Switch', 'has been pushed.', 0, 10, 1),
(1103, 'AS-WorseThings_023.unr', 7, 'Attackers', 'escaped!', 0, 10, 1),
(1102, 'AS-WorseThings_023.unr', 6, 'Top Side Hatch', 'has been opened!', 31, 10, 1),
(992, 'AS-OrbitalDE_001.unr', 0, 'Blast Door Fuse 1', 'was destroyed!', 220, 10, 1),
(993, 'AS-OrbitalDE_001.unr', 1, 'Reactor Control 1', 'was de-activated!', 150, 10, 1),
(994, 'AS-OrbitalDE_001.unr', 2, 'Reactor Control 2', 'was de-activated!', 150, 10, 1),
(995, 'AS-OrbitalDE_001.unr', 3, 'Reactor Control 3', 'was de-activated!', 150, 10, 1),
(996, 'AS-OrbitalDE_001.unr', 4, 'Reactor Control 4', 'was de-activated!', 150, 10, 1),
(997, 'AS-OrbitalDE_001.unr', 5, 'Blast Door Fuse 2', 'was destroyed!', 220, 10, 1),
(998, 'AS-OrbitalDE_001.unr', 7, 'centreblock1', '', 120, 10, 1),
(999, 'AS-OrbitalDE_005.unr', 0, 'Blast Door Fuse 1', 'was destroyed!', 220, 10, 1),
(1000, 'AS-OrbitalDE_005.unr', 1, 'Reactor Control 1', 'was de-activated!', 150, 10, 1),
(1001, 'AS-OrbitalDE_005.unr', 2, 'Reactor Control 2', 'was de-activated!', 150, 10, 1),
(1002, 'AS-OrbitalDE_005.unr', 3, 'Reactor Control 3', 'was de-activated!', 150, 10, 1),
(1003, 'AS-OrbitalDE_005.unr', 4, 'Reactor Control 4', 'was de-activated!', 150, 10, 1),
(1004, 'AS-OrbitalDE_005.unr', 5, 'Blast Door Fuse 2', 'was destroyed!', 220, 10, 1),
(1005, 'AS-OrbitalDE_005.unr', 7, 'centreblock1', '', 120, 10, 1),
(1101, 'AS-WorseThings_023.unr', 5, 'Control Pump', 'deactivated!', 40, 10, 1),
(1100, 'AS-WorseThings_023.unr', 4, 'Attackers Spawn Point', 'has advanced!', 30, 10, 1),
(1099, 'AS-WorseThings_023.unr', 3, 'Drilling Machine', 'activated!', 15, 10, 1),
(1098, 'AS-WorseThings_023.unr', 2, 'Oil Pit', 'has been drained!', 20, 10, 1),
(1097, 'AS-WorseThings_023.unr', 1, 'Lower Side Hatch', 'has been opened!', 0, 10, 1),
(1096, 'AS-WorseThings_023.unr', 0, 'Borehole', 'was destroyed!', 10, 10, 1),
(1095, 'AS-TheScarabSE_betaVl.unr', 5, 'Shield Control', 'has been hacked!', 40, 10, 1),
(1094, 'AS-TheScarabSE_betaVl.unr', 4, 'The Scarab', 'has been stolen!', 5, 10, 1),
(1093, 'AS-TheScarabSE_betaVl.unr', 3, 'Sniper tower Door', 'unlocked!', 0, 10, 1),
(1092, 'AS-TheScarabSE_betaVl.unr', 2, 'Ships Engines', 'activated!', 15, 10, 1),
(1091, 'AS-TheScarabSE_betaVl.unr', 1, 'Enemy Base', 'entered!', 35, 10, 1),
(1090, 'AS-TheScarabSE_betaVl.unr', 0, 'Hanger Doors', 'opening!', 25, 10, 1),
(1089, 'AS-B_497.unr', 4, 'Attackers escaped :P', 'was hacked!', 0, 12, 1),
(1088, 'AS-B_497.unr', 3, 'Asgard core', 'was stolen!', 0, 10, 1),
(1087, 'AS-B_497.unr', 2, 'Generator', 'was deactivated!', 0, 10, 1),
(1030, 'AS-SaqqaraSG1beta20.unr', 0, 'Green chamber switch', 'has been activated!', 2, 12, 1),
(1031, 'AS-SaqqaraSG1beta20.unr', 1, 'Yellow chamber switch', 'has been activated!', 2, 12, 1),
(1032, 'AS-SaqqaraSG1beta20.unr', 2, 'Red chamber switch', 'has been activated!', 1, 12, 1),
(1033, 'AS-SaqqaraSG1beta20.unr', 3, 'Blue chamber switch', 'has been activated!', 1, 12, 1),
(1034, 'AS-SaqqaraSG1beta20.unr', 4, 'Waterfall', 'was breached!', 0, 12, 1),
(1035, 'AS-SaqqaraSG1beta20.unr', 5, 'Scarab Doors', 'has been breached!', 0, 12, 1),
(1036, 'AS-SaqqaraSG1beta20.unr', 6, 'Pyramid', 'has been breached!', 2, 12, 1),
(1037, 'AS-SaqqaraSG1beta20.unr', 7, 'Waterfall', 'was breached!', 0, 12, 1),
(1038, 'AS-SaqqaraLE_preview2b256.unr', 0, 'Green chamber switch', 'has been activated!', 2, 3, 1),
(1039, 'AS-SaqqaraLE_preview2b256.unr', 1, 'Yellow chamber switch', 'has been activated!', 2, 3, 1),
(1040, 'AS-SaqqaraLE_preview2b256.unr', 2, 'Red chamber switch', 'has been activated!', 1, 7, 1),
(1041, 'AS-SaqqaraLE_preview2b256.unr', 3, 'Blue chamber switch', 'has been activated!', 1, 7, 1),
(1042, 'AS-SaqqaraLE_preview2b256.unr', 4, 'Scarab Doors', 'has been breached!', 0, 7, 1),
(1043, 'AS-SaqqaraLE_preview2b256.unr', 5, 'Pyramid', 'has been breached!', 2, 2, 1),
(1044, 'AS-SaqqaraLE_preview2b256.unr', 6, 'Waterfall', 'was breached!', 0, 10, 1),
(1086, 'AS-B_497.unr', 1, 'Defence sytem control', 'was destroyed!', 0, 10, 1),
(1085, 'AS-B_497.unr', 0, 'Maingate control', 'was hacked!', 0, 12, 1),
(1084, 'AS-B_495.unr', 4, 'Attackers escaped :P', 'was hacked!', 0, 12, 1),
(1083, 'AS-B_495.unr', 3, 'Asgard core', 'was stolen!', 0, 10, 1),
(1082, 'AS-B_495.unr', 2, 'Generator', 'was deactivated!', 0, 10, 1),
(1081, 'AS-B_495.unr', 1, 'Defence sytem control', 'was destroyed!', 0, 10, 1),
(1080, 'AS-B_495.unr', 0, 'Maingate control', 'was hacked!', 0, 12, 1),
(1079, 'AS-OverlordAL.unr', 2, 'Beachhead', 'breached!', 2, 10, 1),
(1078, 'AS-OverlordAL.unr', 1, 'Boiler room', 'reached!', 1, 7, 1),
(1077, 'AS-OverlordAL.unr', 0, 'Gun control', 'destroyed!', 0, 5, 1),
(1076, 'AS-O-MG_345.unr', 7, 'Left fuse', 'was destroyed!', 0, 6, 1),
(1075, 'AS-O-MG_345.unr', 6, 'Right fuse', 'was destroyed!', 0, 6, 1),
(1074, 'AS-O-MG_345.unr', 5, 'DHD Control Crystals', 'have been stolen!', 0, 9, 1),
(1073, 'AS-O-MG_345.unr', 4, 'Cave entance', 'was reached!', 0, 2, 1),
(1072, 'AS-O-MG_345.unr', 3, 'The core', 'was destroyed!', 0, 4, 1),
(1071, 'AS-O-MG_345.unr', 2, 'Ring Transport', '', 0, 10, 1),
(1070, 'AS-O-MG_345.unr', 1, 'Base', 'Has Been Entered', 1, 8, 1),
(1069, 'AS-O-MG_345.unr', 0, 'Shield main system', 'shut down!', 6, 10, 1),
(1128, 'AS-WorseThings_043.unr', 6, 'Top Side Hatch', 'has been opened!', 31, 10, 1),
(1129, 'AS-WorseThings_043.unr', 7, 'Attackers', 'escaped!', 0, 10, 1),
(1130, 'AS-WorseThings_049.unr', 0, 'Borehole', 'was destroyed!', 10, 10, 1),
(1131, 'AS-WorseThings_049.unr', 1, 'Lower Side Hatch', 'has been opened!', 0, 10, 1),
(1132, 'AS-WorseThings_049.unr', 2, 'Oil Pit', 'has been drained!', 20, 10, 1),
(1133, 'AS-WorseThings_049.unr', 3, 'Drilling Machine', 'activated!', 15, 10, 1),
(1134, 'AS-WorseThings_049.unr', 4, 'Attackers Spawn Point', 'has advanced!', 30, 10, 1),
(1135, 'AS-WorseThings_049.unr', 5, 'Control Pump', 'deactivated!', 40, 10, 1),
(1136, 'AS-WorseThings_049.unr', 6, 'Top Side Hatch', 'has been opened!', 31, 10, 1),
(1137, 'AS-WorseThings_049.unr', 7, 'Attackers', 'escaped!', 0, 10, 1),
(1138, 'AS-OceanFloor]l[_beta2.unr', 0, 'Terminal 4', 'was deactivated!', 0, 8, 1),
(1139, 'AS-OceanFloor]l[_beta2.unr', 1, 'Terminal 2', 'was deactivated!', 0, 8, 1),
(1140, 'AS-OceanFloor]l[_beta2.unr', 2, 'Terminal 1', 'was deactivated!', 0, 8, 1),
(1141, 'AS-OceanFloor]l[_beta2.unr', 3, 'Terminal 3', 'was deactivated!', 0, 8, 1),
(1142, 'AS-OceanFloor]l[_beta2.unr', 4, 'Tesla inductor', 'was destoyed!', 0, 8, 1),
(1143, 'AS-OceanFloor]l[_beta3.unr', 0, 'Terminal 4', 'was deactivated!', 0, 8, 1),
(1144, 'AS-OceanFloor]l[_beta3.unr', 1, 'Terminal 2', 'was deactivated!', 0, 8, 1),
(1145, 'AS-OceanFloor]l[_beta3.unr', 2, 'Terminal 1', 'was deactivated!', 0, 8, 1),
(1146, 'AS-OceanFloor]l[_beta3.unr', 3, 'Terminal 3', 'was deactivated!', 0, 8, 1),
(1147, 'AS-OceanFloor]l[_beta3.unr', 4, 'Tesla inductor', 'was destoyed!', 0, 8, 1),
(1148, 'AS-WorseThings_052.unr', 0, 'Borehole', 'was destroyed!', 10, 10, 1),
(1149, 'AS-WorseThings_052.unr', 1, 'Lower Side Hatch', 'has been opened!', 0, 10, 1),
(1150, 'AS-WorseThings_052.unr', 2, 'Oil Pit', 'has been drained!', 20, 10, 1),
(1151, 'AS-WorseThings_052.unr', 3, 'Drilling Machine', 'activated!', 15, 10, 1),
(1152, 'AS-WorseThings_052.unr', 4, 'Attackers Spawn Point', 'has advanced!', 30, 10, 1),
(1153, 'AS-WorseThings_052.unr', 5, 'Control Pump', 'deactivated!', 40, 10, 1),
(1154, 'AS-WorseThings_052.unr', 6, 'Top Side Hatch', 'has been opened!', 31, 10, 1),
(1155, 'AS-WorseThings_052.unr', 7, 'Attackers', 'escaped!', 0, 10, 1),
(1156, 'AS-Atlantica.unr', 0, 'Generator', 'is running!', 96, 3, 1),
(1157, 'AS-Atlantica.unr', 1, 'Powerswitch', 'is turned on!', 64, 3, 1),
(1158, 'AS-Atlantica.unr', 2, 'Light', 'is on !', 32, 3, 1),
(1159, 'AS-WorseThings_051.unr', 0, 'Borehole', 'was destroyed!', 10, 10, 1),
(1160, 'AS-WorseThings_051.unr', 1, 'Lower Side Hatch', 'has been opened!', 0, 10, 1),
(1161, 'AS-WorseThings_051.unr', 2, 'Oil Pit', 'has been drained!', 20, 10, 1),
(1162, 'AS-WorseThings_051.unr', 3, 'Drilling Machine', 'activated!', 15, 10, 1),
(1163, 'AS-WorseThings_051.unr', 4, 'Attackers Spawn Point', 'has advanced!', 30, 10, 1),
(1164, 'AS-WorseThings_051.unr', 5, 'Control Pump', 'deactivated!', 40, 10, 1),
(1165, 'AS-WorseThings_051.unr', 6, 'Top Side Hatch', 'has been opened!', 31, 10, 1),
(1166, 'AS-WorseThings_051.unr', 7, 'Attackers', 'escaped!', 0, 10, 1),
(1167, 'AS-Navarone_033.unr', 0, 'Charge 3', 'Placed!', 0, 12, 1),
(1168, 'AS-Navarone_033.unr', 1, 'Demolition Charge 1', 'placed!', 0, 10, 1),
(1169, 'AS-Navarone_033.unr', 2, 'Demolition Charge 2', 'placed!', 0, 10, 1),
(1170, 'AS-Navarone_033.unr', 3, 'Lower Passage', 'Unlocked!', 0, 10, 1),
(1171, 'AS-Navarone_033.unr', 4, 'Supplies', 'collected!', 0, 10, 1),
(1172, 'AS-Navarone_033.unr', 5, 'Elevator', 'reached!', 0, 10, 1),
(1173, 'AS-B_529.unr', 0, 'Maingate control', 'was hacked!', 0, 12, 1),
(1174, 'AS-B_529.unr', 1, 'Defence sytem control', 'was destroyed!', 0, 10, 1),
(1175, 'AS-B_529.unr', 2, 'Generator', 'was deactivated!', 0, 10, 1),
(1176, 'AS-B_529.unr', 3, 'Asgard core', 'was stolen!', 0, 10, 1),
(1177, 'AS-B_529.unr', 4, 'Attackers escaped :P', 'was hacked!', 0, 12, 1),
(1178, 'AS-2sillbitac_023.unr', 2, 'Final Objective', 'was destroyed!', 0, 10, 1),
(1179, 'AS-B_547.unr', 0, 'Maingate control', 'was hacked!', 0, 12, 1),
(1180, 'AS-B_547.unr', 1, 'Defence sytem control', 'was destroyed!', 0, 10, 1),
(1181, 'AS-B_547.unr', 2, 'Generator', 'was deactivated!', 0, 10, 1),
(1182, 'AS-B_547.unr', 3, 'Asgard core', 'was stolen!', 0, 10, 1),
(1183, 'AS-B_547.unr', 4, 'Attackers escaped :P', 'was hacked!', 0, 12, 1),
(1184, 'AS-B_550.unr', 0, 'Maingate control', 'was hacked!', 0, 12, 1),
(1185, 'AS-B_550.unr', 1, 'Defence sytem control', 'was destroyed!', 0, 10, 1),
(1186, 'AS-B_550.unr', 2, 'Generator', 'was deactivated!', 0, 10, 1),
(1187, 'AS-B_550.unr', 3, 'Asgard core', 'was stolen!', 0, 10, 1),
(1188, 'AS-B_550.unr', 4, 'Attackers escaped :P', 'was hacked!', 0, 12, 1),
(1189, 'AS-Pandora_preview.unr', 0, 'Maingate control', 'was hacked!', 0, 12, 1),
(1190, 'AS-Pandora_preview.unr', 1, 'Defence sytem control', 'was destroyed!', 0, 10, 1),
(1191, 'AS-Pandora_preview.unr', 2, 'Generator', 'was deactivated!', 0, 10, 1),
(1192, 'AS-Pandora_preview.unr', 3, 'Asgard core', 'was stolen!', 0, 10, 1),
(1193, 'AS-Pandora_preview.unr', 4, 'Attackers escaped :P', 'was hacked!', 0, 12, 1),
(1194, 'AS-FoT-Chronoshift.unr', 0, 'Reactor cooling', 'disabled!', 40, 20, 1),
(1195, 'AS-FoT-Chronoshift.unr', 1, 'Primary power', 'shut down!', 40, 20, 1),
(1196, 'AS-FoT-Chronoshift.unr', 10, 'Power to gate 1', 'restored.', 60, 20, 1),
(1197, 'AS-FoT-Chronoshift.unr', 11, 'Direct surface access', 'granted.', 30, 20, 1),
(1198, 'AS-FoT-Chronoshift.unr', 12, 'Northern monorail entrance', 'is opening.', 20, 20, 1),
(1199, 'AS-FoT-Chronoshift.unr', 2, 'Gravity engine', 'offline!', 40, 20, 1),
(1200, 'AS-FoT-Chronoshift.unr', 3, 'Service bridges', 'are lowering.', 80, 20, 1),
(1201, 'AS-FoT-Chronoshift.unr', 4, 'Chain 2', 'was destroyed!', 100, 20, 1),
(1202, 'AS-FoT-Chronoshift.unr', 5, 'Chain 1', 'was destroyed!', 100, 20, 1),
(1203, 'AS-FoT-Chronoshift.unr', 6, 'Monorail station', '', 10, 20, 1),
(1204, 'AS-FoT-Chronoshift.unr', 7, 'Cargo crane', 'is operating.', 90, 20, 1),
(1205, 'AS-FoT-Chronoshift.unr', 8, 'Core access gate 2', 'is opening.', 70, 20, 1),
(1206, 'AS-FoT-Chronoshift.unr', 9, 'Core access gate 1', 'is opening.', 50, 20, 1),
(1207, 'AS-Navarone.unr', 0, 'Charge 3', 'Placed!', 0, 12, 1),
(1208, 'AS-Navarone.unr', 1, 'Demolition Charge 1', 'placed!', 0, 10, 1),
(1209, 'AS-Navarone.unr', 2, 'Demolition Charge 2', 'placed!', 0, 10, 1),
(1210, 'AS-Navarone.unr', 3, 'Lower Passage', 'Unlocked!', 0, 10, 1),
(1211, 'AS-Navarone.unr', 4, 'Supplies', 'collected!', 0, 10, 1),
(1212, 'AS-Navarone.unr', 5, 'Tunnels Passage', 'Open!', 0, 10, 1),
(1213, 'AS-Navarone.unr', 6, 'Tunnels', 'breached!', 0, 10, 1),
(1214, 'AS-Navarone.unr', 7, 'Sewers', 'breached!', 0, 10, 1),
(1215, 'AS-Navarone.unr', 8, 'Generator Room', 'entered!', 0, 10, 1),
(1216, 'AS-DawnRaidBetaj.unr', 0, 'generator', 'was destroyed!', 3, 10, 1),
(1217, 'AS-DawnRaidBetaj.unr', 1, 'Main Entrance', 'Has Been Breached', 6, 10, 1),
(1218, 'AS-DawnRaidBetaj.unr', 2, 'Final Area', 'Has Been Breached', 1, 10, 1),
(1219, 'AS-DawnRaidBetaj.unr', 3, 'Base Generator', 'was destroyed!', 0, 10, 1),
(1220, 'AS-DawnRaidBetaj.unr', 4, 'Intruders', 'Have entered the building', 5, 10, 1),
(1221, 'AS-DawnRaidBetaj.unr', 5, 'Lift', 'Has Been Breached!', 2, 10, 1),
(1222, 'AS-DawnRaidBetaj.unr', 6, 'Sector B', 'Has Been Taken', 4, 10, 1),
(1223, 'AS-Flugzeugtraeger.unr', 0, 'The Machineroom', 'was destroyed!', 1, 6, 1),
(1224, 'AS-Flugzeugtraeger.unr', 1, 'The Controlroom', 'The Controlroom was destroyed', 2, 6, 1),
(1225, 'AS-Overlord]I[.unr', 2, 'The attackers have entered', 'the lava cave!', 75, 10, 1),
(1226, 'AS-Overlord]I[.unr', 3, 'Final Objective', 'was destroyed!', 0, 7, 1),
(1227, 'AS-[RoVID_031.unr', 0, 'Fort Entrance', 'has been breached!', 100, 14, 1),
(1228, 'AS-[RoVID_031.unr', 1, 'First gate', 'is opening!', 90, 14, 1),
(1229, 'AS-[RoVID_031.unr', 2, 'Second gate', 'will open in 10 seconds!', 90, 14, 1),
(1230, 'AS-[RoVID_031.unr', 3, 'Anti-air base 1', 'has been scrambled!', 80, 14, 1),
(1231, 'AS-[RoVID_031.unr', 4, 'Anti-air base 2', 'has been scrambled!', 80, 14, 1),
(1232, 'AS-[RoVID_031.unr', 5, 'The sewers', 'have been breached!', 80, 14, 1),
(1233, 'AS-[RoVID_031.unr', 6, 'Base entrance terminal', 'was destroyed!', 70, 14, 1),
(1234, 'AS-[RoVID_031.unr', 7, 'Pump station', 'was destroyed!', 0, 10, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_smartass_objstats`
-- 
-- Creation: Jan 22, 2006 at 04:38 PM
-- Last update: Nov 09, 2007 at 01:15 PM
-- Last check: Mar 03, 2007 at 03:02 AM
-- 

DROP TABLE IF EXISTS `uts_smartass_objstats`;
CREATE TABLE IF NOT EXISTS `uts_smartass_objstats` (
  `id` int(11) NOT NULL auto_increment,
  `matchid` mediumint(8) NOT NULL default '0',
  `pid` mediumint(10) NOT NULL default '0',
  `playerid` tinyint(4) NOT NULL default '0',
  `objid` mediumint(10) NOT NULL default '0',
  `final` tinyint(3) NOT NULL default '0',
  `timestamp` float NOT NULL default '0',
  `att_teamsize` tinyint(3) NOT NULL default '0',
  `def_teamsize` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `matchid` (`matchid`),
  KEY `pid` (`pid`),
  KEY `playerid` (`playerid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

--
-- AUTO_INCREMENT for table `uts_smartass_objstats`
--
ALTER TABLE `uts_smartass_objstats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- INDEX for table `uts_smartass_objstats`
--
ALTER TABLE `uts_smartass_objstats`
  ADD INDEX `matchid` (`matchid`);

ALTER TABLE `uts_smartass_objstats`
  ADD INDEX `matchid` (`matchid`);

ALTER TABLE `uts_smartass_objstats`
  ADD INDEX `pid` (`pid`);
  
ALTER TABLE `uts_smartass_objstats`
  ADD INDEX `playerid` (`playerid`);

ALTER TABLE `uts_smartass_objstats`
  ADD INDEX `match_pid` (`matchid`,`pid`);

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_weapons`
-- 
-- Creation: Jan 22, 2006 at 04:38 PM
-- Last update: Jul 14, 2007 at 10:46 PM
-- Last check: Mar 03, 2007 at 03:02 AM
-- 

DROP TABLE IF EXISTS `uts_weapons`;
CREATE TABLE IF NOT EXISTS `uts_weapons` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `image` varchar(50) NOT NULL default '',
  `sequence` tinyint(3) unsigned NOT NULL default '200',
  `hide` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`(20))
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

--
-- AUTO_INCREMENT for table `uts_weapons`
--
ALTER TABLE `uts_weapons`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- INDEX for table `uts_weapons`
--
ALTER TABLE `uts_weapons`
  ADD INDEX `name` (`name`);

-- 
-- Dumping data for table `uts_weapons`
-- 

INSERT INTO `uts_weapons` VALUES (1, 'Translocator', 'trans.jpg', 1, 'N'),
(2, 'Impact Hammer', 'impact.jpg', 2, 'N'),
(3, 'Enforcer', 'enforcer.jpg', 3, 'N'),
(4, 'Double Enforcer', 'enforcer2.jpg', 4, 'N'),
(5, 'GES Bio Rifle', 'bio.jpg', 5, 'N'),
(6, 'Ripper', 'ripper.jpg', 6, 'N'),
(7, 'Shock Rifle', 'shock.jpg', 7, 'N'),
(8, 'Enhanced Shock Rifle', 'ishock.jpg', 8, 'N'),
(9, 'Pulse Gun', 'pulse.jpg', 9, 'N'),
(10, 'Minigun', 'minigun.jpg', 10, 'N'),
(11, 'Flak Cannon', 'flak.jpg', 11, 'N'),
(12, 'Rocket Launcher', 'rockets.jpg', 12, 'N'),
(13, 'Sniper Rifle', 'sniper.jpg', 13, 'N'),
(14, 'Redeemer', 'deemer.jpg', 14, 'N'),
(15, 'None', 'blank.jpg', 15, 'Y'),
(16, 'Chainsaw', 'chainsaw.jpg', 16, 'N'),
(23, 'dummyweapon', '', 200, 'Y'),
(24, 'Min0.00', '', 200, 'N'),
(25, 'Snipe0.00', '', 200, 'N'),
(26, 'INFUT_ADD_TurretWeaponM250', '', 200, 'N'),
(27, 'Impac0.00', '', 200, 'N'),
(28, 'Rocket0.00', '', 200, 'N'),
(29, 'Flak0.00', '', 200, 'N'),
(30, 'info', '', 200, 'N'),
(31, 'Prototype GES Super-Bio Rifle', '', 200, 'N'),
(32, 'NoWeaponNoFire', '', 200, 'N');

-- --------------------------------------------------------

-- 
-- Table structure for table `uts_weaponstats`
-- 
-- Creation: Jan 22, 2006 at 04:38 PM
-- Last update: Nov 09, 2007 at 01:15 PM
-- Last check: Nov 09, 2007 at 11:46 AM
-- 

DROP TABLE IF EXISTS `uts_weaponstats`;
CREATE TABLE IF NOT EXISTS `uts_weaponstats` (
  `matchid` mediumint(8) unsigned NOT NULL default '0',
  `pid` int(10) unsigned NOT NULL default '0',
  `year` smallint(4) unsigned NOT NULL default '0',
  `weapon` tinyint(3) unsigned NOT NULL default '0',
  `kills` mediumint(8) unsigned NOT NULL default '0',
  `shots` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `damage` int(10) unsigned NOT NULL default '0',
  `acc` float unsigned NOT NULL default '0',
  KEY `full` (`matchid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- INDEX for table `uts_weaponstats`
--
ALTER TABLE `uts_weaponstats`
  ADD INDEX `pid_weap_year` (`pid`, `weapon`, `year`);

ALTER TABLE `uts_weaponstats`
  ADD INDEX `pid_match` (`pid`, `matchid`);

-- --------------------------------------------------------

-- 
-- Table structure for table `x_ftpservers`
-- 
-- Creation: Jan 22, 2006 at 04:39 PM
-- Last update: Aug 13, 2007 at 12:48 AM
-- Last check: Mar 03, 2007 at 03:03 AM
-- 

DROP TABLE IF EXISTS `x_ftpservers`;
CREATE TABLE IF NOT EXISTS `x_ftpservers` (
  `id` int(11) NOT NULL auto_increment,
  `servername` varchar(30) NOT NULL default '',
  `ftp_hostname` varchar(30) NOT NULL default '',
  `ftp_port` int(11) NOT NULL default '21',
  `ftp_uname` varchar(20) NOT NULL default '',
  `ftp_upass` varchar(20) NOT NULL default '',
  `ftp_dir` mediumtext NOT NULL,
  `ftp_passive` tinyint(1) NOT NULL default '1',
  `ftp_delete` tinyint(1) NOT NULL default '1',
  `ftp_logext` enum('.log','.bz2') NOT NULL default '.log',
  `gmt_offset` tinyint(4) NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `x_ipcache`
-- 
-- Creation: Jan 22, 2006 at 04:39 PM
-- Last update: Nov 06, 2007 at 10:26 PM
-- Last check: Mar 03, 2007 at 03:03 AM
-- 

DROP TABLE IF EXISTS `x_ipcache`;
CREATE TABLE IF NOT EXISTS `x_ipcache` (
  `ipaddr` varchar(15) NOT NULL default '',
  `dns` tinytext NOT NULL,
  `disable` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `cacheoutput` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `x_themes`
-- 
-- Creation: Jan 22, 2006 at 04:39 PM
-- Last update: Mar 03, 2007 at 03:03 AM
-- Last check: Mar 03, 2007 at 03:03 AM
-- 

DROP TABLE IF EXISTS `x_themes`;
CREATE TABLE IF NOT EXISTS `x_themes` (
  `id` int(11) NOT NULL auto_increment,
  `themename` varchar(50) NOT NULL default '',
  `themelocation` varchar(50) NOT NULL default '',
  `weaponimages` enum('0','1') NOT NULL default '0',
  `customsidebar` enum('0','1') NOT NULL default '0',
  `customchartbars` enum('0','1') NOT NULL default '0',
  `customsig` enum('0','1') NOT NULL default '0',
  `default` enum('0','1') NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `customsidebar` (`customsidebar`,`customchartbars`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COMMENT='Theme list' AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `x_themes`
-- 
INSERT INTO `x_themes` VALUES (1, 'Timo''s Dark Theme', 'themes/timo_dark/', '1', '1', '0', '0', '0', '1'),
(2, 'UTStats Original Blue', '', '0', '0', '0', '0', '0', '1'),
(3, 'Timo''s Purple Lover Theme', 'themes/timo_plove/', '1', '1', '0', '0', '0', '0'),
(4, 'Timo''s Lite Theme', 'themes/timo_text/', '1', '1', '0', '0', '0', '0'),
(5, 'Fraghub inspired Pug Theme', 'themes/xb_pug/', '1', '1', '0', '0', '1', '1');