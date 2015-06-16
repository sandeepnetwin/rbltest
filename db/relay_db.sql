-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 12, 2015 at 11:04 AM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `relay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `rlb_modes`
--

CREATE TABLE IF NOT EXISTS `rlb_modes` (
  `mode_id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(255) NOT NULL,
  `mode_status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mode_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rlb_modes`
--

INSERT INTO `rlb_modes` (`mode_id`, `mode_name`, `mode_status`) VALUES
(1, 'Auto', 0),
(2, 'Manual', 1),
(3, 'Time-Out', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rlb_relay_prog`
--

CREATE TABLE IF NOT EXISTS `rlb_relay_prog` (
  `relay_prog_id` int(11) NOT NULL AUTO_INCREMENT,
  `relay_prog_name` varchar(255) NOT NULL,
  `relay_number` varchar(8) NOT NULL,
  `relay_prog_type` int(2) NOT NULL COMMENT '1-Daily, 2-Weekly',
  `relay_prog_days` varchar(255) NOT NULL COMMENT '0-All, 1-Mon, 2-Tue...7-Sun',
  `relay_start_time` varchar(255) NOT NULL,
  `relay_end_time` varchar(255) NOT NULL,
  `relay_prog_created_date` datetime NOT NULL,
  `relay_prog_modified_date` datetime NOT NULL,
  `relay_prog_delete` int(1) NOT NULL DEFAULT '0',
  `relay_prog_active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`relay_prog_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
