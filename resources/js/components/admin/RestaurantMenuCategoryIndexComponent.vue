
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
								All Restaurant Menu Categories
							</h2>
							 
                        	<button 
                        		type="button" 
                        		@click="showRestaurantMenuCategoryCreateModal"
                        	 	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant-menu-categories
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
								<table class="table table-hover table-bordered table-striped text-center">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Restaurant Name</th>
											<th scope="col">Menu Categories</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="allRestaurantMenuCategories.length"
									    	v-for="(restaurant, index) in allRestaurantMenuCategories"
									    	:key="restaurant.id"
									  	>
									    	<td scope="row">
									    		{{ index + 1 }}
									    	</td>
								    		<td>
								    			{{ restaurant.name }}
								    			<span :class="[restaurant.admin_approval ? 'badge-success' : 'badge-danger', 'right badge ml-1']"
								    			>
								    				{{ 
								    					restaurant.admin_approval ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>
								    		</td>
								    		<td>
							    				<p 
								    				class="small text-danger" 
								    				v-show="restaurant.restaurant_menu_categories.length === 0"
							    				>
							                		Menu-Category not available or trashed
							                	</p>

								    			<ul 
								    				v-show="restaurant.restaurant_menu_categories.length"
								    			>
													<li v-for="menuCategory in restaurant.restaurant_menu_categories" 
														:key="menuCategory.id"
													>
													
														{{ menuCategory.name }}
													
													</li>
												</ul>
								    		</td>
								    		<td>
								    			<button 
									    			type="button" 
									    			@click="showRestaurantMenuCategoryDetails(restaurant)" 
									    			class="btn btn-primary btn-sm"
								    			>
										        	<i class="fas fa-eye"></i>
										        	View Category Details
										      	</button>

										      	<button 
											      	type="button" 
											      	@click="showRestaurantMenuList(restaurant)" 
											      	class="btn btn-warning btn-sm"
										      	>
										        	<i class="fas fa-eye"></i>
										        	View Menu-Items
										      	</button>

										      	<!-- 
										      	<button type="button" @click="showRestaurantMenuCategoryEditModal(restaurant)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
 												-->
								    		</td>
									  	</tr>
									  	<tr v-show="!allRestaurantMenuCategories.length"
									  	>
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No Category or Restaurant found.</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
							<div class="row d-flex align-items-center align-content-center">
								<div class="col-sm-1">
									<select 
										class="form-control" 
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
										@click="query === '' ? fetchAllRestaurantMenuCategories() : searchData()"
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
										@paginate="query === '' ? fetchAllRestaurantMenuCategories() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-restaurantMenuCategory -->
			 
			<div class="modal fade" id="modal-createOrEdit-restaurantMenuCategory">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ 
						  			editMode ? 'Edit' : 'Create' 
						  		}} 
						  		Restaurant Menu-Category
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="storeRestaurantMenuCategory()"
						  	autocomplete="off"
					  	>
							<div class="modal-body text-dark">

					      		<input 
						      		type="hidden" 
						      		name="_token" 
						      		:value="csrf"
					      		>

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Restaurant Name
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.restaurantObject"
				                                  			placeholder="Restaurant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="expectedRestaurants" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantMenuCategory.restaurant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantMenuCategory.restaurant')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.restaurantMenuCategory.restaurant }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Menu-Category Name 
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.menuCategoryObjects"
				                                  			placeholder="MenuCategory Names" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMenuCategories" 
					                                  		:required="true" 
					                                  		:multiple="true" 
					                                  		:class="!errors.restaurantMenuCategory.menuCategory ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantMenuCategory.menuCategory')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{
												        		errors.restaurantMenuCategory.menuCategory
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Serving From
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.restaurantMenuCategory.serving_from"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantScheduleHours" 
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove"
				                                  		>
					                                	</multiselect>
									                </div>
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Serving To
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.restaurantMenuCategory.serving_to"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantScheduleHours" 
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove"
				                                  		>
					                                	</multiselect>
									                </div>
									              	
								              	</div>
								            </div>
								            
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer justify-content-between">
								<div class="col-sm-12 text-right">
									<span 
										class="text-danger p-0 m-0 small" 
										v-show="!submitForm"
									>
								  		Please input all required fields
								  	</span>
								</div>
							  	<button 
								  	type="button" 
								  	class="btn btn-outline-light" 
								  	data-dismiss="modal"
							  	>
							  		Close
							  	</button>

							  	<button 
							  		type="submit" 
							  		class="btn btn-outline-light"
							  		:disabled="!submitForm" 
							  	>
							  		{{ editMode ? 'Update' : 'Create' }} Restaurant Menu Category
							  	</button>
							</div>
						</form>
					</div>
				
				</div>
				
			</div> 
			
			<!-- /.modal-createOrEdit-restaurantMenuCategory-->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var restaurantSingleMenuCategoryData = {
		
    	restaurantObject : {
			
    	},

    	menuCategoryObjects : [],

    	restaurantMenuCategory : {
			menu_category_id : [],
			serving_from : '10.00',
			serving_to : '22.00',
			restaurant_id : null,
			from_menu_category_index : true,
    	}
    };

	var menuCategoryListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	allMenuCategories : [],
    	allRestaurants : [],
    	expectedRestaurants : [],

    	allRestaurantMenuCategories : [],

    	restaurantScheduleHours : ['6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00', '20.00', '21.00', '22.00', '23.00', '24.00', '1.00', '2.00'],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		restaurantMenuCategory : {},
    	},

        restaurantSingleMenuCategoryData : restaurantSingleMenuCategoryData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
		// Local registration of components
		components: {
			Multiselect, // short form of Multiselect : Multiselect
		},

	    data() {
	        return menuCategoryListData;
		},

		created(){
			this.fetchAllMenuCategories();
			this.fetchAllRestaurants();
			this.fetchAllRestaurantMenuCategories();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllRestaurantMenuCategories();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			'restaurantSingleMenuCategoryData.menuCategoryObjects' : function(menuCategoryObjects){

				let array = [];
				$.each(menuCategoryObjects, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id = array;
			},
			'restaurantSingleMenuCategoryData.restaurantObject' : function(restaurantObject){
				
				if (restaurantObject) {
					this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id = restaurantObject.id;
				}
				else
					this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id = null;

			},
		},

		methods : {

			fetchAllMenuCategories(){
				this.loading = true;
				axios
					.get('/api/menu-categories/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMenuCategories = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllRestaurants() {
				this.loading = true;
				axios
					.get('/api/restaurants/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurants = response.data;
							this.expectedRestaurants = this.allRestaurants;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllRestaurantMenuCategories() {
				this.loading = true;
				axios
					.get('/api/restaurant-menu-categories/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurantMenuCategories = response.data.data;
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
					this.fetchAllRestaurantMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllRestaurantMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showRestaurantMenuCategoryDetails(restaurant){
				this.$router.push({
			 		name: 'admin.restaurantMenuCategoryDetail.index', 
			 		params: { restaurant : restaurant.id, restaurantName : restaurant.name }, 
				});
			},
			
			showRestaurantMenuCategoryCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.restaurantMenuCategory = {};

				this.expectedRestaurants = this.allRestaurants;

				this.restaurantSingleMenuCategoryData.restaurantObject = {};
				this.restaurantSingleMenuCategoryData.menuCategoryObjects = [];
				this.restaurantSingleMenuCategoryData.restaurantMenuCategory = {};
				this.restaurantSingleMenuCategoryData.restaurantMenuCategory.from_menu_category_index = true;

				$('#modal-createOrEdit-restaurantMenuCategory').modal('show');
			},
    		storeRestaurantMenuCategory(){

    			if (!this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id || this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuCategory').modal('hide');
				
				axios
					.post('/restaurant-menu-categories/'+ this.perPage, this.restaurantSingleMenuCategoryData.restaurantMenuCategory)
					.then(response => {

						if (response.status == 200) {
							
							this.restaurantSingleMenuCategoryData.restaurantObject = {};
							this.restaurantSingleMenuCategoryData.menuCategoryObjects = [];
							this.restaurantSingleMenuCategoryData.restaurantMenuCategory = {};

							this.query = '';
							this.allRestaurantMenuCategories = response.data.data;
							this.pagination = response.data;

							toastr.success(response.data.success, "Added");
							
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
			
			/*
			showRestaurantMenuCategoryEditModal(restaurant) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurantMenuCategory = {};

				this.expectedRestaurants = this.allRestaurants.filter(
					object => {
				  		return object.id == restaurant.id;
					}
				);
				
				this.restaurantSingleMenuCategoryData.restaurantObject = restaurant;
				this.restaurantSingleMenuCategoryData.menuCategoryObjects = restaurant.restaurant_menu_categories;

				if (restaurant.restaurant_menu_categories.length) {

					this.restaurantSingleMenuCategoryData.restaurantMenuCategory = restaurant.restaurant_menu_categories[0].pivot;
				}

				$("#modal-createOrEdit-restaurantMenuCategory").modal("show");
			},
			updateRestaurantMenuCategory(){

				if (!this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id || this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuCategory').modal('hide');
				
				axios
					.put('/restaurant-menu-categories/' + this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id + '/' + this.perPage, this.restaurantSingleMenuCategoryData.restaurantMenuCategory)
					.then(response => {

						if (response.status == 200) {

							this.restaurantSingleMenuCategoryData.restaurantObject = {};
							this.restaurantSingleMenuCategoryData.menuCategoryObjects = [];
							this.restaurantSingleMenuCategoryData.restaurantMenuCategory = {};

							if (this.query === '') {
								this.allRestaurantMenuCategories = response.data.data;
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
			*/
		    searchData() {
				
				axios
				.get(
					"/api/restaurant-menu-categories/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allRestaurantMenuCategories = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'restaurantMenuCategory.restaurant' :

						if (Object.keys(this.restaurantSingleMenuCategoryData.restaurantObject).length === 0) {
							this.errors.restaurantMenuCategory.restaurant = 'Restaurant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuCategory, 'restaurant');
						}

						break;

					case 'restaurantMenuCategory.menuCategory' :

						if (this.restaurantSingleMenuCategoryData.menuCategoryObjects.length === 0) {
							this.errors.restaurantMenuCategory.menuCategory = 'Menu Category name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuCategory, 'menuCategory');
						}

						break;
				}
	 
			},
			showRestaurantMenuList(restaurant) {
				this.$router.push({
			 		name: 'admin.restaurantMenuItem.index', 
			 		params: { restaurant : restaurant.id, restaurantName : restaurant.name }, 
				});
			},

		}
  	}

</script>

<style scoped>
	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}
</style>