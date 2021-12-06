
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
							<h2 class="lead float-left mt-1">Restaurant Deals</h2>

                        	<button 
                        		type="button" 
	                        	@click="showDealCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Deal
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
													@click="showCurrentDeals"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedDeals"
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
											<th scope="col">Restaurant Name</th>
											<th scope="col">Sale Percentage</th>
											<th scope="col">Promotional Discount</th>
											<th scope="col">Native Discount</th>
											<th scope="col">Net Discount</th>
											<th scope="col">Delivery Charge Available</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="restaurantDealsToShow.length"
									    	v-for="(restaurant, index) in restaurantDealsToShow"
									    	:key="restaurant.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>
								    			{{ restaurant.name || 'Trashed' }}
								    			<span :class="[restaurant.admin_approval ? 'badge-success' : 'badge-danger', 'right badge ml-1']"
								    			>
								    				{{ 
								    					restaurant.admin_approval ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>
								    		</td>
								    		<td>
								    			{{ restaurant.deal ? restaurant.deal.sale_percentage : 'No Deal' }}
								    		</td>
								    		<td>
								    			{{ restaurant.deal ? restaurant.deal.restaurant_promotional_discount : 'No Deal' }}
								    		</td>
								    		<td>
								    			{{ restaurant.deal ? restaurant.deal.native_discount : 'No Deal' }}
								    		</td>
								    		<td>
								    			{{ restaurant.deal ? restaurant.deal.net_discount : 'No Deal' }}
								    		</td>
								    		<td>
								    			<span v-if="restaurant.deal"
								    				:class="[restaurant.deal.delivery_fee_addition ? 'badge-warning' : 'badge-info', 'right badge']"
								    			>
								    				{{ 
								    					restaurant.deal.delivery_fee_addition ? 'Applicable' : 'Not-Applicable' 
								    				}}
								    			</span>

								    			<span v-else class="badge-danger right badge">
								    				No Deal
								    			</span>
								    		</td>
								    		<td>
										      	<button type="button" 
										      			v-show="restaurant.deleted_at === null && restaurant.deal === null" 
										      			@click="showDealCreateModal(restaurant)" 
										      			class="btn btn-secondary btn-sm">
										        	<i class="fa fa-plus-circle"></i>
										        	Add
										      	</button>

										      	<button type="button" 
										      			v-show="restaurant.deleted_at === null && restaurant.deal !== null" 
										      			@click="showRestaurantDealEditModal(restaurant)" 
										      			class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

										      	<p 	class="text-danger" 
										      		v-show="restaurant.deleted_at !== null">
										      		Trashed Restaurant
										      	</p>
								    		</td>
									  	</tr>
									  	<tr v-show="!restaurantDealsToShow.length">
								    		<td colspan="8">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No deal found.
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

			<!-- /.modal-createOrEdit-deal -->
			<div class="modal fade" id="modal-createOrEdit-deal">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Deal
						  	</h4>
						  	
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
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Restaurant Name
								              		</label>
									                <div class="col-sm-7">
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
				                                  		>
					                                	</multiselect>
									                	<div 
									                		class="text-danger" 
									                		v-show="errors.restaurantDeal.restaurant"
									                	>
												        	{{ 
												        		errors.restaurantDeal.restaurant 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Net Discount
								              		</label>
									                <div class="col-sm-7">
									                	<div class="input-group mb-3">
										                	<input 
																type="number" 
																class="form-control" 
																v-model.number="singleRestaurantDealData.restaurantDeal.net_discount" 
																min="0" 
																max="100" 
																step=".1" 
																placeholder="Discount Rate" 
																:class="!errors.restaurantDeal.net_discount  ? 'is-valid' : 'is-invalid'"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantDeal.net_discount 
													        	}}
													  		</div>
									                	</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Rest. Promotional
								              		</label>
									                <div class="col-sm-7">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model.number="singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount" 
																min="0" 
																max="100" 
																step=".1" 
																placeholder="Promotional Percentage" 
																:class="!errors.restaurantDeal.restaurant_promotional_discount  ? 'is-valid' : 'is-invalid'"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantDeal.restaurant_promotional_discount 
													        	}}
													  		</div>
														</div>
									                </div>
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Qupaid Native
								              		</label>
									                <div class="col-sm-7">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model.number="singleRestaurantDealData.restaurantDeal.native_discount" 
																min="0" 
																max="100" 
																step=".1" 
																placeholder="Sale Percentage" 
																:class="!errors.restaurantDeal.native_discount  ? 'is-valid' : 'is-invalid'"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantDeal.native_discount 
													        	}}
													  		</div>
														</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Sale Percentage
								              		</label>
									                <div class="col-sm-7">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model.number="singleRestaurantDealData.restaurantDeal.sale_percentage" 
																min="0" 
																max="100" 
																step=".1" 
																placeholder="Sale Percentage" 
																:class="!errors.restaurantDeal.sale_percentage  ? 'is-valid' : 'is-invalid'"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantDeal.sale_percentage 
													        	}}
													  		</div>
														</div>
									                </div> 	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Delivery Fee
								              		</label>
									                <div class="col-sm-7">
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
								  		{{ editMode ? 'Update' : 'Create' }} Deal
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-deal-->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import { ToggleButton } from 'vue-js-toggle-button';

	var dealListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allRestaurantDeals : [],
    	restaurantDealsToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		restaurantDeal : {},
    	},

        singleRestaurantDealData : {
        	
        	restaurantObject : {},

        	restaurantDeal : {
        		// id : null,
				// sale_percentage : null,
				// restaurant_promotional_discount : null,
				// native_discount : null,
				// net_discount : null,
				// delivery_fee_addition : null,
				// restaurant_id : null,
        	},

    	},

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
	        return dealListData;
		},

		created(){
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
			showCurrentDeals(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedDeals(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.restaurantDealsToShow = this.allRestaurantDeals.current.data;
					this.pagination = this.allRestaurantDeals.current;
				}else {
					this.restaurantDealsToShow = this.allRestaurantDeals.trashed.data;
					this.pagination = this.allRestaurantDeals.trashed;
				}
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
							this.allRestaurantDeals = response.data;
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
			showDealCreateModal(restaurant = {}){

				this.editMode = false;
				this.submitForm = true;

				this.errors.restaurantDeal = {};

				// this.singleRestaurantDealData = {};
				this.singleRestaurantDealData.restaurantDeal = {};
				this.singleRestaurantDealData.restaurantObject = restaurant ?? {};

				$('#modal-createOrEdit-deal').modal('show');
			},
    		storeRestaurantDeal(){

				if (Object.keys(this.singleRestaurantDealData.restaurantObject).length === 0) {
					
					this.errors.restaurantDeal.restaurant = 'Restaurant name is required';
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-deal').modal('hide');
				
				axios
					.post('/restaurant-deals/'+ this.perPage, this.singleRestaurantDealData.restaurantDeal)
					.then(response => {

						if (response.status == 200) {

							// this.singleRestaurantDealData = {};
							this.singleRestaurantDealData.restaurantDeal = {};
							this.singleRestaurantDealData.restaurantObject = {};

							this.allRestaurantDeals = response.data;

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
			showRestaurantDealEditModal(restaurant) {
				this.editMode = true;
				this.submitForm = true;

				this.errors.restaurantDeal = {};

				this.singleRestaurantDealData.restaurantDeal = restaurant.deal;
				this.singleRestaurantDealData.restaurantObject = restaurant;

				$("#modal-createOrEdit-deal").modal("show");
			},
			updateRestaurantDeal(){

				if (Object.keys(this.singleRestaurantDealData.restaurantObject).length === 0) {
					
					this.errors.restaurantDeal.restaurant = 'Restaurant name is required';
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-deal').modal('hide');
				
				axios
					.put('/restaurant-deals/' + this.singleRestaurantDealData.restaurantDeal.id + '/' + this.perPage, this.singleRestaurantDealData.restaurantDeal)
					.then(response => {

						if (response.status == 200) {

							// this.singleRestaurantDealData = {};
							this.singleRestaurantDealData.restaurantDeal = {};
							this.singleRestaurantDealData.restaurantObject = {};

							if (this.query === '') {
								this.allRestaurantDeals = response.data;
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
		    searchData() {
				
				axios
				.get(
					"/api/restaurant-deals/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allRestaurantDeals = response.data;
					this.restaurantDealsToShow = this.allRestaurantDeals.all.data;
					this.pagination = this.allRestaurantDeals.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'restaurantDeal.restaurant' :

						if (Object.keys(this.singleRestaurantDealData.restaurantObject).length === 0) {
							this.errors.restaurantDeal.restaurant = 'Restaurant name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'restaurant');
						}

						break;

					case 'restaurantDeal.net_discount' :

						if (!this.singleRestaurantDealData.restaurantDeal.net_discount) {
							this.errors.restaurantDeal.net_discount = 'Discount rate is required';
						}
						else if ((this.singleRestaurantDealData.restaurantDeal.native_discount + this.singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount) !== this.singleRestaurantDealData.restaurantDeal.net_discount) {

							this.errors.restaurantDeal.net_discount = 'Promotional with Native discount should be equal to Net discount';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'net_discount');
						}

						break;

					case 'restaurantDeal.restaurant_promotional_discount' :

						if (this.singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount < 0 || this.singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount > 100) {
							
							this.errors.restaurantDeal.restaurant_promotional_discount = 'Value should be between 0 and 100';
						}
						else if ((this.singleRestaurantDealData.restaurantDeal.native_discount + this.singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount) !== this.singleRestaurantDealData.restaurantDeal.net_discount) {

							this.errors.restaurantDeal.restaurant_promotional_discount = 'Promotional with Native discount should be equal to Net discount';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'net_discount');
							this.$delete(this.errors.restaurantDeal, 'restaurant_promotional_discount');
							this.$delete(this.errors.restaurantDeal, 'native_discount');
						}

						break;

					case 'restaurantDeal.native_discount' :

						if (this.singleRestaurantDealData.restaurantDeal.native_discount < 0 || this.singleRestaurantDealData.restaurantDeal.native_discount > 100) {

							this.errors.restaurantDeal.native_discount = 'Value should be between 0 and 100';
						}else if ((this.singleRestaurantDealData.restaurantDeal.native_discount + this.singleRestaurantDealData.restaurantDeal.restaurant_promotional_discount) !== this.singleRestaurantDealData.restaurantDeal.net_discount) {

							this.errors.restaurantDeal.native_discount = 'Promotional with Native discount should be equal to Net discount';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'net_discount');
							this.$delete(this.errors.restaurantDeal, 'restaurant_promotional_discount');
							this.$delete(this.errors.restaurantDeal, 'native_discount');
						}

						break;

					case 'restaurantDeal.sale_percentage' :

						if (this.singleRestaurantDealData.restaurantDeal.sale_percentage < 0 || this.singleRestaurantDealData.restaurantDeal.sale_percentage > 100) {
							this.errors.restaurantDeal.sale_percentage = 'Value should be between 0 and 100';
						}else if (this.singleRestaurantDealData.restaurantDeal.sale_percentage < this.singleRestaurantDealData.restaurantDeal.native_discount) {
							this.errors.restaurantDeal.sale_percentage = 'Value should be greater or equal to native discount';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantDeal, 'sale_percentage');
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