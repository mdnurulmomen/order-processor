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
                :class="[['orders', 'rider-orders', 'agent-orders'].includes(currentRoute) ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'agent-orders' }" 
                    :class="[currentRoute=='agent-orders' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Agent-Orders</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['meals', 'cuisines', 'addons', 'variations', 'product-categories'].includes(currentRoute) ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'variations' }" 
                    :class="[currentRoute=='variations' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Variations</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'product-categories' }"  
                    :class="[currentRoute=='product-categories' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Product Categories</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview">
              <a 
                href="#" 
                :class="[['merchants', 'merchant-deals', 'merchant-meals', 'merchant-admins', 'agents', 'merchant-cuisines', 'kitchens', 'merchant-products', 'merchant-all-products', 'merchant-product-categories', 'merchant-all-product-categories'].includes(currentRoute) ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-hotel"></i>
                <p>
                  Merchants
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'merchants' }" 
                    :class="[currentRoute=='merchants' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Merchants</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'merchant-deals' }" 
                    :class="[currentRoute=='merchant-deals' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Deals</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'merchant-meals' }" 
                    :class="[currentRoute=='merchant-meals' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Meals</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'merchant-admins' }" 
                    :class="[currentRoute=='merchant-admins' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Owners</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'agents' }"  
                    :class="[currentRoute=='agents' ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Agents</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'merchant-cuisines' }" 
                    :class="[currentRoute=='merchant-cuisines' ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'merchant-products' }"  
                    :class="[['merchant-products', 'merchant-all-products'].includes(currentRoute) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Products</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'merchant-product-categories' }"  
                    :class="[['merchant-product-categories', 'merchant-all-product-categories'].includes(currentRoute) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Product Categories</p>
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
                  <a href="pages/examples/legacy-user-product.html" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Legacy User product</p>
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