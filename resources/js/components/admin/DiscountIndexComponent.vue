
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
						:rows="discounts" 
						:columns="columns" 
						:config="config"
						:actions="actions"
					>

					</vue-bootstrap4-table> 
					-->

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">Discount List</h2>

                        	<button type="button" @click="showDiscountCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Discount
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showCurrentDiscounts">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedDiscounts">Trashed</a>
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
											<th scope="col">Rate</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="discountsToShow.length"
									    	v-for="(discount, index) in discountsToShow"
									    	:key="discount.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ discount.rate}}</td>
								    		<td>
										      	<button type="button" v-show="discount.deleted_at === null" @click="showDiscountEditModal(discount)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										      	</button>
								      			<button
								        			v-show="discount.deleted_at === null"
								        			type="button"
								        			@click="showDiscountDeletionModal(discount)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								      			</button>
								      			<button
								        			v-show="discount.deleted_at !== null"
								        			type="button"
								        			@click="showDiscountRestoreModal(discount)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!discountsToShow.length">
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
										@click="query === '' ? fetchAllDiscounts() : searchData()"
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
										@paginate="query === '' ? fetchAllDiscounts() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-discount -->
			<div class="modal fade" id="modal-createOrEdit-discount">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Discount</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateDiscount() : storeDiscount()"
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
									              		
								              		<label for="inputDiscountRate3" class="col-sm-4 col-form-label text-right">Discount Rate</label>
									                <div class="col-sm-8">
									                	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model="singleDiscountData.discount.rate" 
																min="1" 
																max="100" 
																step=".1" 
																placeholder="Discount Rate" 
																required="true"
																:class="!errors.discount.rate  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('discount.rate')"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.discount.rate }}
													  		</div>
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
							  		{{ editMode ? 'Update' : 'Create' }} Discount
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-discount-->

			<!-- modal-discount-delete-confirmation -->
			<div class="modal fade" id="modal-discount-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Discount Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyDiscount" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			rate="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete discount ?? </h5>
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
			<!-- /modal-discount-delete-confirmation -->

			<!-- modal-discount-restore-confirmation -->
			<div class="modal fade" id="modal-discount-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Discount Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreDiscount()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			rate="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore discount ?? </h5>
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
			<!-- /.modal-discount-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleDiscountData = {
    	discount : {
			// id : null,
			// rate : null,
			// deleted_at : null,
    	},
    };

	var discountListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allDiscounts : [],
    	discountsToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		discount : {},
    	},

        singleDiscountData : singleDiscountData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return discountListData;
		},

		created(){
			this.fetchAllDiscounts();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllDiscounts();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentDiscounts(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedDiscounts(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.discountsToShow = this.allDiscounts.current.data;
					this.pagination = this.allDiscounts.current;
				}else {
					this.discountsToShow = this.allDiscounts.trashed.data;
					this.pagination = this.allDiscounts.trashed;
				}
			},
			fetchAllDiscounts(){
				this.loading = true;
				axios
					.get('/api/discounts/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allDiscounts = response.data;
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
					this.fetchAllDiscounts();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllDiscounts();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showDiscountCreateModal(){

				this.editMode = false;
				this.errors.discount = {};
				this.submitForm = true;

				this.singleDiscountData.discount = {};

				$('#modal-createOrEdit-discount').modal('show');
			},
    		storeDiscount(){

				$('#modal-createOrEdit-discount').modal('hide');
				
				axios
					.post('/discounts/'+ this.perPage, this.singleDiscountData.discount)
					.then(response => {

						if (response.status == 200) {
							this.singleDiscountData.discount = {};

							this.allDiscounts = response.data;

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
			showDiscountEditModal(discount) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.discount = {};
				this.singleDiscountData.discount = discount;
				$("#modal-createOrEdit-discount").modal("show");
			},
			updateDiscount(){

				$('#modal-createOrEdit-discount').modal('hide');
				
				axios
					.put('/discounts/' + this.singleDiscountData.discount.id + '/' + this.perPage, this.singleDiscountData.discount)
					.then(response => {

						if (response.status == 200) {

							this.singleDiscountData.discount = {};

							if (this.query === '') {
								this.allDiscounts = response.data;
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
			showDiscountDeletionModal(discount) {
				this.singleDiscountData.discount = discount;
				$("#modal-discount-delete-confirmation").modal("show");
			},
			destroyDiscount(){

				$("#modal-discount-delete-confirmation").modal("hide");

				axios
					.delete('/discounts/'+this.singleDiscountData.discount.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleDiscountData.discount = {};

							if (this.query === '') {
								this.allDiscounts = response.data;
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
			showDiscountRestoreModal(discount) {
				this.singleDiscountData.discount = discount;
				$("#modal-discount-restore-confirmation").modal("show");
			},
			restoreDiscount(){

				$("#modal-discount-restore-confirmation").modal("hide");

				axios
					.patch('/discounts/'+this.singleDiscountData.discount.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleDiscountData.discount = {};

							if (this.query === '') {
								this.allDiscounts = response.data;
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
					"/api/discounts/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allDiscounts = response.data;
					this.discountsToShow = this.allDiscounts.all.data;
					this.pagination = this.allDiscounts.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputRate) {
				
				this.submitForm = false;

				if (!this.singleDiscountData.discount.rate) {
					this.errors.discount.rate = 'Discount rate is required';
				}
				else if (this.singleDiscountData.discount.rate < 1 || this.singleDiscountData.discount.rate > 100 ) {
					this.errors.discount.rate = 'Rate should be between 1 and 100';
				}
				else{
					this.submitForm = true;
					this.$delete(this.errors.discount, 'rate');
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