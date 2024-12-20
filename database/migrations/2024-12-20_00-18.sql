-- Changement de la manière dont les logos des sources sont traités
ALTER TABLE `newssource` 
CHANGE `logo` `logo_light` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL, 
CHANGE `logo_invertable` `logo_dark` TEXT NULL DEFAULT NULL; 

UPDATE `newssource` SET `logo_dark` = `logo_light`;