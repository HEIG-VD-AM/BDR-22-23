-- 1.2 - Implémentation des tables sous PostgreSQL

CREATE SCHEMA COMPANY;

CREATE TABLE COMPANY.employee (
    fname varchar(15) NOT NULL,
    minit char(1),
    lname varchar(15) NOT NULL,
    ssn char(9),
    bdate date,
    address varchar(30),
    sex char(1),
    salary decimal(10,2),
    super_ssn char(9),
    dno integer NOT NULL,
    PRIMARY KEY(ssn)
);

CREATE TABLE COMPANY.department (
    dname varchar(15) NOT NULL,
    dnumber integer,
    mgr_ssn char(9) NOT NULL,
    mgr_start_date date NOT NULL,
    PRIMARY KEY(dnumber)
);

CREATE TABLE COMPANY.dept_location (
    dnumber integer,
    dlocation integer,
    PRIMARY KEY (dnumber, dlocation)
);

CREATE TABLE COMPANY.location (
    lnumber integer,
    lname varchar(15) NOT NULL,
    PRIMARY KEY(lnumber)
);

CREATE TABLE COMPANY.dependent (
    essn char(9),
    dependent_name varchar(15),
    sex char(1),
    bdate date,
    relationship varchar(8),
    PRIMARY KEY(essn, dependent_name)
)

CREATE TABLE company.project (
    pname VARCHAR(15) NOT NULL,
    pnumber INTEGER NOT NULL,
    plocation INTEGER,
    dnum INTEGER NOT NULL,
    PRIMARY KEY (pnumber)
);

CREATE TABLE company.works_on(
    essn CHAR(9) NOT NULL,
    pno INTEGER NOT NULL,
    hours DECIMAL(3,1) NOT NULL,
    PRIMARY KEY (essn, pno)
);

-- 1.3 - Insertion des données

-- 1)
INSERT INTO COMPANY.works_on VALUES('123456789', 3, 10.0);
INSERT INTO COMPANY.works_on VALUES('123456789', 5, 10.0);

-- On ne devrait pas pouvoir réaliser la seconde insertion. Il n'existe pas de projet avec le numéro 5 assigné.

-- 2)
DELETE FROM COMPANY.department WHERE dnumber=5;

-- Le résultat obbtenu n'est pas cohérent, on ne devrait pas pouvoir supprimer un département utiliser ou il faudrait le faire en cascade ou avec des contraintes.


-- 1.4

-- 1)

DELETE FROM company.department;
DELETE FROM company.dependent;
DELETE FROM company.dept_locations;
DELETE FROM company.employee;
DELETE FROM company.location;
DELETE FROM company.project;
DELETE FROM company.works_on;

-- 2)
ALTER TABLE company.project
    ADD CONSTRAINT proj_dnum_fk
        FOREIGN KEY (dnum) REFERENCES company.department;

ALTER TABLE company.project
    ADD CONSTRAINT proj_plocation_fk
        FOREIGN KEY (plocation) REFERENCES company.location;

ALTER TABLE company.works_on
    ADD CONSTRAINT works_pnumber_fk
        FOREIGN KEY (pno) REFERENCES company.project;

ALTER TABLE company.works_on
    ADD CONSTRAINT works_essn_fk
        FOREIGN KEY (essn) REFERENCES company.employee;

ALTER TABLE company.employee
    ADD CONSTRAINT emp_dnumber_fk
        FOREIGN KEY (dno) REFERENCES company.department;

ALTER TABLE company.employee
    ADD CONSTRAINT emp_ssn_fk
        FOREIGN KEY (super_ssn) REFERENCES company.employee;

ALTER TABLE company.dept_locations
    ADD CONSTRAINT deptloc_dnumber_fk
        FOREIGN KEY (dnumber) REFERENCES company.department;

ALTER TABLE company.dept_locations
    ADD CONSTRAINT deptloc_dlocation_fk
        FOREIGN KEY (dlocation) REFERENCES company.location;

ALTER TABLE company.dependent
    ADD CONSTRAINT dep_essn_fk
        FOREIGN KEY (essn) REFERENCES company.employee;

