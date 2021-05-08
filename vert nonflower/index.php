<?php
//simple php code tp pull filenames dynamically
if ($handle = opendir('.')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "index.php") {
            $pic = $entry;
        }
    }
    closedir($handle);
}
?>
<html>
<head>
<style>
body{
	background-color: black;
}
div {
	vertical-align: middle
	width: 100%;
}

img{
	display: block;
	width: 100%;
	height: auto;
}
</style>
</head>
<body>

<div>
<img src="<?php echo($pic);?>">
</div>

</body>
</html>