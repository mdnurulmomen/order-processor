
<template> 
    
  <div>

    <!-- Navbar for back/forward button -->
    <nav 
      class="main-header navbar navbar-expand position-relative p-3" 
      style="top: 55px;z: 0"
    >
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <button 
            type="button" 
            class="btn btn-default btn-sm" 
            v-on:click="$router.go(-1);" 
            title="Go Back" 
          >
            <i class="fa fa-arrow-left"></i>
          </button>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <button 
            type="button" 
            class="btn btn-default btn-sm" 
            v-on:click="$router.go(1);" 
            title="Go Next" 
          >
            <i class="fa fa-arrow-right"></i>
          </button>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-item has-treeview menu-open">
              <a href="#" :class="[['home', 'dashboard2', 'dashboard3'].includes(currentRoute) ? 'active' : '', 'nav-link']">
                <i class="nav-icon fas fa-home"></i>
                <p>
                  Dashboard
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'home' }"  
                    :class="[currentRoute=='home' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Home </p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'dashboard2' }"  
                    :class="[currentRoute=='dashboard2' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Dashboard 2</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'dashboard3' }"  
                    :class="[currentRoute=='dashboard3' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Dashboard 3</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['orders', 'rider-orders', 'waiter-orders'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fab fa-first-order"></i>
                <p>
                  Orders
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'orders' }" 
                    :class="[currentRoute=='orders' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'rider-orders' }" 
                    :class="[currentRoute=='rider-orders' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Rider-Orders</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'waiter-orders' }" 
                    :class="[currentRoute=='waiter-orders' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Waiter-Orders</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['meals', 'cuisines', 'addons', 'item-variations', 'menu-categories'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-utensils"></i>
                <p>
                  Assets
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'meals' }"    
                    :class="[currentRoute=='meals' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Meals</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'cuisines' }"   
                    :class="[currentRoute=='cuisines' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cuisines</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'addons' }"    
                    :class="[currentRoute=='addons' ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Addons</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'item-variations' }" 
                    :class="[currentRoute=='item-variations' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Item Variations</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'menu-categories' }"  
                    :class="[currentRoute=='menu-categories' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Menu Categories</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['restaurants', 'restaurant-deals', 'restaurant-meals', 'restaurant-admins', 'waiters', 'restaurant-cuisines', 'kitchens', 'expected-restaurant-menu-items', 'restaurant-menu-items', 'restaurant-menu-categories'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-hotel"></i>
                <p>
                  Restaurants
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'restaurants' }" 
                    :class="[currentRoute=='restaurants' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurants</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'restaurant-deals' }" 
                    :class="[currentRoute=='restaurant-deals' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Deals</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'restaurant-meals' }" 
                    :class="[currentRoute=='restaurant-meals' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Meals</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'restaurant-admins' }" 
                    :class="[currentRoute=='restaurant-admins' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Owners</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'waiters' }"  
                    :class="[currentRoute=='waiters' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Waiters</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'restaurant-cuisines' }" 
                    :class="[currentRoute=='restaurant-cuisines' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cuisines</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'kitchens' }"  
                    :class="[currentRoute=='kitchens' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kitchens</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'expected-restaurant-menu-items' }"  
                    :class="[['expected-restaurant-menu-items', 'restaurant-menu-items'].includes(currentRoute) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Menu-Items</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'restaurant-menu-categories' }"  
                    :class="[['restaurant-menu-categories', 'restaurant-menu-category-details'].includes(currentRoute) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Menu Categories</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['delivery-men'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-bicycle"></i>
                <p>
                  Delivery-Men
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'delivery-men' }" 
                    :class="[currentRoute=='delivery-men' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Delivery-Men</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['notifications'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-bell"></i>
                <p>
                  Notifications
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'notifications' }" 
                    :class="[currentRoute=='notifications' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Notifications</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['cancelation-reasons'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-times"></i>
                <p>
                  Cancelations
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'cancelation-reasons' }" 
                    :class="[currentRoute=='cancelation-reasons' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Reasons</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['coupons'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-percent"></i>
                <p>
                  Coupons
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
              <!-- 
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'discounts' }" 
                    :class="[currentRoute=='discounts' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Rates</p>
                  </router-link>
                </li>
              -->

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'coupons' }" 
                    :class="[currentRoute=='coupons' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Coupons</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['api-list'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-key"></i>
                <p>
                  API
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'api-list' }" 
                    :class="[currentRoute=='api-list' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>API List</p>
                  </router-link>
                </li>
              </ul>
            </li>

        <!-- 
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-copy"></i>
                <p>
                  Layout Options
                  <i class="fas fa-angle-left right"></i>
                  <span class="badge badge-info right">6</span>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="pages/layout/top-nav.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Top Navigation</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/layout/top-nav-sidebar.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Top Navigation + Sidebar</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/layout/boxed.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Boxed</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/layout/fixed-sidebar.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Fixed Sidebar</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/layout/fixed-topnav.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Fixed Navbar</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/layout/fixed-footer.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Fixed Footer</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/layout/collapsed-sidebar.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Collapsed Sidebar</p>
                  </a>
                </li>
              </ul>
            </li>
          
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon far fa-plus-square"></i>
                <p>
                  Extras
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="pages/examples/login.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Login</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/register.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Register</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/forgot-password.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Forgot Password</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/recover-password.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Recover Password</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/lockscreen.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lockscreen</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/legacy-user-menu.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Legacy User Menu</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/language-menu.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Language Menu</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/404.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Error 404</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/500.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Error 500</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/pace.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Pace</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="pages/examples/blank.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Blank Page</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="starter.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Starter Page</p>
                  </a>
                </li>
              </ul>
            </li>
        -->
            
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
          
        <!-- 
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ currentRoute }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v2</li>
            </ol>
          </div> 
        </div>  
        -->
         
        <router-view></router-view>

    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <!-- <aside class="control-sidebar control-sidebar-dark"> -->
      <!-- Control sidebar content goes here -->
    <!-- </aside> -->
    <!-- /.control-sidebar -->
  </div>
  
</template>

<script type="text/javascript">
  
  export default {
    computed:{
      currentRoute(){
        return this.$route.name;
      } 
    },
  }

</script>