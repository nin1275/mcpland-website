SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `LEADEROSv5`
--

-- --------------------------------------------------------

--
-- Table `Accounts`
--

CREATE TABLE IF NOT EXISTS `Accounts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `realname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) DEFAULT 'your@email.com',
  `password` VARCHAR(255) NOT NULL,
  `lastlogin` BIGINT(20) DEFAULT '0',
  `x` DOUBLE NOT NULL DEFAULT '0',
  `y` DOUBLE NOT NULL DEFAULT '0',
  `z` DOUBLE NOT NULL DEFAULT '0',
  `world` VARCHAR(255) DEFAULT 'world',
  `isLogged` SMALLINT(6) NOT NULL DEFAULT '0',
  `credit` INT(5) UNSIGNED NOT NULL DEFAULT '0',
  `authStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `creationIP` VARCHAR(40) NOT NULL DEFAULT '127.0.0.1',
  `creationDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY(`id`),
  UNIQUE KEY(`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountContactInfo`
--

CREATE TABLE IF NOT EXISTS `AccountContactInfo` (
  `accountID` INT(11) NOT NULL,
  `firstName` VARCHAR(255) NOT NULL,
  `lastName` VARCHAR(255) NOT NULL,
  `phoneNumber` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`accountID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountNoticationInfo`
--

CREATE TABLE IF NOT EXISTS `AccountNoticationInfo` (
  `accountID` INT(11) NOT NULL,
  `lastReadDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`accountID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountNoticationInfo`
--

CREATE TABLE IF NOT EXISTS `AccountOneSignalInfo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `oneSignalID` CHAR(36) NOT NULL DEFAULT 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
  PRIMARY KEY(`id`),
  UNIQUE KEY(`oneSignalID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountSocialMedia`
--

CREATE TABLE IF NOT EXISTS `AccountSocialMedia` (
  `accountID` INT(11) NOT NULL,
  `skype` VARCHAR(255) NOT NULL DEFAULT '0',
  `discord` VARCHAR(255) NOT NULL DEFAULT '0',
  PRIMARY KEY(`accountID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountRecovers`
--

CREATE TABLE IF NOT EXISTS `AccountRecovers` (
  `accountID` INT(11) NOT NULL,
  `recoverToken` CHAR(32),
  `creationIP` VARCHAR(40) NOT NULL DEFAULT '127.0.0.1',
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`accountID`),
  UNIQUE KEY(`recoverToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountTFARecovers`
--

CREATE TABLE IF NOT EXISTS `AccountTFARecovers` (
  `accountID` INT(11) NOT NULL,
  `recoverToken` CHAR(32),
  `creationIP` VARCHAR(40) NOT NULL DEFAULT '127.0.0.1',
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`accountID`),
  UNIQUE KEY(`recoverToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountSessions`
--

CREATE TABLE IF NOT EXISTS `AccountSessions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `loginToken` CHAR(32),
  `creationIP` VARCHAR(40) NOT NULL DEFAULT '127.0.0.1',
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`),
  UNIQUE KEY(`loginToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `AccountAuths`
--

CREATE TABLE IF NOT EXISTS `AccountAuths` (
  `accountID` INT(11) NOT NULL,
  `secretKey` CHAR(16),
  PRIMARY KEY(`accountID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `BannedAccounts`
--

CREATE TABLE IF NOT EXISTS `BannedAccounts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `categoryID` ENUM('1', '2', '3') NOT NULL DEFAULT '1',
  `reasonID` ENUM('1', '2', '3', '4', '5', '6') NOT NULL DEFAULT '1',
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `News`
--

CREATE TABLE IF NOT EXISTS `News` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `categoryID` INT(11) NOT NULL,
  `imageID` CHAR(32) NOT NULL,
  `imageType` VARCHAR(6) NOT NULL DEFAULT 'jpg',
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `views` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `commentsStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `updateDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `NewsTags`
--

CREATE TABLE IF NOT EXISTS `NewsTags` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `newsID` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `NewsCategories`
--

CREATE TABLE IF NOT EXISTS `NewsCategories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `NewsComments`
--

CREATE TABLE IF NOT EXISTS `NewsComments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `newsID` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('0', '1') NOT NULL DEFAULT '0',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Servers`
--

CREATE TABLE IF NOT EXISTS `Servers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `ip` VARCHAR(255) NOT NULL,
  `port` INT(11) NOT NULL,
  `consoleID` ENUM('1', '2', '3') NOT NULL DEFAULT '1',
  `consolePort` INT(11) NOT NULL,
  `consolePassword` VARCHAR(255) NOT NULL,
  `imageID` CHAR(32) NOT NULL,
  `imageType` VARCHAR(6) NOT NULL DEFAULT 'jpg',
  `priority` int(11) NOT NULL DEFAULT '0',
  `minecraftStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `minecraftTitle` varchar(255) DEFAULT NULL,
  `minecraftDescription` TEXT DEFAULT NULL,
  `minecraftItem` varchar(255) DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Products`
--

CREATE TABLE IF NOT EXISTS `Products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `categoryID` INT(11) NOT NULL,
  `serverID` INT(11) NOT NULL,
  `giveRoleID` varchar(255) DEFAULT NULL DEFAULT '0',
  `name` VARCHAR(255) NOT NULL,
  `imageID` CHAR(32) NOT NULL,
  `imageType` VARCHAR(6) NOT NULL DEFAULT 'jpg',
  `details` TEXT NOT NULL,
  `price` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `discountedPrice` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `discountExpiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `duration` INT(4) NOT NULL,
  `stock` INT(5) NOT NULL DEFAULT '-1',
  `priority` int(11) NOT NULL DEFAULT '0',
  `minecraftStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `minecraftTitle` varchar(255) DEFAULT NULL,
  `minecraftDescription` TEXT DEFAULT NULL,
  `minecraftItem` varchar(255) DEFAULT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ProductCommands`
--

CREATE TABLE IF NOT EXISTS `ProductCommands` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `productID` INT(11) NOT NULL,
  `command` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ProductCategories`
--

CREATE TABLE IF NOT EXISTS `ProductCategories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `serverID` INT(11) NOT NULL,
  `parentID` INT(11) NOT NULL DEFAULT '0',
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `imageID` CHAR(32) NOT NULL,
  `imageType` VARCHAR(6) NOT NULL DEFAULT 'jpg',
  `priority` int(11) NOT NULL DEFAULT '0',
  `minecraftStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `minecraftTitle` varchar(255) DEFAULT NULL,
  `minecraftDescription` TEXT DEFAULT NULL,
  `minecraftItem` varchar(255) DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Chests`
--

CREATE TABLE IF NOT EXISTS `Chests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `productID` INT(11) NOT NULL,
  `status` ENUM('0', '1') NOT NULL DEFAULT '0',
  `isLocked` ENUM('0', '1') NOT NULL DEFAULT '0',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Lotteries`
--

CREATE TABLE IF NOT EXISTS `Lotteries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `price` INT(11) UNSIGNED NOT NULL DEFAULT '5',
  `duration` INT(4) NOT NULL,
  PRIMARY KEY(`id`),
  UNIQUE KEY(`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `LotteryAwards`
--

CREATE TABLE IF NOT EXISTS `LotteryAwards` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `lotteryID` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `chance` INT(3) UNSIGNED NOT NULL,
  `awardType` ENUM('1', '2', '3') NOT NULL DEFAULT '1',
  `award` INT(11) NOT NULL,
  `color` VARCHAR(32) NOT NULL DEFAULT '#000000',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ProductGifts`
--

CREATE TABLE IF NOT EXISTS `ProductGifts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `giftType` ENUM('1', '2') NOT NULL DEFAULT '1',
  `gift` INT(11) NOT NULL,
  `piece` INT(6) UNSIGNED NOT NULL,
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`),
  UNIQUE KEY(`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ProductCoupons`
--

CREATE TABLE IF NOT EXISTS `ProductCoupons` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `products` TEXT NOT NULL,
  `discount` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `piece` INT(6) UNSIGNED NOT NULL DEFAULT '0',
  `minPayment` INT(11) NOT NULL DEFAULT '0',
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`),
  UNIQUE KEY(`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ChestsHistory`
--

CREATE TABLE IF NOT EXISTS `ChestsHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `chestID` INT(11) NOT NULL,
  `type` ENUM('1', '2', '3') NOT NULL DEFAULT '1',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `LotteryHistory`
--

CREATE TABLE IF NOT EXISTS `LotteryHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `lotteryAwardID` INT(11) UNSIGNED NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ProductGiftsHistory`
--

CREATE TABLE IF NOT EXISTS `ProductGiftsHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `giftID` INT(11) UNSIGNED NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ProductCouponsHistory`
--

CREATE TABLE IF NOT EXISTS `ProductCouponsHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `couponID` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `productID` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `CreditHistory`
--

CREATE TABLE IF NOT EXISTS `CreditHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `paymentID` VARCHAR(255) NOT NULL DEFAULT '0',
  `paymentAPI` VARCHAR(255) NOT NULL DEFAULT 'other',
  `paymentStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `type` ENUM('1', '2', '3', '4', '5', '6') NOT NULL DEFAULT '1',
  `price` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `earnings` DECIMAL(8,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `StoreHistory`
--

CREATE TABLE IF NOT EXISTS `StoreHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `productID` INT(11) NOT NULL,
  `price` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Leaderboards`
--

CREATE TABLE IF NOT EXISTS `Leaderboards` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `serverName` VARCHAR(255) NOT NULL,
  `serverSlug` VARCHAR(255) NOT NULL,
  `mysqlServer` VARCHAR(255) NOT NULL DEFAULT '0',
  `mysqlPort` INT(11) NOT NULL,
  `mysqlUsername` VARCHAR(255) NOT NULL,
  `mysqlPassword` VARCHAR(255) NOT NULL,
  `mysqlDatabase` VARCHAR(255) NOT NULL,
  `mysqlTable` VARCHAR(255) NOT NULL,
  `usernameColumn` VARCHAR(255) NOT NULL,
  `tableTitles` TEXT NOT NULL,
  `tableData` TEXT NOT NULL,
  `sorter` VARCHAR(255) NOT NULL,
  `dataLimit` ENUM('10', '25', '50', '100') NOT NULL DEFAULT '100',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Supports`
--

CREATE TABLE IF NOT EXISTS `Supports` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `categoryID` INT(11) NOT NULL,
  `serverID` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `statusID` ENUM('1', '2', '3', '4') NOT NULL DEFAULT '1',
  `readStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `updateDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `SupportMessages`
--

CREATE TABLE IF NOT EXISTS `SupportMessages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `supportID` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `message` TEXT NOT NULL,
  `writeLocation` ENUM('1', '2') NOT NULL DEFAULT '1',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `SupportCategories`
--

CREATE TABLE IF NOT EXISTS `SupportCategories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `userTemplate` TEXT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `SupportAnswers`
--

CREATE TABLE IF NOT EXISTS `SupportAnswers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Games`
--

CREATE TABLE IF NOT EXISTS `Games` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `imageID` CHAR(32) NOT NULL,
  `imageType` VARCHAR(6) NOT NULL DEFAULT 'jpg',
  `content` TEXT NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Pages`
--

CREATE TABLE IF NOT EXISTS `Pages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Download`
--

CREATE TABLE IF NOT EXISTS `Downloads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `downloadURL` TEXT NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Payment`
--

CREATE TABLE IF NOT EXISTS `Payment` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `apiID` VARCHAR(255) NOT NULL DEFAULT 'other',
  `title` VARCHAR(255) NOT NULL,
  `type` ENUM('1', '2', '3') NOT NULL DEFAULT '1',
  `bonusCredit` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `bonusCreditMinAmount` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `PaymentSettings`
--

CREATE TABLE IF NOT EXISTS `PaymentSettings` (
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `variables` TEXT NOT NULL,
  `status` ENUM('0', '1') NOT NULL DEFAULT '0',
  PRIMARY KEY(`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Broadcast`
--

CREATE TABLE IF NOT EXISTS `Broadcast` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `url` VARCHAR(255) NOT NULL DEFAULT '#',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ApplicationForms`
--

CREATE TABLE IF NOT EXISTS `ApplicationForms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `reappliable` ENUM('0', '1') NOT NULL DEFAULT '1',
  `isEnabled` ENUM('0', '1') NOT NULL DEFAULT '1',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ApplicationFormQuestions`
--

CREATE TABLE IF NOT EXISTS `ApplicationFormQuestions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `formID` INT(11) NOT NULL,
  `question` TEXT NOT NULL,
  `type` INT(2) NOT NULL,
  `variables` TEXT NOT NULL,
  `isEnabled` ENUM('0', '1') NOT NULL DEFAULT '1',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Tablo için tablo yapısı `Applications`
--

CREATE TABLE IF NOT EXISTS `Applications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `formID` INT(11) NOT NULL,
  `reason` TEXT NOT NULL,
  `status` INT(1) UNSIGNED NOT NULL DEFAULT '2',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ApplicationAnswers`
--

CREATE TABLE IF NOT EXISTS `ApplicationAnswers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `applicationID` INT(11) NOT NULL,
  `questionID` INT(11) NOT NULL,
  `answer` TEXT NOT NULL,
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Slider`
--

CREATE TABLE IF NOT EXISTS `Slider` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `url` VARCHAR(255) NOT NULL DEFAULT '#',
  `imageID` CHAR(32) NOT NULL,
  `imageType` VARCHAR(6) NOT NULL DEFAULT 'jpg',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Theme`
--

CREATE TABLE IF NOT EXISTS `Theme` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `header` TEXT NOT NULL,
  `customCSS` TEXT NULL,
  `updatedAt` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Settings`
--

CREATE TABLE IF NOT EXISTS `Settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `themeName` VARCHAR(255) NOT NULL DEFAULT 'blaze',
  `serverName` VARCHAR(255) NOT NULL,
  `serverIP` VARCHAR(255) NOT NULL,
  `serverVersion` VARCHAR(255) NOT NULL,
  `siteSlogan` VARCHAR(32) NOT NULL,
  `siteDescription` VARCHAR(155) NULL,
  `siteTags` TEXT NULL,
  `rules` TEXT NOT NULL,
  `supportMessageTemplate` TEXT NOT NULL,
  `footerFacebook` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerTwitter` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerInstagram` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerYoutube` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerDiscord` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerEmail` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerPhone` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerWhatsapp` VARCHAR(255) NOT NULL DEFAULT '0',
  `footerAboutText` VARCHAR(255) NOT NULL DEFAULT '0',
  `recaptchaPagesStatus` TEXT NOT NULL,
  `recaptchaPublicKey` VARCHAR(255) NOT NULL DEFAULT '0',
  `recaptchaPrivateKey` VARCHAR(255) NOT NULL DEFAULT '0',
  `analyticsUA` VARCHAR(255) NOT NULL DEFAULT '0',
  `tawktoID` VARCHAR(255) NOT NULL DEFAULT '0',
  `bonusCredit` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `oneSignalAppID` VARCHAR(255) NOT NULL DEFAULT '0',
  `oneSignalAPIKey` VARCHAR(255) NOT NULL DEFAULT '0',
  `headerLogoType` ENUM('1', '2') NOT NULL DEFAULT '1',
  `topSalesStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `avatarAPI` ENUM('1', '2') NOT NULL DEFAULT '1',
  `onlineAPI` ENUM('1', '2', '3', '4', '5', '6') NOT NULL DEFAULT '1',
  `passwordType` ENUM('1', '2', '3') NOT NULL DEFAULT '1',
  `sslStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `maintenanceStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `creditStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `giftStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `authStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `preloaderStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `debugModeStatus` ENUM('0', '1') NOT NULL DEFAULT '0',
  `registerLimit` ENUM('0', '1', '2', '3') NOT NULL DEFAULT '0',
  `newsLimit` ENUM('3', '6', '9', '12') NOT NULL DEFAULT '6',
  `commentsStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `storeDiscount` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `storeDiscountExpiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `storeDiscountProducts` TEXT NOT NULL,
  `minPay` INT(5) UNSIGNED NOT NULL DEFAULT '1',
  `maxPay` INT(5) UNSIGNED NOT NULL DEFAULT '100',
  `smtpServer` VARCHAR(255) NULL,
  `smtpPort` VARCHAR(255) NULL,
  `smtpSecure` ENUM('1', '2') NOT NULL DEFAULT '1',
  `smtpUsername` VARCHAR(255) NULL,
  `smtpPassword` VARCHAR(255) NULL,
  `smtpPasswordTemplate` TEXT NOT NULL,
  `smtpTFATemplate` TEXT NOT NULL,
  `webhookCreditURL` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookStoreURL` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookSupportURL` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookNewsURL` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookLotteryURL` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookApplicationURL` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookCreditTitle` VARCHAR(255) NOT NULL DEFAULT 'Credit',
  `webhookStoreTitle` VARCHAR(255) NOT NULL DEFAULT 'Store',
  `webhookSupportTitle` VARCHAR(255) NOT NULL DEFAULT 'Support',
  `webhookNewsTitle` VARCHAR(255) NOT NULL DEFAULT 'Blog',
  `webhookLotteryTitle` VARCHAR(255) NOT NULL DEFAULT 'Wheel of Fortune',
  `webhookApplicationTitle` VARCHAR(255) NOT NULL DEFAULT 'Application',
  `webhookCreditMessage` TEXT NOT NULL,
  `webhookStoreMessage` TEXT NOT NULL,
  `webhookSupportMessage` TEXT NOT NULL,
  `webhookNewsMessage` TEXT NOT NULL,
  `webhookLotteryMessage` TEXT NOT NULL,
  `webhookApplicationMessage` TEXT NOT NULL,
  `webhookCreditEmbed` TEXT NOT NULL,
  `webhookStoreEmbed` TEXT NOT NULL,
  `webhookSupportEmbed` TEXT NOT NULL,
  `webhookNewsEmbed` TEXT NOT NULL,
  `webhookLotteryEmbed` TEXT NOT NULL,
  `webhookApplicationEmbed` TEXT NOT NULL,
  `webhookCreditImage` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookStoreImage` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookSupportImage` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookNewsImage` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookLotteryImage` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookApplicationImage` VARCHAR(255) NOT NULL DEFAULT '0',
  `webhookCreditColor` CHAR(6) NOT NULL DEFAULT '000000',
  `webhookStoreColor` CHAR(6) NOT NULL DEFAULT '000000',
  `webhookSupportColor` CHAR(6) NOT NULL DEFAULT '000000',
  `webhookNewsColor` CHAR(6) NOT NULL DEFAULT '000000',
  `webhookLotteryColor` CHAR(6) NOT NULL DEFAULT '000000',
  `webhookApplicationColor` CHAR(6) NOT NULL DEFAULT '000000',
  `webhookCreditAdStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `webhookStoreAdStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `webhookSupportAdStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `webhookNewsAdStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `webhookLotteryAdStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `webhookApplicationAdStatus` ENUM('0', '1') NOT NULL DEFAULT '1',
  `lastCheckAccounts` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `totalAccountCount` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `thisYearAccountCount` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `thisMonthAccountCount` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `lastMonthAccountCount` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `language` VARCHAR(255) NOT NULL DEFAULT 'en',
  `timezone` VARCHAR(255) NULL,
  `currency` VARCHAR(255) NOT NULL DEFAULT 'USD',
  `creditIcon` VARCHAR(255) NOT NULL,
  `creditMultiplier` INT(11) NOT NULL DEFAULT '1',
  `bonusCreditMinAmount` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `updatedAt` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `bazaarCommission` INT(11) NOT NULL DEFAULT '0',
  `gamingNightDay` varchar(255) NOT NULL DEFAULT 'Friday',
  `gamingNightStart` varchar(255) NOT NULL DEFAULT '2000',
  `gamingNightEnd` varchar(255) NOT NULL DEFAULT '2359',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `Notifications`
--

CREATE TABLE IF NOT EXISTS `Notifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `type` ENUM('1', '2', '3', '4') NOT NULL DEFAULT '1',
  `variables` VARCHAR(255) NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ConsoleHistory`
--

CREATE TABLE IF NOT EXISTS `ConsoleHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `serverID` INT(11) NOT NULL,
  `command` VARCHAR(255) NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `ChatHistory`
--

CREATE TABLE IF NOT EXISTS `ChatHistory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo için tablo yapısı `OnlineAccountsHistory`
--

CREATE TABLE IF NOT EXISTS `OnlineAccountsHistory` (
  `accountID` INT(11) NOT NULL,
  `type` ENUM('0', '1') NOT NULL DEFAULT '0',
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`accountID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `HelpArticles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `accountID` int(11) NOT NULL,
    `topicID` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `content` text NOT NULL,
    `views` int(10) unsigned NOT NULL DEFAULT 0,
    `likesCount` int(10) unsigned NOT NULL DEFAULT 0,
    `dislikesCount` int(10) unsigned NOT NULL DEFAULT 0,
    `updateDate` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
    `creationDate` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `HelpTopics` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `imageID` char(32) NOT NULL,
    `imageType` varchar(6) NOT NULL DEFAULT 'jpg',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table `Languages`
--

CREATE TABLE IF NOT EXISTS `Languages` (
    `code` VARCHAR(10) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Modules` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `settings` TEXT NOT NULL,
  `isEnabled` ENUM('0', '1') NOT NULL DEFAULT '1',
  PRIMARY KEY(`id`),
  UNIQUE KEY(`name`, `slug`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `CustomForms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `isEnabled` ENUM('0', '1') NOT NULL DEFAULT '1',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `CustomFormQuestions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `formID` INT(11) NOT NULL,
  `question` TEXT NOT NULL,
  `type` INT(2) NOT NULL,
  `variables` TEXT NOT NULL,
  `isEnabled` ENUM('0', '1') NOT NULL DEFAULT '1',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Forms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `formID` INT(11) NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `FormAnswers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `applicationID` INT(11) NOT NULL,
  `questionID` INT(11) NOT NULL,
  `answer` TEXT NOT NULL,
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `BazaarItems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `serverID` int(11) NOT NULL,
  `itemID` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lore` text NULL,
  `amount` int(11) NOT NULL DEFAULT 1,
  `durability` int(11) NOT NULL,
  `maxDurability` int(11) NOT NULL,
  `enchantments` text NULL,
  `base64` text NOT NULL,
  `description` text NULL,
  `price` int(11) NOT NULL,
  `sold` ENUM('0', '1') NOT NULL DEFAULT '0',
  `creationDate` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `BazaarHistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `type` ENUM('0', '1') NOT NULL DEFAULT '0',
  `creationDate` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY(`name`, `slug`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY(`name`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `RolePermissions` (
  `roleID` int(11) NOT NULL,
  `permissionID` int(11) NOT NULL,
  PRIMARY KEY (`roleID`, `permissionID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AccountPermissions` (
  `accountID` int(11) NOT NULL,
  `permissionID` int(11) NOT NULL,
  PRIMARY KEY (`accountID`, `permissionID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AccountRoles` (
  `accountID` int(11) NOT NULL,
  `roleID` int(11) NOT NULL,
  `expiryDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`accountID`, `roleID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Logs`;
CREATE TABLE IF NOT EXISTS `Logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountID` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ShoppingCarts` (
  `accountID` int(11) NOT NULL,
  `couponID` int(11) NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`accountID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ShoppingCartProducts` (
  `shoppingCartID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`shoppingCartID`, `productID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountID` int(11) NOT NULL,
  `coupon` varchar(255) NULL,
  `total` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL,
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `OrderProducts` (
  `orderID` int(11) NOT NULL,
  `productID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`orderID`, `productID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ForumCategories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `parentID` INT(11) NOT NULL DEFAULT '0',
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `imageID` CHAR(32) NOT NULL,
  `imageType` VARCHAR(6) NOT NULL DEFAULT 'jpg',
  `isEnabled` ENUM('0', '1') NOT NULL DEFAULT '1',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ForumCategoryRoles` (
  `categoryID` INT(11) NOT NULL,
  `roleID` INT(11) NOT NULL,
  `type` INT(11) NOT NULL,
  PRIMARY KEY(`categoryID`, `roleID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ForumThreads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `categoryID` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `views` INT(11) NOT NULL,
  `updatedDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ForumReplies` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `accountID` INT(11) NOT NULL,
  `threadID` INT(11) NOT NULL,
  `replyTo` INT(11) NOT NULL,
  `message` TEXT NOT NULL,
  `updatedDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `creationDate` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `VIPTables` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `serverID` INT(11) NOT NULL,
  `categoryID` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `VIPs` (
  `tableID` INT(11) NOT NULL,
  `vipID` INT(11) NOT NULL,
  PRIMARY KEY(`tableID`, `vipID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `VIPTitles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tableID` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `VIPDesc` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `vipID` INT(11) NOT NULL,
  `titleID` INT(11) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `VIPExplain` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titleID` INT(11) NOT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY(`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `SeoPages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` TEXT NULL,
  `image` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY(`page`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `GamingNightProducts` (
  `productID` INT(11) NOT NULL,
  `price` INT(11) NOT NULL,
  `stock` INT(11) NOT NULL,
  PRIMARY KEY(`productID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
