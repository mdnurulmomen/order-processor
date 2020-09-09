
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
											<th scope="col">Order Id</th>
											<th scope="col">Type</th>
											<!-- <th scope="col">Status</th> -->
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="ordersToShow.length"
									    	v-for="(restaurant, index) in ordersToShow"
									    	:key="restaurant.id" 
									    	:class="orderRowClass(restaurant.order)" 
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ restaurant.order_id }}</td>
								    		<td>{{ restaurant.order.order_type | capitalize }}</td>
								    		<!-- <td></td> -->
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showOrderDetailModal(restaurant)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>
								      			<!-- disabled if restaurant already confirmed as ready-->
								      			<button 
									      			type="button" 
									      			class="btn btn-success btn-sm" 
									      			v-if="!cancelledOrder(restaurant.order.restaurant_acceptances, restaurant.order.restaurant_order_cancelations) && orderToServe(restaurant.order) && confirmedReservationOrder(restaurant.order)" 
									      			:disabled="formSubmitionMode" 
									      			@click="singleOrderData.order=restaurant.order;singleOrderData.order.serveOrder=true;confirmOrder()" 
								      			>
								        			<i class="fas fa-bell"></i>
							        				Serve-Order
								      			</button>
								      			<!-- disabled if restaurant already confirmed as ready-->
								      			<button 
									      			type="button" 
									      			class="btn btn-primary btn-sm" 
									      			v-if="!cancelledOrder(restaurant.order.restaurant_acceptances, restaurant.order.restaurant_order_cancelations) && !readyOrder(restaurant.order.order_ready_confirmations) && confirmedReservationOrder(restaurant.order)" 
									      			:disabled="formSubmitionMode" 
									      			@click="singleOrderData.order=restaurant.order;singleOrderData.order.orderReady=true;confirmOrder()" 
								      			>
								        			<i class="fas fa-bell"></i>
							        				Order-Ready
								      			</button>
								      			<!-- if restaurant not accepted yet-->
								      			<button 
									      			type="button" 
									      			class="btn btn-warning btn-sm" 
									      			v-if="!cancelledOrder(restaurant.order.restaurant_acceptances, restaurant.order.restaurant_order_cancelations) && !readyOrder(restaurant.order.order_ready_confirmations) && !acceptedOrder(restaurant.order.restaurant_acceptances) && confirmedReservationOrder(restaurant.order)" 
									      			:disabled="formSubmitionMode" 
									      			@click="singleOrderData.order=restaurant.order;singleOrderData.order.orderReady=false;confirmOrder()" 
								      			>
								        			<i class="fas fa-bell"></i>
								        			Accept-Order
								      			</button>
								      			<!-- disabled if restaurant accepted -->
								      			<button 
									      			type="button" 
									      			class="btn btn-secondary btn-sm" 
									      			v-if="!cancelledOrder(restaurant.order.restaurant_acceptances, restaurant.order.restaurant_order_cancelations) && !acceptedOrder(restaurant.order.restaurant_acceptances) && confirmedReservationOrder(restaurant.order)" 
									      			@click="showOrderCancelationModal(restaurant.order)" 
								      			>
								        			<i class="fas fa-times"></i>
								        			Cancel
								      			</button>
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!ordersToShow.length"
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
									<a class="nav-link active" data-toggle="tab" href="#show-order-items-details">
										Order Items
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div id="show-order-details" class="container tab-pane fade">
									
			            			<div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Order id
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
						                	{{ singleOrderData.order.order_type | capitalize }}
						                </div>
						            </div>
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			ASAP/Scheduled
					              		</label>
						                <div class="col-sm-6">
						                  	{{
						                  		singleOrderData.order.is_asap_order ?
						                  		'ASAP' : singleOrderData.order.order_schedule
						                  	}}
						                </div>	
						            </div> 
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Price
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.order_price }}
						                </div>	
						            </div>
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Vat
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.vat }}
						                </div>	
						            </div>
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Discount
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.discount }}
						                </div>	
						            </div>
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Delivery-fee
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.delivery_fee }}
						                </div>	
						            </div> 
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Payable Price
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.net_payable }}
						                </div>	
						            </div> 
						            <div class="form-group row">		
					              		<label class="col-sm-6 text-right">
					              			Cutlery
					              		</label>
						                <div class="col-sm-6">
						                  	{{ singleOrderData.order.cutlery_addition ? 'Added' : 'None' }}
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
												singleOrderData.order.orderer && singleOrderData.order.orderer.hasOwnProperty('restaurant_id') ? 
						                  		'Waiter' : 'Customer'
											}})
						                </div>	
						            </div>
								</div>
								<div id="show-order-items-details" class="container tab-pane active">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Order Items
							              		</label>
								                <div class="col-sm-6">

								                	<ul v-show="Boolean(singleOrderData.order.items && singleOrderData.order.items.length)" 
								                	>	
														<li v-for="(item, index) in singleOrderData.order.items" 
															:key="item.id"
														>	
															{{ item.restaurant_menu_item.name }}

															<span class="d-block"
																v-if="item.restaurant_menu_item.has_variation" 
															>
																(Selected Variation : {{ item.selected_item_variation.restaurant_menu_item_variation.variation.variation_name }} )
															</span>

															(quantity : {{ item.quantity }})

															<span 
																class="d-block font-weight-bold" 
																v-if="item.additional_ordered_addons.length"
															>
																Extra Addons
															</span>

															<ul v-if="item.restaurant_menu_item.has_addon && item.additional_ordered_addons.length">

																<li v-for="(additionalOrderedAddon, index) in item.additional_ordered_addons">
																	{{ additionalOrderedAddon.restaurant_menu_item_addon.addon.name }}
																</li>
															</ul>

														</li>
													</ul>

													<p 
														class="text-danger" 
														v-show="Boolean(singleOrderData.order.items && !singleOrderData.order.items.length)"
													>
														No Items Found Yet
													</p>

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

			<!-- modal-order-cancelation -->
			<div class="modal fade" id="modal-order-cancelation">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ defineOrderType(singleOrderData.order) | capitalize }} Cancelation
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
				              			Cancelation Reason
				              		</label>
					                <div class="col-sm-8">
					                  	
					                  	<select 
					                  		v-model="singleOrderData.orderCancelation.reason_id" 
					                  		class="form-control" 
					                  		@change="submitCancelationForm=true;errors.orderCancelation.reason=null"
					                  	>
											<option :value="null" selected="true" disabled>Reason of cancelation</option>
											<option 
												v-for="cancelationReason in allCancelationReasons" 
												v-bind:value="cancelationReason.id"
											>
												<span v-html="cancelationReason.reason"></span>
											</option>
										</select>

					                	<div 
						                	class="text-danger" 
						                	v-if="errors.orderCancelation.reason"
					                	>
								        	{{ errors.orderCancelation.reason }}
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
							  		:disabled="!submitCancelationForm" 
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
			<!-- /modal-order-cancelation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleOrderData = {
		order : {
			orderReady : false,
		},
		orderCancelation : {
			reason_id : null,
			restaurant_id : document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
		}
	};

	var restaurantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	formSubmitionMode : false,
    	submitCancelationForm : true,
    	
    	allOrders : [],
    	ordersToShow : [],
    	allCancelationReasons : [],

    	currentTab : 'all',

    	pagination: {
        	current_page: 1
      	},

      	singleOrderData : singleOrderData,

      	errors : {
      		orderCancelation : {
      			reason : null,
      		},
      	},

        csrf : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        restaurant_id : document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
    };

	export default {

	    // Local registration of components
		components: { 
			// VueBootstrap4Table
		},

	    data() {
	        return restaurantListData;
		},

		created(){
			this.fetchAllOrders();
			this.fetchAllCancelationReasons();
		},

		mounted(){
			    
			Pusher.logToConsole = true;

			Echo.private(`notifyRestaurant.` + this.restaurant_id)
			.listen('.updation-for-restaurant', (orderedRestaurant) => {

			    console.log(orderedRestaurant);

			    // due to pagination, checking if this broadcasted one already exists 
			    const orderExist = (currentOrder) => currentOrder.order_id==orderedRestaurant.order_id;

			    // due to pagination, checking if this broadcasted restaurant is for this one & ringing 
			    const restaurantRinging = (restaurantOrderRecord) => (restaurantOrderRecord.restaurant_id==this.restaurant_id && restaurantOrderRecord.food_order_acceptance==-1);
			    
			    // console.log(orderedRestaurant.order.restaurant_acceptances.some(restaurantRinging));
			    // console.log(!this.ordersToShow.some(orderExist));

			    // new order and not in the list or nothing in the list
			    if ((orderedRestaurant.order.restaurant_acceptances.some(restaurantRinging) && !this.ordersToShow.some(orderExist)) || (Array.isArray(this.ordersToShow) && !this.ordersToShow.length)) {
			    	
			    	this.ordersToShow.unshift(orderedRestaurant);
			    	toastr.info("New Order arrives");
			    }
			    // now showing the order in this page
			    else if (this.ordersToShow.some(orderExist)) {
				    let index = this.ordersToShow.findIndex(currentOrder => currentOrder.order_id==orderedRestaurant.order_id);
				    // console.log(index);
				    // this.ordersToShow[index] = orderedRestaurant;
				    // this.ordersToShow.$set(index, orderedRestaurant);
				    // this.$set(this.ordersToShow, index, orderedRestaurant)
				    Vue.set(this.ordersToShow, index, orderedRestaurant);
				    toastr.info("Old Order arrives");
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
					this.ordersToShow = this.allOrders.all.data;
					this.pagination = this.allOrders.all;
				}else if (this.currentTab=='served') {
					this.ordersToShow = this.allOrders.served.data;
					this.pagination = this.allOrders.served;
				}
			},
			fetchAllCancelationReasons(){
				this.loading = true;
				axios
					.get('/api/cancelation-reasons/')
					.then(response => {		
						if (response.status == 200) {
							this.allCancelationReasons = response.data;
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
					.get('/orders/' + this.restaurant_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
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
    		showOrderDetailModal(restaurant) {
				this.singleOrderData.order = restaurant.order;
				this.singleOrderData.order.items = restaurant.items;
				$("#modal-show-order").modal("show");
			},
			confirmOrder(){

				this.formSubmitionMode = true;
				this.singleOrderData.order.restaurant_id = this.restaurant_id;

				axios
					.post('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.order)
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
			showOrderCancelationModal(order) {
				this.singleOrderData.order = order;
				// this.singleOrderData.orderCancelation.reason_id = null;
				this.singleOrderData.orderCancelation.restaurant_id = this.restaurant_id;
				$("#modal-order-cancelation").modal("show");
			},
			cancelOrder(){
				
				if (!this.singleOrderData.orderCancelation.reason_id) {
					this.submitCancelationForm = false;
					this.errors.orderCancelation.reason = 'Reason is required';
					return;
				}

				$("#modal-order-cancelation").modal("hide");
				
				this.formSubmitionMode = true;

				axios
					.put('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.orderCancelation)
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
					this.ordersToShow = this.allOrders.all.data;
					this.pagination = this.allOrders.all;
				})
				.catch(e => {
					console.log(e);
				});

			},
			cancelledOrder(orderAcceptedRestaurants, restaurantOrderCancelations) {
				
				let cancelled = false;

				if (orderAcceptedRestaurants.length) {
					cancelled = orderAcceptedRestaurants.some(
						currentAcceptance => (currentAcceptance.restaurant_id==this.restaurant_id && currentAcceptance.food_order_acceptance==0)
					);
				}
				if (!cancelled && restaurantOrderCancelations.length) {
					return restaurantOrderCancelations.some(
						cancelationReason => (cancelationReason.restaurant_id==this.restaurant_id)
					);
				}
				
				return cancelled;
			},
			acceptedOrder(restaurantAcceptances) {

				if (restaurantAcceptances.length) {
					return restaurantAcceptances.some(
						currentRestaurantAcceptance => (currentRestaurantAcceptance.restaurant_id==this.restaurant_id && currentRestaurantAcceptance.food_order_acceptance==1)
					);
				}

				return false;
			},
			readyOrder(orderReadyConfirmations) {

				if (orderReadyConfirmations.length) {
					return orderReadyConfirmations.some(
						currentRestaurantConfirmation => (currentRestaurantConfirmation.restaurant_id==this.restaurant_id && currentRestaurantConfirmation.food_ready_confirmation==1)
					);
				}
				
				return false;
			},
			confirmedReservationOrder(order) {

				if (this.defineOrderType(order)=='reservation' && order.customer_confirmation==-1) {

					return false;

				}

				// paid reservation or not even a reservation order
				return true;
			}, 
			defineOrderType(order) {

				return order.order_type;

			},
			orderToServe(order) {

				if (this.servingOrder(order) && !this.servedOrder(order)) {

					return true;

				}

				return false;

			},
			// order is for serve 
			servingOrder(order) {

				if (this.defineOrderType(order)==='reservation' || this.defineOrderType(order)==='serve-on-table' ) {

					return true;

				}

				return false;

			},
			// completed order
			servedOrder(order) {

				if (order.order_serve_confirmation && order.order_serve_confirmation.food_serve_confirmation==1) {
					return true;
				}else
					return false;

			},
			orderRowClass(order) {

				if (this.cancelledOrder(order.restaurant_acceptances, order.restaurant_order_cancelations) || !this.confirmedReservationOrder(order)) {

					return 'bg-secondary';
				}
				else if (this.servingOrder(order) && this.servedOrder(order)) {
					
					return 'bg-success';
				}
				else if (this.servingOrder(order) && this.readyOrder(order.order_ready_confirmations)) {
					
					return 'bg-primary';
				}
				else if (!this.servingOrder(order) && this.readyOrder(order.order_ready_confirmations)) {
					
					return 'bg-success';
				}
				else if (this.acceptedOrder(order.restaurant_acceptances)) {

					return 'bg-warning';
				}
				else
					return 'bg-danger';

			}

		}
  	}

</script>