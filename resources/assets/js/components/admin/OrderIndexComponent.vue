
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
													:class="[{ 'active': currentTab==='new' }, 'nav-link']" 
													@click="showNewOrders"
												>
													New
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab==='deliveredOrServed' }, 'nav-link']" 
													@click="showDeliveredOrServedOrders"
												>
													Delivered/Served
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab==='cancelled' }, 'nav-link']" 
													@click="showCancelledOrders"
												>
													Cancelled
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab==='prepaid' }, 'nav-link']" 
													@click="showPrePaidOrders"
												>
													Pre-paid
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab==='postpaid' }, 'nav-link']" 
													@click="showPostPaidOrders"
												>
													Post-paid
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
											<th scope="col">Status</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="ordersToShow.length"
									    	v-for="(order, index) in ordersToShow"
									    	:key="order.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ order.id }}</td>
								    		<td>
								    			<span :class="[orderCallConfirmationClass(order.call_confirmation), 'badge d-block']"
								    			>
								    				{{ 
								    					orderCallConfirmationStatus(order.call_confirmation)
								    				}}
								    			</span>

								    			<span 
								    				v-if="order.restaurant_acceptances.length" 
 								    				v-for="restaurantOrderAcceptance in order.restaurant_acceptances" 
								    				:class="[restaurantOrderAcceptanceClass(restaurantOrderAcceptance), 'badge d-block']"
								    			>
								    				<!-- {{ restaurantOrderAcceptance }} -->
								    				
								    				{{ 
								    					restaurantOrderAcceptanceStatus(restaurantOrderAcceptance)
								    				}}
								    			</span>

								    			<span 
								    				v-if="order.rider_assignment"
								    				:class="[order.rider_assignment ? 'badge-warning' : 'badge-danger', 'badge d-block']"
								    			>
								    				{{ 
								    					order.rider_assignment ? 'Rider Assigned' : 'Not-assigned' 
								    				}}
								    			</span>

								    			<span 
								    				v-if="order.order_ready_confirmations.length" 
								    				v-for="restaurantReadyConfirmation in order.order_ready_confirmations" 
								    				:class="[restaurantOrderReadyClass(restaurantReadyConfirmation), 'badge d-block']"
								    			>
								    				<!-- {{ restaurantReadyConfirmation }} -->

								    				{{ 
								    					restaurantOrderReadyStatus(restaurantReadyConfirmation)
								    				}}
								    			</span>

								    			<span 
								    				v-if="order.restaurant_order_cancelations.length" 
								    				v-for="restaurantOrderCancelation in order.restaurant_order_cancelations" 
								    				class="badge badge-secondary d-block"
								    			>
								    				{{
								    					restaurantOrderCancelation.restaurant.name + ' has cancelled'
								    				}}
								    			</span>

								    			<span 
								    				v-if="order.rider_food_pick_confirmations.length" 
									    			v-for="riderFoodPickUpConfirmation in order.rider_food_pick_confirmations" 
								    				:class="[riderFoodPickUpClass(riderFoodPickUpConfirmation), 'badge d-block']"
								    			>
								    				{{ 
								    					riderFoodPickUpStatus(riderFoodPickUpConfirmation)
								    				}}
								    			</span>
								    			
								    			<span 
								    				v-if="order.rider_order_cancelation" 
								    				class="badge badge-secondary d-block"
								    			>
								    				{{
								    					order.rider_order_cancelation.rider.name + ' has cancelled'
								    				}}
								    			</span>

								    			<span v-if="order.rider_delivery_confirmation" 
								    				:class="[order.rider_delivery_confirmation.rider_delivery_confirmation===1 ? 'badge-success' : 'badge-warning', 'badge d-block']"
								    			>
								    				{{ 
								    					riderFoodDeliveryStatus(order.rider_delivery_confirmation)
								    				}}
								    			</span>

								    			<span v-if="order.waiter_serve_confirmation"
								    				:class="[order.waiter_serve_confirmation.waiter_serve_confirmation===1 ? 'badge-success' : 'badge-secondary', 'badge d-block']"
								    			>
								    				{{ 
								    					waiterFoodServeStatus(order.waiter_serve_confirmation)
								    				}}
								    			</span>

								    		</td>
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-info btn-sm"
									      			@click="showOrderDetailModal(order)" 
								      			>
								        			<i class="fas fa-eye"></i>
								        			Details
								      			</button>
								      			<!-- show if customer is not confirmed and orderer is not waiter -->
								      			<button 
									      			type="button" 
									      			class="btn btn-success btn-sm" 
									      			v-if="order.call_confirmation===-1" 
									      			@click="showOrderConfirmationModal(order)" 
								      			>
								        			<i class="fas fa-check"></i>
								        			Confirm
								      			</button>
												<div class="btn-group">
													<!-- disabled if not even confirmed or already delivered or cancelled by restaurants in order-->
													<button 
														type="button" 
														class="btn btn-danger btn-sm dropdown-toggle" 
														data-toggle="dropdown" 
									      				:disabled="order.call_confirmation!==1 || (order.rider_delivery_confirmation && order.rider_delivery_confirmation.rider_delivery_confirmation===1) || (order.restaurant_order_cancelations.length && order.restaurant_order_cancelations.length===order.restaurant_acceptances.length)"
													>
														<i class="fas fa-times"></i>
														Cancel
													</button>
													<div class="dropdown-menu">
														<!-- disabled if no rider or not picked up -->
										      			<button 
											      			type="button" 
											      			class="btn btn-warning btn-sm dropdown-item" 
											      			@click="showRiderCancelationModal(order)" 
											      			:disabled="order.rider_assignment===null || order.rider_food_pick_confirmations.length>0"
										      			>
										        			<i class="fas fa-cancel"></i>
										        			Cancelled by Rider
										      			</button>
										      			<!-- disabled if no restaurant accepted the order -->
										      			<button 
											      			type="button" 
											      			class="btn btn-warning btn-sm dropdown-item" 
											      			@click="showRestaurantCancelationModal(order)" 
											      			:disabled="!order.restaurant_acceptances.length"
										      			>
										        			<i class="fas fa-cancel"></i>
										        			Cancelled by Restaurant
										      			</button>	
													</div>
												</div>
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
						  		Order Details
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true">&times;</span></button>
						</div>

						<div class="modal-body">
							<ul class="nav nav-tabs justify-content-center mb-4" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#order">
										Order
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#order-items">
										Items & Restaurants
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#delivery-info">
										Delivery Info
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
							              			Type 
							              		</label>

								                <div class="col-sm-6">
								                	{{ singleOrderData.order.order_type }}
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
							              			Payment type
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.payment_method }}
								                  	(Payment id : {{ singleOrderData.order.payment ? singleOrderData.order.payment.payment_id : 'NA' }})
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
								                  		singleOrderData.order.orderer.user_name : 'NA'  
													}}
													({{
														singleOrderData.order.orderer && singleOrderData.order.orderer.hasOwnProperty('restaurant_id') ? 
								                  		'Waiter' : 'Customer'
													}})
								                </div>	
								            </div>
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Order Status
							              		</label>
								                <div class="col-sm-6">
								                	<!-- <div v-show="order.payment_method==='cash'"> -->

									    			<span :class="[orderCallConfirmationClass(singleOrderData.order.call_confirmation), 'badge d-block']"
									    			>
									    				{{ 
									    					orderCallConfirmationStatus(singleOrderData.order.call_confirmation)
									    				}}
									    			</span>

									    			<span 
									    				v-if="singleOrderData.order.restaurant_acceptances.length" 
	 								    				v-for="restaurantOrderAcceptance in singleOrderData.order.restaurant_acceptances" 
									    				:class="[restaurantOrderAcceptanceClass(restaurantOrderAcceptance), 'badge d-block']"
									    			>
									    				<!-- {{ restaurantOrderAcceptance }} -->
									    				
									    				{{ 
									    					restaurantOrderAcceptanceStatus(restaurantOrderAcceptance)
									    				}}
									    			</span>

									    			<span 
									    				v-if="singleOrderData.order.rider_assignment"
									    				:class="[singleOrderData.order.rider_assignment ? 'badge-warning' : 'badge-danger', 'badge d-block']"
									    			>
									    				{{ 
									    					singleOrderData.order.rider_assignment ? 'Rider Assigned' : 'Not-assigned' 
									    				}}
									    			</span>

									    			<span 
									    				v-if="singleOrderData.order.order_ready_confirmations.length" 
									    				v-for="restaurantReadyConfirmation in singleOrderData.order.order_ready_confirmations" 
									    				:class="[restaurantOrderReadyClass(restaurantReadyConfirmation), 'badge d-block']"
									    			>
									    				<!-- {{ restaurantReadyConfirmation }} -->

									    				{{ 
									    					restaurantOrderReadyStatus(restaurantReadyConfirmation)
									    				}}
									    			</span>

									    			<span 
									    				v-if="singleOrderData.order.rider_food_pick_confirmations.length" 
										    			v-for="riderFoodPickUpConfirmation in singleOrderData.order.rider_food_pick_confirmations" 
									    				:class="[riderFoodPickUpClass(riderFoodPickUpConfirmation), 'badge d-block']"
									    			>
									    				{{ 
									    					riderFoodPickUpStatus(riderFoodPickUpConfirmation)
									    				}}
									    			</span>

									    			<span v-if="singleOrderData.order.rider_delivery_confirmation" 
									    				:class="[singleOrderData.order.rider_delivery_confirmation.rider_delivery_confirmation===1 ? 'badge-success' : 'badge-warning', 'badge d-block']"
									    			>
									    				{{ 
									    					riderFoodDeliveryStatus(order.rider_delivery_confirmation)
									    				}}
									    			</span>

									    			<span v-if="singleOrderData.order.waiter_serve_confirmation"
									    				:class="[singleOrderData.order.waiter_serve_confirmation.waiter_serve_confirmation===1 ? 'badge-success' : 'badge-secondary', 'badge d-block']"
									    			>
									    				{{ 
									    					waiterFoodServeStatus(singleOrderData.order.waiter_serve_confirmation)
									    				}}
									    			</span>
										    		
								                	<!-- </div> -->
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
								                	<ul v-show="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length">
														<li v-for="(orderedRestaurant, index) in singleOrderData.order.restaurants" 
															:key="orderedRestaurant.id"
														>
															{{ orderedRestaurant.restaurant.name }}

															<ol v-show="orderedRestaurant.items.length">
																<li v-for="(item, index) in orderedRestaurant.items" 
																	:key="item.id"
																>	
																	{{ item.restaurant_menu_item.name }}  
																	(quantity : {{ item.quantity }})
																</li>
															</ol>
														</li>
													</ul>
								                </div>	
								            </div>  
					            		</div>
					            	</div>
								</div>

								<div id="delivery-info" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Delivery Address
							              		</label>
								                <div class="col-sm-6">

								                	<br>

								                  	<address v-if="singleOrderData.order.delivery && singleOrderData.order.delivery.customer_address">

														<dl>
															
															<dd>-
																{{
																	singleOrderData.order.delivery.customer_address.house 
																}}
										                  	</dd>
															
															<dd>-
																{{ 
										                  			singleOrderData.order.delivery.customer_address.road
											                  	}}
															</dd>
															
															<dd v-if="singleOrderData.order.delivery.customer_address.additional_hint"> 
																{{ 
										                  			(singleOrderData.order.delivery.customer_address.additional_hint)
											                  	}}
															</dd>

															<dd>-
																{{ 
										                  			singleOrderData.order.delivery.customer_address.address_name
											                  	}}
															</dd>

															<dd>- 
																{{ 
										                  			singleOrderData.order.delivery.additional_info
											                  	}}
															</dd>
														</dl> 
									                  	
								                  	</address>

								                  	<address v-else>
								                  		<dl>
								                  			<dd>
								                  				Order for '{{ singleOrderData.order.order_type }}'
								                  			</dd>
								                  		</dl>
								                  	</address>
								                  	
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

			<!-- modal-confirmOrCancel-order -->
			<div class="modal fade" id="modal-confirmOrCancel-order">
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
														<a class="nav-link active" data-toggle="tab" href="#confirmation-orderer">
															Orderer
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#confirmation-order">
															Order Detail
														</a>
													</li>
												</ul>

												<!-- Tab panes -->
												<div class="tab-content">
													<div id="confirmation-orderer" class="container tab-pane active">
														<div class="row">
										            		<div class="col-sm-12">
										            			<div class="form-group row">		
												              		<label class="col-sm-6 text-right">
												              			Orderer Name
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
													            <div class="form-group row">		
												              		<label class="col-sm-6 text-right">
												              			Phone
												              		</label>
													                <div class="col-sm-6">
													                  	{{ 
													                  		singleOrderData.order.orderer ? 
													                  		singleOrderData.order.orderer.mobile : 'NA' 
													                  	}}
													                </div>	
													            </div>
													            <div class="form-group row">		
												              		<label class="col-sm-6 text-right">
												              			Email
												              		</label>
													                <div class="col-sm-6">
													                  	{{ 
													                  		singleOrderData.order.orderer ? 
													                  		singleOrderData.order.orderer.email : 'NA' 
													                  	}}
													                </div>	
													            </div>
													            <div class="form-group row">		
												              		<label class="col-sm-6 text-right">
												              			Joined on
												              		</label>
													                <div class="col-sm-6">
													                  	{{ 
													                  		singleOrderData.order.orderer ? 
													                  		singleOrderData.order.orderer.created_at : 'NA' 
													                  	}}
													                </div>	
													            </div>
										            		</div>
										            	</div>
													</div>

													<div id="confirmation-order" class="container tab-pane fade">
														<div class="row">
										            		<div class="col-sm-12">

										            			<div class="form-group row">		
												              		<label class="col-sm-6 text-right">
												              			Order id
												              		</label>
													                <div class="col-sm-6" >
													                  	{{ 
													                  		singleOrderData.order.id 
													                  	}}
													                </div>
													            </div>
													            <div class="form-group row">		
												              		<label class="col-sm-6 text-right">
												              			Type 
												              		</label>

													                <div class="col-sm-6">
													                	{{ 
													                		singleOrderData.order.order_type
													                  	}}
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
													                  	{{singleOrderData.order.discount}}
													                </div>	
													            </div>

													            <div class="form-group row">
												              		<label class="col-sm-6 text-right">
												              			Delivery-fee
												              		</label>
													                <div class="col-sm-6">
													                  	{{singleOrderData.order.delivery_fee}}
													                </div>
													            </div> 
													            <div class="form-group row">
												              		<label class="col-sm-6 text-right">
												              			Payable Price
												              		</label>
													                <div class="col-sm-6">
													                  	{{
													                  		singleOrderData.order.net_payable
													                  	}}
													                </div>	
													            </div>

													            <div class="form-group row">
												              		<label class="col-sm-6 text-right">
												              			Payment type
												              		</label>
													                <div class="col-sm-6">
													                  	{{ singleOrderData.order.payment_method }}
													                  	(Payment id : {{ singleOrderData.order.payment ? singleOrderData.order.payment.payment_id : 'NA' }})
													                </div>	
													            </div> 
													            <div class="form-group row">
												              		<label class="col-sm-6 text-right">
												              			Cutlery
												              		</label>
													                <div class="col-sm-6">
													                  	{{ singleOrderData.order.cutlery_addition ? 'Added' : 'No' }}
													                </div>	
													            </div> 

													            <div class="form-group row">
												              		<label class="col-sm-6 text-right">
												              			Order Items
												              		</label>
													                <div class="col-sm-6">
																	
													                	<ul v-show="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length">
																			<li v-for="(orderedRestaurant, index) in singleOrderData.order.restaurants" 
																				:key="orderedRestaurant.id"
																			>
																				{{ orderedRestaurant.restaurant.name }}

																				<ol v-show="orderedRestaurant.items.length">
																					<li v-for="(item, index) in orderedRestaurant.items" 
																						:key="item.id"
																					>	
																						{{ item.restaurant_menu_item.name }}  
																						(quantity:{{ item.quantity }})
																					</li>
																				</ol>
																			</li>
																		</ul>
																		
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
							<div class="modal-footer justify-content-between">
							  	<button 
							  		type="submit" 
							  		name="cancel-order" 
							  		@click="orderConfirming=false" 
							  		class="btn btn-outline-danger float-right"
							  	>
							  		Cancel-Order
							  	</button>

							  	<button 
							  		type="submit" 
							  		name="confirm-order" 
							  		@click="orderConfirming=true" 
							  		class="btn btn-outline-success float-right"
							  	>
							  		Confirm-Order
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-confirmOrCancel-order -->



			<!-- /.modal-restaurantOrRider-orderCancelation -->
			<div class="modal fade" id="modal-restaurantOrRider-orderCancelation">
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
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="cancelOrder()"
						  	autocomplete="off"
					  	>
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
				              			Cancelled By
				              		</label>

					                <div class="col-sm-8"> 

					                	<select 
											class="form-control" 
											v-model="singleOrderData.orderCancelation.cancelledBy" 
											@change="errors.orderCancelation.cancelledBy=null;submitCancelationForm = true;" 
										>
											<option :value="null" selected="true" disabled="true">
												Pick Cancelling Entity 
											</option>
											<option 
												v-for="cancelledBy in ['Restaurants', 'Rider']" 
												:value="cancelledBy" 
											>
												{{ cancelledBy }}
											</option>
										</select>

					                	<div class="text-danger" v-if="errors.orderCancelation.cancelledBy">
								        	{{ errors.orderCancelation.cancelledBy }}
								  		</div>
					                </div>	
				              	</div>

				              	<div 
				              		class="form-group row" 
				              		v-if="singleOrderData.orderCancelation.cancelledBy==='Restaurants'"
				              	>	
				              		<label 
				              			for="inputMenuName3" 
				              			class="col-sm-4 col-form-label text-right"
				              		>
				              			Restaurant Name
				              		</label>
					                <div class="col-sm-8">

										<select 
											class="form-control" 
											v-model="singleOrderData.orderCancelation.restaurant_id" 
											@change="errors.orderCancelation.restaurant=null;submitCancelationForm = true;" 
										>
											<option :value="null" selected="true" disabled="true">
												Select Restaurant Name 
											</option>
											<option 
												v-if="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length" 
												v-for="orderedRestaurant in singleOrderData.order.restaurants" 
												:value="orderedRestaurant.restaurant.id" 
											>
												{{ orderedRestaurant.restaurant.name }}
											</option>
										</select>

					                	<div class="text-danger" v-if="errors.orderCancelation.restaurant">
								        	{{ errors.orderCancelation.restaurant }}
								  		</div>
					                </div>
				              	</div>

				              	<div class="form-group row">
					              		
				              		<label 
				              			for="inputMenuName3" 
				              			class="col-sm-4 col-form-label text-right"
				              		>
				              			Cancelation Reason
				              		</label>

					                <div class="col-sm-8">

	                                	<select 
											class="form-control" 
											v-model="singleOrderData.orderCancelation.reason_id" 
											@change="errors.orderCancelation.reason=null;submitCancelationForm=true;" 
										>
											<option :value="null" selected="true" disabled="true">
												Select Reason 
											</option>
											<option 
												v-if="allCancelationReasons.length" 
												v-for="cancelationReason in allCancelationReasons" 
												:value="cancelationReason.id" 
											>
												<span v-html="cancelationReason.reason"></span>
											</option>
										</select>

					                	<div class="text-danger" v-if="errors.orderCancelation.reason">
								        	{{ errors.orderCancelation.reason }}
								  		</div>
					                </div>	
					              	
				              	</div>
							</div>
							<div class="modal-footer justify-content-between">
							<!-- 
								<div class="col-sm-12 text-right">
									<span class="text-danger p-0 m-0 small" v-show="!submitCancelationForm">
								  		Please input all required fields
								  	</span>
								</div> 
							-->
							  	<button 
								  	type="button" 
								  	class="btn btn-outline-light" 
								  	data-dismiss="modal"
							  	>
							  		Close
							  	</button>

							  	<button 
							  		type="submit" 
							  		class="btn btn-outline-light"
							  		:disabled="!submitCancelationForm" 
							  	>
							  		Cancel-Order
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-restaurantOrRider-orderCancelation-->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleOrderData = {
		order : {

		},
		orderCancelation : {
			reason : {},
			// restaurant : {},
			reason_id : null,
			restaurant_id : null,
			cancelledBy : 'Customer',
		}
	};

	var restaurantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitCancelationForm : true,
    	
    	allOrders : [],
    	ordersToShow : [],
    	allCancelationReasons : [],

    	currentTab : 'all',

    	orderConfirming : true,

    	pagination: {
        	current_page: 1
      	},

      	singleOrderData : singleOrderData,

      	errors : {
      		orderCancelation : {
      			reason : null,
      			restaurant : null,
      			cancelledBy : null,
      		},
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    // Local registration of components
		components: { 
			// VueBootstrap4Table
			Multiselect, // short form of Multiselect : Multiselect
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

			Echo.private(`notifyAdmin`)
			.listen('UpdateAdmin', (order) => {
			    
			    console.log(order);

			    // due to pagination, checking if this broadcasted one already exists 
			    const objectExist = (element) => element.id===order.id;

			    // new order and not in the list or nothing in the list
			    if ((order.call_confirmation===-1 && !this.ordersToShow.some(objectExist)) || (Array.isArray(this.ordersToShow) && !this.ordersToShow.length)) {
			    	this.ordersToShow.unshift(order);
			    	toastr.warning("New Order update arrives");
			    }
			    // now showing the order in this page
			    else if (this.ordersToShow.some(objectExist)) {
				    let index = this.ordersToShow.findIndex(currentObject => currentObject.id===order.id);
				    this.ordersToShow[index] = order;
				    toastr.warning("Old Order update arrives");
			    }
			    // no previous order available
			    else {
			    	toastr.warning("Else Order update arrives");
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

		methods : {
			showAllOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'all';
				this.showListDataForSelectedTab();
			},
			showNewOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'new';
				this.showListDataForSelectedTab();
			},
			showDeliveredOrServedOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'deliveredOrServed';
				this.showListDataForSelectedTab();
			},
			showCancelledOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'cancelled';
				this.showListDataForSelectedTab();
			},
			showPrePaidOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'prepaid';
				this.showListDataForSelectedTab();
			},
			showPostPaidOrders(){
				this.pagination.current_page = 1;
				this.fetchAllOrders();
				this.currentTab = 'postpaid';
				this.showListDataForSelectedTab();
			},
			showListDataForSelectedTab(){
				if (this.currentTab=='all') {
					this.ordersToShow = this.allOrders.all.data;
					this.pagination = this.allOrders.all;
				}else if (this.currentTab=='new') {
					this.ordersToShow = this.allOrders.new.data;
					this.pagination = this.allOrders.new;
				}else if (this.currentTab=='deliveredOrServed') {
					this.ordersToShow = this.allOrders.deliveredOrServed.data;
					this.pagination = this.allOrders.deliveredOrServed;
				}else if (this.currentTab=='cancelled') {
					this.ordersToShow = this.allOrders.cancelled.data;
					this.pagination = this.allOrders.cancelled;
				}else if (this.currentTab=='prepaid') {
					this.ordersToShow = this.allOrders.prepaid.data;
					this.pagination = this.allOrders.prepaid;
				}
				else {
					this.ordersToShow = this.allOrders.postpaid.data;
					this.pagination = this.allOrders.postpaid;
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
					.get('/api/orders/' + this.perPage +'?page='+ this.pagination.current_page)
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
    		fetchExpectedOrderDetail(order){
    			axios
					.get('/orders/'+ order.id +'/show')
					.then(response => {		
						if (response.status == 200) {
							this.singleOrderData.order = response.data.expectedOrder;
							// console.log(this.singleOrderData.order);
						}
					})
					.catch(error => {
						console.log(error);
					});
    		},
    		showOrderDetailModal(order) {
				this.fetchExpectedOrderDetail(order);
				$("#modal-show-order").modal("show");
			},
			showOrderConfirmationModal(order) {
				this.fetchExpectedOrderDetail(order);
				$("#modal-confirmOrCancel-order").modal("show");
			},
			submitConfirmation(){
				if (this.orderConfirming) {
					this.confirmOrder();
				}else {
					this.singleOrderData.orderCancelation.cancelledBy = 'Customer';
					this.cancelOrder();
				}
			},
			confirmOrder(){
				
				$("#modal-confirmOrCancel-order").modal("hide");
				
				axios
					.post('/orders/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.order)
					.then(response => {
						if (response.status == 200) {
							
							this.singleOrderData.order = {};

							this.allOrders = response.data;
							this.showListDataForSelectedTab();

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
			showRiderCancelationModal(order) {
				this.fetchExpectedOrderDetail(order);
				console.log(this.singleOrderData.order);
				this.singleOrderData.orderCancelation.cancelledBy = 'Rider';
				$("#modal-restaurantOrRider-orderCancelation").modal("show");
			},
			showRestaurantCancelationModal(order) {
				this.fetchExpectedOrderDetail(order);
				console.log(this.singleOrderData.order);
				this.singleOrderData.orderCancelation.cancelledBy = 'Restaurants';
				$("#modal-restaurantOrRider-orderCancelation").modal("show");
			},
			cancelOrder(){

				if (this.singleOrderData.orderCancelation.cancelledBy==='Restaurants' || this.singleOrderData.orderCancelation.cancelledBy === 'Rider') {

					if (this.singleOrderData.orderCancelation.cancelledBy==='Restaurants' && !this.singleOrderData.orderCancelation.restaurant_id) {	

						this.errors.orderCancelation.restaurant = 'Restaurant name is required';
						this.submitCancelationForm = false;
						return;

					}
					else if (!this.singleOrderData.orderCancelation.reason_id) {

						this.errors.orderCancelation.reason = 'Reason is required';
						this.submitCancelationForm = false;
						return;
						
					}

					$("#modal-restaurantOrRider-orderCancelation").modal("hide");

				}
				
				$("#modal-confirmOrCancel-order").modal("hide");
				
				axios
					.put('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.orderCancelation)
					.then(response => {
						if (response.status == 200) {
							
							this.singleOrderData.order = {};

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
			orderCallConfirmationClass(callConfirmation) {

				if (callConfirmation===1) {
					return 'badge-success';
				}else if (callConfirmation===-1) {
					return 'badge-danger';
				}else
					return 'badge-secondary';

			},
			orderCallConfirmationStatus(orderCallConfirmation) {
				if (orderCallConfirmation===0) {
					return 'Cancelled from customer';
				}else if (orderCallConfirmation===1) {
					return 'Confirmed from customer';
				}else
					return 'Not confirmed yet';
			},
			restaurantOrderAcceptanceClass(restaurantOrderAcceptance) {
				if (restaurantOrderAcceptance.food_order_acceptance===1) {
					return 'badge-warning';
				}else if (restaurantOrderAcceptance.food_order_acceptance===-1) {
					return 'badge-danger';
				}else
					return 'badge-secondary';
			},
			restaurantOrderAcceptanceStatus(restaurantOrderAcceptance) {
				if (restaurantOrderAcceptance.food_order_acceptance===-1) {
					return restaurantOrderAcceptance.restaurant.name +' is Ringing';
				}else if (restaurantOrderAcceptance.food_order_acceptance===1) {
					return restaurantOrderAcceptance.restaurant.name +' has Accepted';
				}else
					return restaurantOrderAcceptance.restaurant.name +' has Cancelled';
					// return 'Cancelled';
			},
			restaurantOrderReadyClass(restaurantReadyConfirmation) {
				if (restaurantReadyConfirmation.food_ready_confirmation===1) {
					return 'badge-success';
				}else if (restaurantReadyConfirmation.food_ready_confirmation===-1) {
					return 'badge-warning';
				}else
					return 'badge-secondary';
			},
			//  cancelled by restaurant
			restaurantOrderReadyStatus(restaurantReadyConfirmation) {
				if (restaurantReadyConfirmation.food_ready_confirmation===-1) {
					return restaurantReadyConfirmation.restaurant.name +' Not ready yet';
				}else if (restaurantReadyConfirmation.food_ready_confirmation===1) {
					return restaurantReadyConfirmation.restaurant.name +' is ready';
				}else
					return restaurantReadyConfirmation.restaurant.name +' has cancelled';
					// return 'Cancelled';
			},
			riderFoodPickUpClass(riderFoodPickUpConfirmation) {
				if (riderFoodPickUpConfirmation.rider_food_pick_confirmation===1) {
					return 'badge-warning';
				}else if (riderFoodPickUpConfirmation.rider_food_pick_confirmation===-1) {
					return 'badge-danger';
				}else
					return 'badge-secondary';
			},
			//  cancelled by rider
			riderFoodPickUpStatus(riderFoodPickUpConfirmation) {
				if (riderFoodPickUpConfirmation.rider_food_pick_confirmation===-1) {
					return riderFoodPickUpConfirmation.rider.user_name +' not picked-up yet from '+riderFoodPickUpConfirmation.restaurant.name;
				}else if (riderFoodPickUpConfirmation.rider_food_pick_confirmation===1) {
					return riderFoodPickUpConfirmation.rider.user_name +' picked-up from '+riderFoodPickUpConfirmation.restaurant.name;
				}else
					return riderFoodPickUpConfirmation.rider.user_name +' has cancelled order of '+riderFoodPickUpConfirmation.restaurant.name;
			},
			riderFoodDeliveryStatus(riderDeliveryConfirmation) {
				if (riderDeliveryConfirmation.rider_delivery_confirmation===1) {
					return 'Delivered by '+riderDeliveryConfirmation.rider.user_name;
				}else if (riderDeliveryConfirmation.rider_delivery_confirmation===-1) {
					return riderDeliveryConfirmation.rider.user_name+' is on the way';
				}else if (riderDeliveryConfirmation.rider_delivery_confirmation===2) {
					return 'Dropped at office by '+riderDeliveryConfirmation.rider.user_name;
				}
				else
					return 'No response';
			},
			waiterFoodServeStatus(waiterServeConfirmation) {
				if (waiterServeConfirmation.waiter_serve_confirmation===1) {
					return 'Served';
				}else if (waiterServeConfirmation.waiter_serve_confirmation===-1) {
					'Not served yet ';
				}
				else
					return 'Cancelled';
			},
			
		}
  	}

</script>

<style scoped>

	@import '~vue-multiselect/dist/vue-multiselect.min.css';

</style>