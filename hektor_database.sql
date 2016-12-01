use skill_test;


drop table if exists countdown;
create table countdown(
	id int(20) primary key auto_increment,
	year int(20),
	month int(20),
	day int(20),
	hours int(20),
	minutes int(20),
	seconds int(20),
	fontSize int(20),
	bgColor varchar(45),
	bgImage varchar(96),
	counterColor varchar(45),
	labelColor varchar(45)
);