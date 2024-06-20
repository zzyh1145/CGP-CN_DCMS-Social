                CREATE TABLE `privat_chat` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `id_room` int(11) NOT NULL DEFAULT '0',
                  `id_user` int(11) NOT NULL DEFAULT '0',
                  `msg` varchar(1024) DEFAULT NULL,
                  `time` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

