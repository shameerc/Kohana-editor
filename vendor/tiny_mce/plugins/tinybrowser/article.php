<?php
require_once('../../../../dbsetup/eg-config.php');
$dbConnection = mysql_connect (DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME, $dbConnection); 

if ($_REQUEST['sortby'] == 'modified') {
	$sortby = 'link_updated';
} elseif ($_REQUEST['sortby'] == 'name') {
	$sortby = 'link_name';
}

$searchString = $_REQUEST['find'];

if ($_REQUEST['sorttype'] == 'asc') {
	$qry = mysql_query("SELECT * FROM wp_links WHERE link_name LIKE '%$searchString%' ORDER BY $sortby ASC");
} elseif ($_REQUEST['sorttype'] == 'desc') {
	$qry = mysql_query("SELECT * FROM wp_links WHERE link_name LIKE '%$searchString%' ORDER BY $sortby DESC");

} else {
	$qry = mysql_query("SELECT * FROM wp_links WHERE link_name LIKE '%$searchString%'");
}

require_once("config_tinybrowser.php");
// Set language
if(isset($tinybrowser['language']) && file_exists('langs/'.$tinybrowser['language'].'.php'))
	{
	require_once('langs/'.$tinybrowser['language'].'.php'); 
	}
else
	{
	require_once('langs/en.php'); // Falls back to English
	}
require_once("fns_tinybrowser.php");

// Check session, if it exists
if(session_id() != '')
	{
	if(!isset($_SESSION[$tinybrowser['sessioncheck']]))
		{
		echo TB_DENIED;
		exit;
		}
	}


// Assign get variables
$typenow = (isset($_GET['type']) ? $_GET['type'] : 'image');
$standalone = ((isset($_GET['feid']) && $_GET['feid']!='') ? true : false);

if($standalone)
	{
	$passfeid = '&feid='.$_GET['feid'];	
	$rowhlightinit =  ' onload="rowHighlight();"';
	}
else
	{
	$passfeid = '';
	$rowhlightinit =  '';	
	}

// Assign view, thumbnail and link paths
$browsepath = $tinybrowser['path'][$typenow];
$linkpath = $tinybrowser['link'][$typenow];
$thumbpath = $tinybrowser[$tinybrowser['thumbsrc']][$typenow];

// Assign browsing options
$sortbynow = (isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : $tinybrowser['order']['by']);
$sorttypenow = (isset($_REQUEST['sorttype']) ? $_REQUEST['sorttype'] : $tinybrowser['order']['type']);
$sorttypeflip = ($sorttypenow == 'asc' ? 'desc' : 'asc');  
$viewtypenow = (isset($_REQUEST['viewtype']) ? $_REQUEST['viewtype'] : $tinybrowser['view']['image']);
$findnow = (isset($_POST['find']) && !empty($_POST['find']) ? $_POST['find'] : false);
$showpagenow = (isset($_REQUEST['showpage']) ? $_REQUEST['showpage'] : 0);

// Assign sort parameters for column header links
$sortbyget = array();
$sortbyget['name'] = '&viewtype='.$viewtypenow.'&sortby=name';
$sortbyget['size'] = '&viewtype='.$viewtypenow.'&sortby=size'; 
$sortbyget['type'] = '&viewtype='.$viewtypenow.'&sortby=type'; 
$sortbyget['modified'] = '&viewtype='.$viewtypenow.'&sortby=modified';
$sortbyget['dimensions'] = '&viewtype='.$viewtypenow.'&sortby=dimensions'; 
$sortbyget[$sortbynow] .= '&sorttype='.$sorttypeflip;

// Assign css style for current sort type column
$thclass = array();
$thclass['name'] = '';
$thclass['size'] = ''; 
$thclass['type'] = ''; 
$thclass['modified'] = '';
$thclass['dimensions'] = ''; 
$thclass[$sortbynow] = ' class="'.$sorttypenow.'"';

// Initalise alert array
$notify = array(
	"type" => array(),
	"message" => array()
);
$newthumbqty = 0;

