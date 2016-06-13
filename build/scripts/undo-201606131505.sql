-- Fragment begins: 1 --

DROP TABLE `post`;

--//
DELETE FROM changelog
	                         WHERE change_number = 1
	                         AND delta_set = 'Main';
-- Fragment ends: 1 --
