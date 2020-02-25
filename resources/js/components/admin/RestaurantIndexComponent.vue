
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

			<!-- 
			<div class="row">
				<div class="col-sm-12">
					<h2 class="text-center mb-4 lead float-left">Restaurant List</h2>
					<router-link :to="{ name: 'admin.restaurants.create' }"  class="btn btn-outline-secondary btn-sm rounded-pill float-right">
	                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
	                    New Restaurant
	              	</router-link>
				</div>
			</div>	 
			-->
		
			<div class="row" v-show="!loading">
				<div class="col-sm-12">
	              	
					<!--
					// For those who has only to view
					<vue-bootstrap4-table 
						:rows="restaurants" 
						:columns="columns" 
						:config="config"
						:actions="actions"
					>

					</vue-bootstrap4-table> 
					-->

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">Restaurant List</h2>

                        	<button type="button" @click="showRestaurantCreateModal()" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" @click="showAllRestaurants">All</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" @click="showApprovedRestaurants">Approved</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" @click="showNonApprovedRestaurants">Non-Approved</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" @click="showTrashedRestaurants">Trashed</a>
											</li>
										</ul>
									</div>

									<div class="col-sm-6 float-right">
									  	<input v-model="query" type="text" class="form-control" placeholder="Search">
									</div>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-striped text-center">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Name</th>
											<th scope="col">Email</th>
											<th scope="col">Phone</th>
											<th scope="col">Website</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="restaurantsToShow.length"
									    	v-for="(restaurant, index) in restaurantsToShow"
									    	:key="restaurant.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ restaurant.name}}</td>
								    		<td>{{ restaurant.email }}</td>
								    		<td>{{ restaurant.mobile }}</td>
								    		<td>{{ restaurant.website }}</td>
								    		<td>
								      			<button type="button" @click="showRestaurantDetailModal(restaurant)" class="btn btn-info btn-sm">
								        			<i class="fas fa-eye"></i>
								      			</button>
										      	<button type="button" v-show="restaurant.deleted_at === null" @click="showRestaurantEditModal(restaurant)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										      	</button>
								      			<button
								        			v-show="restaurant.deleted_at === null"
								        			type="button"
								        			@click="showRestaurantDeletionModal(restaurant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								      			</button>
								      			<button
								        			v-show="restaurant.deleted_at !== null"
								        			type="button"
								        			@click="showRestaurantRestoreModal(restaurant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-restore"></i>
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!restaurantsToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No data found.</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
							<div class="row">
								<div class="col-2">
									<button type="button" class="btn btn-primary btn-sm" @click="reload">
										Reload
										<i class="fas fa-sync"></i>
									</button>
								</div>
								<div class="col-10">
									<pagination
										v-if="pagination.last_page > 1"
										:pagination="pagination"
										:offset="5"
										@paginate="query === '' ? fetchAllRestaurants() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<div class="modal fade" id="modal-show-restaurant">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant Details</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">

							<ul class="nav nav-tabs justify-content-center mb-4" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#profile">
										Profile
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#address">
										Address
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#menu">
										Menu Profile
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#service">
										Service & Schedule
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div id="profile" class="container tab-pane active">
									<div class="row">
					            		<div class="col-sm-4">
						                	<div class="text-center">
						                  		<img class="img-fluid " :src="singleRestaurantData.restaurant.banner_preview" alt="Restaurant Banner">
						                	</div>
						                	<h3 class="profile-username text-center">
						                		{{ singleRestaurantData.restaurant.name }}
						                	</h3>
						              	</div>
					            		<div class="col-sm-8">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			User-name :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.user_name}}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Mobile :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.mobile}}
								                </div>	
								            </div> 
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Email :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.email}}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Website :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.website ? singleRestaurantData.restaurant.website : 'No website'}}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Approval :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.admin_approval ? 'Approved' : 'Not approved'}}
								                </div>	
								            </div>  
					            		</div>
					            	</div>
								</div>
								<div id="address" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Restaurant Address :
							              		</label>
								                <div class="col-sm-6">
								                	<span v-html="singleRestaurantData.restaurant.address"></span>
								                </div>	
								            </div>
								            <div class="form-group row">
								            	<label class="col-sm-6 text-right">
							              			Map :
							              		</label>		
								                <div class="col-sm-6">
								                	Map will be added later
								                </div>	
								            </div>  
					            		</div>
					            	</div>
								</div>
								<div id="menu" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Restaurant Type :
							              		</label>
								                <div class="col-sm-6">
								                	<ul>
													  	<li v-for="restaurantCuisineObjectTag in singleRestaurantData.restaurantCuisineObjectTags">
													    	{{ restaurantCuisineObjectTag.name }}
													  	</li>
													</ul>
								                </div>	
								            </div>
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Best Menu :
							              		</label>
								                <div class="col-sm-6">
								                	<ul>
													  	<li v-for="restaurantFoodObjectTag in singleRestaurantData.restaurantFoodObjectTags">
													    	{{ restaurantFoodObjectTag.name }}
													  	</li>
													</ul>
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Available Meals :
							              		</label>
								                <div class="col-sm-6">
								                  	<ul>
													  	<li v-for="restaurantMealObjectTag in singleRestaurantData.restaurantMealObjectTags">
													    	{{ restaurantMealObjectTag.name }}
													  	</li>
													</ul>
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Minimum Order :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.min_order}} tk
								                </div>	
								            </div> 
					            		</div>
					            	</div>
								</div>
								<div id="service" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Payment :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.is_post_paid ? 'Post-paid' : 'Pre-paid'}}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Self-service :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.is_self_service ? 'Self-service' : 'Waiter service'}}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Parking :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.has_perking ? 'Available' : 'Not available'}}
								                </div>	
								            </div> 
					            		</div>
					            	</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
						  	<button type="button" class="btn btn-outline-secondary float-right" data-dismiss="modal">
						  		Close
						  	</button>
						</div>
						
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>

			<div class="modal fade" id="modal-restaurant">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Update' : 'Create' }} Restaurant</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
						  	<!-- form start -->
							<form class="form-horizontal" v-on:submit.prevent="editMode ? updateRestaurant() : storeRestaurant()" autocomplete="off" novalidate>

								<input type="hidden" name="_token" :value="csrf">

								<transition-group name="fade">

									<div class="row" v-bind:key="1" v-show="!loading && singleRestaurantData.step==1">

									  	<div class="col-sm-12">
	  
									        <h2 class="text-center mb-4 lead">Restaurant Profile</h2>
									      
									        <div class="row">

									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">

									                      	<div class="d-flex flex-wrap align-content-center form-group row">
									                        	<div class="col-sm-8">
									                              	<div class="text-center">
									                                	<img class="img-fluid" :src="this.singleRestaurantData.restaurant.banner_preview" alt="Restaurant Banner" style="max-height: 150px;">

									                              	</div>
									                          	</div>
									                          	<div class="col-sm-4  align-self-center">
									                              	<div class="input-group">
									                                	<div class="custom-file">
									                                    	<input type="file" class="custom-file-input" v-on:change="onBannerChange" accept="image/*">
									                                    	<label class="custom-file-label" for="customFile">
									                                    		Change Banner
									                                    	</label>
									                                	</div>
									                              	</div>
									                          	</div>
									                        </div>

									                        <hr>

									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="row">
									                              		<label for="inputName3" class="col-sm-4 col-form-label text-right">
									                              			Restaurant Name
									                              		</label>
									                              		<div class="col-sm-8">
									                                  		<input type="text" class="form-control" v-model="singleRestaurantData.restaurant.name" placeholder="Restaurant Name">
									                              		</div>
									                          		</div>
									                      		</div>
									                  			<div class="col-6">
									                            	<div class="row">
									                              		<label for="inputUserName3" class="col-sm-4 col-form-label text-right">Username</label>
									                              		<div class="col-sm-8">
									                                  		<input type="text" class="form-control" v-model="singleRestaurantData.restaurant.user_name" placeholder="No Space or special characters">
									                              		</div>
										                          	</div>
										                      	</div>
									                        </div>
									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="row">
									                              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Email</label>
									                              		<div class="col-sm-8">
									                                		<input type="email" class="form-control" v-model="singleRestaurantData.restaurant.email" placeholder="Email" required="true">
									                              		</div>
									                            	</div>
									                          	</div>
									                          	<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMobile3" class="col-sm-4 col-form-label text-right">Mobile</label>
										                              	<div class="col-sm-8">
										                                  	<input type="tel" class="form-control" v-model="singleRestaurantData.restaurant.mobile" placeholder="Mobile" required="true">
										                              	</div>
										                            </div>
									                          	</div>
									                        </div>
									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="row">
									                              		<label for="inputPassword3" class="col-sm-4 col-form-label text-right">
									                              			Password
									                              		</label>
									                              		<div class="col-sm-8">
									                                  		<input type="password" class="form-control" v-model="singleRestaurantData.restaurant.password" placeholder="Login Password" :required="!editMode">
									                              		</div>
									                            	</div>
									                          	</div>
									                          	<div class="col-6">
									                            	<div class="row">
									                              		<label for="inputConfirmPassword3" class="col-sm-4 col-form-label text-right">
									                              			Repeat Password
									                              		</label>
									                              		<div class="col-sm-8">
									                                  		<input type="password" class="form-control" v-model="singleRestaurantData.restaurant.password_confirmation" placeholder="Repeat Password" :required="!editMode">
									                              		</div>
									                            	</div>
									                          	</div>
									                        </div>

									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="row d-flex align-items-center">

									                              		<label for="inputCusineTags3" class="col-sm-4 col-form-label text-right">
									                              			Restaurant Type
									                              		</label>
									                              		<div class="col-sm-6">

										                                	<multiselect v-model="singleRestaurantData.restaurantCuisineObjectTags" placeholder="Restaurant Type" label="name" track-by="id" :options="singleRestaurantData.allRestaurantCuisines" :multiple="true" :max="3" :required="true">

										                                	</multiselect>

										                              	</div>
										                              	<div class="col-sm-2 text-center">
										                                  	<button type="button" class="btn btn-secondary btn-sm p-0" data-toggle="modal" data-target="#modal-create-restaurant-category">
										                                    	<i class="fa fa-plus-circle" aria-hidden="true"></i>
											                                    Cuisine
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
										                                  <input type="url" class="form-control" v-model="singleRestaurantData.restaurant.website" placeholder="Restaurant Website">
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

									<div class="row" v-bind:key="2" v-show="singleRestaurantData.step==2">
									  
									  	<div class="col-sm-12">
									        
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

									                              	<ckeditor class="form-control" :editor="singleRestaurantData.editor" v-model="singleRestaurantData.restaurant.address">
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
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="singleRestaurantData.step-=1">
									                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
									          
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
									                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
								                  	</button>
									          	</div>
								            </div>
									  	</div>
									</div>

									<div class="row" v-bind:key="3" v-show="singleRestaurantData.step==3">
									  
									  	<div class="col-sm-12">
									        
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
										                                  	<input type="number" class="form-control" v-model="singleRestaurantData.restaurant.min_order" placeholder="Minimum Currency" min="100" step="1">
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
										                                	<div class="checkbox mt-2">
										                                  
										                                    	<toggle-button :sync="true" v-model="singleRestaurantData.restaurant.is_post_paid" value="true" :width="120" :font-size="15" :color="{checked: 'green', unchecked: '#17a2b8'}" 
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
									                                  
									                                  		<multiselect v-model="singleRestaurantData.restaurantFoodObjectTags" placeholder="Select three main foods" label="name" track-by="id" :options="singleRestaurantData.allMenuCategories" :multiple="true" :max="3" :required="true">
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
									                                  
									                                		<multiselect v-model="singleRestaurantData.restaurantMealObjectTags" placeholder="Available Meals" label="name" track-by="id" :options="singleRestaurantData.allMeals" :multiple="true" :max="6" :required="true">

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
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="singleRestaurantData.step-=1">
									                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
									          
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
									                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
								                  	</button>
									          	</div>
								            </div>
									  	</div>
									</div>

									<div class="row" v-bind:key="4" v-show="singleRestaurantData.step==4"> 
									  	<div class="col-sm-12">

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
									                                  		<div class="checkbox mt-2">
									                                  
									                                    		<toggle-button value="true" :sync="true" v-model="singleRestaurantData.restaurant.has_parking" :width="120" :font-size="15" :color="{checked: 'green', unchecked: '#FF0000'}" 
									                                  			:labels="{checked: 'Available', unchecked: 'No Parking' }"/>
									                                		</div>
									                              		</div>
									                          		</div>
									                      		</div>
									                  			<div class="col-6">
									                            	<div class="row">
									                              		<label for="inputSelfService3" class="col-sm-4 col-form-label text-right">Self Service</label>
									                                
									                              		<div class="col-sm-8">
									                                  		<div class="checkbox mt-2">
									                                  
									                                    		<toggle-button value ="true" :sync="true" v-model="singleRestaurantData.restaurant.is_self_service" :width="120" :font-size="15" :color="{checked: 'green', unchecked: '#17a2b8'}" 
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
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="singleRestaurantData.step-=1">
								                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
									          
								                  	<button type="submit" class="btn btn-danger rounded-pill">
									                    <span v-if="editMode">Update</span>
														<span v-else>Create</span>
							                     		Restaurant
								                  	</button>
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
										        	<div class="progress-bar bg-success w-25" v-show="singleRestaurantData.step>=1">
										          		Profile
										        	</div>
										        	<div class="progress-bar bg-info w-25" v-show="singleRestaurantData.step>=2">
										          		Address
										        	</div>
										        	<div class="progress-bar bg-warning w-25" v-show="singleRestaurantData.step>=3">
										          		Menu Profile
										        	</div>
										        	<div class="progress-bar bg-danger w-25" v-show="singleRestaurantData.step>=4">
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
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>

			<!-- /.modal-create-restaurant-category -->
			<div class="modal fade" id="modal-create-restaurant-category">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Add More Cuisine</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="storeRestaurantCuisine" autocomplete="off" novalidate>
							<div class="modal-body text-dark">

					      		<input type="hidden" name="_token" :value="csrf">

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">New Cuisine Name</label>
									                <div class="col-sm-8">
									                  <input type="text" class="form-control" v-model="singleRestaurantData.newRestaurantCuisine.name" placeholder="Cuisine Name" required="true">
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
							  	<button type="submit" class="btn btn-outline-light">Add Cuisine</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-create-restaurant-category -->

			<div class="modal fade" id="modal-create-meal-tag">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Create New Meal</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="storeNewMeal" autocomplete="off">
							<div class="modal-body text-dark" novalidate>

					      		<input type="hidden" name="_token" :value="csrf">

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Meal Name</label>
									                <div class="col-sm-8">
									                  <input type="text" class="form-control" v-model="singleRestaurantData.newMeal.name" placeholder="Meal Name" required="true">
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
			<div class="modal fade" id="modal-create-menu-category">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Add Menu Category</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="storeMenuCategory" autocomplete="off" novalidate>
							<div class="modal-body text-dark">

					      		<input type="hidden" name="_token" :value="csrf">

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Menu Name</label>
									                <div class="col-sm-8">
									                  <input type="text" class="form-control" v-model="singleRestaurantData.newMenuCategory.name" placeholder="Menu Name" required="true">
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

			<!-- /.modal-modal-create-meal-tag -->
			<div class="modal fade" id="modal-restaurant-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyRestaurant()" autocomplete="off" novalidate>
							<div class="modal-body">
					      		<input type="hidden" name="_token" :value="csrf">
					      		<h5>Are you sure want to delete restaurant ?? </h5>
					      		<h5 class="text-secondary"><small>But once you want, you can retreive it from bin.</small></h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
							  	<button type="submit" class="btn btn-outline-light">Delete</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-modal-create-menu-category -->

			<!-- /.modal-modal-create-meal-tag -->
			<div class="modal fade" id="modal-restaurant-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreRestaurant()" autocomplete="off" novalidate>
							<div class="modal-body">
					      		<input type="hidden" name="_token" :value="csrf">
					      		<h5>Are you sure want to restore restaurant ?? </h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
							  	<button type="submit" class="btn btn-outline-light">Restore</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-modal-create-menu-category -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import { ToggleButton } from 'vue-js-toggle-button';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
	import VueGoogleAutocomplete from 'vue-google-autocomplete';
	// import VueBootstrap4Table from 'vue-bootstrap4-table';

	var singleRestaurantData = {
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

        // csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    	service_schedule : {},
		booking_break_schedule : {},
    };

	var restaurantListData = {
    	editMode : false,
    	currentTab : 'all',
    	loading : false,
    	allRestaurants : [],
    	restaurantsToShow : [],
    	pagination: {
        	current_page: 1
      	},
      	query : '',
    	queryFiled : '',
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

        singleRestaurantData : singleRestaurantData,

    	/*
    	columns : 	
    	[
            {
                label: "Name",
                name: "user_name",
                sort: true,
                filter: {
		            type: "simple",
		            placeholder: "Search Name",
		            case_sensitive: true, // "false" by default
		            showClearButton: false,
		            filterOnPressEnter: false,
		        },
            },
            {
                label: "Mobile",
                name: "mobile",
                sort: true,
                filter: {
		            type: "simple",
		            placeholder: "Search Mobile No.",
		            case_sensitive: true, // "false" by default
		            showClearButton: false,
		            filterOnPressEnter: false,
		        }
            },
            {
                label: "Email",
                name: "email",
                sort: true,
                filter: {
		            type: "simple",
		            placeholder: "Search Email",
		            case_sensitive: true, // "false" by default
		            showClearButton: false,
		            filterOnPressEnter: false,
		        }
            },
            {
                label: "Website",
                name: "website",
                sort: true,
                filter: {
		            type: "simple",
		            placeholder: "Search Website",
		            case_sensitive: true, // "false" by default
		            showClearButton: false,
		            filterOnPressEnter: false,
		        }
            },
            {
                label: "Action",
                name: "",
            }
        ],

        config: {
            card_mode:  true,
            server_mode : false,
            checkbox_rows: false,
            rows_selectable: false,

            global_search: {
                visibility: true,
                showClearButton: false,
                placeholder: "Custom Search",
            },

            // show_reset_button:  false,
            show_refresh_button:  false,
            pagination: true, 
            per_page: 20, // default 10
            pagination_info : false,
            per_page_options:  [20,  50,  100],
            num_of_visibile_pagination_buttons: 7,
        },
        */
    };

	export default {
		// Local registration of components
		components: { 
			// VueBootstrap4Table
			ToggleButton : ToggleButton, 
			Multiselect, // short form of Multiselect : Multiselect
			ckeditor: CKEditor.component,
			VueGoogleAutocomplete
		},

	    data() {
	        return restaurantListData;
		},

		created(){
			this.fetchAllRestaurants();
			this.fetchAllRestaurantCuisines();
			this.fetchAllMenuCategories();
			this.fetchAllMeals();
		},

		mounted(){
			this.enableScheduler();
		},

		watch : {
			'singleRestaurantData.restaurantCuisineObjectTags' : function(restaurantCuisineObjectTags){
				let array = [];
				$.each(restaurantCuisineObjectTags, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.singleRestaurantData.restaurant.restaurantCuisineTags = array;
			},
			'singleRestaurantData.restaurantFoodObjectTags' : function(restaurantFoodObjectTags){
				let array = [];
				$.each(restaurantFoodObjectTags, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.singleRestaurantData.restaurant.restaurantFoodTags = array;
			},
			'singleRestaurantData.restaurantMealObjectTags' : function(restaurantMealObjectTags){
				let array = [];
				$.each(restaurantMealObjectTags, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.singleRestaurantData.restaurant.restaurantMealTags = array;
			},
			'singleRestaurantData.restaurant.name' : function(val){
				if(typeof(val) !== 'undefined' && val !== null) {
					this.singleRestaurantData.restaurant.user_name = val.replace(/\s/g, '');
				}
			},
			'singleRestaurantData.service_schedule' : function(val){
				this.singleRestaurantData.restaurant.service_schedule = val;
			},
			'singleRestaurantData.booking_break_schedule' : function(val){
				this.singleRestaurantData.restaurant.booking_break_schedule = val;
			},
		},

		methods : {
			showAllRestaurants(){
				this.currentTab = 'all';
				this.restaurantsToShow = this.allRestaurants.all.data;
				this.pagination = this.allRestaurants.all;
			},
			showApprovedRestaurants(){
				this.currentTab = 'approved';
				this.restaurantsToShow = this.allRestaurants.approved.data;
				this.pagination = this.allRestaurants.approved;
			},
			showNonApprovedRestaurants(){
				this.currentTab = 'nonApproved';
				this.restaurantsToShow = this.allRestaurants.nonApproved.data;
				this.pagination = this.allRestaurants.nonApproved;
			},
			showTrashedRestaurants(){
				this.currentTab = 'trashed';
				this.restaurantsToShow = this.allRestaurants.trashed.data;
				this.pagination = this.allRestaurants.trashed;
			},
			fetchAllRestaurants(){
				this.loading = true;
				axios
					.get('/api/restaurants?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							
							this.loading = false;
							this.allRestaurants = response.data;

							if (this.currentTab=='all') {
								this.restaurantsToShow = this.allRestaurants.all.data;
								this.pagination = response.data.all;
							}else if (this.currentTab=='approved') {
								this.restaurantsToShow = this.allRestaurants.approved.data;
								this.pagination = response.data.approved;
							}else if (this.currentTab=='nonApproved') {
								this.restaurantsToShow = this.allRestaurants.nonApproved.data;
								this.pagination = response.data.nonApproved;
							}
							else {
								this.restaurantsToShow = this.allRestaurants.trashed.data;
								this.pagination = response.data.trashed;
							}

							// console.log(response);
							// console.log(this.allRestaurants);
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			reload() {
				this.fetchAllRestaurants();
				// this.query = "";
				// this.queryFiled = "name";
				// this.$snotify.success("Data Successfully Refresh", "Success");
    		},
    		showRestaurantCreateModal() {		
		    	this.editMode = false;
		    	this.singleRestaurantData.step = 1;

		    	this.singleRestaurantData.restaurant = {};
				this.singleRestaurantData.restaurantCuisineObjectTags = this.singleRestaurantData.restaurantFoodObjectTags = this.singleRestaurantData.restaurantMealObjectTags = [];

				$("#modal-restaurant").modal("show");
			},
		    storeRestaurant() {
				
				this.singleRestaurantData.restaurant.banner_preview = this.singleRestaurantData.restaurantNewBanner;
				$("#modal-restaurant").modal("hide");
				// this.restaurant.lat : null,
				// this.restaurant.lng : null,
				// this.restaurant.service_schedule : this.restaurant.service_schedule,
				// this.restaurant.booking_break_schedule : this.restaurant.booking_break_schedule,

				axios
					.post('/restaurants', this.singleRestaurantData.restaurant)
					.then(response => {
						// console.log(response.data);
						if (response.status == 200) {
							this.singleRestaurantData.restaurant = {};
							this.singleRestaurantData.restaurantCuisineObjectTags = this.singleRestaurantData.restaurantFoodObjectTags = this.singleRestaurantData.restaurantMealObjectTags = [];

							// this.allRestaurants = response.data.data;
							this.allRestaurants = response.data;
							this.restaurantsToShow = this.allRestaurants.all.data;
							this.pagination = response.data.all;
							toastr.success(response.data.success, "Success");
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
		    showRestaurantDetailModal(restaurant) {
		    	
				this.singleRestaurantData.restaurant = restaurant;
				this.singleRestaurantData.restaurantCuisineObjectTags = restaurant.restaurant_cuisines;
		    	this.singleRestaurantData.restaurantFoodObjectTags = restaurant.restaurant_menu_categories;
		    	this.singleRestaurantData.restaurantMealObjectTags = restaurant.restaurant_meal_categories;

				$("#modal-show-restaurant").modal("show");
				// console.log(restaurant);
			},
			showRestaurantEditModal(restaurant) {
				// console.log(restaurant);
				this.editMode = true;
				this.singleRestaurantData.step = 1;
				this.singleRestaurantData.restaurant = restaurant;
				
				this.singleRestaurantData.restaurantCuisineObjectTags = restaurant.restaurant_cuisines;
		    	this.singleRestaurantData.restaurantFoodObjectTags = restaurant.restaurant_menu_categories;
		    	this.singleRestaurantData.restaurantMealObjectTags = restaurant.restaurant_meal_categories;
		    	
				$("#modal-restaurant").modal("show");
			},
			updateRestaurant() {
			
				$("#modal-restaurant").modal("hide");

				this.singleRestaurantData.restaurant.banner_preview = this.singleRestaurantData.restaurantNewBanner;
				// this.restaurant.lat : null,
				// this.restaurant.lng : null,
				// this.restaurant.service_schedule : this.restaurant.service_schedule,
				// this.restaurant.booking_break_schedule : this.restaurant.booking_break_schedule,

				axios
					.put('/restaurants/'+this.singleRestaurantData.restaurant.id, this.singleRestaurantData.restaurant)
					.then(response => {
						if (response.status == 200) {
							// this.allRestaurants = response.data.data;
							this.allRestaurants = response.data;

							if (this.currentTab=='all') {
								this.restaurantsToShow = this.allRestaurants.all.data;
								this.pagination = response.data.all;
							}else if (this.currentTab=='approved') {
								this.restaurantsToShow = this.allRestaurants.approved.data;
								this.pagination = response.data.approved;
							}else {
								this.restaurantsToShow = this.allRestaurants.nonApproved.data;
								this.pagination = response.data.nonApproved;
							}

							toastr.success(response.data.success, "Updated");
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
			showRestaurantDeletionModal(restaurant) {
				this.singleRestaurantData.restaurant = restaurant;
				$("#modal-restaurant-delete-confirmation").modal("show");
			},
			destroyRestaurant() {
				
				$("#modal-restaurant-delete-confirmation").modal("hide");

				axios
					.delete('/restaurants/'+this.singleRestaurantData.restaurant.id)
					.then(response => {
						if (response.status == 200) {
							
							this.allRestaurants = response.data;

							if (this.currentTab=='all') {
								this.restaurantsToShow = this.allRestaurants.all.data;
								this.pagination = response.data.all;
							}else if (this.currentTab=='approved') {
								this.restaurantsToShow = this.allRestaurants.approved.data;
								this.pagination = response.data.approved;
							}else {
								this.restaurantsToShow = this.allRestaurants.nonApproved.data;
								this.pagination = response.data.nonApproved;
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
			showRestaurantRestoreModal(restaurant) {
				this.singleRestaurantData.restaurant = restaurant;
				$("#modal-restaurant-restore-confirmation").modal("show");
			},
			restoreRestaurant() {
				
				$("#modal-restaurant-restore-confirmation").modal("hide");

				axios
					.patch('/restaurants/'+this.singleRestaurantData.restaurant.id)
					.then(response => {
						if (response.status == 200) {
							// this.allRestaurants = response.data.data;
							this.allRestaurants = response.data;
							this.restaurantsToShow = this.allRestaurants.trashed.data;
							this.pagination = response.data.trashed;
							toastr.success(response.data.success, "Restored");
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
				// this.$Progress.start();
				axios
				.get(
				  "/api/search/customers/" +
				    this.queryFiled +
				    "/" +
				    this.query +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					// this.customers = response.data.data;
					// this.pagination = response.data.meta;
					// this.$Progress.finish();
				})
				.catch(e => {
					console.log(e);
					this.$Progress.fail();
				});
			},
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
							this.singleRestaurantData.allRestaurantCuisines = response.data;
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
							this.singleRestaurantData.allMenuCategories = response.data;
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
						// console.log('Meal Response : '+response);
						if (response.status == 200) {
							this.loading = false;
							this.singleRestaurantData.allMeals = response.data;
							// console.log('All Meals :'+this.allMeals);
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
						singleRestaurantData.service_schedule = $('#service_schedule').scheduler('val');
						// console.log(singleRestaurantData.service_schedule);
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
						singleRestaurantData.booking_break_schedule = $('#booking_break_schedule').scheduler('val');
						// console.log(singleRestaurantData.booking_break_schedule);
					  	// console.log($('#booking_break_schedule').scheduler('val'));
					},
				});
			},
			storeRestaurantCuisine(){
				$('#modal-create-restaurant-category').modal('hide');
				axios
					.post('/restaurant-cuisines', this.singleRestaurantData.newRestaurantCuisine)
					.then(response => {
						if (response.status == 200) {
							this.singleRestaurantData.newRestaurantCuisine = {};
							this.singleRestaurantData.allRestaurantCuisines = response.data;
							toastr.success(response.data.success, "Success");
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
			storeMenuCategory(){
				$('#modal-create-menu-category').modal('hide');
				axios
					.post('/menu-categories', this.singleRestaurantData.newMenuCategory)
					.then(response => {
						if (response.status == 200) {
							this.singleRestaurantData.newMenuCategory = {};
							this.singleRestaurantData.allMenuCategories = response.data;
							toastr.success(response.data.success, "Success");
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
			storeNewMeal(){
				$('#modal-create-meal-tag').modal('hide');
				axios
					.post('/meals', this.singleRestaurantData.newMeal)
					.then(response => {
						if (response.status == 200) {
							this.singleRestaurantData.newMeal = {};
							this.singleRestaurantData.allMeals = response.data;
							toastr.success(response.data.success, "Success");
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

				this.singleRestaurantData.step+=1;
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
                    this.singleRestaurantData.restaurantNewBanner = this.singleRestaurantData.restaurant.banner_preview = evnt.target.result;
                };
                reader.readAsDataURL(file);
            }

		}
  	}

</script>

<style scoped>
	/*
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
	*/
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
	
	.fade-enter-active {
  		transition: all .9s ease;
	}
	.fade-leave-active {
  		transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}
	/* .slide-fade-leave-active below version 2.1.8 */ 
	.fade-enter, .fade-leave-to {
  		transform: translateX(10px);
  		opacity: 0;
	}
	.modal-body {
		word-break: break-all;
	}
</style>