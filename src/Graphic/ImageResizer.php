<?php
namespace Bucorel\Waf\Gaphic;

/**
 * ImageResizer Class
 * * Resizes images using the GD library, maintaining the aspect ratio
 * and preserving quality via resampling (imagecopyresampled).
 */
class ImageResizer
{
    private string $sourcePath;
    private $imageResource;
    private int $originalWidth;
    private int $originalHeight;
    private int $imageType;
    private int $quality;

    /**
     * @param string $sourcePath The full path to the source image file.
     * @throws \Exception If the file is not a supported image or does not exist.
     */
    public function __construct(string $sourcePath)
    {
        if (!file_exists($sourcePath)) {
            throw new \Exception("Source file not found at: {$sourcePath}");
        }
        $this->sourcePath = $sourcePath;
        $this->quality = 90; // Default quality for JPEG/PNG
        
        $this->loadResource();
    }

    /**
     * Sets the output quality for JPEG (0-100) and PNG (0-9, where 0 is best quality)
     * @param int $quality
     * @return self
     */
    public function setQuality(int $quality): self
    {
        $this->quality = max(0, min(100, $quality));
        return $this;
    }

    /**
     * Loads the image into a GD resource and retrieves dimensions.
     * @throws \Exception
     */
    private function loadResource(): void
    {
        $imageInfo = getimagesize($this->sourcePath);

        if ($imageInfo === false) {
            throw new \Exception("Could not get image size or unsupported file type.");
        }

        list($this->originalWidth, $this->originalHeight, $this->imageType) = $imageInfo;

        switch (image_type_to_mime_type($this->imageType)) {
            case 'image/jpeg':
                $this->imageResource = imagecreatefromjpeg($this->sourcePath);
                break;
            case 'image/png':
                $this->imageResource = imagecreatefrompng($this->sourcePath);
                break;
            case 'image/gif':
                $this->imageResource = imagecreatefromgif($this->sourcePath);
                break;
            default:
                throw new \Exception("Unsupported image format detected.");
        }
        
        if ($this->imageResource === false) {
             throw new \Exception("Failed to create image resource from file.");
        }
    }

    /**
     * Calculates the new width and height while preserving the aspect ratio,
     * ensuring the result fits within the specified max boundaries.
     *
     * @param int $maxWidth The maximum allowed width.
     * @param int $maxHeight The maximum allowed height.
     * @return array [newWidth, newHeight]
     */
    private function calculateNewDimensions(int $maxWidth, int $maxHeight): array
    {
        $widthRatio = $maxWidth / $this->originalWidth;
        $heightRatio = $maxHeight / $this->originalHeight;

        // Use the smaller ratio to ensure the image fits *within* the boundaries
        $ratio = min($widthRatio, $heightRatio);

        // Optional: Prevent upscaling if the image is already smaller
        if ($ratio > 1) {
            $ratio = 1;
        }

        $newWidth = (int)round($this->originalWidth * $ratio);
        $newHeight = (int)round($this->originalHeight * $ratio);

        return [$newWidth, $newHeight];
    }

