
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
											<th scope="col">Id</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="deliveriesToShow.length"
									    	v-for="(riderDeliveryRecord, index) in deliveriesToShow"
									    	:key="riderDeliveryRecord.id" 
									    	:class="[(riderDeliveryRecord.rider_delivery_confirmation && riderDeliveryRecord.rider_delivery_confirmation.rider_delivery_confirmation) ? 'bg-success' : (riderDeliveryRecord.rider_order_cancelations.some(orderCancelled=>orderCancelled.order_id==riderDeliveryRecord.order_id) || riderDeliveryRecord.restaurant_order_cancelations.some(orderCancelled=>orderCancelled.order_id==riderDeliveryRecord.order_id)) ? 'bg-secondary' : riderDeliveryRecord.delivery_order_acceptance ? 'bg-warning' : 'bg-danger']" 
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ riderDeliveryRecord.order_id }}</td>
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showOrderDetailModal(riderDeliveryRecord)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>
								      			<!-- disabled if rider / restaurant already cancelled -->
								      			<button 
									      			type="button" 
									      			class="btn btn-primary btn-sm" 
									      			v-if="!riderDeliveryRecord.rider_order_cancelations.some(orderCancelled=>orderCancelled.order_id==riderDeliveryRecord.order_id) && !riderDeliveryRecord.restaurant_order_cancelations.some(orderCancelled=>orderCancelled.order_id==riderDeliveryRecord.order_id)" 
									      			@click="showOrderConfirmationModal(riderDeliveryRecord)" 
								      			>
								        			<i class="fas fa-bell"></i>
								        			{{
								        				riderDeliveryRecord.rider_food_pick_confirmations.filter(
								        					orderPickUp => orderPickUp.rider_food_pick_confirmation == 1
								        				).length === (riderDeliveryRecord.restaurants.length-riderDeliveryRecord.restaurant_order_cancelations.length) ? 'Drop' : 'Pick-Up'  
								        			}}
								      			</button>
								      			<!-- disabled if rider not yet accepted or already cancelled -->
								      			<button 
									      			type="button" 
									      			class="btn btn-secondary btn-sm" 
									      			v-if="!riderDeliveryRecord.delivery_order_acceptance!=1 && !riderDeliveryRecord.rider_order_cancelations.some(orderCancelled=>orderCancelled.order_id==riderDeliveryRecord.order_id) && !riderDeliveryRecord.restaurant_order_cancelations.some(orderCancelled=>orderCancelled.order_id==riderDeliveryRecord.order_id)" 
									      			@click="showOrderCancelationModal(riderDeliveryRecord)" 
								      			>
								        			<i class="fas fa-times"></i>
								        			Cancel
								      			</button>
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
										@paginate="fetchAllOrders()"
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
							              			Order id
							              		</label>
								                <div class="col-sm-6" >
								                  	{{ singleOrderData.order.id }}
								                </div>
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			ASAP/Scheduled
							              		</label>
								                <div class="col-sm-6">
								                  	{{
								                  		singleOrderData.order.is_asap_order ?
								                  		'ASAP' : singleOrderData.order.delivery_datetime
								                  	}}
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
							              			Payable Price
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.net_payable }}
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
								<div id="show-order-items-details" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Order Items
							              		</label>
								                <div class="col-sm-6">
								                	<ul v-show="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length" 
								                		style="list-style-type: none;"
								                	>
														<li v-for="(orderedRestaurant, index) in singleOrderData.order.restaurants" 
															:key="orderedRestaurant.id"
														>
															<ul v-show="orderedRestaurant.items.length">
																<li v-for="(item, index) in orderedRestaurant.items" 
																	:key="item.id"
																>	
																	{{ item.restaurant_menu_item.name }}  
																	(quantity : {{ item.quantity }})
																</li>
															</ul>
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
							              			Ordered By
							              		</label>
								                <div class="col-sm-6">
								                  	{{ 
								                  		singleOrderData.order.orderer ? 
								                  		singleOrderData.order.orderer.user_name : 'NA'  
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

			<!-- modal-acceptOrPickOrDrop-order -->
			<div class="modal fade" id="modal-acceptOrPickOrDrop-order">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-warning">
						  	<h4 class="modal-title">Order Confirmation</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="submitConfirmation()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								            	
												<ul class="nav nav-tabs justify-content-center mb-4" role="tablist">
													<li class="nav-item">
														<a class="nav-link active" data-toggle="tab" href="#order">
															Order
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#order-items">
															Order Items
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#orderer">
															Orderer
														</a>
													</li>
												</ul>

												<!-- Tab panes -->
												<div class="tab-content">
													<div id="order" class="container tab-pane active">
														<div class="row">
										            		<div class="col-sm-12">
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
												              			ASAP/Scheduled
												              		</label>
													                <div class="col-sm-6">
													                  	{{
													                  		singleOrderData.order.is_asap_order ?
													                  		'ASAP' : singleOrderData.order.delivery_datetime
													                  	}}
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
												              			Payable Price
												              		</label>
													                <div class="col-sm-6">
													                  	{{ singleOrderData.order.net_payable }}
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
													<div id="order-items" class="container tab-pane fade">
														<div class="row">
										            		<div class="col-sm-12">
										            			<div class="form-group row">		
												              		<label class="col-sm-6 text-right">
												              			Order Items
												              		</label>
													                <div class="col-sm-6">
													                	<ul v-show="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length" 
													                		style="list-style-type: none;"
													                	>
																			<li v-for="(orderedRestaurant, index) in singleOrderData.order.restaurants" 
																				:key="orderedRestaurant.id"
																			>
																				<ul v-show="orderedRestaurant.items.length">
																					<li v-for="(item, index) in orderedRestaurant.items" 
																						:key="item.id"
																					>	
																						{{ item.restaurant_menu_item.name }}  
																						(quantity : {{ item.quantity }})
																					</li>
																				</ul>
																			</li>
																		</ul>
													                </div>	
													            </div>  
										            		</div>
										            	</div>
													</div>
													<div id="orderer" class="container tab-pane fade">
														<div class="row">
										            		<div class="col-sm-12">
										            			<div class="form-group row">		
													                		
												              		<label class="col-sm-6 text-right">
												              			Ordered By
												              		</label>
													                <div class="col-sm-6">
													                  	{{ 
													                  		singleOrderData.order.orderer ? 
													                  		singleOrderData.order.orderer.user_name : 'NA'  
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
								            <!-- /.card-body -->
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
							  	<button 
							  		type="submit" 
							  		name="cancel-order" 
							  		:disabled="true" 
							  		@click="singleOrderData.rider.orderDropped=true;" 
							  		class="btn btn-outline-success float-right" 
							  		v-if="true" 
							  	>
							  		Drop-Order
							  	</button>

							  	<button 
							  		type="submit" 
							  		name="cancel-order" 
							  		:disabled="true" 
							  		@click="singleOrderData.rider.orderPicked=true" 
							  		class="btn btn-outline-success float-right" 
							  		v-if="true" 
							  	>
							  		Picked-up
							  	</button>

							  	<button 
							  		type="submit" 
							  		name="confirm-order" 
							  		@click="singleOrderData.rider.orderAccepted=false" 
							  		class="btn btn-outline-warning float-right" 
							  		v-if="true"
							  	>
							  		Accept-Order
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-acceptOrPickOrDrop-order -->

			<!-- modal-order-cancelation -->
			<div class="modal fade" id="modal-order-cancelation">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Order Cancelation
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
			// orderReady : false,
		},
		rider : {
			orderDropped : false,
			orderPicked : false,
			orderAccepted : false,
			rider_id : null,
		},
		orderCancelation : {
			reason_id : null,
			rider_id : 1, // have to be sent from app dynamically
		}
	};

	var restaurantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitCancelationForm : true,
    	
    	allOrders : [],
    	deliveriesToShow : [],
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
        // restaurant_id : document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
        rider_id : 1,
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

			Echo.private(`notifyRider`)
			.listen('UpdateRider', (riderDeliveryOrder) => {
			    
			    console.log(riderDeliveryOrder);
			    this.deliveriesToShow.unshift(riderDeliveryOrder);
		    	toastr.warning("New order delivery arrives");

			});

		},

		methods : {
			showListDataForSelectedTab(){
				this.deliveriesToShow = this.allOrders.all.data;
				this.pagination = this.allOrders.all;
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
					.get('/api/rider-orders/' + this.rider_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {		
						if (response.status == 200) {
							console.log(response);
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
				this.fetchAllOrders();
    		},
			reload() {
				this.pagination.current_page = 1;
				this.fetchAllOrders();
    		},
    		showOrderDetailModal(riderDeliveryRecord) {
    			console.log(riderDeliveryRecord);
				
				this.singleOrderData.order = riderDeliveryRecord.order;

				// if order request is accepted
				if (riderDeliveryRecord.delivery_order_acceptance) {
					this.singleOrderData.order.restaurants = riderDeliveryRecord.restaurants;
				}

				// if any food is picked
				if (riderDeliveryRecord.rider_food_pick_confirmations.length) {
					
					let totalRestaurantFoodPicked = riderDeliveryRecord.rider_food_pick_confirmations.filter(
														orderPickUp => orderPickUp.rider_food_pick_confirmation == 1
													).length;

					let totalRestaurantFoodsToPick = riderDeliveryRecord.restaurants.length - riderDeliveryRecord.restaurant_order_cancelations.length;

					// if all restaruants food are picked
					if(totalRestaurantFoodPicked == totalRestaurantFoodsToPick) {

						this.singleOrderData.order.orderer = riderDeliveryRecord.order.orderer;

					}

				}

				$("#modal-show-order").modal("show");
			},
			showOrderConfirmationModal(riderDeliveryRecord) {
				
				this.singleOrderData.order = riderDeliveryRecord.order;

				this.singleOrderData.rider.orderDropped = false;
				this.singleOrderData.rider.orderPicked = false;
				this.singleOrderData.rider.orderAccepted = false;
				this.singleOrderData.rider.rider_id = this.rider_id;

				// if order request is accepted
				if (riderDeliveryRecord.delivery_order_acceptance) {
					this.singleOrderData.order.restaurants = riderDeliveryRecord.restaurants;
				}

				// if any food is picked
				if (riderDeliveryRecord.rider_food_pick_confirmations.length) {
					
					let totalRestaurantFoodPicked = riderDeliveryRecord.rider_food_pick_confirmations.filter(
														orderPickUp => orderPickUp.rider_food_pick_confirmation == 1
													).length;

					let totalRestaurantFoodsToPick = riderDeliveryRecord.restaurants.length - riderDeliveryRecord.restaurant_order_cancelations.length;

					// if all restaruants food are picked
					if(totalRestaurantFoodPicked == totalRestaurantFoodsToPick) {

						this.singleOrderData.order.orderer = riderDeliveryRecord.order.orderer;

					}

				}

				$("#modal-acceptOrPickOrDrop-order").modal("show");
			},
			submitConfirmation(){

				$("#modal-acceptOrPickOrDrop-order").modal("hide");
				
				axios
					.post('/delivery-order-confirmations/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.rider)
					.then(response => {
						if (response.status == 200) {
							
							this.singleOrderData.order = {};

							this.allOrders = response.data;
							this.showListDataForSelectedTab();

							toastr.success(response.data.success, "Accepted");
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
			showOrderCancelationModal(riderDeliveryRecord) {
				this.singleOrderData.order = riderDeliveryRecord.order;
				this.singleOrderData.orderCancelation.reason_id = null;
				this.singleOrderData.orderCancelation.rider_id = this.rider_id;
				$("#modal-order-cancelation").modal("show");
			},
			cancelOrder(){
				if (!this.singleOrderData.orderCancelation.reason_id) {
					this.submitCancelationForm = false;
					this.errors.orderCancelation.reason = 'Reason is required';
					return;
				}

				$("#modal-order-cancelation").modal("hide");
				
				axios
					.put('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.orderCancelation)
					.then(response => {
						if (response.status == 200) {
							
							this.singleOrderData.order = {};
							this.singleOrderData.orderCancelation = {};

							this.allOrders = response.data;
							this.showListDataForSelectedTab();

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
		}
  	}

</script>