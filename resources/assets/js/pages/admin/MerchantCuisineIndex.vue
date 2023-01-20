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
							<h2 class="lead float-left mt-1">Merchant Cuisine List</h2>

                        	<button 
	                        	type="button" 
	                        	@click="showMerchantCuisineCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Merchant-Cuisine
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
											<th scope="col">Merchant</th>
											<th scope="col">Cuisines</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="allMerchantCuisines.length"
									    	v-for="(merchant, index) in allMerchantCuisines"
									    	:key="merchant.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
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
							    				<p class="small text-danger" v-show="merchant.cuisines.length === 0">
							                		Cuisine not available or trashed
							                	</p>

								    			<ul>
													<li v-for="cuisine in merchant.cuisines" 
														:key="cuisine.id"
													>
													
														{{ cuisine.name | capitalize }}
													
													</li>
												</ul>
								    		</td>
								    		<td>
										      	<button 
										      		type="button" 
										      		@click="showMerchantCuisineEditModal(merchant)" 
										      		class="btn btn-primary btn-sm"
										      	>
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button 
								        			type="button" 
								        			@click="showMerchantCuisineDeletionModal(merchant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!allMerchantCuisines.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No cuisine or merchant found.</div>
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
										@click="query === '' ? fetchAllMerchantCuisines() : searchData()"
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
										@paginate="query === '' ? fetchAllMerchantCuisines() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-merchantCuisine -->
			<div class="modal fade" id="modal-createOrEdit-merchantCuisine">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Merchant-Cuisine
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateMerchantCuisine() : storeMerchantCuisine()"
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
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Merchant Name
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleMerchantCuisineData.merchantObject"
				                                  			placeholder="Merchant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="merchantsFiltered" 
					                                  		:required="true"
					                                  		:class="!errors.merchantCuisine.merchant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false" 
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('merchantCuisine.merchant')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantCuisine.merchant 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Cuisines
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleMerchantCuisineData.cuisineObjects"
				                                  			placeholder="Cuisine Names" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allCuisines" 
					                                  		:required="true" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false"
					                                  		:class="!errors.merchantCuisine.cuisine ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('merchantCuisine.cuisine')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantCuisine.cuisine 
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
								  		{{ editMode ? 'Update' : 'Create' }} Merchant-Cuisine
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-merchantCuisine-->

			<!-- modal-merchantCuisine-delete-confirmation -->
			<div class="modal fade" id="modal-merchantCuisine-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Merchant-Cuisine Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="destroyMerchantCuisine" 
						  	autocomplete="off"
					  	>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5 style="color:#c6cacc">
					      			<small>
					      				Once you delete, you can't retreive it again.
					      			</small>
					      		</h5>
					      		<h5>
					      			Are you sure want to delete merchant cuisine ??
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
			<!-- /modal-merchantCuisine-delete-confirmation -->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleMerchantCuisineData = {
		
    	cuisineObjects : [],
    	merchantObject : {
			
    	},

    	merchantCuisine : {
			cuisine_id : [],
			merchant_id : null,
    	}
    };

	var mealListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	allCuisines : [],
    	allMerchants : [],
    	merchantsFiltered : [],

    	allMerchantCuisines : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		merchantCuisine : {},
    	},

        singleMerchantCuisineData : singleMerchantCuisineData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
		// Local registration of components
		components: {
			Multiselect, // short form of Multiselect : Multiselect
		},

	    data() {
	        return mealListData;
		},

		created(){
			this.fetchAllCuisines();
			this.fetchAllMerchants();
			this.fetchAllMerchantCuisines();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllMerchantCuisines();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			'singleMerchantCuisineData.cuisineObjects' : function(cuisineObjects){
				let array = [];
				$.each(cuisineObjects, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.singleMerchantCuisineData.merchantCuisine.cuisine_id = array;
			},
			'singleMerchantCuisineData.merchantObject' : function(merchantObject){
				if (merchantObject) {
					this.singleMerchantCuisineData.merchantCuisine.merchant_id = merchantObject.id;
				}else
					this.singleMerchantCuisineData.merchantCuisine.merchant_id = null;
			},
		},

		methods : {

			fetchAllCuisines(){
				this.loading = true;
				axios
					.get('/api/cuisines/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allCuisines = response.data;
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
						/*
							this.allMerchants = this.allMerchants.filter(
								object => {
							  		return object.admin_approval == true;
								}
							);
						*/
							this.merchantsFiltered = this.allMerchants;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllMerchantCuisines() {
				this.loading = true;
				axios
					.get('/api/merchant-cuisines/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchantCuisines = response.data.data;
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
					this.fetchAllMerchantCuisines();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMerchantCuisines();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showMerchantCuisineCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.merchantCuisine = {};

				this.merchantsFiltered = this.allMerchants;

				this.singleMerchantCuisineData.merchantObject = {};
				this.singleMerchantCuisineData.cuisineObjects = [];

				$('#modal-createOrEdit-merchantCuisine').modal('show');
			},
    		storeMerchantCuisine(){

    			if (!this.singleMerchantCuisineData.merchantCuisine.merchant_id || this.singleMerchantCuisineData.merchantCuisine.cuisine_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-merchantCuisine').modal('hide');
				
				axios
					.post('/merchant-cuisines/'+ this.perPage, this.singleMerchantCuisineData.merchantCuisine)
					.then(response => {

						if (response.status == 200) {
							
							this.singleMerchantCuisineData.merchantObject = {};
							this.singleMerchantCuisineData.cuisineObjects = [];

							this.query = '';
							this.allMerchantCuisines = response.data.data;
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
			showMerchantCuisineEditModal(merchant) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.merchantCuisine = {};

				this.merchantsFiltered = this.allMerchants.filter(
					object => {
				  		return object.id == merchant.id;
					}
				);
				
				this.singleMerchantCuisineData.merchantObject = merchant;
				this.singleMerchantCuisineData.cuisineObjects = merchant.cuisines;

				$("#modal-createOrEdit-merchantCuisine").modal("show");
			},
			updateMerchantCuisine(){

				if (!this.singleMerchantCuisineData.merchantCuisine.merchant_id || this.singleMerchantCuisineData.merchantCuisine.cuisine_id.length === 0) {
					
					this.submitForm = false;
					return;
				}
				
				$('#modal-createOrEdit-merchantCuisine').modal('hide');
				
				axios
					.put('/merchants/' + this.singleMerchantCuisineData.merchantCuisine.merchant_id + '/cuisines/' + this.perPage, this.singleMerchantCuisineData.merchantCuisine)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantCuisineData.merchantObject = {};
							this.singleMerchantCuisineData.cuisineObjects = [];

							if (this.query === '') {
								this.allMerchantCuisines = response.data.data;
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
			showMerchantCuisineDeletionModal(merchant) {
				this.singleMerchantCuisineData.merchantObject = merchant;
				$("#modal-merchantCuisine-delete-confirmation").modal("show");
			},
			destroyMerchantCuisine(){

				$("#modal-merchantCuisine-delete-confirmation").modal("hide");

				axios
					.delete('/merchants/' + this.singleMerchantCuisineData.merchantObject.id + '/cuisines/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMerchantCuisineData.merchantObject = {};

							if (this.query === '') {
								this.allMerchantCuisines = response.data.data;
								this.pagination = response.data;
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
		    searchData() {
				
				axios
				.get(
					"/api/search-merchant-cuisines/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allMerchantCuisines = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'merchantCuisine.merchant' :

						if (Object.keys(this.singleMerchantCuisineData.merchantObject).length === 0) {
							this.errors.merchantCuisine.merchant = 'Merchant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantCuisine, 'merchant');
						}

						break;

					case 'merchantCuisine.cuisine' :

						if (Object.keys(this.singleMerchantCuisineData.cuisineObjects).length === 0) {
							this.errors.merchantCuisine.cuisine = 'Cuisine name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantCuisine, 'cuisine');
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