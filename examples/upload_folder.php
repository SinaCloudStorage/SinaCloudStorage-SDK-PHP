<?php
/**
 * 批量上传一个文件夹，上传后和本地文件夹的目录结构一致
 * Created by PhpStorm.
 * User: mingming6
 * Date: 2018/5/4
 * Time: 16:45
 */

//you can ignore it
@include_once('config.php');

if (!class_exists('SCS')) require_once '../class/SCS.php';

date_default_timezone_set('UTC');

// SCS access info
if (!defined('AccessKey')) define('AccessKey', 'change-this');
if (!defined('SecretKey')) define('SecretKey', 'change-this');
if (!defined('Bucket')) define('Bucket', 'change-this');
SCS::setAuth(AccessKey, SecretKey);
SCS::setExceptions(true);

/*上传到的根目录，可以留空，留空表示上传到bucket的根目录下*/
$upload_folder = 'aaa';

/*本地的目录，可以写绝对路径*/
$local_folder = './bbb';

function get_files($dir)
{
    $files = array();

    for (; $dir->valid(); $dir->next()) {
        if ($dir->isDir() && !$dir->isDot()) {
            if ($dir->haschildren()) {
                $files = array_merge($files, get_files($dir->getChildren()));
            };
        } else if ($dir->isFile()) {
            $files[] = $dir->getPathName();
        }
    }
    return $files;
}

$dir = new RecursiveDirectoryIterator($local_folder);
$all_files = get_files($dir);
if (!$all_files) {
    echo(sprintf("Nothing to sync\n"));
    exit(0);
}
// 上传文件的前缀
if ($upload_folder) {
    $upload_folder = trim($upload_folder, '/');
}
if ($upload_folder) {
    $upload_folder = $upload_folder.'/';
}
$tmp_folder_length = strlen($local_folder);
foreach ($all_files as $f) {
    if (is_link($f)) {
        // 忽略链接文件
        continue;
    }
    $file_path_local = substr($f, $tmp_folder_length);
    $file_path_remote = $upload_folder.$file_path_local;
    $ret = SCS::putObject(SCS::inputResource(fopen($f, 'rb'), filesize($f)), Bucket, $file_path_remote, SCS::ACL_PUBLIC_READ);
    if (!$ret) {
        echo(sprintf("Upload file: %s to SinaStorage failed.", $f));
        continue;
    }
}

echo("Upload all file success\n");