DROP TABLE IF EXISTS sign_in;
CREATE TABLE sign_in(
	nickname VARCHAR(30),
	password VARCHAR(25),
	INDEX(nickname(5))
) ENGINE innoDB;