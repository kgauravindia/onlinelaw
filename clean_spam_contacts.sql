-- ====================================================================
-- Online Law - Purge Spam Inquiries Script
-- ====================================================================
-- This script removes all known bot inquiries, Cyrillic (Russian) spam,
-- gambling / prediction links, disposable scam domains, and Russian phone numbers.
--
-- Can be executed directly in phpMyAdmin, Hostinger Database Query runner, or CLI:
-- mysql -u u305984835_onlinelaw -p u305984835_onlinelaw < clean_spam_contacts.sql
-- ====================================================================

DELETE FROM `contact` WHERE 
    id IN (18, 19, 20, 21, 22, 23)
    OR `name` REGEXP '[а-яА-ЯёЁ]'
    OR `message` REGEXP '[а-яА-ЯёЁ]'
    OR `name` LIKE '%Руб%'
    OR `message` LIKE '%Руб%'
    OR `name` LIKE '%http://%'
    OR `name` LIKE '%https://%'
    OR `name` LIKE '%.buzz%'
    OR `message` LIKE '%.buzz%'
    OR `name` LIKE '%proton.me%'
    OR `message` LIKE '%proton.me%'
    OR `name` LIKE '%truetips%'
    OR `message` LIKE '%truetips%'
    OR `name` LIKE '%meestCex%'
    OR `email` LIKE '%.shw'
    OR `email` LIKE '%.dve'
    OR `email` LIKE '%.xjs'
    OR `email` LIKE '%.aow'
    OR `email` LIKE '%.bkf'
    OR `email` LIKE '%.dat'
    OR `email` LIKE '% %'
    OR (LENGTH(`mobile`) = 11 AND `mobile` LIKE '8%');

-- Optional: Optimize the contact table after cleanup
OPTIMIZE TABLE `contact`;

