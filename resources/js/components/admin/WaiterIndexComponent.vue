
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
							<h2 class="lead float-left mt-1">Waiter List</h2>

                        	<button type="button" 
                    				@click="showWaiterCreateModal" 
                    				class="btn btn-secondary btn-sm float-right mb-2"
                    		>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Waiter
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" 
									  		v-show="query === ''"
									  	>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showCurrentWaiters"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedWaiters"
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
											<th scope="col">Owner Restaurant Name</th>
											<th scope="col">Mobile</th>
											<th scope="col">Email</th>
											<th scope="col">Waiter Approved</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="waitersToShow.length"
									    	v-for="(waiter, index) in waitersToShow"
									    	:key="waiter.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ waiter.user_name}}</td>
								    		<td>
								    			{{ 
								    				waiter.restaurant ? waiter.restaurant.name : 'Trashed' 
								    			}}
								    			<span v-show="waiter.restaurant" 
								    				  :class="[waiter.restaurant ? waiter.restaurant.admin_approval ? 'badge-success' : 'badge-danger' : '', 'right badge ml-1']"
								    			>
								    				{{ 
								    					waiter.restaurant ? waiter.restaurant.admin_approval ? 'Approved' : 'Not-approved' : '' 
								    				}}
								    			</span>
								    		</td>
								    		<td>{{ waiter.mobile }}</td>
								    		<td>{{ waiter.email }}</td>
								    		<td>
								    			<span :class="[waiter.admin_approval ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					waiter.admin_approval ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>
								    		</td>
								    		<td>
										      	<button type="button" v-show="waiter.deleted_at === null" @click="showWaiterEditModal(waiter)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="waiter.deleted_at === null"
								        			type="button"
								        			@click="showRestaurantWaiterDeletionModal(waiter)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="waiter.deleted_at !== null && waiter.restaurant !== null"
								        			type="button"
								        			@click="showWaiterRestoreModal(waiter)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								      			<p 	class="text-danger" 
								      				v-show="waiter.restaurant === null"
								      			>
								      				Trashed Restaurant
								      			</p>
								    		</td>
									  	</tr>
									  	<tr v-show="!waitersToShow.length">
								    		<td colspan="7">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No waiter found.
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
										@click="query === '' ? fetchAllWaiters() : searchData()"
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
										@paginate="query === '' ? fetchAllWaiters() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-waiter -->
			<div class="modal fade" id="modal-createOrEdit-waiter">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Waiter
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateWaiter() : storeWaiter()"
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
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Restaurant Name
								              		</label>
									                <div class="col-sm-8">
									                  	<multiselect 
				                                  			v-model="singleWaiterData.restaurantObject"
				                                  			placeholder="Restaurant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allRestaurants" 
					                                  		:required="true"
					                                  		:class="!errors.waiter.restaurant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('waiter.restaurantObject')"
				                                  		>
					                                	</multiselect>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.restaurant }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Waiter Firstname
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleWaiterData.waiter.first_name" 
															placeholder="Waiters Name" 
															required="true"
															:class="!errors.waiter.first_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('waiter.first_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.first_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Waiter Lastname
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleWaiterData.waiter.last_name" 
															placeholder="Waiters Name" 
															required="true"
															:class="!errors.waiter.last_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('waiter.last_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.last_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Waiter Username
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleWaiterData.waiter.user_name" 
															placeholder="Login Username" 
															required="true"
															:class="!errors.waiter.user_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('waiter.user_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.user_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Waiter Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleWaiterData.waiter.mobile" 
															placeholder="Waiter Mobile" 
															required="true"
															:class="!errors.waiter.mobile  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('waiter.mobile')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.mobile }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleWaiterData.waiter.email" 
															placeholder="Login Email" 
															required="true"
															:class="!errors.waiter.email  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('waiter.email')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleWaiterData.waiter.password" 
															placeholder="Login Password" 
															:required="!editMode" 
															:class="!errors.waiter.password  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('waiter.password')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Confirm Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleWaiterData.waiter.password_confirmation" 
															placeholder="Confirm Password" 
															:required="!editMode"
															:class="!errors.waiter.password_confirmation  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('waiter.password_confirmation')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.waiter.password_confirmation }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputWaiterName3" class="col-sm-4 col-form-label text-right">
								              			Admin Approval
								              		</label>
									                <div class="col-sm-8">
									                  	<toggle-button 
				                                  			:sync="true" 
				                                  			v-model="singleWaiterData.waiter.admin_approval" 
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
							  		{{ editMode ? 'Update' : 'Create' }} Waiter
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-waiter-->

			<!-- modal-waiter-delete-confirmation -->
			<div class="modal fade" id="modal-waiter-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Waiter Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyWaiter" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete this waiter ?? </h5>
					      		<h5 class="text-secondary">
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
			<!-- /modal-waiter-delete-confirmation -->

			<!-- modal-waiter-restore-confirmation -->
			<div class="modal fade" id="modal-waiter-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Waiter Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreWaiter()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore waiter ?? </h5>
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
			<!-- /.modal-waiter-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import { ToggleButton } from 'vue-js-toggle-button';

	var singleWaiterData = {

    	waiter : {
			first_name : null,
			last_name : null,
			user_name : null,
			email : null,
			mobile : null,
			password : null,
			restaurant_id : null,
			admin_approval : false,
    	},

    	restaurantObject : {
    		// id : null,
			// email : null,
			// mobile : null,
    	}
    };

	var restaurantWaiterListData = {

      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allWaiters : [],
    	waitersToShow : [],

    	allRestaurants : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		waiter : {},
    	},

        singleWaiterData : singleWaiterData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    components: { 
			Multiselect, // short form of Multiselect : Multiselect
			ToggleButton : ToggleButton,
		},

	    data() {
	        return restaurantWaiterListData;
		},

		created(){
			this.fetchAllWaiters();
			this.fetchAllRestaurants();
		},

		watch : {
			'singleWaiterData.restaurantObject' : function(restaurantObject){
				if (restaurantObject) {
					this.singleWaiterData.waiter.restaurant_id = restaurantObject.id;
				}else
					this.singleWaiterData.waiter.restaurant_id = null;
			},
			query : function(val){
				if (val==='') {
					this.fetchAllWaiters();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentWaiters(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedWaiters(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.waitersToShow = this.allWaiters.current.data;
					this.pagination = this.allWaiters.current;
				}else {
					this.waitersToShow = this.allWaiters.trashed.data;
					this.pagination = this.allWaiters.trashed;
				}
			},
			fetchAllRestaurants(){
				this.loading = true;
				axios
					.get('/api/restaurants/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurants = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllWaiters(){
				this.loading = true;
				axios
					.get('/api/restaurant-waiters/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allWaiters = response.data;
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
					this.fetchAllWaiters();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllWaiters();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showWaiterCreateModal(){

				this.editMode = false;
				this.errors.waiter = {};
				this.submitForm = true;

				this.singleWaiterData.waiter = {};

				$('#modal-createOrEdit-waiter').modal('show');
			},
    		storeWaiter(){

				if (Object.keys(this.singleWaiterData.restaurantObject).length === 0) {
					this.errors.waiter.restaurant = 'Restaurant name is required';
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-waiter').modal('hide');
				
				axios
					.post('/restaurant-waiters/'+ this.perPage, this.singleWaiterData.waiter)
					.then(response => {

						if (response.status == 200) {
							this.singleWaiterData.waiter = {};

							this.allWaiters = response.data;

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
			showWaiterEditModal(waiter) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.waiter = {};
				this.singleWaiterData.waiter = waiter;
				this.singleWaiterData.restaurantObject = waiter.restaurant;
				$("#modal-createOrEdit-waiter").modal("show");
			},
			updateWaiter(){

				$('#modal-createOrEdit-waiter').modal('hide');
				
				axios
					.put('/restaurant-waiters/' + this.singleWaiterData.waiter.id + '/' + this.perPage, this.singleWaiterData.waiter)
					.then(response => {

						if (response.status == 200) {

							this.singleWaiterData.waiter = {};

							if (this.query === '') {
								this.allWaiters = response.data;
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
			showRestaurantWaiterDeletionModal(waiter) {
				this.singleWaiterData.waiter = waiter;
				$("#modal-waiter-delete-confirmation").modal("show");
			},
			destroyWaiter(){

				$("#modal-waiter-delete-confirmation").modal("hide");

				axios
					.delete('/restaurant-waiters/'+this.singleWaiterData.waiter.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleWaiterData.waiter = {};

							if (this.query === '') {
								this.allWaiters = response.data;
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
			showWaiterRestoreModal(waiter) {
				this.singleWaiterData.waiter = waiter;
				$("#modal-waiter-restore-confirmation").modal("show");
			},
			restoreWaiter(){

				$("#modal-waiter-restore-confirmation").modal("hide");

				axios
					.patch('/restaurant-waiters/'+this.singleWaiterData.waiter.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleWaiterData.waiter = {};

							if (this.query === '') {
								this.allWaiters = response.data;
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
					"/api/restaurant-waiters/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allWaiters = response.data;
					this.waitersToShow = this.allWaiters.all.data;
					this.pagination = this.allWaiters.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'waiter.restaurantObject' :

						if (Object.keys(singleWaiterData.restaurantObject).length === 0) {
							this.errors.waiter.restaurant = 'Restaurant name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'restaurant');
						}

						break;

					case 'waiter.first_name' :

						if (this.singleWaiterData.waiter.first_name && !this.singleWaiterData.waiter.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.waiter.first_name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'first_name');
						}

						break;

					case 'waiter.last_name' :

						if (this.singleWaiterData.waiter.last_name && !this.singleWaiterData.waiter.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.waiter.last_name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'last_name');
						}

						break;	

					case 'waiter.user_name' :

						if (!this.singleWaiterData.waiter.user_name) {
							this.errors.waiter.user_name = 'Username is required';
						}
						else if (!this.singleWaiterData.waiter.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.waiter.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'user_name');
						}

						break;

					case 'waiter.email' :

						if (!this.singleWaiterData.waiter.email) {
							this.errors.waiter.email = 'Email is required';
						}
						else if (!this.singleWaiterData.waiter.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.waiter.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'email');
						}

						break;

					case 'waiter.mobile' :

						if (!this.singleWaiterData.waiter.mobile) {
							this.errors.waiter.mobile = 'Mobile is required';
						}
						else if (!this.singleWaiterData.waiter.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.waiter.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'mobile');
						}

						break;

					case 'waiter.password' :

						if (this.singleWaiterData.waiter.password && this.singleWaiterData.waiter.password.length < 8) {
							this.errors.waiter.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'password');
						}

						break;

					case 'waiter.password_confirmation' :

						if (this.singleWaiterData.waiter.password && this.singleWaiterData.waiter.password !== this.singleWaiterData.waiter.password_confirmation) {
							this.errors.waiter.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.waiter, 'password_confirmation');
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