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
								All Merchant Product Categories
							</h2>
							 
                        	<button 
                        		type="button" 
                        		@click="showMerchantProductCategoryCreateModal"
                        	 	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Product Categories
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-12 float-right was-validated">
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
											<th scope="col">
												Merchant Name
											</th>
											<th scope="col">
												Product Categories
											</th>
											<th scope="col">
												Action
											</th>
										</tr>
									</thead>
									<tbody>
									  	<tr 
									  		v-show="allMerchantProductCategories.length"
									    	v-for="(merchant, index) in allMerchantProductCategories"
									    	:key="merchant.id"
									  	>
									    	<td scope="row">
									    		{{ index + 1 }}
									    	</td>
								    		<td>
								    			{{ merchant.name | capitalize }}

								    			<span :class="[merchant.admin_approval ? 'badge-success' : 'badge-danger', 'right badge ml-1']"
								    			>
								    				{{ 
								    					merchant.admin_approval ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>
								    		</td>
								    		<td>
							    				<p 
								    				class="small text-danger" 
								    				v-show="merchant.product_categories.length === 0"
							    				>
							                		No Product-Category or Trashed
							                	</p>

								    			<ul 
								    				v-show="merchant.product_categories.length"
								    			>
													<li v-for="productCategory in merchant.product_categories" 
														:key="productCategory.id"
													>
													
														{{ productCategory.name | capitalize }}

														<span 
															v-show="productCategory.pivot.deleted_at"
															:class="[productCategory.pivot.deleted_at ? 'badge-warning' : '', 'right badge ml-1']"
										    			>
										    				{{ 
										    					productCategory.pivot.deleted_at ? 'Trashed' : '' 
										    				}}
										    			</span>
													</li>
												</ul>
								    		</td>
								    		<td>
								    			<button 
									    			type="button" 
									    			@click="showMerchantProductCategoryDetails(merchant)" 
									    			class="btn btn-primary btn-sm"
								    			>
										        	<i class="fas fa-eye"></i>
										        	Categories
										      	</button>

										      	<button 
											      	type="button" 
											      	@click="showMerchantProducts(merchant)" 
											      	class="btn btn-warning btn-sm"
										      	>
										        	<i class="fas fa-eye"></i>
										        	Products
										      	</button>

										      	<!-- 
										      	<button type="button" @click="showMerchantProductCategoryEditModal(merchant)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
 												-->
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!allMerchantProductCategories.length"
									  	>
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No Category or Merchant found.</div>
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
										<option>40</option>
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllMerchantProductCategories() : searchData()"
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
										@paginate="query === '' ? fetchAllMerchantProductCategories() : searchData()"
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
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ 
						  			editMode ? 'Edit' : 'Create' 
						  		}} 
						  		Merchant Product-Category
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="storeMerchantProductCategory()"
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
				                                  			v-model="singleMerchantProductCategoryData.merchant"
				                                  			placeholder="Merchant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMerchants" 
					                                  		:required="true" 
					                                  		class="form-control p-0"
					                                  		:class="!errors.merchant ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('merchant')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.merchant }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Product Category Names 
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleMerchantProductCategoryData.productCategories"
				                                  			placeholder="Product Categories" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allProductCategories" 
					                                  		:required="true" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false" 
					                                  		class="form-control p-0"
					                                  		:class="!errors.productCategory ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('productCategory')"
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
				                                  			placeholder="Start Time" 
				                                  			class="form-control p-0 is-valid"
					                                  		:options="merchantScheduleHours" 
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove"
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
				                                  			placeholder="End Time" 
				                                  			class="form-control p-0 is-valid"
					                                  		:options="merchantScheduleHours" 
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove"
				                                  		>
					                                	</multiselect>
									                </div>
									              	
								              	</div>
								            </div>
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer flex-column">
								<div class="col-sm-12 text-right">
									<span 
										class="text-danger small" 
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
				</div>
			</div> 
			<!-- /.modal-createOrEdit-merchantProductCategory-->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleMerchantProductCategoryData = {
		
		merchant : {
			
    	},

    	productCategories : [],
		productCategoriesId : [],
    	
		serving_from : '10.00',
		serving_to : '22.00',
		merchant_id : null,

		fromProductCategoryIndex : true,
    	
    };

	var productCategoryListData = {
      	query : '',
    	perPage : 10,
    	
    	loading : false,
    	editMode : false,
    	submitForm : true,
    	
    	allProductCategories : [],
    	allMerchants : [],
    	// expectedMerchants : [],

    	allMerchantProductCategories : [],

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

	    data() {
	        return productCategoryListData;
		},

		created(){
			this.fetchAllMerchants();
			this.fetchAllProductCategories();
			this.fetchAllMerchantProductCategories();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllMerchantProductCategories();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			'singleMerchantProductCategoryData.productCategories' : function(productCategoryObjects){

				let array = [];

				$.each(productCategoryObjects, function(key, value) {
			     	array.push(value.id);
			   	});

		     	this.singleMerchantProductCategoryData.productCategoriesId = array;
		     	
			},
			'singleMerchantProductCategoryData.merchant' : function(merchantObject){
				
				if (merchantObject) {
					this.singleMerchantProductCategoryData.merchant_id = merchantObject.id;
				}
				else
					this.singleMerchantProductCategoryData.merchant_id = null;

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
			fetchAllMerchants() {
				this.loading = true;
				axios
					.get('/api/merchants/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchants = response.data;
							// this.expectedMerchants = this.allMerchants;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllMerchantProductCategories() {
				this.loading = true;
				axios
					.get('/api/merchant-product-categories/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchantProductCategories = response.data.data;
							this.pagination = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchAllMerchantProductCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMerchantProductCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showMerchantProductCategoryDetails(merchant){
				this.$router.push({
			 		name: 'merchant-all-product-categories', 
			 		params: { 
			 			merchantId : merchant.id, 
			 			merchantName : merchant.name 
			 		}, 
				});
			},
			showMerchantProductCategoryCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors = {};

				// this.expectedMerchants = this.allMerchants;

				this.singleMerchantProductCategoryData = {

					merchant : {},
					productCategories : [],
					fromProductCategoryIndex : true

				};

				$('#modal-createOrEdit-merchantProductCategory').modal('show');
			},
    		storeMerchantProductCategory(){

    			if (!this.singleMerchantProductCategoryData.merchant_id || this.singleMerchantProductCategoryData.productCategoriesId.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-merchantProductCategory').modal('hide');
				
				axios
					.post('/merchant-product-categories/'+ this.perPage, this.singleMerchantProductCategoryData)
					.then(response => {

						if (response.status == 200) {
							
							this.singleMerchantProductCategoryData = {

								merchant : {},
								productCategories : [],
								fromProductCategoryIndex : true

							};

							this.query = '';
							this.allMerchantProductCategories = response.data.data;
							this.pagination = response.data;

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
			
			/*
			showMerchantProductCategoryEditModal(merchant) {

				this.editMode = true;
				this.submitForm = true;
				this.errors = {};

				this.expectedMerchants = this.allMerchants.filter(
					object => {
				  		return object.id == merchant.id;
					}
				);
				
				this.singleMerchantProductCategoryData.merchant = merchant;
				this.singleMerchantProductCategoryData.productCategories = merchant.product_categories;

				if (merchant.product_categories.length) {

					this.singleMerchantProductCategoryData.merchantProductCategory = merchant.product_categories[0].pivot;
				}

				$("#modal-createOrEdit-merchantProductCategory").modal("show");
			},
			updateMerchantProductCategory(){

				if (!this.singleMerchantProductCategoryData.merchantProductCategory.merchant_id || this.singleMerchantProductCategoryData.merchantProductCategory.productCategoriesId.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-merchantProductCategory').modal('hide');
				
				axios
					.put('/merchant-product-categories/' + this.singleMerchantProductCategoryData.merchantProductCategory.merchant_id + '/' + this.perPage, this.singleMerchantProductCategoryData.merchantProductCategory)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantProductCategoryData.merchant = {};
							this.singleMerchantProductCategoryData.productCategories = [];
							this.singleMerchantProductCategoryData.merchantProductCategory = {};

							if (this.query === '') {
								this.allMerchantProductCategories = response.data.data;
								this.pagination = response.data;
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
			*/
		    searchData() {
				
				axios
				.get(
					"/api/search-merchant-product-categories/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allMerchantProductCategories = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'merchant' :

						if (Object.keys(this.singleMerchantProductCategoryData.merchant).length === 0) {
							// this.errors.merchant = 'Merchant name is required';
							this.$set(this.errors, 'merchant', 'Merchant name is required');
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'merchant');
						}

						break;

					case 'productCategory' :

						if (this.singleMerchantProductCategoryData.productCategories.length === 0) {
							// this.errors.productCategory = 'Product Category name is required';
							this.$set(this.errors, 'productCategory', 'Product Category name is required')
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'productCategory');
						}

						break;
				}
	 
			},
			showMerchantProducts(merchant) {
				this.$router.push({
			 		name: 'merchant-all-products', 
			 		params: { 
			 			merchantId : merchant.id, 
			 			merchantName : merchant.name 
			 		}, 
				});
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