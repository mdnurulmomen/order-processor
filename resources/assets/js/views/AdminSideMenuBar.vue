
<template> 
    
  <div>

    <!-- Navbar for back/forward button -->
    <nav 
      class="main-header navbar navbar-expand position-relative" 
      style="top: 55px;z-index: 0"
    >

      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <button 
            type="button" 
            class="btn btn-default btn-sm" 
            v-on:click="$router.go(-1);" 
            title="Go back" 
          >
            <i class="fas fa-backward"></i>
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
            title="Go back" 
          >
            <i class="fas fa-forward"></i>
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
              <a href="#" v-bind:class="[(currentPage.includes('home') || currentPage.includes('dashboard')) ? 'active' : '', 'nav-link']">
                <i class="nav-icon fas fa-home"></i>
                <p>
                  Dashboard
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.home' }"  class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Dashboard v1</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.dashboard2' }"  class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Dashboard v2</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.dashboard3' }"  class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Dashboard v3</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a 
                href="#" 
                v-bind:class="[currentPage.includes('/orders') ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'admin.orders.index' }" 
                    v-bind:class="[(currentPage.includes('/orders')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orders</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a href="#" v-bind:class="[(currentPage.includes('restaurant-admins') || currentPage.includes('restaurants') || currentPage.includes('kitchens') || currentPage.includes('restaurant-deals') || currentPage.includes('restaurant-meals') || currentPage.includes('restaurant-cuisines') || currentPage.includes('/waiters') || currentPage.includes('/restaurant-menu-categories')) ? 'active' : '', 'nav-link']">
                <i class="nav-icon fas fa-hotel"></i>
                <p>
                  Restaurants
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.restaurants.index' }" v-bind:class="[(currentPage.includes('/restaurants')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurants</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.restaurantDeals.index' }" v-bind:class="[(currentPage.includes('/restaurant-deals')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurant Deals</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.restaurantMeals.index' }" v-bind:class="[(currentPage.includes('/restaurant-meals')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurant Meals</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.restaurantAdmins.index' }" v-bind:class="[(currentPage.includes('/restaurant-admins')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurant Admins</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.waiters.index' }"  v-bind:class="[(currentPage.includes('/waiters')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurant Waiters</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.restaurantCuisines.index' }" v-bind:class="[(currentPage.includes('/restaurant-cuisines')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurant Cuisines</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.kitchens.index' }"  v-bind:class="[(currentPage.includes('/kitchens')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Restaurant Kitchens</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.expectedRestaurantForMenuItem.index' }"  v-bind:class="[(currentPage.includes('/restaurant-menu-items')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Rst Menu-Items</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.restaurantMenuCategories.index' }"  v-bind:class="[(currentPage.includes('/restaurant-menu-categories')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Rst Menu-Categories</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a href="#" v-bind:class="[(currentPage.includes('/cuisines') || currentPage.includes('/menu-categories') || currentPage.includes('/meals') || currentPage.includes('/add-ons') || currentPage.includes('/item-variations')) ? 'active' : '', 'nav-link']">
                <i class="nav-icon fas fa-utensils"></i>
                <p>
                  Food Items
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.meals.index' }"    v-bind:class="[(currentPage.includes('/meals')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Meals</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.cuisines.index' }"   v-bind:class="[(currentPage.includes('/cuisines')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cuisines</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.addons.index' }"    v-bind:class="[(currentPage.includes('/add-ons')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Add-ons</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.itemVariations.index' }"  v-bind:class="[(currentPage.includes('/item-variations')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Item Variations</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'admin.menuCategories.index' }"  v-bind:class="[(currentPage.includes('/menu-categories')) ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Menu Categories</p>
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a 
                href="#" 
                v-bind:class="[(currentPage.includes('/delivery-men') || currentPage.includes('/delivery-men')) ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'admin.deliveryMen.index' }" 
                    v-bind:class="[(currentPage.includes('/delivery-men')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Riders</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'admin.riderOrders.index' }" 
                    v-bind:class="[(currentPage.includes('/rider-orders')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Rider-Orders</p>
                  </router-link>
                </li>

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'admin.waiterOrders.index' }" 
                    v-bind:class="[(currentPage.includes('/waiter-orders')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Waiter-Orders</p>
                  </router-link>
                </li>

              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a 
                href="#" 
                v-bind:class="[(currentPage.includes('/notifications') || currentPage.includes('/notifications')) ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'admin.notifications.index' }" 
                    v-bind:class="[(currentPage.includes('/notifications')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Notifications</p>
                  </router-link>
                </li>

              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a 
                href="#" 
                v-bind:class="[(currentPage.includes('/cancelation-reasons') || currentPage.includes('/cancelation-reasons')) ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'admin.cancelationReasons.index' }" 
                    v-bind:class="[(currentPage.includes('/cancelation-reasons')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Reasons</p>
                  </router-link>
                </li>

              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a 
                href="#" 
                v-bind:class="[(currentPage.includes('/discounts') || currentPage.includes('/coupons')) ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'admin.discounts.index' }" 
                    v-bind:class="[(currentPage.includes('/discounts')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Rates</p>
                  </router-link>
                </li>
              -->

                <li class="nav-item">
                  <router-link 
                    :to="{ name: 'admin.coupons.index' }" 
                    v-bind:class="[(currentPage.includes('/coupons')) ? 'active' : '', 'nav-link']"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Coupons</p>
                  </router-link>
                </li>

              </ul>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a 
                href="#" 
                v-bind:class="[(currentPage.includes('/api-list') || currentPage.includes('/api-list')) ? 'active' : '', 'nav-link']"
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
                    :to="{ name: 'admin.apiList.index' }" 
                    v-bind:class="[(currentPage.includes('/api-list')) ? 'active' : '', 'nav-link']"
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
            <h1 class="m-0 text-dark">{{ currentPage }}</h1>
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
      currentPage(){
        return this.$route.path;
      } 
    },
  }

</script>