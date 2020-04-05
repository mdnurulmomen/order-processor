
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

                        	<button type="button" @click="showRestaurantCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='all' }, 'nav-link']" data-toggle="tab" @click="showAllRestaurants">All</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='approved' }, 'nav-link']" data-toggle="tab" @click="showApprovedRestaurants">Approved</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='nonApproved' }, 'nav-link']" data-toggle="tab" @click="showNonApprovedRestaurants">Non-Approved</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedRestaurants">Trashed</a>
											</li>
										</ul>
									</div>

									<div class="col-sm-6 float-right was-validated">
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
											<th scope="col">Name</th>
											<!-- <th scope="col">Email</th> -->
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
								    		<!-- <td>{{ restaurant.email }}</td> -->
								    		<td>{{ restaurant.mobile }}</td>
								    		<td>{{ restaurant.website }}</td>
								    		<td>
								      			<button type="button" @click="showRestaurantDetailModal(restaurant)" class="btn btn-info btn-sm">
								        			<i class="fas fa-eye"></i>
								        			View
								      			</button>
								      			<button type="button" @click="showRestaurantMenuList(restaurant)" class="btn btn-warning btn-sm">
								        			<i class="fas fa-object-group"></i>
								        			Menu-Items
								      			</button>
										      	<button type="button" v-show="restaurant.deleted_at === null" @click="showRestaurantEditModal(restaurant)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="restaurant.deleted_at === null"
								        			type="button"
								        			@click="showRestaurantDeletionModal(restaurant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="restaurant.deleted_at !== null"
								        			type="button"
								        			@click="showRestaurantRestoreModal(restaurant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
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
							<div class="row d-flex align-items-center align-content-center">
								<div class="col-sm-1">
									<select class="form-control" v-model="perPage" @change="changeNumberContents()">
										<option>10</option>
										<option>20</option>
										<option>30</option>
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllRestaurants() : searchData()"
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
										@paginate="query === '' ? fetchAllRestaurants() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- modal-show-restaurant -->
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
							              			Restaurant Admin :
							              		</label>
								                <div 
								                	class="col-sm-6" 
								                	:class="!singleRestaurantData.restaurantAdminObject ? 'text-danger small' : ''"
								                >
								                  	{{ !singleRestaurantData.restaurantAdminObject ? 
								                  		'No admin available or trashed' : 
								                  		singleRestaurantData.restaurantAdminObject.user_name 
								                  	}}
								                </div>
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Admin Mobile :
							              		</label>

								                <div 
								                	class="col-sm-6"
								                	:class="!singleRestaurantData.restaurantAdminObject ? 'text-danger small' : ''"
								                >
								                	{{ !singleRestaurantData.restaurantAdminObject ? 
								                  		'No admin available or trashed' : 
								                  		singleRestaurantData.restaurantAdminObject.mobile 
								                  	}}
								                </div>
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Restaurant Mobile :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.mobile}}
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
								                	
								                	<p class="small text-danger" v-show="Object.keys(singleRestaurantData.restaurantCuisineObjectTags).length === 0">
								                		No cuisine available or deleted
								                	</p>
								                	
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
								                	
								                	<p class="small text-danger" v-show="Object.keys(singleRestaurantData.restaurantFoodObjectTags).length === 0">
								                		No menu available or deleted
								                	</p>
								                	
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
								                  	
								                	<p class="small text-danger" v-show="Object.keys(singleRestaurantData.restaurantMealObjectTags).length === 0">
								                		No meal available or deleted
								                	</p>
								                	
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
							              			Parking Facility:
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.has_parking ? 'Available' : 'Not-available'}}
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
			<!-- /modal-show-restaurant -->

			<!-- modal-createOrEdit-restaurant -->
			<div class="modal fade" id="modal-createOrEdit-restaurant">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Restaurant</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">

					  		<!-- form start -->
							<form class="form-horizontal" v-on:submit.prevent=" editMode ? updateRestaurant() : storeRestaurant()" autocomplete="off" novalidate="true">

								<input type="hidden" name="_token" :value="csrf">

								<transition-group name="fade">

									<div 
										class="row" 
										v-bind:key="1" 
										v-show="!loading && step==1"
									>

									  	<div class="col-sm-12">
	  
									        <h2 class="text-center mb-4 lead">Restaurant Profile</h2>
									      
									        <div class="row">

									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">

									                      	<div class="d-flex flex-wrap align-content-center form-group row">
									                        	<div class="col-sm-8 text-center mb-2">
									                        		<picture>
									                                	<img class="img-fluid" :src="singleRestaurantData.restaurant.banner_preview" alt="Restaurant Banner" style="max-height: 200px;">
									                                </picture>
									                          	</div>
									                          	<div class="col-sm-4 align-self-center">
									                              	<div class="input-group">
									                                	<div class="custom-file">

									                                    	<input 
										                                    	type="file" 
										                                    	class="custom-file-input"
										                                    	@change="onBannerChange" 
										                                    	accept="image/*"
									                                    	>
									                                    	<label 
										                                    	class="custom-file-label"
										                                    	for="customFile"
										                                    >
									                                    		Change Banner
									                                    	</label>
										                                    
									                                	</div>
									                              	</div>
									                          	</div>
									                        </div>

									                        <hr class="mt-1">

									                        <div class="form-group row">


									                        	<div class="col-6">
									                            	<div class="row d-flex align-items-center">
									                              		<label for="inputFoodTags3" class="col-sm-4 col-form-label text-right">Restaurant Admin</label>
									                              		<div class="col-sm-6">
									                                  		
									                                  		<multiselect 
									                                  			v-model="singleRestaurantData.restaurantAdminObject"
									                                  			placeholder="Restaurant Owner" 
										                                  		label="user_name" 
										                                  		track-by="id" 
										                                  		:options="allRestaurantAdmins" 
										                                  		:required="true"
										                                  		:class="!errors.restaurant.restaurantAdminObject  ? 'is-valid' : 'is-invalid'"
										                                  		:allow-empty="false"
										                                  		selectLabel = "Press/Click"
										                                  		deselect-label="Can't remove single value"
										                                  		@close="validateFormInput('restaurant.restaurantAdminObject')"
									                                  		>
										                                	</multiselect>

									                                		<div 
											                                  	class="invalid-feedback"
											                                >
																		        {{ errors.restaurant.restaurantAdminObject }}
																		  	</div>

									                              		</div>
									                               
										                              	<div class="col-sm-2 text-right">
										                                  	<button type="button" class="btn btn-secondary btn-sm p-0" data-toggle="modal" data-target="#modal-create-restaurant-admin">
										                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
										                                    	Admin
										                                	</button>
										                              	</div> 
									                            
									                            	</div>
									                          	</div>

																<div class="col-6">
																	<div class="row">
																  		<label for="inputAdminApproval3" class="col-sm-4 col-form-label text-right">Admin Approval</label>
																  		<div class="col-sm-8">
																  			
																			<toggle-button 
									                                  			:sync="true" 
									                                  			v-model="singleRestaurantData.restaurant.admin_approval" 
									                                  			value="true" 
									                                  			:width="130"  
									                                  			:height="30" 
									                                  			:font-size="15" 
									                                  			:color="{checked: 'green', unchecked: 'red'}" 
									                                  			:labels="{checked: 'Approved', unchecked: 'Not-approved' }"
								                                  			/>
																    			
																  		</div>
																	</div>
																</div>
										                        

									                        </div>

									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="row">
									                              		<label for="inputName3" class="col-sm-4 col-form-label text-right">
									                              			Restaurant Name
									                              		</label>
									                              		<div class="col-sm-8">
									                              			
									                                  		<input 
										                                  		type="text" 
										                                  		class="form-control" 
										                                  		v-model="singleRestaurantData.restaurant.name"  
										                                  		placeholder="Restaurant Name" 
										                                  		:class="!errors.restaurant.name  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('restaurant.name')" 
									                                  		>
									                                  		<div 
										                                  		class="invalid-feedback" 
									                                  		>
																		        {{ errors.restaurant.name  }}
																		  	</div>
									                                  		
									                              		</div>
									                          		</div>
									                      		</div>

									                      		<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMobile3" class="col-sm-4 col-form-label text-right">Mobile</label>
										                              	<div class="col-sm-8">
										                              		
									                                  		<input 
										                                  		type="tel" 
										                                  		class="form-control" 
										                                  		v-model="singleRestaurantData.restaurant.mobile" 
										                                  		placeholder="Mobile" 
										                                  		:class="!errors.restaurant.mobile  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('restaurant.mobile')"
									                                  		>

									                                  		<div 
										                                  		class="invalid-feedback"
										                                  	>
																		        {{ errors.restaurant.mobile }}
																		  	</div>
										                                  		
										                              	</div>
										                            </div>
									                          	</div>
									                        </div>

									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="row d-flex align-items-center">

									                              		<label 
										                              		for="inputCusineTags3" 
										                              		class="col-sm-4 col-form-label text-right"
									                              		>
									                              			Restaurant Type
									                              		</label>
									                              		<div class="col-sm-6">

									                              			<multiselect 
									                                  			v-model="singleRestaurantData.restaurantCuisineObjectTags"
									                                  			placeholder="Restaurant Type" 
										                                  		label="name" 
										                                  		track-by="id" 
										                                  		:options="allRestaurantCuisines" 
										                                  		:multiple="true" 
										                                  		:max="3" 
										                                  		:required="true"
										                                  		:class="!errors.restaurant.restaurantCuisineObjectTags  ? 'is-valid' : 'is-invalid'" 
										                                  		:allow-empty="false"
										                                  		selectLabel = "Press/Click"
										                                  		deselect-label="Can't remove single value"
										                                  		@close="validateFormInput('restaurant.restaurantCuisineObjectTags')"
									                                  		>
										                                	</multiselect>

										                                	<div 
											                                	class="invalid-feedback"
											                                >

																		        {{ errors.restaurant.restaurantCuisineObjectTags }}

																		  	</div>

										                              	</div>
										                              	<div class="col-sm-2 text-right">
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
										                              	
									                                  	<input 
										                                  	type="url" 
										                                  	class="form-control" 
										                                  	v-model="singleRestaurantData.restaurant.website" 
										                                  	placeholder="Restaurant Website" 
										                                  	:class="!errors.restaurant.website  ? 'is-valid' : 'is-invalid'"
										                                  	@keyup="validateFormInput('restaurant.website')"
										                                >

									                                  	<div 
										                                  	class="invalid-feedback"
										                                >
																	        {{errors.restaurant.website}}
																	  	</div>
										                                  	
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

									        <div class="row mb-2">
									          	<div class="col-sm-12 text-right">
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
								                    	<i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
								                  	</button>
									          	</div>
								            </div>
									  	</div>

									</div>

									<div 
										class="row" 
										v-bind:key="2" 
										v-show="step==2"
									>
									  
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
										                    			id="restaurantEditAddress"
			                        									classname="form-control"
										                        		placeholder="Start typing"
										                        		v-on:placechanged="getAddressData"
												                        types="(cities)"
												                        country="bd"
										                    		>
										                    		</vue-google-autocomplete>

									                    			<!-- 
									                              	<input type="text" class="form-control" v-model="restaurant.lat" placeholder="Restaurant Location" required>
									                              	-->

									                          	</div>   
									                        </div>
									                          
									                        <div class="form-group row">
									                          	<label for="inputAddress3" class="col-sm-4 col-form-label text-right">Detail Address</label>
									                          	<div class="col-sm-8">

									                              	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="singleRestaurantData.restaurant.address"
										                              	:class="!errors.restaurant.address  ? 'is-valid' : 'is-invalid'"
										                              	@blur="validateFormInput('restaurant.address')"
										                            >
									                              	</ckeditor>

									                              	<div 
									                                  	class="invalid-feedback" 
									                                >
																        {{ errors.restaurant.address }}
																  	</div>
										                              	
									                          	</div>  
									                        </div>

									                    </div>
									                    <!-- /.card-body -->
									              	</div>
									              	<!-- /.card-card -->
									          	</div>  
									        </div>

									        <div class="row mb-2">
									          	<div class="col-sm-6 text-left">
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="step-=1">
									                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
								                </div>

								                <div class="col-sm-6 text-right">
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
									                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
								                  	</button>
									          	</div>
								            </div>
									  	</div>
									</div>

									<div 
										class="row" 
										v-bind:key="3" 
										v-show="step==3"
									>
									  
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
										                              		
									                                  		<input 
										                                  		type="number" 
										                                  		class="form-control" 
										                                  		v-model.number="singleRestaurantData.restaurant.min_order" 
										                                  		placeholder="Minimum Currency" 
										                                  		min="100" 
										                                  		max="5000" 
										                                  		step="1" 
										                                  		:class="!errors.restaurant.min_order  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('restaurant.min_order')"
										                                  	>
										                                  		
										                                  	<div 
											                                  	class="invalid-feedback" 
											                                >
																		        {{ errors.restaurant.min_order }}
																		  	</div>

										                              	</div>
										                          	</div>
										                      	</div>
										                  		<div class="col-6">
										                            <div class="d-flex flex-wrap align-content-center form-group row">
										                                
										                              	<div class="col-sm-4 text-right">
										                                	<label for="inputPayment3" class="col-form-label pb-0">
										                                		Payment
										                                	</label>
										                                	<div class="p-0 m-0">
										                                		(physical orders)
										                                	</div>
										                                
										                              	</div>

										                              	<div class="col-sm-8 align-self-center text-center">
										                                	<div class="checkbox">
										                                  
										                                    	<toggle-button 
											                                    	:sync="true" 
											                                    	v-model="singleRestaurantData.restaurant.is_post_paid" 
											                                    	value="true" 
											                                    	:width="120"  
											                                    	:height="30" 
											                                    	:font-size="15" 
											                                    	:color="{checked: 'green', unchecked: '#17a2b8'}" 
											                                    	:labels="{checked: 'Post-paid', unchecked: 'Pre-paid' }"
											                                    />

										                                	</div>
										                              	</div>
										                          	</div>
										                      	</div>
									                        </div>
									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="row d-flex align-items-center">
									                              		<label for="inputFoodTags3" class="col-sm-4 col-form-label text-right">Best Food Menu's</label>
									                              		<div class="col-sm-6">
									                        
									                                  		<multiselect 
									                                  			v-model="singleRestaurantData.restaurantFoodObjectTags"
									                                  			placeholder="Select three main foods" 
										                                  		label="name" 
										                                  		track-by="id" 
										                                  		:options="allMenuCategories" 
										                                  		:multiple="true" 
										                                  		:max="3" 
										                                  		:required="true"
										                                  		:class="!errors.restaurant.restaurantFoodObjectTags  ? 'is-valid' : 'is-invalid'" 
										                                  		:allow-empty="false"
										                                  		selectLabel = "Press/Click"
										                                  		deselect-label="Can't remove single value"
										                                  		@close="validateFormInput('restaurant.restaurantFoodObjectTags')"
									                                  		>

									                                		</multiselect>

									                                		<div 
											                                  	class="invalid-feedback"
											                                >
																		        {{ errors.restaurant.restaurantFoodObjectTags }}
																		  	</div>

									                              		</div>
									                               
										                              	<div class="col-sm-2 text-right">
										                                  	<button type="button" class="btn btn-secondary btn-sm p-0" data-toggle="modal" data-target="#modal-create-menu-category">
										                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
										                                    	Food
										                                	</button>
										                              	</div> 
									                            
									                            	</div>
									                          	</div>
									                          	<div class="col-6">
									                            	<div class="row d-flex align-items-center">
									                              		<label for="inputMealTags3" class="col-sm-4 col-form-label text-right">Available Meal's</label>
									                              		<div class="col-sm-6">
									                            
								                                			<multiselect 
									                                			v-model="singleRestaurantData.restaurantMealObjectTags"
									                                			placeholder="Available Meals" 
									                                			label="name" 
									                                			track-by="id" 
									                                			:options="allMeals" 
									                                			:multiple="true" 
									                                			:max="6" 
									                                			:required="true"
									                                			:class="!errors.restaurant.restaurantMealObjectTags  ? 'is-valid' : 'is-invalid'"
									                                			:allow-empty="false"
										                                  		selectLabel = "Press/Click"
										                                  		deselect-label="Can't remove single value"
									                                			@close="validateFormInput('restaurant.restaurantMealObjectTags')"
								                                			>

								                                			</multiselect>

								                                			<div 
											                                  	class="invalid-feedback"
											                                >
																		        {{ errors.restaurant.restaurantMealObjectTags }}
																		  	</div>

									                              		</div>
									                              
									                              		<div class="col-sm-2 text-right">
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

									        <div class="row mb-2">
									          	<div class="col-sm-6 text-left">
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="step-=1">
									                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
								                </div>

								                <div class="col-sm-6 text-right">
								                  	<button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" v-on:click="nextPage">
									                    <i class="fa fa-2x fa-angle-double-right" aria-hidden="true"></i>
								                  	</button>
									          	</div>
								            </div>
									  	</div>
									</div>

									<div 
										class="row" 
										v-bind:key="4" 
										v-show="step==4"
									> 
									  	<div class="col-sm-12">

									        <h2 class="text-center mb-4 lead">Service & Schedule</h2>
									      
									        <div class="row">
									          
									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">

									                        <div class="form-group row">
									                          	<div class="col-6">
									                            	<div class="d-flex flex-wrap align-content-center row">
									                              		<label for="inputParking3" class="col-sm-4 col-form-label text-right">Parking Facility</label>
									                              		<div class="col-sm-8 align-self-center text-center">
									                                  		<div class="checkbox">
									                                  
									                                    		<toggle-button 
										                                    		value="true" 
										                                    		:sync="true" 
										                                    		v-model="singleRestaurantData.restaurant.has_parking" 
										                                    		:width="120"  
										                                    		:height="30" 
										                                    		:font-size="15" 
										                                    		:color="{checked: 'green', unchecked: '#FF0000'}" 
										                                    		:labels="{checked: 'Available', unchecked: 'No Parking' }"
									                                    		/>

									                                		</div>
									                              		</div>
									                          		</div>
									                      		</div>
									                  			<div class="col-6">
									                            	<div class="d-flex flex-wrap align-content-center row">

									                              		<label for="inputSelfService3" class="col-sm-4 col-form-label text-right">Self Service</label>
									                                
									                              		<div class="col-sm-8 align-self-center text-center">
									                                  		<div class="checkbox">
									                                  
									                                    		<toggle-button 
										                                    		value ="true" 
										                                    		:sync="true" 
										                                    		v-model="singleRestaurantData.restaurant.is_self_service" 
										                                    		:width="120"  
										                                    		:height="30" 
										                                    		:font-size="15" 
										                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
										                                    		:labels="{checked: 'Yes', unchecked: 'No' }"
										                                    	/>
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
									                              <input type="text" class="form-control" v-model="restaurant.service_schedule" placeholder="Available Meals" required>
									                               -->
									                          	</div>
									                            
									                        </div>

									                        <div class="form-group row">      
									                          	<label for="inputBookingBreak3" class="col-sm-2 col-form-label text-right">Booking Breaks</label>

									                          	<div class="col-sm-10">

									                            	<table id="booking_break_schedule"></table>

									                              	<!-- 
									                              	<input type="text" class="form-control" v-model="restaurant.booking_break_schedule" placeholder="Available Meals" required>
									                    			-->

									                          	</div>      
									                        </div>
									                    </div>
									                    <!-- /.card-body -->
									              	</div>
									              	<!-- /.card-card -->
									          	</div>  
									        </div>

									        <div class="row mb-2">
									          	<div class="col-sm-4 text-left">
								                  	<button 
									                  	type="button" 
									                  	class="btn btn-outline-secondary btn-sm rounded-pill" 
									                  	v-on:click="step-=1"
								                  	>
								                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
								                </div>
								                
									          	<div class="col-sm-8 text-right">
									                <div class="text-danger small" v-show="!submitForm">
												  		Please input all required fields
										          	</div>
								                  	<button 
									                  	type="submit" 
									                  	class="btn btn-danger rounded-pill" 
									                  	:disabled="!submitForm"
								                  	>
									                    {{ editMode ? 'Update' : 'Create' }} Restaurant
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
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-createOrEdit-restaurant -->

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
					  	<form class="form-horizontal" v-on:submit.prevent="storeRestaurantCuisine" autocomplete="off">
							<div class="modal-body text-dark">

					      		<input type="hidden" name="_token" :value="csrf">

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">New Cuisine Name</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="newRestaurantCuisine.name"
										                  	placeholder="Cuisine Name"
										                  	required="true" 
										                  	:class="!errors.newRestaurantCuisine.name  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('newRestaurantCuisine.name')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.newRestaurantCuisine.name }}
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
								
				              	<button type="button" class="btn btn-outline-light" data-dismiss="modal">
				              		Close
				              	</button>
				              	<button 
								  	type="submit" 
								  	:disabled="!submitForm" 
								  	class="btn btn-outline-light">
							  		Add Cuisine
								</button>
								
				            </div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-create-restaurant-category -->

			<!-- /.modal-create-restaurant-admin -->
			<div class="modal fade" id="modal-create-restaurant-admin">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Add More Restaurant Admin</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="storeRestaurantAdmin" autocomplete="off">
							<div class="modal-body text-dark">

					      		<input type="hidden" name="_token" :value="csrf">

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
									              		
								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">User Name</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="newRestaurantAdmin.user_name"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true" 
										                  	:class="!errors.newRestaurantAdmin.user_name  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('newRestaurantAdmin.user_name')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.newRestaurantAdmin.user_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">Mobile</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="newRestaurantAdmin.mobile"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true"  
										                  	:class="!errors.newRestaurantAdmin.mobile  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('newRestaurantAdmin.mobile')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.newRestaurantAdmin.mobile }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">Email</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="newRestaurantAdmin.email"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true"  
										                  	:class="!errors.newRestaurantAdmin.email  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('newRestaurantAdmin.email')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.newRestaurantAdmin.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">Password</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="password" class="form-control" 
										                  	v-model="newRestaurantAdmin.password"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true" 
										                  	:class="!errors.newRestaurantAdmin.password  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('newRestaurantAdmin.password')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.newRestaurantAdmin.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">Repeat Password</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="password" class="form-control" 
										                  	v-model="newRestaurantAdmin.password_confirmation"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true" 
										                  	:class="!errors.newRestaurantAdmin.password_confirmation  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('newRestaurantAdmin.password_confirmation')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.newRestaurantAdmin.password_confirmation }}
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
								  	:disabled="!submitForm" 
								  	class="btn btn-outline-light">
							  		Add Restaurant Admin
								</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-create-restaurant-admin -->
			
			<!-- modal-create-meal-tag -->
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
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="storeNewMeal" 
						  	autocomplete="off"
					  	>
							<div class="modal-body text-dark">

					      		<input type="hidden" name="_token" :value="csrf">

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label for="inputMealName3" class="col-sm-4 col-form-label text-right">Meal Name</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="newMeal.name" 
															placeholder="Meal Name" 
															required="true"
															:class="!errors.newMeal.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('newMeal.name')"
									                  	>
									                  	<div class="invalid-feedback">
												        	{{ errors.newMeal.name }}
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
								  	:disabled="!submitForm" 
								  	class="btn btn-outline-light"
							  	>
							  		Add Meal
								</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-modal-create-meal-tag -->

			<!-- /.modal-modal-create-menu-category -->
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
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="storeMenuCategory" 
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
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Menu Name</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="newMenuCategory.name" 
															placeholder="Menu Name" 
															required="true"
															:class="!errors.newMenuCategory.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('newMenuCategory.name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.newMenuCategory.name }}
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
							  		:disabled="!submitForm" 
							  		class="btn btn-outline-light"
							  	>
							  		Add Food Menu
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-modal-create-menu-category -->

			<!-- modal-restaurant-delete-confirmation -->
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
					  	<form class="form-horizontal" v-on:submit.prevent="destroyRestaurant()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete restaurant ?? </h5>
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
			<!-- /modal-restaurant-delete-confirmation -->

			<!-- modal-restaurant-restore-confirmation -->
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
					  	<form class="form-horizontal" v-on:submit.prevent="restoreRestaurant()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore restaurant ?? </h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>

							  	<button 
							  		type="submit" 
							  		class="btn btn-outline-light"
							  	>
							  		Restore
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-restaurant-restore-confirmation -->

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

    	restaurant : {
    		banner_preview : null,
			name : null,
			mobile : null,
			website : null,

			address : null,
			lat : null,
			lng : null,

			min_order : 100,
			is_post_paid : false,
			
			restaurantAdmin : null,
			restaurantCuisineTags : [],
			restaurantFoodTags : [],
			restaurantMealTags : [],

			has_parking : false,
			is_self_service : false,
			service_schedule : null,
			booking_break_schedule : null,

			admin_approval : false,
    	},

		restaurantAdminObject : {},
    	restaurantFoodObjectTags : [],
    	restaurantMealObjectTags : [],
		restaurantCuisineObjectTags : [],

    	service_schedule : {},
		booking_break_schedule : {},
    };

	var restaurantListData = {
		step : 1,
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	editor: ClassicEditor,
    	
    	currentTab : 'all',
    	allRestaurants : [],
    	restaurantsToShow : [],

    	allMeals : [],
    	allMenuCategories : [],
        allRestaurantAdmins : [],
        allRestaurantCuisines : [],

    	newMeal : {
    		// name : null,
    	},
    	newMenuCategory : {
    		// name : null,
    	},
    	newRestaurantCuisine : {
    		// name : null,
    	},
    	newRestaurantAdmin : {
			// email : null,
			// mobile : null,
    		// user_name : null,
			// password : null,
			// password_confirmation : null,
    	},

    	restaurantNewBanner : null,

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		newMeal : {},
    		restaurant : {},
    		newMenuCategory : {},
			newRestaurantAdmin : {},
    		newRestaurantCuisine : {},
    	},

        singleRestaurantData : singleRestaurantData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
		// Local registration of components
		components: { 
			// VueBootstrap4Table
			Multiselect, // short form of Multiselect : Multiselect
			VueGoogleAutocomplete,
			ToggleButton : ToggleButton, 
			ckeditor: CKEditor.component,
		},

	    data() {
	        return restaurantListData;
		},

		created(){
			this.fetchAllMeals();
			this.fetchAllRestaurants();
			this.fetchAllMenuCategories();
			this.fetchAllRestaurantAdmins();
			this.fetchAllRestaurantCuisines();
		},

		mounted(){
			this.enableScheduler();
		},

		watch : {
			'singleRestaurantData.restaurantAdminObject' : function(restaurantAdminObject){

				if (restaurantAdminObject) {
					this.singleRestaurantData.restaurant.restaurantAdmin = restaurantAdminObject.id;
				}else
					this.singleRestaurantData.restaurant.restaurantAdmin = null;
			},
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
			'singleRestaurantData.service_schedule' : function(val){
				this.singleRestaurantData.restaurant.service_schedule = val;
			},
			'singleRestaurantData.booking_break_schedule' : function(val){
				this.singleRestaurantData.restaurant.booking_break_schedule = val;
			},
			query : function(val){
				if (val==='') {
					this.fetchAllRestaurants();
				}
				else 
					this.searchData();
			}
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
			showListDataForSelectedTab(){
				if (this.currentTab=='all') {
					this.restaurantsToShow = this.allRestaurants.all.data;
					this.pagination = this.allRestaurants.all;
				}else if (this.currentTab=='approved') {
					this.restaurantsToShow = this.allRestaurants.approved.data;
					this.pagination = this.allRestaurants.approved;
				}else if (this.currentTab=='nonApproved') {
					this.restaurantsToShow = this.allRestaurants.nonApproved.data;
					this.pagination = this.allRestaurants.nonApproved;
				}else {
					this.restaurantsToShow = this.allRestaurants.trashed.data;
					this.pagination = this.allRestaurants.trashed;
				}
			},
			fetchAllRestaurants(){
				this.loading = true;
				axios
					.get('/api/restaurants/'+ this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
							
						if (response.status == 200) {
							this.currentTab = 'all';
							this.allRestaurants = response.data;
							this.showListDataForSelectedTab();
							this.loading = false;

						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchAllRestaurants();
				}else
					this.searchData();
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllRestaurants();
				}else
					this.searchData();
    		},
    		showRestaurantCreateModal(restaurant) {
    			
    			this.step = 1;
    			this.editMode = false;
    			this.submitForm = true;
    			this.errors.restaurant = {};

				this.singleRestaurantData.restaurant = {};
				this.singleRestaurantData.restaurantAdminObject = {};
				// this.restaurantNewBanner = this.singleRestaurantData.restaurant.banner_preview = null;

				this.singleRestaurantData.restaurantCuisineObjectTags = 
		    	this.singleRestaurantData.restaurantFoodObjectTags = 
		    	this.singleRestaurantData.restaurantMealObjectTags = [];

				$("#modal-createOrEdit-restaurant").modal("show");
			},
		    storeRestaurant() {

				$("#modal-createOrEdit-restaurant").modal("hide");

				this.singleRestaurantData.restaurant.banner_preview = this.restaurantNewBanner;
				
				// this.restaurant.lat : null,
				// this.restaurant.lng : null,
				// this.restaurant.service_schedule : this.restaurant.service_schedule,
				// this.restaurant.booking_break_schedule : this.restaurant.booking_break_schedule,

				axios
					.post('/restaurants/'+ this.perPage, this.singleRestaurantData.restaurant)
					.then(response => {

						if (response.status == 200) {

							this.singleRestaurantData.restaurant = {};
							this.singleRestaurantData.restaurantAdminObject = {};
							// this.restaurantNewBanner = this.singleRestaurantData.restaurant.banner_preview = null;

							this.singleRestaurantData.restaurantCuisineObjectTags = this.singleRestaurantData.restaurantFoodObjectTags = this.singleRestaurantData.restaurantMealObjectTags = [];

							this.query = '';
							this.currentTab = 'all';
							this.allRestaurants = response.data;
							this.showListDataForSelectedTab();
							toastr.success(response.data.success, "Added");
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
			showRestaurantMenuList(restaurant) {
				this.$router.push({
			 		name: 'admin.restaurantMenuItem.index', 
			 		params: { restaurant : restaurant.id, restaurantName : restaurant.name }, 
				});
			},
		    showRestaurantDetailModal(restaurant) {

				this.singleRestaurantData.restaurant = restaurant;

				this.singleRestaurantData.restaurantAdminObject = restaurant.restaurant_admin;

				this.singleRestaurantData.restaurantCuisineObjectTags = restaurant.restaurant_cuisines;
		    	this.singleRestaurantData.restaurantFoodObjectTags = restaurant.restaurant_menu_categories;
		    	this.singleRestaurantData.restaurantMealObjectTags = restaurant.restaurant_meal_categories;

				$("#modal-show-restaurant").modal("show");
			},
			showRestaurantEditModal(restaurant) {

				this.step = 1;
				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurant = {};

				this.singleRestaurantData.restaurant = restaurant;

				this.singleRestaurantData.restaurantAdminObject = restaurant.restaurant_admin;

				this.singleRestaurantData.restaurantCuisineObjectTags = restaurant.restaurant_cuisines;
		    	this.singleRestaurantData.restaurantFoodObjectTags = restaurant.restaurant_menu_categories;
		    	this.singleRestaurantData.restaurantMealObjectTags = restaurant.restaurant_meal_categories;
		    	
				$("#modal-createOrEdit-restaurant").modal("show");
			},
			updateRestaurant() {
			
				$("#modal-createOrEdit-restaurant").modal("hide");

				this.singleRestaurantData.restaurant.banner_preview = this.restaurantNewBanner;
				
				// this.restaurant.lat : null,
				// this.restaurant.lng : null,
				// this.restaurant.service_schedule : this.restaurant.service_schedule,
				// this.restaurant.booking_break_schedule : this.restaurant.booking_break_schedule,

				axios
					.put('/restaurants/'+this.singleRestaurantData.restaurant.id+'/'+this.perPage, this.singleRestaurantData.restaurant)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantData.restaurant = {};
							this.singleRestaurantData.restaurantAdminObject = {};
							// this.restaurantNewBanner = this.singleRestaurantData.restaurant.banner_preview = null;

							this.singleRestaurantData.restaurantCuisineObjectTags = this.singleRestaurantData.restaurantFoodObjectTags = this.singleRestaurantData.restaurantMealObjectTags = [];

							if (this.query === '') {
								this.allRestaurants = response.data;
								this.showListDataForSelectedTab();
							}
							else
								this.searchData();

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
					.delete('/restaurants/'+this.singleRestaurantData.restaurant.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantData.restaurant = {};

							if (this.query === '') {
								this.allRestaurants = response.data;
								this.showListDataForSelectedTab();
							}
							else
								this.searchData();

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
					.patch('/restaurants/'+this.singleRestaurantData.restaurant.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {

							this.singleRestaurantData.restaurant = {};

							if (this.query === '') {
								this.allRestaurants = response.data;
								this.showListDataForSelectedTab();
							}
							else
								this.searchData();

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

				this.pagination.current_page = 1;
				
				axios
				.get(
					"/api/restaurants/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {

					this.allRestaurants = response.data;
					this.restaurantsToShow = this.allRestaurants.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			getAddressData(addressData, placeResultData, id) {
				// console.log(addressData);
			},
			fetchAllRestaurantCuisines(){
				this.loading = true;
				axios
					.get('/api/cuisines')
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
			fetchAllRestaurantAdmins(){
				this.loading = true;
				axios
					.get('/api/restaurant-admins')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurantAdmins = response.data;
							// console.log(this.allRestaurantAdmins);
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
						if (response.status == 200) {
							this.loading = false;
							this.allMeals = response.data;
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
					.post('/cuisines', this.newRestaurantCuisine)
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
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});
			},
			storeRestaurantAdmin(){

				$('#modal-create-restaurant-admin').modal('hide');

				axios
					.post('/restaurant-admins', this.newRestaurantAdmin)
					.then(response => {
						if (response.status == 200) {
							this.newRestaurantAdmin = {};
							this.allRestaurantAdmins = response.data;
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
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});
			},
			storeNewMeal(){

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
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});
			},
			nextPage (event) {
				this.checkRestaurantFormValidation();
				this.step += 1;
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'restaurant.restaurantAdminObject' :

						if (!this.singleRestaurantData.restaurantAdminObject) {
							this.errors.restaurant.restaurantAdminObject = 'Restaurant admin is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'restaurantAdminObject');
						}

						break;

					case 'restaurant.name' :

						if (!this.singleRestaurantData.restaurant.name) {
							this.errors.restaurant.name = 'Name is required';
						}
						else if (!this.singleRestaurantData.restaurant.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.restaurant.name = 'No special characters';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'name');
						}

						break;

					case 'restaurant.mobile' :

						if (!this.singleRestaurantData.restaurant.mobile) {
							this.errors.restaurant.mobile = 'Mobile is required';
						}
						else if (!this.singleRestaurantData.restaurant.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.restaurant.mobile = 'Invalid mobile number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'mobile');
						}

						break;

					case 'restaurant.restaurantCuisineObjectTags' :

						if (this.singleRestaurantData.restaurantCuisineObjectTags.length===0) {
							this.errors.restaurant.restaurantCuisineObjectTags = 'Type is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'restaurantCuisineObjectTags');
						}

						break;

					case 'restaurant.website' :

						if (this.singleRestaurantData.restaurant.website && !this.singleRestaurantData.restaurant.website.match(/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/g)) {
							this.errors.restaurant.website = 'Invalid url';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'website');
						}

						break;

					case 'restaurant.address' :

						if (!this.singleRestaurantData.restaurant.address) {
							this.errors.restaurant.address = 'Address is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'address');
						}

						break;

					case 'restaurant.min_order' :

						if (!this.singleRestaurantData.restaurant.min_order) {
							this.errors.restaurant.min_order = 'Minimum order is required';
						}
						else if (this.singleRestaurantData.restaurant.min_order < 100 || this.singleRestaurantData.restaurant.min_order > 5000) {
							this.errors.restaurant.min_order = 'Value should be between 100 and 5000';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'min_order');
						}

						break;

					case 'restaurant.restaurantFoodObjectTags' :

						if (this.singleRestaurantData.restaurantFoodObjectTags.length===0) {
							this.errors.restaurant.restaurantFoodObjectTags = 'Food tag is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'restaurantFoodObjectTags');
						}

						break;

					case 'restaurant.restaurantMealObjectTags' :

						if (this.singleRestaurantData.restaurantMealObjectTags.length===0) {
							this.errors.restaurant.restaurantMealObjectTags = 'Meal tag is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'restaurantMealObjectTags');
						}

						break;

					case 'newRestaurantCuisine.name' :

						if (!this.newRestaurantCuisine.name) {
							this.errors.newRestaurantCuisine.name = 'Cuisine name is required';
						}
						else if (!this.newRestaurantCuisine.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.newRestaurantCuisine.name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newRestaurantCuisine, 'name');
						}

						break;

					case 'newMeal.name' :

						if (!this.newMeal.name) {
							this.errors.newMeal.name = 'Meal name is required';
						}
						else if (!this.newMeal.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.newMeal.name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newMeal, 'name');
						}

						break;

					case 'newMenuCategory.name' :

						if (!this.newMenuCategory.name) {
							this.errors.newMenuCategory.name = 'Menu category name is required';
						}
						else if (!this.newMenuCategory.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.newMenuCategory.name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newMenuCategory, 'name');
						}

						break;

					case 'newRestaurantAdmin.user_name' :

						if (!this.newRestaurantAdmin.user_name) {
							this.errors.newRestaurantAdmin.user_name = 'Username is required';
						}
						else if (!this.newRestaurantAdmin.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.newRestaurantAdmin.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newRestaurantAdmin, 'user_name');
						}

						break;

					case 'newRestaurantAdmin.email' :

						if (!this.newRestaurantAdmin.email) {
							this.errors.newRestaurantAdmin.email = 'Email is required';
						}
						else if (!this.newRestaurantAdmin.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.newRestaurantAdmin.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newRestaurantAdmin, 'email');
						}

						break;

					case 'newRestaurantAdmin.mobile' :

						if (!this.newRestaurantAdmin.mobile) {
							this.errors.newRestaurantAdmin.mobile = 'Mobile is required';
						}
						else if (!this.newRestaurantAdmin.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.newRestaurantAdmin.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newRestaurantAdmin, 'mobile');
						}

						break;

					case 'newRestaurantAdmin.password' :

						if (this.newRestaurantAdmin.password && this.newRestaurantAdmin.password.length < 8) {
							this.errors.newRestaurantAdmin.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newRestaurantAdmin, 'password');
						}

						break;

					case 'newRestaurantAdmin.password_confirmation' :

						if (this.newRestaurantAdmin.password && this.newRestaurantAdmin.password !== this.newRestaurantAdmin.password_confirmation) {
							this.errors.newRestaurantAdmin.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.newRestaurantAdmin, 'password_confirmation');
						}

						break;

				}
					 
			},
			checkRestaurantFormValidation(){

				if (this.singleRestaurantData.restaurant.restaurantAdmin && this.singleRestaurantData.restaurant.name && this.singleRestaurantData.restaurant.mobile && this.singleRestaurantData.restaurant.restaurantCuisineTags.length > 0 && this.singleRestaurantData.restaurant.address && this.singleRestaurantData.restaurant.min_order && this.singleRestaurantData.restaurant.restaurantFoodTags.length > 0 && this.singleRestaurantData.restaurant.restaurantMealTags.length > 0 && Object.keys(this.errors.restaurant).length === 0) {
					
					this.submitForm = true;
				}
				else
					this.submitForm = false;
			},
			onBannerChange(evnt){

		      	let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.createImage(files[0]);
		      	}
		      	else
		      		this.singleRestaurantData.restaurant.banner_preview = this.restaurantNewBanner = null;

		      	return;
			},
			createImage(file) {
                let reader = new FileReader();
                reader.onload = (evnt) => {
                    this.singleRestaurantData.restaurant.banner_preview = this.restaurantNewBanner = evnt.target.result;
                };
                reader.readAsDataURL(file);
            }

		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
	
	.fade-enter-active {
  		transition: all .9s ease;
	}
	.fade-leave-active {
  		transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
	}
	.fade-enter, .fade-leave-to {
  		transform: translateX(10px);
  		opacity: 0;
	}
	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}
</style>