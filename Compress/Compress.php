<?php
namespace Compress;
/**
 * Compresses files including zip, image, pdf and lots more
 *
  * @link https://manomiteblog.com for Programming Tutorials
 *  @author Manomite <manomitehq@gmail.com>
 *  @version 1.0.1
 */
class Compress
{
     /**
     * Text compress
     *
     * @param String $content
     */
    public static function compressor(String $content)
    {
        return gzcompress($content);
    }
   /**
     * Decompress Text
     *
     * @param String $content
     */
    public static function uncompressor(String $content)
    {
        return gzuncompress($content);
    }
    /**
     * File Compresser
     *
     * @param String $filePath
     * @param String $storePath
     * @param Bolean $removeMeta
     */
    public static function compressFile(String $filePath, String $storePath, $removeMeta = false)
    {
       
        try {
            $file = @fopen($filePath, "rb");
            $content = fread($file, filesize($filePath));
             //Meta heads are removed in new version which cannot be reversed
            if ($removeMeta) {
                $replace    = '@<meta[^>]*?>';
                $with       = ' ';
                $content    = str_replace($replace, $with, $content);
                $content    = preg_replace("/$replace/", $with, $content);
            }
            $content    = self::compressor(serialize($content));
            fclose($file);
            file_put_contents($storePath, $content);
            return true;
        } catch (\Extension $e) {
            return $e->getMessage();
        }
    }
   /**
     * File DeCompresser
     *
     * @param String $fileInputPath
     * @param String $fileOutputPath
     */
    public static function uncompressFile($fileInputPath, $fileOutputPath)
    {
        try {
            $file = fopen($fileInputPath, "rb");
            $contents = unserialize(self::uncompressor(fread($file, filesize($fileInputPath))));
            file_put_contents($fileOutputPath, $contents);
            fclose($file);
            return true;
        } catch (\Extension $e) {
            return $e->getMessage();
        }
    }
}