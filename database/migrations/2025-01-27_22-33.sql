-- Une source correspond à un organe de presse, qui peut distribuer un ou plusieurs fluxs.

-- Les anciennes sources deviennent des fluxs
RENAME TABLE `newssource` TO `newsfeed`; 

-- Créer la nouvelle table des sources
CREATE TABLE `newssource` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` TEXT NOT NULL,
    `logo_light` TEXT NOT NULL,
    `logo_dark` TEXT NOT NULL,
    `address` TEXT NULL,
    `website` TEXT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

-- Ajouter un ID de source à chaque flux
ALTER TABLE `newsfeed` ADD `newssource_id` INT NOT NULL AFTER `name`; 
ALTER TABLE `newsfeed` ADD FOREIGN KEY (`newssource_id`) REFERENCES `newssource`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- Copier tous les flux existants dans la table des sources
INSERT INTO `newssource`(`name`, `logo_light`, `logo_dark`)
SELECT `name`, `logo_light`, `logo_dark` FROM `newsfeed`;

-- Lier chaque flux à sa nouvelle source
UPDATE `newsfeed` 
SET `newssource_id` = (SELECT `id` FROM `newssource` WHERE `newssource`.`name` = `newsfeed`.`name`);

-- Retirer leur logo aux fluxs
ALTER TABLE `newsfeed` DROP `logo_light`; 
ALTER TABLE `newsfeed` DROP `logo_dark`; 

-- Renommer la table intermédiaire
RENAME TABLE `newscategory_has_newssource` TO `newscategory_has_newsfeed`; 
ALTER TABLE `newscategory_has_newssource` CHANGE `newssource_uuid` `newsfeed_uuid` VARCHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL; 

-- Créer la table des articles
CREATE TABLE `newspost` (
    `guid` VARCHAR(255) NOT NULL,
    `newsfeed_uuid` VARCHAR(36) NOT NULL,
    `title` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `thumbnail_src` TEXT NOT NULL,
    `publication_date` DATETIME NOT NULL,
    `link` TEXT NOT NULL,
    PRIMARY KEY (`guid`, `newsfeed_uuid`)
) ENGINE = InnoDB;