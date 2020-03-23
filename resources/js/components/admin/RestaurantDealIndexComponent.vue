
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
						:rows="restaurantDeals" 
						:columns="columns" 
						:config="config"
						:actions="actions"
					>

					</vue-bootstrap4-table> 
					-->

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">Restaurant Deal List</h2>

                        	<button type="button" @click="showRestaurantDealCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Restaurant Deal
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
											<th scope="col">Restaurant Name</th>
											<th scope="col">Sale Percentage</th>
											<th scope="col">Promotional Discount</th>
											<th scope="col">Native Discount</th>
											<th scope="col">Delivery Charge Available</th>
											<th scope="col">Net Discount</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="restaurantDealsToShow.length"
									    	v-for="(restaurantDeal, index) in restaurantDealsToShow"
									    	:key="restaurantDeal.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ restaurantDeal.restaurant.name || 'Restaurant trashed' }}</td>
								    		<td>{{ restaurantDeal.sale_percentage}}</td>
								    		<td>{{ restaurantDeal.restaurant_promotional_discount }}</td>
								    		<td>{{ restaurantDeal.native_discount }}</td>
								    		<td>{{ restaurantDeal.delivery_fee_addition ? 'Yes' : 'No' }}</td>
								    		<td>{{ restaurantDeal.discount.rate || 'Discount trashed' }}</td>
								    		<td>
										      	<button type="button" @click="showRestaurantDealEditModal(restaurantDeal)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										      	</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!restaurantDealsToShow.length">
								    		<td colspan="8">
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
										@click="query === '' ? fetchAllRestaurantDeals() : searchData()"
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
										@paginate="query === '' ? fetchAllRestaurantDeals() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-restaurantDeal -->
			<div class="modal fade" id="modal-createOrEdit-restaurantDeal">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Restaurant Deal</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateRestaurantDeal() : storeRestaurantDeal()"
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
								              		<label for="inputDealName3" class="col-sm-4 col-form-label text-right">
								              			Sale Percentage :
								              		</label>
									                <div class="col-sm-8">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model="singleRestaurantDealData.restaurantDeal.sale_percentage" 
																min="0" 
																max="100" 
																step=".1" 
																placeholder="Sale Percentage" 
																:class="!errors.restaurantDeal.sale_percentage  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('restaurantDeal.sale_percentage')"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.restaurantDeal.sale_percentage }}
													  		</div>
														</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-4 col-form-label text-right">
								              			Promotional Discount :
								              		</label>
									                <div class="col-sm-8">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model="singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount" 
																min="0" 
																max="100" 
																step=".1" 
																placeholder="Promotional Percentage" 
																:class="!errors.restaurantDeal.restaurant_promotional_discount  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('restaurantDeal.restaurant_promotional_discount')"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.restaurantDeal.restaurant_promotional_discount }}
													  		</div>
														</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-4 col-form-label text-right">
								              			Native Discount :
								              		</label>
									                <div class="col-sm-8">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model="singleRestaurantDealData.restaurantDeal.native_discount" 
																min="0" 
																max="100" 
																step=".1" 
																placeholder="Sale Percentage" 
																:class="!errors.restaurantDeal.native_discount  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('restaurantDeal.native_discount')"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.restaurantDeal.native_discount }}
													  		</div>
														</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-4 col-form-label text-right">
								              			Net Discount :
								              		</label>
									                <div class="col-sm-8">
									                  	<multiselect 
				                                  			v-model="singleRestaurantDealData.discountObject"
				                                  			placeholder="Discount Rate" 
					                                  		label="rate" 
					                                  		track-by="id" 
					                                  		:options="allDiscounts" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantDeal.discount  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantDeal.discount')"
				                                  		>
					                                	</multiselect>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantDeal.discount }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-4 col-form-label text-right">
								              			Delivery Fee :
								              		</label>
									                <div class="col-sm-8">
									                  	<toggle-button 
					                                    	:sync="true" 
					                                    	v-model="singleRestaurantDealData.restaurantDeal.delivery_fee_addition" 
					                                    	value="true" 
					                                    	:width="120"  
					                                    	:height="30" 
					                                    	:font-size="15" 
					                                    	:color="{checked: 'green', unchecked: 'red'}" 
					                                    	:labels="{checked: 'Enable', unchecked: 'Disable' }"
					                                    />
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-4 col-form-label text-right">
								              			Restaurant Name :
								              		</label>
									                <div class="col-sm-8">
									                  	<multiselect 
				                                  			v-model="singleRestaurantDealData.restaurantObject"
				                                  			placeholder="Restaurant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allRestaurants" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantDeal.restaurant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantDeal.restaurant')"
				                                  		>
					                                	</multiselect>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantDeal.restaurant }}
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
							  		{{ editMode ? 'Update' : 'Create' }} Deal
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-restaurantDeal-->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import { ToggleButton } from 'vue-js-toggle-button';

	var restaurantDealListData = {

      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,

    	restaurantDealsToShow : [],

    	pagination: {
        	current_page: 1,
      	},

    	errors : {
    		restaurantDeal : {},
    	},

        singleRestaurantDealData : {
        	
        	restaurantDeal : {
				// id : null,
				// sale_percentage : null,
				// restaurant_promotional_discount : null,
				// native_discount : null,
				// discount_id : null,
				// delivery_fee_addition : null,
				// restaurant_id : null,
        	},

        	restaurantObject : {},
        	discountObject : {},
    	},

    	allDiscounts : [],
    	allRestaurants : [],

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
		
		// Local registration of components
		components: { 
			Multiselect, // short form of Multiselect : Multiselect
			ToggleButton : ToggleButton, 
			
		},

	    data() {
	        return restaurantDealListData;
		},

		created(){
			this.fetchAllDiscounts();
			this.fetchAllRestaurants();
			this.fetchAllRestaurantDeals();
		},

		watch : {

			'singleRestaurantDealData.restaurantObject' : function(restaurantObject){
				if (restaurantObject) {
					this.singleRestaurantDealData.restaurantDeal.restaurant_id = restaurantObject.id;
				}else
					this.singleRestaurantDealData.restaurantDeal.restaurant_id = null;
			},
			'singleRestaurantDealData.discountObject' : function(discountObject){
				if (discountObject) {
					this.singleRestaurantDealData.restaurantDeal.discount_id = discountObject.id;
				}else
					this.singleRestaurantDealData.restaurantDeal.discount_id = null;
			},
			query : function(val){
				if (val==='') {
					this.fetchAllRestaurantDeals();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			fetchAllDiscounts(){
				this.loading = true;
				axios
					.get('/api/discounts')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allDiscounts = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllRestaurants(){
				this.loading = true;
				axios
					.get('/api/restaurants')
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
			fetchAllRestaurantDeals(){
				this.loading = true;
				axios
					.get('/api/restaurant-deals/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.restaurantDealsToShow = response.data.data;
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
					this.fetchAllRestaurantDeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllRestaurantDeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showRestaurantDealCreateModal(){
				this.editMode = false;
				this.submitForm = true;

				this.errors.restaurantDeal = {};

				this.singleRestaurantDealData.restaurantDeal = {};
				this.singleRestaurantDealData.discountObject = {};
				this.singleRestaurantDealData.restaurantObject = {};

				$('#modal-createOrEdit-restaurantDeal').modal('show');
			},
    		storeRestaurantDeal(){

				if (Object.keys(this.singleRestaurantDealData.restaurantObject).length === 0 || Object.keys(this.singleRestaurantDealData.discountObject).length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantDeal').modal('hide');
				
				axios
					.post('/restaurant-deals/'+ this.perPage, this.singleRestaurantDealData.restaurantDeal)
					.then(response => {

						if (response.status == 200) {

							this.query = '';
							this.restaurantDealsToShow = response.data.data;
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
			showRestaurantDealEditModal(restaurantDeal) {
				this.editMode = true;
				this.submitForm = true;

				this.errors.restaurantDeal = {};

				this.singleRestaurantDealData.restaurantDeal = restaurantDeal;
				this.singleRestaurantDealData.discountObject = restaurantDeal.discount;
				this.singleRestaurantDealData.restaurantObject = restaurantDeal.restaurant;

				$("#modal-createOrEdit-restaurantDeal").modal("show");
			},
			updateRestaurantDeal(){

				$('#modal-createOrEdit-restaurantDeal').modal('hide');
				
				axios
					.put('/restaurant-deals/' + this.singleRestaurantDealData.restaurantDeal.id + '/' + this.perPage, this.singleRestaurantDealData.restaurantDeal)
					.then(response => {

						if (response.status == 200) {

							if (this.query === '') {
								this.restaurantDealsToShow = response.data.data;
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
		    searchData() {
				
				axios
				.get(
					"/api/restaurant-deals/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.restaurantDealsToShow = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'restaurantDeal.sale_percentage' :

						if (this.singleRestaurantDealData.restaurantDeal.sale_percentage < 0 || this.singleRestaurantDealData.restaurantDeal.sale_percentage > 100) {
							this.errors.restaurantDeal.sale_percentage = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'sale_percentage');
						}

						break;


					case 'restaurantDeal.restaurant_promotional_discount' :

						if (this.singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount < 0 || this.singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount > 100) {
							this.errors.restaurantDeal.restaurant_promotional_discount = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'restaurant_promotional_discount');
						}

						break;

					case 'restaurantDeal.native_discount' :

						if (this.singleRestaurantDealData.restaurantDeal.native_discount < 0 || this.singleRestaurantDealData.restaurantDeal.native_discount > 100) {
							this.errors.restaurantDeal.native_discount = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'native_discount');
						}

						break;

					case 'restaurantDeal.discount' :

						if (Object.keys(this.singleRestaurantDealData.discountObject).length === 0) {
							this.errors.restaurantDeal.discount = 'Discount rate is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'discount');
						}

						break;

					case 'restaurantDeal.restaurant' :

						if (Object.keys(this.singleRestaurantDealData.restaurantObject).length === 0) {
							this.errors.restaurantDeal.restaurant = 'Restaurant name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'restaurant');
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