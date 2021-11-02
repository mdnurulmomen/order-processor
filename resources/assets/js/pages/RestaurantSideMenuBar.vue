
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

            <li class="nav-item">
              <router-link 
                :to="{ name: 'home' }" 
                :class="[currentRoute=='home' ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-home"></i>
                <p>
                  Dashboard
                </p>
              </router-link>
            </li>

            <li class="nav-item">
              <router-link 
                :to="{ name: 'orders' }" 
                :class="[currentRoute=='orders' ? 'active' : '', 'nav-link']"
              >
                <i class="nav-icon fas fa-bell"></i>
                <p>
                  Orders
                  <span class="right badge badge-danger">1</span>
                </p>
              </router-link>
            </li>

            <li class="nav-item has-treeview menu-open">
              <a href="#" :class="[['my-meals', 'my-cuisines', 'my-menu-items', 'my-kitchen', 'my-waiters'].includes(currentRoute) ? 'active' : '', 'nav-link']">
                <i class="nav-icon fas fa-hotel"></i>
                <p>
                  My Restaurant
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link :to="{ name: 'my-meals' }" :class="[currentRoute=='my-meals' ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Meals</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'my-cuisines' }" :class="[currentRoute=='my-cuisines' ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cuisines</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'my-menu-items' }"  :class="[currentRoute=='my-menu-items' ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Menu-Items</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'my-kitchen' }"  :class="[currentRoute=='my-kitchen' ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kitchen</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link :to="{ name: 'my-waiters' }"  :class="[currentRoute=='my-waiters' ? 'active' : '', 'nav-link']">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Waiters</p>
                  </router-link>
                </li>
              </ul>
            </li>
            
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
         
        <router-view></router-view>

    </div>
    
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