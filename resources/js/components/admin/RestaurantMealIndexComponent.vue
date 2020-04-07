
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
							<h2 class="lead float-left mt-1">Restaurant Meal List</h2>

                        	<button type="button" @click="showRestaurantMealCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant-Meal
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
									        <!-- No special characters (except '@&*+-_=') -->
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
											<th scope="col">Meals</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="allRestaurantMeals.length"
									    	v-for="(restaurant, index) in allRestaurantMeals"
									    	:key="restaurant.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ restaurant.name }}</td>
								    		<td>
							    				<p class="small text-danger" v-show="restaurant.restaurant_meal_categories.length === 0">
							                		Meal not available or trashed
							                	</p>

								    			<ul v-show="restaurant.restaurant_meal_categories.length">
													<li v-for="meal in restaurant.restaurant_meal_categories" 
														:key="meal.id">
													
														{{ meal.name }}
													
													</li>
												</ul>
								    		</td>
								    		<td>
										      	<button type="button" @click="showRestaurantMealEditModal(restaurant)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										      	</button>
								      			<button 
								        			type="button" 
								        			@click="showRestaurantMealDeletionModal(restaurant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!allRestaurantMeals.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No data found.</div>
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
										@click="query === '' ? fetchAllRestaurantMeals() : searchData()"
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
										@paginate="query === '' ? fetchAllRestaurantMeals() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-restaurantMeal -->
			<div class="modal fade" id="modal-createOrEdit-restaurantMeal">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Restaurant-Meal</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateRestaurantMeal() : storeRestaurantMeal()"
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
				                                  			v-model="singleRestaurantMealData.restaurantObject"
				                                  			placeholder="Restaurant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="restaurantsFiltered" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantMeal.restaurant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantMeal.restaurant')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.restaurantMeal.restaurant }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Meal Name
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleRestaurantMealData.mealObjects"
				                                  			placeholder="Meal Names" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMeals" 
					                                  		:required="true" 
					                                  		:multiple="true" 
					                                  		:max="6" 
					                                  		:class="!errors.restaurantMeal.meal ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantMeal.meal')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.restaurantMeal.meal }}
												  		</div>
									                </div>	
									              	
								              	</div>
								            </div>
								            <!-- /.card-body -->
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer justify-content-between">
								<div class="col-sm-12 text-right">
									<span class="text-danger p-0 m-0 small" v-show="!submitForm">
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
							  		{{ editMode ? 'Update' : 'Create' }} Restaurant Meal
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-restaurantMeal-->

			<!-- modal-restaurantMeal-delete-confirmation -->
			<div class="modal fade" id="modal-restaurantMeal-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant-Meal Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyRestaurantMeal" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete restaurant meal ?? </h5>
					      		<h5 class="text-secondary"><small>But once you want, you can retreive it from bin.</small></h5>
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
			<!-- /modal-restaurantMeal-delete-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleRestaurantMealData = {
		
    	mealObjects : [],
    	restaurantObject : {
			
    	},

    	restaurantMeal : {
			meal_id : [],
			restaurant_id : null,
    	}
    };

	var mealListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	allMeals : [],
    	allRestaurants : [],
    	restaurantsFiltered : [],

    	allRestaurantMeals : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		restaurantMeal : {},
    	},

        singleRestaurantMealData : singleRestaurantMealData,

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
			this.fetchAllMeals();
			this.fetchAllRestaurants();
			this.fetchAllRestaurantMeals();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllRestaurantMeals();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			'singleRestaurantMealData.mealObjects' : function(mealObjects){
				let array = [];
				$.each(mealObjects, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.singleRestaurantMealData.restaurantMeal.meal_id = array;
			},
			'singleRestaurantMealData.restaurantObject' : function(restaurantObject){
				if (restaurantObject) {
					this.singleRestaurantMealData.restaurantMeal.restaurant_id = restaurantObject.id;
				}else
					this.singleRestaurantMealData.restaurantMeal.restaurant_id = null;
			},
		},

		methods : {

			fetchAllMeals(){
				this.loading = true;
				axios
					.get('/api/meals/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMeals = response.data;
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
							this.restaurantsFiltered = this.allRestaurants;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllRestaurantMeals() {
				this.loading = true;
				axios
					.get('/api/restaurant-meals/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurantMeals = response.data.data;
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
					this.fetchAllRestaurantMeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllRestaurantMeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showRestaurantMealCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.restaurantMeal = {};

				this.restaurantsFiltered = this.allRestaurants;

				this.singleRestaurantMealData.restaurantObject = {};
				this.singleRestaurantMealData.mealObjects = [];

				$('#modal-createOrEdit-restaurantMeal').modal('show');
			},
    		storeRestaurantMeal(){

    			if (!this.singleRestaurantMealData.restaurantMeal.restaurant_id || this.singleRestaurantMealData.restaurantMeal.meal_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMeal').modal('hide');
				
				axios
					.post('/restaurant-meals/'+ this.perPage, this.singleRestaurantMealData.restaurantMeal)
					.then(response => {

						if (response.status == 200) {
							
							this.singleRestaurantMealData.restaurantObject = {};
							this.singleRestaurantMealData.mealObjects = [];

							this.query = '';
							this.allRestaurantMeals = response.data.data;
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
			showRestaurantMealEditModal(restaurant) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurantMeal = {};

				this.restaurantsFiltered = this.allRestaurants.filter(
					object => {
				  		return object.id == restaurant.id;
					}
				);
				
				this.singleRestaurantMealData.restaurantObject = restaurant;
				this.singleRestaurantMealData.mealObjects = restaurant.restaurant_meal_categories;

				$("#modal-createOrEdit-restaurantMeal").modal("show");
			},
			updateRestaurantMeal(){

				if (!this.singleRestaurantMealData.restaurantMeal.restaurant_id || this.singleRestaurantMealData.restaurantMeal.meal_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMeal').modal('hide');
				
				axios
					.put('/restaurant-meals/' + this.singleRestaurantMealData.restaurantMeal.restaurant_id + '/' + this.perPage, this.singleRestaurantMealData.restaurantMeal)
					.then(response => {

						if (response.status == 200) {

							this.singleRestaurantMealData.restaurantObject = {};
							this.singleRestaurantMealData.mealObjects = [];

							if (this.query === '') {
								this.allRestaurantMeals = response.data.data;
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
			showRestaurantMealDeletionModal(restaurant) {
				this.singleRestaurantMealData.restaurantObject = restaurant;
				$("#modal-restaurantMeal-delete-confirmation").modal("show");
			},
			destroyRestaurantMeal(){

				$("#modal-restaurantMeal-delete-confirmation").modal("hide");

				axios
					.delete('/restaurant-meals/'+this.singleRestaurantMealData.restaurantObject.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantMealData.restaurantObject = {};

							if (this.query === '') {
								this.allRestaurantMeals = response.data.data;
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
					"/api/restaurant-meals/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allRestaurantMeals = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'restaurantMeal.restaurant' :

						if (Object.keys(this.singleRestaurantMealData.restaurantObject).length === 0) {
							this.errors.restaurantMeal.restaurant = 'Restaurant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMeal, 'restaurant');
						}

						break;

					case 'restaurantMeal.meal' :

						if (Object.keys(this.singleRestaurantMealData.mealObjects).length === 0) {
							this.errors.restaurantMeal.meal = 'Meal name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMeal, 'meal');
						}

						break;
				}
	 
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