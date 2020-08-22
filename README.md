# Compress
 Advanced file compresser which reduces large files to smaller chunks by saving them as binary for later use. Many applications supports file uploads which consumes large memory sizes on their systems. This library has been designed to reduce both zip, rar, images, pdf, words sizes or any kinds of files or documents into smaller pieces.

## Install:
Use composer to install
```php
composer require mitmelon/compress
```

## Usage :

```php
require_once __DIR__."/vendor/autoload.php";

// Initialize library class
$compress = new Compress\Compress;

/**
 * @param $filePath string
 * File location to be compressed
 *
 * @param $storePath string
 * Path to output compressed binary file to
 *
 */
$compress::compressFile($filePath, $storePath);

//Compress Image file
$compress::compressFile(__DIR__.'/image.png', __DIR__.'/image.txt'));

// Compress PDF
$compress::compressFile(__DIR__.'/file.pdf', __DIR__.'/file.txt'));
//Compress as lot files you want including zip files

/**
 * UnCompress Image file [Get original file back from stored binary]
 * @param $storePath string
 * Path containing binary file which was compressed
 *
 * @param $fileOutputPath string
 * Path to output original file to
 *
 */
$compress::uncompressFile($storePath, $fileOutputPath);

//Uncompress Image
$compress::uncompressFile(__DIR__.'/image.txt', __DIR__.'/image.png');

// Uncompress PDF
$compress::uncompressFile( __DIR__.'/file.txt', __DIR__.'/file.pdf')

```

# Future Update

Adding binary file encryptions for security purposes.

# License

Released under the MIT license.