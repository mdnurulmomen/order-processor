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
								{{ 
									merchantNameFromData 
								}}

								Merchant-Product
							</h2>

                        	<button type="button" 
                        			@click="showMerchantAllProductCategories" class="btn btn-default btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-eye" aria-hidden="true"></i>
                                My Product-Categories
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
								<table class="table table-hover table-bordered text-center">
									
									<tbody>
									  	<tr v-show="merchantAllProductCategories.length"
									    	v-for="(merchantProductCategory, index) in merchantAllProductCategories"
									    	:key="merchantProductCategory.id"
									  	>  		
								  			<td colspan="8">
									    		<p class="font-weight-bold font-italic">
									    			{{ 
									    				merchantProductCategory.product_category ? merchantProductCategory.product_category.name : 'Trashed Product Category' 
									    			}}
										    		<span 
											    		class="badge badge-danger" 
											    		v-show="merchantProductCategory.deleted_at"
											    	>
											    		trashed
											    	</span>
									    		</p>
										    	<table class="table">
										    		<thead>
														<tr>
															<th scope="col">#</th>
															<th scope="col">Product</th>
															<th scope="col">Detail</th>
															<th scope="col">Variations</th>
															<th scope="col">Addons</th>
															<th scope="col">Price</th>
															<th scope="col">Customizable</th>
															<th scope="col">Action</th>
														</tr>
													</thead>
										    		<tbody>
												    	<tr v-show="merchantProductCategory.merchant_products.length" 
													    	v-for="(product, index) in merchantProductCategory.merchant_products" 
													    	:key="product.id"
													  	>
													    	<td scope="row">
													    		{{ index + 1 }}
													    	</td>
												    		<td>
												    			{{ product.name }}

												    			<span 
														    		class="badge badge-danger" 
														    		v-show="product.deleted_at"
														    	>
														    		outdated
														    	</span>

												    		</td>
												    		<td>
												    			<span v-html="product.detail">
												    				
												    			</span>
												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="product.has_variation"
												    			>
																  	<li v-for="productVariation in product.variations" 
																  		:key="productVariation.id"
																  	>
																    	
																    	{{ 
																    		productVariation.name 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="productVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="!product.has_variation"
												    			>
												    				Not-Available
												    			</p>
												    		</td>
												    		<td>
												    			
												    			<ul class="text-left" 
												    				v-show="product.has_addon"
												    			>
																  	<li v-for="productAddon in product.merchant_product_addons" 
																  		:key="productAddon.id"
																  	>
																    	
																    	{{ 
																    		productAddon.name 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="productAddon.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="!product.has_addon"
												    			>
												    				Not-Available
												    			</p>

												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="product.variations.length"
												    			>
																  	<li v-for="productVariation in product.variations" 
																  		:key="productVariation.id"
																  	>
																    	
																    	{{ 
																    		productVariation.pivot.price 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="productVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			{{ 
												    				! product.has_variation ? product.price : ''
												    			}}
												    		</td>
												    		<td>
												    			{{ product.customizable ? 'Customizable' : 'Not-Customizable' }}
												    		</td>

												    		<td>
														      	<toggle-button 
						                                  			:sync="true" 
						                                  			v-model="product.available" 
						                                  			:width="130"  
						                                  			:height="30" 
						                                  			:font-size="15" 
						                                  			:color="{checked: 'green', unchecked: 'red'}" 
						                                  			:labels="{checked: 'On', unchecked: 'Off' }" 
						                                  			@change="updateMerchantProductStock(product)"
					                                  			/>
												    		</td>
												    	</tr>

												    	<tr v-show="! merchantProductCategory.merchant_products.length"
												    	>
												    		<td colspan="8">
													      		<div class="alert alert-danger" role="alert">
													      			No Merchant-Product Found
													      		</div>
													    	</td>
													  	</tr>
										    		</tbody>
										    	</table>
									    	</td>
									  	</tr>
									  	<tr v-show="!merchantAllProductCategories.length">
								    		<td colspan="8">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Menu Category Found or Trashed.
									      		</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
							<div class="row d-flex align-items-center align-content-center">
								<div class="col-sm-1">
									<select class="form-control" 
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
										@click="query === '' ? fetchMerchantAllProducts() : searchData()"
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
										@paginate="query === '' ? fetchMerchantAllProducts() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import { ToggleButton } from 'vue-js-toggle-button';

	var merchantProductListData = {
      	query : '',
    	perPage : 10,
    	loading : false,

    	currentMerchantProduct : {},
    	merchantAllProductCategories : [],
    	
    	pagination: {
        	current_page: 1
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        merchant_id: document.querySelector('meta[name="owner-id"]').getAttribute('content'),
    };

	export default {

		// Local registration of components
		components: {
			ToggleButton : ToggleButton, 
		},

	    data() {
	        return merchantProductListData;
		},

		created(){
			this.fetchMerchantAllProducts();
		},

		computed: {
		    // a computed getter
		    merchantNameFromData: function () {
		      // `this` points to the vm instance
		      if (this.merchantAllProductCategories.length) {
	      		return this.merchantAllProductCategories[0].merchant.name;
		      }

		      return 'Current Merchant';
		    },
		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchMerchantAllProducts();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},

		},

		methods : {

			fetchMerchantAllProducts(){
				this.loading = true;
				axios
					.get('/api/merchant-products/' + this.merchant_id + '/' + this.perPage + "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.merchantAllProductCategories = response.data.data;
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
					this.fetchMerchantAllProducts();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchMerchantAllProducts();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
    		showMerchantAllProductCategories(){
				this.$router.push({
			 		name: 'merchant.myMenuCategories.index',
				});
			},
			updateMerchantProductStock(merchantProduct){
				
				this.currentMerchantProduct.id = merchantProduct.id;
				this.currentMerchantProduct.available = merchantProduct.available;

				axios
					.put('/merchant-products/' + this.merchant_id + '/' + this.perPage + "?page=" +
				    this.pagination.current_page, this.currentMerchantProduct)
					.then(response => {

						if (response.status == 200) {

							if (this.query === '') {
								this.merchantAllProductCategories = response.data.data;
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
					"/merchant-products/" + this.merchant_id + "/" + this.query + "/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.merchantAllProductCategories = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
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