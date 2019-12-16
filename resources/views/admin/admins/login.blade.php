<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8">
<title>安抖平台</title>
<meta name="description" content="particles.js is a lightweight JavaScript library for creating particles.">
<meta name="author" content="Vincent Garreau">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" href="{{loadEdition('/admin/login/css/base.css')}}">
<link rel="stylesheet" media="(min-width:750px)" href="{{loadEdition('/admin/login/css/style.css')}}">
<link rel="stylesheet" media="(max-width:750px)" href="{{loadEdition('/admin/login/css/wap.css')}}">

</head>
<body>
<div id="particles-js">
	<div class="wrapper">
		<div class="title transformX"><img src="{{loadEdition('/admin/login/images/title.png')}}" ></div>
		<div class="content transformX">
			<div class="login transformX">
				<div class="login-logo">
					<img class="transformX transformY" src="{{loadEdition('/admin/login/images/logo.png')}}" >
				</div>
				<div class="login-content">
					<form  method="post"  action="{{route('login-handle')}}">
						{{csrf_field()}}
						<h1>登录</h1>

						<input type="text" name="mobile" value="{{old('mobile')}}"  placeholder="手机号" required />
						<input type="password" name="password"  placeholder="密码" required />
						<div style="width: 36px;margin-top: 10px">
							{!! Geetest::render() !!}
						</div>
						<input type="submit" id="submit" style="display:none;">
						<a href="javascript:;" onclick="document.getElementById('submit').click()" class="btn">登录</a>
						@include('flash::message')
						@if (count($errors) > 0)
						<div class="alert alert-danger">
							<h4>登录失败：</h4>
							<ul>
								@foreach ($errors->all() as $error)
								<li> {{ $error }}</li>
								@endforeach
							</ul>
						</div>
						@endif
					</form>
				</div>
			</div>
		</div>
		<p class="transformX max">版权所有：西豆科技发展重庆有限公司</p>
		<p class="transformX min">技术支持—重庆卓松科技有限公司</p>
	</div>
	<canvas class="particles-js-canvas-el" style="width: 100%; height: 100%;" width="1094" height="969"></canvas>
</div>

<script src="{{loadEdition('/admin/login/js/particles.min.js')}}"></script>
<script src="{{loadEdition('/admin/login/js/app.js')}}"></script>

</body>
</html>