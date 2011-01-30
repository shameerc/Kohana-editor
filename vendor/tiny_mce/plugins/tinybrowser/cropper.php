<?php
/**
 * Image croping addon for tinybrowser(tinymce editor)
 *
 * This can be used as an addon for tinybrowser module, which is a plugin
 * for tinymce editor. The cropping uses jquery and jcrop library.
 *
 * @author Nikhil Ben Kuruvilla
 * @date 28-10-2010
 * @link http://www.ispg.in
 * @package EcommerceGuruji
 * @category Editor
 * @copyright 2010 ISPG Technologies
 */

// include required files
require('config_tinybrowser.php');
require('image.class.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// change upload path here 
	$src = "../../../uploads/images/".$_REQUEST['img'];
	$objImage = new ImageManipulation($src);
	
	 if ( $objImage->imageok ) {
  		$objImage->setCrop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
 		$objImage->save();
 		header("location:tinybrowser.php");
 	} else {
  		echo 'Error!';
 	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Crop</title>
<!-- include jquery and jcrop -->
<script src="js/jquery.min.js"></script>
<script src="js/jquery.Jcrop.js"></script>
<!-- include site style and jcrop style sheets -->
<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="css/style_tinybrowser.css.php" />
<!-- jcrop script -->
<script language="Javascript">
$(function(){
	$('#cropbox').Jcrop({
	boxWidth: 600,
	aspectRatio: 0,
	onSelect: updateCoords
	});
});
	function updateCoords(c)
		{
			$('#x').val(c.x);
			$('#y').val(c.y);
			$('#w').val(c.w);
			$('#h').val(c.h);
		};

	function checkCoords()
		{
			if (parseInt($('#w').val())) return true;
			alert('Please select a crop region then press submit.');
			return false;
		};
</script>
</head>
<body bgcolor="#f9f9f7">
<?php  $img = $_REQUEST['img']; ?>
<p> <img src="<?php echo $tinybrowser['path']['image'].$_REQUEST['img'];?>"  id="cropbox" class="shrink-me" > </p>
<!-- This is the form that our event handler fills -->

<form action="cropper.php?img=<?php echo $img;?>" method="post" onSubmit="return checkCoords();">
  <input type="hidden" id="x" name="x" />
  <input type="hidden" id="y" name="y" />
  <input type="hidden" id="w" name="w" />
  <input type="hidden" id="h" name="h" />
  <p>
    <button type="submit">
    <a href='javascript:history.go(-1)' style="text-decoration:none;color:#000000">Go Back</a>
    </button>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <button type="submit">
    <a>Crop Image</a>
    </button>
  <p>
</form>
</body>
</html>
