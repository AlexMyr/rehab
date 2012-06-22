-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Хост: openserver:3306
-- Время создания: Июн 22 2012 г., 14:29
-- Версия сервера: 5.1.61
-- Версия PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `rehab`
--

-- --------------------------------------------------------

--
-- Структура таблицы `paypal_transactions`
--

CREATE TABLE IF NOT EXISTS `paypal_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trainer_id` int(10) unsigned NOT NULL,
  `name` varchar(300) CHARACTER SET utf8 NOT NULL,
  `profile_id` varchar(25) CHARACTER SET utf8 DEFAULT NULL COMMENT 'for recurring payments',
  `status` varchar(50) CHARACTER SET utf8 NOT NULL,
  `type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `amount` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ack` varchar(150) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Acknowledgement status',
  `request` text CHARACTER SET utf8 COMMENT 'full request string',
  `correlation_id` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT 'uniquely identifies the transaction to PayPal',
  `error` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `answer` text CHARACTER SET utf8 COMMENT 'full answer',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