// read folder contents if folder exists
if(file_exists($tinybrowser['docroot'].$browsepath))
	{
	// Read directory contents and populate $file array
	$dh = opendir($tinybrowser['docroot'].$browsepath);
	$file = array();
	while (($filename = readdir($dh)) !== false)
		{
		if($filename != "." && $filename != ".." && !is_dir($tinybrowser['docroot'].$browsepath.$filename))
			{
			// search file name if search term entered
			if($findnow) $exists = stripos($filename,$findnow);
	
			// assign file details to array, for all files or those that match search
			if(!$findnow || ($findnow && $exists !== false))
				{
				$file['name'][] = $filename;
				$file['modified'][] = filemtime($tinybrowser['docroot'].$browsepath.$filename);
				$file['size'][] = filesize($tinybrowser['docroot'].$browsepath.$filename);
	
				// image specific info or general
				if($typenow=='image' && $imginfo = getimagesize($tinybrowser['docroot'].$browsepath.$filename))
					{
					$file['width'][] = $imginfo[0];
					$file['height'][] = $imginfo[1];
					$file['dimensions'][] = $imginfo[0] + $imginfo[1];
					$file['type'][] = $imginfo['mime'];
					
					// Check a thumbnail exists
					if(!file_exists($tinybrowser['docroot'].$browsepath."_thumbs/")) createfolder($tinybrowser['docroot'].$browsepath."_thumbs/",$tinybrowser['unixpermissions']);
			  		$thumbimg = $tinybrowser['docroot'].$browsepath."_thumbs/_".$filename;
					if (!file_exists($thumbimg))
						{
						$nothumbimg = $tinybrowser['docroot'].$browsepath.$filename;
						$mime = getimagesize($nothumbimg);
						$im = convert_image($nothumbimg,$mime['mime']);
						resizeimage($im,$tinybrowser['thumbsize'],$tinybrowser['thumbsize'],$thumbimg,$tinybrowser['thumbquality']);
						imagedestroy($im);
						$newthumbqty++;
						}
					}
				else 
					{
					$file['width'][] = 'N/A';
					$file['height'][] = 'N/A';
					$file['dimensions'][] = 'N/A';
					$file['type'][] = returnMIMEType($filename);
					}
				}			
			}
		}
	closedir($dh);
	}

	

// determine sort order
$sortorder = ($sorttypenow == 'asc' ? SORT_ASC : SORT_DESC);
$num_of_files = (isset($file['name']) ? count($file['name']) : 0);

if($num_of_files>0)
	{
	// sort files by selected order
	sortfileorder($sortbynow,$sortorder,$file);
	}

// determine pagination
if($tinybrowser['pagination']>0)
	{
	$showpage_start = ($showpagenow ? ($_REQUEST['showpage']*$tinybrowser['pagination'])-$tinybrowser['pagination'] : 0);
	$showpage_end = $showpage_start+$tinybrowser['pagination'];
	if($showpage_end>$num_of_files) $showpage_end = $num_of_files;
	}
else
	{
	$showpage_start = 0;
	$showpage_end = $num_of_files;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo POPUP_TITLE; ?> - <?php echo TB_BROWSE; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if(!$standalone && $tinybrowser['integration']=='tinymce')
	{
	?><script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script><?php 
	}
else
	{
	?><link rel="stylesheet" type="text/css" media="all" href="css/stylefull_tinybrowser.css" /><?php 
	}
?>
<link rel="stylesheet" type="text/css" media="all" href="css/style_tinybrowser.css.php" />
<script language="javascript" type="text/javascript" src="js/tinybrowser.js.php?<?php echo substr($passfeid,1); ?>"></script>
<script type="text/javascript">
function insertURL(url)
	{
		document.passform.fileurl.value = url;
		FileBrowserDialogue.mySubmit();
	}
	var FileBrowserDialogue = {
	    init : function () {
	        // Here goes your code for setting your custom things onLoad.
				rowHighlight();
	    },
	    mySubmit : function () {
	 		  var URL = document.passform.fileurl.value;
	        var win = tinyMCEPopup.getWindowArg("window");
	
	        // insert information now
	        win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
	
	        // for image browsers: update image dimensions
			  if (typeof(win.ImageDialog) != "undefined" && document.URL.indexOf('type=image') != -1)
				  {
		        if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
		        if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);
				  }
	
	        // close popup window
	        tinyMCEPopup.close();
	    }
	}
	tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);
