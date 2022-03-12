# Compress
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fmitmelon%2FCompress.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fmitmelon%2FCompress?ref=badge_shield)

 Advanced file compresser which reduces and encrypts large files to smaller chunks by saving them as binary for later use. Many applications supports file uploads which consumes large memory sizes on their systems. This library has been designed to reduce both zip, rar, images, pdf, words sizes or any kinds of files or documents into smaller pieces.

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
 * @param String $filePath
 * File location to be compressed
 * @param String $storePath
 * Path to output compressed binary file to
 * @param Array optional $options ["removeMeta" => false, "encrypt" => false, "key" => "password"]
 * Options to remove meta and encrypt file
 */
$compress::compressFile($filePath, $storePath, $options = []);

//Compress Image file
$compress::compressFile(__DIR__.'/image.png', __DIR__.'/image.txt'));

// Compress PDF
$compress::compressFile(__DIR__.'/file.pdf', __DIR__.'/file.txt', true));
//Compress as lot of files you want including zip files

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


# Changelog

All notable changes to this project will be documented here.

## [2.0.0] - 2022-03-12

We're super excited to announce `encryption` version of compress

When you upgrade, consider updating compressFile() parameter. The third argument is now array

### New features

  - 🌟 Added AES 256 CBC Mode encryption

# Future Update

* Store files on centralize and decentralize cloud providers
* Add key vaults to safely store your file keys
* Add file signing digest key using SHA-3 Algorithmn
* Add asymmetric file encryption protocol with Diffie Helman Key Exchange

# Support

If you love my project and wish to assist me to keep working on this project. Below is my Wallet Address.

BTC Wallet : 14PAtDFHTwH6SWdhGXRHGWDWYWDgVfHr6R
ETH Wallet : 0x8c26549052667A0b77327505D2Ea1fe4c207630e

If you donated, please send me a mail at manomitehq@gmail.com with your name so I can list you here among my supporters
I will really appreciate your donations. Thanks for using Compress.


# License

Released under the MIT license.

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fmitmelon%2FCompress.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fmitmelon%2FCompress?ref=badge_large)