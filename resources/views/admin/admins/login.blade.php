<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>后台登录 - {{ config('app.name', 'Laravel') }}</title>
    <meta name="keywords" content="后台登录">
    <meta name="description" content="后台登录">
    <link href="{{loadEdition('/admin/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/style.min.css')}}" rel="stylesheet">
    <link href="{{loadEdition('/admin/css/login.min.css')}}" rel="stylesheet">
    <script>
        if(window.top!==window.self){window.top.location=window.location};
    </script>

</head>

<body class="signin">
    <div class="signinpanel">
        <div class="row">
            <div class="col-sm-5 animated fadeInLeft">
                <div class="signin-info">
                    <div class="logopanel m-b">
                        @include('flash::message')
                        <h4><span class="label label-info label-sx">{{ config('app.name', '安抖管理平台') }}</span></h4>
                    </div>
                    <div class="m-b"></div>
                    <ul class="m-b">
                        <li><i class="fa fa-circle text-navy"></i> 优势一：</li>
                        <li><i class="fa fa-circle text-navy"></i> 优势二：</li>
                        <li><i class="fa fa-circle text-navy"></i> 优势三：</li>
                        <li><i class="fa fa-circle text-navy"></i> 优势四：</li>
                        <li><i class="fa fa-circle text-navy"></i> 优势五：</li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-7 animated fadeInRight">
                <form method="post" action="{{route('login-handle')}}">
                    {{csrf_field()}}
                    <p class="login-title">登录</p>
                    <p class="m-t-md" style="color:#666">登录到{{ env('APP_NAME') }}系统后台管理</p>
                    <input type="text" class="form-control uname" name="mobile" value="{{old('mobile')}}" required placeholder="手机号" />
                    <input type="password" class="form-control pword m-b" name="password" required placeholder="密码" />
                    <div style="width: 300px;">
                        {!! Geetest::render() !!}
                    </div>
                    <p></p>
                    <button class="btn btn-success btn-block">登录</button>
                    <p></p>
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
        <div class="signup-footer animated fadeInUp">
            &copy; 2015 All Rights Reserved. {{ config('app.name', 'Laravel') }}
        </div>
    </div>
</body>
</html>