ALTER TABLE company.department
    ADD CONSTRAINT dept_mgr_ssn_fk
        FOREIGN KEY (mgr_ssn) REFERENCES company.employee

-- 3)
-- a) Non ce n'est pas possible, parce qu'il y a des dépendances entre les données que l'on essaye d'ajouter. Certaines insertions font références à des données dans d'autres tables, mais celles-ci n'existent pas.
-- b) Il faudrait pouvoir désactiver les contraintes posant problèmes le temps de l'ajout, puis les activer après (en s'assurant que les ajouts sont justes). Ou permettre d'ajouter les tuples et de vérifier lorsqu'ils sont tous ajoutés et non après chaque transaction.

-- 4)
-- Ici, on retrouve le cas des dépendances entre les données. Il est difficile d'ajouter des tuples dont les informations se retrouvent aussi dans d'autres tables. Dans le cas où la donnée n'existe pas, alors le programme s'arrête.
SET search_path = "company";

ALTER TABLE company.employee
    ALTER CONSTRAINT emp_dnumber_fk
    DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE company.department
    ALTER CONSTRAINT dept_mgr_ssn_fk
    DEFERRABLE INITIALLY IMMEDIATE;

BEGIN;

SET CONSTRAINTS dept_mgr_ssn_fk DEFERRED;
SET CONSTRAINTS emp_dnumber_fk DEFERRED;

INSERT INTO company.employee
VALUES ('Steve', 'B', 'Jobs', '555444333', '1965-01-08', '731 Fondren, Houston, TX', 'M', 30000.00, NULL, 10);
INSERT INTO company.department
VALUES ('IT', 10, '555444333', '1981-06-19');

COMMIT;

-- 5)
-- a)
UPDATE company.employee
SET dno=7
WHERE ssn = '999887777';
--[2022-10-15 15:18:41] [23503] ERROR: insert or update on table "employee" violates foreign key constraint "emp_dnumber_fk"
--[2022-10-15 15:18:41] Detail: Key (dno)=(7) is not present in table "department".

-- b)
DELETE
FROM company.employee
WHERE ssn = '999887777';
--[2022-10-15 15:19:30] [23503] ERROR: update or delete on table "employee" violates foreign key constraint "works_essn_fk" on table "works_on"
--[2022-10-15 15:19:30] Detail: Key (ssn)=(999887777) is still referenced from table "works_on".

-- c.1)
INSERT INTO company.works_on
VALUES ('123456789', 3, 10.0);
--Fonctionne

-- c.2)
INSERT INTO company.works_on
VALUES ('123456789', 5, 10.0);
--[2022-10-15 15:20:01] [23503] ERROR: insert or update on table "works_on" violates foreign key constraint "works_pnumber_fk"
--[2022-10-15 15:20:01] Detail: Key (pno)=(5) is not present in table "project".

-- d)
DELETE
FROM company.department
WHERE dnumber = 5;
--[2022-10-15 15:20:22] [23503] ERROR: update or delete on table "department" violates foreign key constraint "deptloc_dnumber_fk" on table "dept_locations"
--[2022-10-15 15:20:22] Detail: Key (dnumber)=(5) is still referenced from table "dept_locations".

-- 1.5

-- 1) 
-- On nous laisse pas le supprimer, car la contrainte ne définie pas de comportement dans ce cas.
DELETE
FROM company.employee
WHERE ssn = '123123123';

ALTER TABLE COMPANY.employee
    DROP CONSTRAINT emp_ssn_fk,
    ADD CONSTRAINT emp_ssn_fk
        FOREIGN KEY (super_ssn) REFERENCES COMPANY.employee ON DELETE SET NULL;


-- 2)
-- La mise à jour n'est pas en cascade, donc le logiciel nous bloque dans l'exécution.
UPDATE company.department
SET dnumber=12
WHERE dname = 'Research';

ALTER TABLE COMPANY.employee
    DROP CONSTRAINT emp_dnumber_fk,
    ADD CONSTRAINT emp_dnumber_fk
        FOREIGN KEY (dno) REFERENCES COMPANY.department ON UPDATE CASCADE;