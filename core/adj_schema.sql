-- SQLite3
-- @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>

CREATE TABLE tb_sbj_report(
	cd_subject INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
	nm_subject TEXT NOT NULL,
	vl_visibility_scope INTEGER DEFAULT 0 NOT NULL CHECK (vl_visibility_scope IN (0, 1))
);

CREATE TABLE tb_report(
	cd_report INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
	ds_report LONGTEXT NOT NULL,
	id_subject INTEGER NOT NULL,
	id_e_user INTEGER NOT NULL CHECK(id_e_user > 0),
	vl_tp_user INTEGER DEFAULT 0 NOT NULL CHECK(vl_tp_user IN (0, 1)),
	dt_report DATETIME DEFAULT TIMESTAMP_CURRENT NOT NULL,
	FOREIGN KEY (id_subject) REFERENCES tb_sbj_report(cd_subject)
);

CREATE TABLE tb_feedbacks(
	cd_feedback INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
	ds_feedback LONGTEXT NOT NULL,
	tp_feedback INTEGER DEFAULT 0 NOT NULL CHECK(tp_feedback IN (0, 1, 2)),
	id_e_user INTEGER NOT NULL CHECK(id_e_user > 0),
	vl_tp_user INTEGER DEFAULT 0 NOT NULL CHECK (vl_tp_user IN (0, 1)),
	dt_feedback DATETIME DEFAULT TIMESTAMP_CURRENT NOT NULL
);

CREATE VIEW IF NOT EXISTS prop_feedbacks AS
	SELECT * FROM tb_feedbacks WHERE vl_tp_user = 1;

CREATE VIEW IF NOT EXISTS usr_feedbacks AS
	SELECT * FROM tb_feedbacks WHERE vl_tp_user = 0;

CREATE VIEW IF NOT EXISTS prop_reports AS
	SELECT rp.*,
	sbj.nm_subject
	FROM tb_report AS rp
	INNER JOIN tb_sbj_report AS sbj
	ON sbj.cd_subject = rp.id_subject
	WHERE vl_tp_user = 1;

CREATE VIEW IF NOT EXISTS usr_reports AS
	SELECT rp.*,
	sbj.nm_subject
	FROM tb_report AS rp
	INNER JOIN tb_sbj_report AS sbj
	ON sbj.cd_subject = rp.id_subject
	WHERE vl_tp_user = 0;

CREATE VIEW IF NOT EXISTS openScopeSbjs AS
	SELECT * FROM tb_sbj_report WHERE vl_visibility_scope = 0;

CREATE VIEW IF NOT EXISTS restrictScopeSbjs AS
	SELECT * FROM tb_sbj_report WHERE vl_visibility_scope = 1;

INSERT INTO tb_sbj_report (nm_subject, vl_visibility_scope)
VALUES ("Other", 0),
("WebSite Design", 0),
("My Account", 0),
("Other account", 0),
("Signatures Checking", 0),
("Clients checking", 0),
("Signatures Managing", 1),
("Clients Managing", 1),
("Access Plot view", 1);
