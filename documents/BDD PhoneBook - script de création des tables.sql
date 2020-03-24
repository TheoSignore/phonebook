/*Phonebook MySQL database cretion (and insertion script) script*/
create table Site(idGroup varchar(50) not null primary key, idCountry char(2), nameSite varchar(30),cn MEDIUMTEXT);

create table Country(idCountry char(2) not null primary key, nameCountry varchar(50), idRegion char(1), dn MEDIUMTEXT);

create table Region(idRegion char not null primary key, nameRegion varchar(10));

create table Dep(idDep int not null primary key, typeDep varchar(30));

create table Departments(idDep int not null, idGroup varchar(50));
/* both attributes are the primary key*/
alter table Departments add constraint pk_Departments primary key (idDep,idGroup);

create table Fax(idFax int not null AUTO_INCREMENT primary key, nameFax varchar(20) not null, numfax varchar(20), idDep int, idGroup varchar(50));

create table Users(idUser varchar(30) not null primary key, nameUser varchar(20), firstNameUser varchar(20), title varchar(50), idDep int,eMail varchar(50), internCisco varchar(20), phoneIntern varchar(20), phoneExtern varchar(20), phoneMobile varchar(20), idGroup varchar(50));

create table Password(idwpa int not null primary key AUTO_INCREMENT, wpaKey MEDIUMTEXT); 

/* foreign keys*/
alter table Site add constraint fk_idCountry_Site foreign key (idCountry) references Country(idCountry);

alter table Users add constraint fk_idSite_User foreign key (idGroup) references Site(idGroup);

alter table Country add constraint fk_idRegion_Country foreign key (idRegion) references Region(idRegion);

alter table Departments add constraint fk_idGroup_Departments foreign key (idGroup) references Site(idGroup);

alter table Departments add constraint fk_idDep_Departments foreign key (idDep) references Dep(idDep);

alter table Users add constraint fk_idDep_Users foreign key (idDep) references Dep(idDep);

alter table Fax add constraint fk_idGroup_Fax foreign key (idGroup) references Site(idGroup);

alter table Fax add constraint fk_idDep_Fax foreign key (idDep) references Dep(idDep);

/*Insertions*/

insert into Dep (idDep,typeDep) values(0,"NO DEPARTMENT");

insert into Region (idRegion,nameRegion) values ("N","North");
insert into Region (idRegion,nameRegion) values ("S","South");
insert into Region (idRegion,nameRegion) values ("E","East");
insert into Region (idRegion,nameRegion) values ("W","West");





insert into Country (idCountry,nameCountry,idRegion,dn) values ("BE","Belgium","N","OU=BE,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("CH","Switzerland","N","OU=CH,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("CZ","Czech Republic","E","OU=CZ,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("DE","Germany","N","OU=DE,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("DK","Denmark","N","OU=DK,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("ES","Spain","S","OU=ES,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("FR","France","S","OU=FR,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("GB","United Kingdom","W","OU=GB,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("IE","Ireland","W","OU=IE,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("NL","Netherlands","N","OU=NL,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("PL","Poland","E","OU=PL,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Country (idCountry,nameCountry,idRegion,dn) values ("SK","Slovakia","E","OU=SK,OU=_Organisation,DC=moviantogroup,DC=com");

insert into Site (idGroup,nameSite,cn) values ("NO GROUP","NO SITE","NONE");
insert into Site (idGroup,nameSite, idCountry,cn) values ("FRONE-AP-Phonebook","Gonesse","FR","CN=FRONE-AP-Phonebook,OU=Application-Groups (L),OU=Groups,OU=ONE,OU=FR,OU=_Organisation,DC=moviantogroup,DC=com");
insert into Site (idGroup,nameSite, idCountry,cn) values ("FRTYR-AP-Phonebook","Saint-Cyr-En-Val","FR","CN=FRTYR-AP-Phonebook,OU=Application-Groups (L),OU=Groups,OU=TYR,OU=FR,OU=_Organisation,DC=moviantogroup,DC=com");




