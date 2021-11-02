
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
							<h2 class="lead float-left mt-1">Restaurant Cuisine List</h2>

                        	<button 
	                        	type="button" 
	                        	@click="showRestaurantCuisineCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant-Cuisine
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
											<th scope="col">Cuisines</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="allRestaurantCuisines.length"
									    	v-for="(restaurant, index) in allRestaurantCuisines"
									    	:key="restaurant.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
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
							    				<p class="small text-danger" v-show="restaurant.restaurant_cuisines.length === 0">
							                		Cuisine not available or trashed
							                	</p>

								    			<ul>
													<li v-for="cuisine in restaurant.restaurant_cuisines" 
														:key="cuisine.id"
													>
													
														{{ cuisine.name }}
													
													</li>
												</ul>
								    		</td>
								    		<td>
										      	<button 
										      		type="button" 
										      		@click="showRestaurantCuisineEditModal(restaurant)" 
										      		class="btn btn-primary btn-sm"
										      	>
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button 
								        			type="button" 
								        			@click="showRestaurantCuisineDeletionModal(restaurant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!allRestaurantCuisines.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No cuisine or restaurant found.</div>
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
										@click="query === '' ? fetchAllRestaurantCuisines() : searchData()"
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
										@paginate="query === '' ? fetchAllRestaurantCuisines() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-restaurantCuisine -->
			<div class="modal fade" id="modal-createOrEdit-restaurantCuisine">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Restaurant-Cuisine
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateRestaurantCuisine() : storeRestaurantCuisine()"
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
				                                  			v-model="singleRestaurantCuisineData.restaurantObject"
				                                  			placeholder="Restaurant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="restaurantsFiltered" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantCuisine.restaurant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false" 
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantCuisine.restaurant')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.restaurantCuisine.restaurant 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Cuisine Name
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleRestaurantCuisineData.cuisineObjects"
				                                  			placeholder="Cuisine Names" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allCuisines" 
					                                  		:required="true" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false"
					                                  		:class="!errors.restaurantCuisine.cuisine ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantCuisine.cuisine')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.restaurantCuisine.cuisine 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>
								            </div>
								            <!-- /.card-body -->
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer flex-column">
								<div class="col-sm-12 text-right">
									<span 
										class="text-white small" 
										v-show="!submitForm"
									>
								  		Please input all required fields
								  	</span>
								</div>
								<div class="col-sm-12 d-flex justify-content-between">
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
								  		{{ editMode ? 'Update' : 'Create' }} Restaurant-Cuisine
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-restaurantCuisine-->

			<!-- modal-restaurantCuisine-delete-confirmation -->
			<div class="modal fade" id="modal-restaurantCuisine-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant-Cuisine Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="destroyRestaurantCuisine" 
						  	autocomplete="off"
					  	>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5 style="color:#c6cacc">
					      			<small>
					      				Once you delete, you can't retreive it again.
					      			</small>
					      		</h5>
					      		<h5>
					      			Are you sure want to delete restaurant cuisine ??
					      		</h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>

							  	<button 
							  		type="submit" 
							  		class="btn btn-outline-light"
							  	>
							  		Delete
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-restaurantCuisine-delete-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleRestaurantCuisineData = {
		
    	cuisineObjects : [],
    	restaurantObject : {
			
    	},

    	restaurantCuisine : {
			cuisine_id : [],
			restaurant_id : null,
    	}
    };

	var mealListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	allCuisines : [],
    	allRestaurants : [],
    	restaurantsFiltered : [],

    	allRestaurantCuisines : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		restaurantCuisine : {},
    	},

        singleRestaurantCuisineData : singleRestaurantCuisineData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
		// Local registration of components
		components: {
			Multiselect, // short form of Multiselect : Multiselect
		},

	    data() {
	        return mealListData;
		},

		created(){
			this.fetchAllCuisines();
			this.fetchAllRestaurants();
			this.fetchAllRestaurantCuisines();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllRestaurantCuisines();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			'singleRestaurantCuisineData.cuisineObjects' : function(cuisineObjects){
				let array = [];
				$.each(cuisineObjects, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.singleRestaurantCuisineData.restaurantCuisine.cuisine_id = array;
			},
			'singleRestaurantCuisineData.restaurantObject' : function(restaurantObject){
				if (restaurantObject) {
					this.singleRestaurantCuisineData.restaurantCuisine.restaurant_id = restaurantObject.id;
				}else
					this.singleRestaurantCuisineData.restaurantCuisine.restaurant_id = null;
			},
		},

		methods : {

			fetchAllCuisines(){
				this.loading = true;
				axios
					.get('/api/cuisines/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allCuisines = response.data;
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
						/*
							this.allRestaurants = this.allRestaurants.filter(
								object => {
							  		return object.admin_approval == true;
								}
							);
						*/
							this.restaurantsFiltered = this.allRestaurants;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllRestaurantCuisines() {
				this.loading = true;
				axios
					.get('/api/restaurant-cuisines/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurantCuisines = response.data.data;
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
					this.fetchAllRestaurantCuisines();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllRestaurantCuisines();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showRestaurantCuisineCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.restaurantCuisine = {};

				this.restaurantsFiltered = this.allRestaurants;

				this.singleRestaurantCuisineData.restaurantObject = {};
				this.singleRestaurantCuisineData.cuisineObjects = [];

				$('#modal-createOrEdit-restaurantCuisine').modal('show');
			},
    		storeRestaurantCuisine(){

    			if (!this.singleRestaurantCuisineData.restaurantCuisine.restaurant_id || this.singleRestaurantCuisineData.restaurantCuisine.cuisine_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantCuisine').modal('hide');
				
				axios
					.post('/restaurant-cuisines/'+ this.perPage, this.singleRestaurantCuisineData.restaurantCuisine)
					.then(response => {

						if (response.status == 200) {
							
							this.singleRestaurantCuisineData.restaurantObject = {};
							this.singleRestaurantCuisineData.cuisineObjects = [];

							this.query = '';
							this.allRestaurantCuisines = response.data.data;
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
			showRestaurantCuisineEditModal(restaurant) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurantCuisine = {};

				this.restaurantsFiltered = this.allRestaurants.filter(
					object => {
				  		return object.id == restaurant.id;
					}
				);
				
				this.singleRestaurantCuisineData.restaurantObject = restaurant;
				this.singleRestaurantCuisineData.cuisineObjects = restaurant.restaurant_cuisines;

				$("#modal-createOrEdit-restaurantCuisine").modal("show");
			},
			updateRestaurantCuisine(){

				if (!this.singleRestaurantCuisineData.restaurantCuisine.restaurant_id || this.singleRestaurantCuisineData.restaurantCuisine.cuisine_id.length === 0) {
					
					this.submitForm = false;
					return;
				}
				
				$('#modal-createOrEdit-restaurantCuisine').modal('hide');
				
				axios
					.put('/restaurant-cuisines/' + this.singleRestaurantCuisineData.restaurantCuisine.restaurant_id + '/' + this.perPage, this.singleRestaurantCuisineData.restaurantCuisine)
					.then(response => {

						if (response.status == 200) {

							this.singleRestaurantCuisineData.restaurantObject = {};
							this.singleRestaurantCuisineData.cuisineObjects = [];

							if (this.query === '') {
								this.allRestaurantCuisines = response.data.data;
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
			showRestaurantCuisineDeletionModal(restaurant) {
				this.singleRestaurantCuisineData.restaurantObject = restaurant;
				$("#modal-restaurantCuisine-delete-confirmation").modal("show");
			},
			destroyRestaurantCuisine(){

				$("#modal-restaurantCuisine-delete-confirmation").modal("hide");

				axios
					.delete('/restaurant-cuisines/'+this.singleRestaurantCuisineData.restaurantObject.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantCuisineData.restaurantObject = {};

							if (this.query === '') {
								this.allRestaurantCuisines = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Deleted");
						}
					})
					.catch(error => {
						console.log(error);
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
					"/api/restaurant-cuisines/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allRestaurantCuisines = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'restaurantCuisine.restaurant' :

						if (Object.keys(this.singleRestaurantCuisineData.restaurantObject).length === 0) {
							this.errors.restaurantCuisine.restaurant = 'Restaurant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantCuisine, 'restaurant');
						}

						break;

					case 'restaurantCuisine.cuisine' :

						if (Object.keys(this.singleRestaurantCuisineData.cuisineObjects).length === 0) {
							this.errors.restaurantCuisine.cuisine = 'Cuisine name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantCuisine, 'cuisine');
						}

						break;
				}
	 
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