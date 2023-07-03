create table if not exists `usuario` (
	`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`nome` varchar(255) NOT NULL,
	`email` varchar(255) NOT NULL,
	`senha` varchar(512) NOT NULL,
	`ativo` boolean default true,
	`criado` datetime,
	`modificado` datetime
);