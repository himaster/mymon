-- Fragment begins: 1 --
INSERT INTO changelog
                                (change_number, delta_set, start_dt, applied_by, description) VALUES (1, 'Main', NOW(), 'dbdeploy', '1-create_initial_schema.sql ');
--//

CREATE TABLE `post` (
    `title` VARCHAR(255),
    `time_created` DATETIME,
    `content` MEDIUMTEXT
);


UPDATE changelog
	                         SET complete_dt = NOW()
	                         WHERE change_number = 1
	                         AND delta_set = 'Main';
-- Fragment ends: 1 --
