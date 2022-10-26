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
								Merchant List
							</h2>

                        	<button 
	                        	type="button" 
	                        	@click="showMerchantCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Merchant
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
													@click="showAllMerchants"
												>
													All
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='approved' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showApprovedMerchants"
												>
													Approved
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='nonApproved' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showNonApprovedMerchants"
												>
													Non-Approved
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedMerchants"
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
											<th scope="col">Type</th>
											<!-- <th scope="col">Email</th> -->
											<th scope="col">Mobile</th>
											<th scope="col">Status</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="merchantsToShow.length"
									    	v-for="(merchant, index) in merchantsToShow"
									    	:key="merchant.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>
								    			{{ merchant.name | capitalize }}
								    			<span :class="[merchant.is_sponsored ? 'badge-danger' : 'badge-default', 'right badge']"
								    			>
								    				{{ 
								    					merchant.is_sponsored ? 'Sponsored' : 'Not-Sponsored' 
								    				}}
								    			</span>
								    		</td>
								    		<td>{{ merchant.type | capitalize }}</td>
								    		<td>{{ merchant.mobile }}</td>
								    		<td>
								    			<span :class="[merchant.is_approved ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					merchant.is_approved ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>		
								    		</td>
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showMerchantDetailModal(merchant)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			View
								      			</button>

										      	<button 
											      	type="button" 
											      	class="btn btn-primary btn-sm"
											      	v-show="merchant.deleted_at === null" 
											      	@click="showMerchantEditModal(merchant)" 
										      	>
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

								      			<button
								        			type="button"
								        			class="btn btn-danger btn-sm"
								        			v-show="merchant.deleted_at === null"
								        			@click="showMerchantDeletionModal(merchant)"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>

								      			<button
								        			type="button"
								        			class="btn btn-danger btn-sm"
								        			v-show="merchant.deleted_at !== null && merchant.owner !== null"
								        			@click="showMerchantRestoreModal(merchant)"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>

								      			<button 
									      			type="button" 
									      			class="btn btn-dark btn-sm"
									      			@click="showMerchantProductList(merchant)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Product
								      			</button>

								      			<p 	
								      				class="text-danger" 
								      				v-show="merchant.deleted_at !== null && merchant.owner === null"
								      			>
								      				Trashed Owner
								      			</p>
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!merchantsToShow.length"
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
										@click="query === '' ? fetchAllMerchants() : searchData()"
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
										@paginate="query === '' ? fetchAllMerchants() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- modal-show-merchant -->
			<div class="modal fade" id="modal-show-merchant">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Merchant Details
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
									<a class="nav-link" data-toggle="tab" href="#setting">
										Setting
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
					            		<div class="card card-body">
					            			<div class="row">
					            				<div class="col-sm-12">
								                	<div class="text-center">
								                  		<img class="img-fluid " :src="singleMerchantData.banner_preview" alt="Merchant Banner">
								                	</div>
								                	<h3 class="profile-username text-center">
								                		{{ singleMerchantData.name | eachcapitalize }}
								                	</h3>

								                	<p class="text-center mb-0">
								                		<i class="fa fa-star checked" style="color: orange;"></i>
								                		{{ singleMerchantData.mean_rating }}
								                	</p>

								                	<p class="text-center">
								                		<i class="fa fa-check-square"></i>
								                		{{ singleMerchantData.order_acceptance_percentage }}%
								                	</p>
					            				</div>
					            			</div>
						              	</div>
						              	
					            		<div class="col-sm-12">
					            			<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Owner :
							              		</label>
								                <div 
								                	class="col-sm-6" 
								                	:class="!singleMerchantData.merchantOwnerObject ? 'text-danger small' : ''"
								                >
								                  	{{ !singleMerchantData.merchantOwnerObject ? 
								                  		'Owner has been trashed' : 
								                  		singleMerchantData.merchantOwnerObject.user_name | capitalize
								                  	}}
								                </div>
								            </div>

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Type :
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleMerchantData.type | capitalize }}
								                </div>
								            </div>

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Username :
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleMerchantData.user_name | capitalize }}
								                </div>
								            </div>

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Mobile :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.mobile}}
								                </div>	
								            </div>

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Email :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.email}}
								                </div>	
								            </div> 

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Website :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.website ? singleMerchantData.website : 'No website'}}
								                </div>	
								            </div>
					            		</div>
					            	</div>
								</div>

								<div id="setting" class="container tab-pane">
									<div class="row">
					            		<div class="card card-body">
					            			<div class="row">
					            				<div class="col-sm-12">
								                	<div class="form-group form-row">		
									              		<label class="col-sm-6 text-md-right">
									              			Approval :
									              		</label>
										                <div class="col-sm-6">
										                	<span :class="[singleMerchantData.is_approved ? 'badge-success' : 'badge-danger', 'badge']">
										                		{{ singleMerchantData.is_approved ? 'Approved' : 'Not-Approved'}}
										                	</span>
										                </div>	
										            </div>

										            <div class="form-group form-row">		
									              		<label class="col-sm-6 text-md-right">
									              			Taking Order :
									              		</label>
										                <div class="col-sm-6">
										                	<span :class="[singleMerchantData.is_open ? 'badge-success' : 'badge-danger', 'badge']">
										                		{{ singleMerchantData.is_open ? 'Yes' : 'No'}}
										                	</span>
										                </div>	
										            </div> 

										            <div class="form-group form-row">		
									              		<label class="col-sm-6 text-md-right">
									              			Sponsored :
									              		</label>
										                <div class="col-sm-6">
										                	<span :class="[singleMerchantData.is_sponsored ? 'badge-danger' : 'badge-success', 'badge']">
										                		{{ singleMerchantData.is_sponsored ? 'Sponsored' : 'Not-Sponsored'}}
										                	</span>
										                </div>	
										            </div> 
					            				</div>
					            			</div>
						              	</div>
						              	
					            		<div class="col-sm-12">
					            			<div class="form-group form-row">
					            				<label class="col-sm-6 text-md-right">
							              			Sale Percantage (incl. delivery):
							              		</label>
								                <div class="col-sm-6">
								                	{{ singleMerchantData.supported_delivery_order_sale_percentage }} %
								                </div>	
					            			</div>

					            			<div class="form-group form-row">
					            				<label class="col-sm-6 text-md-right">
							              			Sale Percantage (general):
							              		</label>
								                <div class="col-sm-6">
								                	{{ singleMerchantData.general_order_sale_percentage }} %
								                </div>	
					            			</div>

					            			<div class="form-group form-row">
					            				<label class="col-sm-6 text-md-right">
							              			Promotional Discount (all products):
							              		</label>
								                <div class="col-sm-6">
								                	{{ singleMerchantData.discount }} %
								                </div>	
					            			</div>
					            		</div>
					            	</div>
								</div>

								<div id="address" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Merchant Address :
							              		</label>
								                <div class="col-sm-6">
								                	<span v-html="singleMerchantData.address"></span>
								                </div>	
								            </div>
								            <div class="form-group form-row">
								            	<label class="col-sm-6 text-md-right">
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
					            			<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Maximum Booking Seats :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.max_booking || 'No Booking'}}
								                </div>	
								            </div>

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Minimum Order :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.min_order}} tk
								                </div>	
								            </div>

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Delivery Support :
							              		</label>
								                <div class="col-sm-6">
								                	<span :class="[singleMerchantData.has_self_delivery_support ? 'badge-success' : 'badge-danger', 'badge']">
								                		{{ singleMerchantData.has_self_delivery_support ? 'Self Delivery' : 'Supported Delivery' }}
								                	</span>
								                </div>	
								            </div>

					            			<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Payment :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.is_post_paid ? 'Post-paid' : 'Pre-paid'}}
								                </div>	
								            </div>
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Service :
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.is_self_service ? 'Self-service' : 'Agent service'}}
								                </div>	
								            </div>
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Parking Facility:
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.has_parking ? 'Available' : 'Not-available'}}
								                </div>	
								            </div>
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Free Delivery:
							              		</label>
								                <div class="col-sm-6">
								                  	{{singleMerchantData.has_free_delivery ? 'Available' : 'Not-available'}}
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
			<!-- /modal-show-merchant -->

			<!-- modal-createOrEdit-merchant -->
			<div class="modal fade" id="modal-createOrEdit-merchant">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Merchant
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">

					  		<!-- form start -->
							<form 
								class="form-horizontal" 
								v-on:submit.prevent="editMode ? updateMerchant() : storeMerchant()" 
								autocomplete="off" 
								novalidate="true"
							>
								<input type="hidden" name="_token" :value="csrf">

								<div v-show="! loading" class="row">
									<div class="col-sm-12">
									  	<div class="card">
										    <div class="card-header text-center">

										      	<div class="progress">
										        	<div class="progress-bar bg-success w-25" v-show="step>=1">
										          		Profile
										        	</div>

										        	<div class="progress-bar bg-info w-25" v-show="step>=2">
										          		Setting
										        	</div>

										        	<div class="progress-bar bg-warning w-25" v-show="step>=3">
										          		Address
										        	</div>

										        	<div class="progress-bar bg-danger w-25" v-show="step>=4">
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
									        	Merchant Profile
									        </h2>
									      
									        <div class="row">
									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">
									                      	<div class="row d-flex flex-wrap align-content-center form-group">
									                      		<div class="col-sm-12">
										                      		<div class="card mb-4">
										                      			<div class="card-body">
										                      				<div class="row">
													                        	<div class="col-sm-8 text-center mb-2">
													                        		<picture>
													                                	<img 
													                                		class="img-fluid" 
													                                		:src="singleMerchantData.banner_preview" 
													                                		alt="Merchant Banner" 
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
										                      			</div>
										                      		</div>
									                      		</div>
									                        </div>

									                        <hr class="mt-1">

									                        <div class="form-group form-row">
									                        	<div class="col-6">
									                            	<div class="row d-flex align-items-center">
									                              		<label for="inputFoodTags3" class="col-sm-4 col-form-label text-md-right">
									                              			Owner
									                              		</label>
									                              		<div class="col-sm-6">
									                                  		<multiselect 
									                                  			v-model="singleMerchantData.merchantOwnerObject"
									                                  			placeholder="Merchant Owner" 
										                                  		label="user_name" 
										                                  		track-by="id" 
										                                  		:options="allMerchantOwners" 
										                                  		:required="true" 
										                                  		class="form-control p-0" 
										                                  		:class="!errors.merchant.merchantOwnerObject  ? 'is-valid' : 'is-invalid'"
										                                  		:allow-empty="false"
										                                  		selectLabel = "Press/Click"
										                                  		deselect-label="Can't remove single value"
										                                  		@close="validateFormInput('merchantOwnerObject')"
									                                  		>
										                                	</multiselect>

									                                		<div 
											                                  	class="invalid-feedback"
											                                >
																		        {{ errors.merchant.merchantOwnerObject }}
																		  	</div>
									                              		</div>
									                               
										                              	<div class="col-sm-2 text-right">
										                                  	<button type="button" class="btn btn-secondary btn-sm p-0" data-toggle="modal" data-target="#modal-create-merchant-owner">
										                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
										                                    	Owner
										                                	</button>
										                              	</div> 
									                            	</div>
									                          	</div>

									                          	<div class="col-6">
									                            	<div class="row d-flex align-items-center">
									                              		<label for="inputFoodTags3" class="col-sm-4 col-form-label text-md-right">
									                              			Type
									                              		</label>
									                              		<div class="col-sm-8">
									                                  		<multiselect 
									                                  			v-model="singleMerchantData.type"
									                                  			placeholder="Merchant Type" 
										                                  		:options="['shop', 'restaurant']" 
										                                  		:required="true" 
										                                  		class="form-control p-0" 
										                                  		:class="!errors.merchant.type ? 'is-valid' : 'is-invalid'"
										                                  		:allow-empty="false"
										                                  		selectLabel = "Press/Click"
										                                  		deselect-label="Can't remove single value"
										                                  		@close="validateFormInput('type')"
									                                  		>
										                                	</multiselect>

									                                		<div 
											                                  	class="invalid-feedback"
											                                >
																		        {{ errors.merchant.type }}
																		  	</div>
									                              		</div> 
									                            	</div>
									                          	</div>
									                        </div>

									                        <div class="form-group form-row">
									                        	<div class="col-6">
																	<div class="row">
									                              		<label for="inputName3" class="col-sm-4 col-form-label text-md-right">
									                              			Name
									                              		</label>
									                              		<div class="col-sm-8">
									                                  		<input 
										                                  		type="text" 
										                                  		class="form-control" 
										                                  		v-model="singleMerchantData.name"  
										                                  		placeholder="Merchant Name" 
										                                  		:class="!errors.merchant.name  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('name')" 
									                                  		>
									                                  		<div class="invalid-feedback">
																		        {{ errors.merchant.name  }}
																		  	</div>
									                              		</div>
									                          		</div>
																</div>

																<div class="col-6">
																	<div class="row">
									                              		<label for="inputName3" class="col-sm-4 col-form-label text-md-right">
									                              			User Name
									                              		</label>
									                              		<div class="col-sm-8">
									                                  		<input 
										                                  		type="text" 
										                                  		class="form-control" 
										                                  		v-model="singleMerchantData.user_name"  
										                                  		placeholder="User Name" 
										                                  		:class="!errors.merchant.user_name  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('user_name')" 
									                                  		>
									                                  		<div class="invalid-feedback">
																		        {{ errors.merchant.user_name  }}
																		  	</div>
									                              		</div>
									                          		</div>
																</div>
									                        </div>

									                        <div class="form-group form-row">
									                        	<div class="col-6">
										                        	<div class="form-row">
													              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
													              			Password
													              		</label>
														                <div class="col-sm-8">
														                  	<input 
															                  	type="password" class="form-control" 
															                  	v-model="singleMerchantData.password"
															                  	placeholder="Password" 
															                  	required="true" 
															                  	autocomplete="false"
															                  	:class="!errors.merchant.password  ? 'is-valid' : 'is-invalid'"
															                  	@keyup="validateFormInput('password')"
														                  	>
														                  	
														                  	<div class="invalid-feedback">
																	        	{{ errors.merchant.password }}
																	  		</div>
														                </div>	
													              	</div>
									                        	</div>

									                        	<div class="col-6">
													              	<div class="form-row">
													              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
													              			Repeat Password
													              		</label>
														                <div class="col-sm-8">
														                  	<input 
															                  	type="password" class="form-control" 
															                  	v-model="singleMerchantData.password_confirmation"
															                  	placeholder="Confirm Password" 
															                  	required="true" 
															                  	:class="!errors.merchant.password_confirmation  ? 'is-valid' : 'is-invalid'"
															                  	@keyup="validateFormInput('password_confirmation')"
														                  	>
														                  	
														                  	<div class="invalid-feedback">
																	        	{{ errors.merchant.password_confirmation }}
																	  		</div>
														                </div>	
													              	</div>
									                        	</div>
									                        </div>

									                        <div class="form-group form-row">
									                        	<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMobile3" class="col-sm-4 col-form-label text-md-right">
										                              		Mobile
										                              	</label>
										                              	<div class="col-sm-8">
									                                  		<input 
										                                  		type="tel" 
										                                  		class="form-control" 
										                                  		v-model="singleMerchantData.mobile" 
										                                  		placeholder="Mobile No." 
										                                  		:class="!errors.merchant.mobile  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('mobile')"
									                                  		>

									                                  		<div class="invalid-feedback"
										                                  	>
																		        {{ errors.merchant.mobile }}
																		  	</div>
										                              	</div>
										                            </div>
									                          	</div>

									                          	<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMinOrder3" class="col-sm-4 col-form-label text-md-right">
										                              		Min. Order
										                              	</label>
										                              	<div class="col-sm-8">
										                              		
									                                  		<input 
										                                  		type="number" 
										                                  		class="form-control" 
										                                  		v-model.number="singleMerchantData.min_order" 
										                                  		placeholder="Minimum Currency" 
										                                  		min="100" 
										                                  		max="5000" 
										                                  		step="1" 
										                                  		:class="!errors.merchant.min_order  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('min_order')"
										                                  	>
										                                  		
										                                  	<div class="invalid-feedback">
																		        {{ errors.merchant.min_order }}
																		  	</div>

										                              	</div>
										                          	</div>
										                      	</div>
									                        </div>

									                        <div class="form-group form-row">
									                        	<div class="col-6">
										                            <div class="row">
										                              	<label for="inputMaxBooking3" class="col-sm-4 col-form-label text-md-right">
										                              		Max Booking
										                              	</label>
										                              	<div class="col-sm-8">
										                              		
									                                  		<input 
										                                  		type="number" 
										                                  		class="form-control" 
										                                  		v-model.number="singleMerchantData.max_booking" 
										                                  		placeholder="Max Booking Seats"
										                                  		min="0" 
										                                  		max="1000" 
										                                  		step="1" 
										                                  		:class="!errors.merchant.max_booking  ? 'is-valid' : 'is-invalid'"
										                                  		@keyup="validateFormInput('max_booking')"
										                                  	>
										                                  		
										                                  	<div class="invalid-feedback">
																		        {{ errors.merchant.max_booking }}
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
											                                  	v-model="singleMerchantData.website" 
											                                  	placeholder="Merchant Website" 
											                                  	:class="!errors.merchant.website  ? 'is-valid' : 'is-invalid'"
											                                  	@keyup="validateFormInput('website')"
											                                >
										                                  	<div 
											                                  	class="invalid-feedback"
											                                >
																		        {{errors.merchant.website}}
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
									          	<div class="col-6">
								                  	<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
												  		Close
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
										:key="2" 
										v-show="step==2"
									>
										<div class="col-sm-12">
											<h2 class="text-center  mb-4 lead">Merchant Setting</h2>

											<div class="row">
												<div class="card card-body">
							            			<div class="form-group form-row text-center">
							                        	<div class="col-sm-4">
							                        		<div class="form-group">
														  		<label for="inputOwnerApproval3">
														  			Owner Approval
														  		</label>
														  		<div>
																	<toggle-button 
							                                  			:sync="true" 
							                                  			v-model="singleMerchantData.is_approved" 
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
														  		<label for="inputOwnerApproval3">
														  			Taking Order
														  		</label>
														  		<div>
																	<toggle-button 
							                                  			:sync="true" 
							                                  			v-model="singleMerchantData.is_open" 
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
														  		<label for="inputOwnerApproval3">
														  			Sponsored
														  		</label>
														  		<div>
																	<toggle-button 
							                                  			:sync="true" 
							                                  			v-model="singleMerchantData.is_sponsored" 
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
								              	</div>

												<div class="col-sm-12">
													<div class="card">
														<div class="card-body">
															<div class="form-group form-row">	
											              		<label for="inputDealName3" class="col-sm-4 col-form-label text-md-right">
											              			Discount
											              		</label>
												                <div class="col-sm-8">
												                	<div class="input-group mb-3">
													                	<input 
																			type="number" 
																			class="form-control" 
																			v-model.number="singleMerchantData.discount" 
																			placeholder="Discount Rate" 
																			:class="!errors.merchant.discount  ? 'is-valid' : 'is-invalid'" 
																			@keyup="validateFormInput('discount')" 
																			min="0" 
																			step=".1" 
																			max="100" 
													                	>
																		<div class="input-group-append">
																			<span class="input-group-text">
																				%
																			</span>
																		</div>
													                	<div class="invalid-feedback">
																        	{{ errors.merchant.discount }}
																  		</div>
												                	</div>
												                </div>	
											              	</div>

											              	<div class="form-group form-row">	
											              		<label for="inputDealName3" class="col-sm-4 col-form-label text-md-right">
											              			Sale Percentage (Delivery)
											              		</label>
												                <div class="col-sm-8">
												                  	<div class="input-group mb-3">
																		<input 
																			type="number" 
																			class="form-control" 
																			v-model.number="singleMerchantData.supported_delivery_order_sale_percentage" 
																			placeholder="Sale Percentage" 
																			:class="!errors.merchant.supported_delivery_order_sale_percentage ? 'is-valid' : 'is-invalid'"
																			@change="validateFormInput('supported_delivery_order_sale_percentage')"
																			min="0" 
																			max="100" 
																			step=".1" 
													                	>
																		<div class="input-group-append">
																			<span class="input-group-text">
																				%
																			</span>
																		</div>
													                	<div class="invalid-feedback">
																        	{{ errors.merchant.supported_delivery_order_sale_percentage }}
																  		</div>
																	</div>
												                </div> 	
											              	</div>

											              	<div class="form-group form-row">	
											              		<label for="inputDealName3" class="col-sm-4 col-form-label text-md-right">
											              			General Sale Percentage
											              		</label>
												                <div class="col-sm-8">
												                  	<div class="input-group mb-3">
																		<input 
																			type="number" 
																			class="form-control" 
																			v-model.number="singleMerchantData.general_order_sale_percentage" 
																			placeholder="Sale Percentage" 
																			:class="!errors.merchant.general_order_sale_percentage ? 'is-valid' : 'is-invalid'"
																			@change="validateFormInput('general_order_sale_percentage')"
																			min="0" 
																			max="100" 
																			step=".1" 
													                	>
																		<div class="input-group-append">
																			<span class="input-group-text">
																				%
																			</span>
																		</div>
													                	<div class="invalid-feedback">
																        	{{ errors.merchant.general_order_sale_percentage }}
																  		</div>
																	</div>
												                </div> 	
											              	</div>
														</div>
													</div>
												</div>
											</div>

											<div class="row mb-2 mt-2">
									          	<div class="col-6">
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
									        
									        <h2 class="text-center mb-4 lead">Merchant Address</h2>
									      
									        <div class="row">
									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">
									                        <div class="form-group form-row">    
									                          <label for="inputLocation3" class="col-sm-4 col-form-label text-md-right">
									                          	Location
									                          </label>
									                          	<div class="col-sm-8">
										                    		<vue-google-autocomplete
										                    			id="merchantEditAddress"
			                        									classname="form-control"
										                        		placeholder="Start typing"
										                        		v-on:placechanged="getAddressData"
												                        types="(cities)"
												                        country="bd"
										                    		>
										                    		</vue-google-autocomplete>

									                    			<!-- 
									                              	<input type="text" class="form-control" v-model="merchant.lat" placeholder="Merchant Location" required>
									                              	-->
									                          	</div>   
									                        </div>
									                          
									                        <div class="form-group form-row">
									                          	<label for="inputAddress3" class="col-sm-4 col-form-label text-md-right">
									                          		Address Detail
									                          	</label>
									                          	<div class="col-sm-8">

									                              	<ckeditor 
										                              	class="form-control" 
										                              	:editor="editor" 
										                              	v-model="singleMerchantData.address"
										                              	:class="!errors.merchant.address  ? 'is-valid' : 'is-invalid'"
										                              	@blur="validateFormInput('address')"
										                            >
									                              	</ckeditor>

									                              	<div 
									                                  	class="invalid-feedback" 
									                                >
																        {{ errors.merchant.address }}
																  	</div> 	
									                          	</div>  
									                        </div>

									                        <div class="card card-body">
										            			<div class="form-group form-row text-center">
										                        	<div class="col-sm-4">
										                        		<div class="form-group">
																	  		<label for="inputOwnerApproval3">
																	  			Payment (cash)
																	  		</label>
																	  		<div>
																				<toggle-button 
											                                    	:sync="true" 
											                                    	v-model="singleMerchantData.is_post_paid" 
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

										                        	<div class="col-sm-4">
										                        		<div class="form-group">
																	  		<label for="inputOwnerApproval3">
																	  			Parking
																	  		</label>
																	  		<div>
																				<toggle-button 
										                                    		value="true" 
										                                    		:sync="true" 
										                                    		v-model="singleMerchantData.has_parking" 
										                                    		:width="120"  
										                                    		:height="30" 
										                                    		:font-size="15" 
										                                    		:color="{checked: 'green', unchecked: '#FF0000'}" 
										                                    		:labels="{checked: 'Available', unchecked: 'NA' }"
									                                    		/>		
																	  		</div>
										                        		</div>
										                        	</div>

										                        	<div class="col-sm-4">
										                        		<div class="form-group">
																	  		<label for="inputOwnerApproval3">
																	  			Delivery Support
																	  		</label>
																	  		<div>
																				<toggle-button 
										                                    		value="true" 
										                                    		:sync="true" 
										                                    		v-model="singleMerchantData.has_self_delivery_support" 
										                                    		:width="120"  
										                                    		:height="30" 
										                                    		:font-size="15" 
										                                    		:color="{checked: 'green', unchecked: '#FF0000'}" 
										                                    		:labels="{checked: 'Available', unchecked: 'NA' }"
									                                    		/>		
																	  		</div>
										                        		</div>
										                        	</div>

										                        	<div class="col-sm-4">
										                        		<div class="form-group">
																	  		<label for="inputOwnerApproval3">
																	  			Free Delivery
																	  		</label>
																	  		<div>
																				<toggle-button 
										                                    		value="true" 
										                                    		:sync="true" 
										                                    		v-model="singleMerchantData.has_free_delivery" 
										                                    		:width="120"  
										                                    		:height="30" 
										                                    		:font-size="15" 
										                                    		:color="{checked: 'green', unchecked: '#FF0000'}" 
										                                    		:labels="{checked: 'Available', unchecked: 'NA' }"
									                                    		/>		
																	  		</div>
										                        		</div>
										                        	</div>

										                        	<div class="col-sm-4">
										                        		<div class="form-group">
																	  		<label for="inputOwnerApproval3">
																	  			Self Service
																	  		</label>
																	  		<div>
																				<toggle-button 
										                                    		value ="true" 
										                                    		:sync="true" 
										                                    		v-model="singleMerchantData.is_self_service" 
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
									                    </div>
									                    <!-- /.card-body -->
									              	</div>
									              	<!-- /.card-card -->
									          	</div>  
									        </div>

									        <div class="row mb-2 mt-2">
									          	<div class="col-6">
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
										v-bind:key="4" 
										v-show="step==4"
									> 
									  	<div class="col-sm-12">

									        <h2 class="text-center mb-4 lead">
									        	Service & Schedule
									        </h2>
									      
									        <div class="row">
									          
									          	<div class="col-sm-12">
									            	<div class="card">
									                    <div class="card-body">
									                        <div class="form-group form-row mb-4">
									                          	<label for="inputServiceSchedule3" class="col-sm-2 col-form-label text-right">
									                          		Service Schedule
									                          	</label>
									                          	<div class="col-sm-10">
																	<div 
																		class="form-group form-row border border-dark" 
																		v-for="dayName in merchantWeekDays"
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
																				  		:options="merchantScheduleHours" 
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
																				  		:options="merchantScheduleHours" 
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

									                        <div class="form-group form-row mb-4">      
									                          	<label for="inputBookingBreak3" class="col-sm-2 col-form-label text-right">
									                          		Booking Breaks
									                          	</label>

									                          	<div class="col-sm-10">
															    	<div 
															    		class="form-group form-row border border-dark" 
															    		v-for="dayName in merchantWeekDays"
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
																				  		:options="merchantScheduleHours" 
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
																				  		:options="merchantScheduleHours" 
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
									          	<div class="col-6">
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
									                    {{ editMode ? 'Update' : 'Create' }} Merchant
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
			<!-- /modal-createOrEdit-merchant -->

			<!-- /.modal-create-merchant-owner -->
			<div class="modal fade" id="modal-create-merchant-owner">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Add Merchant Owner</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="storeMerchantOwner()" autocomplete="off">
							<div class="modal-body text-dark">

					      		<input type="hidden" name="_token" :value="csrf">

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
									            <div class="form-group form-row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
								              			First Name
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" 
										                  	class="form-control is-valid" 
										                  	v-model="merchantNewOwner.first_name"
										                  	placeholder="First Name" 
										                  	
									                  	>
									                </div>	
								              	</div>

								              	<div class="form-group form-row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
								              			Last Name
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" 
										                  	class="form-control is-valid" 
										                  	v-model="merchantNewOwner.last_name"
										                  	placeholder="Last Name" 
										                  	
									                  	>
									                </div>	
								              	</div>  		
								              	
								              	<div class="form-group form-row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
								              			User Name
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="merchantNewOwner.user_name"
										                  	placeholder="Owner Name" 
										                  	required="true" 
										                  	:class="!errors.merchantNewOwner.user_name  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('merchantNewOwner.user_name')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.merchantNewOwner.user_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group form-row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
								              			Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="merchantNewOwner.mobile"
										                  	placeholder="Owner Mobile" 
										                  	required="true"  
										                  	:class="!errors.merchantNewOwner.mobile  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('merchantNewOwner.mobile')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.merchantNewOwner.mobile }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group form-row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="text" class="form-control" 
										                  	v-model="merchantNewOwner.email"
										                  	placeholder="Owner Email" 
										                  	required="true"  
										                  	:class="!errors.merchantNewOwner.email  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('merchantNewOwner.email')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.merchantNewOwner.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group form-row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="password" class="form-control" 
										                  	v-model="merchantNewOwner.password"
										                  	placeholder="Password" 
										                  	required="true" 
										                  	autocomplete="false" 
										                  	:class="!errors.merchantNewOwner.password  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('merchantNewOwner.password')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.merchantNewOwner.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group form-row">
								              		<label for="inputCuisineName3" class="col-sm-4 col-form-label text-md-right">
								              			Repeat Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
										                  	type="password" class="form-control" 
										                  	v-model="merchantNewOwner.password_confirmation"
										                  	placeholder="Confirm Password" 
										                  	required="true" 
										                  	:class="!errors.merchantNewOwner.password_confirmation  ? 'is-valid' : 'is-invalid'"
										                  	@keyup="validateFormInput('merchantNewOwner.password_confirmation')"
									                  	>
									                  	
									                  	<div class="invalid-feedback">
												        	{{ errors.merchantNewOwner.password_confirmation }}
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
								  		{{ editMode ? 'Update' : 'Create' }} Merchant Owner
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-create-merchant-owner -->

			<!-- modal-merchant-delete-confirmation -->
			<div class="modal fade" id="modal-merchant-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Merchant Deletion
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyMerchant()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete merchant ??</h5>
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
			<!-- /modal-merchant-delete-confirmation -->

			<!-- modal-merchant-restore-confirmation -->
			<div class="modal fade" id="modal-merchant-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Merchant Restoration
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreMerchant()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore merchant ??</h5>
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
			<!-- /.modal-merchant-restore-confirmation -->

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

	var singleMerchantData = {

		banner_preview : null,
		name : null,
		mobile : null,
		website : null,

		address : null,
		lat : null,
		lng : null,

		min_order : 100,

		is_post_paid : false,
		is_self_service : false,
		is_sponsored : false,
		
		merchant_owner_id : null,

		has_parking : false,
		has_free_delivery : false,
		
		has_self_delivery_support : true,

		is_approved : false,

		merchantOwnerObject : {},
    	merchantProductObjectTags : [],
    	merchantMealObjectTags : [],
		merchantCuisineObjectTags : [],
    };

	var merchantListData = {
		step : 1,
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	editor: ClassicEditor,
    	
    	currentTab : 'all',
    	allMerchants : [],
    	merchantsToShow : [],
    	
        allMerchantOwners : [],

        merchantScheduleHours : ['NA', '6 am', '7 am', '8 am', '9 am', '10 am', '11 am', '12 pm', '1 pm', '2 pm', '3 pm', '4 pm', '5 pm', '6 pm', '7 pm', '8 pm', '9 pm', '10 pm', '11 pm', '12 am', '1 am', '2 am'],
    	
    	merchantNewOwner : {
			// email : null,
			// mobile : null,
    		// user_name : null,
			// password : null,
			// password_confirmation : null,
    	},

    	merchantNewBanner : null,

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		merchant : {},
			merchantNewOwner : {},
    	},

        singleMerchantData : singleMerchantData,
			
        merchantWeekDays : ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'],

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
	        return merchantListData;
		},

		created(){
			this.fetchAllMerchants();
			this.fetchAllMerchantOwners();
		},

		watch : {
			'singleMerchantData.merchantOwnerObject' : function(merchantOwnerObject){

				if (merchantOwnerObject) {
					this.singleMerchantData.merchant_owner_id = merchantOwnerObject.id;
				}else
					this.singleMerchantData.merchant_owner_id = null;
			},
			query : function(val){
				if (val==='') {
					this.fetchAllMerchants();
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
			showAllMerchants(){
				this.currentTab = 'all';
				this.merchantsToShow = this.allMerchants.all.data;
				this.pagination = this.allMerchants.all;
			},
			showApprovedMerchants(){
				this.currentTab = 'approved';
				this.merchantsToShow = this.allMerchants.approved.data;
				this.pagination = this.allMerchants.approved;
			},
			showNonApprovedMerchants(){
				this.currentTab = 'nonApproved';
				this.merchantsToShow = this.allMerchants.nonApproved.data;
				this.pagination = this.allMerchants.nonApproved;
			},
			showTrashedMerchants(){
				this.currentTab = 'trashed';
				this.merchantsToShow = this.allMerchants.trashed.data;
				this.pagination = this.allMerchants.trashed;
			},
			showListDataForSelectedTab(){
				if (this.currentTab=='all') {
					this.merchantsToShow = this.allMerchants.all.data;
					this.pagination = this.allMerchants.all;
				}else if (this.currentTab=='approved') {
					this.merchantsToShow = this.allMerchants.approved.data;
					this.pagination = this.allMerchants.approved;
				}else if (this.currentTab=='nonApproved') {
					this.merchantsToShow = this.allMerchants.nonApproved.data;
					this.pagination = this.allMerchants.nonApproved;
				}else {
					this.merchantsToShow = this.allMerchants.trashed.data;
					this.pagination = this.allMerchants.trashed;
				}
			},
			fetchAllMerchants(){
				this.loading = true;
				axios
					.get('/api/merchants/'+ this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
							
						if (response.status == 200) {
							this.currentTab = 'all';
							this.allMerchants = response.data;
							this.showListDataForSelectedTab();
							this.loading = false;

						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllMerchantOwners(){
				this.loading = true;
				axios
					.get('/api/merchant-owners')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchantOwners = response.data;
							// console.log(this.allMerchantOwners);
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchAllMerchants();
				}else
					this.searchData();
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMerchants();
				}else
					this.searchData();
    		},
    		showMerchantDetailModal(merchant) {

				this.singleMerchantData = merchant;
				this.singleMerchantData.max_booking = merchant.booking ? merchant.booking.total_seat : 0;
				this.singleMerchantData.merchantOwnerObject = merchant.owner;

				this.service_schedule  = merchant.service_schedule;
				this.booking_break_schedule = merchant.booking_break_schedule;

				$("#modal-show-merchant").modal("show");
			},
    		showMerchantCreateModal(merchant) {
    			
    			this.step = 1;
    			this.editMode = false;
    			this.submitForm = true;
    			this.errors.merchant = {};

				this.singleMerchantData = {

					merchantOwnerObject : {},

				};

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

				$("#modal-createOrEdit-merchant").modal("show");
			},
		    storeMerchant() {

		    	if (this.step < 4) {

		    		this.nextPage();
		    		return;

		    	}

				this.singleMerchantData.banner_preview = this.merchantNewBanner;

				this.singleMerchantData.service_schedule = this.service_schedule;
				this.singleMerchantData.booking_break_schedule = this.booking_break_schedule;
				
				// this.merchant.lat : null,
				// this.merchant.lng : null,

				axios
					.post('/merchants/'+ this.perPage, this.singleMerchantData)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantData = {

								merchantOwnerObject : {},

							};

							this.query = '';

							this.currentTab = 'all';

							this.allMerchants = response.data;
							this.showListDataForSelectedTab();

							$("#modal-createOrEdit-merchant").modal("hide");

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
			showMerchantEditModal(merchant) {

				this.step = 1;
				this.editMode = true;
				this.submitForm = true;
				this.errors.merchant = {};

				/*
				this.singleMerchantData = {
					merchant,
					max_booking : merchant.booking ? merchant.booking.total_seat : 0,
					merchantOwnerObject : merchant.owner
				};
				*/

				this.singleMerchantData = merchant;
				this.singleMerchantData.max_booking = merchant.booking ? merchant.booking.total_seat : 0;
				this.singleMerchantData.merchantOwnerObject = merchant.owner;

				this.service_schedule  = merchant.service_schedule;
				this.booking_break_schedule = merchant.booking_break_schedule;
		    	
				$("#modal-createOrEdit-merchant").modal("show");
			},
			updateMerchant() {

				this.singleMerchantData.banner_preview = this.merchantNewBanner;
				
				this.singleMerchantData.service_schedule = this.service_schedule;
				this.singleMerchantData.booking_break_schedule = this.booking_break_schedule;

				// this.merchant.lat : null,
				// this.merchant.lng : null,

				axios
					.put('/merchants/' + this.singleMerchantData.id + '/' + this.perPage, this.singleMerchantData)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMerchantData = {

								merchantOwnerObject : {},

							};

							if (this.query === '') {
								this.allMerchants = response.data;
								this.showListDataForSelectedTab();
							}
							else
								this.searchData();

							$("#modal-createOrEdit-merchant").modal("hide");

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
			showMerchantDeletionModal(merchant) {
				this.singleMerchantData = merchant;
				$("#modal-merchant-delete-confirmation").modal("show");
			},
			destroyMerchant() {

				axios
					.delete('/merchants/' + this.singleMerchantData.id + '/' +this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMerchantData = {

								merchantOwnerObject : {},

							};

							if (this.query === '') {
								this.allMerchants = response.data;
								this.showListDataForSelectedTab();
							}
							else
								this.searchData();

							$("#modal-merchant-delete-confirmation").modal("hide");

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
			showMerchantRestoreModal(merchant) {
				this.singleMerchantData = merchant;
				$("#modal-merchant-restore-confirmation").modal("show");
			},
			restoreMerchant() {

				axios
					.patch('/merchants/' + this.singleMerchantData.id + '/' +this.perPage)
					.then(response => {
						if (response.status == 200) {

							this.singleMerchantData = {

								merchantOwnerObject : {},
								
							};

							if (this.query === '') {
								this.allMerchants = response.data;
								this.showListDataForSelectedTab();
							}
							else
								this.searchData();

							$("#modal-merchant-restore-confirmation").modal("hide");

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
					"/api/merchants/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {

					this.allMerchants = response.data;
					this.merchantsToShow = this.allMerchants.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			showMerchantProductList(merchant) {
				this.$router.push({
			 		name: 'merchant-all-products', 
			 		params: { 
			 			merchantId : merchant.id, 
			 			merchantName : merchant.name 
			 		}, 
				});
			},
			getAddressData(addressData, placeResultData, id) {
				// console.log(addressData);
			},
			storeMerchantOwner(){

				this.validateFormInput('merchantNewOwner.user_name');
				this.validateFormInput('merchantNewOwner.mobile');
				this.validateFormInput('merchantNewOwner.email');
				this.validateFormInput('merchantNewOwner.password');
				this.validateFormInput('merchantNewOwner.password_confirmation');

				if (Object.keys(this.errors.merchantNewOwner).length) {
					this.submitForm = false;
					return;
				}

				axios
					.post('/merchant-owners', this.merchantNewOwner)
					.then(response => {
						if (response.status == 200) {
							this.merchantNewOwner = {};
							this.allMerchantOwners = response.data;
							$('#modal-create-merchant-owner').modal('hide');
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

					this.validateFormInput ('merchantOwnerObject');
					this.validateFormInput ('type');
					this.validateFormInput ('name');
					this.validateFormInput ('user_name');
					this.validateFormInput ('password');
					this.validateFormInput ('password_confirmation');
					this.validateFormInput ('mobile');
					this.validateFormInput ('min_order');
					this.validateFormInput ('max_booking');
					this.validateFormInput ('website');
				
				}
				else if (this.step===2) {
					this.validateFormInput('discount');
					this.validateFormInput('general_order_sale_percentage');
					this.validateFormInput('supported_delivery_order_sale_percentage');
				}
				else {

					this.validateFormInput ('address');
				
				}

				if (Object.keys(this.errors.merchant).length === 0) {

					this.step += 1;

				}
				
				return;

			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'merchantOwnerObject' :

						if (Object.keys(this.singleMerchantData.merchantOwnerObject).length === 0) {
							this.errors.merchant.merchantOwnerObject = 'Merchant owner is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'merchantOwnerObject');
						}

						break;

					case 'type' :

						if (! this.singleMerchantData.type) {
							this.errors.merchant.type = 'Merchant type is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'type');
						}

						break;

					case 'name' :

						if (!this.singleMerchantData.name) {
							this.errors.merchant.name = 'Name is required';
						}
						else if (!this.singleMerchantData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.merchant.name = 'No special character or space';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'name');
						}

						break;

					case 'user_name' :

						if (!this.singleMerchantData.user_name) {
							this.errors.merchant.user_name = 'Username is required';
						}
						else if (!this.singleMerchantData.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.merchant.user_name = 'No special character or space';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'user_name');
						}

						break;

					case 'password' :

						if ((! this.editMode && ! this.singleMerchantData.password) || (this.singleMerchantData.password && this.singleMerchantData.password.length < 8)) {
							this.errors.merchant.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'password');
						}

						break;

					case 'password_confirmation' :

						if (this.singleMerchantData.password && this.singleMerchantData.password !== this.singleMerchantData.password_confirmation) {
							this.errors.merchant.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'password_confirmation');
						}

						break;

					case 'mobile' :

						if (!this.singleMerchantData.mobile) {
							this.errors.merchant.mobile = 'Mobile is required';
						}
						else if (!this.singleMerchantData.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.merchant.mobile = 'Invalid mobile number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'mobile');
						}

						break;

					case 'min_order' :

						if (!this.singleMerchantData.min_order) {
							this.errors.merchant.min_order = 'Minimum order is required';
						}
						else if (this.singleMerchantData.min_order < 100 || this.singleMerchantData.min_order > 5000) {
							this.errors.merchant.min_order = 'Value should be between 100 and 5000';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'min_order');
						}

						break;

					case 'max_booking' :

						if (!this.singleMerchantData.max_booking) {
							this.errors.merchant.max_booking = 'Number Seats is required';
						}
						else if (this.singleMerchantData.max_booking < 0 || this.singleMerchantData.max_booking > 1000) {
							this.errors.merchant.max_booking = 'Value should be between 0 and 1000';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'max_booking');
						}

						break;

					case 'website' :

						if (this.singleMerchantData.website && !this.singleMerchantData.website.match(/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/g)) {
							this.errors.merchant.website = 'Invalid url';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'website');
						}

						break;

					case 'discount' :

						if (!this.singleMerchantData.discount) {
							this.errors.merchant.discount = 'Discount rate is required';
						}
						else if (this.singleMerchantData.discount < 0 || this.singleMerchantData.discount > 100) {
							this.errors.merchant.discount = 'Discount rate is invalid';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'discount');
						}

						break;

					case 'supported_delivery_order_sale_percentage' :

						if (! this.singleMerchantData.supported_delivery_order_sale_percentage) {
							this.errors.merchant.supported_delivery_order_sale_percentage = 'Sale % is required';
						}
						else if (this.singleMerchantData.supported_delivery_order_sale_percentage < 0 || this.singleMerchantData.supported_delivery_order_sale_percentage > 100) {
							this.errors.merchant.supported_delivery_order_sale_percentage = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'supported_delivery_order_sale_percentage');
						}

						break;

					case 'general_order_sale_percentage' :

						if (! this.singleMerchantData.general_order_sale_percentage) {
							this.errors.merchant.general_order_sale_percentage = 'Sale % is required';
						}
						else if (this.singleMerchantData.general_order_sale_percentage < 0 || this.singleMerchantData.general_order_sale_percentage > 100) {
							this.errors.merchant.general_order_sale_percentage = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'general_order_sale_percentage');
						}

						break;

					case 'address' :

						if (!this.singleMerchantData.address) {
							this.errors.merchant.address = 'Address is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchant, 'address');
						}

						break;

					case 'merchantNewOwner.user_name' :

						if (!this.merchantNewOwner.user_name) {
							this.errors.merchantNewOwner.user_name = 'Username is required';
						}
						else if (!this.merchantNewOwner.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.merchantNewOwner.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantNewOwner, 'user_name');
						}

						break;

					case 'merchantNewOwner.email' :

						if (!this.merchantNewOwner.email) {
							this.errors.merchantNewOwner.email = 'Email is required';
						}
						else if (!this.merchantNewOwner.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.merchantNewOwner.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantNewOwner, 'email');
						}

						break;

					case 'merchantNewOwner.mobile' :

						if (!this.merchantNewOwner.mobile) {
							this.errors.merchantNewOwner.mobile = 'Mobile is required';
						}
						else if (!this.merchantNewOwner.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.merchantNewOwner.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantNewOwner, 'mobile');
						}

						break;

					case 'merchantNewOwner.password' :

						if (this.merchantNewOwner.password && this.merchantNewOwner.password.length < 8) {
							this.errors.merchantNewOwner.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantNewOwner, 'password');
						}

						break;

					case 'merchantNewOwner.password_confirmation' :

						if (this.merchantNewOwner.password && this.merchantNewOwner.password !== this.merchantNewOwner.password_confirmation) {
							this.errors.merchantNewOwner.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantNewOwner, 'password_confirmation');
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
                    this.singleMerchantData.banner_preview = this.merchantNewBanner = evnt.target.result;
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