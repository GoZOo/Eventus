<?php 

namespace Eventus\Includes\DAO;

/**
* Databse is a class use to manage the database
*
* @package  Includes//DAO
* @access   public
*/
class Database extends MasterDAO {
	/**
    * @var Database   $_instance  Var use to store an instance
    */
    private static $_instance;

	/**
    * Returns an instance of the object
    *
    * @return Databse
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Database();
        }
        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct();
    }

	/**
    * Create every tables
    *
    * @return void
    * @access public
    */
    function createTables() {
		dbDelta( "
			CREATE TABLE {$this->t1} (
				`club_id` int(11) NOT NULL AUTO_INCREMENT,
				`club_name` varchar(255) NOT NULL,
				`club_string` varchar(255) NOT NULL,
				`club_address` varchar(255) NOT NULL,
				`club_img` INT(255) DEFAULT NULL,
				`club_season` VARCHAR(25) NOT NULL,
				PRIMARY KEY (`club_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;

			CREATE TABLE {$this->t3} (
				`team_id` int(11) NOT NULL AUTO_INCREMENT,
				`team_name` varchar(255) NOT NULL,
				`team_urlOne` varchar(500) DEFAULT NULL,
				`team_urlTwo` varchar(500) DEFAULT NULL,
				`team_boy` tinyint(1) NOT NULL,
				`team_girl` tinyint(1) NOT NULL,
				`team_mixed` tinyint(1) NOT NULL,
				`team_position` int(2) NOT NULL,
				`team_points` int(2) NOT NULL,
				`team_time` int(3) NOT NULL,
				`team_img` varchar(255) DEFAULT NULL,
				`team_clubId` int(11) NOT NULL,
				PRIMARY KEY (`team_id`),
				FOREIGN KEY (`team_clubId`) 
				REFERENCES {$this->t1} (`club_id`) 
				ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;

			CREATE TABLE {$this->t2} (
				`match_id` int(11) NOT NULL AUTO_INCREMENT,
				`match_numMatch` int(1) DEFAULT NULL,
				`match_matchDay` int(2) DEFAULT NULL,
				`match_date` date DEFAULT NULL,
				`match_hourRdv` time DEFAULT NULL,
				`match_hourStart` time DEFAULT NULL,
				`match_localTeam` varchar(255) NOT NULL,
				`match_localTeamScore` int(3) DEFAULT NULL,
				`match_visitingTeam` varchar(255) NOT NULL,
				`match_visitingTeamScore` int(3) DEFAULT NULL,
				`match_ext` tinyint(1) DEFAULT NULL,
				`match_street` varchar(255) DEFAULT NULL,
				`match_city` varchar(255) DEFAULT NULL,
				`match_gym` varchar(255) DEFAULT NULL,
				`match_type` int(1) NOT NULL,
				`match_champ` int(1) DEFAULT NULL,				
				`match_idTeam` int(11) NOT NULL,
				`match_idMatchRef` int(11) DEFAULT NULL,
				PRIMARY KEY (`match_id`),
				FOREIGN KEY (`match_idTeam`) 
				REFERENCES {$this->t3} (`team_id`) 
				ON DELETE CASCADE,
				FOREIGN KEY (`match_idMatchRef`)
				REFERENCES {$this->t2} (`match_id`) 
				ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
		);
	}

	/**
    * Drop all the tables
    *
    * @return void
    * @access public
    */
	function deleteTables() {
		$sql_del_table = "DROP TABLE {$this->t2}, {$this->t3}, {$this->t1}";
		$this->wpdb->query($sql_del_table);
	}	

	/**
    * Reset database
    *
    * @return void
    * @access public
    */
	function resetTables(){
		$this->deleteTables();
		$this->createTables();
	}

	/**
    * Reset auto increment of a table
    *
    * @param string   Label of the table to reset increment
    * @return void
    * @access public
    */
	function resetAutoIncr($tab){
		switch ($tab) {
			case 'clubs':
				$this->wpdb->query("ALTER TABLE {$this->t1} AUTO_INCREMENT = 1");
				break;
			case 'matches':
				$this->wpdb->query("ALTER TABLE {$this->t2} AUTO_INCREMENT = 1");
				break;
			case 'teams':
				$this->wpdb->query("ALTER TABLE {$this->t3} AUTO_INCREMENT = 1");
				break;				
			default:
				break;
		}
		
	}
}
?>