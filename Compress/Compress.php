<?php
namespace Compress;
/**
 * Compresses files including zip, image, pdf and lots more
 *
  * @link https://mitnets.com for Programming Tutorials
 *  @author Mitnets Technologies <support@mitnets.com>
 *  @version 1.0.0
 */
class Compress
{
    public static function compressor($content)
    {
        return gzcompress($content);
    }
    //Uncompress Content
    public static function uncompressor($content)
    {
        return gzuncompress($content);
    }
    //Add file to existing zip dir

    public static function compressFile($filePath, $storePath)
    {
        //experimental version 1.0.0
        try {
            $file = fopen($filePath, "rb");
            $contents = self::compressor(serialize(fread($file, filesize($filePath))));
            file_put_contents($storePath, $contents);
            fclose($file);
            return true;
        } catch (\Extension $e) {
            return $e->getMessage();
        }
    }

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