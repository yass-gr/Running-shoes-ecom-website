ALTER TABLE Products ADD COLUMN gender ENUM('mens','womens','unisex') NOT NULL DEFAULT 'unisex' AFTER sales;

UPDATE Products SET gender = 'mens' WHERE name LIKE "Men's%";
UPDATE Products SET gender = 'womens' WHERE name LIKE "Women's%";
UPDATE Products SET gender = 'unisex' WHERE gender = 'unisex' AND id <= 21 OR id BETWEEN 39 AND 43;
