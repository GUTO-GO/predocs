create table if not exists `user` (
    `id` int(11) not null auto_increment,
    `name` varchar(100) not null,
    `email` varchar(100) not null unique,
    `password` text not null,
    `created` datetime not null,
    `modified` datetime not null,
    primary key (`id`)
) engine = InnoDB default charset = utf8;

create index idx_usuario_email on `user` (`email`);

CREATE TABLE user_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    old_data TEXT,
    new_data TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER user_log_trigger_insert
AFTER INSERT ON `user`
FOR EACH ROW
BEGIN
    INSERT INTO user_log (user_id, action, old_data, new_data)
    VALUES (NEW.id, 'INSERT', NULL, JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'email', NEW.email, 'password', NEW.password, 'created', NEW.created, 'modified', NEW.modified));
END ;

CREATE TRIGGER user_log_trigger_update
AFTER UPDATE ON `user`
FOR EACH ROW
BEGIN
    INSERT INTO user_log (user_id, action, old_data, new_data)
    VALUES (NEW.id, 'UPDATE', JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'email', OLD.email, 'password', OLD.password, 'created', OLD.created, 'modified', OLD.modified), JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'email', NEW.email, 'password', NEW.password, 'created', NEW.created, 'modified', NEW.modified));
END ;

CREATE TRIGGER user_log_trigger_delete
AFTER DELETE ON `user`
FOR EACH ROW
BEGIN
    INSERT INTO user_log (user_id, action, old_data, new_data)
    VALUES (OLD.id, 'DELETE', JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'email', OLD.email, 'password', OLD.password, 'created', OLD.created, 'modified', OLD.modified), NULL);
END ;

insert into `user` (`id`, `name`, `email`, `password`, `created`, `modified`) values
    (1, 'Administrador', 'adm@dossier.com' , '$2y$10$6Zwe9nnh/FAYR57Xn9BjPuBhvpbHIGk/NmqpqhOZdNODKneJecJ2e', now(), now());
