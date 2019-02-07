ALTER TABLE comments MODIFY COLUMN user_id int(11) NULL DEFAULT '0';
ALTER TABLE comments DROP FOREIGN KEY comments_ibfk_1;
ALTER TABLE comments ADD CONSTRAINT comments_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL;
ALTER TABLE user ADD COLUMN last_login datetime NOT NULL DEFAULT '2000-01-01 00:00:00', ADD COLUMN auth_method varchar(50) DEFAULT NULL;