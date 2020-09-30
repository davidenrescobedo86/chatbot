CREATE DATABASE IF NOT EXISTS api_chat;
USE api_chat;

CREATE TABLE users(
id              int(255) auto_increment NOT NULL,
name            varchar(50) NOT NULL,
surname         varchar(100),
role            varchar(20),
email           varchar(255) NOT NULL,
password        varchar(255) NOT NULL,
description     text,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
remember_token  varchar(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE accounts(
id              int(255) auto_increment NOT NULL,
user_id         int(255) not null,
number_account  varchar(20) NOT NULL,
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
currency        varchar(3),
amount          float,
CONSTRAINT pk_accounts PRIMARY KEY(id),
CONSTRAINT fk_account_user FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;

CREATE TABLE transactions(
id                      int(255) auto_increment not null,
account_id              int(255) not null,
created_at              datetime DEFAULT NULL,
updated_at              datetime DEFAULT NULL,
amount                  float,
what                    varchar(7) NOT NULL
CONSTRAINT pk_transactions PRIMARY KEY(id),
CONSTRAINT fk_transaction_account FOREIGN KEY(account_id) REFERENCES accounts(id)
)ENGINE=InnoDb;