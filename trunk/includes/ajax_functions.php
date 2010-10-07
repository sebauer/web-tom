<?php
function jsCallback($message, $isError = 'false'){
    echo "<script>window.parent.uploadCallback(\"{$message}\", {$isError});</script>";
}

function downloadFile($filepath, $filename, $md5){
    echo "<script>window.parent.showDownloadInfo(\"{$filepath}\", \"{$filename}\", \"{$md5}\");</script>";
}

function analyzerCallback($output){
    echo "<script>window.parent.analyzerCallback(\"{$output}\");</script>";
}