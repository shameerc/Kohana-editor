<?php
error_reporting(0);
/** 
 * TinyMCE WYSIWYG Editor PHP function
 *
 * @author Nikhil Ben Kuruvilla
 * @date 02-11-2010
 * @package EcommerceGuruji
 * @link http://www.ispg.in
 */


/** 
 * Initialize TinyMce Editor.
 *
 * @param string $mode (advanced, simple, basic)
 * @param string $location location of toolbar (top, bottom)
 * @param string $align alignment of toolbar (left, center, right)
 * @param string $statusBar status bar location (top, bottom)
 * @param int $skin 
 */
function initEditor($param,$mode='',$skin='',$align='',$location='',$statusBar='')
{ 
// Select Query
// Set default js directory path
define('GLOBAL_JS_DIRECTORY',$param['global_js_directory']);
/**
 * Editor selector name, used to identify the textarea that
 * should be used as tinymce editor.
 *
 * Eg: <textarea class="tinymce"></textarea>
 */
$editor_selector = 'tinymce';
if ($skin == '') {
        $skin = $param['skin'];
}
// Select Skin
switch ($skin)
	{
		case '3':
			$skin = "skin : \"o2k7\", skin_variant : \"black\",";
			break;
		case '2':
			$skin = "skin : \"o2k7\", skin_variant : \"silver\",";
			break;
		case '1':
			$skin = "skin : \"o2k7\",";
			break;
		case '0':
		default:
			$skin = "skin : \"default\",";
	}

// Set default mode
if ($mode == '') {
	$mode = $param['mode'];
}

/**
 * NOTE : For testing purpose 
 * Usage eg: append '?mode=advanced' to the url
 */
if ($_REQUEST['mode']) {
	$mode = $_REQUEST['mode'];
}

// Set default toolbar location
if ($location == '') {
	$location = $param['location'];
}

// Set default toolbar alignment
if ($align == '') {
	$align = $param['align'];
}

// Set default location for status bar
if ($statusBar == '') {
	$statusBar = $param['status_bar'];
}

/**
 * Basic Configurations for plugins and button set.
 *
 * Prepare plugin array.
 */
$buttons1_add_before = $buttons1_add = array();
$buttons2_add_before = $buttons2_add = array();
$buttons3_add_before = $buttons3_add = array();
$buttons4 = array();
$plugins  = array();

// For advanced mode
if ($mode == 'advanced') {

	// fonts
	if ($param['fonts'] == 'TRUE') {
		$buttons1_add[]	= 'fontselect,fontsizeselect';
	}
	
	// paste
	if ($param['paste'] == 'TRUE') {
		$plugins[]	= 'paste';
		$buttons4[]	= 'pastetext';
		$buttons4[]	= 'pasteword';
		$buttons4[]	= 'selectall,|';
	}
	
	// search & replace
	if ($param['search_replace'] == 'TRUE') {
		$plugins[]	            = 'searchreplace';
		$buttons2_add_before[]	= 'search,replace,|';
	}
		
	// insert date and/or time plugin
	if ($param['insert_date'] == 'TRUE' || $param['insert_time'] == 'TRUE') {
		$plugins[]	    = 'insertdatetime';
		$buttons2_add[]	= 'insertdate';
		$buttons2_add[]	= 'inserttime';
	}
	
	// colors
	if ($param['colours'] == 'TRUE') {
		$buttons2_add[]	= 'forecolor,backcolor';
	}
		
	// table
	if ($param['table'] == 'TRUE') {
		$plugins[]				= 'table';
		$buttons3_add_before[]	= 'tablecontrols';
	}
		
	// emotions
	if ($param['smileys'] == 'TRUE') {
		$plugins[]	    = 'emotions';
		$buttons3_add[]	= 'emotions';
	}

	//media plugin
	if ($param['media'] == 'TRUE') {
		$plugins[]      = 'media';
		$buttons3_add[] = 'media';
	}
	
	// horizontal line
	if ($param['horizontal_rule'] == 'TRUE') {
		$plugins[]	    = 'advhr';
		$elements[]     = 'hr[id|title|alt|class|width|size|noshade|style]';
		$buttons3_add[]	= 'advhr';
	}
	
	// rtl/ltr buttons
	if ($param['directionality'] == 'TRUE') {
		$plugins[]      = 'directionality';
		$buttons3_add[] = 'ltr,rtl';
	}	

	// fullscreen
	if ($param['fullscreen'] == 'TRUE') {
		$plugins[]	    = 'fullscreen';
		$buttons2_add[]	= 'fullscreen';
	}
		
	// layer
	if ($param['layer'] == 'TRUE') {
		$plugins[]	= 'layer';
		$buttons4[]	= 'insertlayer';
		$buttons4[]	= 'moveforward';
		$buttons4[]	= 'movebackward';
		$buttons4[]	= 'absolute';
	}

	// style
	if ($param['style'] == 'TRUE') {
		$plugins[]	= 'style';
		$buttons4[]	= 'styleprops';
	}
	
	// XHTMLxtras
	if ($param['xhtml_extras'] == 'TRUE') {
		$plugins[]	= 'xhtmlxtras';
		$buttons4[]	= 'cite,abbr,acronym,ins,del,attribs';
	}
	
	// visualchars
	if ($param['visual_chars'] == 'TRUE') {
		$plugins[]	= 'visualchars';
		$buttons4[]	= 'visualchars';
	}
	
	// non-breaking
	if ($param['non_breaking'] == 'TRUE') {
		$plugins[]	= 'nonbreaking';
		$buttons4[]	= 'nonbreaking';
	}
	
	// blockquote
	if ($param['block_quote'] == 'TRUE') {
		//$plugins[]  = 'blockquote';
		$buttons4[] = 'blockquote';
	}
	
	// template
	if ($param['template'] == 'TRUE') {
		$plugins[]	= 'template';
		$buttons4[]	= 'template';
	}
		
	// advimage
	if ($param['adv_image'] == 'TRUE') {
		$plugins[]	= 'advimage';
		$elements[]	= 'img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style]';
	}
		
	// advlink
	if ($param['adv_link'] == 'TRUE') {
		$plugins[]	= 'advlink';
		$elements[]	= 'a[id|class|name|href|target|title|onclick|rel|style]';
	}
	
	// autosave
	if ($param['auto_save'] == 'TRUE') {
		$plugins[]	= 'autosave';
	}
		
	// context menu
	if ($param['context_menu'] == 'TRUE') {
		$plugins[]	= 'contextmenu';
	}
	
	// inline popups
	if ($param['inline_popup'] == 'TRUE') {
		$plugins[]	= 'inlinepopups';
	}
	
	// Pagebreak
	$plugins[]	= 'pagebreak';
	$buttons4[]	= 'pagebreak';

	// Prepare config variables
	$buttons1_add_before = implode(',', $buttons1_add_before);
	$buttons2_add_before = implode(',', $buttons2_add_before);
	$buttons3_add_before = implode(',', $buttons3_add_before);
	$buttons1_add = implode(',', $buttons1_add);
	$buttons2_add = implode(',', $buttons2_add);
	$buttons3_add = implode(',', $buttons3_add);
	$buttons4 = implode(',', $buttons4);
	$plugins = implode(',', $plugins);
	//$elements = implode(',', $elements);

}
 
/** 
 * Advanced - Full featured TinyMce Editor
 * Simple - TinyMce with minimum set of features
 * Basic - TinyMce in advanced mode, but limited features, Default !
 *
 * @param string $mode
 */


switch($mode)
{
	case simple: 
		$return =  "<script type='text/javascript' src='".GLOBAL_JS_DIRECTORY."tiny_mce_src.js'></script>
					<script type='text/javascript' src='".GLOBAL_JS_DIRECTORY."/plugins/tinybrowser/tb_tinymce.js.php'></script>
					<script type='text/javascript'>
					tinyMCE.init({
						mode : 'textareas',
						$skin
						theme : \"$mode\",
						editor_selector : \"$editor_selector\"
					});
					function toggleEditor(id) {
					if (!tinyMCE.get(id))
						tinyMCE.execCommand('mceAddControl', false, id);
					else
						tinyMCE.execCommand('mceRemoveControl', false, id);
					}
				</script>";
	break;
	case advanced:
		$return =  "<script type='text/javascript' src='".GLOBAL_JS_DIRECTORY."tiny_mce_src.js'></script>
					<script type='text/javascript' src='".GLOBAL_JS_DIRECTORY."plugins/tinybrowser/tb_tinymce.js.php'></script>
					<script type='text/javascript'>
					tinyMCE.init({
						mode : 'textareas',
						$skin
						theme : \"$mode\",
						plugins : \"$plugins\",	
						editor_selector : \"$editor_selector\",		
						file_browser_callback : 'tinyBrowser',																																																																																																																																																																																					
						theme_advanced_buttons1_add_before : \"$buttons1_add_before\",
						theme_advanced_buttons2_add_before : \"$buttons2_add_before\",
						theme_advanced_buttons3_add_before : \"$buttons3_add_before\",
						theme_advanced_buttons1_add : \"$buttons1_add\",
						theme_advanced_buttons2_add : \"$buttons2_add\",
						theme_advanced_buttons3_add : \"$buttons3_add\",
						theme_advanced_buttons4 : \"$buttons4\",
						theme_advanced_toolbar_location : \"$location\",
						theme_advanced_toolbar_align : \"$align\",
						theme_advanced_statusbar_location : \"$statusBar\",
						theme_advanced_resizing : true
					});
					function toggleEditor(id) {
					if (!tinyMCE.get(id))
						tinyMCE.execCommand('mceAddControl', false, id);
					else
						tinyMCE.execCommand('mceRemoveControl', false, id);
					}
				</script>";
	break;
	case basic:
		$return =  "<script type='text/javascript' src='".GLOBAL_JS_DIRECTORY."tiny_mce_src.js'></script>
					<script type='text/javascript' src='".GLOBAL_JS_DIRECTORY."plugins/tinybrowser/tb_tinymce.js.php'></script>
					<script type='text/javascript'>
					tinyMCE.init({
						mode : 'textareas',
						$skin
						theme : 'advanced',
						plugins : 'inlinepopups',	
						editor_selector : \"$editor_selector\",		
						file_browser_callback : 'tinyBrowser',																																																																																																																																																																																					
						theme_advanced_toolbar_location : \"$location\",
						theme_advanced_toolbar_align : \"$align\",
						theme_advanced_statusbar_location : \"$statusBar\",
						theme_advanced_resizing : true
					});
					function toggleEditor(id) {
					if (!tinyMCE.get(id))
						tinyMCE.execCommand('mceAddControl', false, id);
					else
						tinyMCE.execCommand('mceRemoveControl', false, id);
					}
				</script>";
}
return $return;
}

