<?php
/**
 * resizes a JPG or PNG image from a source file
 * 
 * @author maetl
 */
class ImageResizer {
	private $source;
	private $size;
	private $type;

	function __construct($source, $type=false) {
		$this->source = realpath($source);
		$this->size = getimagesize($source);
		if (!$type) {
			$system=explode(".", $this->source);
			if (preg_match("/jpg|jpeg/",$system[1])){ $this->type = "image/jpeg"; }
			if (preg_match("/png/",$system[1])){ $this->type = "image/png"; }
		} else {
			$this->type = $type;
		}
	}
	
	/**
	 * Return an image resource from the given file
	 */
	private function createFromSource() {
		switch($this->type) {
			case 'image/jpeg':
				$image = imagecreatefromjpeg($this->source);
			break;
			case 'image/png':
				$image = imagecreatefrompng($this->source);
			break;
			default:
				throw new Exception("Unsupported image type: {$this->type}");
			break;
		}
		return $image;
	}
	
	/**
	 * Returns the width of the source image
	 */
	function sourceWidth() {
		return $this->size[0];
	}			if (preg_match("/jpg|jpeg/",$system[1])){ $this->type = "image/jpeg"; }
			if (preg_match("/png/",$system[1])){ $this->type = "image/png"; }
		} else {
			$this->type = $type;
		}
	}
	
	/**
	 * Return an image resource from the given file
	 */
	private function createFromSource() {
		switch($this->type) {
			case 'image/jpeg':
	$preserveArea
	 */
	function copyTo($target, $width=false, $height=false, $stretch=false) {
		if (!$width && !$height) {
			$this->copyExact($target);
		} elseif ($width && !$height) {
			$this->copyScaled($target, $width);
		} else {
			if ($stretch) {
				$this->copyTransformed($target, $width, $height);
			} else {
				$this->copyResized($target, $width, $height);
			}
		}
	}
	
	/**
	 * Makes an exact copy of the source image in target location
	 */
	private function copyExact($target) {
		if (is_uploaded_file($this->source)) {
			if (!move_uploaded_file($this->source, $target)) throw new Exception("Bad file upload");
			$this->source = $target;
		} else {
			if (!copy($this->source, $target)) throw new Exception("File copy failed");
		}
	}
	
	/**
	 * Makes a resized copy of the source image with the new
	 * height scaled to the original aspect ratio.
	 */
	private function copyScaled($filename, $new_w) {
		$src_img = $this->createFromSource();
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
		$thumb_w = $new_w;
		$thumb_h = $old_y*($new_w/$old_x);
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		if (preg_match("/png/",$filename)) {
			imagepng($dst_img,$filename);
		} else {
			imagejpeg($dst_img,$filename); 
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
	}

	/**
	 * Makes a scaled copy of the original image by squashing or
	 * stretching the original area.
	 */
	private function copyTransformed($filename, $new_w, $new_h) {
		$src_img = $this->createFromSource();
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
		$thumb_w = $new_w;
		$thumb_h = $new_h;
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		if (preg_match("/png/",$filename)) {
			imagepng($dst_img,$filename);
		} else {
			imagejpeg($dst_img,$filename); 
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
	}
	
	/**
	 * Makes a resized copy of the source image
	 */
	private function copyResized($filename, $new_w, $new_h) {
		$src_img = $this->createFromSource();
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
		if ($old_x > $old_y)  {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
			$src_x = 0;
			$src_y = ($old_y-$new_h)/2;
			if ($new_w < $old_x) $old_x = $new_w;
			$old_y = $new_h;
		}
		if ($old_x < $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
			$src_x = 0;
			$src_y = ($old_y-$new_h)/2;
			if ($new_h < $old_y) $old_y = $new_h;
			if ($new_w < $old_x) $old_x = $new_w;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
			$src_x = 0;
			$src_y = 0;
		}
		if ($new_w == $new_h) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
			if ($old_x > $old_y) {
				$src_x = ($old_x-$old_y)/2;
				$src_y = 0;
				$old_x = $old_y;
			} elseif ($old_x < $old_y) {
				$src_y = ($old_y-$old_x)/2;
				$src_x = 0;
				$old_y = $old_x;
			} else {
				$src_x = 0;
				$src_y = 0;
			}
		}
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,$src_x,$src_y,$thumb_w,$thumb_h,$old_x,$old_y); 
		if (preg_match("/png/",$filename)) {
			imagepng($dst_img,$filename);
		} else {
			imagejpeg($dst_img,$filename); 
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
	}

}

?>