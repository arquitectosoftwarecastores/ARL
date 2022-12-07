<script>
<?php
	if (isset($origem)){
		if ($origen == 'local'){
			echo "var scriptTag = '<' + 'script src=\"http://maps.googleapis.com/maps/api/js?key=' + myKey + '&sensor=false\" type=\"text/javascript\">'+'<'+'/script>';";
		}else{
			echo "var scriptTag = '<' + 'script src=\"http://maps.googleapis.com/maps/api/js?key=' + myKey2 + '&sensor=false\" type=\"text/javascript\">'+'<'+'/script>';";
			}
		}else{
			echo "var scriptTag = '<' + 'script src=\"http://maps.googleapis.com/maps/api/js?key=' + myKey + '&sensor=false\" type=\"text/javascript\">'+'<'+'/script>';";
		}
	echo "	document.write(scriptTag);";
?>
</script>