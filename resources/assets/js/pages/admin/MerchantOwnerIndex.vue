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
							<h2 class="lead float-left mt-1">Merchant Owner List</h2>

                        	<button type="button" @click="showMerchantOwnerCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Merchant Owner
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='current' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showCurrentMerchantOwners"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedMerchantOwners"
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
											<th scope="col">Mobile</th>
											<th scope="col">Email</th>
											<th scope="col"># Merchants</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="merchantOwnersToShow.length"
									    	v-for="(merchantOwner, index) in merchantOwnersToShow"
									    	:key="merchantOwner.id"
									  	>
									    	<td scope="row">
									    		{{ index + 1 }}
									    	</td>
								    		<td>
								    			{{ merchantOwner.user_name | eachcapitalize }}
								    		</td>
								    		<td>
								    			{{ merchantOwner.mobile }}
								    		</td>
								    		<td>
								    			{{ merchantOwner.email }}
								    		</td>
								    		<td>
								    			{{ merchantOwner.merchants.length }}
								    		</td>
								    		<td>
										      	<button type="button" v-show="merchantOwner.deleted_at === null" @click="showMerchantOwnerEditModal(merchantOwner)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="merchantOwner.deleted_at === null"
								        			type="button"
								        			@click="showMerchantOwnerDeletionModal(merchantOwner)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="merchantOwner.deleted_at !== null"
								        			type="button"
								        			@click="showMerchantOwnerRestoreModal(merchantOwner)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!merchantOwnersToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No Owner found.</div>
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
										@click="query === '' ? fetchAllMerchantOwners() : searchData()"
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
										@paginate="query === '' ? fetchAllMerchantOwners() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-merchantOwner -->
			<div class="modal fade" id="modal-createOrEdit-merchantOwner">
				<div class="modal-dialog modal-lg">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Merchant Owner
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateMerchantOwner() : storeMerchantOwner()"
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
								              		<label for="inputOwnerName3" class="col-sm-4 col-form-label text-right">
								              			Username
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMerchantOwnerData.merchantOwner.user_name" 
															placeholder="Owner Username" 
															required="true"
															:class="!errors.merchantOwner.user_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('merchantOwner.user_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantOwner.user_name 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputOwnerName3" class="col-sm-4 col-form-label text-right">
								              			Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMerchantOwnerData.merchantOwner.mobile" 
															placeholder="Owner Mobile" 
															required="true"
															:class="!errors.merchantOwner.mobile  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('merchantOwner.mobile')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.merchantOwner.mobile }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputOwnerName3" class="col-sm-4 col-form-label text-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMerchantOwnerData.merchantOwner.email" 
															placeholder="Owner Email" 
															required="true"
															:class="!errors.merchantOwner.email  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('merchantOwner.email')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantOwner.email 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputOwnerName3" class="col-sm-4 col-form-label text-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleMerchantOwnerData.merchantOwner.password" 
															placeholder="Password" 
															:required="!editMode" 
															:class="!errors.merchantOwner.password  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('merchantOwner.password')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.merchantOwner.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputOwnerName3" class="col-sm-4 col-form-label text-right">
								              			Confirm Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleMerchantOwnerData.merchantOwner.password_confirmation" 
															placeholder="Confirm Password" 
															:required="!editMode"
															:class="!errors.merchantOwner.password_confirmation  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('merchantOwner.password_confirmation')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.merchantOwner.password_confirmation }}
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
								  		{{ editMode ? 'Update' : 'Create' }} Owner
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-merchantOwner-->

			<!-- modal-merchantOwner-delete-confirmation -->
			<div class="modal fade" id="modal-merchantOwner-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Merchant Owner Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="destroyMerchantOwner" 
						  	autocomplete="off"
					  	>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete this admin ??</h5>
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
			<!-- /modal-merchantOwner-delete-confirmation -->

			<!-- modal-merchantOwner-restore-confirmation -->
			<div class="modal fade" id="modal-merchantOwner-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Merchant Owner Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreMerchantOwner()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore merchant admin ??</h5>
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
			<!-- /.modal-merchantOwner-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleMerchantOwnerData = {
    	merchantOwner : {
			// id : null,
			// name : null,
			// email : null,
			// mobile : null,
			// deleted_at : null,
    	},
    };

	var merchantOwnerListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allMerchantOwners : [],
    	merchantOwnersToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		merchantOwner : {},
    	},

        singleMerchantOwnerData : singleMerchantOwnerData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return merchantOwnerListData;
		},

		created(){
			this.fetchAllMerchantOwners();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllMerchantOwners();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentMerchantOwners(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedMerchantOwners(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.merchantOwnersToShow = this.allMerchantOwners.current.data;
					this.pagination = this.allMerchantOwners.current;
				}else {
					this.merchantOwnersToShow = this.allMerchantOwners.trashed.data;
					this.pagination = this.allMerchantOwners.trashed;
				}
			},
			fetchAllMerchantOwners(){
				this.loading = true;
				axios
					.get('/api/merchant-owners/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchantOwners = response.data;
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
					this.fetchAllMerchantOwners();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMerchantOwners();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showMerchantOwnerCreateModal(){

				this.editMode = false;
				this.errors.merchantOwner = {};
				this.submitForm = true;

				this.singleMerchantOwnerData.merchantOwner = {};

				$('#modal-createOrEdit-merchantOwner').modal('show');
			},
    		storeMerchantOwner(){

				$('#modal-createOrEdit-merchantOwner').modal('hide');
				
				axios
					.post('/merchant-owners/'+ this.perPage, this.singleMerchantOwnerData.merchantOwner)
					.then(response => {

						if (response.status == 200) {
							this.singleMerchantOwnerData.merchantOwner = {};

							this.allMerchantOwners = response.data;

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
			showMerchantOwnerEditModal(merchantOwner) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.merchantOwner = {};
				this.singleMerchantOwnerData.merchantOwner = merchantOwner;
				$("#modal-createOrEdit-merchantOwner").modal("show");
			},
			updateMerchantOwner(){

				$('#modal-createOrEdit-merchantOwner').modal('hide');
				
				axios
					.put('/merchant-owners/' + this.singleMerchantOwnerData.merchantOwner.id + '/' + this.perPage, this.singleMerchantOwnerData.merchantOwner)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantOwnerData.merchantOwner = {};

							if (this.query === '') {
								this.allMerchantOwners = response.data;
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
			showMerchantOwnerDeletionModal(merchantOwner) {
				this.singleMerchantOwnerData.merchantOwner = merchantOwner;
				$("#modal-merchantOwner-delete-confirmation").modal("show");
			},
			destroyMerchantOwner(){

				$("#modal-merchantOwner-delete-confirmation").modal("hide");

				axios
					.delete('/merchant-owners/'+this.singleMerchantOwnerData.merchantOwner.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {

							this.singleMerchantOwnerData.merchantOwner = {};

							if (this.query === '') {
								this.allMerchantOwners = response.data;
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
			showMerchantOwnerRestoreModal(merchantOwner) {
				this.singleMerchantOwnerData.merchantOwner = merchantOwner;
				$("#modal-merchantOwner-restore-confirmation").modal("show");
			},
			restoreMerchantOwner(){

				$("#modal-merchantOwner-restore-confirmation").modal("hide");

				axios
					.patch('/merchant-owners/'+this.singleMerchantOwnerData.merchantOwner.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMerchantOwnerData.merchantOwner = {};

							if (this.query === '') {
								this.allMerchantOwners = response.data;
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
					"/api/merchant-owners/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allMerchantOwners = response.data;
					this.merchantOwnersToShow = this.allMerchantOwners.all.data;
					this.pagination = this.allMerchantOwners.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'merchantOwner.user_name' :

						if (!this.singleMerchantOwnerData.merchantOwner.user_name) {
							this.errors.merchantOwner.user_name = 'Username is required';
						}
						else if (!this.singleMerchantOwnerData.merchantOwner.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.merchantOwner.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantOwner, 'user_name');
						}

						break;

					case 'merchantOwner.email' :

						if (!this.singleMerchantOwnerData.merchantOwner.email) {
							this.errors.merchantOwner.email = 'Email is required';
						}
						else if (!this.singleMerchantOwnerData.merchantOwner.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.merchantOwner.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantOwner, 'email');
						}

						break;

					case 'merchantOwner.mobile' :

						if (!this.singleMerchantOwnerData.merchantOwner.mobile) {
							this.errors.merchantOwner.mobile = 'Mobile is required';
						}
						else if (!this.singleMerchantOwnerData.merchantOwner.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.merchantOwner.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantOwner, 'mobile');
						}

						break;

					case 'merchantOwner.password' :

						if (this.singleMerchantOwnerData.merchantOwner.password && this.singleMerchantOwnerData.merchantOwner.password.length < 8) {
							this.errors.merchantOwner.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantOwner, 'password');
						}

						break;

					case 'merchantOwner.password_confirmation' :

						if (this.singleMerchantOwnerData.merchantOwner.password && this.singleMerchantOwnerData.merchantOwner.password !== this.singleMerchantOwnerData.merchantOwner.password_confirmation) {
							this.errors.merchantOwner.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantOwner, 'password_confirmation');
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