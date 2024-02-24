create table if not exists `user` (
    `id` int(11) not null auto_increment,
    `name` varchar(100) not null,
    `email` varchar(100) not null unique,
    `password` varchar(100) not null,
    `created` datetime not null,
    `modified` datetime not null,
    primary key (`id`)
) engine = InnoDB default charset = utf8;

create index idx_usuario_email on `user` (`email`);

insert into `user` (`id`, `name`, `email`, `password`, `created`, `modified`) values
    (1, 'Administrador', 'adm@dossier.com' , '$2y$10$6Zwe9nnh/FAYR57Xn9BjPuBhvpbHIGk/NmqpqhOZdNODKneJecJ2e', now(), now());
