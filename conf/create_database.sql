DROP DATABASE historylog;

CREATE DATABASE historylog
       DEFAULT CHARACTER SET utf8
       DEFAULT COLLATE utf8_general_ci;

USE historylog;
CREATE TABLE sources (
	id INT PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(200) NOT NULL,
	author VARCHAR(80),
	url VARCHAR(250));

CREATE TABLE quotations (
	id INT PRIMARY KEY AUTO_INCREMENT,
	source_id INT NOT NULL,
	content TEXT NOT NULL,
	description VARCHAR(120),
	start_time DATETIME,
	end_time DATETIME);

CREATE TABLE tags (
	id INT PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL);

CREATE TABLE quotation_tags (
	quotation_id INT NOT NULL,
	tag_id INT NOT NULL,
	PRIMARY KEY (quotation_id,tag_id));