</script>
</head>
<body<?php echo $rowhlightinit; ?>>
<?php
if(count($notify['type'])>0) alert($notify);
?>
<div class="tabs">
<ul> 
<li id="browse_tab" ><span><a href="tinybrowser.php?type=<?php echo $typenow.$passfeid ; ?>"><?php echo TB_BROWSE; ?></a></span></li><?php
if($tinybrowser['allowupload']) 
	{ 	?><li id="upload_tab"><span><a href="upload.php?type=<?php echo $typenow.$passfeid ; ?>"><?php echo TB_UPLOAD; ?></a></span></li><?php
	}
if($tinybrowser['allowedit'] || $tinybrowser['allowdelete'])
	{
	?><li id="edit_tab"><span><a href="edit.php?type=<?php echo $typenow.$passfeid ; ?>"><?php echo TB_EDIT; ?></a></span></li><?php
	} ?>
    <?php
if ($_REQUEST['type'] == 'file') { ?>
<li id="article_tab" class="current"><span><a href="article.php?type=<?php echo $typenow.$passfeid ; ?>"><?php echo TB_ARTICLES; ?></a></span></li>
<li id="article_tab" ><span><a href="menu.php?type=<?php echo $typenow.$passfeid ; ?>"><?php echo TB_MENU; ?></a></span></li><?php
} ?>
</ul>
</div>
<div class="panel_wrapper">
<div id="general_panel" class="panel currentmod">
<fieldset>
<legend><?php echo TB_BROWSEARTICLES; ?></legend>
<?php
form_open('browse','custom',basename($_SERVER["SCRIPT_NAME"]),'?type='.$typenow.$passfeid);
?>
<div class="pushleft">
<?php
// Offer view type if file type is image
if($typenow=='image')

	{
	$select = array(
		array("thumb",TB_THUMBS),
		array("detail",TB_DETAILS)
	);
	form_select($select,'viewtype',TB_VIEW,$viewtypenow,true);
	}
	
// Show page select if pagination is set
if($tinybrowser['pagination']>0)
	{
	$pagelimit = ceil($num_of_files/$tinybrowser['pagination'])+1;
	$page = array();
	for($i=1;$i<$pagelimit;$i++)
		{
		$page[] = array($i,TB_PAGE.' '.$i);
		}
	if($i>2) form_select($page,'showpage',TB_SHOW,$showpagenow,true);
	}
?></div><div class="pushright"><?php

form_hidden_input('sortby',$sortbynow);
form_hidden_input('sorttype',$sorttypenow);
form_text_input('find',false,'',25,50);


form_submit_button('search',TB_SEARCH,'');

?></div>
<?php

// if image show dimensions header
if($typenow=='image')
	{
	$imagehead = '<th><a href="?type='.$typenow.$passfeid.$sortbyget['dimensions'].'"'.$thclass['dimensions'].'>'.TB_DIMENSIONS.'</a></th>';
	}
else $imagehead = '';

echo '<div class="tabularwrapper"><table class="browse">'
		.'<tr><th><a href="?type='.$typenow.$passfeid.$sortbyget['name'].'"'.$thclass['name'].'>'.TB_ARTICLETITLE.'</a></th>'
		.'<th><a href="?type='.$typenow.$passfeid.$sortbyget['modified'].'"'.$thclass['modified'].'>'.TB_ARTICLEDATE.'</th></tr>';


	while ($row = mysql_fetch_array($qry)) 
		{
		
		echo '<tr class="'.$alt.'">';
	echo '<td><a href="#" onclick="insertURL(\''.$row['link_url'].'\');" title="'.$row['link_name'].'">'.truncate_text($row['link_name'],50).'</a></td>';
		echo '<td>'.date($tinybrowser['dateformat'],$file['modified'][$i]).'</td></tr>'."\n";
		}
	echo '</table></div>';
	
?>
</fieldset></div></div>
<form name="passform"><input name = "fileurl" type="hidden" value= "" /></form>
</body>
</html>
