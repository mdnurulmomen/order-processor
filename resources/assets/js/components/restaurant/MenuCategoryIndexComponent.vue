
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
		
			<div 
				class="row" 
				v-show="!loading"
			>
				<div class="col-sm-12">

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">
								{{ 
									restaurantNameFromData
								}}
								 
								Menu Categories
							</h2>

					      	<button 
	                        	type="button" 
	                        	@click="showRestaurantMenuList" 
	                        	class="btn btn-default btn-sm float-right"
                        	>
			        			<i class="fas fa-eye"></i>
			        			My Menu-Items
			      			</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-12 was-validated">
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
								<table class="table table-hover table-bordered table-striped text-center">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Menu Category Name</th>
											<th scope="col">Serving From</th>
											<th scope="col">Serving To</th>
											<th scope="col">Number Menu Items</th>
											<!-- <th scope="col">Action</th> -->
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="restaurantMenuCategoriesToShow.length"
									    	v-for="(restaurantMenuCategory, index) in restaurantMenuCategoriesToShow"
									    	:key="restaurantMenuCategory.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>
								    			{{ 
								    				restaurantMenuCategory.menu_category ? restaurantMenuCategory.menu_category.name : 'Trashed Menu-Category' 
								    			}}
								    		</td>
								    		<td>
								    			{{ restaurantMenuCategory.serving_from }}
								    		</td>
								    		<td>
								    			{{ restaurantMenuCategory.serving_to }}
								    		</td>
								    		<td>
								    			{{ 
								    				restaurantMenuCategory.restaurant_menu_items.length 
								    			}}
								    		</td>
							    		<!-- 
								    		<td>
												<toggle-button 
		                                  			:sync="true" 
		                                  			v-model="restaurantMenuCategory.deleted_at" 
		                                  			:width="130"  
		                                  			:height="30" 
		                                  			:font-size="15" 
		                                  			:color="{checked: 'green', unchecked: 'red'}" 
		                                  			:labels="{checked: 'On', unchecked: 'Off' }" 
		                                  			@change="updateRestaurantMenuCategoryAvailability(menuItem)"
	                                  			/>
								    		</td> 
								    	-->
									  	</tr>
									  	<tr v-show="!restaurantMenuCategoriesToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Menu Category found.
									      		</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
							<div class="row d-flex align-items-center align-content-center">
								<div class="col-sm-1">
									<select class="form-control" v-model="perPage" @change="changeNumberContents()">
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
										@click="query === '' ? fetchRestaurantAllMenuCategories() : searchData()"
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
										@paginate="query === '' ? fetchRestaurantAllMenuCategories() : searchData()"
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
	// import { ToggleButton } from 'vue-js-toggle-button';

	var menuCategoryListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	
    	restaurantAllMenuCategories : [],
		restaurantMenuCategoriesToShow : [],

    	pagination: {
        	current_page: 1
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        restaurant_id: document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
    };

	export default {

		// Local registration of components
	/*
		components: {
			ToggleButton : ToggleButton, 
		},
	*/

	    data() {
	        return menuCategoryListData;
		},

		created(){
			this.fetchRestaurantAllMenuCategories();
		},

		computed: {
		    // a computed getter
		    restaurantNameFromData: function () {
		      	// `this` points to the vm instance
		      	if (this.restaurantMenuCategoriesToShow.length) {
	      			return this.restaurantMenuCategoriesToShow[0].restaurant.name;
		      	}

		      	return 'My Restaurant';
		    },
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchRestaurantAllMenuCategories();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {

			fetchRestaurantAllMenuCategories() {
				this.loading = true;
				axios
					.get('/api/restaurant-menu-categories/' + this.restaurant_id + '/' + this.perPage + "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.restaurantAllMenuCategories = response.data;
							// this.pagination = response.data;
							this.showDataListOfSelectedTab();
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchRestaurantAllMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchRestaurantAllMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
		    searchData() {
				axios
				.get(
					"/restaurant-menu-categories/" + this.restaurant_id + "/"  + this.query + "/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {

					this.restaurantAllMenuCategories = response.data;
					this.restaurantMenuCategoriesToShow = this.restaurantAllMenuCategories.all.data;
					this.pagination = this.restaurantAllMenuCategories.all;

				})
				.catch(e => {
					console.log(e);
				});
			},
			showDataListOfSelectedTab(){
				this.restaurantMenuCategoriesToShow = this.restaurantAllMenuCategories.current.data;
				this.pagination = this.restaurantAllMenuCategories.current;
			},
			showRestaurantMenuList() {
				this.$router.push({
			 		name: 'restaurant.myMenuItems.index',  
				});
			},

		}
  	}

</script>