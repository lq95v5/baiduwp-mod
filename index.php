<?php

/**
 * PanDownload 网页复刻版，PHP 语言版主文件
 *
 * 功能描述：使用百度 SVIP 账号获取真实下载地址，与 Pandownload 原版无关。
 *
 * 希望在使用时能够保留导航栏的 Github 感谢！
 *
 * 此项目 GitHub 地址：https://github.com/yuantuo666/baiduwp-php
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
$programVersion_Index = "2.2.7";
session_start();
define('init', true);
if (!file_exists('./common/invalidCheck.php')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关文件！无法正常运行程序！\r\n请重新 Clone 项目并进入此页面安装！\r\n将在五秒内跳转到 GitHub 储存库！");
}
require('./common/invalidCheck.php');
require('config.php');
if ($programVersion_Index !== programVersion) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=install.php');
	die("HTTP 503 服务不可用！\r\n配置文件版本异常！\r\n将在五秒内跳转到安装程序！\r\n若重新安装无法解决问题，请重新 Clone 项目并配置！");
}
// 确认会员账号模式是否正常
if (USING_DB == false and SVIPSwitchMod != 0) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n配置错误，未启用数据库无法使用其他会员模式！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 保存启动时间
$system_start_time = microtime(true);
// 导入配置和函数
require('./common/language.php');
require('./common/functions.php');

// 确认账号 Cookie 设置正确
$BDUSS = getSubstr(Cookie, 'BDUSS=', ';');
$STOKEN = getSubstr(Cookie, 'STOKEN=', ';');
$csrfToken = getSubstr(Cookie, 'csrfToken=', ';');
$BAIDUID = getSubstr(Cookie, 'BAIDUID=', ';');
if (!$BDUSS or !$STOKEN or !$csrfToken or !$BAIDUID) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=./install.php');
	die("HTTP 503 服务不可用！\r\n配置错误，使用本项目必须配置**完整**的普通账号 Cookie！\r\n请重安装此项目！\r\n将在五秒内跳转到安装界面！");
}

// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
// 隐藏错误代码，保护信息安全
if (DEBUG) {
	error_reporting(E_ALL);
} else {
	error_reporting(0); // 关闭错误报告
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="referrer" content="same-origin" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="author" content="LC" />
	<meta name="version" content="<?php echo programVersion; ?>" />
	<meta name="description" content="PanDownload 网页版，百度网盘分享链接在线解析工具。" />
	<meta name="keywords" content="PanDownload,百度网盘,分享链接,下载,不限速" />
	<title><?php echo Sitename; ?></title>
	<link rel="icon" href="favicon.ico" />
	<link rel="stylesheet" href="static/index.css?v=<?php echo programVersion; ?>" />
	<link rel="stylesheet" href="js/all.min.css" />
	<link rel="stylesheet" id="ColorMode-Light" href="js/bootstrap.min.css" />
	<link rel="stylesheet" id="ColorMode-Dark" href="https://fastly.jsdelivr.net/gh/vinorodrigues/bootstrap-dark@0.0.9/dist/bootstrap-dark.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Dark" href="https://fastly.jsdelivr.net/npm/@sweetalert2/theme-dark@4.0.2/dark.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Light" href="https://fastly.jsdelivr.net/npm/@sweetalert2/theme-default@4.0.2/default.min.css" />
	<script src="js/jquery.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="https://fastly.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.min.js"></script>
	<script src="https://fastly.jsdelivr.net/npm/@keeex/qrcodejs-kx"></script>
	<script src="http://filecxx.com/script/create_filec_address.js"></script>
	<script src="static/color.js?v=<?php echo programVersion; ?>"></script>
	<script src="static/functions.js?v=<?php echo programVersion; ?>"></script>
	<script defer src="static/ready.js?v=<?php echo programVersion; ?>"></script>
	<script>
		var USING_DB = <?php echo USING_DB ? "true" : "false"; ?>;
		var IsConfirmDownload = <?php echo IsConfirmDownload ? "true" : "false"; ?>;

		function confirmdl(fs_id, timestamp, sign, randsk, share_id, uk) {
			if (!USING_DB || !IsConfirmDownload) {
				dl(fs_id, timestamp, sign, randsk, share_id, uk)
				return
			}

			Swal.fire({
				title: "<?php echo Language["ConfirmTitle"] ?>",
				html: "<?php echo Language["ConfirmText"] ?>",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "<?php echo Language["ConfirmmButtonText"] ?>",
				reverseButtons: true
			}).then(function(e) {
				if (e.isConfirmed) {
					dl(fs_id, timestamp, sign, randsk, share_id, uk);
				}
			});
		}
	</script>
</head>

<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="./"><img src="resource/logo.png" class="img-fluid rounded logo-img mr-2" alt="LOGO" />PanDownload</a>
			<button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collpase-bar"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="collpase-bar">
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="./"><?php echo Language["IndexButton"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="?help" target="_blank"><?php echo Language["HelpButton"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="?usersettings"><?php echo Language["UserSettings"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="https://github.com/yuantuo666/baiduwp-php" target="_blank">Github</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container main">
		<?php
		if (DEBUG) {
			echo '<script>console.log("$_GET",' . json_encode($_GET) . ')</script>';
			echo '<script>console.log("$_POST",' . json_encode($_POST) . ')</script>';
		}
		if (isset($_GET["help"])) echo Language["HelpPage"]; // 帮助页
		elseif (isset($_GET["usersettings"])) require("./common/usersettings.php"); // 用户设置页面
		elseif (isset($_POST["surl"])) require("./common/list.php"); // 解析链接页面
		elseif (isset($_GET["download"])) require("./common/download.php");  // 解析下载地址页面
		else require("./common/index.php"); // 首页

		echo Footer; ?>
	</div>

	<?php
	$system_end_time = microtime(true);
	$system_runningtime = $system_end_time - $system_start_time;
	echo "<script>console.log('后端计算时间 $system_runningtime 秒');</script>";
	?>
</body>

</html>
