ALTER TABLE `newspost` CHANGE `thumbnail_src` `thumbnail_src` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL; 
ALTER TABLE `newssource` CHANGE `logo_light` `logo_light` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL, CHANGE `logo_dark` `logo_dark` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL; 