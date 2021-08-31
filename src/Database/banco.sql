drop database php_finance;

create database php_finance;

CREATE TABLE Usuarios (
    id serial NOT NULL UNIQUE,
    name varchar(255) NOT NULL UNIQUE,
    password varchar(255),
    CONSTRAINT PK_Usuarios PRIMARY KEY (ID)
);

select * from Usuarios;