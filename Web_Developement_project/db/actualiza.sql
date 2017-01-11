
----
---- Quitamos cosas que no usaremos
----

ALTER TABLE customers
    DROP creditcardexpiration,
    DROP creditcardtype,
    DROP address2,
    DROP state,
    DROP phone,
    DROP age,
    DROP region,
    ALTER zip SET NOT NULL,
    ALTER email SET NOT NULL,
    ADD lastname2 character varying(50);

ALTER TABLE optionbet
    DROP optiondesc;

----
---- Añadimos claves primarias/foraneas no existentes
----

-- clave foranea en bets para winneropt
ALTER TABLE bets ADD CONSTRAINT pk_winneropt FOREIGN KEY (winneropt)
      REFERENCES options (optionid) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

-- clave primaria en clientbets, 
ALTER TABLE clientbets ADD COLUMN clientbetid serial not null;
ALTER TABLE clientbets ADD PRIMARY KEY (clientbetid);

-- clave foranea en clientorders para customer
ALTER TABLE clientorders ADD CONSTRAINT pk_customer FOREIGN KEY (customerid)
      REFERENCES customers (customerid) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE clientorders ADD PRIMARY KEY (orderid);

-- clave foranea en clientbets
ALTER TABLE clientbets ADD CONSTRAINT pk_clientorder FOREIGN KEY (orderid)
      REFERENCES clientorders (orderid) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

-- clave primaria en optionbet
ALTER TABLE optionbet ADD COLUMN optionbetid serial not null;
ALTER TABLE optionbet ADD PRIMARY KEY (optionbetid);

--campo email unico en customerid
ALTER TABLE customers ADD CONSTRAINT email UNIQUE (email);

----
---- Modificamos la db para tener una tabla category y evitar redundancias
----

--Actualizar datos ya existentes
UPDATE bets    SET category  = 1 WHERE category  = 'FutbolFemenino';
UPDATE bets    SET category  = 2 WHERE category  = 'Petanca';
UPDATE bets    SET category  = 3 WHERE category  = 'Regional';
UPDATE bets    SET category  = 4 WHERE category  = 'ACB';
UPDATE bets    SET category  = 5 WHERE category  = 'Asobal';

--Crear tabla nueva
CREATE TABLE category (
  categoryid serial NOT NULL,
  categoryname character varying(30) NOT NULL,
  CONSTRAINT category_pkey PRIMARY KEY (categoryid)
);
--Poblar tabla
INSERT INTO category (categoryid, categoryname) VALUES
    (1, 'FutbolFemenino'),
    (2, 'Petanca'),
    (3, 'Regional'),
    (4, 'ACB'),
    (5, 'Asobal'),
    (6, 'k1');

--Cambiar tipo de dato de category a integer
ALTER TABLE options DROP COLUMN categoria;
ALTER TABLE bets ALTER COLUMN category TYPE integer USING (trim(category)::integer);

--Añadir claves primarias en las modificadas
ALTER TABLE bets ADD CONSTRAINT pk_category FOREIGN KEY (category)
      REFERENCES category (categoryid) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;
----
---- Modificamos la db para tener una tabla category y evitar redundancias
----
ALTER TABLE clientorders ADD COLUMN totaloutcome numeric;

----
---- Actualizamos valores serial para poder insertar bien
----
SELECT setval('bets_betid_seq',             max(betid))       from bets;
SELECT setval('category_categoryid_seq',    max(categoryid))  from category;
SELECT setval('clientbets_clientbetid_seq', max(clientbetid)) from clientbets;
SELECT setval('clientorders_id_seq',        max(orderid))     from clientorders;
SELECT setval('customers_customerid_seq',   max(customerid))  from customers;
SELECT setval('optionbet_optionbetid_seq',  max(optionbetid)) from optionbet;
SELECT setval('options_optionid_seq',       max(optionid))    from options;

----
---- Hasheamos las passwords, al final nos resignaremos a usar md5 por falta de tiempo
----
update customers set password=md5(password);

----
---- Convertimos el saldo en un valor numerico
----
ALTER TABLE customers ALTER COLUMN credit TYPE numeric;

----
---- Modificamos cambios fundamentales en clientbets para saber si una apuesta está activa o no
----
ALTER TABLE clientbets ADD COLUMN confirmed boolean DEFAULT true;
ALTER TABLE clientbets alter confirmed SET DEFAULT false;