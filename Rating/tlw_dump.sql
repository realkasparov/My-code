-- --------------------------------------------------------
-- Хост:                         localhost
-- Версия сервера:               5.7.11 - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных tlw_db
CREATE DATABASE IF NOT EXISTS `tlw_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `tlw_db`;


-- Дамп структуры для таблица tlw_db.letter
CREATE TABLE IF NOT EXISTS `letter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `letter` (`letter`)
) ENGINE=MyISAM CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица tlw_db.word_1
CREATE TABLE IF NOT EXISTS `word_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `word` (`word`)
) ENGINE=MyISAM CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица tlw_db.word_2
CREATE TABLE IF NOT EXISTS `word_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `word` (`word`)
) ENGINE=MyISAM CHARSET=utf8;

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
