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


create table tb_signatures_check_history(
    cd_reg integer primary key auto_increment not null unique,
    id_user integer not null,
    id_signature integer not null,
    vl_valid integer default 1 not null check(vl_valid in (0, 1)),
    dt_reg timestamp default current_timestamp() not null,
    vl_code integer default 0 not null check(vl_code in (0, 1, 2, 3)),
    foreign key (id_user) references tb_users(cd_user),
    foreign key (id_signature) references tb_signatures(cd_signature);
);

create table tb_signatures_prop_check_h(
    cd_reg integer primary key auto_increment not null unique,
    id_prop integer not null,
    id_signature integer not null,
    vl_valid integer default 1 not null check(vl_valid in (0, 1)),
    dt_reg timestamp default current_timestamp() not null,
    vl_code integer default 0 not null check(vl_code in (0, 1, 2, 3)),
    foreign key (id_prop) references tb_proprietareies(cd_proprietary),
    foreign key (id_signature) references tb_signatures(cd_signature);
);