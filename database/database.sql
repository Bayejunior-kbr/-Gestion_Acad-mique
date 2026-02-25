create database academique;

create table niveau(
    id_niveau int auto_increment primary key,
    nom_niveau enum('L1','L2','L3','M1','M2')
);

create table classe(
    id_classe int auto_increment primary key,
    nom_classe varchar(45) not null,
    id_niveau int,
    FOREIGN KEY (id_niveau) references niveau(id_niveau)
);

create table etudiant(
    id_etudiant int auto_increment primary key,
    matricule varchar(35),
    nom varchar(50),
    prenom varchar(50),
    id_classe int,
    FOREIGN KEY (id_classe) references classe(id_classe)
);

create table module(
    id_module int auto_increment primary key,
    code_module varchar(35),
    nom_module varchar(35)
);

CREATE TABLE classe_module (
    id_classe INT,
    id_module INT,
    coefficient INT NOT NULL,
    PRIMARY KEY (id_classe, id_module),
    FOREIGN KEY (id_classe) REFERENCES classe(id_classe),
    FOREIGN KEY (id_module) REFERENCES module(id_module)
);

create table evaluation(
    id_evaluation int auto_increment primary key,
    matricule_etudiant varchar(45),
    code_module varchar(35),
    type_evaluation enum('devoir','examen','TP'),
    note DECIMAL(5,2),
    id_etudiant int,
    id_module int,
     FOREIGN key (id_etudiant) references etudiant(id_etudiant),
    FOREIGN key (id_module) references module(id_module)
);