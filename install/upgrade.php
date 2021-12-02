<?php
$argc = 4;
$fileLock = 'upgrade.lock';
if (file_exists($fileLock)) {
    include_once 'ok.php';
    die;
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <title>PHP程序数据库升级程序</title>
    </head>
    <body>
    <center>
        <h2>PHP程序数据库在线升级程序</h2>
        <hr/>
        <form action="" method="post">
            <table>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <input type="hidden" name="is_pre" value="1" />
                        <input type="submit" value="预升级"/>
                        <input type="reset" value="重置"/>
                    </td>
                </tr>
            </table>
        </form>
    </center>
    </body>
    </html>
    <?php
} elseif (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['is_pre'] == "1")) {
    $filename = __DIR__ . '/../config/global.php';
    $sql_file = 'latest.sql';

    // //连接数据库
    $config = include_once($filename);
    $db = $config['db'];
    if (!@$link = mysqli_connect($db['host'], $db['user'], $db['pwd'])) {
        echo "数据库连接失败,请检查数据库配置";
    } else {
        mysqli_query($link, 'create database if not exists `temp2021`');
        mysqli_select_db($link, 'temp2021');

        //读取建表语句
        $handle = fopen($sql_file, "r");
        $sql = fread($handle, filesize($sql_file));
        fclose($handle);

        $a = explode(";", $sql);
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
?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
                <title>PHP程序数据库升级程序</title>
            </head>
            <body>
            <center>
                <h2>PHP程序数据库在线升级程序</h2>
                <hr/>
                <form action="" method="post">
                    <table>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <textarea name="sql" id="sql" cols="100" rows="50"><?php trim(require_once __DIR__ .'/pre_upgrade.php'); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <input type="hidden" name="is_pre" value="0" />
                                <input type="submit" value="开始升级"/>
                            </td>
                        </tr>
                    </table>
                </form>
            </center>
            </body>
            </html>
<?php
        }
    }
}elseif (($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['is_pre'] == "0")) {
    $filename = __DIR__ . '/../config/global.php';
    // //连接数据库
    $config = include_once($filename);
    $db = $config['db'];
    if (!@$link = mysqli_connect($db['host'], $db['user'], $db['pwd'])) {
        echo "数据库连接失败,请检查数据库配置";
    } else {
        mysqli_query($link, "create database if not exists `{$db["base"]}`");
        mysqli_select_db($link, $db["base"]);

        $sql = trim($_POST['sql']);
        if (!$sql){
            exit('无任何sql可执行');
        }
        $sql = explode(";", $sql);
        $ret = false;
        foreach ($sql as $b) {
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
//            fopen($fileLock, "w");
        }
    }
}

?>