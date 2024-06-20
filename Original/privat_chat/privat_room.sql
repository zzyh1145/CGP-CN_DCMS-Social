                CREATE TABLE `privat_room` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `id_user` int(11) NOT NULL DEFAULT '0',
                  `name` varchar(56) DEFAULT NULL,
                  `password` varchar(16) DEFAULT NULL,
                  `id_avtor` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

