<h1 align="center"><a href="#" target="_blank"><img src="https://github.com/mitmelon/Compress/assets/55149512/d566ece6-41c3-4d25-9aae-d9bca05570f6" alt="File Compress" /></a></h1>

# Compress

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fmitmelon%2FCompress.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fmitmelon%2FCompress?ref=badge_shield) [![Total Downloads](http://poser.pugx.org/mitmelon/compress/downloads)](https://packagist.org/packages/mitmelon/compress) [![License](http://poser.pugx.org/mitmelon/compress/license)](https://packagist.org/packages/mitmelon/compress) [![PHP Version Require](http://poser.pugx.org/mitmelon/compress/require/php)](https://packagist.org/packages/mitmelon/compress)

Advanced file compresser which reduces and encrypts large files to smaller chunks by saving them as binary for later use. Many applications supports file uploads which consumes large memory sizes on their systems. This library has been designed to reduce both zip, rar, images, pdf, words sizes or any kinds of files or documents into smaller size.

## Install:

Use composer to install

```php
composer require mitmelon/compress
```

## Usage :

```php
require_once __DIR__."/vendor/autoload.php";

// Initialize library class
$compress = new Compress\Compress();

```

## Compress File Only

```php
/**
 * @param String $filePath
 * File location to be compressed
 * @param String $storePath
 * Path to output compressed binary file to
 * @param Mixed $options ["removeMeta" => false, "encrypt" => false, "key" => "password", "scanFile" => ["token" => $token, "service" => $service, "csp" => $csp, "region" => $region]]
 * Options to remove meta, encrypt file and scan content for reputations
 */
$compress::compressFile($filePath, $storePath, $options = []);

//Compress Image file
$compress::compressFile(__DIR__.'/image.png', __DIR__.'/image.txt');

// Compress PDF
$compress::compressFile(__DIR__.'/file.pdf', __DIR__.'/file.txt');
```

## Scan File and Compress

Scan content to get informations such as file's disposition, ranging from malicious (malware, ransomware, trojan horses, spyware, adware) to known good content (operating system files, known third-party software packages) using Pangea's File Intel Service.

To use the Pangea's File Intel Service [![create an account](https://pangea.cloud) and plug your credentials into the options below. Account creation is free. 

```php

$options = array("scanFile" => array("token" => "{File_Intel_API_Token}", "service" => 'file-intel', "csp" => 'aws', "region" => 'us'));
$compress::compressFile(__DIR__.'/file.pdf', __DIR__.'/file.txt', $options);

```

## Decompress File

```php
/**
 * UnCompress Image file [Get original file back from stored binary]
 * @param String $storePath
 * Path containing binary file which was compressed
 * @param String $fileOutputPath
 * Path to output original file to
 * @param String $encrypt_key
 * If your file was encrypted then provide the key for decryption as third argument
 */
$compress::uncompressFile($storePath, $fileOutputPath, $encrypt_key = null);

//Uncompress Image
$compress::uncompressFile(__DIR__.'/image.txt', __DIR__.'/image.png');

// Uncompress PDF
$compress::uncompressFile( __DIR__.'/file.txt', __DIR__.'/file.pdf')

```

## Other Public Variables To Change

```php
    /**
     * Encryption block chunk size
     *
     * @param Int $block
     */
    public int $block = 1024;
    /**
     * Compression and Uncompression chunk size
     *
     * @param Int $compress_chunk_size
     */
    public int $compress_chunk_size = 100 * 1024 * 1024; // 100 MB
    /**
     * Encryption cipher
     *
     * @default aes-256-cbc
     */
    public string $cipher = 'aes-256-cbc';

```

# Changelog

All notable changes to this project will be documented here.

## [3.0.0] - 2023-06-11

- 🌟 Added Pangea's File Intel Service to get file reputations and only allow secure content for compressing
- 🌟 Updated compressor, uncompressor and file handlers to handle large file without consuming huge memory
- 🌟 Fixed Bugs and updated the documentation page


## [2.0.0] - 2022-03-12

- 🌟 Added AES 256 CBC Mode encryption


# Future Update
- Store files on centralize and decentralize cloud providers

# Support

If you love my project and wish to assist me to keep working on this project. Please follow this link <a href="https://flutterwave.com/donate/oq61dyrjk9xh">https://flutterwave.com/donate/oq61dyrjk9xh</a> to donate.

# License

Released under the MIT license.

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fmitmelon%2FCompress.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fmitmelon%2FCompress?ref=badge_large)
