-- ============================================================================
-- Online Law - Two-Factor Authentication (OTP) Setup
-- Adds `otp_code` and `otp_expiry` columns to `op_user`
-- ============================================================================

ALTER TABLE `op_user` 
    ADD COLUMN IF NOT EXISTS `otp_code` VARCHAR(10) NULL AFTER `allow_otp`,
    ADD COLUMN IF NOT EXISTS `otp_expiry` DATETIME NULL AFTER `otp_code`;

-- Enable OTP for administrator accounts (ID 1 and 2)
UPDATE `op_user` SET `allow_otp` = 'YES' WHERE `id` IN (1, 2);
