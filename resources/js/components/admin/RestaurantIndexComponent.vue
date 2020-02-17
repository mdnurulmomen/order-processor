
<template>

	<div class="container-fluid">

		<section v-show="loading">
			<div class="row justify-content-center">
				<div class="d-flex flex-column justify-content-center align-items-center vh-100">
					<div class="card p-5">
					  	<div class="overlay dark">
					    	<i class="fas fa-3x fa-sync-alt fa-spin"></i>
					  	</div>
					</div>
				</div>
			</div>
	    </section>

		<div class="row">

			<div class="col-sm-12">
				<div class="card card-secondary card-outline">
					<div class="card-body">

						<div class="row">
							<div class="col-sm-12">
								<h2 class="text-center mb-4 lead float-left">Restaurant List</h2>
								<router-link :to="{ name: 'admin.restaurants.create' }"  class="btn btn-outline-secondary btn-sm rounded-pill float-right">
				                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
				                    New Restaurant
				              	</router-link>
							</div>
						</div>	
					
						<div class="row">
							<div class="col-sm-12">
								<div class="card">
						            <div class="card-body">

						              	

						            </div>
						            <!-- /.card-body -->
							    </div>
							    <!-- /.card-card -->
							</div>	
						</div>

						<div class="row">
							<div class="col-sm-12 text-right">
					          	
							</div>
				        </div>

					</div>
				</div>
			</div>

		</div>

	</div>
    
</template>

<script type="text/javascript">

	var restaurantComponentData = {
    	
    	restaurant : {
    		banner_preview : null,
			name : null,
			user_name : null,
			mobile : null,
			email : null,
			password : null,
			password_confirmation : null,
			restaurantCuisineTags : [],
			website : null,

			address : null,
			lat : null,
			lng : null,

			min_order : 100,
			is_post_paid : false,
			restaurantFoodTags : [],
			restaurantMealTags : [],

			has_parking : false,
			is_self_service : false,
			service_schedule : null,
			booking_break_schedule : null,
    	},
    	loading : false,
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
		// Local registration of components
		components: { 

		},

	    data() {
	        return restaurantComponentData;
		},

		created(){

		},

		mounted(){
			
		},

		watch : {
			
		},

		methods : {

		}
  	}

</script>

<style>
	
</style>