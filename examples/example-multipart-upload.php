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


$object = "path/to/my/Sublime Text 2.0.2.dmg";
$file = "/Users/caoli/Downloads/Sublime Text 2.0.2.dmg";


$fp = fopen($file, 'rb');

SCS::setExceptions(true);

try
{
	//初始化上传
	$info = SCS::initiateMultipartUpload($bucket, $object, SCS::ACL_PUBLIC_READ);
	
	$uploadId = $info['upload_id'];
	
	$fp = fopen($file, 'rb');
	
	$i = 1;
	
	$part_info = array();
	
	
	while (!feof($fp)) {
		
		//上传分片	
		$res = SCS::putObject(SCS::inputResourceMultipart($fp, 1024*512, $uploadId, $i), $bucket, $object);
		
		if (isset($res['hash']))
		{	
			echo 'Part: ' . $i . " OK! \n";
			
			$part_info[] = array(
				
				'PartNumber' => $i,
				'ETag' => $res['hash'],
			);
		}
		
		$i++;
	}
	
	//列分片
	$parts = SCS::listParts($bucket, $object, $uploadId);
	
	//print_r($parts);
	//print_r($part_info);
	
	if (count($parts) > 0 && count($parts) == count($part_info)) {
		
		foreach ($parts as $part_number => $part) {
			
			//echo $part['etag'] . "\n";
			//echo $part_info[$k]['ETag'] . "\n";
			
			if ($part['etag'] != $part_info[$part_number-1]['ETag']) {
				
				exit('分片不匹配');
				break;
			}
		}
		
		//合并分片
		echo "开始合并\n";
		
		SCS::completeMultipartUpload($bucket, $object, $uploadId, $part_info);
		
		echo "上传完成\n";
		
		fclose($fp);
		
	}
	
}
catch(SCSException $e)
{
    echo $e->getMessage();
}






