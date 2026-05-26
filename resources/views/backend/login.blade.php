<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Login | CRYPTOYAAR - CRYPTO SEEKHO</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Crypto - Cryptoyaar" name="description" />
        <meta content="Crypto - Cryptoyaar" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('/assets/admin/images/favicon.ico')}}">

        <!-- Bootstrap Css -->
        <link href="{{asset('/assets/admin/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('/assets/admin/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('/assets/admin/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />

    </head>
    <style>
        *{
            overflow: hidden;
        }
    </style>

    <body data-sidebar="dark">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->
        <div class="account-pages min-vh-100 d-flex flex-column justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10 text-center">

                        <!-- Logo Section -->
                        <div class="mb-4">
                            <a href="#" class="auth-logo d-inline-block mb-2">
                                <img src="{{ asset('/assets/img/cryptoyaar.png') }}" alt="CRYPTO Logo" height="40">
                            </a>
                            <p class="font-size-15 text-muted mb-0">
                                <strong>CRYPTOYAAR</strong> Admin Dashboard
                            </p>
                        </div>

                        <!-- Login Card -->
                        <div class="d-flex justify-content-center">
                            <div class="card shadow-lg border-0 rounded-4" style="width: 400px;">
                                <div class="card-body p-4 text-start"> <!-- text-start ensures left alignment -->
                                    <h5 class="mb-2 text-center">Welcome Back!</h5>
                                    <p class="text-muted mb-4 text-center">Sign in to continue to Crypto Admin Panel.</p>

                                    <form action="{{ route('admin.login.submit') }}" method="post">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="fw-semibold">Username</label>
                                            <input type="text" class="form-control" name="email" id="email" placeholder="Enter username">
                                        </div>

                                        <div class="mb-3 mb-4">
                                            <label for="userpassword" class="fw-semibold">Password</label>
                                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
                                        </div>

                                        <div class="mb-3">
                                            <label class="fw-semibold">Captcha</label><br>
                                            <img src="{{ route('captcha.generate') }}" id="captcha-img" alt="Captcha Image">
                                            <button type="button" class="btn btn-secondary ml-2" onclick="refreshCaptcha()">
                                                <i class="fas fa-sync"></i> 
                                            </button>  
                                            <input type="text" name="captcha" class="form-control" placeholder="Captcha" required>   
                                            @if ($errors->has('captcha'))
                                                <div class="alert alert-danger">
                                                    {{ $errors->first('captcha') }}
                                                </div>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 fw-semibold">Log In</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 text-center">
                            <p class="text-muted mb-0">
                                © <script>document.write(new Date().getFullYear())</script>
                                <strong>CRYPTO</strong> - <i class="mdi mdi-heart text-danger"></i>
                                CRYPTO
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- end account page -->
        

        <!-- JAVASCRIPT -->
        <script src="{{asset('/assets/admin/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('/assets/admin/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('/assets/admin/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('/assets/admin/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('/assets/admin/libs/node-waves/waves.min.js')}}"></script>

        <script src="{{asset('/assets/admin/js/app.js')}}"></script>

        <script>
			function refreshCaptcha() {
				document.getElementById('captcha-img').src = "{{ route('captcha.generate') }}?rand=" + Math.random();
			}
		</script>


    </body>
</html>
