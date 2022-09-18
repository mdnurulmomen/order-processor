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
								{{ 
									merchantNameFromData
								}}
								 
								Product Categories
							</h2>

					      	<button 
	                        	type="button" 
	                        	@click="showMerchantProductList" 
	                        	class="btn btn-default btn-sm float-right"
                        	>
			        			<i class="fas fa-eye"></i>
			        			My Products
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
											<th scope="col">Product Category Name</th>
											<th scope="col">Serving From</th>
											<th scope="col">Serving To</th>
											<th scope="col">Number Products</th>
											<!-- <th scope="col">Action</th> -->
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
								    				merchantProductCategory.product_category ? merchantProductCategory.product_category.name : 'Trashed Product-Category' 
								    			}}
								    		</td>
								    		<td>
								    			{{ merchantProductCategory.serving_from }}
								    		</td>
								    		<td>
								    			{{ merchantProductCategory.serving_to }}
								    		</td>
								    		<td>
								    			{{ 
								    				merchantProductCategory.merchant_products.length 
								    			}}
								    		</td>
							    		<!-- 
								    		<td>
												<toggle-button 
		                                  			:sync="true" 
		                                  			v-model="merchantProductCategory.deleted_at" 
		                                  			:width="130"  
		                                  			:height="30" 
		                                  			:font-size="15" 
		                                  			:color="{checked: 'green', unchecked: 'red'}" 
		                                  			:labels="{checked: 'On', unchecked: 'Off' }" 
		                                  			@change="updateMerchantProductCategoryAvailability(merchantProduct)"
	                                  			/>
								    		</td> 
								    	-->
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
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	// import { ToggleButton } from 'vue-js-toggle-button';

	var productCategoryListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	
    	merchantAllProductCategories : [],
		merchantProductCategoriesToShow : [],

    	pagination: {
        	current_page: 1
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        merchant_id: document.querySelector('meta[name="merchant-id"]').getAttribute('content'),
    };

	export default {

		// Local registration of components
	/*
		components: {
			ToggleButton : ToggleButton, 
		},
	*/

	    data() {
	        return productCategoryListData;
		},

		created(){
			this.fetchMerchantAllProductCategories();
		},

		computed: {
		    // a computed getter
		    merchantNameFromData: function () {
		      	// `this` points to the vm instance
		      	if (this.merchantProductCategoriesToShow.length) {
	      			return this.merchantProductCategoriesToShow[0].merchant.name;
		      	}

		      	return 'My Merchant';
		    },
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
			}
		},

		methods : {

			fetchMerchantAllProductCategories() {
				this.loading = true;
				axios
					.get('/api/merchant-product-categories/' + this.merchant_id + '/' + this.perPage + "?page=" +
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
		    searchData() {
				axios
				.get(
					"/merchant-product-categories/" + this.merchant_id + "/"  + this.query + "/" + this.perPage +
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
			showDataListOfSelectedTab(){
				this.merchantProductCategoriesToShow = this.merchantAllProductCategories.current.data;
				this.pagination = this.merchantAllProductCategories.current;
			},
			showMerchantProductList() {
				this.$router.push({
			 		name: 'merchant.myProducts.index',  
				});
			},

		}
  	}

</script>