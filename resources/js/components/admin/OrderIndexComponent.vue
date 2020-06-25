
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
								    			<span :class="[order.call_confirmation===1 ? 'badge-warning' : order.call_confirmation===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
								    			>
								    				{{ 
								    					customerCallConfirmation(order.call_confirmation)
								    				}}
								    			</span>

								    			<span v-for="restaurantOrderAcceptance in order.restaurant_acceptances" :key="restaurantOrderAcceptance.id"
								    				:class="[restaurantOrderAcceptance.food_order_acceptance===1 ? 'badge-warning' : restaurantOrderAcceptance.food_order_acceptance===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
								    			>
								    				{{ 
								    					restaurantOrderStatus(restaurantOrderAcceptance)
								    				}}
								    			</span>

								    			<span v-if="order.rider_assignment"
								    				:class="[order.rider_assignment ? 'badge-warning' : 'badge-danger', 'badge']"
								    			>
								    				{{ 
								    					order.rider_assignment ? 'Rider Assigned' : 'Not-assigned' 
								    				}}
								    			</span>

								    			<span v-for="restaurantReadyConfirmation in order.order_ready_confirmations" :key="restaurantReadyConfirmation.id"
								    				:class="[restaurantReadyConfirmation.food_ready_confirmation===1 ? 'badge-warning' : restaurantReadyConfirmation.food_ready_confirmation===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
								    			>
								    				{{ 
								    					restaurantReadyConfirmation(restaurantReadyConfirmation)
								    				}}
								    			</span>

								    			<span v-for="riderFoodPickUpConfirmation in order.rider_food_pick_confirmations" :key="riderFoodPickUpConfirmation.id"
								    				:class="[riderFoodPickUpConfirmation.rider_food_pick_confirmation===1 ? 'badge-warning' : riderFoodPickUpConfirmation.rider_food_pick_confirmation===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
								    			>
								    				{{ 
								    					riderFoodPickUpConfirmation(riderFoodPickUpConfirmation)
								    				}}
								    			</span>

								    			<span v-if="order.rider_delivery_confirmation"
								    				:class="[order.rider_delivery_confirmation.rider_delivery_confirmation===1 ? 'badge-success' : 'badge-secondary', 'badge']"
								    			>
								    				{{ 
								    					riderDeliveryConfirmation(order.rider_delivery_confirmation)
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
								      			<!-- disabled if customer is already confirmed/cancelled or orderer is waiter -->
								      			<button 
									      			type="button" 
									      			class="btn btn-success btn-sm" 
									      			:disabled="order.call_confirmation!==-1 || order.orderer.hasOwnProperty('restaurant_id')" 
									      			@click="showOrderConfirmationModal(order)" 
								      			>
								        			<i class="fas fa-check"></i>
								        			Confirm
								      			</button>
												<div class="btn-group">
													<!-- disabled if not even confirmed or already delivered -->
													<button 
														type="button" 
														class="btn btn-danger btn-sm dropdown-toggle" 
														data-toggle="dropdown" 
									      				:disabled="order.call_confirmation!==1 || (order.rider_delivery_confirmation!==null && order.rider_delivery_confirmation.rider_delivery_confirmation===1)"
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

									    			<span :class="[singleOrderData.order.call_confirmation===1 ? 'badge-warning' : singleOrderData.order.call_confirmation===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
									    			>
									    				{{ 
									    					customerCallConfirmation(singleOrderData.order.call_confirmation)
									    				}}
									    			</span>

									    			<span v-for="restaurantOrderAcceptance in singleOrderData.order.restaurant_acceptances" :key="restaurantOrderAcceptance.id"
									    				:class="[restaurantOrderAcceptance.food_order_acceptance===1 ? 'badge-warning' : restaurantOrderAcceptance.food_order_acceptance===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
									    			>
									    				{{ 
									    					restaurantOrderStatus(restaurantOrderAcceptance)
									    				}}
									    			</span>

									    			<span v-if="singleOrderData.order.rider_assignment"
									    				:class="[singleOrderData.order.rider_assignment ? 'badge-warning' : 'badge-danger', 'badge']"
									    			>
									    				{{ 
									    					singleOrderData.order.rider_assignment ? 'Rider Assigned' : 'Not-assigned' 
									    				}}
									    			</span>

									    			<span v-for="restaurantReadyConfirmation in singleOrderData.order.order_ready_confirmations" :key="restaurantReadyConfirmation.id"
									    				:class="[restaurantReadyConfirmation.food_ready_confirmation===1 ? 'badge-warning' : restaurantReadyConfirmation.food_ready_confirmation===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
									    			>
									    				{{ 
									    					restaurantReadyConfirmation(restaurantReadyConfirmation)
									    				}}
									    			</span>

									    			<span v-for="riderFoodPickUpConfirmation in singleOrderData.order.rider_food_pick_confirmations" :key="riderFoodPickUpConfirmation.id"
									    				:class="[riderFoodPickUpConfirmation.rider_food_pick_confirmation===1 ? 'badge-warning' : riderFoodPickUpConfirmation.rider_food_pick_confirmation===-1 ? 'badge-danger' : 'badge-secondary', 'badge']"
									    			>
									    				{{ 
									    					riderFoodPickUpConfirmation(riderFoodPickUpConfirmation)
									    				}}
									    			</span>

									    			<span v-if="singleOrderData.order.rider_delivery_confirmation"
									    				:class="[singleOrderData.order.rider_delivery_confirmation.rider_delivery_confirmation===1 ? 'badge-success' : 'badge-secondary', 'badge']"
									    			>
									    				{{ 
									    					riderDeliveryConfirmation(singleOrderData.order.rider_delivery_confirmation)
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
							  		class="btn btn-outline-warning float-right"
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
					<div class="modal-content bg-danger">
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

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Cancelled By
								              		</label>

									                <div class="col-sm-8"> 	
									                  	<multiselect 
									                  	v-model="singleOrderData.orderCancelation.cancelledBy" 
									                  	:options="['Restaurants', 'Rider']" 
									                  	:required="true" 
									                  	:allow-empty="false"
									                  	:show-labels="false" 
									                  	placeholder="Pick an entity"
									                  	@close="validateFormInput('orderCancelation.cancelledBy')"
									                  	:class="!errors.orderCancelation.cancelledBy ? 'is-valid' : 'is-invalid'"
									                  	>
									                  		
									                  	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.orderCancelation.cancelledBy 
												        	}}
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
									                  	<multiselect 
				                                  			v-model="singleOrderData.orderCancelation.restaurant"
					                                  		track-by="restaurant_id" 
					                                  		:show-labels="false"
					                                  		:custom-label="customLabel" 
					                                  		:options="singleOrderData.order.restaurants" 
					                                  		:required="true"
					                                  		:class="!errors.orderCancelation.restaurant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
				                                  			placeholder="Restaurant Name" 
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('orderCancelation.restaurant')"
				                                  		>
					                                	</multiselect>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.orderCancelation.restaurant 
												        	}}
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
									                  	
									                  	<multiselect 
				                                  			v-model="singleOrderData.orderCancelation.reason"
				                                  			placeholder="Meal Names" 
					                                  		label="reason" 
					                                  		track-by="id" 
					                                  		:options="allCancelationReasons" 
					                                  		:required="true" 
					                                  		:multiple="true" 
					                                  		:class="!errors.orderCancelation.reason ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('orderCancelation.reason')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.orderCancelation.reason }}
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
								<div class="col-sm-12 text-right">
									<span class="text-danger p-0 m-0 small" v-show="!submitCancelationForm">
								  		Please input all required fields
								  	</span>
								</div>
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
							  		Order Cancelled by {{ singleOrderData.orderCancelation.cancelledBy }}
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
			restaurant : {},
			reason_id : null,
			cancelledBy : 'Customer',
			restaurant_id : null,
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
			.listen('NewOrderArrival', (e) => {

			    console.log(e);
			    this.allOrders.new.data.unshift(e);
			    this.showListDataForSelectedTab();
				// this.pagination = this.allOrders.new;

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

			'singleOrderData.orderCancelation.reason' : function(val){
				if (val) {
					this.singleOrderData.orderCancelation.reason_id = val.id;
				}
				this.singleOrderData.orderCancelation.reason_id = null;
			},

			'singleOrderData.orderCancelation.restaurant' : function(val){
				if (val) {
					this.singleOrderData.orderCancelation.restaurant_id = val.restaurant_id;
				}
				this.singleOrderData.orderCancelation.restaurant_id = null;
			},
		},

		filters: {

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
    		showOrderDetailModal(order) {
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
				
				$("#modal-show-order").modal("show");
			},
			showOrderConfirmationModal(order) {
				axios
					.get('/orders/'+ order.id +'/show')
					.then(response => {		
						if (response.status == 200) {
							this.singleOrderData.order = response.data.expectedOrder;
						}
					})
					.catch(error => {
						console.log(error);
					});

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
					.put('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.order)
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
				axios
					.get('/orders/'+ order.id +'/show')
					.then(response => {		
						if (response.status == 200) {
							this.singleOrderData.order = response.data.expectedOrder;
						}
					})
					.catch(error => {
						console.log(error);
					});

				this.singleOrderData.orderCancelation.cancelledBy = 'Rider';
				$("#modal-restaurantOrRider-orderCancelation").modal("show");
			},
			showRestaurantCancelationModal(order) {
				axios
					.get('/orders/'+ order.id +'/show')
					.then(response => {		
						if (response.status == 200) {
							this.singleOrderData.order = response.data.expectedOrder;
						}
					})
					.catch(error => {
						console.log(error);
					});

				this.singleOrderData.orderCancelation.cancelledBy = 'Restaurants';
				$("#modal-restaurantOrRider-orderCancelation").modal("show");
			},
			cancelOrder(){

				if (this.singleOrderData.orderCancelation.cancelledBy === 'Restaurants' || this.singleOrderData.orderCancelation.cancelledBy === 'Rider') {

					if (!this.singleOrderData.orderCancelation.cancelledBy || (this.singleOrderData.orderCancelation.cancelledBy === 'Restaurants' && Object.keys(this.singleOrderData.orderCancelation.restaurant).length === 0) || Object.keys(this.singleOrderData.orderCancelation.reason).length === 0) {
						
						this.submitCancelationForm = false;
						return;
					}

					$("#modal-restaurantOrRider-orderCancelation").modal("hide");
				}

				
				$("#modal-confirmOrCancel-order").modal("hide");
				
				axios
					.patch('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.orderCancelation)
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
			customerCallConfirmation(customerCallConfirmation) {
				if (customerCallConfirmation===0) {
					return 'Cancelled by customer';
				}else if (customerCallConfirmation===1) {
					return 'Confirmed by customer';
				}else
					return 'Not confirmed yet';
			},
			restaurantOrderStatus(restaurantOrderAcceptance) {
				if (restaurantOrderAcceptance.food_order_acceptance===-1) {
					return restaurantOrderAcceptance.restaurant.name +' is Ringing';
				}else if (restaurantOrderAcceptance.food_order_acceptance===1) {
					return restaurantOrderAcceptance.restaurant.name +' has Accepted';
				}else
					return restaurantOrderAcceptance.restaurant.name +' has Cancelled';
			},
			//  cancelled by restaurant
			restaurantReadyConfirmation(restaurantReadyConfirmation) {
				if (restaurantReadyConfirmation.food_ready_confirmation===-1) {
					return restaurantReadyConfirmation.restaurant.name +' Not ready yet';
				}else if (restaurantReadyConfirmation.food_ready_confirmation===1) {
					return restaurantReadyConfirmation.restaurant.name +' is Ready';
				}else
					return restaurantReadyConfirmation.restaurant.name +' has Cancelled';
			},
			//  cancelled by rider
			riderFoodPickUpConfirmation(riderFoodPickUpConfirmation) {
				if (riderFoodPickUpConfirmation.rider_food_pick_confirmation===-1) {
					return riderFoodPickUpConfirmation.rider.user_name +' not picked-up yet from '+riderFoodPickUpConfirmation.restaurant.name;
				}else if (riderFoodPickUpConfirmation.rider_food_pick_confirmation===1) {
					return riderFoodPickUpConfirmation.rider.user_name +' picked-up from '+riderFoodPickUpConfirmation.restaurant.name;
				}else
					return riderFoodPickUpConfirmation.rider.user_name +' has cancelled order of '+riderFoodPickUpConfirmation.restaurant.name;
			},
			riderDeliveryConfirmation(riderDeliveryConfirmation) {
				if (riderDeliveryConfirmation.rider_delivery_confirmation===1) {
					return 'Delivered by '+riderDeliveryConfirmation.rider.user_name;
				}else if (riderDeliveryConfirmation.rider_delivery_confirmation===-1) {
					'Not delivered yet by '+riderDeliveryConfirmation.rider.user_name;
				}else if (riderDeliveryConfirmation.rider_delivery_confirmation===2) {
					'Dropped at office by '+riderDeliveryConfirmation.rider.user_name;
				}
				else
					return 'Cancelled by agents';
			},
			validateFormInput(formInputName)
			{
				this.submitCancelationForm = false;

				switch(formInputName) {

					case 'orderCancelation.cancelledBy' :

						if (!this.singleOrderData.orderCancelation.cancelledBy) {
							this.errors.orderCancelation.cancelledBy = 'This field is required';
						}
						else {
							this.submitCancelationForm = true;
							this.$delete(this.errors.orderCancelation, 'cancelledBy');
						}

						break;

					case 'orderCancelation.restaurant' :

						if (Object.keys(this.singleOrderData.orderCancelation.restaurant).length === 0) {
							this.errors.orderCancelation.restaurant = 'Restaurant Name is required';
						}
						else {
							this.submitCancelationForm = true;
							this.$delete(this.errors.orderCancelation, 'restaurant');
						}

						break;

					case 'orderCancelation.reason' :

						if (Object.keys(this.singleOrderData.orderCancelation.reason).length === 0) {
							this.errors.orderCancelation.reason = 'Reason is required';
						}
						else {
							this.submitCancelationForm = true;
							this.$delete(this.errors.orderCancelation, 'reason');
						}

						break;

				}

			},
			customLabel ({ restaurants }) {
				return `${restaurants.name}`;
			}
		}
  	}

</script>

<style scoped>

	@import '~vue-multiselect/dist/vue-multiselect.min.css';

</style>