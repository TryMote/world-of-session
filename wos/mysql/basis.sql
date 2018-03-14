DROP TABLE IF EXISTS user_primary_data;
CREATE TABLE user_primary_data(
	user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	first_name VARCHAR(20) NOT NULL,
	last_name VARCHAR(30) NOT NULL,
	email VARCHAR(50) NOT NULL,
	gender TINYINT DEFAULT 0,
	INDEX(first_name(5)),
	INDEX(last_name(5)),
	INDEX(email(10)),
	INDEX(gender(1))
	) ENGINE innoDB;

DROP TABLE IF EXISTS user_second_data;
CREATE TABLE user_second_data(
	photo VARCHAR(50),
	user_xp INT(5) UNSIGNED NOT NULL,
	vk VARCHAR(50),
	creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
	INDEX(user_xp),
	INDEX(vk(10)),
	INDEX(creation_date(5))
) ENGINE innoDB;

DROP TABLE IF EXISTS sign_in;
CREATE TABLE sign_in(
	nickname VARCHAR(30),
	password VARCHAR(25),
	INDEX(nickname(5))
) ENGINE innoDB;

DROP TABLE IF EXISTS subjects;
CREATE TABLE subjects(
	subject_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	subject_name VARCHAR(40) NOT NULL,
	INDEX(subject_name(5))
) ENGINE innoDB;

DROP TABLE IF EXISTS topics;
CREATE TABLE topics(
	topic_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	topic_name VARCHAR(40) NOT NULL,
	INDEX(topic_name(5))
) ENGINE innoDB;

DROP TABLE IF EXISTS lections;
CREATE TABLE lections(
	lection_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	lection_name VARCHAR(40) NOT NULL,
	INDEX(lection_name(5))
) ENGINE innoDB;

DROP TABLE IF EXISTS teachers;
CREATE TABLE teachers(
	teacher_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	teacher_name VARCHAR(30) NOT NULL,
	INDEX(teacher_name(5))
) ENGINE innoDB;

DROP TABLE IF EXISTS user_subjects;
CREATE TABLE user_subjects(
	progress INT(3) UNSIGNED NOT NULL DEFAULT 0,
	start_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	INDEX(progress),
	INDEX(start_date(5))
) ENGINE innoDB;

DROP TABLE IF EXISTS statuses;
CREATE TABLE statuses(
	status_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	status_name VARCHAR(40) NOT NULL,
	status_xp INT(5) NOT NULL,
	INDEX(status_name(5)),
	INDEX(status_xp)
) ENGINE innoDB;