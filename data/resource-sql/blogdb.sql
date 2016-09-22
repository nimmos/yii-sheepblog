drop table if exists tbl_comment;
drop table if exists tbl_post;
drop table if exists tbl_user;

create table if not exists tbl_user (
	user_id int,
	name varchar(20) not null unique,
	email varchar(40) not null,
	pass varchar(20) not null,
	authkey varchar(50),
	token varchar(50),
	constraint user_id_pk primary key (user_id)
)engine=innodb;

create table if not exists tbl_post (
	post_id int,
	user_id int,
	time timestamp not null,
	title varchar(160),
	content text,
	constraint post_id_pk primary key (post_id),
	constraint post_user_fk foreign key (user_id)
		references tbl_user(user_id)
)engine=innodb;

create table if not exists tbl_comment (
	comment_id int,
	user_id int,
	post_id int,
	time timestamp not null,
	content text,
	constraint comm_id_pk primary key (comment_id),
	constraint comm_user_fk foreign key (user_id)
		references tbl_user(user_id),
	constraint comm_post_fk foreign key (post_id)
		references tbl_post(post_id)
)engine=innodb;
