#!/usr/local/bin/php
<?php
/**
* $Id$
*
* 大文件分片上传示例
*/

//you can ignore it
@include_once('config.php');

if (!class_exists('SCS')) require_once '../class/SCS.php';

date_default_timezone_set('UTC');

// SCS access info
if (!defined('AccessKey')) define('AccessKey', 'change-this');
if (!defined('SecretKey')) define('SecretKey', 'change-this');
if (!defined('BucketName')) define('BucketName', 'change-this');

// Check for CURL
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
	exit("\nERROR: CURL extension not loaded\n\n");

// Pointless without your keys!
if (AccessKey == 'change-this' || SecretKey == 'change-this')
	exit("\nERROR: SCS access information required\n\nPlease edit the following lines in this file:\n\n".
	"define('AccessKey', 'change-me');\ndefine('SecretKey', 'change-me');\n\n");
	
// Pointless without your BucketName!
if (BucketName == 'change-this')
	exit("\nERROR: BucketName required\n\nPlease edit the following lines in this file:\n\n".
	"define('BucketName', 'change-me');\n\n");


SCS::setAuth(AccessKey, SecretKey);

$bucket = BucketName;

################################################################################


$object = "path/to/my/file.txt";
$file = "/Users/caoli/Desktop/text.txt";


$fp = fopen($file, 'rb');

SCS::setExceptions(true);

try
{
	$info = SCS::initiateMultipartUpload($bucket, $object, $acl = SCS::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array());
	
	
	$fp = fopen($file, 'rb');
	
	$i = 1;
	
	while (!feof($fp)) {
		
		if (SCS::putObject(SCS::inputResourceMultipart($fp, 10, $info['upload_id'], $i), $bucket, $object))
		{
			echo 'Part: ' . $i . " OK! \n";
		}
		
		$i++;
	}
	
	$parts = SCS::listParts($bucket, $object, $info['upload_id']);
	
	print_r($parts);
}
catch(SCSException $e)
{
    echo $e->getMessage();
}






