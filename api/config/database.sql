DROP DATABASE IF EXISTS lol_mood_db;

CREATE DATABASE lol_mood_db;
use lol_mood_db;


CREATE TABLE account (
  id int AUTO_INCREMENT,
  puuid varchar(96) UNIQUE NOT NULL,
  name varchar(64) NOT NULL DEFAULT "",
  level int(4) NOT NULL DEFAULT 0,
  profile_icon_id int(4) NOT NULL DEFAULT 0,
  rank VARCHAR(12) NOT NULL DEFAULT "",
  tier VARCHAR(4) NOT NULL DEFAULT "",
  lp int(4) NOT NULL DEFAULT 0,
  games int(5) NOT NULL DEFAULT 0,
  wins int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
);

CREATE TABLE game (
  id int AUTO_INCREMENT,
  identifier varchar(32) UNIQUE NOT NULL,
  patch VARCHAR(5),
  duration int(4),

  PRIMARY KEY (id)
);

CREATE TABLE champ (
  id int AUTO_INCREMENT,
  name varchar(64) UNIQUE NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE asset (
  id int AUTO_INCREMENT,
  identifier int(4) UNIQUE NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE positioning (
  id int AUTO_INCREMENT,
  lane varchar(64) UNIQUE NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE game_info (
  game_id int NOT NULL,
  account_id int NOT NULL,
  champ_id int NOT NULL,
  positioning_id int NOT NULL,


  win boolean NOT NULL,
  kills int(2),
  deaths int(2),
  assists int(2),
  multikills tinyint(1),
  skills_order VARCHAR(18),
  evolves_order VARCHAR(4),


  primaryStyle_id int NOT NULL,
  subStyle_id int NOT NULL,
  perk_id int NOT NULL,

  rune0_id int NOT NULL,
  rune1_id int NOT NULL,
  rune2_id int NOT NULL,
  rune3_id int NOT NULL,
  rune4_id int NOT NULL,

  statsMod0_id int NOT NULL,
  statsMod1_id int NOT NULL,
  statsMod2_id int NOT NULL,

  summoner1_id int NOT NULL,
  summoner2_id int NOT NULL,


  item0_id int,
  item1_id int,
  item2_id int,
  item3_id int,
  item4_id int,
  item5_id int,

  start_item0_id int,
  start_item1_id int,
  start_item2_id int,
  start_item3_id int,
  start_item4_id int,
  start_item5_id int,
  start_item6_id int,

  completed_item0_id int,
  completed_item1_id int,
  completed_item2_id int,
  completed_item3_id int,
  completed_item4_id int,
  completed_item5_id int,


  PRIMARY KEY (game_id, account_id),

  CONSTRAINT fk_game_id FOREIGN KEY(game_id) REFERENCES game(id),
  CONSTRAINT fk_account_id FOREIGN KEY(account_id) REFERENCES account(id),
  CONSTRAINT fk_champ_id FOREIGN KEY(champ_id) REFERENCES champ(id),
  CONSTRAINT fk_positioning_id FOREIGN KEY(positioning_id) REFERENCES positioning(id),

  CONSTRAINT fk_primaryStyle_id FOREIGN KEY(primaryStyle_id) REFERENCES asset(id),
  CONSTRAINT fk_subStyle_id FOREIGN KEY(subStyle_id) REFERENCES asset(id),
  CONSTRAINT fk_perk_id FOREIGN KEY(perk_id) REFERENCES asset(id),

  CONSTRAINT fk_rune0_id FOREIGN KEY(rune0_id) REFERENCES asset(id),
  CONSTRAINT fk_rune1_id FOREIGN KEY(rune1_id) REFERENCES asset(id),
  CONSTRAINT fk_rune2_id FOREIGN KEY(rune2_id) REFERENCES asset(id),
  CONSTRAINT fk_rune3_id FOREIGN KEY(rune3_id) REFERENCES asset(id),
  CONSTRAINT fk_rune4_id FOREIGN KEY(rune4_id) REFERENCES asset(id),

  CONSTRAINT fk_statsMod0_id FOREIGN KEY(statsMod0_id) REFERENCES asset(id),
  CONSTRAINT fk_statsMod1_id FOREIGN KEY(statsMod1_id) REFERENCES asset(id),
  CONSTRAINT fk_statsMod2_id FOREIGN KEY(statsMod2_id) REFERENCES asset(id),

  CONSTRAINT fk_summoner1_id FOREIGN KEY(summoner1_id) REFERENCES asset(id),
  CONSTRAINT fk_summoner2_id FOREIGN KEY(summoner2_id) REFERENCES asset(id),

  CONSTRAINT fk_item0_id FOREIGN KEY(item0_id) REFERENCES asset(id),
  CONSTRAINT fk_item1_id FOREIGN KEY(item1_id) REFERENCES asset(id),
  CONSTRAINT fk_item2_id FOREIGN KEY(item2_id) REFERENCES asset(id),
  CONSTRAINT fk_item3_id FOREIGN KEY(item3_id) REFERENCES asset(id),
  CONSTRAINT fk_item4_id FOREIGN KEY(item4_id) REFERENCES asset(id),
  CONSTRAINT fk_item5_id FOREIGN KEY(item5_id) REFERENCES asset(id),

  CONSTRAINT fk_start_item0_id FOREIGN KEY (start_item0_id) REFERENCES asset(id),
  CONSTRAINT fk_start_item1_id FOREIGN KEY (start_item1_id) REFERENCES asset(id),
  CONSTRAINT fk_start_item2_id FOREIGN KEY (start_item2_id) REFERENCES asset(id),
  CONSTRAINT fk_start_item3_id FOREIGN KEY (start_item3_id) REFERENCES asset(id),
  CONSTRAINT fk_start_item4_id FOREIGN KEY (start_item4_id) REFERENCES asset(id),
  CONSTRAINT fk_start_item5_id FOREIGN KEY (start_item5_id) REFERENCES asset(id),

  CONSTRAINT fk_completed_item0_id FOREIGN KEY (completed_item0_id) REFERENCES asset(id),
  CONSTRAINT fk_completed_item1_id FOREIGN KEY (completed_item1_id) REFERENCES asset(id),
  CONSTRAINT fk_completed_item2_id FOREIGN KEY (completed_item2_id) REFERENCES asset(id),
  CONSTRAINT fk_completed_item3_id FOREIGN KEY (completed_item3_id) REFERENCES asset(id),
  CONSTRAINT fk_completed_item4_id FOREIGN KEY (completed_item4_id) REFERENCES asset(id),
  CONSTRAINT fk_completed_item5_id FOREIGN KEY (completed_item5_id) REFERENCES asset(id)
);