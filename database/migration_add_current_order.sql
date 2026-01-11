-- Migration: Add Table Management System
-- Date: January 11, 2026
-- Description: Add current_order_id to tables, update table_number type, add indexes

USE warkop_qr;

-- Check if column exists before adding (untuk safety)
SET @dbname = DATABASE();
SET @tablename = 'tables';
SET @columnname = 'current_order_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column exists, skipping...' AS message;",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT NULL AFTER status;")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add foreign key constraint if not exists
SET @constraint_name = 'fk_current_order';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE
      CONSTRAINT_SCHEMA = @dbname
      AND CONSTRAINT_NAME = @constraint_name
      AND TABLE_NAME = @tablename
  ) > 0,
  "SELECT 'Constraint exists, skipping...' AS message;",
  CONCAT("ALTER TABLE ", @tablename, " ADD CONSTRAINT ", @constraint_name, 
         " FOREIGN KEY (current_order_id) REFERENCES orders(id) ON DELETE SET NULL;")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Change table_number type in orders from INT to VARCHAR
ALTER TABLE orders 
MODIFY COLUMN table_number VARCHAR(10) NOT NULL;

-- Add indexes for performance (check if exists first)
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
    WHERE table_schema = DATABASE() AND table_name = 'orders' AND index_name = 'idx_table_status');
SET @sqlstmt := IF(@exist > 0, 'SELECT "Index idx_table_status already exists"', 
    'CREATE INDEX idx_table_status ON orders(table_number, status)');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
    WHERE table_schema = DATABASE() AND table_name = 'orders' AND index_name = 'idx_active_orders');
SET @sqlstmt := IF(@exist > 0, 'SELECT "Index idx_active_orders already exists"', 
    'CREATE INDEX idx_active_orders ON orders(table_number, status, created_at)');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Success message
SELECT '  Migration completed successfully!' AS message;
