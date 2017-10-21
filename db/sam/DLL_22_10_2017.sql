create table s_pars_category_5
(
  id int auto_increment
    primary key,
  name varchar(255) default '' not null comment 'наименование',
  catId int null comment 'ID категории в 5элем',
  rootId int default '0' null comment 'Главная категория товара в 5элем',
  act int default '0' not null comment 'Актуальность данных (1- актуальл, 0 - не актуально)',
  constraint s_pars_category_5_catId_uindex
  unique (catId)
)
  comment 'Инфо о парсинге'
;

create table s_pars_cena_5
(
  id int auto_increment
    primary key,
  product_id int not null,
  cena decimal(10,2) default '0.00' null,
  oplata_id int not null,
  main_id int not null
)
;

create index product_id
  on s_pars_cena_5 (product_id)
;

create index s_pars_cena_5_s_pars_oplata_5_id_fk
  on s_pars_cena_5 (oplata_id)
;

create index s_pars_cena_5_s_pars_main_5_id_fk
  on s_pars_cena_5 (main_id)
;

create table s_pars_main_5
(
  id int auto_increment comment 'Ид парсинга'
    primary key,
  date datetime not null comment 'Дата парсинга',
  act int default '0' not null comment 'Актуальность данных (1- актуальл, 0 - не актуально)'
)
  comment 'Инфо о парсинге'
;

alter table s_pars_cena_5
  add constraint s_pars_cena_5_s_pars_main_5_id_fk
foreign key (main_id) references user1111058_sam.s_pars_main_5 (id)
;

create table s_pars_oplata_5
(
  id int auto_increment
    primary key,
  creditId int default '0' null,
  name varchar(255) null,
  constraint s_pars_oplata_5_creditId_uindex
  unique (creditId)
)
;

alter table s_pars_cena_5
  add constraint s_pars_cena_5_s_pars_oplata_5_id_fk
foreign key (oplata_id) references user1111058_sam.s_pars_oplata_5 (id)
;

create table s_pars_product_5
(
  id int auto_increment comment 'ИД'
    primary key,
  category_id int null comment 'Ссылка на категоию',
  prodId int default '0' not null,
  name varchar(255) null comment 'Наименование продукта',
  cod int null comment 'Код продукта',
  constraint s_pars_product_5_prodId_uindex
  unique (prodId)
)
  comment 'Описание продукта'
;

create index cat_id
  on s_pars_product_5 (category_id)
;

create index cod
  on s_pars_product_5 (cod)
;

alter table s_pars_cena_5
  add constraint s_pars_cena_5_s_pars_product_5_id_fk
foreign key (product_id) references user1111058_sam.s_pars_product_5 (id)
;



/*
-- ЗАПРОС НА ВЫБОРКУ ВСЕХ ДАННЫХ;

select c.name, p.name, p.cod, o.name, cen.cena
from s_pars_cena_5 cen, s_pars_category_5 c, s_pars_product_5 p, s_pars_oplata_5 o
where  cen.product_id=p.id and c.id=p.category_id and cen.oplata_id=o.id and main_id=(select max(id) from s_pars_main_5)
order by 1,2;

*/