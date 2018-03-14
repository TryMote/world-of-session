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