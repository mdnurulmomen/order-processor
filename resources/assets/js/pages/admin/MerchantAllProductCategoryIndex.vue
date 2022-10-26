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
								{{ merchantName | capitalize }}
								 
								Product Categories
							</h2>

                        	<button 
	                        	type="button" 
	                        	@click="showMerchantProductCategoryCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2 ml-1"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Product Categories
					      	</button>

					      	<button 
	                        	type="button" 
	                        	@click="showMerchantProducts()" 
	                        	class="btn btn-default btn-sm float-right"
                        	>
			        			<i class="fas fa-eye"></i>
			        			Products
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
												<a 
													:class="[{ 'active': currentTab=='current' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showMerchantCurrentProductCategories"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showMerchantTrashedProductCategories"
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
											<th scope="col">Category Name</th>
											<th scope="col">Serving From</th>
											<th scope="col">Serving To</th>
											<th scope="col">Discount</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="merchantProductCategoriesToShow.length"
									    	v-for="(merchantProductCategory, index) in merchantProductCategoriesToShow"
									    	:key="merchantProductCategory.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>
								    			{{ 
								    				merchantProductCategory.product_category ? merchantProductCategory.product_category.name : 'Trashed Product-Category' | capitalize 
								    			}}

								    			<span class="badge badge-info">{{ merchantProductCategory.merchant_products.length }}</span>
								    		</td>
								    		<td>
								    			{{ merchantProductCategory.serving_from }}
								    		</td>
								    		<td>
								    			{{ merchantProductCategory.serving_to }}
								    		</td>
								    		<td>
								    			{{ merchantProductCategory.discount || 0 }} %
								    		</td>
								    		<td>
										      	 
										      	<button type="button" 
										      		v-show="merchantProductCategory.deleted_at === null && merchantProductCategory.product_category !== null"
										      		@click="showMerchantProductCategoryEditModal(merchantProductCategory)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

										      	<button 
										      		type="button" 
										      		v-show="merchantProductCategory.deleted_at === null"
										      		@click="showMerchantProductCategoryDeleteModal(merchantProductCategory)" class="btn btn-danger btn-sm"
										      	>
										        	<i class="fas fa-trash"></i>
										        	Delete
										      	</button>

										      	<button 
										      		type="button" 
										      		v-show="merchantProductCategory.deleted_at !== null && merchantProductCategory.product_category && merchantProductCategory.product_category.deleted_at === null"
										      		@click="showMerchantProductCategoryRestoreModal(merchantProductCategory)" class="btn btn-danger btn-sm"
										      	>
										        	<i class="fas fa-undo"></i>
										        	Restore
										      	</button>

										      	<p 
										      		class="text-danger" 
										      		v-show="merchantProductCategory.deleted_at !== null && merchantProductCategory.product_category && merchantProductCategory.product_category.deleted_at !== null"
										      	>
										      		Trashed Product-Category
										      	</p>
 												
								    		</td>
									  	</tr>
									  	<tr v-show="!merchantProductCategoriesToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Product Category found.
									      		</div>
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
										<option>40</option>
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchMerchantAllProductCategories() : searchData()"
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
										@paginate="query === '' ? fetchMerchantAllProductCategories() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-merchantProductCategory -->
			<div class="modal fade" id="modal-createOrEdit-merchantProductCategory">
				<div class="modal-dialog modal-lg">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} 
						  		{{ merchantName }} Product-Category
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="editMode ? updateMerchantProductCategory() : storeMerchantProductCategory()"
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
									              		
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Merchant Name
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="merchant" 
				                                  			class="form-control is-valid p-0" 
				                                  			placeholder="Merchant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="[]" 
					                                  		:required="true" 
					                                  		:disabled="true"
				                                  		>
					                                	</multiselect>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Product-Categories
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleMerchantProductCategoryData.productCategories"
				                                  			placeholder="Product Categories" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allProductCategories" 
					                                  		:required="true" 
					                                  		:multiple="!editMode" 
					                                  		:close-on-select="false"
					                                  		:class="!errors.productCategory ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value" 
					                                  		@input="setProductCategoryIdCollection()"
					                                  		@close="validateFormInput('merchantProductCategory.productCategory')" 
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{
												        		errors.productCategory 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right">
								              			Serving From
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleMerchantProductCategoryData.serving_from"
				                                  			placeholder="Category Name" 
					                                  		:options="merchantScheduleHours" 
					                                  		:show-labels="false"  
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove is required"
				                                  		>
					                                	</multiselect>
									                </div>
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right">
								              			Serving To
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleMerchantProductCategoryData.serving_to"
				                                  			placeholder="Category Name" 
					                                  		:options="merchantScheduleHours" 
					                                  		:show-labels="false"  
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove"
				                                  		>
					                                	</multiselect>
									                </div>
								              	</div>

								              	<div class="form-group row">	
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Discount
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMerchantProductCategoryData.discount" 
															placeholder="Discount" 
															:class="!errors.discount  ? 'is-valid' : 'is-invalid'" 
															@keyup="validateFormInput('discount')"
															required="true"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.discount 
												        	}}
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
								  		{{ editMode ? 'Update' : 'Create' }} Product-Category
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-merchantProductCategory-->

			<!-- modal-merchantProductCategory-delete-confirmation -->
			<div class="modal fade" id="modal-merchantProductCategory-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Merchant-Product-Category Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
						  	v-on:submit.prevent="destroyMerchantProductCategory" 
						  	autocomplete="off"
					  	>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete product-category ??</h5>
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
			<!-- /modal-merchantProductCategory-delete-confirmation -->

			<!-- modal-merchantProductCategory-restore-confirmation -->
			<div class="modal fade" id="modal-merchantProductCategory-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Merchant-Product-Category Restoration
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
						  	v-on:submit.prevent="restoreMerchantProductCategory" 
						  	autocomplete="off"
						>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore meal ??</h5>
							</div>
							<div class="modal-footer justify-content-between">
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
			<!-- /.modal-merchantProductCategory-restore-confirmation-->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleMerchantProductCategoryData = {

		productCategoriesId : [],	// for create mode
    	productCategories : [], 

		serving_from : '10.00',
		serving_to : '22.00',
		merchant_id : null,
		product_category : {},
		merchantProducts : [],
    	
    };

	var productCategoryListData = {
      	query : '',
    	perPage : 10,
    	currentTab : 'current',

    	loading : false,
    	editMode : false,
    	submitForm : true,

    	merchant : {
			
    	},
    	
    	allProductCategories : [],
    	
    	merchantAllProductCategories : [],
		merchantProductCategoriesToShow : [],

    	merchantScheduleHours : ['6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00', '20.00', '21.00', '22.00', '23.00', '24.00', '1.00', '2.00'],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		
    	},

        singleMerchantProductCategoryData : singleMerchantProductCategoryData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

		// Local registration of components
		components: {
			Multiselect, // short form of Multiselect : Multiselect
		},

		props: {

			merchantId:{
				type: Number,
				required: true,
			},
			merchantName:{
				type: String,
				required: true,
			},

		},

	    data() {
	        return productCategoryListData;
		},

		created(){
			this.fetchAllProductCategories();
			this.fetchMerchantAllProductCategories();
			this.setCurrentMerchant();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchMerchantAllProductCategories();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
		},

		methods : {

			fetchAllProductCategories(){
				this.loading = true;
				axios
					.get('/api/product-categories/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allProductCategories = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchMerchantAllProductCategories() {
				this.loading = true;
				axios
					.get('/api/merchant-product-categories/' + this.merchantId + '/' + this.perPage + "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.merchantAllProductCategories = response.data;
							// this.pagination = response.data;
							this.showDataListOfSelectedTab();
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			setCurrentMerchant() {

				this.merchant = {

					id : this.merchantId,
					name : this.merchantName,

				}

				this.singleMerchantProductCategoryData.merchant_id = this.merchant.id;

			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchMerchantAllProductCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchMerchantAllProductCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showMerchantProductCategoryCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors = {};
				
				this.singleMerchantProductCategoryData.productCategories = [];
				
				this.singleMerchantProductCategoryData.serving_from = this.singleMerchantProductCategoryData.serving_to = null;

				$('#modal-createOrEdit-merchantProductCategory').modal('show');
			},
			showMerchantProductCategoryEditModal(merchantProductCategory) {

				this.editMode = true;
				this.submitForm = true;
				this.errors = {};
				
				this.singleMerchantProductCategoryData = merchantProductCategory;

				var array = [];
				
				if (merchantProductCategory.product_category) {
					array.push(merchantProductCategory.product_category);
				}

				this.singleMerchantProductCategoryData.productCategories = array;

				this.setProductCategoryIdCollection();

				$("#modal-createOrEdit-merchantProductCategory").modal("show");
			},
    		storeMerchantProductCategory(){

    			if (! this.singleMerchantProductCategoryData.productCategoriesId || this.singleMerchantProductCategoryData.productCategoriesId.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-merchantProductCategory').modal('hide');
				
				axios
					.post('/merchant-product-categories/' + this.perPage, this.singleMerchantProductCategoryData)
					.then(response => {

						if (response.status == 200) {
							
							this.singleMerchantProductCategoryData.productCategories = [];

							this.merchantAllProductCategories = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

							// this.pagination = response.data;

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
			updateMerchantProductCategory(){

				if (! this.singleMerchantProductCategoryData.productCategoriesId || this.singleMerchantProductCategoryData.productCategoriesId.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-merchantProductCategory').modal('hide');
				
				axios
					.put('/merchant-product-categories/' + this.singleMerchantProductCategoryData.id + '/' + this.perPage, this.singleMerchantProductCategoryData)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantProductCategoryData.productCategories = [];

							if (this.query === '') {
								this.merchantAllProductCategories = response.data;
								// this.pagination = response.data;
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
			showMerchantProductCategoryDeleteModal(merchantProductCategory){
				
				this.singleMerchantProductCategoryData = merchantProductCategory;

				$("#modal-merchantProductCategory-delete-confirmation").modal("show");

			},
			destroyMerchantProductCategory(){

				$('#modal-merchantProductCategory-delete-confirmation').modal('hide');
				
				axios
					.delete('/merchant-product-categories/' + this.singleMerchantProductCategoryData.id + '/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {

						if (response.status == 200) {

							if (this.query === '') {
								this.merchantAllProductCategories = response.data;
								// this.pagination = response.data;
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
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});
			},
			showMerchantProductCategoryRestoreModal(merchantProductCategory){
				
				this.singleMerchantProductCategoryData = merchantProductCategory;

				$("#modal-merchantProductCategory-restore-confirmation").modal("show");

			},
			restoreMerchantProductCategory(){

				$('#modal-merchantProductCategory-restore-confirmation').modal('hide');
				
				axios
					.patch('/merchant-product-categories/' + this.singleMerchantProductCategoryData.id + '/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {

						if (response.status == 200) {

							if (this.query === '') {
								this.merchantAllProductCategories = response.data;
								// this.pagination = response.data;
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
					"/api/search-merchant-product-categories/" + this.merchantId + "/"  + this.query + "/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {

					this.merchantAllProductCategories = response.data;
					
					this.merchantProductCategoriesToShow = this.merchantAllProductCategories.all.data;
					this.pagination = this.merchantAllProductCategories.all;

				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					/*
					case 'merchantProductCategory.merchant' :

						if (Object.keys(this.singleMerchantProductCategoryData.merchant).length === 0) {
							this.errors.merchant = 'Merchant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'merchant');
						}

						break;
					*/

					case 'merchantProductCategory.productCategory' :

						if (this.singleMerchantProductCategoryData.productCategories.length === 0) {
							this.errors.productCategory = 'Product Category name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'productCategory');
						}

						break;

					case 'discount' :

						if (this.singleMerchantProductData.discount < 0 || this.singleMerchantProductData.discount > 100) {
							this.errors.discount = 'Value should be between 0 to 100';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'discount');
						}

						break;
				}
	 
			},
			showMerchantCurrentProductCategories(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showMerchantTrashedProductCategories(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.merchantProductCategoriesToShow = this.merchantAllProductCategories.current.data;
					this.pagination = this.merchantAllProductCategories.current;
				}else {
					this.merchantProductCategoriesToShow = this.merchantAllProductCategories.trashed.data;
					this.pagination = this.merchantAllProductCategories.trashed;
				}
			},
			showMerchantProducts() {
				this.$router.push({
			 		name: 'merchant-all-products', 
			 		params: { 
			 			merchantId : this.merchantId, 
			 			merchantName : this.merchantName, 
			 		}, 
				});
			},
			setProductCategoryIdCollection() {

				if (Array.isArray(this.singleMerchantProductCategoryData.productCategories) && this.singleMerchantProductCategoryData.productCategories.length) {

					let array = [];

					$.each(this.singleMerchantProductCategoryData.productCategories, function(key, value) {
				     	array.push(value.id);
				   	});

			     	this.singleMerchantProductCategoryData.productCategoriesId = array;

				}
				/*
				else{

					if (this.singleMerchantProductCategoryData.productCategories) {
						this.singleMerchantProductCategoryData.productCategoriesId[0] = this.singleMerchantProductCategoryData.productCategories.id;
					}
					else
						this.singleMerchantProductCategoryData.productCategoriesId = [];
				}
				*/

			}

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