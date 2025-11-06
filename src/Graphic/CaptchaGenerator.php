<?php
namespace Bucorel\Waf\Graphic;

class CaptchaGenerator{

	protected $width = 118;
	protected $height = 30;
	protected $noiseLevel = 30;
	protected $backgroundColor = [255,255,255];
	protected $foregroundColor = [0,0,0];

	function __construct( $width=118, $height=30, $noiseLevel=30 ){
		$this->width = $width;
		$this->height = $height;
		$this->noiseLevel = $noiseLevel;
	}

	function setBackgroundColor( int $red, int $green, int $blue ){
		$this->backgroundColor = [ $red, $green, $blue ];
	}

	function setForegroundColor( int $red, int $green, int $blue ){
		$this->foregroundColor = [ $red, $green, $blue ];
	}

	function generate(){
		//generate the random code
		$code=rand(100000,999999);
			
		//save it in SESSION for furhter form validation
		$_SESSION["captcha"]=$code;
			
		//create the image resource 
		$im = imagecreatetruecolor($this->width, $this->height);
		//$bg = imagecolorallocate($im, 255, 255, 255); //background color
		$bg = imagecolorallocate($im, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]); //background color
		$fg = imagecolorallocate($im, $this->foregroundColor[0], $this->foregroundColor[1], $this->foregroundColor[2]);//text color
		$ns = imagecolorallocate($im, $this->foregroundColor[0], $this->foregroundColor[1], $this->foregroundColor[2]);//noise color
			
		//fill the image resource with the bg color
		imagefill($im, 0, 0, $bg);
			
		//Add the random code of string to the image
		$font =dirname(__FILE__).'/Pacifico.ttf';
		imagettftext($im, 30, 0, 5, 22, $fg, $font, $code);

		// Add some noise to the image.
		for ($i = 0; $i < $this->noiseLevel; $i++) {
			for ($j = 0; $j < $this->noiseLevel; $j++) {
				imagesetpixel(
					$im,
					rand(0, $this->width), 
					rand(0, $this->height),//make sure the pixels are random and don't overflow out of the image
					$ns
				);
			}
		}
		
		for($j=0;$j<15;$j++){
			imageline($im, rand(0,50), rand(0,30), 100, rand(10,30), $fg);
		}

		//tell the browser that this is an image
		header("Cache-Control: no-cache, must-revalidate");
		header('Content-type: image/png');
			
		//generate the png image
		imagepng($im);
			
		//destroy the image
		imagedestroy($im);
	}
}
?>