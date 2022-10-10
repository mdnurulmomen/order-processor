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
							<div class="row">
								<div class="col-sm-12">
									<h2 class="lead mt-1">
										Agent Order List
									</h2>
								</div>
							</div>

						</div>

						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-striped text-center">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Order Id</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="merchantOrdersToShow.length"
									    	v-for="(merchantOrderRecord, index) in merchantOrdersToShow"
									    	:key="merchantOrderRecord.id" 
									    	:class="[cancelledOrder(merchantOrderRecord) ? 'bg-secondary' :  servedOrder(merchantOrderRecord) ? 'bg-success' : 'bg-danger']" 
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ merchantOrderRecord.order_id }}</td>
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showOrderDetailModal(merchantOrderRecord)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>
								      			<!-- disabled if agent / restaurant already cancelled -->
								      			<!-- drop button -->
								      			<button 
									      			type="button" 
									      			class="btn btn-primary btn-sm" 
									      			v-if="!cancelledOrder(merchantOrderRecord) && merchantOrderIsReady(merchantOrderRecord) && !servedOrder(merchantOrderRecord)" 
									      			:disabled="formSubmitionMode" 
									      			@click="orderServingConfirmation(merchantOrderRecord)" 
								      			>
								        			<i class="fas fa-bell"></i>
								        			Serve
								      			</button>
								      			
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!merchantOrdersToShow.length"
									  	>
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No data found.
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
										<option>100</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="reload()"
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
										@paginate="fetchAllOrdersToBeServed()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- modal-show-order -->
			<div class="modal fade" id="modal-show-order">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header bg-info">
						  	<h4 class="modal-title">
						  		Order Details
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<ul class="nav nav-tabs justify-content-center mb-4" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#show-order-details">
										Order
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#show-order-products">
										Products
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#show-orderer-details">
										Orderer
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div id="show-order-details" class="container tab-pane active">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Id
							              		</label>
								                <div class="col-sm-6" >
								                  	{{ singleOrderData.order.id }}
								                </div>
								            </div>
								            <div class="form-group row" v-if="singleOrderData.order.is_asap_order || singleOrderData.order.schedule">		
							              		<label class="col-sm-6 text-right">
							              			ASAP/Scheduled
							              		</label>
								                <div class="col-sm-6">
								                  	{{
								                  		singleOrderData.order.is_asap_order ?
								                  		'ASAP' : singleOrderData.order.schedule.schedule
								                  	}}
								                </div>	
								            </div>
								            <div class="form-group row" v-show="singleOrderData.order.has_cutlery">		
							              		<label class="col-sm-6 text-right">
							              			Cutlery
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.has_cutlery ? 'Added' : 'None' }}
								                </div>	
								            </div> 
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Price
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.price }}
								                  	{{ $application_settings.official_currency || 'BDT' | capitalize }}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Vat
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.vat }} %
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Discount
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.discount }} %
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Payable Price
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.net_payable }}
								                  	{{ $application_settings.official_currency || 'BDT' | capitalize }}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Payment
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.payment_method }}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Order Created
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.created_at }}
								                </div>	
								            </div> 
					            		</div>
					            	</div>
								</div>
								<div id="show-order-products" class="container tab-pane fade">
									<div class="row">
						                <div class="col-sm-12 text-md-center">
						                	<ul 
						                		class="list-group" 
						                		v-show="singleOrderData.order.merchants && singleOrderData.order.merchants.length"
						                	>
												<li 
													class="list-group-item"
													v-for="(merchantOrder, index) in singleOrderData.order.merchants" 
													:key="merchantOrder.id"
												>
													<p class="font-weight-bold">
														{{ merchantOrder.merchant_name | capitalize }}
													</p>

													<ul 
														class="list-group" 
														v-show="merchantOrder.products.length" 
													>
														<li 
															class="list-group-item" 
															v-for="(product, index) in merchantOrder.products" 
															:key="product.id"
														>	
															{{ product.merchant_product.name | capitalize }} 

															<span v-if="product.merchant_product.has_variation" 
															>
																({{ product.variation.merchant_product_variation.variation.name | capitalize }} )
															</span>

															<p class="d-block">
																<span class="font-weight-bold">- Qty : </span>
																{{ product.quantity }}
															</p>

															<span 
																class="d-block font-weight-bold" 
																v-if="product.addons.length"
															>
																- Extra Addons
															</span>

															<ul 
																class="form-group" 
																style="list-style-type: circle; list-style-position: inside;" 
																v-if="product.merchant_product.has_addon && product.addons.length"
															>

																<li v-for="(additionalOrderedAddon, index) in product.addons">
																	{{ additionalOrderedAddon.merchant_product_addon.addon.name | capitalize }} (Qty:{{ additionalOrderedAddon.quantity }})
																</li>
															</ul>

															<p class="d-block" v-if="product.customization">
																<span class="font-weight-bold">- Customization : </span>
																{{ product.customization | capitalize }}
															</p>
														</li>
													</ul>

													<p class="text-danger" 
														v-show="! merchantOrder.products.length"
													>
														No Products Found Yet
													</p>
												</li>
											</ul>
						                </div>
					            	</div>
								</div>
								<div id="show-orderer-details" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
								                		
							              		<label class="col-sm-6 text-right">
							              			Ordered By
							              		</label>
								                <div class="col-sm-6">
								                  	{{ 
								                  		singleOrderData.order.orderer ? 
								                  		singleOrderData.order.orderer.user_name : 'NA' | capitalize 
													}}
													({{
														singleOrderData.order.orderer && singleOrderData.order.orderer.hasOwnProperty('merchant_id') ? 
								                  		'Waiter' : 'Customer'
													}})
								                </div>	
										            
								            </div>  
					            		</div>
					            	</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
						  	<button type="button" class="btn btn-outline-secondary float-right" data-dismiss="modal">
						  		Close
						  	</button>
						</div>
						
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-show-order -->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleOrderData = {
		order : {
			
		},
		agent : {
			merchant_agent_id : null,
			merchant_id : null,
			orderServed : false,
		}
	};

	var merchantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	
    	merchantOrdersToShow : [],
    	allOrdersToBeServed : [],
    	formSubmitionMode : false,

    	pagination: {
        	current_page: 1
      	},

      	singleOrderData : singleOrderData,

        csrf : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        // merchant_id : document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
        merchant_agent_id : 1,
        merchant_id : 1,
    };

	export default {

	    data() {
	        return merchantListData;
		},

		created(){
			this.fetchAllOrdersToBeServed();
		},

		mounted(){
			    
			Pusher.logToConsole = true;

			Echo.private(`notifyMerchantAgents.` + this.merchant_id)
			.listen('.updation-for-agents', (merchantOrderToServe) => {
			    
			    // console.log(merchantOrderToServe);

			    // due to pagination, checking if this broadcasted one already exists 
			    const objectExist = (merchantOrderRecord) => merchantOrderRecord.order_id==merchantOrderToServe.order_id;

			    // if new delivery order in the list or nothing in the list
			    if ((!this.merchantOrdersToShow.some(objectExist)) || (Array.isArray(this.merchantOrdersToShow) && !this.merchantOrdersToShow.length)) {
			    	
			    	this.merchantOrdersToShow.unshift(merchantOrderToServe);
			    	// toastr.info("New serve-order arrives");
			    }
			    // now showing the order in this page
			    else if (this.merchantOrdersToShow.some(objectExist)) {
				    let index = this.merchantOrdersToShow.findIndex(
				    		currentMerchantOrder => (currentMerchantOrder.order_id==merchantOrderToServe.order_id)
				    	);

				    Vue.set(this.merchantOrdersToShow, index, merchantOrderToServe)
				    // toastr.info("Old serve-order update arrives");
				    // console.log(index);
			    }
			    
		    	toastr.warning("Else order-serve arrives");

			});

		},

		filters: {
			capitalize: function (value) {
				if (!value) return ''
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		},

		methods : {
			fetchAllOrdersToBeServed(){
				this.loading = true;
				axios
					.get('/api/agent-orders/' + this.merchant_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {		
						if (response.status == 200) {
							this.allOrdersToBeServed = response.data;
							this.showListDataForSelectedTab();
							this.loading = false;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			showListDataForSelectedTab(){
				this.merchantOrdersToShow = this.allOrdersToBeServed.all.data;
				this.pagination = this.allOrdersToBeServed.all;
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				this.fetchAllOrdersToBeServed();
    		},
			reload() {
				this.pagination.current_page = 1;
				this.fetchAllOrdersToBeServed();
    		},
    		showOrderDetailModal(merchantOrderRecord) {

				this.singleOrderData.order = merchantOrderRecord.order;

				// this.singleOrderData.order.merchants = merchantOrderRecord.ordered_restaurants;
				
				// if any food is picked
				if (this.merchantOrderIsReady(merchantOrderRecord)) {

					this.singleOrderData.order.orderer = merchantOrderRecord.order.orderer;

				}

				$("#modal-show-order").modal("show");

			},

			orderServingConfirmation(merchantOrderRecord){

				this.formSubmitionMode = true;

				this.singleOrderData.agent = {};
				this.singleOrderData.agent.orderServed = true;
				this.singleOrderData.agent.merchant_agent_id = this.merchant_agent_id;
				this.singleOrderData.agent.merchant_id = this.merchant_id;

				this.singleOrderData.order = merchantOrderRecord.order;
				
				this.submitConfirmation();

			},

			submitConfirmation(){

				// console.log(this.singleOrderData.order);
				// console.log(this.singleOrderData.agent);
				// return;

				axios
					.post('/order-serve-confirmations/' + this.singleOrderData.order.id + '/' + this.perPage + '?page=' + this.pagination.current_page, this.singleOrderData.agent)
					.then(response => {
						if (response.status == 200) {
							
							this.singleOrderData.order = {};
							this.singleOrderData.agent = {};

							this.allOrdersToBeServed = response.data;
							this.showListDataForSelectedTab();

							this.formSubmitionMode = false;
							$("#modal-acceptOrPickOrDrop-order").modal("hide");
							toastr.success(response.data.success, "Success");
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
			cancelledOrder(merchantOrderRecord) {

				if (merchantOrderRecord.order_cancellations.length) {
					return true;
				}

				return false;

			},

			servedOrder(merchantOrderRecord){
				
				if (merchantOrderRecord.order_serve_progression) {
					return true;
				}

				return false;
					
			},

			merchantOrderIsReady(merchantOrderRecord) {

				if (merchantOrderRecord.is_ready) {
					return true;
				}

				return false;

			},

		}
  	}

</script>