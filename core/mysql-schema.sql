-- MySQL
-- @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>

create database if not exists LPGP_WEB;
use LPGP_WEB;

create table if not exists tb_users(
    cd_user integer primary key auto_increment not null unique,
    nm_user varchar(100) not null unique,
    vl_email varchar(100) not null,
    vl_password longtext not null,
    vl_img varchar(255) not null,
    vl_key varchar(255) not null unique,
    checked integer not null default 0 check(checked in (0, 1)),
    dt_creation timestamp not null default current_timestamp()
);

create table if not exists tb_proprietaries(
    cd_proprietary integer primary key auto_increment not null unique,
    nm_proprietary varchar(100) not null unique,
    vl_email varchar(100) not null,
    vl_password longtext not null,
    vl_img varchar(255) not null,
    vl_key varchar(255) not null unique,
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
    vl_valid integer not null default 1 check(vl_valid in (0, 1)),
    dt_reg timestamp not null default current_timestamp(),
    vl_code integer not null default 0 check(vl_code in (0, 1, 2, 3)),
    foreign key (id_user) references tb_users(cd_user),
    foreign key (id_signature) references tb_signatures(cd_signature)
);

create table tb_signatures_prop_check_h(
    cd_reg integer primary key auto_increment not null unique,
    id_prop integer not null,
    id_signature integer not null,
    vl_valid integer not null default 1 not null check(vl_valid in (0, 1)),
    dt_reg timestamp not null default current_timestamp(),
    vl_code integer not null default 0 check(vl_code in (0, 1, 2, 3)),
    foreign key (id_prop) references tb_proprietaries(cd_proprietary),
    foreign key (id_signature) references tb_signatures(cd_signature)
);

create table tb_clients(
    cd_client integer primary key auto_increment not null unique,
    nm_client varchar(200) not null unique,
    tk_client varchar(255) not null unique,
    id_proprietary integer not null,
    vl_root integer not null default 0 check(vl_root in (0, 1)),
    foreign key (id_proprietary) references tb_proprietaries(cd_proprietary)
        on delete CASCADE
        on update CASCADE
);

create table tb_access(
    cd_access integer primary key auto_increment not null unique,
    id_client integer not null,
    dt_access timestamp not null default current_timestamp(),
    vl_success integer not null default 1 check(vl_success in (0, 1)),
    foreign key (id_client) references tb_clients(cd_client)
    on delete cascade
    on update cascade
);

create user 'lpgp_internal'@'localhost' identified with mysql_native_password by "lpgpofficial";
grant all privileges on LPGP_WEB.* to lpgp_internal@localhost;

create user 'lpgp_auth'@'localhost' identified with mysql_native_password by "lpgp_ext_official";
grant select on LPGP_WEB.tb_clients to lpgp_auth@localhost;
grant insert select on LPGP_WEB.tb_access to lpgp_auth@localhost;

create user 'client_normal'@'localhost' identified with mysql_native_password;
grant select on LPGP_WEB.* to client_normal@localhost;

create user 'client_root'@'localhost' identified with mysql_native_password;
grant insert update delete select on LPGP_WEB.tb_proprietaries to client_root@localhost;
grant insert update delete select on LPGP_WEB.tb_signatures to client_root@localhsot;
grant insert update delete select on LPGP_WEB.tb_clients to client_root@localhost;
grant select insert on LPGP_WEB.tb_access to client_root@localhost;