    /**
     * Creates a new canvas and copies the resampled image data onto it.
     *
     * @param int $newWidth
     * @param int $newHeight
     * @return resource|\GdImage The new image resource.
     */
    private function createNewImageResource(int $newWidth, int $newHeight)
    {
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Handle transparency for PNG and GIF
        if ($this->imageType == IMAGETYPE_GIF) {
            $trnprt_indx = imagecolortransparent($this->imageResource);
            if ($trnprt_indx >= 0) {
                $trnprt_color = imagecolorsforindex($this->imageResource, $trnprt_indx);
                $trnprt_indx = imagecolorallocate($newImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                imagefill($newImage, 0, 0, $trnprt_indx);
                imagecolortransparent($newImage, $trnprt_indx);
            }
        } elseif ($this->imageType == IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            imagefill($newImage, 0, 0, $transparent);
        }

        // Resample the original image onto the new canvas
        imagecopyresampled(
            $newImage,
            $this->imageResource,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $this->originalWidth,
            $this->originalHeight
        );

        return $newImage;
    }

    /**
     * Saves the GD resource to the specified path based on the original image type.
     *
     * @param resource|\GdImage $newImage The GD resource to save.
     * @param string $destinationPath The full path to save the resized image.
     * @return bool True on success, false on failure.
     */
    private function saveImage($newImage, string $destinationPath): bool
    {
        switch (image_type_to_mime_type($this->imageType)) {
            case 'image/jpeg':
                // Quality 0-100
                return imagejpeg($newImage, $destinationPath, $this->quality);
            case 'image/png':
                // Compression 0-9 (0 is no compression, 9 is max compression/low quality)
                $compression = (int)round((100 - $this->quality) / 10);
                return imagepng($newImage, $destinationPath, $compression);
            case 'image/gif':
                return imagegif($newImage, $destinationPath);
            default:
                return false;
        }
    }
    
    /**
     * Main function to resize and save the image.
     *
     * @param string $destinationPath The full path where the resized image should be saved.
     * @param int $maxWidth The maximum width boundary.
     * @param int $maxHeight The maximum height boundary.
     * @return bool True on successful save, false otherwise.
     */
    public function resizeAndSave(string $destinationPath, int $maxWidth, int $maxHeight): bool
    {
        if ($this->imageResource === null) {
            return false;
        }

        list($newWidth, $newHeight) = $this->calculateNewDimensions($maxWidth, $maxHeight);

        $newImage = $this->createNewImageResource($newWidth, $newHeight);
        
        $result = $this->saveImage($newImage, $destinationPath);

        // Clean up the newly created image resource
        imagedestroy($newImage);

        return $result;
    }

    /**
     * Cleans up the original image resource when the object is destroyed.
     */
    public function __destruct()
    {
        if (is_resource($this->imageResource) || ($this->imageResource instanceof \GdImage)) {
            imagedestroy($this->imageResource);
        }
    }
}

// --- EXAMPLE USAGE ---
/*
// 1. Set up file paths (You would typically get the source from an uploaded $_FILES['file']['tmp_name'])
$sourceFile = __DIR__ . '/test_image.jpg'; // Path to an existing image
$destinationFile = __DIR__ . '/resized_thumbnail.jpg'; // Path to save the new image

// 2. Mock a dummy file for the example if it doesn't exist
if (!file_exists($sourceFile)) {
    // Create a dummy JPEG file using GD for testing purposes
    $dummy = imagecreatetruecolor(1200, 800);
    $white = imagecolorallocate($dummy, 255, 255, 255);
    $black = imagecolorallocate($dummy, 0, 0, 0);
    imagefilledrectangle($dummy, 0, 0, 1200, 800, $white);
    imagestring($dummy, 5, 450, 400, '1200x800 Test Image', $black);
    imagejpeg($dummy, $sourceFile);
    imagedestroy($dummy);
    echo "Created a dummy image for testing at: " . basename($sourceFile) . "\n\n";
}

// 3. Define the max dimensions
$maxWidth = 400;
$maxHeight = 300;

try {
    // 4. Instantiate the resizer class
    $resizer = new ImageResizer($sourceFile);
    
    // Optional: Set quality (e.g., 85 for JPEG)
    $resizer->setQuality(85);

    // 5. Resize and save
    $success = $resizer->resizeAndSave($destinationFile, $maxWidth, $maxHeight);

    if ($success) {
        // Get new dimensions for confirmation
        list($newWidth, $newHeight) = getimagesize($destinationFile);
        
        echo "Successfully resized image!\n";
        echo "Source: 1200x800\n";
        echo "Target Max: {$maxWidth}x{$maxHeight}\n";
        echo "Result Saved: " . basename($destinationFile) . " ({$newWidth}x{$newHeight}) - Ratio Maintained.\n";
    } else {
        echo "Error: Image saving failed (check permissions on the directory).\n";
    }
} catch (\Exception $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}
*/