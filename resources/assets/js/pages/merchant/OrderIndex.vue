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
								<div class="col-sm-6">
									<h2 class="lead mt-1">
										Order List
									</h2>
								</div>
									
								<div class="col-sm-6">
									<div class="was-validated">
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

						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-12">

									  	<ul 
										  	class="nav nav-tabs nav-justified mb-2" 
										  	v-show="query === ''"
									  	>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab==='all' }, 'nav-link']" 
													@click="showAllOrders"
												>
													All
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab==='served' }, 'nav-link']" 
													@click="showServedOrders"
												>
													Served
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-striped text-center">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Id</th>
											<th scope="col">Type</th>
											<th scope="col">Status</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="merchantOrdersToShow.length"
									    	v-for="(merchantOrder, index) in merchantOrdersToShow"
									    	:key="merchantOrder.id" 
									    	:class="orderRowClass(merchantOrder)" 
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ merchantOrder.order_id }}</td>
								    		<td>{{ merchantOrder.order.type | capitalize }}</td>
								    		<td>
								    			<span 
								    				v-if="orderIsFailed(merchantOrder.order) || orderIsCancelled(merchantOrder)" 
								    				class="badge badge-secondary d-block"
								    			>	
								    				Cancelled
								    			</span>

												<!-- no option should be shown without picking/cancelling every merchant orders -->
								    			<span 
								    				v-else-if="(isServingOrder(merchantOrder.order) && orderIsServed(merchantOrder)) || (isSelfDeliveryOrder(merchantOrder) && orderIsSelfDelivered(merchantOrder)) || (! isServingOrder(merchantOrder.order) && ! isSelfDeliveryOrder(merchantOrder) && orderIsReady(merchantOrder))" 
								    				class='badge badge-success d-block'
								    			>	
								    				Success
								    			</span>

								    			<!-- 
								    				// after call confirmation
								    				if rider has been assigned (riderFoodPickConfirmations is auto set after assignment)  
								    				// for each merchants in order
								    			-->
							    				<span 
							    					v-else-if="orderIsAccepted(merchantOrder)"
							    				>
								    				<span class='badge badge-info d-block'>
									    				Pending
								    				</span>
							    				</span>	

							    				<!-- 
							    					new order before call confirmation
							    				-->
								    			<span 
								    				class='badge badge-danger d-block' 
								    				v-else-if="orderIsRinging(merchantOrder)">	
								    				Ringing
								    			</span>
								    		</td>

								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showOrderDetailModal(merchantOrder)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>

								      			<!-- disabled if merchant already confirmed as ready-->
								      			<button 
									      			type="button" 
									      			class="btn btn-success btn-sm" 
									      			v-if="!orderIsCancelled(merchantOrder) && reservationOrderIsConfirmed(merchantOrder.order) && ! orderIsStopped(merchantOrder.order) && orderIsYetToServe(merchantOrder)" 
									      			:disabled="formSubmitionMode" 
									      			@click="singleOrderData.order=merchantOrder.order; singleOrderData.order.serveOrder=true; confirmOrder()" 
								      			>
								        			<i class="fas fa-bell"></i>
							        				Serve-Order
								      			</button>

								      			<!-- disabled if merchant already confirmed as ready-->
								      			<button 
									      			type="button" 
									      			class="btn btn-success btn-sm" 
									      			v-if="!orderIsCancelled(merchantOrder) && ! orderIsStopped(merchantOrder.order) && orderIsYetToDeliver(merchantOrder)" 
									      			:disabled="formSubmitionMode" 
									      			@click="singleOrderData.order=merchantOrder.order; singleOrderData.order.deliverOrder=true; confirmOrder()" 
								      			>
								        			<i class="fas fa-bell"></i>
							        				Deliver-Order
								      			</button>

								      			<!-- disabled if merchant already confirmed as ready-->
								      			<button 
									      			type="button" 
									      			class="btn btn-primary btn-sm" 
									      			v-if="!orderIsCancelled(merchantOrder) && !orderIsReady(merchantOrder) && reservationOrderIsConfirmed(merchantOrder.order) && ! orderIsStopped(merchantOrder.order)" 
									      			:disabled="formSubmitionMode" 
									      			@click="singleOrderData.order=merchantOrder.order; singleOrderData.order.orderReady=true; confirmOrder()" 
								      			>
								        			<i class="fas fa-bell"></i>
							        				Order-Ready
								      			</button>

								      			<!-- if merchant not accepted yet-->
								      			<button 
									      			type="button" 
									      			class="btn btn-warning btn-sm" 
									      			v-if="!orderIsCancelled(merchantOrder) && !orderIsReady(merchantOrder) && !orderIsAccepted(merchantOrder) && orderIsRinging(merchantOrder) && reservationOrderIsConfirmed(merchantOrder.order) && ! orderIsStopped(merchantOrder.order)" 
									      			:disabled="formSubmitionMode" 
									      			@click="singleOrderData.order=merchantOrder.order; singleOrderData.order.orderReady=false; confirmOrder()" 
								      			>
								        			<i class="fas fa-bell"></i>
								        			Accept-Order
								      			</button>

								      			<!-- disabled if merchant accepted -->
								      			<button 
									      			type="button" 
									      			class="btn btn-secondary btn-sm" 
									      			v-if="!orderIsCancelled(merchantOrder) && !orderIsAccepted(merchantOrder) && reservationOrderIsConfirmed(merchantOrder.order) && ! orderIsStopped(merchantOrder.order)" 
									      			@click="showOrderCancellationModal(merchantOrder.order)" 
								      			>
								        			<i class="fas fa-times"></i>
								        			Cancel
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
										@paginate="query === '' ? fetchAllOrders() : searchData()"
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
						  		{{ defineOrderType(singleOrderData.order) | capitalize }} Details
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true">&times;</span></button>
						</div>

						<div class="modal-body">
							<ul class="nav nav-tabs justify-content-center mb-4" role="tablist">
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#show-order-details">
										Order
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#show-order-products">
										Products
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div id="show-order-details" class="container tab-pane fade">
									
			            			<div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Id
					              		</label>
						                <div class="col-sm-6" >
						                  	{{ singleOrderData.order.id }}
						                </div>
						            </div>
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Type 
					              		</label>

						                <div class="col-sm-6">
						                	{{ singleOrderData.order.type | capitalize }}
						                </div>
						            </div>
						            <div class="form-group row" v-if="singleOrderData.order.is_asap_order || singleOrderData.order.scheduled">		
					              		<label class="col-sm-6 text-right">
					              			ASAP/Scheduled
					              		</label>
						                <div class="col-sm-6">
						                  	{{
						                  		singleOrderData.order.is_asap_order ?
						                  		'ASAP' : singleOrderData.order.scheduled.order_schedule
						                  	}}
						                </div>	
						            </div> 
						            <!-- 
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
					              			Discount
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.discount }} %
						                </div>	
						            </div>
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Delivery-fee
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.delivery_fee }}
						                  	{{ $application_settings.official_currency || 'BDT' | capitalize }}
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
						        	-->
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
					              			Ordered By
					              		</label>
						                <div class="col-sm-6">
						                  	{{ 
						                  		singleOrderData.order.orderer ? 
						                  		singleOrderData.order.orderer.user_name : 'NA' | capitalize 
											}}
											({{
												singleOrderData.order.orderer && singleOrderData.order.orderer.hasOwnProperty('merchant_id') ? 
						                  		'Merchant Agent' : 'Customer'
											}})
						                </div>	
						            </div>
								</div>
								<div id="show-order-products" class="container tab-pane active">
									<div class="row">
						                <div class="col-sm-12 text-md-center">
						                	<ul 
												class="list-group list-group-flush" 
												v-show="Boolean(singleOrderData.order.products && singleOrderData.order.products.length)" 
											>
												<li 
													class="list-group-item" 
													v-for="(product, index) in singleOrderData.order.products" 
													:key="product.id"
												>	
													{{ product.merchant_product.name | capitalize }} 

													<span v-if="product.merchant_product.has_variation" 
													>
														({{ product.variation.merchant_product_variation | capitalize }})
													</span>

													<p class="d-block">
														<span class="font-weight-bold">- Qty : </span>
														{{ product.quantity }}
													</p>

													<span 
														class="d-block font-weight-bold" 
														v-if="product.addons.length"
													>
														- Addons
													</span>

													<ul 
														class="form-group" 
														style="list-style-type: circle; list-style-position: inside;" 
														v-if="product.merchant_product.has_addon && product.addons.length"
													>

														<li v-for="(additionalOrderedAddon, index) in product.addons">
															{{ additionalOrderedAddon.merchant_product_addon | capitalize }} ({{ additionalOrderedAddon.quantity }})
														</li>
													</ul>

													<p class="d-block" v-if="product.customization">
														<span class="font-weight-bold">- Customization : </span>
														{{ product.customization | capitalize }}
													</p>
												</li>
											</ul>

											<p 
												class="text-danger" 
												v-show="Boolean(singleOrderData.order.products && !singleOrderData.order.products.length)"
											>
												No Products Found Yet
											</p>
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

			<!-- modal-order-cancellation -->
			<div class="modal fade" id="modal-order-cancellation">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ defineOrderType(singleOrderData.order) | capitalize }} Cancellation
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="cancelOrder()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<div class="form-group row">	
				              		<label 
				              			for="inputMenuName3" 
				              			class="col-sm-4 col-form-label text-right"
				              		>
				              			Cancellation Reason
				              		</label>
					                <div class="col-sm-8">
					                  	<select 
					                  		v-model="singleOrderData.orderCancellation.cancellation_reason_id" 
					                  		class="form-control" 
					                  		@change="submitCancellationForm=true;errors.orderCancellation.reason=null"
					                  	>
											<option :value="null" selected="true" disabled>Reason of cancellation</option>
											<option 
												v-for="cancellationReason in allCancellationReasons" 
												v-bind:value="cancellationReason.id"
											>
												<span v-html="cancellationReason.reason"></span>
											</option>
										</select>

					                	<div 
						                	class="text-danger" 
						                	v-if="errors.orderCancellation.reason"
					                	>
								        	{{ errors.orderCancellation.reason }}
								  		</div>
					                </div>
				              	</div>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">
							  		Close
							  	</button>

							  	<button 
							  		type="submit" 
							  		class="btn btn-outline-light float-right" 
							  		:disabled="!submitCancellationForm" 
							  	>
							  		Cancel Order
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-order-cancellation -->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleOrderData = {
		order : {
			orderReady : false,
		},
		orderCancellation : {
			cancellation_reason_id : null,
			merchant_id : document.querySelector('meta[name="merchant-id"]').getAttribute('content'),
		}
	};

	var merchantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	formSubmitionMode : false,
    	submitCancellationForm : true,
    	
    	allOrders : [],
    	merchantOrdersToShow : [],
    	allCancellationReasons : [],

    	currentTab : 'all',

    	pagination: {
        	current_page: 1
      	},

      	singleOrderData : singleOrderData,

      	errors : {
      		orderCancellation : {
      			reason : null,
      		},
      	},

        csrf : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        merchant_id : document.querySelector('meta[name="merchant-id"]').getAttribute('content'),
    };

	export default {

	    // Local registration of components
		components: { 
			// VueBootstrap4Table
		},

	    data() {
	        return merchantListData;
		},

		created(){
			this.fetchAllOrders();
			this.fetchAllCancellationReasons();
		},

		mounted(){
			    
			Pusher.logToConsole = true;

			Echo.private(`notifyMerchant.` + this.merchant_id)
			.listen('.updation-for-merchant', (merchantOrder) => {

			    // console.log(merchantOrder);

			    // due to pagination, checking if this broadcasted one already exists 
			    const orderExist = (currentOrder) => currentOrder.order_id==merchantOrder.order_id;
			    
			    // console.log(!this.merchantOrdersToShow.some(orderExist));

			    // new order and not in the list or nothing in the list
			    if ((merchantOrder.is_accepted == -1 && !this.merchantOrdersToShow.some(orderExist)) || (Array.isArray(this.merchantOrdersToShow) && !this.merchantOrdersToShow.length)) {
			    	
			    	this.merchantOrdersToShow.unshift(merchantOrder);
			    	// toastr.info("New Order arrives");
			    }
			    // now showing the order in this page
			    else if (this.merchantOrdersToShow.some(orderExist)) {
				    let index = this.merchantOrdersToShow.findIndex(currentOrder => currentOrder.order_id==merchantOrder.order_id);
				    // console.log(index);
				    // this.merchantOrdersToShow[index] = merchantOrder;
				    // this.merchantOrdersToShow.$set(index, merchantOrder);
				    // this.$set(this.merchantOrdersToShow, index, merchantOrder)
				    Vue.set(this.merchantOrdersToShow, index, merchantOrder);
				    // toastr.info("Old Order arrives");
			    }
			    // no previous order available
			    else {
			    	toastr.warning("Order update arrives");
			    }

			});

		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllOrders();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
		},

		filters: {
			capitalize: function (value) {
				if (!value) return ''
				value = value.toString()
				return value.charAt(0).toUpperCase() + value.slice(1)
			}
		},

		methods : {
			showAllOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'all';
				this.showListDataForSelectedTab();
			},
			showServedOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'served';
				this.showListDataForSelectedTab();
			},
			showListDataForSelectedTab(){
				if (this.currentTab=='all') {
					this.merchantOrdersToShow = this.allOrders.all.data;
					this.pagination = this.allOrders.all;
				}else if (this.currentTab=='served') {
					this.merchantOrdersToShow = this.allOrders.served.data;
					this.pagination = this.allOrders.served;
				}
			},
			fetchAllCancellationReasons(){
				this.loading = true;
				axios
					.get('/api/cancellation-reasons/')
					.then(response => {		
						if (response.status == 200) {
							this.allCancellationReasons = response.data;
							this.loading = false;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllOrders(){
				this.loading = true;
				axios
					.get('/orders/' + this.merchant_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {		
						if (response.status == 200) {
							this.allOrders = response.data;
							this.showListDataForSelectedTab();
							this.loading = false;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchAllOrders();
				}else {
					this.searchData();
				}
    		},
			reload() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchAllOrders();
				}else {
					this.searchData();
				}
    		},
    		showOrderDetailModal(merchant) {
				this.singleOrderData.order = merchant.order;
				this.singleOrderData.order.products = merchant.products;
				$("#modal-show-order").modal("show");
			},
			confirmOrder(){

				this.formSubmitionMode = true;
				this.singleOrderData.order.merchant_id = this.merchant_id;

				axios
					.post('/orders/' + this.singleOrderData.order.id + '/' + this.perPage + '?page=' + this.pagination.current_page, this.singleOrderData.order)
					.then(response => {
						if (response.status == 200) {
							
							this.allOrders = response.data;
							this.showListDataForSelectedTab();

							this.formSubmitionMode = false;
							this.singleOrderData.order = {};

							toastr.success(response.data.success, "Confirmed");
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
			showOrderCancellationModal(order) {
				this.singleOrderData.order = order;
				// this.singleOrderData.orderCancellation.cancellation_reason_id = null;
				this.singleOrderData.orderCancellation.merchant_id = this.merchant_id;
				$("#modal-order-cancellation").modal("show");
			},
			cancelOrder(){
				
				if (!this.singleOrderData.orderCancellation.cancellation_reason_id) {
					this.submitCancellationForm = false;
					this.errors.orderCancellation.reason = 'Reason is required';
					return;
				}

				$("#modal-order-cancellation").modal("hide");
				
				this.formSubmitionMode = true;

				axios
					.put('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.orderCancellation)
					.then(response => {
						if (response.status == 200) {
							
							this.allOrders = response.data;
							this.showListDataForSelectedTab();

							this.formSubmitionMode = false;
							this.singleOrderData.order = {};

							toastr.success(response.data.success, "Cancelled");
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
					"/api/orders/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allOrders = response.data;
					this.merchantOrdersToShow = this.allOrders.all.data;
					this.pagination = this.allOrders.all;
				})
				.catch(e => {
					console.log(e);
				});

			},
			orderIsCancelled(merchantOrder) {
				
				return Boolean(merchantOrder.is_accepted==0);

			},
			orderIsRinging(merchantOrder) {

				return merchantOrder.is_accepted == -1 ? true : false;

			},
			orderIsAccepted(merchantOrder) {

				return merchantOrder.is_accepted == 1 ? true : false;

			},
			orderIsReady(merchantOrder) {

				return merchantOrder.is_ready == 1 ? true : false;

			},
			reservationOrderIsConfirmed(order) {

				if (this.defineOrderType(order)=='reservation' && order.customer_confirmation==-1) {

					return false;

				}

				// paid reservation or not even a reservation order
				return true;

			}, 
			defineOrderType(order) {

				return order.type;

			},
			orderIsStopped(order){

				return order.in_progress==0 ? true : false;		// delivered / served / 
			},
			orderIsFailed(order){

				return order.in_progress===0 && order.is_completed===0 ? true : false;
			},
			orderIsYetToDeliver(merchantOrder) {

				if (this.isSelfDeliveryOrder(merchantOrder) && !this.orderIsSelfDelivered(merchantOrder)) {

					return true;

				}

				return false;

			},
			isSelfDeliveryOrder(merchantOrder) {

				if (this.defineOrderType(merchantOrder.order)==='delivery' && merchantOrder.is_self_delivery == 1) {
					return true;
				}

				return false;

			},
			// completed order
			orderIsSelfDelivered(merchantOrder) {

				if (merchantOrder.is_delivered==1) {

					return true;
				}
				else{
					return false;
				}
			},
			orderIsYetToServe(merchantOrder) {

				if (this.isServingOrder(merchantOrder.order) && !this.orderIsServed(merchantOrder)) {

					return true;

				}

				return false;

			},
			// order is for serve 
			isServingOrder(order) {

				if (this.defineOrderType(order)==='reservation' || this.defineOrderType(order)==='serving' ) {

					return true;

				}

				return false;

			},
			// completed order
			orderIsServed(merchantOrder) {

				if (merchantOrder.order_serve_confirmation && merchantOrder.order_serve_confirmation.is_served==1) {

					return true;
				}
				else{
					return false;
				}
			},
			orderRowClass(merchantOrder) {

				if (this.orderIsFailed(merchantOrder.order) || this.orderIsCancelled(merchantOrder) || ! this.reservationOrderIsConfirmed(merchantOrder.order)) {

					return 'bg-secondary';
				}
				else if (this.isServingOrder(merchantOrder.order) && this.orderIsServed(merchantOrder)) {
					
					return 'bg-success';
				}
				else if (this.isSelfDeliveryOrder(merchantOrder) && this.orderIsSelfDelivered(merchantOrder)) {
					
					return 'bg-success';
				}
				else if ((this.isServingOrder(merchantOrder.order) || this.isSelfDeliveryOrder(merchantOrder)) && this.orderIsReady(merchantOrder)) {
					
					return 'bg-primary';
				}
				else if (! this.isServingOrder(merchantOrder.order) && ! this.isSelfDeliveryOrder(merchantOrder) && this.orderIsReady(merchantOrder)) {
					
					return 'bg-success';
				}
				else if (this.orderIsAccepted(merchantOrder)) {

					return 'bg-info';
				}
				else
					return 'bg-danger';

			}

		}
  	}

</script>