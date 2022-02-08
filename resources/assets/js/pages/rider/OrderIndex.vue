
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
									    	v-for="(riderDeliveryRecord, index) in deliveriesToShow"
									    	:key="riderDeliveryRecord.id" 
									    	:class="[(failedOrder(riderDeliveryRecord.order) || cancelledOrder(riderDeliveryRecord)) ? 'bg-secondary' : returnedOrder(riderDeliveryRecord) ? 'bg-primary' : deliveredOrder(riderDeliveryRecord) ? 'bg-success' : acceptedDeliveryOrder(riderDeliveryRecord) ? 'bg-info' : 'bg-danger']" 
									  	>
									    	<td scope="row">{{ index + 1 }}</td>

								    		<td>{{ riderDeliveryRecord.order_id }}</td>
								    		
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-primary btn-sm" 
									      			v-show="acceptedDeliveryOrder(riderDeliveryRecord)"
									      			@click="showOrderDetailModal(riderDeliveryRecord)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>
								      			<!-- disabled if rider / restaurant already cancelled -->
								      			
								      			<div v-if="! failedOrder(riderDeliveryRecord.order) && ! cancelledOrder(riderDeliveryRecord) && ! deliveredOrder(riderDeliveryRecord) && ! returnedOrder(riderDeliveryRecord)">
									      			<!-- return button -->
									      			<div v-if="allRestaurantPickedUp(riderDeliveryRecord)">
										      			<button 
											      			type="button" 
											      			class="btn btn-info btn-sm" 
											      			:disabled="formSubmitionMode" 
											      			@click="orderReturnConfirmation(riderDeliveryRecord)" 
										      			>
										        			<i class="fas fa-bell"></i>
										        			Return
										      			</button>

									      				<!-- drop button -->
										      			<button 
											      			type="button" 
											      			class="btn btn-success btn-sm" 
											      			:disabled="formSubmitionMode" 
											      			@click="orderDroppingConfirmation(riderDeliveryRecord)" 
										      			>
										        			<i class="fas fa-bell"></i>
										        			Drop
										      			</button>
									      			</div>
									      			
									      			<!-- pick up buttons -->
									      			<div v-if="! allRestaurantPickedUp(riderDeliveryRecord) && acceptedDeliveryOrder(riderDeliveryRecord)">
										      			<button
										      				type="button" 
											      			class="btn btn-warning btn-sm" 
											      			v-for="restaurantOrderRecord in riderDeliveryRecord.restaurants_accepted" 
											      			:disabled="Boolean(formSubmitionMode || pickedUp(riderDeliveryRecord, restaurantOrderRecord.restaurant.id))" 
											      			:key="restaurantOrderRecord.id" 
											      			@click="orderPickUpConfirmation(riderDeliveryRecord, restaurantOrderRecord)" 
										      			>
										      				{{ 'Pick Up from ' + restaurantOrderRecord.restaurant.name }}
										      			</button>
									      			</div>

									      			<!-- accept button -->
									      			<div v-if="! timeOutDeliveryOrder(riderDeliveryRecord) && !acceptedDeliveryOrder(riderDeliveryRecord)">
										      			<button
										      				type="button" 
											      			class="btn btn-primary btn-sm" 
											      			:disabled="formSubmitionMode"  
											      			@click="orderAcceptanceConfirmation(riderDeliveryRecord)" 
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
									<a class="nav-link" data-toggle="tab" href="#show-order-items-details">
										Order Items
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
							              			Order id:
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
								                  		'ASAP' : singleOrderData.order.scheduled ? singleOrderData.order.scheduled.order_schedule : 'NA'
								                  	}}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Cutlery:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.cutlery ? 'Added' : 'None' }}
								                </div>	
								            </div> 
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Price:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.order_price }}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Vat:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.vat }}
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
								                </div>	
								            </div>
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
								<div id="show-order-items-details" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Order Items:
							              		</label>
								                <div class="col-sm-6">
								                	<ul v-show="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length && singleOrderData.delivery_order_acceptance==1">
														<li v-for="(orderRestaurant, index) in singleOrderData.order.restaurants" 
															:key="orderRestaurant.id"
														>
															{{ orderRestaurant.restaurant ? orderRestaurant.restaurant.name : 'NA' | capitalize }}

															<ol v-show="orderRestaurant.items.length">
																<li v-for="(item, index) in orderRestaurant.items" 
																	:key="item.id"
																>	
																	{{ item.restaurant_menu_item.name | capitalize }} 

																	<span class="d-block"
																		v-if="item.restaurant_menu_item.has_variation" 
																	>
																		(Selected Variation : {{ item.variation.restaurant_menu_item_variation.variation.name | capitalize }} )
																	</span>

																	<p class="d-block">
																		<span class="font-weight-bold">- Quantity: </span>
																		{{ item.quantity }}
																	</p>

																	<span 
																		class="d-block font-weight-bold" 
																		v-if="item.addons.length"
																	>
																		Addons:
																	</span>

																	<ul v-if="item.restaurant_menu_item.has_addon && item.addons.length">

																		<li v-for="(additionalOrderedAddon, index) in item.addons">
																			{{ additionalOrderedAddon.restaurant_menu_item_addon.addon.name | capitalize }} ({{ additionalOrderedAddon.quantity }})
																		</li>
																	</ul>

																	<p class="d-block" v-if="item.customization">
																		<span class="font-weight-bold">- Customization: </span>
																		{{ item.customization | capitalize }}
																	</p>
																</li>
															</ol>

															<p class="text-danger" 
																v-show="! orderRestaurant.items.length"
															>
																No Items Found Yet
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
														singleOrderData.order.orderer && singleOrderData.order.orderer.hasOwnProperty('restaurant_id') ? 
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
		rider : {
			rider_id : null,
			orderPicked : false,
			orderDropped : false,
			orderAccepted : false,
		}
	};

	var restaurantListData = {
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
        // restaurant_id : document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
        rider_id : 1,
    };

	export default {

	    data() {
	        return restaurantListData;
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

				    Vue.set(this.deliveriesToShow, index, riderDeliveryOrder)
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
					.get('/api/rider-orders/' + this.rider_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
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
    		showOrderDetailModal(riderDeliveryRecord) {

				this.singleOrderData = riderDeliveryRecord;

				// if order request is accepted
				if (this.acceptedDeliveryOrder(riderDeliveryRecord)) {
					this.singleOrderData.order.restaurants = riderDeliveryRecord.restaurants;
				}

				// if any food is picked
				if (riderDeliveryRecord.rider_food_pick_confirmations.length && this.allRestaurantPickedUp(riderDeliveryRecord)) {
					
					this.singleOrderData.order.orderer = riderDeliveryRecord.order.orderer;

				}

				$("#modal-show-order").modal("show");

			},
			orderReturnConfirmation(riderDeliveryRecord) {

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderReturned = true;
				this.singleOrderData.rider.orderPicked = false;
				this.singleOrderData.rider.orderDropped = false;
				this.singleOrderData.rider.orderAccepted = false;
				this.singleOrderData.rider.rider_id = this.rider_id;

				this.singleOrderData.order = riderDeliveryRecord.order;
				this.submitConfirmation();

			},
			orderDroppingConfirmation(riderDeliveryRecord){

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderPicked = false;
				this.singleOrderData.rider.orderDropped = true;
				this.singleOrderData.rider.orderAccepted = false;
				this.singleOrderData.rider.rider_id = this.rider_id;

				this.singleOrderData.order = riderDeliveryRecord.order;
				this.submitConfirmation();

			},
			orderPickUpConfirmation(riderDeliveryRecord, restaurantOrderRecord){

				// console.log(riderDeliveryRecord);
				// console.log(restaurantOrderRecord);

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderPicked = true;
				this.singleOrderData.rider.orderDropped = false;
				this.singleOrderData.rider.orderAccepted = false;
				this.singleOrderData.rider.rider_id = this.rider_id;

				this.singleOrderData.rider.restaurant_id = restaurantOrderRecord.restaurant.id;

				this.singleOrderData.order = riderDeliveryRecord.order;
				this.submitConfirmation();

			},
			orderAcceptanceConfirmation(riderDeliveryRecord){

				this.formSubmitionMode = true;
				this.singleOrderData.rider = {};

				this.singleOrderData.rider.orderPicked = false;
				this.singleOrderData.rider.orderDropped = false;
				this.singleOrderData.rider.orderAccepted = true;
				this.singleOrderData.rider.rider_id = this.rider_id;

				this.singleOrderData.order = riderDeliveryRecord.order;
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

							this.formSubmitionMode = false;
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
			failedOrder(order){
				return order.in_progress===0 && order.complete_order===0 ? true : false;
			},
			cancelledOrder(riderDeliveryRecord) {

				if (this.riderCancelledOrder(riderDeliveryRecord) || this.allRestaurantCancelled(riderDeliveryRecord)) {
					return true;
				}

				return false;

			},
			timeOutDeliveryOrder(riderDeliveryRecord) {

			    // console.log(Date.now() - new Date(riderDeliveryRecord.created_at) > 1000 * 60);

			    // 30 seconds ago
			    return Date.now() - new Date(riderDeliveryRecord.created_at) > 1000 * 30 ; 	/* ms */

			},
			riderCancelledOrder(riderDeliveryRecord){

				return 	riderDeliveryRecord.rider_order_cancelations.some(
							orderCancelled=>orderCancelled.order_id==riderDeliveryRecord.order_id
						);

			},
			allRestaurantCancelled(riderDeliveryRecord) {

				let numberCancelledRestaurants = riderDeliveryRecord.restaurant_order_cancelations.length;

				let numberRestaurantsInOrder = riderDeliveryRecord.restaurants.length;

				if (numberCancelledRestaurants==numberRestaurantsInOrder) {
					return true;
				}

				return false;

			},
			deliveredOrder(riderDeliveryRecord){
				
				if (riderDeliveryRecord.rider_delivery_confirmation!=null && riderDeliveryRecord.rider_delivery_confirmation.rider_delivery_confirmation==1) {
					return true;
				}

				return false;
					
			},
			returnedOrder(riderDeliveryRecord){
				
				/*
				if (riderDeliveryRecord.rider_delivery_return!=null && riderDeliveryRecord.rider_delivery_return.rider_return_confirmation==1) {
					return true;
				}
				*/
			
				if (riderDeliveryRecord.rider_delivery_confirmation!=null && riderDeliveryRecord.rider_delivery_confirmation.rider_delivery_confirmation==2) {
					return true;
				}

				return false;
					
			},
			allRestaurantPickedUp(riderDeliveryRecord) {

				let totalRestaurantPickedUp = riderDeliveryRecord.rider_food_pick_confirmations.filter(
													orderPickUp => orderPickUp.rider_food_pick_confirmation == 1
												).length;

				let netRestaurantsToBePicked = riderDeliveryRecord.restaurants.length - riderDeliveryRecord.restaurant_order_cancelations.length;

				if ((totalRestaurantPickedUp===netRestaurantsToBePicked) && this.acceptedDeliveryOrder(riderDeliveryRecord)) {
					return true;
				}

				return false;

			},
			acceptedDeliveryOrder(riderDeliveryRecord){
				return riderDeliveryRecord.delivery_order_acceptance==1 ? true : false;
			},
			pickedUp(riderDeliveryRecord, restaurantId) {
				return 	riderDeliveryRecord.rider_food_pick_confirmations.some(
					orderPickUp => (orderPickUp.rider_food_pick_confirmation == 1 && orderPickUp.restaurant_id == restaurantId)
				);
			}
		}
  	}

</script>