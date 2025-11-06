<?php
namespace Bucorel\Waf\Graphic;

class BarcodeGenerator {

    protected $data = "";
    protected $fontPath = 'LibreBarcode39Text-Regular.ttf'; // CHANGE THIS: Path to your free Code 39 TTF font
    protected $width = 300;
    protected $height = 80;
    protected $fontSize = 30;
    protected $barColor = [0, 0, 0];   // Black
    protected $backgroundColor = [255, 255, 255]; // White

    /**
     * Constructor for BarcodeGenerator.
     * * @param string $data The string to be encoded in the barcode.
     * @param string $fontPath Path to the Code 39 TTF font file.
     */
    function __construct( $data = "", $fontPath = null ){
        $this->data = strtoupper($data); // Code 39 typically uses uppercase
        if ($fontPath) {
            $this->fontPath = $fontPath;
        }else{
			$this->fontPath = dirname(__FILE__).'/LibreBarcode39Text-Regular.ttf';
		}
    }

    // --- Setter Methods ---

    function setDimensions( int $width, int $height ){
        $this->width = $width;
        $this->height = $height;
    }

    function setFontSize( int $size ){
        $this->fontSize = $size;
    }

    function setBarColor( int $red, int $green, int $blue ){
        $this->barColor = [ $red, $green, $blue ];
    }

    function setBackgroundColor( int $red, int $green, int $blue ){
        $this->backgroundColor = [ $red, $green, $blue ];
    }
    
    // --- Generation Method ---

    function generate(){
        // 1. Prepare data for Code 39 (requires start/stop asterisks)
        $barcode_text = "*" . $this->data . "*";
        
        // Check if the font file exists
        if (!file_exists($this->fontPath)) {
            // Log an error or throw an exception if the font is missing
            throw new \Exception("Barcode font file not found at: " . $this->fontPath);
        }

        // 2. Image Creation
        $im = imagecreatetruecolor($this->width, $this->height);

        // Allocate colors
        $bg = imagecolorallocate($im, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
        $fg = imagecolorallocate($im, $this->barColor[0], $this->barColor[1], $this->barColor[2]);

        // 3. Draw Background
        imagefill($im, 0, 0, $bg);

        // 4. Calculate position to center the barcode
        // Get the bounding box of the rendered text
        $bbox = imagettfbbox($this->fontSize, 0, $this->fontPath, $barcode_text);
        
        // Calculate text width and height
        $text_width = $bbox[2] - $bbox[0];
        $text_height = $bbox[1] - $bbox[7]; // Calculate height from bottom-left to top-left

        // Calculate centering positions
        $x_pos = intval(($this->width - $text_width) / 2);
        $y_pos = intval(($this->height / 2) + ($text_height / 2)); 

        // 5. Draw the barcode bars using the font
        imagettftext($im, $this->fontSize, 0, $x_pos, $y_pos, $fg, $this->fontPath, $barcode_text);

        // 6. Output Image
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-type: image/png');
            
        // Generate the png image
        imagepng($im);
            
        // Destroy the image
        imagedestroy($im);
    }
}
?>