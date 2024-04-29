CREATE TABLE users (
  User VARCHAR(255) BINARY NOT NULL,
  Password VARCHAR(255) BINARY NOT NULL,
  Uid INT NOT NULL default '-1',
  Gid INT NOT NULL default '-1',
  QuotaSize INT NOT NULL default '100',
  Dir VARCHAR(255) BINARY NOT NULL,
  PRIMARY KEY (User)
);

INSERT INTO users (User,Password,Uid,Gid,QuotaSize,Dir) VALUES ('felipe@email.com',ENCRYPT('test'),'1003','1005',100,'/storage');
