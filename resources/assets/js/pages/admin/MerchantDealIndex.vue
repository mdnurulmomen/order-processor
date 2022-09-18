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
							<h2 class="lead float-left mt-1">Merchant Deals</h2>

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
											<th scope="col">Merchant</th>
											<th scope="col">Sale Percentage</th>
											<th scope="col">Promotional Discount</th>
											<th scope="col">Native Discount</th>
											<th scope="col">Net Discount</th>
											<th scope="col">Delivery Charge Available</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="dealsToShow.length"
									    	v-for="(deal, dealIndex) in dealsToShow"
									    	:key="deal.id"
									  	>
									    	<td scope="row">{{ dealIndex + 1 }}</td>
								    		<td>
								    			{{ deal.merchant ? deal.merchant.user_name : '--' | capitalize }}
								    			
								    			<span :class="[(deal.merchant && deal.merchant.admin_approval) ? 'badge-success' : 'badge-danger', 'right badge ml-1']"
								    			>
								    				{{ 
								    					(deal.merchant && deal.merchant.admin_approval) ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>
								    		</td>
								    		<td>
								    			{{ deal ? (deal.sale_percentage + '%') : '--' }}
								    		</td>
								    		<td>
								    			{{ deal ? (deal.promotional_discount + '%') : '--' }}
								    		</td>
								    		<td>
								    			{{ deal ? (deal.native_discount + '%') : '--' }}
								    		</td>
								    		<td>
								    			{{ deal ? (deal.net_discount + '%') : '--' }}
								    		</td>
								    		<td>
								    			<span :class="[deal.delivery_fee_addition ? 'badge-warning' : 'badge-info', 'right badge']"
								    			>
								    				{{ deal.delivery_fee_addition ? 'Applicable' : 'Not-Applicable' }}
								    			</span>
								    		</td>
								    		<td>
										      	<button type="button" 
										      			v-show="deal.merchant && deal.merchant.deleted_at === null" 
										      			@click="showMerchantDealEditModal(deal)" 
										      			class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

										      	<p 	class="text-danger" 
										      		v-show="deal.merchant && deal.merchant.deleted_at !== null">
										      		Trashed Merchant
										      	</p>
								    		</td>
									  	</tr>
									  	<tr v-show="!dealsToShow.length">
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
										@click="query === '' ? fetchAllMerchantDeals() : searchData()"
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
										@paginate="query === '' ? fetchAllMerchantDeals() : searchData()"
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
						  	v-on:submit.prevent=" editMode ? updateMerchantDeal() : storeMerchantDeal()"
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
								              			Merchant
								              		</label>
									                <div class="col-sm-7">
									                  	<multiselect 
				                                  			v-model="singleMerchantDealData.merchant" 
				                                  			class="form-control is-valid p-0"
				                                  			placeholder="Merchant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMerchants" 
					                                  		:required="true"
					                                  		:allow-empty="false" 
					                                  		@input="setDealMerchant(); validateFormInput('merchant')"
				                                  		>
					                                	</multiselect>
									                	<div 
									                		class="text-danger" 
									                		v-show="errors.merchant"
									                	>
												        	{{ errors.merchant }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Promotional
								              		</label>
									                <div class="col-sm-7">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model.number="singleMerchantDealData.promotional_discount" 
																placeholder="Promotional Percentage" 
																:class="!errors.promotional_discount  ? 'is-valid' : 'is-invalid'"
																min="0" 
																max="100" 
																step=".1" 
																@change="setNetDiscount(); validateFormInput('promotional_discount')" 
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.promotional_discount 
													        	}}
													  		</div>
														</div>
									                </div>
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDealName3" class="col-sm-5 col-form-label text-right">
								              			Native
								              		</label>
									                <div class="col-sm-7">
									                  	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model.number="singleMerchantDealData.native_discount" 
																placeholder="Sale Percentage" 
																:class="!errors.native_discount ? 'is-valid' : 'is-invalid'" 
																@change="setNetDiscount(); validateFormInput('promotional_discount')" 
																min="0" 
																step=".1" 
																max="100" 
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.native_discount 
													        	}}
													  		</div>
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
																v-model.number="singleMerchantDealData.net_discount" 
																placeholder="Discount Rate" 
																:class="!errors.net_discount  ? 'is-valid' : 'is-invalid'" 
																:readonly="true" 
																min="0" 
																step=".1" 
																max="100" 
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.net_discount }}
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
																v-model.number="singleMerchantDealData.sale_percentage" 
																placeholder="Sale Percentage" 
																:class="!errors.sale_percentage ? 'is-valid' : 'is-invalid'"
																@change="validateFormInput('sale_percentage')"
																min="0" 
																max="100" 
																step=".1" 
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.sale_percentage }}
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
					                                    	v-model="singleMerchantDealData.delivery_fee_addition" 
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
    	allMerchantDeals : [],
    	dealsToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {

    	},

        singleMerchantDealData : {
        	
        	merchant : {},

    		// id : null,
			// sale_percentage : null,
			// promotional_discount : null,
			// native_discount : null,
			// net_discount : null,
			// delivery_fee_addition : null,
			// merchant_id : null,

    	},

    	allMerchants : [],

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
			this.fetchAllMerchants();
			this.fetchAllMerchantDeals();
		},

		watch : {
			query : function(val){

				if (val==='') {
					this.fetchAllMerchantDeals();
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
					this.dealsToShow = this.allMerchantDeals.current.data;
					this.pagination = this.allMerchantDeals.current;
				}else {
					this.dealsToShow = this.allMerchantDeals.trashed.data;
					this.pagination = this.allMerchantDeals.trashed;
				}
			},
			fetchAllMerchants(){
				this.loading = true;
				axios
					.get('/api/merchants')
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
			fetchAllMerchantDeals(){
				this.loading = true;
				axios
					.get('/api/merchant-deals/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchantDeals = response.data;
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
					this.fetchAllMerchantDeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMerchantDeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showDealCreateModal(){

				this.editMode = false;
				this.submitForm = true;

				this.errors = {};

				this.singleMerchantDealData = {

					merchant : {},

				};

				$('#modal-createOrEdit-deal').modal('show');
			},
    		storeMerchantDeal(){

				this.verifyInputs();

				if (Object.keys(this.errors).length) {
					
					this.submitForm = false;
					return;

				}
				
				axios
					.post('/merchant-deals/'+ this.perPage, this.singleMerchantDealData)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantDealData = {
								
								merchant : {}

							};							

							this.allMerchantDeals = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

							$('#modal-createOrEdit-deal').modal('hide');

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
			showMerchantDealEditModal(deal) {
				this.editMode = true;
				this.submitForm = true;

				this.errors = {};

				this.singleMerchantDealData = deal;

				$("#modal-createOrEdit-deal").modal("show");
			},
			updateMerchantDeal(){

				this.verifyInputs();

				if (Object.keys(this.errors).length) {
					
					this.submitForm = false;
					return;

				}
				
				axios
					.put('/merchant-deals/' + this.singleMerchantDealData.id + '/' + this.perPage, this.singleMerchantDealData)
					.then(response => {

						if (response.status == 200) {

							this.singleMerchantDealData = {
								merchant : {}
							};

							if (this.query === '') {
								this.allMerchantDeals = response.data;
								this.showDataListOfSelectedTab();
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							$('#modal-createOrEdit-deal').modal('hide');

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
					"/api/merchant-deals/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allMerchantDeals = response.data;
					this.dealsToShow = this.allMerchantDeals.all.data;
					this.pagination = this.allMerchantDeals.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			setNetDiscount () {

				this.singleMerchantDealData.net_discount = (this.singleMerchantDealData.promotional_discount ?? 0) + (this.singleMerchantDealData.native_discount ?? 0);
			
			},
			verifyInputs() {

				this.validateFormInput('merchant');
				this.validateFormInput('net_discount');
				this.validateFormInput('promotional_discount');
				this.validateFormInput('native_discount');
				this.validateFormInput('sale_percentage');

			},
			setDealMerchant() {

				if (this.singleMerchantDealData.hasOwnProperty('merchant') && Object.keys(this.singleMerchantDealData.merchant).length > 1) {			// deal inside


					this.singleMerchantDealData.merchant_id = this.singleMerchantDealData.merchant.id;


				}
				else {

					this.singleMerchantDealData.merchant_id = null;

				}

			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'merchant' :

						if (! this.singleMerchantDealData.merchant || ! Object.keys(this.singleMerchantDealData.merchant).length) {
							this.errors.merchant = 'Merchant is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'merchant');
						}

						break;

					case 'net_discount' :

						if (!this.singleMerchantDealData.net_discount) {
							this.errors.net_discount = 'Discount rate is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'net_discount');
						}

						break;

					case 'promotional_discount' :

						if (this.singleMerchantDealData.promotional_discount < 0 || this.singleMerchantDealData.promotional_discount > 100) {
							
							this.errors.promotional_discount = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'promotional_discount');
						}

						break;

					case 'native_discount' :

						if (this.singleMerchantDealData.native_discount < 0 || this.singleMerchantDealData.native_discount > 100) {

							this.errors.native_discount = 'Value should be between 0 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'native_discount');
						}

						break;

					case 'sale_percentage' :

						if (this.singleMerchantDealData.sale_percentage < 0 || this.singleMerchantDealData.sale_percentage > 100) {
							this.errors.sale_percentage = 'Value should be between 0 and 100';
						}else if (this.singleMerchantDealData.sale_percentage < this.singleMerchantDealData.native_discount) {
							this.errors.sale_percentage = 'Value should be greater or equal to native discount';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'sale_percentage');
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