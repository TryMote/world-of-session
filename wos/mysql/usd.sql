DROP TABLE IF EXISTS user_second_data;
CREATE TABLE user_second_data(
	photo VARCHAR(50),
	user_xp INT(5) UNSIGNED NOT NULL,
	vk VARCHAR(50),
	creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	INDEX(user_xp),
	INDEX(vk(10)),
	INDEX(creation_date(5))
);