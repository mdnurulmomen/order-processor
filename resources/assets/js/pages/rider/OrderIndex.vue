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
										Rider Order List
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
									  	<tr v-show="deliveriesToShow.length"
									    	v-for="(riderDelivery, index) in deliveriesToShow"
									    	:key="riderDelivery.id" 
									    	:class="[(failedOrder(riderDelivery.order) || cancelledOrder(riderDelivery)) ? 'bg-secondary' : returnedOrder(riderDelivery) ? 'bg-primary' : deliveredOrder(riderDelivery) ? 'bg-success' : acceptedDeliveryOrder(riderDelivery) ? 'bg-info' : 'bg-danger']" 
									  	>
									    	<td scope="row">{{ index + 1 }}</td>

								    		<td>{{ riderDelivery.order_id }}</td>
								    		
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-primary btn-sm" 
									      			v-show="acceptedDeliveryOrder(riderDelivery)"
									      			@click="showOrderDetailModal(riderDelivery)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>
								      			<!-- disabled if rider / merchant already cancelled -->
								      			
								      			<div v-if="! failedOrder(riderDelivery.order) && ! cancelledOrder(riderDelivery) && ! deliveredOrder(riderDelivery) && ! returnedOrder(riderDelivery)">
									      			<!-- return button -->
									      			<div v-if="allMerchantOrderIsCollected(riderDelivery)">
										      			<button 
											      			type="button" 
											      			class="btn btn-warning btn-sm" 
											      			:disabled="formSubmitionMode" 
											      			@click="orderReturnConfirmation(riderDelivery)" 
										      			>
										        			<i class="fas fa-bell"></i>
										        			Return
										      			</button>

									      				<!-- drop button -->
										      			<button 
											      			type="button" 
											      			class="btn btn-success btn-sm" 
											      			:disabled="formSubmitionMode" 
											      			@click="orderDroppingConfirmation(riderDelivery)" 
										      			>
										        			<i class="fas fa-bell"></i>
										        			Drop
										      			</button>
									      			</div>
									      			
									      			<!-- pick up buttons -->
									      			<div v-if="! allMerchantOrderIsCollected(riderDelivery) && acceptedDeliveryOrder(riderDelivery)">
										      			<button
										      				type="button" 
											      			class="btn btn-warning btn-sm" 
											      			v-for="merchantOrder in riderDelivery.merchants_accepted" 
											      			v-show="! pickedUp(riderDelivery, merchantOrder)"
											      			:disabled="formSubmitionMode" 
											      			:key="merchantOrder.id" 
											      			@click="orderPickUpConfirmation(riderDelivery, merchantOrder)" 
										      			>
										      				{{ 'Pick Up from ' + merchantOrder.merchant.name }}
										      			</button>
									      			</div>

									      			<!-- accept button -->
									      			<div v-if="! callReceivingTimeIsOver(riderDelivery) && ! acceptedDeliveryOrder(riderDelivery)">
										      			<button
										      				type="button" 
											      			class="btn btn-primary btn-sm" 
											      			:disabled="formSubmitionMode"  
											      			@click="orderAcceptanceConfirmation(riderDelivery)" 
										      			>
										      				Accept
										      			</button>
									      			</div>
								      			</div>
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!deliveriesToShow.length"
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
										@paginate="fetchAllDeliveryOrders()"
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
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Order Details
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true">&times;</span></button>
						</div>
						<div 
							class="modal-body"
							v-if="singleOrderData.order"
						>
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
							              			Id:
							              		</label>
								                <div class="col-sm-6" >
								                  	{{ singleOrderData.order.id }}
								                </div>
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			ASAP/Scheduled:
							              		</label>
								                <div class="col-sm-6">
								                  	{{
								                  		singleOrderData.order.asap ?
								                  		'ASAP' : singleOrderData.order.schedule ? singleOrderData.order.schedule.schedule : 'NA'
								                  	}}
								                </div>	
								            </div>
								            <div class="form-group row" v-show="singleOrderData.order.has_cutlery">		
							              		<label class="col-sm-6 text-right">
							              			Cutlery:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.has_cutlery ? 'Added' : 'None' }}
								                </div>	
								            </div> 
								            <!-- 
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Price:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.price }}
								                  	{{ $application_settings.official_currency || 'BDT' | capitalize }}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Vat:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.vat }} %
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Discount:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.discount }} %
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Payable Price:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.net_payable }}
								                  	{{ $application_settings.official_currency || 'BDT' | capitalize }}
								                </div>	
								            </div> 
								        	-->
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Payment:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.payment_method }}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Order Created:
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
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Products:
							              		</label>
								                <div class="col-sm-6">
								                	<ul v-show="singleOrderData.order.merchants && singleOrderData.order.merchants.length && singleOrderData.is_accepted==1">
														<li v-for="(merchantOrder, index) in singleOrderData.order.merchants" 
															:key="merchantOrder.id"
														>
															{{ merchantOrder.merchant ? merchantOrder.merchant.name : 'NA' | capitalize }}

															<ol v-show="merchantOrder.products.length">
																<li v-for="(product, index) in merchantOrder.products" 
																	:key="product.id"
																>	
																	{{ product.merchant_product.name | capitalize }} 

																	<span class="d-block"
																		v-if="product.merchant_product.has_variation" 
																	>
																		(Selected Variation : {{ product.variation.merchant_product_variation.variation.name | capitalize }} )
																	</span>

																	<p class="d-block">
																		<span class="font-weight-bold">- Quantity: </span>
																		{{ product.quantity }}
																	</p>

																	<span 
																		class="d-block font-weight-bold" 
																		v-if="product.addons.length"
																	>
																		Addons:
																	</span>

																	<ul v-if="product.merchant_product.has_addon && product.addons.length">

																		<li v-for="(additionalOrderedAddon, index) in product.addons">
																			{{ additionalOrderedAddon.merchant_product_addon.addon.name | capitalize }} ({{ additionalOrderedAddon.quantity }})
																		</li>
																	</ul>

																	<p class="d-block" v-if="product.customization">
																		<span class="font-weight-bold">- Customization: </span>
																		{{ product.customization | capitalize }}
																	</p>
																</li>
															</ol>

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
					            	</div>
								</div>
								<div id="show-orderer-details" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
								                		
							              		<label class="col-sm-6 text-right">
							              			Orderer:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ 
								                  		singleOrderData.order.orderer ? 
								                  		singleOrderData.order.orderer.user_name : 'NA' | capitalize  
													}}
													({{
														singleOrderData.order.orderer && singleOrderData.order.orderer.hasOwnProperty('merchant_id') ? 
								                  		'Agent' : 'Customer'
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
		rider : {
			rider_id : null,
			orderPicked : false,
			orderDropped : false,
			orderAccepted : false,
		}
	};

	var merchantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	
    	deliveriesToShow : [],
    	allDeliveryOrders : [],
    	formSubmitionMode : false,

    	pagination: {
        	current_page: 1
      	},

      	singleOrderData : singleOrderData,

        csrf : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        // merchant_id : document.querySelector('meta[name="merchant-id"]').getAttribute('content'),
        rider_id : 1,
    };

	export default {

	    data() {
	        return merchantListData;
		},

		created(){
			this.fetchAllDeliveryOrders();
		},

		mounted(){
			    
			Pusher.logToConsole = true;

			Echo.private(`notifyRider.` + this.rider_id)
			.listen('UpdateRider', (riderDeliveryOrder) => {
			    
			    // console.log(riderDeliveryOrder);

			    // due to pagination, checking if this broadcasted one already exists 
			    const objectExist = (orderObject) => orderObject.id==riderDeliveryOrder.id;

			    // if new delivery order in the list or nothing in the list
			    if ((!this.deliveriesToShow.some(objectExist)) || (Array.isArray(this.deliveriesToShow) && !this.deliveriesToShow.length)) {
			    	
			    	this.deliveriesToShow.unshift(riderDeliveryOrder);
			    	toastr.info("New delivery-order arrives");
			    }
			    // now showing the order in this page
			    else if (this.deliveriesToShow.some(objectExist)) {
				    let index = this.deliveriesToShow.findIndex(
				    		currentDeliveryOrder => (currentDeliveryOrder.id==riderDeliveryOrder.id && currentDeliveryOrder.order_id==riderDeliveryOrder.order_id)
				    	);

				    this.$set(this.deliveriesToShow, index, riderDeliveryOrder)
				    toastr.info("Delivery-order update arrives");
				    // console.log(index);
			    }
			    
		    	toastr.warning("Else");

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
			fetchAllDeliveryOrders(){
				this.loading = true;
				axios
					.get('/api/riders/' + false + '/orders/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {		
						if (response.status == 200) {
							this.allDeliveryOrders = response.data;
							this.showListDataForSelectedTab();
							this.loading = false;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			showListDataForSelectedTab(){
				this.deliveriesToShow = this.allDeliveryOrders.all.data;
				this.pagination = this.allDeliveryOrders.all;
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				this.fetchAllDeliveryOrders();
    		},
			reload() {
				this.pagination.current_page = 1;
				this.fetchAllDeliveryOrders();
    		},
    		showOrderDetailModal(riderDelivery) {

				this.singleOrderData = riderDelivery;

				// if order request is accepted
				if (this.acceptedDeliveryOrder(riderDelivery)) {
					this.singleOrderData.order.merchants = riderDelivery.merchants;
				}

				// if any food is picked
				if (riderDelivery.collections.length && this.allMerchantOrderIsCollected(riderDelivery)) {
					
					this.singleOrderData.order.orderer = riderDelivery.order.orderer;

				}

				$("#modal-show-order").modal("show");

			},
			orderReturnConfirmation(riderDelivery) {

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderReturned = true;
				this.singleOrderData.rider.orderPicked = false;
				this.singleOrderData.rider.orderDropped = false;
				this.singleOrderData.rider.orderAccepted = false;
				this.singleOrderData.rider.rider_id = riderDelivery.rider_id;

				this.singleOrderData.order = riderDelivery.order;
				this.submitConfirmation();

			},
			orderDroppingConfirmation(riderDelivery){

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderPicked = false;
				this.singleOrderData.rider.orderDropped = true;
				this.singleOrderData.rider.orderAccepted = false;
				this.singleOrderData.rider.rider_id = riderDelivery.rider_id;

				this.singleOrderData.order = riderDelivery.order;
				this.submitConfirmation();

			},
			orderPickUpConfirmation(riderDelivery, merchantOrder){

				// console.log(riderDelivery);
				// console.log(merchantOrder);

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderPicked = true;
				this.singleOrderData.rider.orderDropped = false;
				this.singleOrderData.rider.orderAccepted = false;
				this.singleOrderData.rider.rider_id = riderDelivery.rider_id;

				this.singleOrderData.rider.merchant_id = merchantOrder.merchant.id;

				this.singleOrderData.order = riderDelivery.order;
				this.submitConfirmation();

			},
			orderAcceptanceConfirmation(riderDelivery){

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderPicked = false;
				this.singleOrderData.rider.orderDropped = false;
				this.singleOrderData.rider.orderAccepted = true;
				this.singleOrderData.rider.rider_id = riderDelivery.rider_id;

				this.singleOrderData.order = riderDelivery.order;
				this.submitConfirmation();

			},
			submitConfirmation() {
				
				$("#modal-acceptOrPickOrDrop-order").modal("hide");

				axios
					.post('/delivery-order-confirmations/' + this.singleOrderData.order.id + '/' + this.perPage + '?page=' + this.pagination.current_page, this.singleOrderData.rider)
					.then(response => {
						if (response.status == 200) {
							
							this.singleOrderData.order = {};
							this.singleOrderData.rider = {};

							this.allDeliveryOrders = response.data;
							this.showListDataForSelectedTab();

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
					})
					.finally(() => {
					    this.formSubmitionMode = false;
				  	});
			},
			failedOrder(order){
				return order.in_progress===0 && order.is_completed===0 ? true : false;
			},
			cancelledOrder(riderDelivery) {

				if (this.riderCancelledOrder(riderDelivery) || this.allMerchantCancelled(riderDelivery)) {
					return true;
				}

				return false;

			},
			callReceivingTimeIsOver(riderDelivery) {

			    return (((new Date() - new Date(riderDelivery.created_at)) / 1000) > riderDelivery.rider_call_receiving_time);

			},
			riderCancelledOrder(riderDelivery){

				return 	riderDelivery.rider_order_cancellations.some(
							orderCancelled=>orderCancelled.order_id==riderDelivery.order_id
						);

			},
			allMerchantCancelled(riderDelivery) {

				let numberCancelledMerchants = riderDelivery.merchant_order_cancellations.length;

				let numberMerchantsInOrder = riderDelivery.merchants.length;

				if (numberCancelledMerchants==numberMerchantsInOrder) {
					return true;
				}

				return false;

			},
			deliveredOrder(riderDelivery){
				
				if (riderDelivery.is_delivered==1) {
					return true;
				}

				return false;
					
			},
			returnedOrder(riderDelivery){
				
				/*
				if (riderDelivery.rider_delivery_return!=null && riderDelivery.rider_delivery_return.rider_return_confirmation==1) {
					return true;
				}
				*/
			
				if (riderDelivery.is_delivered==2) {
					return true;
				}

				return false;
					
			},
			allMerchantOrderIsCollected(riderDelivery) {

				if (Array.isArray(riderDelivery.collections) && Array.isArray(riderDelivery.merchants) && Array.isArray(riderDelivery.merchant_order_cancellations)) {

					let totalMerchantPickedUp = riderDelivery.collections.filter(
														orderPickUp => orderPickUp.is_collected == 1
													).length;

					let netMerchantsToBePicked = riderDelivery.merchants.length - riderDelivery.merchant_order_cancellations.length;

					if ((totalMerchantPickedUp===netMerchantsToBePicked) && this.acceptedDeliveryOrder(riderDelivery)) {
						return true;
					}

				}

				return false;

			},
			acceptedDeliveryOrder(riderDelivery){
				return riderDelivery.is_accepted==1 ? true : false;
			},
			pickedUp(riderDelivery, merchantOrder) {
				return 	merchantOrder.hasOwnProperty('merchant') && riderDelivery.collections.some(
					orderPickUp => (orderPickUp.is_collected == 1 && orderPickUp.merchant_id == merchantOrder.merchant.id)
				);
			}
		}
  	}

</script>