/** 
 * Editor calling function, displays TinyMce editor.
 *
 * NOTE : To use this function TinyMce initializing function,
 * initEditor() should be called at first.
 * Usage : editor('name');
 *
 * @param string $name
 * @param text $content
 * @param int $width
 * @param int $height
 * @param int $col
 * @param int $row
 */
function editor($name, $content = '', $width = '', $height = '', $col = '', $row = '', $toggleMode = FALSE) 
{
	// To set width and height type
	if (is_numeric( $width )) {
			$width .= '';
		}
		if (is_numeric( $height )) {
			$height .= '';
		}
	
	// For toggling between modes	
	if ($_REQUEST['mode'] == 'simple') {
		$mode = 'basic';
	} elseif ($_REQUEST['mode'] == 'advanced') {
		$mode = 'simple';
	} else {
		$mode = 'advanced';
	}
	// convert <br /> tags so they are not visible when editing
 	$content = str_replace( '<br />', "\n", $content );
	
	$return = "<textarea id=\"$name\" name=\"$name\" cols=\"$col\" rows=\"$row\" style=\"width:{$width}%; height:{$height};\" class=\"tinymce\">".htmlspecialchars($content)."</textarea>"
			 ."<a href=\"javascript:toggleEditor('$name');\">Toggle Editor</a>"; 
			   
		   // Enable 'toggle mode' option
		   if ($toggleMode == TRUE) {
				$return .= "| <a href=\"?mode={$mode}\">Toggle Mode</a>";
			}
	
	return $return;
}