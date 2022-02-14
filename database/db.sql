
use Quizty;

CREATE TABLE Quiz (
id VARCHAR(255) NOT NULL,
user_id VARCHAR(255) NOT NULL,
name VARCHAR(255) NOT NULL,
date_time DATETIME NOT NULL,
duration INT NOT NULL,
questions TEXT NOT NULL,
answers TEXT NOT NULL,
enable TINYINT DEFAULT 1
);

CREATE TABLE `User` (
id VARCHAR(255) NOT NULL,
name VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL,
`password` VARCHAR(255) NOT NULL,
enable TINYINT DEFAULT 1
);

CREATE TABLE `Session`(
id VARCHAR(255) NOT NULL,
user_id VARCHAR(255) NOT NULL,
user_email VARCHAR(255) NOT NULL,
date_time DATETIME NOT NULL,
enable TINYINT DEFAULT 1
);

CREATE TABLE Solved_Quiz (
id INT NOT NULL AUTO_INCREMENT,
quiz_id VARCHAR(255) NOT NULL,
user_id VARCHAR(255) ,
date_time DATETIME NOT NULL,
duration INT NOT NULL,
answers TEXT NOT NULL,
enable TINYINT DEFAULT 1,
PRIMARY KEY(`id`)
);


