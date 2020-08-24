/* Make sure the database does not exist */
DROP DATABASE IF EXISTS VIPdatabase;

/* Creating the Database*/
CREATE DATABASE VIPdatabase
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;


/*Create items table*/
CREATE TABLE VIPdatabase.items
(
    item_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    price FLOAT NOT NULL,
    color VARCHAR(150) NOT NULL,
    size FLOAT NOT NULL,
    material VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    node INT
)
ENGINE = InnoDB;

CREATE TABLE VIPdatabase.map
(
    section VARCHAR(100),
    node INT
)
ENGINE = InnoDB;