-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Хост: openserver:3306
-- Время создания: Апр 26 2012 г., 18:41
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
-- Структура таблицы `categs_test`
--

CREATE TABLE IF NOT EXISTS `categs_test` (
  `cat_id` int(3) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) NOT NULL,
  `cat_type` enum('top','sub') NOT NULL,
  `top_cat` varchar(100) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=126 ;

-- --------------------------------------------------------

--
-- Структура таблицы `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `client_note` tinytext,
  `email` varchar(255) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `print_image_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-lineart, 1-image',
  `trainer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=164 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_content_box`
--

CREATE TABLE IF NOT EXISTS `cms_content_box` (
  `content_box_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `subtitle` varchar(255) NOT NULL DEFAULT '',
  `headline` text NOT NULL,
  `content` text NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `content_template_id` int(11) NOT NULL DEFAULT '0',
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`content_box_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_content_template`
--

CREATE TABLE IF NOT EXISTS `cms_content_template` (
  `content_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`content_template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_menu`
--

CREATE TABLE IF NOT EXISTS `cms_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `template_file_v` varchar(255) NOT NULL DEFAULT '',
  `template_file_h` varchar(255) NOT NULL DEFAULT '',
  `css_file_h` varchar(255) NOT NULL DEFAULT '',
  `css_file_v` varchar(255) NOT NULL DEFAULT '',
  `tag_h` varchar(255) NOT NULL DEFAULT '',
  `tag_v` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `h_version` tinyint(4) NOT NULL DEFAULT '0',
  `v_version` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_menu_link`
--

CREATE TABLE IF NOT EXISTS `cms_menu_link` (
  `menu_link_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint(4) NOT NULL DEFAULT '0',
  `target` varchar(255) DEFAULT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menu_link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=206 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_menu_submenu`
--

CREATE TABLE IF NOT EXISTS `cms_menu_submenu` (
  `menu_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `menu_link_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_menu_template_file`
--

CREATE TABLE IF NOT EXISTS `cms_menu_template_file` (
  `menu_template_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `file_name` varchar(40) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`menu_template_file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_page_type`
--

CREATE TABLE IF NOT EXISTS `cms_page_type` (
  `page_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `template_id` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`page_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_page_type_czone`
--

CREATE TABLE IF NOT EXISTS `cms_page_type_czone` (
  `page_type_czone_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_type_id` int(11) NOT NULL DEFAULT '0',
  `template_czone_id` int(11) NOT NULL DEFAULT '0',
  `default_data` text NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  `prefilled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_type_czone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=122 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_tag_library`
--

CREATE TABLE IF NOT EXISTS `cms_tag_library` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `file_name` varchar(255) DEFAULT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_template`
--

CREATE TABLE IF NOT EXISTS `cms_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_template_czone`
--

CREATE TABLE IF NOT EXISTS `cms_template_czone` (
  `template_czone_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`template_czone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_web_page`
--

CREATE TABLE IF NOT EXISTS `cms_web_page` (
  `web_page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_type_id` int(11) NOT NULL DEFAULT '0',
  `template_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  `date` varchar(20) NOT NULL DEFAULT '',
  `no_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`web_page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=144 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cms_web_page_content`
--

CREATE TABLE IF NOT EXISTS `cms_web_page_content` (
  `web_page_content_id` int(11) NOT NULL AUTO_INCREMENT,
  `web_page_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` tinyint(4) NOT NULL DEFAULT '0',
  `date` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `subtitle` varchar(255) DEFAULT NULL,
  `headline` text,
  `content` longtext NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `template_czone_id` int(11) NOT NULL DEFAULT '0',
  `content_template_id` int(11) NOT NULL DEFAULT '0',
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`web_page_content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=523 ;

-- --------------------------------------------------------

--
-- Структура таблицы `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `is_main` tinyint(4) NOT NULL DEFAULT '0',
  `code` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=250 ;

-- --------------------------------------------------------

--
-- Структура таблицы `exercise_notes`
--

CREATE TABLE IF NOT EXISTS `exercise_notes` (
  `exercise_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_id` int(11) NOT NULL,
  `exercise_notes` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`exercise_note_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `exercise_plan`
--

CREATE TABLE IF NOT EXISTS `exercise_plan` (
  `exercise_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `exercise_program_id` varchar(255) NOT NULL,
  `exercise_notes` text NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`exercise_plan_id`),
  UNIQUE KEY `id` (`exercise_plan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=331 ;

-- --------------------------------------------------------

--
-- Структура таблицы `exercise_plan_set`
--

CREATE TABLE IF NOT EXISTS `exercise_plan_set` (
  `exercise_set_id` int(11) NOT NULL AUTO_INCREMENT,
  `exercise_plan_id` int(11) NOT NULL,
  `exercise_program_id` varchar(255) NOT NULL,
  `plan_description` text NOT NULL,
  `plan_set_no` varchar(255) NOT NULL,
  `plan_repetitions` varchar(255) NOT NULL,
  `plan_time` varchar(255) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `is_program_plan` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`exercise_set_id`),
  UNIQUE KEY `id` (`exercise_set_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=623 ;

-- --------------------------------------------------------

--
-- Структура таблицы `exercise_preview_template_czone`
--

CREATE TABLE IF NOT EXISTS `exercise_preview_template_czone` (
  `template_czone_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  `lang` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`template_czone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `exercise_program_plan`
--

CREATE TABLE IF NOT EXISTS `exercise_program_plan` (
  `exercise_program_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `exercise_program_id` text COLLATE utf8_unicode_ci NOT NULL,
  `exercise_notes` text COLLATE utf8_unicode_ci NOT NULL,
  `date_created` date NOT NULL,
  `date_modified` date NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `print_image_type` tinyint(1) NOT NULL DEFAULT '0',
  `client_note` tinytext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`exercise_program_plan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `faq_id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_category_id` int(11) NOT NULL DEFAULT '0',
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`faq_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `faq_category`
--

CREATE TABLE IF NOT EXISTS `faq_category` (
  `faq_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `level` int(4) NOT NULL DEFAULT '0',
  `sort_order` int(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`faq_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `faq_category_subcategory`
--

CREATE TABLE IF NOT EXISTS `faq_category_subcategory` (
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `faq_category_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `faq_template_czone`
--

CREATE TABLE IF NOT EXISTS `faq_template_czone` (
  `template_czone_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`template_czone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `meta_translate_en`
--

CREATE TABLE IF NOT EXISTS `meta_translate_en` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `keywords` varchar(511) NOT NULL,
  `description` varchar(511) NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Структура таблицы `meta_translate_us`
--

CREATE TABLE IF NOT EXISTS `meta_translate_us` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `keywords` varchar(511) NOT NULL,
  `description` varchar(511) NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Структура таблицы `page_template_czone`
--

CREATE TABLE IF NOT EXISTS `page_template_czone` (
  `template_czone_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  `lang` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`template_czone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `price_plan`
--

CREATE TABLE IF NOT EXISTS `price_plan` (
  `price_id` double DEFAULT NULL,
  `licence_type` varchar(30) DEFAULT NULL,
  `licence_amount` varchar(9) DEFAULT NULL,
  `licence_period` varchar(30) DEFAULT NULL,
  `currency` varchar(9) DEFAULT NULL,
  `price_value` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `price_plan_new`
--

CREATE TABLE IF NOT EXISTS `price_plan_new` (
  `price_id` int(2) NOT NULL AUTO_INCREMENT,
  `price_plan_name` varchar(255) NOT NULL,
  `has_logo` tinyint(1) NOT NULL,
  `can_create_exercise` tinyint(1) NOT NULL DEFAULT '1',
  `email` tinyint(1) NOT NULL DEFAULT '1',
  `photo_lineart` tinyint(1) NOT NULL DEFAULT '1',
  `licence_amount` varchar(5) NOT NULL,
  `licence_period` varchar(64) NOT NULL,
  `price_value` varchar(30) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `price_view` varchar(64) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'GBP',
  PRIMARY KEY (`price_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `programs`
--

CREATE TABLE IF NOT EXISTS `programs` (
  `programs_id` int(11) NOT NULL AUTO_INCREMENT,
  `lineart` varchar(255) DEFAULT NULL,
  `thumb_lineart` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `thumb_image` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `sort_order` int(4) NOT NULL DEFAULT '0',
  `programs_code` varchar(20) NOT NULL,
  PRIMARY KEY (`programs_id`),
  UNIQUE KEY `id_program` (`programs_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=924 ;

-- --------------------------------------------------------

--
-- Структура таблицы `programs_category`
--

CREATE TABLE IF NOT EXISTS `programs_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) DEFAULT NULL,
  `category_level` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `sort_order` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=126 ;

-- --------------------------------------------------------

--
-- Структура таблицы `programs_category_subcategory`
--

CREATE TABLE IF NOT EXISTS `programs_category_subcategory` (
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `programs_in_category`
--

CREATE TABLE IF NOT EXISTS `programs_in_category` (
  `programs_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `main` tinyint(4) NOT NULL,
  KEY `programs_id` (`programs_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `programs_translate_en`
--

CREATE TABLE IF NOT EXISTS `programs_translate_en` (
  `programs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `programs_title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`programs_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=923 ;

-- --------------------------------------------------------

--
-- Структура таблицы `programs_translate_us`
--

CREATE TABLE IF NOT EXISTS `programs_translate_us` (
  `programs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `programs_title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`programs_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=923 ;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `constant_name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `long_value` text,
  `module` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `sys_message`
--

CREATE TABLE IF NOT EXISTS `sys_message` (
  `sys_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `from_email` varchar(255) NOT NULL DEFAULT '',
  `from_name` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `lang` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`sys_message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tmpl_translate_en`
--

CREATE TABLE IF NOT EXISTS `tmpl_translate_en` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_file` varchar(220) NOT NULL,
  `tag` varchar(220) NOT NULL,
  `tag_text` varchar(1000) NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=331 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tmpl_translate_us`
--

CREATE TABLE IF NOT EXISTS `tmpl_translate_us` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_file` varchar(220) NOT NULL,
  `tag` varchar(220) NOT NULL,
  `tag_text` varchar(1000) NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=331 ;

-- --------------------------------------------------------

--
-- Структура таблицы `trainer`
--

CREATE TABLE IF NOT EXISTS `trainer` (
  `trainer_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_id` int(11) DEFAULT '0',
  `is_trial` tinyint(1) NOT NULL DEFAULT '1',
  `is_login` tinyint(1) NOT NULL DEFAULT '0',
  `expire_date` datetime DEFAULT NULL COMMENT 'date untill the account will be active',
  `create_date` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1' COMMENT '0-expired, 1-active not logged in yet, 2-active',
  `access_level` tinyint(1) NOT NULL DEFAULT '3',
  `clinic_name` varchar(255) NOT NULL,
  `is_clinic` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0-user, 1-clinic, 2-not set',
  `price_plan_id` int(4) NOT NULL,
  `country_id` int(4) NOT NULL,
  `paypal_profile_id` varchar(255) NOT NULL,
  `affiliate_refferer_id` varchar(32) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `last_login_date` int(11) NOT NULL,
  `fb_id` int(11) DEFAULT NULL,
  `lang` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`trainer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=269 ;

-- --------------------------------------------------------

--
-- Структура таблицы `trainer_dashboard_template_czone`
--

CREATE TABLE IF NOT EXISTS `trainer_dashboard_template_czone` (
  `template_czone_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `mode` tinyint(4) NOT NULL DEFAULT '1',
  `lang` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`template_czone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `trainer_header_paper`
--

CREATE TABLE IF NOT EXISTS `trainer_header_paper` (
  `header_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `address` tinytext,
  `post_code` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `fax` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `profile_id` int(11) NOT NULL,
  `city` tinytext NOT NULL,
  `exercise_notes` text NOT NULL,
  PRIMARY KEY (`header_id`),
  UNIQUE KEY `id` (`header_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Структура таблицы `trainer_profile`
--

CREATE TABLE IF NOT EXISTS `trainer_profile` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `address` tinytext,
  `post_code` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `exercise_notes` text NOT NULL,
  `city` tinytext NOT NULL,
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `id` (`profile_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_level` tinyint(4) NOT NULL DEFAULT '2',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
