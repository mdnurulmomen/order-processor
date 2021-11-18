
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
										Waiter Order List
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
									  	<tr v-show="restaurantOrdersToShow.length"
									    	v-for="(restaurantOrderRecord, index) in restaurantOrdersToShow"
									    	:key="restaurantOrderRecord.id" 
									    	:class="[cancelledOrder(restaurantOrderRecord) ? 'bg-secondary' :  servedOrder(restaurantOrderRecord) ? 'bg-success' : 'bg-danger']" 
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ restaurantOrderRecord.order_id }}</td>
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showOrderDetailModal(restaurantOrderRecord)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>
								      			<!-- disabled if waiter / restaurant already cancelled -->
								      			<!-- drop button -->
								      			<button 
									      			type="button" 
									      			class="btn btn-primary btn-sm" 
									      			v-if="!cancelledOrder(restaurantOrderRecord) && restaurantOrderReady(restaurantOrderRecord) && !servedOrder(restaurantOrderRecord)" 
									      			:disabled="formSubmitionMode" 
									      			@click="orderServingConfirmation(restaurantOrderRecord)" 
								      			>
								        			<i class="fas fa-bell"></i>
								        			Serve
								      			</button>
								      			
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!restaurantOrdersToShow.length"
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
								            <div class="form-group row" v-if="singleOrderData.order.asap || singleOrderData.order.scheduled">		
							              		<label class="col-sm-6 text-right">
							              			ASAP/Scheduled
							              		</label>
								                <div class="col-sm-6">
								                  	{{
								                  		singleOrderData.order.asap ?
								                  		'ASAP' : singleOrderData.order.scheduled.order_schedule
								                  	}}
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Cutlery
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.cutlery_added ? 'Added' : 'None' }}
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

								                	<ul v-show="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length">
														<li v-for="(orderedRestaurant, index) in singleOrderData.order.restaurants" 
															:key="orderedRestaurant.id" 
															style="list-style-type:none;" 
														>
															<ol v-show="orderedRestaurant.items.length">
																<li v-for="(item, index) in orderedRestaurant.items" 
																	:key="item.id"
																>	
																	{{ item.restaurant_menu_item.name }} 

																	<span class="d-block"
																		v-if="item.restaurant_menu_item.has_variation" 
																	>
																		(Selected Variation : {{ item.selected_item_variation.restaurant_menu_item_variation.variation.name }} )
																	</span>

																	(quantity : {{ item.quantity }})

																	<span 
																		class="d-block" 
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
															</ol>
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
		waiter : {
			waiter_id : null,
			restaurant_id : null,
			orderServed : false,
		}
	};

	var restaurantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	
    	restaurantOrdersToShow : [],
    	allOrdersToBeServed : [],
    	formSubmitionMode : false,

    	pagination: {
        	current_page: 1
      	},

      	singleOrderData : singleOrderData,

        csrf : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        // restaurant_id : document.querySelector('meta[name="restaurant-id"]').getAttribute('content'),
        waiter_id : 1,
        restaurant_id : 1,
    };

	export default {

	    data() {
	        return restaurantListData;
		},

		created(){
			this.fetchAllOrdersToBeServed();
		},

		mounted(){
			    
			Pusher.logToConsole = true;

			Echo.private(`notifyRestaurantWaiters.` + this.restaurant_id)
			.listen('.updation-for-waiters', (waiterOrderToServe) => {
			    
			    console.log(waiterOrderToServe);

			    // due to pagination, checking if this broadcasted one already exists 
			    const objectExist = (restaurantOrderRecord) => restaurantOrderRecord.order_id==waiterOrderToServe.order_id;

			    // if new delivery order in the list or nothing in the list
			    if ((!this.restaurantOrdersToShow.some(objectExist)) || (Array.isArray(this.restaurantOrdersToShow) && !this.restaurantOrdersToShow.length)) {
			    	
			    	this.restaurantOrdersToShow.unshift(waiterOrderToServe);
			    	toastr.info("New serve-order arrives");
			    }
			    // now showing the order in this page
			    else if (this.restaurantOrdersToShow.some(objectExist)) {
				    let index = this.restaurantOrdersToShow.findIndex(
				    		currentRestaurantOrder => (currentRestaurantOrder.order_id==waiterOrderToServe.order_id)
				    	);

				    Vue.set(this.restaurantOrdersToShow, index, waiterOrderToServe)
				    toastr.info("Old serve-order update arrives");
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
					.get('/api/waiter-orders/' + this.restaurant_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
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
				this.restaurantOrdersToShow = this.allOrdersToBeServed.all.data;
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
    		showOrderDetailModal(restaurantOrderRecord) {

				this.singleOrderData.order = restaurantOrderRecord.order;

				this.singleOrderData.order.restaurants = restaurantOrderRecord.ordered_restaurants;
				
				// if any food is picked
				if (this.restaurantOrderReady(restaurantOrderRecord)) {

					this.singleOrderData.order.orderer = restaurantOrderRecord.order.orderer;

				}

				$("#modal-show-order").modal("show");

			},

			orderServingConfirmation(restaurantOrderRecord){

				this.formSubmitionMode = true;

				this.singleOrderData.waiter = {};
				this.singleOrderData.waiter.orderServed = true;
				this.singleOrderData.waiter.waiter_id = this.waiter_id;
				this.singleOrderData.waiter.restaurant_id = this.restaurant_id;

				this.singleOrderData.order = restaurantOrderRecord.order;
				
				this.submitConfirmation();

			},

			submitConfirmation(){

				// console.log(this.singleOrderData.order);
				// console.log(this.singleOrderData.waiter);
				// return;
				
				$("#modal-acceptOrPickOrDrop-order").modal("hide");

				axios
					.post('/order-serve-confirmations/' + this.singleOrderData.order.id + '/' + this.perPage + '?page=' + this.pagination.current_page, this.singleOrderData.waiter)
					.then(response => {
						if (response.status == 200) {
							
							this.singleOrderData.order = {};
							this.singleOrderData.waiter = {};

							this.allOrdersToBeServed = response.data;
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
			cancelledOrder(restaurantOrderRecord) {

				if (restaurantOrderRecord.order_cancellation_reasons.length) {
					return true;
				}

				return false;

			},

			servedOrder(restaurantOrderRecord){
				
				if (restaurantOrderRecord.order_serve_progression) {
					return true;
				}

				return false;
					
			},

			restaurantOrderReady(restaurantOrderRecord) {

				if (restaurantOrderRecord.order_ready_confirmations.length) {
					return true;
				}

				return false;

			},

		}
  	}

</script>