
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>Qupaid | Admin</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <!-- Toastr style -->
  <link href="{{ asset('css/toastr.css') }}" rel="stylesheet"/>
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- PAGE PLUGINS -->
  <!-- jQuery -->
  <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
      </ul>

      <!-- Brand Logo -->
      <a href="{{ route('admin.home') }}" class="navbar-brand ml-1">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             width="30" height="30"  style="opacity: .8">
        <span class="font-weight-light">Qupaid</span>
      </a>

      <!-- SEARCH FORM -->
      <!-- 
      <form class="form-inline float-right">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form> 
      -->
      
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">

        <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
            <i class="fa fa-th-large fa-2x"></i>
          </a>
        </li>

        <li class="nav-item navbar-custom-menu">
          <ul class="navbar-nav ml-auto mt-1">
            
            <li class="nav-item dropdown user-menu float-right">
              <a href="#" class="nav-link" data-toggle="dropdown" aria-expanded="false">
                
                <img src="{{ asset(\Auth::guard('admin')->user()->profile_picture) }}" class="user-image profile-user-img img-fluid" alt="User Image">
                
              </a>
              <ul class="ml-auto dropdown-menu">
                <!-- User image -->
                <li class="user-header bg-success">
                  <img src="{{ asset(\Auth::guard('admin')->user()->profile_picture) }}" class="img-circle" alt="User Image">

                  <p>
                    {{ Auth::guard('admin')->user()->full_name }}
                    <small>Role Name</small>
                  </p>
                </li>

                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="float-left">
                    <a href="/profile" class="btn btn-default">Profile</a>
                  </div>

                  <div class="float-right">
                    <a class="btn btn-default" href="{{ route('admin.logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        {{ __('Sign out') }}
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                  </div>
                </li>

              </ul>
            </li>
            <!-- Control Sidebar Toggle Button -->
            
          </ul>
        </li>

      </ul>

    </nav>
    <!-- /.navbar -->

    <div id="app">
      
    </div>

  </div>
<!-- ./wrapper -->

<script type="text/javascript">
  
  $(document).ready(function() {
    
    @if ($errors->any())
      @foreach ($errors->all() as $error)
        toastr.error("{{ $error }}", "Oops");
      @endforeach

    @elseif(session('success'))
      toastr.success("{{ session('success') }}", "Success");

    @elseif(session('info'))
      toastr.info("{{ session('info') }}", "Hints");

    @elseif(session('warning'))
      toastr.warning("{{ session('warning') }}", "Warning");

    @endif
    
    toastr.options = {
       "timeOut": "2000"
    } 
  
  });

</script>

<!-- PAGE SCRIPTS -->
<script src="{{ asset('js/app.js') }}"></script>
<!-- REQUIRED SCRIPTS -->
<!-- Toastr -->
<script src="{{ asset('js/toastr.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- OPTIONAL SCRIPTS -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
<!-- jQuery Mapael -->
<script src="{{ asset('plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

</body>
</html>