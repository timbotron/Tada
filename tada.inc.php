<?php	
//
// Project: Tada
// Version: 0.90
// Synopsis: A simple include to handle all communication between PHP and MySQL.
//

//Set your database connection(s) here.  Add another connection if you will be connecting to different databases
$conn[0] = new Connection("db.lab.citracode.com","citralabuser","pa55w0rd","citralabdb");
//$conn[1] = new Connection("mysql.example.com","username","password","databasename");
//$conn[2] = ...

//The Connection class, for easily holding the data for one db connection
class Connection
{
	public $location = '';
	public $user = '';
	public $pass ='';
	public $db = '';
	public $link ='';  //this is the link that is used for the db connection

	function __construct($newloc,$newuser,$newpass,$newdb) 
	{
      $this->location = $newloc;
      $this->user = $newuser;
      $this->pass = $newpass;
      $this->db = $newdb;      
    }
}

//This is called by either select() or insert(), if a new connection is requested
function ConnectToDB($conn,$connid=0)
{
	$conn[$connid]->link = mysql_connect($conn[$connid]->location, $conn[$connid]->user, $conn[$connid]->pass)
	   or die('Could not connect: ' . mysql_error());
	   
	mysql_select_db($conn[$connid]->db) or die('Could not select database');
	
}

//This is called by either select() or insert(), if closing the connection is requested
function CloseDB($conn,$connid=0)
{
	// Closing connection
	mysql_close($conn[$connid]->link);	
	
}
//Used to run insert queries, or any query that isn't returning row(s) of info (like an UPDATE), returns a true if successful
function insert($query, $params=false, $openyn=1, $closeyn=1, $usingdb=0)
{
	//global $link;
	global $conn;

	if($openyn == 1) ConnectToDB($conn,$usingdb);	
	
	if ($params) {
        foreach ($params as &$v) { $v = mysql_real_escape_string($v, $conn[$usingdb]->link); }    // Escaping parameters
        $sql_query = vsprintf(str_replace("?","%s",$query), $params);   
        $r = mysql_query($sql_query) or die('insert failed: ' . mysql_error());    // Perfoming escaped query
    } else {
        $r = mysql_query($query) or die('insert failed: ' . mysql_error());    // If no params...
    }
	
	if($closeyn == 1) CloseDB();
	
	return true;	
}
//Used to run select queries.
//Ex. $results = select("SELECT * FROM tablename WHERE id=? AND lastname='?'",array(6,"Smith"),1,1,0);
function select($query, $params=false, $openyn=1, $closeyn=1, $usingdb=0)
{
	//global $link;
	global $conn;

	if($openyn == 1)	ConnectToDB($conn,$usingdb);

	if ($params) {
        foreach ($params as &$v) { $v = mysql_real_escape_string($v, $conn[$usingdb]->link); }    // Escaping parameters
        $sql_query = vsprintf(str_replace("?","%s",$query), $params);   
        $r = mysql_query($sql_query) or die('select failed: ' . mysql_error());    // Perfoming escaped query
    } else {
        $r = mysql_query($query) or die('select failed: ' . mysql_error());    // If no params...
    }
		
	//BEGIN ERROR-CHECKING THE MySQL
	if(!$r) 
	{
		$err=mysql_error();
		print $err;
		return 0;
	}
	
	if(mysql_num_rows($r)==FALSE)
	{
		// Free resultset
		mysql_free_result($r);
		
		if($conn[$usingdb]->link)
		{
			// Closing connection
			mysql_close($conn[$usingdb]->link);
		}
		return 0;
	}
	
	//END ERROR-CHECKING THE MySQL
	
	//to be sure it's at the 0th row
	mysql_data_seek($r, 0);	
	
	$counter = 0;
	while ($row = mysql_fetch_assoc($r)) 
	{				
		$TemporalArray[$counter] = $row;	//puts the row array onto the other, making a multidimensional array!
		$counter++;
	}
	
	$TemporalArray["numberofrows"] = $counter;
	
	// Free resultset
	mysql_free_result($r);	

	if($closeyn == 1) CloseDB($conn,$usingdb);
	
	return $TemporalArray;

}
	
?>
