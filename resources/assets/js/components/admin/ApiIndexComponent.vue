
<template>

	<div class="container-fluid">

		<section>
		
			<div class="row">
				<div class="col-sm-12">

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">
								API List
							</h2>
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
								<table class="table table-hover table-bordered table-striped">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">API</th>
											<th scope="col">Method</th>
											<th scope="col">Purpose</th>
											<th scope="col">Parameters</th>
										</tr>
									</thead>
									<tbody>
										<tr 
									  		v-show="apiToShow.length"
									    	v-for="(api, index) in apiToShow"
									    	:key="api.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td><mark>{{ api.url }}</mark></td>
								    		<td>{{ api.method }}</td>
								    		<td>{{ api.purpose }}</td>
								    		<td>
												<span v-if="Object.keys(api.parameters).length>0">
													<pre>{{ api.parameters }}</pre>
												</span>
												<span v-if="Object.keys(api.parameters).length==0">
													None
												</span>
								    		</td>
									  	</tr>
								  		<tr v-show="!apiToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No api found.</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
						</div>
					</div>
				</div>	
			</div>

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	var apiListData = {
    	
      	query : '',
    	allApi : [],
    	apiToShow : [],

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return apiListData;
		},

		created(){

			this.allApi = [
				{
					url : 'general-info',
					method : 'get',
					purpose : 'Show settings Info',
					parameters : {},
				},
				{
					url : 'restaurants/{expectedLatitude}/{expectedLongitude}',
					method : 'get',
					purpose : 'Show restaurant list in expected area',
					parameters : {},
				},
				{
					url : 'restaurant-menu-items/{expectedRestaurantId}',
					method : 'get',
					purpose : 'Show restaurant menu items',
					parameters : {},
				},

				{
					url : 'orders',
					method : 'post',
					purpose : 'Post new order',
					parameters : {
						'order_type (required)' : 'home-delivery/serve-on-table/take-away/reservation', 
						'is_asap_order (required)' : 'true/false',
						'delivery_datetime (required if not asap)' : 'date',
						'order_price (required)' : 'numeric', 
						'vat (required)' : 'numeric', 
						'discount (required)' : 'numeric', 
						'delivery_fee (required)' : 'numeric', 
						'net_payable (required)' : 'numeric', 
						'payment_method (required)' : 'cash/bkash/card', 
						'payment_id (required if not cash)' : 'string', 
						'cutlery_addition' : 'true/false', 
						'orderer_type (required)' : 'customer/waiter', 
						'orderer_id (required)' : 'numeric', 
						'delivery_new_address (required if no delivery_address_id)' : { 
							'house (required)' : 'string',
							'road (required)' : 'string',
							'lat (required)' : 'string',
							'lang (required)' : 'string',
							'address_name (required)'  : 'string',
							'additional_hint'  : 'string',
						},
						'delivery_address_id (required without delivery_new_address)' : 'string', 
						'delivery_additional_info' : 'string', 
						'orderItems (required)' : [
							{
								'restaurant_id (required)' : 'numeric', 
								'menuItems (required)' : [
									{
										'id (required)' : 'numeric',
										'quantity (required)' : 'numeric',
										'itemVariations (required)' : { 
											'id (required if not false)' : 'numeric'
										},
										'itemAddons' : [
											{
												'id (required if not empty-array)' : 'numeric',
												'quantity (required if not empty-array)' : 'numeric'
											}
										],
										'customization' : 'string'
									}
								]

							}
						]
					}
				},
			];

			this.showAllApi();

		},

		watch : {
			query : function(val){
				if (val==='') {
					this.showAllApi();
				}
				else {
					this.searchData();
				}
			}
		},

		methods : {
			showAllApi(){
				this.apiToShow = this.allApi; 
			},
		    searchData() {
				this.apiToShow = this.allApi.filter(
					currentObject => {
						if (currentObject.url.toLowerCase().includes(this.query.toLowerCase()) || currentObject.method.toLowerCase().includes(this.query.toLowerCase()) || currentObject.purpose.toLowerCase().includes(this.query.toLowerCase()) || currentObject.parameters.toLowerCase().includes(this.query.toLowerCase())) {

							return currentObject;

						}
					}
				);
			},
		}
  	}

</script>