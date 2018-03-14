DROP TABLE IF EXISTS statuses;
CREATE TABLE statuses(
	status_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	status_name VARCHAR(40) NOT NULL,
	status_xp INT(5) NOT NULL,
	INDEX(status_name(5)),
	INDEX(status_xp)
) ENGINE innoDB;