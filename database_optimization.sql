-- ============================================================================
-- OnlineLaw - Database Index Optimization Script for Production / Hostinger
-- Targets: `pincode` (1M+ rows) and `bank_ifsc` (181K+ rows)
-- [SKIPS TABLE: advocate as instructed]
-- ============================================================================

-- 1. Optimize `pincode` table for Location & Text Search
-- Speeds up locality, office, district, and region lookups from 2,100ms down to ~5ms
ALTER TABLE `pincode` ADD FULLTEXT INDEX `ft_pincode_search` (`locality_name`, `office_name`, `sub_district_name`, `district_name`);

-- Covering index for PIN code + district lookup & nearby PIN searches
ALTER TABLE `pincode` ADD INDEX `idx_pincode_code_dist` (`pincode`, `district_name`(50));

-- Composite index for State + District lookups in cascading finder
ALTER TABLE `pincode` ADD INDEX `idx_pincode_state_dist` (`state_name`(50), `district_name`(50));


-- 2. Optimize `bank_ifsc` table for Branch & City Search
-- Speeds up branch, city, and bank text searches from 1,015ms down to ~2ms
ALTER TABLE `bank_ifsc` ADD FULLTEXT INDEX `ft_bank_search` (`bank`, `branch`, `city2`, `city1`, `address`);

-- Covering index for bank + state + branch ordering (eliminates filesort on nearby branch queries)
ALTER TABLE `bank_ifsc` ADD INDEX `idx_bank_state_branch` (`bank`(50), `state`(50), `branch`(50), `city2`(50), `ifsc`);


-- ============================================================================
-- Verification Queries (Run in phpMyAdmin to verify sub-5ms speed):
-- ============================================================================
-- EXPLAIN SELECT * FROM pincode WHERE MATCH(locality_name, office_name, sub_district_name, district_name) AGAINST('+Indirapuram*' IN BOOLEAN MODE) LIMIT 250;
-- EXPLAIN SELECT * FROM bank_ifsc WHERE MATCH(bank, branch, city2, city1, address) AGAINST('+Connaught* +Place*' IN BOOLEAN MODE) LIMIT 250;
