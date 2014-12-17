<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>無題ドキュメント</title>
</head>

<body>
<?PHP

$file = "user/icon/IMG_0742.JPG";

		$strStart = strrpos($file, "/") + 1;
		$strEnd = strrpos($file, ".");
        $filename = \mb_substr($file, $strStart, ($strEnd - $strStart) ); //much faster & safer than pathinfo!

var_dump($strStart);
var_dump($strEnd);
var_dump($filename);

?>
</body>
</html>