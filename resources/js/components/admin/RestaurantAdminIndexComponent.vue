
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
						:rows="restaurantAdmins" 
						:columns="columns" 
						:config="config"
						:actions="actions"
					>

					</vue-bootstrap4-table> 
					-->

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">Restaurant Admin List</h2>

                        	<button type="button" @click="showRestaurantAdminCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant Admin
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showCurrentRestaurantAdmins">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedRestaurantAdmins">Trashed</a>
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
											<th scope="col">Username</th>
											<th scope="col">Mobile</th>
											<th scope="col">Email</th>
											<th scope="col">Number Restaurants</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="restaurantAdminsToShow.length"
									    	v-for="(restaurantAdmin, index) in restaurantAdminsToShow"
									    	:key="restaurantAdmin.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>
								    			{{ restaurantAdmin.user_name}}
								    		</td>
								    		<td>
								    			{{ restaurantAdmin.mobile}}
								    		</td>
								    		<td>
								    			{{ restaurantAdmin.email}}
								    		</td>
								    		<td>
								    			{{ restaurantAdmin.restaurants.length}}
								    		</td>
								    		<td>
										      	<button type="button" v-show="restaurantAdmin.deleted_at === null" @click="showRestaurantAdminEditModal(restaurantAdmin)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										      	</button>
								      			<button
								        			v-show="restaurantAdmin.deleted_at === null"
								        			type="button"
								        			@click="showRestaurantAdminDeletionModal(restaurantAdmin)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								      			</button>
								      			<button
								        			v-show="restaurantAdmin.deleted_at !== null"
								        			type="button"
								        			@click="showRestaurantAdminRestoreModal(restaurantAdmin)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!restaurantAdminsToShow.length">
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
										@click="query === '' ? fetchAllRestaurantAdmins() : searchData()"
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
										@paginate="query === '' ? fetchAllRestaurantAdmins() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-restaurantAdmin -->
			<div class="modal fade" id="modal-createOrEdit-restaurantAdmin">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Restaurant Admin</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateRestaurantAdmin() : storeRestaurantAdmin()"
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
								              		<label for="inputAdminName3" class="col-sm-4 col-form-label text-right">
								              			Admin Username
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleRestaurantAdminData.restaurantAdmin.user_name" 
															placeholder="Admin Username" 
															required="true"
															:class="!errors.restaurantAdmin.user_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantAdmin.user_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantAdmin.user_name }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAdminName3" class="col-sm-4 col-form-label text-right">
								              			Admin Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleRestaurantAdminData.restaurantAdmin.mobile" 
															placeholder="Admin Mobile" 
															required="true"
															:class="!errors.restaurantAdmin.mobile  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantAdmin.mobile')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantAdmin.mobile }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAdminName3" class="col-sm-4 col-form-label text-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleRestaurantAdminData.restaurantAdmin.email" 
															placeholder="Admin Email" 
															required="true"
															:class="!errors.restaurantAdmin.email  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantAdmin.email')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantAdmin.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAdminName3" class="col-sm-4 col-form-label text-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleRestaurantAdminData.restaurantAdmin.password" 
															placeholder="Admin Email" 
															:required="!editMode" 
															:class="!errors.restaurantAdmin.password  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantAdmin.password')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantAdmin.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAdminName3" class="col-sm-4 col-form-label text-right">
								              			Confirm Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleRestaurantAdminData.restaurantAdmin.password_confirmation" 
															placeholder="Admin Email" 
															:required="!editMode"
															:class="!errors.restaurantAdmin.password_confirmation  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantAdmin.password_confirmation')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantAdmin.password_confirmation }}
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
							  		{{ editMode ? 'Update' : 'Create' }} Admin
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-restaurantAdmin-->

			<!-- modal-restaurantAdmin-delete-confirmation -->
			<div class="modal fade" id="modal-restaurantAdmin-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant Admin Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyRestaurantAdmin" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete this admin ?? </h5>
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
			<!-- /modal-restaurantAdmin-delete-confirmation -->

			<!-- modal-restaurantAdmin-restore-confirmation -->
			<div class="modal fade" id="modal-restaurantAdmin-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant Admin Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreRestaurantAdmin()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore restaurant admin ?? </h5>
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
			<!-- /.modal-restaurantAdmin-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleRestaurantAdminData = {
    	restaurantAdmin : {
			// id : null,
			// name : null,
			// email : null,
			// mobile : null,
			// deleted_at : null,
    	},
    };

	var restaurantAdminListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allRestaurantAdmins : [],
    	restaurantAdminsToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		restaurantAdmin : {},
    	},

        singleRestaurantAdminData : singleRestaurantAdminData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return restaurantAdminListData;
		},

		created(){
			this.fetchAllRestaurantAdmins();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllRestaurantAdmins();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentRestaurantAdmins(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedRestaurantAdmins(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.restaurantAdminsToShow = this.allRestaurantAdmins.current.data;
					this.pagination = this.allRestaurantAdmins.current;
				}else {
					this.restaurantAdminsToShow = this.allRestaurantAdmins.trashed.data;
					this.pagination = this.allRestaurantAdmins.trashed;
				}
			},
			fetchAllRestaurantAdmins(){
				this.loading = true;
				axios
					.get('/api/restaurant-admins/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allRestaurantAdmins = response.data;
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
					this.fetchAllRestaurantAdmins();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllRestaurantAdmins();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showRestaurantAdminCreateModal(){

				this.editMode = false;
				this.errors.restaurantAdmin = {};
				this.submitForm = true;

				this.singleRestaurantAdminData.restaurantAdmin = {};

				$('#modal-createOrEdit-restaurantAdmin').modal('show');
			},
    		storeRestaurantAdmin(){

				$('#modal-createOrEdit-restaurantAdmin').modal('hide');
				
				axios
					.post('/restaurant-admins/'+ this.perPage, this.singleRestaurantAdminData.restaurantAdmin)
					.then(response => {

						if (response.status == 200) {
							this.singleRestaurantAdminData.restaurantAdmin = {};

							this.allRestaurantAdmins = response.data;

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
			showRestaurantAdminEditModal(restaurantAdmin) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurantAdmin = {};
				this.singleRestaurantAdminData.restaurantAdmin = restaurantAdmin;
				$("#modal-createOrEdit-restaurantAdmin").modal("show");
			},
			updateRestaurantAdmin(){

				$('#modal-createOrEdit-restaurantAdmin').modal('hide');
				
				axios
					.put('/restaurant-admins/' + this.singleRestaurantAdminData.restaurantAdmin.id + '/' + this.perPage, this.singleRestaurantAdminData.restaurantAdmin)
					.then(response => {

						if (response.status == 200) {

							this.singleRestaurantAdminData.restaurantAdmin = {};

							if (this.query === '') {
								this.allRestaurantAdmins = response.data;
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
			showRestaurantAdminDeletionModal(restaurantAdmin) {
				this.singleRestaurantAdminData.restaurantAdmin = restaurantAdmin;
				$("#modal-restaurantAdmin-delete-confirmation").modal("show");
			},
			destroyRestaurantAdmin(){

				$("#modal-restaurantAdmin-delete-confirmation").modal("hide");

				axios
					.delete('/restaurant-admins/'+this.singleRestaurantAdminData.restaurantAdmin.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {

							this.singleRestaurantAdminData.restaurantAdmin = {};

							if (this.query === '') {
								this.allRestaurantAdmins = response.data;
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
			showRestaurantAdminRestoreModal(restaurantAdmin) {
				this.singleRestaurantAdminData.restaurantAdmin = restaurantAdmin;
				$("#modal-restaurantAdmin-restore-confirmation").modal("show");
			},
			restoreRestaurantAdmin(){

				$("#modal-restaurantAdmin-restore-confirmation").modal("hide");

				axios
					.patch('/restaurant-admins/'+this.singleRestaurantAdminData.restaurantAdmin.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantAdminData.restaurantAdmin = {};

							if (this.query === '') {
								this.allRestaurantAdmins = response.data;
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
					"/api/restaurant-admins/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allRestaurantAdmins = response.data;
					this.restaurantAdminsToShow = this.allRestaurantAdmins.all.data;
					this.pagination = this.allRestaurantAdmins.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'restaurantAdmin.user_name' :

						if (!this.singleRestaurantAdminData.restaurantAdmin.user_name) {
							this.errors.restaurantAdmin.user_name = 'Username is required';
						}
						else if (!this.singleRestaurantAdminData.restaurantAdmin.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.restaurantAdmin.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'user_name');
						}

						break;

					case 'restaurantAdmin.email' :

						if (!this.singleRestaurantAdminData.restaurantAdmin.email) {
							this.errors.restaurantAdmin.email = 'Email is required';
						}
						else if (!this.singleRestaurantAdminData.restaurantAdmin.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.restaurantAdmin.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'email');
						}

						break;

					case 'restaurantAdmin.mobile' :

						if (!this.singleRestaurantAdminData.restaurantAdmin.mobile) {
							this.errors.restaurantAdmin.mobile = 'Mobile is required';
						}
						else if (!this.singleRestaurantAdminData.restaurantAdmin.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.restaurantAdmin.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'mobile');
						}

						break;

					case 'restaurantAdmin.password' :

						if (this.singleRestaurantAdminData.restaurantAdmin.password && this.singleRestaurantAdminData.restaurantAdmin.password.length < 8) {
							this.errors.restaurantAdmin.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'password');
						}

						break;

					case 'restaurantAdmin.password_confirmation' :

						if (this.singleRestaurantAdminData.restaurantAdmin.password && this.singleRestaurantAdminData.restaurantAdmin.password !== this.singleRestaurantAdminData.restaurantAdmin.password_confirmation) {
							this.errors.restaurantAdmin.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'password_confirmation');
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