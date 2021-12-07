
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
								Restaurant List
							</h2>

                        	<button 
	                        	type="button" 
	                        	@click="showRestaurantCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul 
										  	class="nav nav-tabs mb-2" 
										  	v-show="query === ''"
									  	>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='all' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showAllRestaurants"
												>
													All
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='approved' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showApprovedRestaurants"
												>
													Approved
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='nonApproved' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showNonApprovedRestaurants"
												>
													Non-Approved
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedRestaurants"
												>
													Trashed
												</a>
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
											<th scope="col">Status</th>
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
								    		<td>{{ restaurant.mobile }}</td>
								    		<td>
								    			{{ 
								    				restaurant.website || 'No Website' 
								    			}}
								    		</td>
								    		<td>
								    			<span :class="[restaurant.admin_approval ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					restaurant.admin_approval ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>		
								    		</td>
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showRestaurantDetailModal(restaurant)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			View
								      			</button>

										      	<button 
											      	type="button" 
											      	class="btn btn-primary btn-sm"
											      	v-show="restaurant.deleted_at === null" 
											      	@click="showRestaurantEditModal(restaurant)" 
										      	>
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

								      			<button
								        			type="button"
								        			class="btn btn-danger btn-sm"
								        			v-show="restaurant.deleted_at === null"
								        			@click="showRestaurantDeletionModal(restaurant)"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>

								      			<button
								        			type="button"
								        			class="btn btn-danger btn-sm"
								        			v-show="restaurant.deleted_at !== null && restaurant.admin !== null"
								        			@click="showRestaurantRestoreModal(restaurant)"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>

								      			<button 
									      			type="button" 
									      			class="btn btn-dark btn-sm"
									      			@click="showRestaurantMenuList(restaurant)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Menu
								      			</button>

								      			<p 	
								      				class="text-danger" 
								      				v-show="restaurant.deleted_at !== null && restaurant.admin === null"
								      			>
								      				Trashed Admin
								      			</p>
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!restaurantsToShow.length"
									  	>
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No data found.
									      		</div>
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
						  	<h4 class="modal-title">
						  		Restaurant Details
						  	</h4>
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
								                  		'Admin has been trashed' : 
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
								                  		'Admin has been trashed' : 
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
								                	<span :class="[singleRestaurantData.restaurant.admin_approval ? 'badge-success' : 'badge-danger', 'badge']">
								                		{{ singleRestaurantData.restaurant.admin_approval ? 'Approved' : 'Not-Approved'}}
								                	</span>
								                </div>	
								            </div>

								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Taking Order :
							              		</label>
								                <div class="col-sm-6">
								                	<span :class="[singleRestaurantData.restaurant.taking_order ? 'badge-success' : 'badge-danger', 'badge']">
								                		{{ singleRestaurantData.restaurant.taking_order ? 'Yes' : 'No'}}
								                	</span>
								                </div>	
								            </div> 

								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Sponsored :
							              		</label>
								                <div class="col-sm-6">
								                	<span :class="[singleRestaurantData.restaurant.sponsored ? 'badge-danger' : 'badge-success', 'badge']">
								                		{{ singleRestaurantData.restaurant.sponsored ? 'Sponsored' : 'Not-Sponsored'}}
								                	</span>
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

								<div id="service" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Maximum Booking Seats :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleRestaurantData.restaurant.max_booking || 'No Booking'}}
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
							              			Service :
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
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Restaurant
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">

					  		<!-- form start -->
							<form 
								class="form-horizontal" 
								v-on:submit.prevent="editMode ? updateRestaurant() : storeRestaurant()" 
								autocomplete="off" 
								novalidate="true"
							>
								<input type="hidden" name="_token" :value="csrf">

								<div v-show="! loading" class="row">
									<div class="col-sm-12">
									  	<div class="card">
										    <div class="card-header text-center">

										      	<div class="progress">
										        	<div class="progress-bar bg-success w-50" v-show="step>=1">
										          		Profile
										        	</div>
										        	<div class="progress-bar bg-info w-25" v-show="step>=2">
										          		Address
										        	</div>
										        	<div class="progress-bar bg-danger w-25" v-show="step>=3">
										          		Service & Schedule
										        	</div>
										      	</div>

										    </div>
									  	</div>
									</div>
								</div>

								<transition-group name="fade">
									<div 
										class="row" 
										v-bind:key="1" 
										v-show="!loading && step==1"
									>
									  	<div class="col-sm-12">
	  
									        <h2 class="text-center mb-4 lead">
									        	Restaurant Profile
									        </h2>
									      
									        <div class="row">

									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">

									                      	<div class="d-flex flex-wrap align-content-center form-group row">
									                        	<div class="col-sm-8 text-center mb-2">
									                        		<picture>
									                                	<img 
									                                		class="img-fluid" 
									                                		:src="singleRestaurantData.restaurant.banner_preview" 
									                                		alt="Restaurant Banner" 
									                                		style="max-height: 200px;"
									                                	>
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

									                        <div class="form-group row text-center">
									                        	<div class="col-sm-4">
									                        		<div class="form-group">
																  		<label for="inputAdminApproval3">
																  			Admin Approval
																  		</label>
																  		<div>
																			<toggle-button 
									                                  			:sync="true" 
									                                  			v-model="singleRestaurantData.restaurant.admin_approval" 
									                                  			value="true" 
									                                  			:width="140"  
									                                  			:height="30" 
									                                  			:font-size="15" 
									                                  			:color="{checked: 'green', unchecked: 'red'}" 
									                                  			:labels="{checked: 'Approved', unchecked: 'Not-approved' }"
								                                  			/>		
																  		</div>
									                        		</div>
									                        	</div>

									                        	<div class="col-sm-4">
									                        		<div class="form-group">
																  		<label for="inputAdminApproval3">
																  			Taking Order
																  		</label>
																  		<div>
																			<toggle-button 
									                                  			:sync="true" 
									                                  			v-model="singleRestaurantData.restaurant.taking_order" 
									                                  			value="true" 
									                                  			:width="140"  
									                                  			:height="30" 
									                                  			:font-size="15" 
									                                  			:color="{checked: 'green', unchecked: 'red'}" 
									                                  			:labels="{checked: 'Yes', unchecked: 'No' }"
								                                  			/>		
																  		</div>
									                        		</div>
									                        	</div>

									                        	<div class="col-sm-4">
									                        		<div class="form-group">
																  		<label for="inputAdminApproval3">
																  			Sponsored
																  		</label>
																  		<div>
																			<toggle-button 
									                                  			:sync="true" 
									                                  			v-model="singleRestaurantData.restaurant.sponsored" 
									                                  			value="true" 
									                                  			:width="140"  
									                                  			:height="30" 
									                                  			:font-size="15" 
									                                  			:color="{checked: 'red', unchecked: 'green'}" 
									                                  			:labels="{checked: 'Sponsored', unchecked: 'Not-Sponsored' }"
								                                  			/>		
																  		</div>
									                        		</div>
									                        	</div>
															</div>

									                        <hr class="mt-1">

									                        <div class="form-group row">
									                        	<div class="col-6">
									                            	<div class="row d-flex align-items-center">
									                              		<label for="inputFoodTags3" class="col-sm-4 col-form-label text-right">
									                              			Restaurant Admin
									                              		</label>
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
									                                  		<div class="invalid-feedback">
																		        {{ errors.restaurant.name  }}
																		  	</div>
									                                  		
									                              		</div>
									                          		</div>
																</div>
									                        </div>

									                        <div class="form-group row">
									                      		<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMobile3" class="col-sm-4 col-form-label text-right">
										                              		Rest. Mobile
										                              	</label>
										                              	<div class="col-sm-8">
									                                  		<input 
										                                  		type="tel" 
										                                  		class="form-control" 
										                                  		v-model="singleRestaurantData.restaurant.mobile" 
										                                  		placeholder="Mobile" 
										                                  		:class="!errors.restaurant.mobile  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('restaurant.mobile')"
									                                  		>

									                                  		<div class="invalid-feedback"
										                                  	>
																		        {{ errors.restaurant.mobile }}
																		  	</div>
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

									                        <div class="form-group row">
									                          	<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMinOrder3" class="col-sm-4 col-form-label text-right">
										                              		Min. Order
										                              	</label>
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
										                                  		
										                                  	<div class="invalid-feedback">
																		        {{ errors.restaurant.min_order }}
																		  	</div>

										                              	</div>
										                          	</div>
										                      	</div>
									                          	<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMaxBooking3" class="col-sm-4 col-form-label text-right">
										                              		Max Booking
										                              	</label>
										                              	<div class="col-sm-8">
										                              		
									                                  		<input 
										                                  		type="number" 
										                                  		class="form-control" 
										                                  		v-model.number="singleRestaurantData.restaurant.max_booking" 
										                                  		placeholder="Max Booking Seats"
										                                  		min="0" 
										                                  		max="1000" 
										                                  		step="1" 
										                                  		:class="!errors.restaurant.max_booking  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('restaurant.max_booking')"
										                                  	>
										                                  		
										                                  	<div class="invalid-feedback">
																		        {{ errors.restaurant.max_booking }}
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
									          		<div class="text-danger small" v-show="!submitForm">
												  		Please input required fields
										          	</div>
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
									                          <label for="inputLocation3" class="col-sm-4 col-form-label text-right">
									                          	Restaurant Location
									                          </label>
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
									                          	<label for="inputAddress3" class="col-sm-4 col-form-label text-right">
									                          		Detail Address
									                          	</label>
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

									                        <div class="form-group row">
									                          	<label for="inputAddress3" class="col-sm-4 col-form-label text-right">
									                          		Payment (cash)
									                          	</label>
									                          	<div class="col-sm-8 align-self-center">
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

									                        <div class="form-group row">
									                          	<label for="inputAddress3" class="col-sm-4 col-form-label text-right">
									                          		Parking
									                          	</label>
									                          	<div class="col-sm-8 align-self-center">
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

									                        <div class="form-group row">
									                          	<label for="inputAddress3" class="col-sm-4 col-form-label text-right">
									                          		Self Service
									                          	</label>
									                          	<div class="col-sm-8 align-self-center">
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
									                    <!-- /.card-body -->
									              	</div>
									              	<!-- /.card-card -->
									          	</div>  
									        </div>

									        <div class="row mb-2 mt-2">
									          	<div class="col-6 text-left">
								                  	<button type="button" 
								                  		class="btn btn-outline-secondary btn-sm rounded-pill" 
								                  		v-on:click="step-=1"
								                  	>
									                    <i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
								                </div>

								                <div class="col-6 text-right">
								                	<div class="text-danger small" 
								                		v-show="!submitForm"
								                	>
												  		Please input required fields
										          	</div>
								                  	<button type="button" 
								                  		class="btn btn-outline-secondary btn-sm rounded-pill" 
								                  		v-on:click="nextPage"
								                  	>
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

									        <h2 class="text-center mb-4 lead">
									        	Service & Schedule
									        </h2>
									      
									        <div class="row">
									          
									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">
									                        <div class="form-group row mb-4">
									                          	<label for="inputServiceSchedule3" class="col-sm-2 col-form-label text-right">
									                          		Service Schedule
									                          	</label>
									                          	<div class="col-sm-10">
																	<div 
																		class="form-group row border border-dark" 
																		v-for="dayName in restaurantWeekDays"
																	>
																		<label class="col-sm-4 col-form-label text-center">
																      		{{ dayName | capitalize }}
																      	</label>
																      	
																        <div class="col-sm-8">
																        	<div class="row d-flex mb-4 mt-2 border-top" 
																        		v-for="(schedule, index) in service_schedule[dayName].schedules"
																        	>
																        		<div class="col-sm-12 text-right"
																        			v-show="service_schedule[dayName].schedules.length >= 2 && index === (service_schedule[dayName].schedules.length-1)"
																        		>
																	        		<button 
																		        		type="button" 
																		        		class="pr-1 close" 
																		        		@click="service_schedule[dayName].schedules.pop();" 
																		        		aria-label="Close"
																	        		>
																				    	<span aria-hidden="true" class="text-danger">&times;</span>
																					</button>
																        		</div>

																        		<div class="col-sm-4">
																        			<span>
																              			Start Time
																              		</span>
																					             	
																				  	<multiselect v-model="service_schedule[dayName].schedules[index].start_time" 
																				  		:options="restaurantScheduleHours" 
																				  		:key="index" 
																				  		:show-labels="false" 
																                  		:allow-empty="false"
																				  		placeholder="Start Time" 
																				  		selectLabel = "Click to select"
																				  		deselect-label="Can't remove"
																						>
																					</multiselect>
																        		</div>

																        		<div class="col-sm-4">
																        			<span>
																              			End Time
																              		</span>
																					
																				  	<multiselect v-model="service_schedule[dayName].schedules[index].end_time" 
																				  		:options="restaurantScheduleHours" 
																				  		:key="index" 
																				  		:show-labels="false" 
																                  		:allow-empty="false" 
																				  		placeholder="End Time" 
																				  		selectLabel = "Click to select"
																				  		deselect-label="Can't remove"
																						>
																					</multiselect>
																        		</div>

																        		<div class="col-sm-4 align-self-center text-center">
																        			<i 	class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" 
																		        		aria-hidden="true" 
																			        	@click="service_schedule[dayName].schedules.push({ 'start_time' : '10 am','end_time' : '10 pm', })"
																		        	>	
																		        	</i>
																          		
																		        	<i 	class="fas fa-times-circle fa-2x rounded-circle bg-info" 
																		        		aria-hidden="true" 
																		        		@click="service_schedule[dayName].schedules.pop();" 
																          				v-show="service_schedule[dayName].schedules.length >= 2 && index === (service_schedule[dayName].schedules.length-1)"
																		        	>	
																		        	</i>
																        		</div>
																        	</div>			                  	
																        </div>
																  	</div>
									                          	</div> 
									                        </div>

									                        <div class="form-group row mb-4">      
									                          	<label for="inputBookingBreak3" class="col-sm-2 col-form-label text-right">
									                          		Booking Breaks
									                          	</label>

									                          	<div class="col-sm-10">
															    	<div 
															    		class="form-group row border border-dark" 
															    		v-for="dayName in restaurantWeekDays"
															    	>
															    		<label class="col-sm-4 col-form-label text-center">
														              		{{ dayName | capitalize }}
														              	</label>

														                <div class="col-sm-8">
														                	<div class="row d-flex mb-4 mt-2 border-top" 
														                		v-for="(schedule, index) in booking_break_schedule[dayName].schedules"
														                	>
														                		<div class="col-sm-12 text-right"
																        			v-show="booking_break_schedule[dayName].schedules.length >= 2 && index === (booking_break_schedule[dayName].schedules.length-1)"
																        		>
																	        		<button 
																		        		type="button" 
																		        		class="pr-1 close" 
																		        		@click="booking_break_schedule[dayName].schedules.pop();" 
																		        		aria-label="Close"
																	        		>
																				    	<span aria-hidden="true" class="text-danger">&times;</span>
																					</button>
																        		</div>

														                		<div class="col-sm-4">
														                			<span>
																              			Start Time
																              		</span>
																					             	
																				  	<multiselect v-model="booking_break_schedule[dayName].schedules[index].start_time" 
																				  		placeholder="Start Time" 
																				  		:options="restaurantScheduleHours" 
																				  		:key="index" 
																				  		:show-labels="false" 
														                      			:allow-empty="false" 
																				  		selectLabel = "Click to select"
																				  		deselect-label="Can't remove"
																						>
																					</multiselect>
														                		</div>

														                		<div class="col-sm-4">
														                			<span>
																              			End Time
																              		</span>
																					
																				  	<multiselect v-model="booking_break_schedule[dayName].schedules[index].end_time" 
																				  		placeholder="End Time" 
																				  		:options="restaurantScheduleHours" 
																				  		:key="index" 
																				  		:show-labels="false" 
														                      			:allow-empty="false"  
																				  		selectLabel = "Click to select"
																				  		deselect-label="Can't remove"
																						>
																					</multiselect>
														                		</div>

														                		<div class="col-sm-4 align-self-center text-center">
														                			<i 	class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" 
																		        		aria-hidden="true" 
																			        	@click="booking_break_schedule[dayName].schedules.push({ 'start_time' : 'NA','end_time' : 'NA', })"
																		        	>	
																		        	</i>
															              		
																		        	<i 	class="fas fa-times-circle fa-2x  rounded-circle bg-info" aria-hidden="true" 
																		        		@click="booking_break_schedule[dayName].schedules.pop();" 
																          				v-show="booking_break_schedule[dayName].schedules.length >= 2 && index === (booking_break_schedule[dayName].schedules.length-1)"
																		        	>	
																		        	</i>
														                		</div>
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

									        <div class="row mb-2 mt-2">
									          	<div class="col-6 text-left">
								                  	<button 
									                  	type="button" 
									                  	class="btn btn-outline-secondary btn-sm rounded-pill" 
									                  	v-on:click="step-=1"
								                  	>
								                    	<i class="fa fa-2x fa-angle-double-left" aria-hidden="true"></i>
								                  	</button>
								                </div>
								                
									          	<div class="col-6 text-right">
									                <div class="text-danger small" v-show="!submitForm">
												  		Please input required fields
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
							</form>
							<!-- form end -->
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-createOrEdit-restaurant -->

			<!-- /.modal-create-restaurant-admin -->
			<div class="modal fade" id="modal-create-restaurant-admin">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Add Restaurant Admin</h4>
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
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">
								              			User Name
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="restaurantNewAdmin.user_name"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true" 
										                  	:class="!errors.restaurantNewAdmin.user_name  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('restaurantNewAdmin.user_name')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.restaurantNewAdmin.user_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">
								              			Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="restaurantNewAdmin.mobile"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true"  
										                  	:class="!errors.restaurantNewAdmin.mobile  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('restaurantNewAdmin.mobile')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.restaurantNewAdmin.mobile }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="restaurantNewAdmin.email"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true"  
										                  	:class="!errors.restaurantNewAdmin.email  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('restaurantNewAdmin.email')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.restaurantNewAdmin.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="password" class="form-control" 
										                  	v-model="restaurantNewAdmin.password"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true" 
										                  	:class="!errors.restaurantNewAdmin.password  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('restaurantNewAdmin.password')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.restaurantNewAdmin.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-right">
								              			Repeat Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="password" class="form-control" 
										                  	v-model="restaurantNewAdmin.password_confirmation"
										                  	placeholder="Restaurant Admin Name" 
										                  	required="true" 
										                  	:class="!errors.restaurantNewAdmin.password_confirmation  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('restaurantNewAdmin.password_confirmation')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.restaurantNewAdmin.password_confirmation }}
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
								  		{{ editMode ? 'Update' : 'Create' }} Restaurant Admin
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-create-restaurant-admin -->

			<!-- modal-restaurant-delete-confirmation -->
			<div class="modal fade" id="modal-restaurant-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Restaurant Deletion
						  	</h4>
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
					      		<h5>Are you sure want to delete restaurant ??</h5>
					      		<h5 style="color:#c6cacc">
					      			<small>
					      				But once you want, you can retreive it from bin.
					      			</small>
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
			<!-- /modal-restaurant-delete-confirmation -->

			<!-- modal-restaurant-restore-confirmation -->
			<div class="modal fade" id="modal-restaurant-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Restaurant Restoration
						  	</h4>
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
					      		<h5>Are you sure want to restore restaurant ??</h5>
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
			
			restaurant_admins_id : null,

			has_parking : false,
			is_self_service : false,

			admin_approval : false,
    	},

		restaurantAdminObject : {},
    	restaurantFoodObjectTags : [],
    	restaurantMealObjectTags : [],
		restaurantCuisineObjectTags : [],
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
    	
        allRestaurantAdmins : [],

        restaurantScheduleHours : ['NA', '6 am', '7 am', '8 am', '9 am', '10 am', '11 am', '12 pm', '1 pm', '2 pm', '3 pm', '4 pm', '5 pm', '6 pm', '7 pm', '8 pm', '9 pm', '10 pm', '11 pm', '12 am', '1 am', '2 am'],
    	
    	restaurantNewAdmin : {
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
    		restaurant : {},
			restaurantNewAdmin : {},
    	},

        singleRestaurantData : singleRestaurantData,
			
        restaurantWeekDays : ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'],

        service_schedule : {
			sat : {
				schedules : [
					{
						start_time : '10 am',
						end_time : '10 pm',
					},
				]
			},
			sun : {
				schedules : [
					{
						start_time : '10 am',
						end_time : '10 pm',
					},
				]
			},
			mon : {
				schedules : [
					{
						start_time : '10 am',
						end_time : '10 pm',
					},
				]
			},
			tue : {
				schedules : [
					{
						start_time : '10 am',
						end_time : '10 pm',
					},
				]
			},
			wed : {
				schedules : [
					{
						start_time : '10 am',
						end_time : '10 pm',
					},
				]
			},
			thu : {
				schedules : [
					{
						start_time : '10 am',
						end_time : '10 pm',
					},
				]
			},
			fri : {
				schedules : [
					{
						start_time : '10 am',
						end_time : '10 pm',
					},
				]
			},
		},

		booking_break_schedule : {
			sat : {
				schedules : [
					{
						start_time : 'NA',
						end_time : 'NA',
					},
				]
			},
			sun : {
				schedules : [
					{
						start_time : 'NA',
						end_time : 'NA',
					},
				]
			},
			mon : {
				schedules : [
					{
						start_time : 'NA',
						end_time : 'NA',
					},
				]
			},
			tue : {
				schedules : [
					{
						start_time : 'NA',
						end_time : 'NA',
					},
				]
			},
			wed : {
				schedules : [
					{
						start_time : 'NA',
						end_time : 'NA',
					},
				]
			},
			thu : {
				schedules : [
					{
						start_time : 'NA',
						end_time : 'NA',
					},
				]
			},
			fri : {
				schedules : [
					{
						start_time : 'NA',
						end_time : 'NA',
					},
				]
			},
		},

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
			this.fetchAllRestaurants();
			this.fetchAllRestaurantAdmins();
		},

		watch : {
			'singleRestaurantData.restaurantAdminObject' : function(restaurantAdminObject){

				if (restaurantAdminObject) {
					this.singleRestaurantData.restaurant.restaurant_admins_id = restaurantAdminObject.id;
				}else
					this.singleRestaurantData.restaurant.restaurant_admins_id = null;
			},
			query : function(val){
				if (val==='') {
					this.fetchAllRestaurants();
				}
				else 
					this.searchData();
			}
		},

		filters: {
		  	capitalize: function (value) {
			    if (!value) return ''
			    value = value.toString()
			    return value.charAt(0).toUpperCase() + value.slice(1)
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
    		showRestaurantDetailModal(restaurant) {

				this.singleRestaurantData.restaurant = restaurant;
				this.singleRestaurantData.restaurant.max_booking = restaurant.booking ? restaurant.booking.total_seat : 0;

				this.singleRestaurantData.restaurantAdminObject = restaurant.admin;

				$("#modal-show-restaurant").modal("show");
			},
    		showRestaurantCreateModal(restaurant) {
    			
    			this.step = 1;
    			this.editMode = false;
    			this.submitForm = true;
    			this.errors.restaurant = {};

				this.singleRestaurantData.restaurant = {};
				this.singleRestaurantData.restaurantAdminObject = {};

				this.service_schedule = {
					sat : {
						schedules : [
							{
								start_time : '10 am',
								end_time : '10 pm',
							},
						]
					},
					sun : {
						schedules : [
							{
								start_time : '10 am',
								end_time : '10 pm',
							},
						]
					},
					mon : {
						schedules : [
							{
								start_time : '10 am',
								end_time : '10 pm',
							},
						]
					},
					tue : {
						schedules : [
							{
								start_time : '10 am',
								end_time : '10 pm',
							},
						]
					},
					wed : {
						schedules : [
							{
								start_time : '10 am',
								end_time : '10 pm',
							},
						]
					},
					thu : {
						schedules : [
							{
								start_time : '10 am',
								end_time : '10 pm',
							},
						]
					},
					fri : {
						schedules : [
							{
								start_time : '10 am',
								end_time : '10 pm',
							},
						]
					},
				};

				this.booking_break_schedule = {
					sat : {
						schedules : [
							{
								start_time : 'NA',
								end_time : 'NA',
							},
						]
					},
					sun : {
						schedules : [
							{
								start_time : 'NA',
								end_time : 'NA',
							},
						]
					},
					mon : {
						schedules : [
							{
								start_time : 'NA',
								end_time : 'NA',
							},
						]
					},
					tue : {
						schedules : [
							{
								start_time : 'NA',
								end_time : 'NA',
							},
						]
					},
					wed : {
						schedules : [
							{
								start_time : 'NA',
								end_time : 'NA',
							},
						]
					},
					thu : {
						schedules : [
							{
								start_time : 'NA',
								end_time : 'NA',
							},
						]
					},
					fri : {
						schedules : [
							{
								start_time : 'NA',
								end_time : 'NA',
							},
						]
					},
				};

				$("#modal-createOrEdit-restaurant").modal("show");
			},
		    storeRestaurant() {

		    	if (this.step < 3) {

		    		this.nextPage();
		    		return;

		    	}

				$("#modal-createOrEdit-restaurant").modal("hide");

				this.singleRestaurantData.restaurant.banner_preview = this.restaurantNewBanner;

				this.singleRestaurantData.restaurant.service_schedule = this.service_schedule;
				this.singleRestaurantData.restaurant.booking_break_schedule = this.booking_break_schedule;
				
				// this.restaurant.lat : null,
				// this.restaurant.lng : null,

				axios
					.post('/restaurants/'+ this.perPage, this.singleRestaurantData.restaurant)
					.then(response => {

						if (response.status == 200) {

							this.singleRestaurantData.restaurant = {};
							this.singleRestaurantData.restaurantAdminObject = {};

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
			showRestaurantEditModal(restaurant) {

				this.step = 1;
				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurant = {};

				this.singleRestaurantData.restaurant = restaurant;
				this.singleRestaurantData.restaurant.max_booking = restaurant.booking ? restaurant.booking.total_seat : 0;

				this.service_schedule  = JSON.parse(restaurant.service_schedule);
				this.booking_break_schedule = JSON.parse(restaurant.booking_break_schedule);

				this.singleRestaurantData.restaurantAdminObject = restaurant.admin;
		    	
				$("#modal-createOrEdit-restaurant").modal("show");
			},
			updateRestaurant() {
			
				$("#modal-createOrEdit-restaurant").modal("hide");

				this.singleRestaurantData.restaurant.banner_preview = this.restaurantNewBanner;
				
				this.singleRestaurantData.restaurant.service_schedule = this.service_schedule;
				this.singleRestaurantData.restaurant.booking_break_schedule = this.booking_break_schedule;

				// this.restaurant.lat : null,
				// this.restaurant.lng : null,

				axios
					.put('/restaurants/'+this.singleRestaurantData.restaurant.id+'/'+this.perPage, this.singleRestaurantData.restaurant)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantData.restaurant = {};
							this.singleRestaurantData.restaurantAdminObject = {};

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
			showRestaurantMenuList(restaurant) {
				this.$router.push({
			 		name: 'restaurant-menu-items', 
			 		params: { restaurantId : restaurant.id, restaurantName : restaurant.name }, 
				});
			},
			getAddressData(addressData, placeResultData, id) {
				// console.log(addressData);
			},
			storeRestaurantAdmin(){

				$('#modal-create-restaurant-admin').modal('hide');

				axios
					.post('/restaurant-admins', this.restaurantNewAdmin)
					.then(response => {
						if (response.status == 200) {
							this.restaurantNewAdmin = {};
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
			nextPage (event) {

				if (this.step===1) {

					this.validateFormInput ('restaurant.restaurantAdminObject');
					this.validateFormInput ('restaurant.name');
					this.validateFormInput ('restaurant.mobile');
					this.validateFormInput ('restaurant.min_order');
					this.validateFormInput ('restaurant.max_booking');
					this.validateFormInput ('restaurant.website');
				
				}else {

					this.validateFormInput ('restaurant.address');
				
				}

				if (Object.keys(this.errors.restaurant).length === 0) {

					this.step += 1;

				}
				
				return;

			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'restaurant.restaurantAdminObject' :

						if (Object.keys(this.singleRestaurantData.restaurantAdminObject).length === 0) {
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

					case 'restaurant.max_booking' :

						if (!this.singleRestaurantData.restaurant.max_booking) {
							this.errors.restaurant.max_booking = 'Number Seats is required';
						}
						else if (this.singleRestaurantData.restaurant.max_booking < 0 || this.singleRestaurantData.restaurant.max_booking > 1000) {
							this.errors.restaurant.max_booking = 'Value should be between 0 and 1000';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurant, 'max_booking');
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

					case 'restaurantNewAdmin.user_name' :

						if (!this.restaurantNewAdmin.user_name) {
							this.errors.restaurantNewAdmin.user_name = 'Username is required';
						}
						else if (!this.restaurantNewAdmin.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.restaurantNewAdmin.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantNewAdmin, 'user_name');
						}

						break;

					case 'restaurantNewAdmin.email' :

						if (!this.restaurantNewAdmin.email) {
							this.errors.restaurantNewAdmin.email = 'Email is required';
						}
						else if (!this.restaurantNewAdmin.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.restaurantNewAdmin.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantNewAdmin, 'email');
						}

						break;

					case 'restaurantNewAdmin.mobile' :

						if (!this.restaurantNewAdmin.mobile) {
							this.errors.restaurantNewAdmin.mobile = 'Mobile is required';
						}
						else if (!this.restaurantNewAdmin.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.restaurantNewAdmin.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantNewAdmin, 'mobile');
						}

						break;

					case 'restaurantNewAdmin.password' :

						if (this.restaurantNewAdmin.password && this.restaurantNewAdmin.password.length < 8) {
							this.errors.restaurantNewAdmin.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantNewAdmin, 'password');
						}

						break;

					case 'restaurantNewAdmin.password_confirmation' :

						if (this.restaurantNewAdmin.password && this.restaurantNewAdmin.password !== this.restaurantNewAdmin.password_confirmation) {
							this.errors.restaurantNewAdmin.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantNewAdmin, 'password_confirmation');
						}

						break;

				}
					 
			},
			onBannerChange(evnt){
		      	let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.createImage(files[0]);
		      	}

		      	evnt.target.value = '';

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