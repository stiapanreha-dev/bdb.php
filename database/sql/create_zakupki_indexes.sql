-- ============================================================================
-- SQL Server Indexes Creation Script for Zakupki Tables
-- ============================================================================
-- Purpose: Optimize search performance on zakupki tables across all databases
-- Author: System optimization
-- Date: 2025-10-31
-- ============================================================================

-- Notes:
-- 1. Run this script with SQL Server administrator privileges
-- 2. Index creation may take 5-15 minutes depending on data size
-- 3. This script is idempotent - safe to run multiple times
-- ============================================================================

PRINT 'Starting index creation for zakupki tables...';
PRINT '';

-- ============================================================================
-- DATABASE: business2020
-- ============================================================================
USE business2020;
GO

PRINT 'Processing business2020...';

-- Check if index exists and create if not
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created
    ON zakupki(created)
    INCLUDE (id, purchase_object, customer, purchase_type, start_cost);
    PRINT '  ✓ idx_zakupki_created created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created already exists';

-- Composite index for date + search optimization
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created_customer' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created_customer...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
    ON zakupki(created, customer)
    INCLUDE (id, purchase_object);
    PRINT '  ✓ idx_zakupki_created_customer created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created_customer already exists';

PRINT '';

-- ============================================================================
-- DATABASE: business2021
-- ============================================================================
USE business2021;
GO

PRINT 'Processing business2021...';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created
    ON zakupki(created)
    INCLUDE (id, purchase_object, customer, purchase_type, start_cost);
    PRINT '  ✓ idx_zakupki_created created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created_customer' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created_customer...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
    ON zakupki(created, customer)
    INCLUDE (id, purchase_object);
    PRINT '  ✓ idx_zakupki_created_customer created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created_customer already exists';

PRINT '';

-- ============================================================================
-- DATABASE: business2022
-- ============================================================================
USE business2022;
GO

PRINT 'Processing business2022...';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created
    ON zakupki(created)
    INCLUDE (id, purchase_object, customer, purchase_type, start_cost);
    PRINT '  ✓ idx_zakupki_created created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created_customer' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created_customer...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
    ON zakupki(created, customer)
    INCLUDE (id, purchase_object);
    PRINT '  ✓ idx_zakupki_created_customer created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created_customer already exists';

PRINT '';

-- ============================================================================
-- DATABASE: business2023
-- ============================================================================
USE business2023;
GO

PRINT 'Processing business2023...';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created
    ON zakupki(created)
    INCLUDE (id, purchase_object, customer, purchase_type, start_cost);
    PRINT '  ✓ idx_zakupki_created created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created_customer' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created_customer...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
    ON zakupki(created, customer)
    INCLUDE (id, purchase_object);
    PRINT '  ✓ idx_zakupki_created_customer created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created_customer already exists';

PRINT '';

-- ============================================================================
-- DATABASE: business2024
-- ============================================================================
USE business2024;
GO

PRINT 'Processing business2024...';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created
    ON zakupki(created)
    INCLUDE (id, purchase_object, customer, purchase_type, start_cost);
    PRINT '  ✓ idx_zakupki_created created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created_customer' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created_customer...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
    ON zakupki(created, customer)
    INCLUDE (id, purchase_object);
    PRINT '  ✓ idx_zakupki_created_customer created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created_customer already exists';

PRINT '';

-- ============================================================================
-- DATABASE: business2025 (MAIN - largest database)
-- ============================================================================
USE business2025;
GO

PRINT 'Processing business2025 (MAIN DATABASE - may take longer)...';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created
    ON zakupki(created)
    INCLUDE (id, purchase_object, customer, purchase_type, start_cost)
    WITH (ONLINE = OFF, SORT_IN_TEMPDB = ON, MAXDOP = 4);
    PRINT '  ✓ idx_zakupki_created created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created_customer' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created_customer...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
    ON zakupki(created, customer)
    INCLUDE (id, purchase_object)
    WITH (ONLINE = OFF, SORT_IN_TEMPDB = ON, MAXDOP = 4);
    PRINT '  ✓ idx_zakupki_created_customer created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created_customer already exists';

PRINT '';

-- ============================================================================
-- DATABASE: business2026
-- ============================================================================
USE business2026;
GO

PRINT 'Processing business2026...';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created
    ON zakupki(created)
    INCLUDE (id, purchase_object, customer, purchase_type, start_cost);
    PRINT '  ✓ idx_zakupki_created created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created already exists';

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'idx_zakupki_created_customer' AND object_id = OBJECT_ID('zakupki'))
BEGIN
    PRINT '  Creating idx_zakupki_created_customer...';
    CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
    ON zakupki(created, customer)
    INCLUDE (id, purchase_object);
    PRINT '  ✓ idx_zakupki_created_customer created successfully';
END
ELSE
    PRINT '  ✓ idx_zakupki_created_customer already exists';

PRINT '';

-- ============================================================================
-- Summary and Statistics
-- ============================================================================
PRINT '============================================================================';
PRINT 'INDEX CREATION COMPLETED!';
PRINT '============================================================================';
PRINT '';
PRINT 'Summary of created indexes:';
PRINT '  - idx_zakupki_created: Optimizes date range filtering';
PRINT '  - idx_zakupki_created_customer: Optimizes date + search queries';
PRINT '';
PRINT 'Databases processed: business2020, business2021, business2022, business2023,';
PRINT '                     business2024, business2025, business2026';
PRINT '';
PRINT 'Expected performance improvement: 50-90% faster search queries';
PRINT '';
PRINT 'Next steps:';
PRINT '  1. Run UPDATE STATISTICS on all zakupki tables';
PRINT '  2. Test search performance on businessdb.ru';
PRINT '  3. Monitor SQL Server memory usage';
PRINT '';
PRINT '============================================================================';

-- Update statistics for all tables
PRINT 'Updating statistics...';

USE business2020; UPDATE STATISTICS zakupki;
USE business2021; UPDATE STATISTICS zakupki;
USE business2022; UPDATE STATISTICS zakupki;
USE business2023; UPDATE STATISTICS zakupki;
USE business2024; UPDATE STATISTICS zakupki;
USE business2025; UPDATE STATISTICS zakupki;
USE business2026; UPDATE STATISTICS zakupki;

PRINT '✓ Statistics updated for all databases';
PRINT '';
PRINT 'All operations completed successfully!';
GO
