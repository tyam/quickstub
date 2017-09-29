
/* CREATE DATABASE quickstub; */

CREATE TABLE sequence (
    name VARCHAR(50) NOT NULL,
    current_value BIGINT NOT NULL,
    increment INT NOT NULL DEFAULT 1,
    PRIMARY KEY (name)
);

DELIMITER $
CREATE FUNCTION currval (seq_name VARCHAR(50))
    RETURNS BIGINT
    LANGUAGE SQL
    DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
    DECLARE value BIGINT;
    SET value = 0;
    SELECT current_value INTO value
        FROM sequence
        WHERE name = seq_name;
    RETURN value;
END
$
DELIMITER ;

DELIMITER $
CREATE FUNCTION nextval (seq_name VARCHAR(50))
    RETURNS BIGINT
    LANGUAGE SQL
    DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
    UPDATE sequence
    SET current_value = current_value + increment
    WHERE name = seq_name;
    RETURN currval(seq_name);
END
$
DELIMITER ;

DELIMITER $
CREATE FUNCTION setval (seq_name VARCHAR(50), value BIGINT)
    RETURNS BIGINT
    LANGUAGE SQL
    DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
    UPDATE sequence
    SET current_value = value
    WHERE name = seq_name;
    RETURN currval(seq_name);
END
$
DELIMITER ;

CREATE TABLE user (
    userId BIGINT NOT NULL PRIMARY KEY, 
    displayName varchar(50) NOT NULL, 
    created DATETIME NOT NULL
);
INSERT INTO sequence VALUES ('userId', 121, 1);
