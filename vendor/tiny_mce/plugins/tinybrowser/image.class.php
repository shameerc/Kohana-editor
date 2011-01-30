<?php
/**
 * Image Manipulation class (For image crop)
 *
 * This can be used as an addon for tinybrowser module, which is a plugin
 * for tinymce editor. The cropping uses jquery and jcrop library.
 *
 * @package EcommerceGuruji
 * @category Editor
 */

class ImageManipulation {

	public $image   = array('targetx'=>0, 
							'targety'=>0,
							'quality'=>75);
	public $imageok = false;
	
    /**
     * Contructor method. Will create a new image from the target file.
	 * Accepts an image filename as a string. Method also works out how
	 * big the image is and stores this in the $image array.
     *
     * @param string $imgFile 
     */
	public function ImageManipulation($imgfile)
	{
		//detect image format
		$this->image["format"] = ereg_replace(".*\.(.*)$", "\\1", $imgfile);
		$this->image["format"] = strtoupper($this->image["format"]);
		
		// convert image into usable format.
		if ( $this->image["format"] == "JPG" || $this->image["format"] == "JPEG" ) {
			//JPEG
			$this->image["format"] = "JPEG";
			$this->image["src"]    = ImageCreateFromJPEG($imgfile);
		} elseif( $this->image["format"] == "PNG" ){
			//PNG
			$this->image["format"] = "PNG";
			$this->image["src"]    = imagecreatefrompng($imgfile);
		} elseif( $this->image["format"] == "GIF" ){
			//GIF
			$this->image["format"] = "GIF";
			$this->image["src"]    = ImageCreateFromGif($imgfile);
		} elseif ( $this->image["format"] == "WBMP" ){
			//WBMP
			$this->image["format"] = "WBMP";
			$this->image["src"]    = ImageCreateFromWBMP($imgfile);
		} else {
			//DEFAULT
			return false;
		}

		// Image is ok
		$this->imageok = true;
		
		// Work out image size
		$this->image["sizex"]  = imagesx($this->image["src"]);
		$this->image["sizey"] = imagesy($this->image["src"]);
	}

  

	/**
     * This method sets the cropping values of the image. Be sure
	 * to set the height and with of the image if you want the
	 * image to be a certain size after cropping.
     *
     * @param int $x
     * @param int $y 
	 * @param int $w 
     * @param int $h 
     */
	public function setCrop($x, $y, $w, $h)
	{
		$this->image["targetx"] = $x;
		$this->image["targety"] = $y;
		$this->image["sizex"] = $w;
		$this->image["sizey"] = $h;
	}
	
	/**
     * Sets the JPEG output quality.
     *
     * @param int $quality 
     */
	public function setJpegQuality($quality=75)
	{
		//jpeg quality
		$this->image["quality"] = $quality;
	}

		
	/**
     * Private method to run the imagecopyresampled() function with the parameters that have been set up.
	 * This method is used by the save() and show() methods.
     */
	private function createResampledImage()
	{
		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
		if ( isset($this->image["sizex_thumb"]) && isset($this->image["sizey_thumb"]) ) {		
			$this->image["des"] = ImageCreateTrueColor($this->image["sizex_thumb"], $this->image["sizey_thumb"]);
			imagecopyresampled($this->image["des"], $this->image["src"], 0, 0, $this->image["targetx"], $this->image["targety"], $this->image["sizex_thumb"], $this->image["sizey_thumb"], $this->image["sizex"], $this->image["sizey"]);
		} else {
			$this->image["des"] = ImageCreateTrueColor($this->image["sizex"], $this->image["sizey"]);
			imagecopyresampled($this->image["des"], $this->image["src"], 0, 0, $this->image["targetx"], $this->image["targety"], $this->image["sizex"], $this->image["sizey"], $this->image["sizex"], $this->image["sizey"]);
		}	
	}
	
	
	/**
     * Saves the image to a given filename, if no filename is given then a default is created.
	 *
	 * @param string $save The new image filename.
     */	
	public function save($save="")
	{
		//save thumb
		if ( empty($save) ) {
			$save = strtolower("./../../../uploads/images/".time().".".$this->image["format"]);
			
		}
		
		$this->createResampledImage();

		if ( $this->image["format"] == "JPG" || $this->image["format"] == "JPEG" ) {
			//JPEG
			imageJPEG($this->image["des"], $save, $this->image["quality"]);
		} elseif ( $this->image["format"] == "PNG" ) {
			//PNG
			imagePNG($this->image["des"], $save);
		} elseif ( $this->image["format"] == "GIF" ) {
			//GIF
			imageGIF($this->image["des"], $save);
		} elseif ( $this->image["format"] == "WBMP" ) {
			//WBMP
			imageWBMP($this->image["des"], $save);
		}
	}
}