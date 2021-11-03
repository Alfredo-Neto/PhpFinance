drop database php_finance;

create database php_finance;

CREATE TABLE Usuarios (
    id serial NOT NULL UNIQUE,
    name varchar(255) NOT NULL UNIQUE,
    password varchar(255),
    CONSTRAINT PK_Usuarios PRIMARY KEY (ID)
);

CREATE TABLE Tokens (
    id serial NOT NULL UNIQUE,
    token varchar(255) NOT NULL UNIQUE,
    dataHora timestamp,
    status smallint,
    usuarioId bigint NOT NULL,
    CONSTRAINT PK_Tokens PRIMARY KEY(ID),
    CONSTRAINT FK_Usuarios FOREIGN KEY (usuarioId) REFERENCES Usuarios(id)
);

select * from Usuarios;