<?php
namespace app\manage\controller;

class Hint{
    
    public function ie9(){
        return <<<EOT
<html>
	<head>
        <meta charset="UTF-8">
        <meta name="renderer" content="webkit">
		<link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
		<style>
			body {
				margin: 0;
				padding: 0;
				width: 100%;
				height: 100%;
				color: #B0BEC5;
				display: table;
				font-weight: 100;
				font-family: 'Lato';
			}
			.container {
				text-align: center;
				display: table-cell;
				vertical-align: middle;
			}
			.content {
				text-align: center;
				display: inline-block;
			}
			.title {
				font-size: 42px;
				margin-bottom: 40px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="content">
				<div class="title">ERROR 很抱歉，您查看的页面不用用IE9内核浏览器打开！</div>
			</div>
		</div>
	</body>
</html>


EOT;
    }
}