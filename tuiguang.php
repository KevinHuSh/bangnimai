<html>
<head></head>
<body></body>
<?php

$debug = 0;
if ($debug == 1)echo "ssssss";

$param = array();
foreach($_GET as $key=>$val)
{
    $param[$key] = $val;
    if ($debug == 1)echo "<br>KV=>  ". $key."==>".$val;
}

$s = stripos(substr($param['to'], 4), "http") + 4;
if ($debug == 1)
    echo urldecode(substr($param["to"], 0, $s)).substr($param["to"], $s)."<br>KKKKKKKKKKKKKKKKK";
//echo "<script>alert('".urldecode(substr($param["to"], 0, $s)).substr($param["to"], $s)."');</script>";
//echo "<script>location.href='".urldecode($param["to"])."';</script>";
//echo "<script>location.href='".urldecode(substr($param["to"], 0, $s)).substr($param["to"], $s)."';</script>";
echo "<script>var referLink = document.createElement('a');referLink.href = '".urldecode(substr($param["to"], 0, $s)).substr($param["to"], $s)."';document.body.appendChild(referLink);referLink.click(); </script>";
//echo "<script>location.href='".urldecode($param["to"])."';</script>";
exit;

?>

</html>
