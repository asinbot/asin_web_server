<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>数据库安装失败</title>
</head>
<body>
<h2>o(╥﹏╥)o 数据库安装失败。</h2>
<br/>
<?php if (isset($link)) {
    echo mysqli_error($link);
} ?>
</body>
</html>