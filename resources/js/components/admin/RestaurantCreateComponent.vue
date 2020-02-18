
<template>

	<div class="container-fluid">

		<div class="modal fade" id="modal-create-restaurant-category">
			<div class="modal-dialog">
				<div class="modal-content bg-secondary">
					<div class="modal-header">
					  <h4 class="modal-title">Add More Type</h4>
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					    <span aria-hidden="true">&times;</span></button>
					</div>
				  	<!-- form start -->
				  	<form class="form-horizontal" v-on:submit.prevent="addRestaurantCuisine" autocomplete="off">
						<div class="modal-body">

				      		<input type="hidden" name="_token" :value="csrf">

							<div class="row">
								<div class="col-sm-12">
									<div class="card-outline">
							            <div class="card-body box-profile">
							              	<div class="form-group row">
								              		
							              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">New Cuisine Name</label>
								                <div class="col-sm-8">
								                  <input type="text" class="form-control" v-model="newRestaurantCuisine.name" placeholder="Cuisine Name" required="true">
								                </div>	
								              	
							              	</div>
							            </div>
							            <!-- /.card-body -->
								    </div>
								</div>
							</div>
						</div>
						<div class="modal-footer justify-content-between">
						  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
						  	<button type="submit" class="btn btn-outline-light">Add Category</button>
						</div>
					</form>
				</div>
			<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal-create-restaurant-category -->

		<div class="modal fade" id="modal-create-menu-category">
			<div class="modal-dialog">
				<div class="modal-content bg-secondary">
					<div class="modal-header">
					  <h4 class="modal-title">Create Food Menu</h4>
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					    <span aria-hidden="true">&times;</span></button>
					</div>
				  	<!-- form start -->
				  	<form class="form-horizontal" v-on:submit.prevent="addMenuCategory" autocomplete="off">
						<div class="modal-body">

				      		<input type="hidden" name="_token" :value="csrf">

							<div class="row">
								<div class="col-sm-12">
									<div class="card-outline">
							            <div class="card-body box-profile">
							              	<div class="form-group row">
								              		
							              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Menu Name</label>
								                <div class="col-sm-8">
								                  <input type="text" class="form-control" v-model="newMenuCategory.name" placeholder="Menu Name" required="true">
								                </div>	
								              	
							              	</div>
							            </div>
							            <!-- /.card-body -->
								    </div>
								</div>
							</div>
						</div>
						<div class="modal-footer justify-content-between">
						  <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
						  <button type="submit" class="btn btn-outline-light">Add Food Menu</button>
						</div>
					</form>
				</div>
			<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal-modal-create-menu-category -->

		<div class="modal fade" id="modal-create-meal-tag">
			<div class="modal-dialog">
				<div class="modal-content bg-secondary">
					<div class="modal-header">
					  <h4 class="modal-title">Create New Meal</h4>
					  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					    <span aria-hidden="true">&times;</span></button>
					</div>
				  	<!-- form start -->
				  	<form class="form-horizontal" v-on:submit.prevent="addNewMeal" autocomplete="off">
						<div class="modal-body">

				      		<input type="hidden" name="_token" :value="csrf">

							<div class="row">
								<div class="col-sm-12">
									<div class="card-outline">
							            <div class="card-body box-profile">
							              	<div class="form-group row">
								              		
							              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Meal Name</label>
								                <div class="col-sm-8">
								                  <input type="text" class="form-control" v-model="newMeal.name" placeholder="Meal Name" required="true">
								                </div>	
								              	
							              	</div>
							            </div>
							            <!-- /.card-body -->
								    </div>
								</div>
							</div>
						</div>
						<div class="modal-footer justify-content-between">
						  <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
						  <button type="submit" class="btn btn-outline-light">Add Meal</button>
						</div>
					</form>
				</div>
			<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal-modal-create-meal-tag -->

		<section v-show="loading">
			<div class="row justify-content-center vh-100">
				<div class="d-flex align-items-center">
					<div class="card p-5">
					  	<div class="overlay dark">
					    	<i class="fas fa-3x fa-sync-alt fa-spin"></i>
					  	</div>
					</div>
				</div>
			</div>
	    </section>

		<!-- form start -->
	  	<form class="form-horizontal" v-on:submit.prevent="restaurantCreation" autocomplete="off">
		
	  		<input type="hidden" name="_token" :value="csrf">

			<transition-group name="fade">

				<div class="row" v-bind:key="1" v-show="!loading && step==1">
	
					<div class="col-sm-12">
						<div class="card card-secondary card-outline">
							<div class="card-body">
									
								<h2 class="text-center mb-4 lead">Restaurant Profile</h2>
							
								<div class="row">

									<div class="col-sm-12">
										<div class="card">
								            <div class="card-body">

								            	<div class="d-flex flex-wrap align-content-center form-group row">
								            		<div class="col-sm-8">
									                  	<div class="text-center">
										                    <img class="img-fluid" :src="this.restaurant.banner_preview" alt="Restaurant Banner">

									                    </div>
									                </div>
									              	<div class="col-sm-4  align-self-center">
									                  	<div class="input-group">
										                    <div class="custom-file">
										                        <input type="file" class="custom-file-input" v-on:change="onBannerChange" accept="image/*">
										                        <label class="custom-file-label" for="customFile">Change Banner</label>
										                    </div>
									                    </div>
									                </div>
								              	</div>

								              	<hr>

								              	<div class="form-group row">
									              	<div class="col-6">
									              		<div class="row">
										              		<label for="inputName3" class="col-sm-4 col-form-label text-right">Restaurant Name</label>
											                <div class="col-sm-8">
										                  		<input type="text" class="form-control" v-model="restaurant.name" placeholder="Restaurant Name">
											                </div>
											            </div>
											        </div>
	 												<div class="col-6">
									              		<div class="row">
										              		<label for="inputUserName3" class="col-sm-4 col-form-label text-right">Username</label>
											                <div class="col-sm-8">
										                  		<input type="text" class="form-control" v-model="restaurant.user_name" placeholder="No Space or special characters">
											                </div>
											            </div>
											        </div>
								              	</div>
								              	<div class="form-group row">
									              	<div class="col-6">
									              		<div class="row">
										              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Email</label>
											                <div class="col-sm-8">
											                  <input type="email" class="form-control" v-model="restaurant.email" placeholder="Email" required="true">
											                </div>
									              		</div>
									              	</div>
									                <div class="col-6">
									                	<div class="row">
									                		<label for="inputMobile3" class="col-sm-4 col-form-label text-right">Mobile</label>
											                <div class="col-sm-8">
											                  	<input type="tel" class="form-control" v-model="restaurant.mobile" placeholder="Mobile" required="true">
											                </div>
									                	</div>
									              	</div>
								              	</div>
								              	<div class="form-group row">
									              	<div class="col-6">
									              		<div class="row">
										              		<label for="inputPassword3" class="col-sm-4 col-form-label text-right">Password</label>
											                <div class="col-sm-8">
											                  	<input type="password" class="form-control" v-model="restaurant.password" placeholder="Login Password" required="true">
											                </div>
									              		</div>
									              	</div>
									                <div class="col-6">
									                	<div class="row">
									                		<label for="inputConfirmPassword3" class="col-sm-4 col-form-label text-right">Repeat Password</label>
											                <div class="col-sm-8">
											                  	<input type="password" class="form-control" v-model="restaurant.password_confirmation" placeholder="Repeat Password" required="true">
											                </div>
									                	</div>
									              	</div>
								              	</div>

								              	<div class="form-group row">
								              		<div class="col-6">
								              			<div class="row d-flex align-items-center">

									                		<label for="inputCusineTags3" class="col-sm-4 col-form-label text-right">Restaurant Type</label>
											                <div class="col-sm-6">

											                	<multiselect v-model="restaurantCuisineObjectTags" placeholder="Restaurant Type" label="name" track-by="id" :options="allRestaurantCuisines" :multiple="true" :max="3" :required="true">

											                	</multiselect>

											                </div>
											                <div class="col-sm-2 text-center">
									                  			<button type="button" class="btn btn-secondary btn-sm p-0" data-toggle="modal" data-target="#modal-create-restaurant-category">
												                  	<i class="fa fa-plus-circle" aria-hidden="true"></i>
									                  				Type
												                </button>
											                </div>
									                	</div>
									              	</div>
									              	<div class="col-6">
									                	<div class="row">
									                		<div class="col-sm-4 text-right">
										                		<label for="inputWebsite3" class="col-form-label">
										                			Website
										                		</label>
								                				(if any)
									                		</div>
											                <div class="col-sm-8">
											                  	<input type="url" class="form-control" v-model="restaurant.website" placeholder="Restaurant Website">
											                </div>
									                	</div>
									              	</div>
								              	</div>

								            </div>
								            <!-- /.card-body -->
									    </div>
									    <!-- /.card-card -->
									</div>	
								</div>

								<div class="row">
									<div class="col-sm-12 text-right">
							          	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
							          		<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							          	</button>
									</div>
						        </div>

							</div>
						</div>
					</div>

				</div>

				<div class="row" v-bind:key="2" v-show="step==2">
					
					<div class="col-sm-12">
						<div class="card card-secondary card-outline">
							<div class="card-body">
								
								<h2 class="text-center mb-4 lead">Restaurant Address</h2>
							
								<div class="row">
									
									<div class="col-sm-12">
										<div class="card">
								            <div class="card-body">

								              	<div class="form-group row">  	
								              		<label for="inputLocation3" class="col-sm-4 col-form-label text-right">Restaurant Location</label>
									                <div class="col-sm-8">
														
														<vue-google-autocomplete

														    id="restaurantAddress"
														    classname="form-control"
														    placeholder="Start typing"
														    v-on:placechanged="getAddressData"
														    types="(cities)"
														    country="bd"
														>
														</vue-google-autocomplete>

														<!-- 
								                  		<input type="text" class="form-control" v-model="restaurant.lat" placeholder="Restaurant Location" required="true">
								                  		-->

									                </div>   
								              	</div>
									              	
								              	<div class="form-group row">
								              		<label for="inputAddress3" class="col-sm-4 col-form-label text-right">Detail Address</label>
									                <div class="col-sm-8">

								                  		<ckeditor class="form-control" :editor="editor" v-model="restaurant.address">
								                  		</ckeditor>

									                </div>	
								              	</div>

								            </div>
								            <!-- /.card-body -->
									    </div>
									    <!-- /.card-card -->
									</div>	
								</div>

								<div class="row">
									<div class="col-sm-12 text-right">
							          	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="step-=1">
							          		<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							          	</button>
									
							          	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
							          		<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							          	</button>
									</div>
						        </div>

							</div>
						</div>
					</div>

				</div>

				<div class="row" v-bind:key="3" v-show="step==3">
					
					<div class="col-sm-12">
						<div class="card card-secondary card-outline">
							<div class="card-body">
								
								<h2 class="text-center mb-4 lead">Restaurant Menu Profile</h2>
							
								<div class="row">
									
									<div class="col-sm-12">
										<div class="card">
								            <div class="card-body">

								              	<div class="form-group row">
									              	<div class="col-6">
									              		<div class="row">
										              		<label for="inputMinOrder3" class="col-sm-4 col-form-label text-right">Min. Order</label>
											                <div class="col-sm-8">
										                  		<input type="number" class="form-control" v-model="restaurant.min_order" placeholder="Minimum Currency" min="100" step="1">
											                </div>
											            </div>
											        </div>
	 												<div class="col-6">
									              		<div class="row">
											                	
									              			<div class="col-sm-4 text-right">
										                		<label for="inputPayment3" class="col-form-label pb-0">
										                			Payment
										                		</label>
										                		<div class="text-right p-0 m-0">
										                			(physical orders)
										                		</div>
								                				
									                		</div>

											                <div class="col-sm-8">
											                	<div class="checkbox mt-1">
											                		
										                  			<toggle-button  v-model="restaurant.is_post_paid" value="true" :width="120" :font-size="15" :color="{checked: 'green', unchecked: '#17a2b8'}" 
		               												:labels="{checked: 'Post-paid', unchecked: 'Pre-paid' }"/>
											                	</div>
											                </div>
											            </div>
											        </div>
								              	</div>
								              	<div class="form-group row">
									              	<div class="col-6">
									              		<div class="row d-flex align-items-center">
										              		<label for="inputFoodTags3" class="col-sm-4 col-form-label text-right">Best Food Items</label>
											                <div class="col-sm-6">
											                  	
											                  	<multiselect v-model="restaurantFoodObjectTags" placeholder="Select three main foods" label="name" track-by="id" :options="allMenuCategories" :multiple="true" :max="3" :required="true">
											                	</multiselect>

											                </div>
											                 
											                <div class="col-sm-2 text-center">
									                  			<button type="button" class="btn btn-secondary btn-sm p-0" data-toggle="modal" data-target="#modal-create-menu-category">
												                  	<i class="fa fa-plus-circle" aria-hidden="true"></i>
									                  				Food
												                </button>
											                </div> 
											            	
									              		</div>
									              	</div>
									                <div class="col-6">
									                	<div class="row d-flex align-items-center">
									                		<label for="inputMealTags3" class="col-sm-4 col-form-label text-right">Available Meals</label>
											                <div class="col-sm-6">
											                  	
											                	<multiselect v-model="restaurantMealObjectTags" placeholder="Available Meals" label="name" track-by="id" :options="allMeals" :multiple="true" :max="6" :required="true">

											                	</multiselect>

											                </div>
											                
											                <div class="col-sm-2 text-center">
									                  			<button type="button" class="btn btn-secondary btn-sm p-0" data-toggle="modal" data-target="#modal-create-meal-tag">
												                  	<i class="fa fa-plus-circle" aria-hidden="true"></i>
									                  				Meal
												                </button>
											                </div>
											                
									                	</div>
									              	</div>
								              	</div>

								            </div>
								            <!-- /.card-body -->
									    </div>
									    <!-- /.card-card -->
									</div>	
								</div>

								<div class="row">
									<div class="col-sm-12 text-right">
							          	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="step-=1">
							          		<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							          	</button>
									
							          	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
							          		<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
							          	</button>
									</div>
						        </div>

							</div>
						</div>
					</div>

				</div>

				<div class="row" v-bind:key="4" v-show="step==4">
					
					<div class="col-sm-12">
						<div class="card card-secondary card-outline">
							<div class="card-body">
								
								<h2 class="text-center mb-4 lead">Service & Schedule</h2>
							
								<div class="row">
									
									<div class="col-sm-12">
										<div class="card">
								            <div class="card-body">

								              	<div class="form-group row">
									              	<div class="col-6">
									              		<div class="row">
										              		<label for="inputParking3" class="col-sm-4 col-form-label text-right">Parking Facility</label>
											                <div class="col-sm-8">
										                  		<div class="checkbox mt-1">
											                		
										                  			<toggle-button value="true" v-model="restaurant.has_parking" :width="120" :font-size="15" :color="{checked: 'green', unchecked: '#FF0000'}" 
		               												:labels="{checked: 'Available', unchecked: 'No Parking' }"/>
											                	</div>
											                </div>
											            </div>
											        </div>
	 												<div class="col-6">
									              		<div class="row">
										              		<label for="inputSelfService3" class="col-sm-4 col-form-label text-right">Self Service</label>
											                	
											                <div class="col-sm-8">
									                  			<div class="checkbox mt-1">
											                		
										                  			<toggle-button value ="true" v-model="restaurant.is_self_service" :width="120" :font-size="15" :color="{checked: 'green', unchecked: '#17a2b8'}" 
		               												:labels="{checked: 'Yes', unchecked: 'No' }"/>
											                	</div>
											                </div>
											            </div>
											        </div>
								              	</div>
								              	<div class="form-group row mb-4">
									              		
								              		<label for="inputServiceSchedule3" class="col-sm-2 col-form-label text-right">Service Schedule</label>
									                <div class="col-sm-10">

									                	<table id="service_schedule"></table>

									                  	<!-- 
									                  	<input type="text" class="form-control" v-model="restaurant.service_schedule" placeholder="Available Meals" required="true">
									                  	 -->
									                </div>
									              		
								              	</div>

								              	<div class="form-group row">  		
							                		<label for="inputBookingBreak3" class="col-sm-2 col-form-label text-right">Booking Breaks</label>

									                <div class="col-sm-10">

									                	<table id="booking_break_schedule"></table>

									                  	<!-- 
									                  	<input type="text" class="form-control" v-model="restaurant.booking_break_schedule" placeholder="Available Meals" required="true">
 														-->

									                </div>    	
								              	</div>

								            </div>
								            <!-- /.card-body -->
									    </div>
									    <!-- /.card-card -->
									</div>	
								</div>

								<div class="row">
									<div class="col-sm-12 text-right">
							          	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="step-=1">
							          		<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
							          	</button>
									
							          	<button type="submit" class="btn btn-danger rounded-pill">
							          		Create Restaurant
							          	</button>
									</div>
						        </div>

							</div>
						</div>
					</div>

				</div>

			</transition-group>

			<div v-show="!loading" class="row">
				<div class="col-sm-12">

					<div class="card">
						<div class="card-header text-center">

							<div class="progress">
								<div class="progress-bar bg-success w-25" v-show="step>=1">
									Profile
								</div>
								<div class="progress-bar bg-info w-25" v-show="step>=2">
									Address
								</div>
								<div class="progress-bar bg-warning w-25" v-show="step>=3">
									Menu Profile
								</div>
								<div class="progress-bar bg-danger w-25" v-show="step>=4">
									Service & Schedule
								</div>
							</div>

						</div>
					</div>
					
				</div>
			</div>

	  	</form>
		<!-- form end -->
	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import { ToggleButton } from 'vue-js-toggle-button';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
	import VueGoogleAutocomplete from 'vue-google-autocomplete';

	var restaurantCreateData = {
    	step : 1,
    	// errors : [],
	    editor: ClassicEditor,
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
    	restaurantNewBanner : null,

		restaurantCuisineObjectTags : [],
    	allRestaurantCuisines : [],
    	newRestaurantCuisine : {},

    	restaurantFoodObjectTags : [],
    	allMenuCategories : [],
    	newMenuCategory : {},

    	restaurantMealObjectTags : [],
    	allMeals : [],
    	newMeal : {},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    	service_schedule : {},
		booking_break_schedule : {},
    };

	export default {
		// Local registration of components
		components: { 
			ToggleButton : ToggleButton, 
			Multiselect, // short form of Multiselect : Multiselect
			ckeditor: CKEditor.component,
			VueGoogleAutocomplete
		},

	    data() {
	        return restaurantCreateData;
		},

		created(){
			this.fetchAllRestaurantCuisines();
			this.fetchAllMenuCategories();
			this.fetchAllMeals();
		},

		mounted(){
			this.enableScheduler();
		},

		watch : {
			restaurantCuisineObjectTags : function(restaurantCuisineObjectTags){
				let array = [];
				$.each(restaurantCuisineObjectTags, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.restaurant.restaurantCuisineTags = array;
			},
			restaurantFoodObjectTags : function(restaurantFoodObjectTags){
				let array = [];
				$.each(restaurantFoodObjectTags, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.restaurant.restaurantFoodTags = array;
			},
			restaurantMealObjectTags : function(restaurantMealObjectTags){
				let array = [];
				$.each(restaurantMealObjectTags, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.restaurant.restaurantMealTags = array;
			},
			'restaurant.name' : function(val){
				this.restaurant.user_name = val.replace(/\s/g, '');
			},
			service_schedule : function(val){
				this.restaurant.service_schedule = val;
			},
			booking_break_schedule : function(val){
				this.restaurant.booking_break_schedule = val;
			},
		},

		methods : {

			getAddressData(addressData, placeResultData, id) {
				// console.log(addressData);
			},
			fetchAllRestaurantCuisines(){
				this.loading = true;
				axios
					.get('/api/restaurant-cuisines')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurantCuisines = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllMenuCategories(){
				this.loading = true;
				axios
					.get('/api/menu-categories')
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
			fetchAllMeals(){
				this.loading = true;
				axios
					.get('/api/meals')
					.then(response => {
						console.log('Meal Response : '+response);
						if (response.status == 200) {
							this.loading = false;
							this.allMeals = response.data;
							console.log('All Meals :'+this.allMeals);
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			enableScheduler(){

				$('#service_schedule').scheduler({
					accuracy: 1,
					onDragStart: function(){
					  	// console.log('Drag Start');
					},
					onDragMove: function(){
					  	// console.log('Draggging');
					},
					onDragEnd: function(){
					  	// console.log('Drag End');
					},
					onSelect: function(){
						restaurantCreateData.service_schedule = $('#service_schedule').scheduler('val');
						// console.log(restaurantCreateData.service_schedule);
					  	// console.log($('#service_schedule').scheduler('val'));
					},
				});

				$('#booking_break_schedule').scheduler({
					accuracy: 1,
					onDragStart: function(){
					  	// console.log('Drag Start');
					},
					onDragMove: function(){
					  	// console.log('Draggging');
					},
					onDragEnd: function(){
					  	// console.log('Drag End');
					},
					onSelect: function(){
						restaurantCreateData.booking_break_schedule = $('#booking_break_schedule').scheduler('val');
						// console.log(restaurantCreateData.booking_break_schedule);
					  	// console.log($('#booking_break_schedule').scheduler('val'));
					},
				});
			},
			addRestaurantCuisine(){
				$('#modal-create-restaurant-category').modal('hide');
				axios
					.post('/restaurant-cuisines', this.newRestaurantCuisine)
					.then(response => {
						if (response.status == 200) {
							this.newRestaurantCuisine = {};
							this.allRestaurantCuisines = response.data;
							toastr.success(response.data.success, "Success");
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.warning(error.response.data.errors[x], "Warning");
							}
				      	}
					});
			},
			addMenuCategory(){
				$('#modal-create-menu-category').modal('hide');
				axios
					.post('/menu-categories', this.newMenuCategory)
					.then(response => {
						if (response.status == 200) {
							this.newMenuCategory = {};
							this.allMenuCategories = response.data;
							toastr.success(response.data.success, "Success");
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.warning(error.response.data.errors[x], "Warning");
							}
				      	}
					});
			},
			addNewMeal(){
				$('#modal-create-meal-tag').modal('hide');
				axios
					.post('/meals', this.newMeal)
					.then(response => {
						if (response.status == 200) {
							this.newMeal = {};
							this.allMeals = response.data;
							toastr.success(response.data.success, "Success");
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.warning(error.response.data.errors[x], "Warning");
							}
				      	}
					});
			},
			nextPage () {

				/*
				this.errors = [];

				if (this.step==1) {
					if (!this.name) {
				        this.errors.push('Name required.');
				    }
				    if (!this.age) {
				        this.errors.push('Age required.');
				    }
				}
				else if (this.step==2) {

				}
				else if (this.step==3) {

				}
				else if (this.step==4) {

				}
				*/

				this.step+=1;
			},

			onBannerChange(evnt){

		      	let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.createImage(files[0]);
		      	}

		      	return;
			},
			
			createImage(file) {
                let reader = new FileReader();
                reader.onload = (evnt) => {
                    this.restaurantNewBanner = this.restaurant.banner_preview = evnt.target.result;
                };
                reader.readAsDataURL(file);
            },

			restaurantCreation() {
				
				this.restaurant.banner_preview = this.restaurantNewBanner;
				// this.restaurant.lat : null,
				// this.restaurant.lng : null,
				// this.restaurant.service_schedule : this.restaurant.service_schedule,
				// this.restaurant.booking_break_schedule : this.restaurant.booking_break_schedule,

				axios
					.post('/restaurants', this.restaurant)
					.then(response => {
						console.log(response.data);
						if (response.status == 200) {
							this.restaurant = {};
							this.restaurantCuisineObjectTags = [];
							this.restaurantFoodObjectTags = [];
							this.restaurantMealObjectTags = [];
							this.$router.push({name:'admin.restaurants.index'});
							toastr.success(response.data.success, "Success");
						}
					})
					.catch(error => {
						console.log(error);
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.warning(error.response.data.errors[x], "Warning");
							}
				      	}
					});
			}
		}
  	}

</script>

<!-- <style src="vue-multiselect/dist/vue-multiselect.min.css"></style> -->

<style scoped>
	
	@import '~vue-multiselect/dist/vue-multiselect.min.css';

	.fade-enter-active {
  		animation: drag-out .5s reverse;
	}
	.fade-leave-active {
  		animation: drag-out .5s;
	}

	@keyframes drag-out {
		from {
			transform: translate(0, 0);
		}
  		to {
  			transform: translate(-100%, 0);
  		}
	}
</style>