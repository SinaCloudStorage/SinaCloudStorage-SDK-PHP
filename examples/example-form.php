<?php
/**
* $Id$
*
* SCS form upload example
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
$path = 'myfiles/'; // Can be empty ''

$lifetime = 3600; // Period for which the parameters are valid
$maxFileSize = (1024 * 1024 * 50); // 50 MB

$metaHeaders = array('uid' => 123);
$requestHeaders = array(
    'Content-Type' => 'application/octet-stream',
    'Content-Disposition' => 'attachment; filename=${filename}'
);

$params = SCS::getHttpUploadPostParams(
    $bucket,
    $path,
    SCS::ACL_PUBLIC_READ,
    $lifetime,
    $maxFileSize,
    201, // Or a URL to redirect to on success
    $metaHeaders,
    $requestHeaders,
    false // False since we're not using flash
);

$uploadURL = 'http://' . $bucket . '.sinacloud.net/';

?><!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<title>SCS Form Upload</title>
</head>
<body>
    <form method="post" action="<?php echo $uploadURL; ?>" enctype="multipart/form-data">
<?php
    foreach ($params as $p => $v)
        echo "        <input type=\"hidden\" name=\"{$p}\" value=\"{$v}\" />\n";
?>
        <input type="file" name="file" />&#160;<input type="submit" value="Upload" />
    </form>
</body>
</html>
