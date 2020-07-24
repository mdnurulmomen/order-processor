
<template>

	<div class="container-fluid">

		<section>

			<div class="row justify-content-center vh-100" v-show="loading">
				<div class="d-flex align-items-center">
					<div class="card p-5">
					  	<div class="overlay dark">
					    	<i class="fas fa-3x fa-sync-alt fa-spin"></i>
					  	</div>
					</div>
				</div>
			</div>
		
			<div class="row" v-show="!loading">
				<div class="col-sm-12">

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">My Kitchen</h2>
						</div>

						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-striped text-center">
									<thead>
										<tr>
											<th scope="col">Username</th>
											<th scope="col">Email</th>
											<th scope="col">Mobile</th>
											<th scope="col">Restaurant</th>
											<th scope="col">Admin Approval</th>
											<th scope="col">Status</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="Object.keys(myKitchen).length>0"
									    	:key="myKitchen.id"
									  	>
								    		<td>{{ myKitchen.user_name }}</td>
								    		<td>{{ myKitchen.email }}</td>
								    		<td>{{ myKitchen.mobile }}</td>
								    		<td>{{ myKitchen.restaurant!=null ? myKitchen.restaurant.name : 'NA' }}</td>
								    		<td>
								    			<toggle-button 
		                                  			:sync="true" 
		                                  			v-model="myKitchen.admin_approval" 
		                                  			:width="130"  
		                                  			:height="30" 
		                                  			:font-size="15" 
		                                  			:color="{checked: 'green', unchecked: 'red'}" 
		                                  			:labels="{checked: 'Approved', unchecked: 'Not-Approved' }" 
		                                  			:disabled="true"
	                                  			/>
								    		</td>
								    		<td>
								    			{{ myKitchen.deleted_at ? 'Trashed' : 'Available' }}
								    		</td>
									  	</tr>
									  	<tr v-show="!Object.keys(myKitchen).length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No Kitchen found.</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
						</div>
					</div>
				</div>	
			</div>

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import { ToggleButton } from 'vue-js-toggle-button';

	var myKitchen = {
      	
    	loading : false,
    	myKitchen : {},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        restaurant_id: document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
    };

	export default {

	    // Local registration of components
		components: {
			ToggleButton : ToggleButton, 
		},

	    data() {
	        return myKitchen;
		},

		created(){
			this.fetchRestaurantKitchen();
		},

		methods : {
			fetchRestaurantKitchen(){
				this.loading = true;
				axios
					.get('/api/my-kitchen/' + this.restaurant_id)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.myKitchen = response.data.kitchen;
							// console.log(response.data.kitchen);
							// console.log(this.myKitchen.restaurant.name);
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
		}
  	}

</script>