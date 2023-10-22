CREATE TABLE IF NOT EXISTS `usuario` (
	`id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`nome` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`senha` VARCHAR(512) NOT NULL,
	`status` TEXT DEFAULT 'ativo',
	`tipo` TEXT DEFAULT 'usuario',
	`criado` DATETIME,
	`modificado` DATETIME
);