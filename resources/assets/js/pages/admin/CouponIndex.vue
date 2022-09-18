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
							<h2 class="lead float-left mt-1">Coupon List</h2>

                        	<button type="button" @click="showCouponCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Coupon
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-12 was-validated">
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
											<th scope="col">Code</th>
											<th scope="col">Rate</th>
											<th scope="col">Min. Order</th>
											<th scope="col">Max. Discount</th>
											<th scope="col">Status</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="allCoupons.length"
									    	v-for="(coupon, index) in allCoupons"
									    	:key="coupon.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>
								    			{{ coupon.code.toUpperCase() }}
								    		</td>
								    		<td>
								    			{{ coupon.percentage }}
								    		</td>
								    		<td>
								    			{{ coupon.min_order }}
								    		</td>
								    		<td>
								    			{{ coupon.max_discount_per_order }}
								    		</td>
								    		<td>
								    			<span 
							    				  :class="[coupon.status ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					coupon.status ? 'Published' : 'Unpublished'
								    				}}
								    			</span>
								    		</td>
								    		<td>
										      	<button type="button" @click="showCouponEditModal(coupon)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

										      	<button type="button" @click="showCouponDeleteModal(coupon)" class="btn btn-danger btn-sm">
										        	<i class="fas fa-trash"></i>
										        	Delete
										      	</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!allCoupons.length">
								    		<td colspan="7">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No coupon found.
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
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllCoupons() : searchData()"
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
										@paginate="query === '' ? fetchAllCoupons() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-coupon -->
			<div class="modal fade" id="modal-createOrEdit-coupon">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Coupon
						  	</h4>
						  	
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateCoupon() : storeCoupon()"
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
									              		
								              		<label for="inputDiscountRate3" class="col-sm-4 col-form-label text-right">
								              			Coupon Code
								              		</label>
									                <div class="col-sm-8">
									                	
														<input 
															type="text" 
															class="form-control" 
															v-model="singleCouponData.coupon.code" 
															placeholder="Coupon Code" 
															required="true"
															:class="!errors.coupon.code  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('coupon.code')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.coupon.code 
												        	}}
												  		</div>
														
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputDiscountRate3" class="col-sm-4 col-form-label text-right">
								              			Discount Rate
								              		</label>
									                <div class="col-sm-8">
									                	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model="singleCouponData.coupon.percentage" 
																min="1" 
																max="100" 
																step=".1" 
																placeholder="Coupon Rate" 
																required="true"
																:class="!errors.coupon.percentage  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('coupon.percentage')"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.coupon.percentage 
													        	}}
													  		</div>
														</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputDiscountRate3" class="col-sm-4 col-form-label text-right">
								              			Minimum Order
								              		</label>
									                <div class="col-sm-8">
									                	
														<input 
															type="number" 
															class="form-control" 
															v-model="singleCouponData.coupon.min_order" 
															min="100" 
															max="255" 
															step=".1" 
															placeholder="Coupon Rate" 
															required="true"
															:class="!errors.coupon.min_order  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('coupon.min_order')"
									                	>
														
									                	<div class="invalid-feedback">
												        	{{ errors.coupon.min_order }}
												  		</div>
														
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputDiscountRate3" class="col-sm-4 col-form-label text-right">
								              			Max Discount
								              		</label>
									                <div class="col-sm-8">
									                	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model="singleCouponData.coupon.max_discount_per_order" 
																min="1" 
																max="255" 
																step=".1" 
																placeholder="Coupon Rate" 
																required="true"
																:class="!errors.coupon.max_discount_per_order  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('coupon.max_discount_per_order')"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	/order
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.coupon.max_discount_per_order }}
													  		</div>
														</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Durability
								              		</label>
									                <div class="col-sm-8">
									                  	<toggle-button 
									                  		:sync="true" 
				                                  			v-model="singleCouponData.coupon.durability" 
				                                  			value="true" 
				                                  			:width="130"  
				                                  			:height="30" 
				                                  			:font-size="15" 
				                                  			:color="{checked: 'green', unchecked: 'red'}" 
				                                  			:labels="{checked: 'Defined', unchecked: 'Undefined' }"
			                                  			/>
									                </div>	
								              	</div>

								              	<div 
								              		class="form-group row" 
								              		v-show="singleCouponData.coupon.durability"
								              	>	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Valid From
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
									                  		type="date" 
									                  		class="form-control is-valid" 
															v-model="singleCouponData.coupon.valid_from" 
															placeholder="First Date" 
															:min="currentDate" 
															:required="singleCouponData.coupon.durability" 
									                  	>
									                </div>	
								              	</div>

								              	<div 
								              		class="form-group row" 
								              		v-show="singleCouponData.coupon.durability"
								              	>	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Valid To
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
									                  		type="date" 
									                  		class="form-control is-valid" 
															v-model="singleCouponData.coupon.valid_to" 
															placeholder="Last Date" 
															:min="currentDate" 
															:required="singleCouponData.coupon.durability" 
									                  	>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Status
								              		</label>
									                <div class="col-sm-8">
									                  	<toggle-button 
				                                  			:sync="true" 
				                                  			v-model="singleCouponData.coupon.status" 
				                                  			value="true" 
				                                  			:width="130"  
				                                  			:height="30" 
				                                  			:font-size="15" 
				                                  			:color="{checked: 'green', unchecked: 'red'}" 
				                                  			:labels="{checked: 'Published', unchecked: 'Unpublished' }"
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
								  		{{ editMode ? 'Update' : 'Create' }} Coupon
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-coupon-->


			<!-- modal-coupon-delete-confirmation -->
			<div class="modal fade" id="modal-coupon-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Coupon Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyCoupon" autocomplete="off">
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
					      			Are you sure want to delete this coupon ?? 
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
			<!-- /modal-coupon-delete-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import { ToggleButton } from 'vue-js-toggle-button';

	var singleCouponData = {
    	coupon : {
			// id : null,
			// code : null,
			// percentage : null,
			// min_order : null,
			// max_discount_per_order : null,
			// valid_from : null,
			// valid_to : null,
			// status : null,
    	},
    };

	var discountListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	allCoupons : [],
    	currentTab : 'current',

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		coupon : {},
    	},

        singleCouponData : singleCouponData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

		components: { 
			ToggleButton : ToggleButton,
		},

	    data() {
	        return discountListData;
		},

		created(){
			this.fetchAllCoupons();
		},

		computed: {
			currentDate: function () {
				var date = new Date();
				return date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
			}
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllCoupons();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			
			fetchAllCoupons(){
				this.loading = true;
				axios
					.get('/api/coupons/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allCoupons = response.data.data;
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
					this.fetchAllCoupons();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllCoupons();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showCouponCreateModal(){

				this.editMode = false;
				this.errors.coupon = {};
				this.submitForm = true;

				this.singleCouponData.coupon = {};

				$('#modal-createOrEdit-coupon').modal('show');
			},
    		storeCoupon(){
				
				$('#modal-createOrEdit-coupon').modal('hide');
				
				axios
					.post('/coupons/'+ this.perPage, this.singleCouponData.coupon)
					.then(response => {

						if (response.status == 200) {
							this.singleCouponData.coupon = {};

							this.query = '';
							
							this.allCoupons = response.data.data;
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
			showCouponEditModal(coupon) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.coupon = {};

				this.singleCouponData.coupon = coupon;

				$("#modal-createOrEdit-coupon").modal("show");
			},
			updateCoupon(){

				$('#modal-createOrEdit-coupon').modal('hide');
				
				axios
					.put('/coupons/' + this.singleCouponData.coupon.id + '/' + this.perPage, this.singleCouponData.coupon)
					.then(response => {

						if (response.status == 200) {

							this.singleCouponData.coupon = {};
							this.allCoupons = response.data.data;
							
							if (this.query === '') {
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
			showCouponDeleteModal(coupon) {
				this.singleCouponData.coupon = coupon;
				$("#modal-coupon-delete-confirmation").modal("show");
			},
			destroyCoupon(){

				$('#modal-coupon-delete-confirmation').modal('hide');
				
				axios
					.delete('/coupons/' + this.singleCouponData.coupon.id + '/' + this.perPage, this.singleCouponData.coupon)
					.then(response => {

						if (response.status == 200) {

							this.singleCouponData.coupon = {};
							this.allCoupons = response.data.data;
							
							if (this.query === '') {
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
					"/api/coupons/search/" + this.query + "/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allCoupons = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'coupon.code' :

						if (!this.singleCouponData.coupon.code) {
							this.errors.coupon.code = 'Coupon code is required';
						}
						else if (!this.singleCouponData.coupon.code.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.coupon.code = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.coupon, 'code');
						}

						break;	

					case 'coupon.percentage' :

						if (!this.singleCouponData.coupon.percentage) {
							this.errors.coupon.percentage = 'Percentage is required';
						}
						else if (this.singleCouponData.coupon.percentage < 1 || this.singleCouponData.coupon.percentage > 100 ) {
							this.errors.coupon.percentage = 'Rate should be between 1 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.coupon, 'percentage');
						}

						break;


					case 'coupon.min_order' :

						if (!this.singleCouponData.coupon.min_order) {
							this.errors.coupon.min_order = 'Min. order is required';
						}
						else if (this.singleCouponData.coupon.min_order < 1 || this.singleCouponData.coupon.min_order > 255 ) {
							this.errors.coupon.min_order = 'Value should be between 1 and 255';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.coupon, 'min_order');
						}

						break;

					case 'coupon.max_discount_per_order' :

						if (!this.singleCouponData.coupon.max_discount_per_order) {
							this.errors.coupon.max_discount_per_order = 'Max discount is required';
						}
						else if (this.singleCouponData.coupon.max_discount_per_order < 1 || this.singleCouponData.coupon.max_discount_per_order > 255 ) {
							this.errors.coupon.max_discount_per_order = 'Value should be between 1 and 255';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.coupon, 'max_discount_per_order');
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