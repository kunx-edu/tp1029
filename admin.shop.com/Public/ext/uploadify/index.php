<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>UploadiFive Test</title>
<script src="jquery.min.js" type="text/javascript"></script>
<script src="jquery.uploadify.min.js" type="text/javascript"></script>

<style type="text/css">
body {
	font: 13px Arial, Helvetica, Sans-serif;
}

/* = uploadify上传插件样式
------------------------------------------ */
.uploadify-button {
	position: relative;
	text-align: center;
	color: #fff;
	cursor: pointer;
	background-color: #27ae60;
}
.uploadify-queue-item {
	position: absolute;
	margin-top: 4px;
	padding: 15px;
	width: 470px;
	border: 1px solid #ccc;
	background-color: #fff;
}
.uploadify-queue-item .cancel {
	float: right;
}
.uploadify-queue-item .cancel a,
.uploadify-queue-item .cancel a:hover {
	font-family: Consolas;
	color: #404040;
	text-decoration: none;
	border-bottom: 0 none;
}
.uploadify-queue-item .fileName {
	color: #2D7200;
}
.uploadify-error {
	background-color: #FDE5DD !important;
}
.uploadify-queue-item.completed {
	background-color: #E5E5E5;
}
.uploadify-progress {
	background-color: #E5E5E5;
	margin-top: 10px;
	width: 100%;
}
.uploadify-progress-bar {
	background-color: #0099FF;
	height: 3px;
	width: 1px;
}
.upload-img-box {
	margin-top: 4px;
}
.upload-img-box .upload-pre-item {
	padding: 1px;
	width: 120px;
	max-height: 120px;
	overflow: hidden;
	text-align: center;
	cursor: pointer;
	border: 1px solid #ccc;
	transition: all .3s linear;
}
.upload-img-box .upload-pre-item img {
	vertical-align: top;
}
.upload-img-box .upload-pre-file {
	padding: 0 10px;
	width: 380px;
	height: 35px;
	line-height: 35px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	border: 1px dashed #ccc;
	background-color: #fff;
}
/* 上传图片点击弹出层 */
.upload-img-popup {
	position: fixed;
	z-index: 9999;
	padding: 3px;
	border: 1px solid #c3c3c3;
	background-color: #fff;
	box-shadow: 0 0 4px rgba(0,0,0,.5);
}
.upload-img-popup .close-pop {
	position: absolute;
	top: -8px;
	right: -8px;
	width: 17px;
	height: 17px;
	background: url(../images/bg_icon.png) no-repeat -25px 0;
}
.upload-img-popup .close-pop:hover {
	text-decoration: none;
	border-bottom: 0 none;
}
.upload-img-popup img {
	display: block;
}
.upload_icon_all {
	width: 15px;
	height: 15px;
	background: url(../images/attachment_1.png);
	display: inline-block;
	vertical-align: middle;
	margin-right: 5px
}
</style>
</head>

<body>
	<h1>Uploadify Demo</h1>
	<form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file" multiple="true">
	</form>

	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadify({
				'formData'     : {
					'timestamp' : '<?php echo $timestamp;?>',
					'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
				},
				'swf'      : 'uploadify.swf',
				'uploader' : 'uploadify.php',
					'fileObjName':'kunx_test',
			});
		});
	</script>
</body>
</html>