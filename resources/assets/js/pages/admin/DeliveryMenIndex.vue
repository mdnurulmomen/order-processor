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
								Delivery-Men List
							</h2>

                        	<button type="button" 
                    				@click="showDeliveryManCreateModal" 
                    				class="btn btn-secondary btn-sm float-right mb-2"
                    		>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Delivery-Man
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
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showCurrentDeliveryMen"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedDeliveryMen"
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
											<th scope="col">Username</th>
											<th scope="col">Email</th>
											<th scope="col">Mobile</th>
											<th scope="col">Picture</th>
											<th scope="col">Approval</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="deliveryMenToShow.length"
									    	v-for="(deliveryMan, index) in deliveryMenToShow"
									    	:key="deliveryMan.id"
									  	>
									    	<td scope="row">
									    		{{ index + 1 }}
									    	</td>
								    		<td>
								    			{{ 
								    				deliveryMan.user_name
								    			}}
								    		</td>
								    		<td>
								    			{{ 
								    				deliveryMan.email
								    			}}
								    		</td>
								    		<td>
								    			{{ 
								    				deliveryMan.mobile
								    			}}
								    		</td>
								    		<td>
								    			<img 
								    				class="profile-user-img img-fluid img-circle" 
								    				:src="deliveryMan.profile_pic_preview" alt="Delivery-Man Image"
								    			>
								    		</td>
								    		<td>
								    			
								    			<span 
								    				  :class="[deliveryMan.admin_approval ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					deliveryMan.admin_approval ? 'Approved' : 'Not-approved'
								    				}}
								    			</span>

								    		</td>
								    		
								    		<td>
										      	<button type="button" v-show="deliveryMan.deleted_at === null" @click="showDeliveryManEditModal(deliveryMan)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="deliveryMan.deleted_at === null"
								        			type="button"
								        			@click="showDeliveryManDeletionModal(deliveryMan)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="deliveryMan.deleted_at !== null"
								        			type="button"
								        			@click="showDeliveryManRestoreModal(deliveryMan)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!deliveryMenToShow.length">
								    		<td colspan="7">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Delivery-Man found.
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
										<option>1</option>
										<option>2</option>
										<option>3</option>
										<option>5</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllDeliveryMen() : searchData()"
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
										@paginate="query === '' ? fetchAllDeliveryMen() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-deliveryMan -->
			<div class="modal fade" id="modal-createOrEdit-deliveryMan">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Delivery-Men
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateDeliveryMan() : storeDeliveryMan()"
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
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Firstname
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.first_name" 
															placeholder="First Name" 
															:class="!errors.deliveryMan.first_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.first_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.first_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Lastname
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.last_name" 
															placeholder="Last Name" 
															:class="!errors.deliveryMan.last_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.last_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.last_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Username
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.user_name" 
															placeholder="Login Username" 
															required="true" 
															:class="!errors.deliveryMan.user_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.user_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.deliveryMan.user_name 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.mobile" 
															placeholder="Delivery-Men Mobile" 
															required="true" 
															:class="!errors.deliveryMan.mobile  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.mobile')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.mobile }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.email" 
															placeholder="Login Email" 
															required="true" 
															:class="!errors.deliveryMan.email  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.email')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.password" 
															placeholder="Login Password" 
															:required="!editMode" 
															:class="!errors.deliveryMan.password  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.password')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Confirm Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.password_confirmation" 
															placeholder="Confirm Password" 
															:required="!editMode"
															:class="!errors.deliveryMan.password_confirmation  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.password_confirmation')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.password_confirmation }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Birth Date
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
									                  		type="date" 
									                  		class="form-control is-valid" 
															v-model="singleDeliveryManData.deliveryMan.birth_date" 
															placeholder="Date of Birth" 
															min="1980-01-01" 
															:max="currentDate" 
															required="true"
									                  	>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Gender
								              		</label>
									                <div class="col-sm-8">

									                  	<div class="custom-control custom-radio">
															<input 
																type="radio" 
																class="custom-control-input" 
																id="customControlValidation2" 
																name="gender" 
																value="male" 
																v-model="singleDeliveryManData.deliveryMan.gender" 
																:checked="singleDeliveryManData.deliveryMan.gender === 'male'" 
																required="true" 
															>
															<label class="custom-control-label" for="customControlValidation2">
																Male
															</label>
														</div>

														<div class="custom-control custom-radio mb-3">
															<input 
																type="radio" 
																class="custom-control-input" 
																id="customControlValidation3" 
																name="gender" 
																value="female" 
																v-model="singleDeliveryManData.deliveryMan.gender" 
																:checked="singleDeliveryManData.deliveryMan.gender === 'female'"
															>
															<label class="custom-control-label" for="customControlValidation3">
																Female
															</label>
														</div>

									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<div class="col-sm-4 text-center">
								                  		<img class="profile-user-img img-fluid" :src="singleDeliveryManData.deliveryMan.profile_pic_preview" alt="profile picture">
								                	</div>

									                <div class="col-sm-8">
									                  	<div class="input-group">
										                    <div class="custom-file">
										                        <input 
										                        	type="file" 
										                        	class="form-control custom-file-input" 
										                        	v-on:change="onProfilePicChange" 
										                        	accept="image/*" 
										                        	:class="!errors.deliveryMan.profile_pic_preview  ? 'is-valid' : 'is-invalid'" 
										                        	:required="!editMode" 
										                        >
										                        <label class="custom-file-label" for="exampleInputFile">
										                        	Change Picture
										                        </label>
										                    </div>
									                    </div>

									                	<div 
									                		class="text-danger" 
									                		v-show="errors.deliveryMan.profile_pic_preview"
									                	>
												        	{{ errors.deliveryMan.profile_pic_preview }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Present Address
								              		</label>
									                <div class="col-sm-8">
									                  	<ckeditor 
							                              	class="form-control" 
							                              	:editor="editor" 
							                              	v-model="singleDeliveryManData.deliveryMan.present_address"
							                              	:class="!errors.deliveryMan.present_address  ? 'is-valid' : 'is-invalid'"
							                              	@blur="validateFormInput('deliveryMan.present_address')"
							                            >
						                              	</ckeditor>

						                              	<div 
						                                  	class="invalid-feedback" 
						                                >
													        {{ errors.deliveryMan.present_address }}
													  	</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			NID Number
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="number" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.nid_number" 
															placeholder="National ID Number" 
															:required="!editMode"
															:class="!errors.deliveryMan.nid_number  ? 'is-valid' : 'is-invalid'" 
															@keyup="validateFormInput('deliveryMan.nid_number')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.deliveryMan.nid_number 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		
								              		<div class="col-sm-4 text-center">
								                  		<img class="profile-user-img img-fluid" :src="singleDeliveryManData.deliveryMan.nid_front_preview" alt="NID Front">
								                	</div>

									                <div class="col-sm-8">
									                  	<div class="input-group">
										                    <div class="custom-file">
										                        <input 
										                        	type="file" 
										                        	class="form-control custom-file-input" 
										                        	v-on:change="onNIDFrontChange" 
										                        	accept="image/*" 
										                        	:class="!errors.deliveryMan.nid_front_preview  ? 'is-valid' : 'is-invalid'" 
										                        	:required="!editMode" 
										                        >
										                        <label class="custom-file-label" for="exampleInputFile">
										                        	Change NID Front
										                        </label>
										                    </div>
									                    </div>

									                	<div 
									                		class="text-danger" 
									                		v-show="errors.deliveryMan.nid_front_preview"
									                	>
												        	{{ 
												        		errors.deliveryMan.nid_front_preview 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		
								              		<div class="col-sm-4 text-center">
								                  		<img class="profile-user-img img-fluid" :src="singleDeliveryManData.deliveryMan.nid_back_preview" alt="NID Back">
								                	</div>

									                <div class="col-sm-8">
									                  	<div class="input-group">
										                    <div class="custom-file">
										                        <input 
										                        	type="file" 
										                        	class="form-control custom-file-input"  
										                        	v-on:change="onNIDBackChange" 
										                        	accept="image/*" 
										                        	:class="!errors.deliveryMan.nid_back_preview  ? 'is-valid' : 'is-invalid'" 
										                        	:required="!editMode" 
										                        >
										                        <label class="custom-file-label" for="exampleInputFile">Change NID Back</label>
										                    </div>
									                    </div>

									                	<div 
									                		class="text-danger" 
									                		v-show="errors.deliveryMan.nid_back_preview"
									                	>
												        	{{ errors.deliveryMan.nid_back_preview }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Payment Method
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.payment_method" 
															placeholder="Eg. BKash, Rocket" 
															required="true"
															:class="!errors.deliveryMan.payment_method  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.payment_method')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.payment_method }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Account Number
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleDeliveryManData.deliveryMan.payment_account_number" 
															placeholder="Bank Account Number" 
															:required="!editMode"
															:class="!errors.deliveryMan.payment_account_number  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('deliveryMan.payment_account_number')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.deliveryMan.payment_account_number }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Admin Approval
								              		</label>
									                <div class="col-sm-8">
									                  	<toggle-button 
				                                  			:sync="true" 
				                                  			v-model="singleDeliveryManData.deliveryMan.admin_approval" 
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
								  		{{ editMode ? 'Update' : 'Create' }} Delivery-Men
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-deliveryMan-->

			<!-- modal-deliveryMan-delete-confirmation -->
			<div class="modal fade" id="modal-deliveryMan-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Delivery-Men Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyDeliveryMan" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete this deliveryMan ?? </h5>
					      		<h5 style="color:#c6cacc">
					      			<small>
					      				But once you want, you can retreive it from bin.
					      			</small>
					      		</h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">
							  		Close
							  	</button>

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
			<!-- /modal-deliveryMan-delete-confirmation -->

			<!-- modal-deliveryMan-restore-confirmation -->
			<div class="modal fade" id="modal-deliveryMan-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Delivery-Men Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreDeliveryMan()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore deliveryMan ?? </h5>
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
			<!-- /.modal-deliveryMan-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import { ToggleButton } from 'vue-js-toggle-button';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	var singleDeliveryManData = {

    	deliveryMan : {
			first_name : null,
			last_name : null,
			user_name : null,
			email : null,
			mobile : null,
			password : null,

			birth_date : null,
			gender : 'male',
			profile_pic_preview : null,
			present_address : null,
			nid_number : null,
			nid_front_preview : null,
			nid_back_preview : null,
			payment_method : null,
			payment_account_number : null,

			admin_approval : false,
    	},
    };

	var deliveryMenListData = {

      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	editor: ClassicEditor,
    	
    	currentTab : 'current',
    	allDeliveryMen : [],
    	deliveryMenToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		deliveryMan : {},
    	},

        newNIDBack : null,
        newNIDFront : null,
        newProfilePicture : null,
        singleDeliveryManData : singleDeliveryManData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    components: { 
			Multiselect, // short form of Multiselect : Multiselect
			ToggleButton : ToggleButton,
			ckeditor: CKEditor.component,
		},

	    data() {
	        return deliveryMenListData;
		},

		created(){
			this.fetchAllDeliveryMen();
		},

		computed: {
			currentDate: function () {
				var date = new Date();
				return date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
			}
		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchAllDeliveryMen();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},

		},

		methods : {
			showCurrentDeliveryMen(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedDeliveryMen(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.deliveryMenToShow = this.allDeliveryMen.current.data;
					this.pagination = this.allDeliveryMen.current;
				}else {
					this.deliveryMenToShow = this.allDeliveryMen.trashed.data;
					this.pagination = this.allDeliveryMen.trashed;
				}
			},
			fetchAllDeliveryMen(){
				this.loading = true;
				axios
					.get('/api/delivery-men/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allDeliveryMen = response.data;
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
					this.fetchAllDeliveryMen();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllDeliveryMen();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
    		onProfilePicChange(evnt){
    			let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {

					this.$delete(this.errors.deliveryMan, 'profile_pic_preview');
                	this.createImage(files[0], 'profile');

		      	}
		      	else {

		      		this.errors.deliveryMan.profile_pic_preview = 'Please upload an image';
		      		return;
		      	}

    		},
    		onNIDFrontChange(evnt){
    			
    			let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		
					this.$delete(this.errors.deliveryMan, 'nid_front_preview');
                	this.createImage(files[0], 'NIDFront');

		      	}
		      	else {
		      		
		      		this.errors.deliveryMan.nid_front_preview = 'Please upload an image';
		      		return;
		      	}

    		},
    		onNIDBackChange(evnt){
    			
    			let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {

		      		this.$delete(this.errors.deliveryMan, 'nid_back_preview');
                	this.createImage(files[0], 'NIDBack');

		      	}
		      	else {
		      		
		      		this.errors.deliveryMan.nid_back_preview = 'Please upload an image';
		      		return;
		      	}

    		},
    		createImage(file, $variable) {
                let reader = new FileReader();
                reader.onload = (evnt) => {
                	
                	if ($variable === 'profile') {
                		this.newProfilePicture = this.singleDeliveryManData.deliveryMan.profile_pic_preview = evnt.target.result;
                	}
                	else if ($variable === 'NIDFront') {
                		this.newNIDFront = this.singleDeliveryManData.deliveryMan.nid_front_preview = evnt.target.result;
                	}
                	else if ($variable === 'NIDBack') {
                		this.newNIDBack = this.singleDeliveryManData.deliveryMan.nid_back_preview = evnt.target.result;
                	}
                    
                };
                reader.readAsDataURL(file);
            },
			showDeliveryManCreateModal(){

				this.editMode = false;
				this.submitForm = true;
				this.errors.deliveryMan = {};

				this.singleDeliveryManData.deliveryMan = {};
				$('#modal-createOrEdit-deliveryMan').find('form')[0].reset();

				$('#modal-createOrEdit-deliveryMan').modal('show');
			},
    		storeDeliveryMan(){

				if (!this.singleDeliveryManData.deliveryMan.present_address) {
					
					this.submitForm = false;
					this.errors.deliveryMan.present_address = 'Address is required';
					
					return;
				}
				
				$('#modal-createOrEdit-deliveryMan').modal('hide');
				
				axios
					.post('/delivery-men/'+ this.perPage, this.singleDeliveryManData.deliveryMan)
					.then(response => {

						if (response.status == 200) {
							this.singleDeliveryManData.deliveryMan = {};

							this.allDeliveryMen = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

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
			showDeliveryManEditModal(deliveryMan) {
				
				this.editMode = true;
				this.submitForm = true;
				this.newNIDBack = null;
        		this.newNIDFront = null;
        		this.newProfilePicture = null;
				this.errors.deliveryMan = {};
				
				this.singleDeliveryManData.deliveryMan = deliveryMan;
				
				$("#modal-createOrEdit-deliveryMan").modal("show");
			},
			updateDeliveryMan(){				

				if (!this.singleDeliveryManData.deliveryMan.present_address) {
					
					this.submitForm = false;
					this.errors.deliveryMan.present_address = 'Address is required';
					
					return;
				}

				$('#modal-createOrEdit-deliveryMan').modal('hide');

				this.singleDeliveryManData.deliveryMan.profile_pic_preview = this.newProfilePicture;
				this.singleDeliveryManData.deliveryMan.nid_front_preview = this.newNIDFront;
				this.singleDeliveryManData.deliveryMan.nid_back_preview = this.newNIDBack;
				
				axios
					.put('/delivery-men/' + this.singleDeliveryManData.deliveryMan.id + '/' + this.perPage, this.singleDeliveryManData.deliveryMan)
					.then(response => {

						if (response.status == 200) {

							this.singleDeliveryManData.deliveryMan = {};
							this.allDeliveryMen = response.data;

							if (this.query === '') {
								this.showDataListOfSelectedTab();
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
			showDeliveryManDeletionModal(deliveryMan) {
				this.singleDeliveryManData.deliveryMan = deliveryMan;
				$("#modal-deliveryMan-delete-confirmation").modal("show");
			},
			destroyDeliveryMan(){

				$("#modal-deliveryMan-delete-confirmation").modal("hide");

				axios
					.delete('/delivery-men/'+this.singleDeliveryManData.deliveryMan.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleDeliveryManData.deliveryMan = {};

							this.allDeliveryMen = response.data;
							
							if (this.query === '') {
								this.showDataListOfSelectedTab();
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
			showDeliveryManRestoreModal(deliveryMan) {
				this.singleDeliveryManData.deliveryMan = deliveryMan;
				$("#modal-deliveryMan-restore-confirmation").modal("show");
			},
			restoreDeliveryMan(){

				$("#modal-deliveryMan-restore-confirmation").modal("hide");

				axios
					.patch('/delivery-men/' + this.singleDeliveryManData.deliveryMan.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleDeliveryManData.deliveryMan = {};
							
							this.allDeliveryMen = response.data;

							if (this.query === '') {
								this.showDataListOfSelectedTab();
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

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
				
				axios
				.get(
					"/api/delivery-men/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allDeliveryMen = response.data;
					this.deliveryMenToShow = this.allDeliveryMen.all.data;
					this.pagination = this.allDeliveryMen.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'deliveryMan.first_name' :

						if (this.singleDeliveryManData.deliveryMan.first_name && !this.singleDeliveryManData.deliveryMan.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.deliveryMan.first_name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'first_name');
						}

						break;	

					case 'deliveryMan.last_name' :

						if (this.singleDeliveryManData.deliveryMan.last_name && !this.singleDeliveryManData.deliveryMan.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.deliveryMan.last_name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'last_name');
						}

						break;	

					case 'deliveryMan.user_name' :

						if (!this.singleDeliveryManData.deliveryMan.user_name) {
							this.errors.deliveryMan.user_name = 'Username is required';
						}
						else if (!this.singleDeliveryManData.deliveryMan.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.deliveryMan.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'user_name');
						}

						break;

					case 'deliveryMan.email' :

						if (!this.singleDeliveryManData.deliveryMan.email) {
							this.errors.deliveryMan.email = 'Email is required';
						}
						else if (!this.singleDeliveryManData.deliveryMan.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.deliveryMan.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'email');
						}

						break;

					case 'deliveryMan.mobile' :

						if (!this.singleDeliveryManData.deliveryMan.mobile) {
							this.errors.deliveryMan.mobile = 'Mobile is required';
						}
						else if (!this.singleDeliveryManData.deliveryMan.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.deliveryMan.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'mobile');
						}

						break;

					case 'deliveryMan.password' :

						if (this.singleDeliveryManData.deliveryMan.password && this.singleDeliveryManData.deliveryMan.password.length < 8) {
							this.errors.deliveryMan.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'password');
						}

						break;

					case 'deliveryMan.password_confirmation' :

						if (this.singleDeliveryManData.deliveryMan.password && this.singleDeliveryManData.deliveryMan.password !== this.singleDeliveryManData.deliveryMan.password_confirmation) {
							this.errors.deliveryMan.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'password_confirmation');
						}

						break;

					case 'deliveryMan.present_address' :

						if (!this.singleDeliveryManData.deliveryMan.present_address) {
							this.errors.deliveryMan.present_address = 'Address is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'present_address');
						}

						break;

					case 'deliveryMan.nid_number' :

						if (!this.singleDeliveryManData.deliveryMan.nid_number) {
							this.errors.deliveryMan.nid_number = 'NID Number is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'nid_number');
						}

						break;

					case 'deliveryMan.payment_method' :

						if (!this.singleDeliveryManData.deliveryMan.payment_method) {
							this.errors.deliveryMan.payment_method = 'Payment method is required';
						}
						else if (this.singleDeliveryManData.deliveryMan.payment_method && !this.singleDeliveryManData.deliveryMan.payment_method.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.deliveryMan.payment_method = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'payment_method');
						}

						break;

					case 'deliveryMan.payment_account_number' :

						if (!this.singleDeliveryManData.deliveryMan.payment_account_number) {
							this.errors.deliveryMan.payment_account_number = 'Account number is required';
						}
						else if (!this.singleDeliveryManData.deliveryMan.payment_account_number.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.deliveryMan.payment_account_number = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.deliveryMan, 'payment_account_number');
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