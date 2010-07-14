<?php
include 'tada.inc.php';

// Returns a multidimensional array.
$user = "dantheman";

// This is what the select() is looking for: select($query, $params=false, $openyn=1, $closeyn=1, $usingdb=0)
//
// All values for select() and insert() entered via the $params array are escaped using mysql_real_escape_string to
// ensure no SQL injection

$output = select("SELECT id, blurb FROM tadatest WHERE id>? AND username='?'",array(3,$user),1,0);

// Generates query: "SELECT id, blurb FROM tadatest WHERE id>3 AND username='dantheman'",
// opens a new db connection, doesn't close the db connection when complete,
// and doesn't specify which db connection to use, so by default $conn[0] is used.

// RESULTS BREAKDOWN
// $output["numberofrows"] = 3 //because there were three rows returned by this SELECT query
// $output[0]["id"], $output[0]["blurb"], // the first rows returned id and blurb
// $output[1]["id"], $output[1]["blurb"], // the second rows returned id and blurb
// $output[2]["id"], $output[2]["blurb"], // the third rows returned id and blurb
//
// This allows for easy looping through query results, and your code makes sense.

for ($i=0;$i<$output["numberofrows"];$i++)
{
	echo "This is blurb $i: " . $output[$i]["blurb"] . "<br />";
}

// OUTPUT:
// This is blurb 0: I am the first blurb<br />
// This is blurb 1: I am the second blurb<br />
// This is blurb 2: I am the third blurb<br />

// This is what the insert() is looking for: insert($query, $params=false, $openyn=1, $closeyn=1, $usingdb=0)
$results = insert("INSERT INTO tadatest VALUES ('','?','?')",array($user,"This is a new blurb"),0,0);
// Generates query: "INSERT INTO tadatest VALUES ('','dantheman','This is a new blurb')",
// doesn't open a new db connection, doesn't close the db connection when complete,
// and doesn't specify which db connection to use, so by default $conn[0] is used.

// RESULTS BREAKDOWN
// This returned 'true' because the insert was a success.

if($results)
{
	echo "The insert worked!<br />";
}

// One last select statement, this time grabbing one specific row
$output = select("SELECT * FROM tadatest WHERE id=? LIMIT 1",array(367),0,1,0);
// Generates query: "SELECT * FROM tadatest WHERE id=367 LIMIT 1",
// doesn't open a new db connection, does close the db connection when complete,
// and specifies the use of db conn 0, also known as $conn[0].

// RESULTS BREAKDOWN
// $output["numberofrows"] = 1 //because there were one row returned by this SELECT query
// $output[0]["id"], $output[0]["username"], $output[0]["blurb"], // the first rows returned id, username and blurb

echo "The user is: ". $output[0]["username"] . ", and the blurb is: ". $output[0]["blurb"];

?>
