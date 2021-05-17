<?php
//simple php code tp pull filenames dynamically
$scanned_directory = array_diff(scandir('.'), array('..', '.','index.php'));
?>
<html>
<head>
<!-- disable browser caching -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta http-equiv="refresh" content="1800"
<!-- css styling to center and scale images -->
<style>
body{
	background-color: black;
    color: white;
}
div {
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

<script type="text/javascript">

// Using PHP implode() function to parse data into js readable
var passedArray =
	<?php echo '["' . implode('", "', $scanned_directory) . '"]' ?>;

// checking that only one image is in directory.
if(passedArray[1] == null){
// Build Image
document.write('<img src="');
document.write(passedArray[0]);
document.write('">');
}else{
  document.write('Only one image per directory... check folders');  
}
</script>


</body>
</html>