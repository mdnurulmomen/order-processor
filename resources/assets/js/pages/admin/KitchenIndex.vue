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
		
			<div 
				class="row" 
				v-show="!loading"
			>
				<div class="col-sm-12">

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">
								Kitchen List
							</h2>

                        	<button 
	                        	type="button" 
	                        	@click="showKitchenCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Kitchen
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
													@click="showCurrentKitchens"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedKitchens"
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
											<th scope="col">Owner</th>
											<th scope="col">Mobile</th>
											<th scope="col">Email</th>
											<th scope="col">Approval</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="kitchensToShow.length"
									    	v-for="(kitchen, index) in kitchensToShow"
									    	:key="kitchen.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ kitchen.user_name | capitalize }}</td>
								    		<td>
								    			{{ 
								    				kitchen.merchant ? kitchen.merchant.name : 'Trashed' | eachcapitalize 
								    			}}
								    			<span v-show="kitchen.merchant" 
								    				  :class="[kitchen.merchant ? kitchen.merchant.is_approved ? 'badge-success' : 'badge-danger' : '', 'right badge ml-1']"
								    			>
								    				{{ 
								    					kitchen.merchant ? kitchen.merchant.is_approved ? 'Approved' : 'Not-approved' : '' 
								    				}}
								    			</span>
								    		</td>
								    		<td>{{ kitchen.mobile }}</td>
								    		<td>{{ kitchen.email }}</td>
								    		<td>
								    			<span :class="[kitchen.is_approved ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					kitchen.is_approved ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>
								    		</td>
								    		<td>
										      	<button type="button" v-show="kitchen.deleted_at === null" @click="showKitchenEditModal(kitchen)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="kitchen.deleted_at === null"
								        			type="button"
								        			@click="showMerchantKitchenDeletionModal(kitchen)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="kitchen.deleted_at !== null && kitchen.merchant !== null"
								        			type="button"
								        			@click="showKitchenRestoreModal(kitchen)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								      			<p 	class="text-danger" 
								      				v-show="kitchen.merchant === null"
								      			>
								      				Restore Merchant
								      			</p>
								    		</td>
									  	</tr>
									  	<tr v-show="!kitchensToShow.length">
								    		<td colspan="7">
									      		<div class="alert alert-danger" role="alert">Sorry, No kitchen found.</div>
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
										@click="query === '' ? fetchAllKitchens() : searchData()"
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
										@paginate="query === '' ? fetchAllKitchens() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-kitchen -->
			<div class="modal fade" id="modal-createOrEdit-kitchen">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Kitchen
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateKitchen() : storeKitchen()"
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
								              		<label for="inputKitchenName3" class="col-sm-4 col-form-label text-right">
								              			Merchant
								              		</label>
									                <div class="col-sm-8">
									                  	<multiselect 
				                                  			v-model="singleKitchenData.merchantObject"
				                                  			placeholder="Merchant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMerchants" 
					                                  		:required="true"
					                                  		:class="!errors.kitchen.merchant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
				                                  		>
					                                	</multiselect>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.kitchen.merchant 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputKitchenName3" class="col-sm-4 col-form-label text-right">
								              			Username
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleKitchenData.kitchen.user_name" 
															placeholder="Kitchen Mobile" 
															required="true"
															:class="!errors.kitchen.user_name  ? 'is-valid' : 'is-invalid'"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.kitchen.user_name 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputKitchenName3" class="col-sm-4 col-form-label text-right">
								              			Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleKitchenData.kitchen.mobile" 
															placeholder="Kitchen Mobile" 
															required="true"
															:class="!errors.kitchen.mobile  ? 'is-valid' : 'is-invalid'"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.kitchen.mobile }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputKitchenName3" class="col-sm-4 col-form-label text-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleKitchenData.kitchen.email" 
															placeholder="Kitchen Email" 
															:class="!errors.kitchen.email  ? 'is-valid' : 'is-invalid'"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.kitchen.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputKitchenName3" class="col-sm-4 col-form-label text-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleKitchenData.kitchen.password" 
															placeholder="Kitchen Password" 
															:required="!editMode" 
															:class="!errors.kitchen.password  ? 'is-valid' : 'is-invalid'"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.kitchen.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputKitchenName3" class="col-sm-4 col-form-label text-right">
								              			Confirm Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleKitchenData.kitchen.password_confirmation" 
															placeholder="Confirm Password" 
															:required="!editMode"
															:class="!errors.kitchen.password_confirmation  ? 'is-valid' : 'is-invalid'"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.kitchen.password_confirmation 
												        	}}
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
				                                  			v-model="singleKitchenData.kitchen.is_approved" 
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
								  		{{ editMode ? 'Update' : 'Create' }} Kitchen
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-kitchen-->

			<!-- modal-kitchen-delete-confirmation -->
			<div class="modal fade" id="modal-kitchen-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Kitchen Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="destroyKitchen" 
						  	autocomplete="off"
					  	>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete this kitchen ?? </h5>
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
			<!-- /modal-kitchen-delete-confirmation -->

			<!-- modal-kitchen-restore-confirmation -->
			<div class="modal fade" id="modal-kitchen-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Kitchen Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="restoreKitchen()" 
						  	autocomplete="off"
					  	>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore kitchen ??</h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">
							  		Close
							  	</button>

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
			<!-- /.modal-kitchen-restore-confirmation -->

	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import { ToggleButton } from 'vue-js-toggle-button';

	var singleKitchenData = {

    	kitchen : {
			user_name : null,
			mobile : null,
			email : null,
			password : null,
			merchant_id : null,
			is_approved : false,
    	},

    	merchantObject : {
    		// id : null,
			// email : null,
			// mobile : null,
    	}
    };

	var merchantKitchenListData = {

      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allKitchens : [],
    	kitchensToShow : [],

    	allMerchants : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		kitchen : {},
    	},

        singleKitchenData : singleKitchenData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    components: { 
			Multiselect, // short form of Multiselect : Multiselect
			ToggleButton : ToggleButton,
		},

	    data() {
	        return merchantKitchenListData;
		},

		created(){
			this.fetchAllKitchens();
			this.fetchAllMerchants();
		},

		watch : {
			'singleKitchenData.merchantObject' : function(merchantObject){
				if (merchantObject) {
					this.singleKitchenData.kitchen.merchant_id = merchantObject.id;
				}else
					this.singleKitchenData.kitchen.merchant_id = null;
			},
			query : function(val){
				if (val==='') {
					this.fetchAllKitchens();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentKitchens(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedKitchens(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.kitchensToShow = this.allKitchens.current.data;
					this.pagination = this.allKitchens.current;
				}else {
					this.kitchensToShow = this.allKitchens.trashed.data;
					this.pagination = this.allKitchens.trashed;
				}
			},
			fetchAllMerchants(){
				this.loading = true;
				axios
					.get('/api/merchants/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchants = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllKitchens(){
				this.loading = true;
				axios
					.get('/api/merchant-kitchens/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allKitchens = response.data;
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
					this.fetchAllKitchens();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllKitchens();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showKitchenCreateModal(){

				this.editMode = false;
				this.errors.kitchen = {};
				this.submitForm = true;

				this.singleKitchenData.kitchen = {};
				this.singleKitchenData.merchantObject = {};

				$('#modal-createOrEdit-kitchen').modal('show');
			},
    		storeKitchen(){

				if (Object.keys(this.singleKitchenData.merchantObject).length === 0) {
					
					this.submitForm = false;
					this.errors.kitchen.merchant = 'Merchant name is required';
					
					return;
				}
				
				axios
					.post('/merchant-kitchens/'+ this.perPage, this.singleKitchenData.kitchen)
					.then(response => {

						if (response.status == 200) {
							this.singleKitchenData.kitchen = {};

							this.allKitchens = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

							toastr.success(response.data.success, "Added");

							$('#modal-createOrEdit-kitchen').modal('hide');
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
			showKitchenEditModal(kitchen) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.kitchen = {};

				this.singleKitchenData.kitchen = kitchen;
				this.singleKitchenData.merchantObject = kitchen.merchant;

				$("#modal-createOrEdit-kitchen").modal("show");
			},
			updateKitchen(){

				if (Object.keys(this.singleKitchenData.merchantObject).length === 0) {
					
					this.submitForm = false;
					this.errors.kitchen.merchant = 'Merchant name is required';
					
					return;
				}
				
				axios
					.put('/merchant-kitchens/' + this.singleKitchenData.kitchen.id + '/' + this.perPage, this.singleKitchenData.kitchen)
					.then(response => {

						if (response.status == 200) {

							this.singleKitchenData.kitchen = {};

							if (this.query === '') {
								this.allKitchens = response.data;
								this.showDataListOfSelectedTab();
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Updated");

							$('#modal-createOrEdit-kitchen').modal('hide');
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
			showMerchantKitchenDeletionModal(kitchen) {
				this.singleKitchenData.kitchen = kitchen;
				$("#modal-kitchen-delete-confirmation").modal("show");
			},
			destroyKitchen(){

				$("#modal-kitchen-delete-confirmation").modal("hide");

				axios
					.delete('/merchant-kitchens/'+this.singleKitchenData.kitchen.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleKitchenData.kitchen = {};

							if (this.query === '') {
								this.allKitchens = response.data;
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
			showKitchenRestoreModal(kitchen) {
				this.singleKitchenData.kitchen = kitchen;
				$("#modal-kitchen-restore-confirmation").modal("show");
			},
			restoreKitchen(){

				$("#modal-kitchen-restore-confirmation").modal("hide");

				axios
					.patch('/merchant-kitchens/'+this.singleKitchenData.kitchen.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleKitchenData.kitchen = {};

							if (this.query === '') {
								this.allKitchens = response.data;
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
					"/api/merchant-kitchens/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allKitchens = response.data;
					this.kitchensToShow = this.allKitchens.all.data;
					this.pagination = this.allKitchens.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'kitchen.merchantObject' :

						if (Object.keys(singleKitchenData.merchantObject).length === 0) {
							this.errors.kitchen.merchant = 'Merchant name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.kitchen, 'merchant');
						}

						break;

					case 'kitchen.user_name' :

						if (!this.singleKitchenData.kitchen.user_name) {
							this.errors.kitchen.user_name = 'Username is required';
						}
						else if (!this.singleKitchenData.kitchen.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.kitchen.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.kitchen, 'user_name');
						}

						break;

					case 'kitchen.email' :

						if (this.singleKitchenData.kitchen.email && !this.singleKitchenData.kitchen.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.kitchen.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.kitchen, 'email');
						}

						break;

					case 'kitchen.mobile' :

						if (!this.singleKitchenData.kitchen.mobile) {
							this.errors.kitchen.mobile = 'Mobile is required';
						}
						else if (!this.singleKitchenData.kitchen.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.kitchen.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.kitchen, 'mobile');
						}

						break;

					case 'kitchen.password' :

						if (this.singleKitchenData.kitchen.password && this.singleKitchenData.kitchen.password.length < 8) {
							this.errors.kitchen.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.kitchen, 'password');
						}

						break;

					case 'kitchen.password_confirmation' :

						if (this.singleKitchenData.kitchen.password && this.singleKitchenData.kitchen.password !== this.singleKitchenData.kitchen.password_confirmation) {
							this.errors.kitchen.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.kitchen, 'password_confirmation');
						}

						break;
				}
	 
			},

		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
	
	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}
</style>