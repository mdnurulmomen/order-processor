
<template>

	<div class="container-fluid">

		<section>

			<div 
				class="row justify-content-center vh-100" 
				v-show="loading"
			>
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
							<h2 class="lead float-left mt-1">
								{{ 
									restaurantNameFromData 
								}}

								Menu-Items
							</h2>

                        	<button type="button" 
                        			@click="showRestaurantAllMenuCategories" class="btn btn-default btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-eye" aria-hidden="true"></i>
                                My Menu-Categories
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-12 float-right was-validated">
									  	<input 
									  		type="text" 
									  		v-model="query" 
									  		pattern="[^'!#$%^()\x22]+" class="form-control" 
									  		placeholder="Search"
									  	>
									  	<div class="invalid-feedback">
									  		Please search with releavant input
									  	</div>
									</div>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-hover table-bordered text-center">
									
									<tbody>
									  	<tr v-show="restaurantAllMenuCategories.length"
									    	v-for="(restaurantMenuCategory, index) in restaurantAllMenuCategories"
									    	:key="restaurantMenuCategory.id"
									  	>  		
								  			<td colspan="8">
									    		<p class="font-weight-bold font-italic">
									    			{{ 
									    				restaurantMenuCategory.menu_category ? restaurantMenuCategory.menu_category.name : 'Trashed Menu Category' 
									    			}}
										    		<span 
											    		class="badge badge-danger" 
											    		v-show="restaurantMenuCategory.deleted_at"
											    	>
											    		trashed
											    	</span>
									    		</p>
										    	<table class="table">
										    		<thead>
														<tr>
															<th scope="col">#</th>
															<th scope="col">Menu Item</th>
															<th scope="col">Detail</th>
															<th scope="col">Variations</th>
															<th scope="col">Addons</th>
															<th scope="col">Price</th>
															<th scope="col">Customizable</th>
															<th scope="col">Action</th>
														</tr>
													</thead>
										    		<tbody>
												    	<tr v-show="restaurantMenuCategory.restaurant_menu_items.length" 
													    	v-for="(menuItem, index) in restaurantMenuCategory.restaurant_menu_items" 
													    	:key="menuItem.id"
													  	>
													    	<td scope="row">
													    		{{ index + 1 }}
													    	</td>
												    		<td>
												    			{{ menuItem.name }}

												    			<span 
														    		class="badge badge-danger" 
														    		v-show="menuItem.deleted_at"
														    	>
														    		outdated
														    	</span>

												    		</td>
												    		<td>
												    			<span 
												    				v-html="menuItem.detail"
												    			>
												    				
												    			</span>
												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="menuItem.has_variation"
												    			>
																  	<li v-for="itemVariation in menuItem.restaurant_menu_item_variations" 
																  		:key="itemVariation.id"
																  	>
																    	
																    	{{ 
																    		itemVariation.name 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="itemVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="!menuItem.has_variation"
												    			>
												    				Not-Available
												    			</p>
												    		</td>
												    		<td>
												    			
												    			<ul class="text-left" 
												    				v-show="menuItem.has_addon"
												    			>
																  	<li v-for="addonItem in menuItem.restaurant_menu_item_addons" 
																  		:key="addonItem.id"
																  	>
																    	
																    	{{ 
																    		addonItem.name 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="addonItem.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="!menuItem.has_addon"
												    			>
												    				Not-Available
												    			</p>

												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="menuItem.restaurant_menu_item_variations.length"
												    			>
																  	<li v-for="itemVariation in menuItem.restaurant_menu_item_variations" 
																  		:key="itemVariation.id"
																  	>
																    	
																    	{{ 
																    		itemVariation.pivot.price 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="itemVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			{{ 
												    				!menuItem.has_variation ? menuItem.price : ''
												    			}}
												    		</td>
												    		<td>
												    			{{ menuItem.customizable ? 'Customizable' : 'Not-Customizable' }}
												    		</td>

												    		<td>
														      	<toggle-button 
						                                  			:sync="true" 
						                                  			v-model="menuItem.item_stock" 
						                                  			:width="130"  
						                                  			:height="30" 
						                                  			:font-size="15" 
						                                  			:color="{checked: 'green', unchecked: 'red'}" 
						                                  			:labels="{checked: 'On', unchecked: 'Off' }" 
						                                  			@change="updateRestaurantMenuItemStock(menuItem)"
					                                  			/>
												    		</td>
												    	</tr>

												    	<tr v-show="!restaurantMenuCategory.restaurant_menu_items.length"
												    	>
												    		<td colspan="8">
													      		<div class="alert alert-danger" role="alert">
													      			No Menu-Items Found
													      		</div>
													    	</td>
													  	</tr>
										    		</tbody>
										    	</table>
									    	</td>
									  	</tr>
									  	<tr v-show="!restaurantAllMenuCategories.length">
								    		<td colspan="8">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Menu Category Found or Trashed.
									      		</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
							<div class="row d-flex align-items-center align-content-center">
								<div class="col-sm-1">
									<select class="form-control" 
											v-model="perPage" 
											@change="changeNumberContents()"
									>
										<option>10</option>
										<option>20</option>
										<option>30</option>
										<option>40</option>
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchRestaurantAllMenuItems() : searchData()"
									>
										Reload
										<i class="fas fa-sync"></i>
									</button>
								</div>
								<div class="col-sm-9">
									<pagination
										v-if="pagination.last_page > 1"
										:pagination="pagination"
										:offset="5"
										@paginate="query === '' ? fetchRestaurantAllMenuItems() : searchData()"
									>
									</pagination>
								</div>
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

	var restaurantMenuListData = {
      	query : '',
    	perPage : 10,
    	loading : false,

    	currentMenuItem : {},
    	restaurantAllMenuCategories : [],
    	
    	pagination: {
        	current_page: 1
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        restaurant_id: document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
    };

	export default {

		// Local registration of components
		components: {
			ToggleButton : ToggleButton, 
		},

	    data() {
	        return restaurantMenuListData;
		},

		created(){
			this.fetchRestaurantAllMenuItems();
		},

		computed: {
		    // a computed getter
		    restaurantNameFromData: function () {
		      // `this` points to the vm instance
		      if (this.restaurantAllMenuCategories.length) {
	      		return this.restaurantAllMenuCategories[0].restaurant.name;
		      }

		      return 'Current Restaurant';
		    },
		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchRestaurantAllMenuItems();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},

		},

		methods : {

			fetchRestaurantAllMenuItems(){
				this.loading = true;
				axios
					.get('/api/menu-items/' + this.restaurant_id + '/' + this.perPage + "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.restaurantAllMenuCategories = response.data.data;
							this.pagination = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			changeNumberContents() {

				this.pagination.current_page = 1;
				
				if (this.query === '') {
					this.fetchRestaurantAllMenuItems();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchRestaurantAllMenuItems();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
    		showRestaurantAllMenuCategories(){
				this.$router.push({
			 		name: 'restaurant.myMenuCategories.index',
				});
			},
			updateRestaurantMenuItemStock(restaurantMenuItem){
				
				this.currentMenuItem.id = restaurantMenuItem.id;
				this.currentMenuItem.item_stock = restaurantMenuItem.item_stock;

				axios
					.put('/menu-items/' + this.restaurant_id + '/' + this.perPage + "?page=" +
				    this.pagination.current_page, this.currentMenuItem)
					.then(response => {

						if (response.status == 200) {

							if (this.query === '') {
								this.restaurantAllMenuCategories = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Updated");
							
						}
						
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});

			},
		    searchData() {
				
				axios
				.get(
					"/menu-items/" + this.restaurant_id + "/" + this.query + "/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.restaurantAllMenuCategories = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},

		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
	
	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}
</style>