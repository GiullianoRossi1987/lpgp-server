-- MySQL
-- @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>

create database if not exists LPGP_WEB;
use LPGP_WEB;

create table if no exists tb_users(
    cd_user integer primary key auto_increment not null unique,
    nm_user varchar(100) not null unique,
    vl_email varchar(100) not null,
    vl_password longtext not null,
    vl_key varchar(255) not null unique,
    checked integer not null default 0 check(checked in (0, 1)),
    dt_creation timestamp not null default current_timestamp()
);

create table if not exists tb_proprietaries(
    cd_proprietary integer primary key auto_increment not null unique,
    nm_proprietary varchar(100) not null unique,
    vl_email varchar(100) not null,
    vl_password longtext not null,
    checked integer not null default 0 check(checked in (0, 1)),
    dt_creation timestamp not null default current_timestamp()
);

create table if not exists tb_signatures(
    cd_signature integer primary key auto_increment not null unique,
    id_proprietary integer not null,
    vl_code integer not null,
    vl_password longtext not null,
    dt_creation timestamp not null default current_timestamp(),
    foreign key (id_proprietary) references tb_proprietaries(cd_proprietary)
);
