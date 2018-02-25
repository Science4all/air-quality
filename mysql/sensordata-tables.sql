-- phpMyAdmin SQL Dump
-- version 4.1.11
-- http://www.phpmyadmin.net
--
-- Host: hl327.dinaserver.com
-- Generation Time: Feb 23, 2018 at 12:56 PM
-- Server version: 5.5.59-0ubuntu0.14.04.1-log
-- PHP Version: 5.4.45-0+deb7u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sensordata`
--

-- --------------------------------------------------------

--
-- Table structure for table `CHANNEL`
--

CREATE TABLE IF NOT EXISTS `CHANNEL` (
  `CH_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CH_ND_KEY` int(11) NOT NULL,
  `CH_PARAM_ID` int(11) NOT NULL,
  PRIMARY KEY (`CH_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Table structure for table `GPS`
--

CREATE TABLE IF NOT EXISTS `GPS` (
  `GPS_ID` int(11) NOT NULL AUTO_INCREMENT,
  `GPS_LAT` float NOT NULL,
  `GPS_LON` float NOT NULL,
  PRIMARY KEY (`GPS_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `NETWORKS`
--

CREATE TABLE IF NOT EXISTS `NETWORKS` (
  `NT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NT_NAME` varchar(150) NOT NULL,
  PRIMARY KEY (`NT_ID`),
  UNIQUE KEY `NT_NAME` (`NT_NAME`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `NODES`
--

CREATE TABLE IF NOT EXISTS `NODES` (
  `ND_KEY` int(11) NOT NULL AUTO_INCREMENT,
  `ND_NAME` varchar(150) NOT NULL,
  `GPS_ID` int(10) unsigned DEFAULT NULL,
  `ND_ID` varchar(30) NOT NULL,
  PRIMARY KEY (`ND_KEY`),
  UNIQUE KEY `ND_NAME` (`ND_NAME`),
  UNIQUE KEY `ND_ID` (`ND_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `NT_ND`
--

CREATE TABLE IF NOT EXISTS `NT_ND` (
  `NW_ND_KEY` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NETWORK_ID` int(11) NOT NULL,
  `NODE_ID` int(11) NOT NULL,
  PRIMARY KEY (`NW_ND_KEY`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `PARAMETERS`
--

CREATE TABLE IF NOT EXISTS `PARAMETERS` (
  `PR_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SN_KEY` int(10) unsigned NOT NULL,
  `SN_LABEL` varchar(30) NOT NULL COMMENT 'ID coming from sensor',
  `PR_MAGNITUDE` varchar(150) NOT NULL,
  `PR_UNITS` varchar(150) NOT NULL,
  PRIMARY KEY (`PR_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `RECORDINGS`
--

CREATE TABLE IF NOT EXISTS `RECORDINGS` (
  `RC_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CH_ID` int(11) NOT NULL,
  `RC_DATA` double NOT NULL,
  `TIMESTAMP_SERVER` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RC_ID`),
  KEY `CH_ID` (`CH_ID`),
  KEY `TIMESTAMP_SERVER` (`TIMESTAMP_SERVER`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=740472 ;

-- --------------------------------------------------------

--
-- Table structure for table `SENSORS`
--

CREATE TABLE IF NOT EXISTS `SENSORS` (
  `SN_ID` int(11) NOT NULL AUTO_INCREMENT,
  `SN_NAME` varchar(150) NOT NULL,
  PRIMARY KEY (`SN_ID`),
  UNIQUE KEY `SN_NAME` (`SN_NAME`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
