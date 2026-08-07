

ALTER TABLE `students`
  ADD COLUMN `address`       TEXT         NULL AFTER `phone`,
  ADD COLUMN `arena`         VARCHAR(50)  NULL AFTER `course`,
  ADD COLUMN `team_members`  TEXT         NULL AFTER `arena`,
  ADD COLUMN `payment_id`    VARCHAR(150) NULL AFTER `team_members`,
  ADD COLUMN `team_id`       VARCHAR(30)  NULL AFTER `payment_id`;


ALTER TABLE `students`
  ADD UNIQUE KEY `uniq_email` (`email`),
  ADD UNIQUE KEY `uniq_team_id` (`team_id`);

