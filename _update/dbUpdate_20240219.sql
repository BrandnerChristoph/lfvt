ALTER TABLE `teacher_wishlist`
	DROP FOREIGN KEY `FK_teacher`;
ALTER TABLE `teacher_wishlist`
	ADD CONSTRAINT `FK_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `lfv`.`teacher` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `class_subject`
	DROP FOREIGN KEY `FK-teacher-classsub`;
ALTER TABLE `class_subject`
	ADD CONSTRAINT `FK-teacher-classsub` FOREIGN KEY (`teacher`) REFERENCES `lfv`.`teacher` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
	
ALTER TABLE `school_class`
	ADD CONSTRAINT `FK_class_head-teacher` FOREIGN KEY (`class_head`) REFERENCES `teacher` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT;


ALTER TABLE `class_subject`
	ADD COLUMN `date_start` DATE NULL DEFAULT NULL AFTER `classroom`,
	ADD COLUMN `date_end` DATE NULL DEFAULT NULL AFTER `date_start`,
	ADD COLUMN `time_start` TIME NULL DEFAULT NULL AFTER `date_end`,
	ADD COLUMN `time_end` TIME NULL DEFAULT NULL AFTER `time_start`,
	ADD COLUMN `cycle` TINYINT NULL DEFAULT NULL AFTER `time_end`;
