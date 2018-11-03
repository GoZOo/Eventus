<?php 

class Database extends MasterDAO {
    private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Database();
        }
        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct();
    }

    function createTables() {
		dbDelta( "
			CREATE TABLE {$this->t1} (
				id int(11) NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				string varchar(255) NOT NULL,
				boy tinyint(1) NOT NULL,
				girl tinyint(1) NOT NULL,
				mixed tinyint(1) NOT NULL,
				adress varchar(255) NOT NULL,
				PRIMARY KEY (id)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
			CREATE TABLE {$this->t3} (
				id int(11) NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				url varchar(500),
				boy tinyint(1) NOT NULL,
				girl tinyint(1) NOT NULL,
				mixed tinyint(1) NOT NULL,
				position INT(2) NOT NULL, 
				points INT(2) NOT NULL,
				time INT(3) NOT NULL,
				img VARCHAR(255), 
				clubId int(11) NOT NULL,
				PRIMARY KEY (id),
				FOREIGN KEY (clubId) 
				REFERENCES {$this->t1} (id) 
				ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
			CREATE TABLE {$this->t2} (
				id int(11) NOT NULL AUTO_INCREMENT,
				numMatch int(1),
				matchDay int(2),
				date date,
				hourRdv time,
				hourStart time,
				localTeam varchar(255) NOT NULL,
				localTeamScore int(3),
				visitingTeam varchar(255) NOT NULL,
				visitingTeamScore int(3),
				ext tinyint(1),
				street varchar(255),
				city varchar(255),
				gym varchar(255),
				type INT(1) NOT NULl,
				idTeam int(11) NOT NULL,
				idMatchRef int(11),
				PRIMARY KEY (id),
				FOREIGN KEY (idTeam) 
				REFERENCES {$this->t3} (id) 
				ON DELETE CASCADE,
				FOREIGN KEY (idMatchRef) 
				REFERENCES {$this->t2} (id) 
				ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
		);
	}

	function deleteTables() {
		$sql_del_table = "DROP TABLE  {$this->t2}, {$this->t3}, {$this->t1}";
		$this->wpdb->query($sql_del_table);
	}	

	function resetTables(){
		$this->deleteTables();
		$this->createTables();
	}
}
?>