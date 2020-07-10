
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (Auth::guard('restaurant')->check()) 
    <meta name="restaurant-id" content="{{ Auth::guard('restaurant')->user()->id }}" />
  @endif
  
  <title>Qupaid | Restaurant</title>

  <!-- Favicon Icon -->
  <link rel="icon" type="image/gif" href="uploads/application/favicon.png" sizes="16x16">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- overlayScrollbars -->
  {{-- <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}"> --}}
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <!-- Toastr style -->
  <link href="{{ asset('plugins/toastr/css/toastr.css') }}" rel="stylesheet"/>
  <!-- jQuery -->
  <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
  <!-- google location api -->
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6P9KPoKUYR8lsXjeLWEA5Zd39ORPvoVY&libraries=places"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#">
            <i class="fas fa-bars"></i>
          </a>
        </li>
      </ul>

      <!-- Brand Logo -->
      <a class="navbar-brand ml-1" onclick="showHomeComponent()">
        <img 
            src="{{ asset('uploads/application/logo.png') }}" 
            alt="Qupaid Logo" 
            class="brand-image img-circle elevation-3" 
            width="30" height="30"  
            style="opacity: .8"
        >
        <span class="font-weight-light">
          Qupaid
        </span>
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

        {{-- 
          <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
              <i class="fa fa-th-large fa-2x"></i>
            </a>
          </li> 
        --}}

        <li class="nav-item navbar-custom-menu">
          <ul class="navbar-nav ml-auto mt-1">
            
            <li class="nav-item dropdown user-menu float-right">
              <a href="#" class="nav-link" data-toggle="dropdown" aria-expanded="false">
                
                <img src="{{ asset(\Auth::guard('restaurant')->user()->profile_picture ?? 'designToBeChangedHere') }}" class="user-image profile-user-img img-fluid" alt="User Image">
                
              </a>
              <ul class="ml-auto dropdown-menu">
                <!-- User image -->
                <li class="user-header bg-success">
                  <img src="{{ asset(\Auth::guard('restaurant')->user()->profile_picture ?? 'designToBeChangedHere') }}" class="img-circle" alt="User Image">

                  <p>
                    {{ \Auth::guard('restaurant')->user()->user_name }}
                    <small>Restaurant Owner</small>
                  </p>
                </li>

                <!-- Menu Footer-->
                <li class="user-footer row">
                  <div class="col-4" onclick="showProfileComponent()">
                    <a class="btn btn-default btn-flat">Profile</a>
                  </div>

                  <div class="col-4" onclick="showSettingComponent()">
                    <a class="btn btn-default btn-flat">Setting</a>
                  </div>

                  <div class="col-4">
                    <a class="btn btn-default btn-flat" href="{{ route('resto.logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        {{ __('logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('resto.logout') }}" method="POST" style="display: none;">
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
    
    toastr.options = {
      "timeOut": "2000"
    } 
  
  });

</script>

<!-- REQUIRED SCRIPTS -->

<!-- Toastr -->
<script src="{{ asset('plugins/toastr/js/toastr.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
{{-- 
  <!-- overlayScrollbars -->
  <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script> 
--}}
<!-- AdminLTE Template -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- PAGE SCRIPTS -->
<script src="{{ mix('js/restaurant.js') }}"></script>
<!-- ChartJS -->
{{-- <script src="{{ asset('plugins/chart.js/js/Chart.min.js') }}"></script> --}}
<!-- OPTIONAL SCRIPTS -->
{{-- <script src="{{ asset('dist/js/demo.js') }}"></script> --}}
<!-- jQuery Mapael -->
{{-- <script src="{{ asset('plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/maps/usa_states.min.js') }}"></script> --}}

</body>
</html>
