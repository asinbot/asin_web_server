<?php
$fileLock = 'install.lock';
if (file_exists($fileLock)) {
    include_once 'upgrade.php';
    die;
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <title>PHP程序数据库安装程序</title>
    </head>
    <body>
    <center>
        <h2>PHP程序数据库在线安装程序</h2>
        <hr/>
        <form action="" method="post">
            <table>
                <tr>
                    <td>数据库地址：</td>
                    <td><input type="text" name="host" value="127.0.0.1" /></td>
                </tr>
                <tr>
                    <td>数据库端口：</td>
                    <td><input type="number" name="port" value="3306" /></td>
                </tr>
                <tr>
                    <td>数据库账号：</td>
                    <td><input type="text" name="username" value="root" /></td>
                </tr>
                <tr>
                    <td>数据库密码：</td>
                    <td><input type="password" name="password" /></td>
                </tr>
                <tr>
                    <td>数据库编码：</td>
                    <td><input type="text" name="charset" value="utf8mb4" /></td>
                </tr>
                <tr>
                    <td>数据库名：</td>
                    <td><input type="text" name="dbname" /></td>
                </tr>
                <tr>
                    <td>数据库表前缀：</td>
                    <td><input type="text" name="prefix" /></td>
                </tr>

                <tr>
                    <td colspan="2" style="text-align:center;">
                        <input type="submit" value="安装"/>
                        <input type="reset" value="重置"/>
                    </td>
                </tr>
            </table>
        </form>
    </center>
    </body>
    </html>

    <?php
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filename = __DIR__ . '/../config/global.php';
    $sql_file = 'install.sql';

//配置文件内容
    $example_file_path = __DIR__ .'/../config/global.php.example';
    if(!file_exists($example_file_path)){
        echo "example文件不存在。";
    }
    $conf_arr = [
        '{{dbname}}' => $_POST['dbname'],
        '{{host}}' => $_POST['host'],
        '{{username}}' => $_POST['username'],
        '{{password}}' => $_POST['password'],
        '{{charset}}'  => $_POST['charset'],
        '{{port}}'     => $_POST['port'],
        '{{prefix}}'   => $_POST['prefix'],
    ];
    $config = file_get_contents($example_file_path);//将整个文件内容读入到一个字符串中
    foreach ($conf_arr as $k => $v){
        $config = str_replace($k,$v,$config);
    }


//    if (is_writable($filename)) {//检测是否有权限可写
        $handle = fopen($filename, "w+");
        fwrite($handle, $config);

        // //连接数据库
        $config = include_once($filename);
        $db = $config['db'];
        if (!@$link = mysqli_connect($db['host'], $db['user'], $db['pwd'])) {
            echo "数据库连接失败,<a href=\"javascript:void(0)\" onclick=\"location.reload()\">返回设置</a>";
        } else {
            mysqli_query($link, 'create database if not exists `' . $db["base"] . '`');
            mysqli_select_db($link, $db["base"]);

            //读取建表语句
            $handle = fopen($sql_file, "r");
            $sql = fread($handle, filesize($sql_file));
            fclose($handle);

            $a = explode(";", trim($sql));
            $ret = false;
            foreach ($a as $b) {
                if (!(bool)$b) continue;
                $c = $b . ";";
                $ret = mysqli_query($link, $c);
                if (!$ret) {
                    include_once 'fail.php';
                    exit();
                }
            }

            if (!$ret) {
                include_once 'fail.php';
            } else {
                include_once 'ok.php';
                fopen($fileLock, "w");
            }
        }
//
//
//    } else {
//        echo "没有读写权限。";
//    }
}

?>

