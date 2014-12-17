<?PHP



try {
	$dbh = new \PDO('mysql:host=k1MobileDev;dbname=envitz', 'root', 'Passw0rd!', array( "PDO::ATTR_PERSISTENT" => true));
	//$dbh = new PDO('mysql:host=k1MobileDev;dbname=envitz', 'root', 'Passw0rd!');

	//$stmt = $dbh->prepare("show tables;");

	// call the stored procedure
	//$stmt->execute();

	$stmt = $dbh->query("SELECT * FROM geoLang WHERE geoLang_visible = 1");


	echo "<B>outputting...</B><BR>\n\r<BR>\n\r<BR>\n\r";
	$dataSrc = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		var_dump( $dataSrc );
	
	/*
	while ($rs = $stmt->fetch( \PDO::FETCH_ASSOC)) { //FETCH_OBJ
		var_dump($rs);
		//echo $rs->stdClass["Tables_in_envitz"]."<BR>";
	}
	*/
	echo "<BR><B>".date("r")."</B>";

} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}


?>