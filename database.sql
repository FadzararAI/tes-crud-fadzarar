CREATE DATABASE IF NOT EXISTS tes_fadzarar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tes_fadzarar;
CREATE TABLE categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;
CREATE TABLE items (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 sku VARCHAR(40) NOT NULL UNIQUE,
 name VARCHAR(120) NOT NULL,
 category_id INT UNSIGNED NOT NULL,
 unit VARCHAR(24) NOT NULL,
 unit_price DECIMAL(14,2) UNSIGNED NOT NULL,
 stock INT UNSIGNED NOT NULL DEFAULT 0,
 FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE transactions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 item_id INT UNSIGNED NOT NULL,
 type ENUM('IN','OUT') NOT NULL,
 quantity INT UNSIGNED NOT NULL,
 transaction_date DATE NOT NULL,
 notes VARCHAR(500) NOT NULL DEFAULT '',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX (transaction_date),
 FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
INSERT INTO categories (id,name) VALUES (1,'Packaging'),(2,'Office supplies'),(3,'Safety equipment');
INSERT INTO items (id,sku,name,category_id,unit,unit_price) VALUES
 (1,'PKG-001','Shipping carton · medium',1,'box',8500),
 (2,'PKG-002','Packing tape',1,'roll',12000),
 (3,'OFF-001','A4 copy paper',2,'ream',55000),
 (4,'OFF-002','Permanent marker',2,'piece',7500),
 (5,'SAF-001','Warehouse work gloves',3,'pair',25000);
START TRANSACTION;
INSERT INTO transactions (item_id,type,quantity,transaction_date,notes) VALUES
 (1,'IN',120,'2026-11-01','Sample opening receipt'),(2,'IN',48,'2026-11-01','Sample opening receipt'),
 (3,'IN',30,'2026-11-02','Sample opening receipt'),(4,'IN',60,'2026-11-02','Sample opening receipt'),
 (5,'IN',25,'2026-11-03','Sample opening receipt'),(1,'OUT',15,'2026-11-04','Sample dispatch');
UPDATE items SET stock = (SELECT COALESCE(SUM(CASE WHEN type='IN' THEN quantity ELSE -CAST(quantity AS SIGNED) END),0) FROM transactions WHERE item_id=items.id);
COMMIT;
