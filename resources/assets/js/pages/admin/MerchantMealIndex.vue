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
							<h2 class="lead float-left mt-1">Merchant Meal List</h2>

                        	<button type="button" @click="showMerchantMealCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Merchant-Meal
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
											<th scope="col">Meals</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="allMerchantMeals.length"
									    	v-for="(merchant, index) in allMerchantMeals"
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
							    				<p class="small text-danger" v-show="merchant.meals.length === 0">
							                		Meal not available or trashed
							                	</p>

								    			<ul v-show="merchant.meals.length">
													<li v-for="meal in merchant.meals" 
														:key="meal.id">
													
														{{ meal.name | capitalize }}
													
													</li>
												</ul>
								    		</td>
								    		<td>
										      	<button type="button" @click="showMerchantMealEditModal(merchant)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button 
								        			type="button" 
								        			@click="showMerchantMealDeletionModal(merchant)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!allMerchantMeals.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Meal or Merchant found.
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
										<option>40</option>
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllMerchantMeals() : searchData()"
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
										@paginate="query === '' ? fetchAllMerchantMeals() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-merchantMeal -->
			<div class="modal fade" id="modal-createOrEdit-merchantMeal">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Merchant-Meal
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>

					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateMerchantMeal() : storeMerchantMeal()"
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
				                                  			v-model="singleMerchantMealData.merchantObject"
				                                  			placeholder="Merchant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="merchantsFiltered" 
					                                  		:required="true"
					                                  		:class="!errors.merchantMeal.merchant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false" 
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('merchantMeal.merchant')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantMeal.merchant 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Meals
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleMerchantMealData.mealObjects"
				                                  			placeholder="Meal Names" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMeals" 
					                                  		:required="true" 
					                                  		:multiple="true" 
					                                  		:close-on-select="false"
					                                  		:class="!errors.merchantMeal.meal ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('merchantMeal.meal')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.merchantMeal.meal }}
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
								  		{{ editMode ? 'Update' : 'Create' }} Merchant-Meal
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-merchantMeal-->

			<!-- modal-merchantMeal-delete-confirmation -->
			<div class="modal fade" id="modal-merchantMeal-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Merchant-Meal Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="destroyMerchantMeal" 
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
					      			Are you sure want to delete merchant meal ??
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
			<!-- /modal-merchantMeal-delete-confirmation -->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleMerchantMealData = {
		
    	mealObjects : [],

    	merchantObject : {
			
    	},

    	merchantMeal : {
			meal_id : [],
			merchant_id : null,
    	}
    };

	var mealListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	allMeals : [],
    	allMerchants : [],
    	merchantsFiltered : [],

    	allMerchantMeals : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		merchantMeal : {},
    	},

        singleMerchantMealData : singleMerchantMealData,

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
			this.fetchAllMeals();
			this.fetchAllMerchants();
			this.fetchAllMerchantMeals();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllMerchantMeals();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			'singleMerchantMealData.mealObjects' : function(mealObjects){
				let array = [];
				$.each(mealObjects, function(key, value) {
			     	array.push(value.id);
			   	});
		     	this.singleMerchantMealData.merchantMeal.meal_id = array;
			},
			'singleMerchantMealData.merchantObject' : function(merchantObject){
				if (merchantObject) {
					this.singleMerchantMealData.merchantMeal.merchant_id = merchantObject.id;
				}else
					this.singleMerchantMealData.merchantMeal.merchant_id = null;
			},
		},

		methods : {

			fetchAllMeals(){
				this.loading = true;
				axios
					.get('/api/meals/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMeals = response.data;
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
			fetchAllMerchantMeals() {
				this.loading = true;
				axios
					.get('/api/merchant-meals/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchantMeals = response.data.data;
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
					this.fetchAllMerchantMeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMerchantMeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showMerchantMealCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.merchantMeal = {};

				this.merchantsFiltered = this.allMerchants;

				this.singleMerchantMealData.merchantObject = {};
				this.singleMerchantMealData.mealObjects = [];

				$('#modal-createOrEdit-merchantMeal').modal('show');
			},
    		storeMerchantMeal(){

    			if (!this.singleMerchantMealData.merchantMeal.merchant_id || this.singleMerchantMealData.merchantMeal.meal_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-merchantMeal').modal('hide');
				
				axios
					.post('/merchant-meals/'+ this.perPage, this.singleMerchantMealData.merchantMeal)
					.then(response => {

						if (response.status == 200) {
							
							this.singleMerchantMealData.merchantObject = {};
							this.singleMerchantMealData.mealObjects = [];

							this.query = '';
							this.allMerchantMeals = response.data.data;
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
			showMerchantMealEditModal(merchant) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.merchantMeal = {};

				this.merchantsFiltered = this.allMerchants.filter(
					object => {
				  		return object.id == merchant.id;
					}
				);
				
				this.singleMerchantMealData.merchantObject = merchant;
				this.singleMerchantMealData.mealObjects = merchant.meals;

				$("#modal-createOrEdit-merchantMeal").modal("show");
			},
			updateMerchantMeal(){

				if (!this.singleMerchantMealData.merchantMeal.merchant_id || this.singleMerchantMealData.merchantMeal.meal_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-merchantMeal').modal('hide');
				
				axios
					.put('/merchant-meals/' + this.singleMerchantMealData.merchantMeal.merchant_id + '/' + this.perPage, this.singleMerchantMealData.merchantMeal)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantMealData.merchantObject = {};
							this.singleMerchantMealData.mealObjects = [];

							if (this.query === '') {
								this.allMerchantMeals = response.data.data;
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
			showMerchantMealDeletionModal(merchant) {
				this.singleMerchantMealData.merchantObject = merchant;
				$("#modal-merchantMeal-delete-confirmation").modal("show");
			},
			destroyMerchantMeal(){

				$("#modal-merchantMeal-delete-confirmation").modal("hide");

				axios
					.delete('/merchant-meals/'+this.singleMerchantMealData.merchantObject.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMerchantMealData.merchantObject = {};

							if (this.query === '') {
								this.allMerchantMeals = response.data.data;
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
					"/api/merchant-meals/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allMerchantMeals = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'merchantMeal.merchant' :

						if (Object.keys(this.singleMerchantMealData.merchantObject).length === 0) {
							this.errors.merchantMeal.merchant = 'Merchant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantMeal, 'merchant');
						}

						break;

					case 'merchantMeal.meal' :

						if (Object.keys(this.singleMerchantMealData.mealObjects).length === 0) {
							this.errors.merchantMeal.meal = 'Meal name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantMeal, 'meal');
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