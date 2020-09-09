
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
										Orders List
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
											<th scope="col">Order Id</th>
											<th scope="col">Type</th>
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
								    		<td>{{ order.order_type | capitalize }}</td>
								    		<td>
												<!-- 
													no option should be shown without picking/cancelling every restaurant orders 
												-->
								    			<span 
								    				v-if="deliveredOrServedOrder(order)" 
								    				class="badge badge-success d-block"
								    			>	
								    				{{ 
								    					deliveredOrServedOrder(order)
								    				}}
								    			</span>

								    			<!-- 
								    				if rider has been assigned (riderFoodPickConfirmations is auto set after assignment)  
								    				// for each restaurants in order
								    				// after call confirmation
								    			-->
								    			<span 
								    				v-else 
								    				v-for="(restaurantOrderRecord, index) in order.restaurant_acceptances" 
								    				:class="[orderProgressionClass(order, index), 'badge d-block']"
								    			>	
								    				{{ 
								    					secondaryOrder(order, index)
								    				}}
								    			</span>

							    				<!-- 
							    					new order before call confirmation
							    				-->
								    			<span 
								    				v-if="initialOrder(order)" 
								    				:class="[initialOrderClass(order), 'badge d-block']"
								    			>	
								    				{{ initialOrder(order) }}
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
						  		{{ defineOrderType(singleOrderData.order) }} Details
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    <span aria-hidden="true">&times;</span></button>
						</div>


						<div class="modal-body">

							<div class="progress mb-4">

								<!-- 
			    					new order before call confirmation
			    				-->
			    				<div 
									class="progress-bar progress-bar-striped progress-bar-animated" 
									style="width:25%" 
									v-if="initialOrderStatus(singleOrderData.order)" 
				    				:class="[initialOrderClass(singleOrderData.order)]"
								>
									{{ initialOrderStatus(singleOrderData.order) }}
								</div>
								
				    			<!-- 
				    				if rider has been assigned (riderFoodPickConfirmations is auto set after assignment)  
				    				// for each restaurants in order
				    				// after call confirmation
				    			-->
				    			<div 
				    				class="progress-bar progress-bar-striped progress-bar-animated" 
				    				v-if="singleOrderData.order.restaurant_acceptances.length" 
				    				v-for="(restaurantOrderRecord, index) in singleOrderData.order.restaurant_acceptances" 
				    				:class="[orderProgressionClass(singleOrderData.order, index)]" 
									:style="{ width: (60/singleOrderData.order.restaurant_acceptances.length) + '%' }"
								>
									{{ 
				    					secondaryOrder(singleOrderData.order, index)
				    				}}
								</div>

								<!-- 
									no option should be shown without picking/cancelling every restaurant orders 
								-->
								<div 
				    				class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
									style="width:15%"
									v-if="deliveredOrServedOrder(singleOrderData.order)" 
								>
									{{ 
				    					deliveredOrServedOrder(singleOrderData.order)
				    				}}
								</div>

							</div>

							<ul class="nav nav-tabs justify-content-center mb-4" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" href="#orderer">
										Orderer
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#order">
										Order
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" href="#order-items">
										Menu-Items
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

								<div id="orderer" class="container tab-pane active">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Orderer Name
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

								<div id="order" class="container tab-pane">
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
							              			Payment type
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.payment_method | capitalize }}
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
							              			Order Status
							              		</label>
								                <div class="col-sm-6">
								                	<!-- <div v-show="order.payment_method==='cash'"> -->

									    			<span :class="[orderCallConfirmationClass(singleOrderData.order), 'badge d-block']"
									    			>
									    				{{ 
									    					orderCallConfirmationStatus(singleOrderData.order)
									    				}}
									    			</span>

									    			<span 
									    				v-if="singleOrderData.order.restaurant_acceptances.length" 
	 								    				v-for="restaurantOrderRecord in singleOrderData.order.restaurant_acceptances" 
									    				:class="[restaurantOrderAcceptanceClass(restaurantOrderRecord), 'badge d-block']"
									    			>	
									    				{{ 
									    					restaurantOrderAcceptanceStatus(restaurantOrderRecord)
									    				}}
									    			</span>

									    			<span 
									    				v-if="riderAssigned(singleOrderData.order)"
									    				:class="[riderAssigned(singleOrderData.order) ? 'badge-warning' : 'badge-danger', 'badge d-block']"
									    			>
									    				{{ 
									    					riderAssigned(singleOrderData.order) ? 'Rider Assigned' : 'Not-assigned' 
									    				}}
									    			</span>

									    			<span 
									    				v-if="singleOrderData.order.order_ready_confirmations.length" 
									    				v-for="restaurantReadyConfirmation in singleOrderData.order.order_ready_confirmations" 
									    				:class="[restaurantReadyConfirmation.food_ready_confirmation==1 ? 'badge-success' : 'badge-primary', 'badge d-block']"
									    			>
									    				{{ restaurantReadyConfirmation.restaurant.name }}

									    				{{ 
									    					restaurantReadyConfirmation.food_ready_confirmation==1 ? ' is ready' : ' aint ready yet'
									    				}}
									    			</span>

									    			<span
									    				v-if="singleOrderData.order.restaurant_order_cancelations.length" 
									    				v-for="restaurantOrderCancelation in singleOrderData.order.restaurant_order_cancelations" 
									    				v-show="cancelledLaterOrInitially(singleOrderData.order, restaurantOrderCancelation)" 
									    				class="badge badge-secondary d-block"
									    			>
									    				{{restaurantOrderCancelation.restaurant.name + ' has cancelled'}} 
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
									    			
								    			<!-- 
									    			<span 
									    				v-if="singleOrderData.order.rider_order_cancelation" 
									    				class="badge badge-secondary d-block"
									    			>
									    				{{
									    					singleOrderData.order.rider_order_cancelation.rider.name + ' has cancelled'
									    				}}
									    			</span>
 												-->
									    			
									    			<span v-if="singleOrderData.order.rider_delivery_confirmation" 
									    				:class="[singleOrderData.order.rider_delivery_confirmation.rider_delivery_confirmation==1 ? 'badge-success' : 'badge-warning', 'badge d-block']"
									    			>
									    				{{ 
									    					riderFoodDeliveryStatus(singleOrderData.order.rider_delivery_confirmation)
									    				}}
									    			</span>

									    			<span v-if="singleOrderData.order.order_serve_confirmation"
									    				:class="[singleOrderData.order.order_serve_confirmation.food_serve_confirmation==1 ? 'badge-success' : 'badge-secondary', 'badge d-block']"
									    			>
									    				{{ 
									    					foodServeStatus(singleOrderData.order.order_serve_confirmation)
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
															</ol>

															<p class="text-danger" 
																v-show="!orderedRestaurant.items.length"
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

								<div id="delivery-info" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Delivery Address
							              		</label>
								                <div class="col-sm-6">

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
																({{ 
										                  			singleOrderData.order.delivery.customer_address.additional_hint
											                  	}})
															</dd>

															<dd class="font-weight-bold">-
																{{ 
										                  			singleOrderData.order.delivery.customer_address.address_name | capitalize
											                  	}}
															</dd>

															<dd>- 
																{{ 
										                  			singleOrderData.order.delivery.additional_info | capitalize
											                  	}}
															</dd>
														</dl> 
									                  	
								                  	</address>

								                  	<address v-else>
								                  		<dl>
								                  			<dd>
								                  				Order for '{{ singleOrderData.order.order_type | capitalize }}'
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
						<div class="modal-footer justify-content-around">
							<!-- show if customer is not confirmed and orderer is not waiter -->
							<button 
						  		type="button" 
						  		class="btn btn-success flex-fill"
						  		v-if="orderToBeConfirmed(singleOrderData.order) && !reservationOrder(singleOrderData.order)" 
						  		@click="confirmOrder()" 
						  		:disabled="formSubmitionMode" 
						  	>
						  		<i class="fas fa-check"></i>
						  		Confirm
						  	</button>
			      			
							<div class="btn-group dropright flex-fill">
								<!-- disabled if not even confirmed or already delivered or cancelled by restaurants in order-->
								<button 
									type="button" 
									class="btn btn-danger dropdown-toggle" 
									data-toggle="dropdown" 
									v-if="!deliveredOrServedOrder(singleOrderData.order) && !cancelledOrder(singleOrderData.order)" 
								>
									<i class="fas fa-times"></i>
									Cancel
								</button>
								<div class="dropdown-menu">
									<!-- disabled if no rider or not picked up -->
					      			<button 
						      			type="button" 
						      			class="btn btn-outline-danger btn-sm dropdown-item" 
						      			@click="showRiderCancelationModal()" 
						      			:disabled="Boolean(formSubmitionMode || !riderAssigned(singleOrderData.order) || riderPickedOrder(singleOrderData.order))"
					      			>
					        			<i class="fas fa-cancel"></i>
					        			Cancelled by Rider
					      			</button>
									<!-- if not already cancelled order -->
									<button 
								  		type="button" 
								  		class="btn btn-outline-danger btn-sm dropdown-item" 
								  		@click="showCustomerCancelationModal()" 
								  		:disabled="Boolean(formSubmitionMode || cancelledOrder(singleOrderData.order) || orderConfirmed(singleOrderData.order))"  
								  	>
								  		Cancelled by Customer
								  	</button>

									<!-- disabled if no restaurant accepted the order or already picked -->
					      			<button 
						      			type="button" 
						      			class="btn btn-outline-danger btn-sm dropdown-item" 
						      			@click="showRestaurantCancelationModal()" 
						      			:disabled="Boolean(formSubmitionMode || allRestaurantOrderPicked(singleOrderData.order) || allRestaurantsCancelledOrder(singleOrderData.order))"
					      			>
					        			<i class="fas fa-cancel"></i>
					        			Cancelled by Restaurant
					      			</button>
								</div>
							</div>
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
												v-for="cancelledBy in ['customer', 'restaurants', 'rider']" 
												:value="cancelledBy" 
											>
												{{ cancelledBy | capitalize }}
											</option>
										</select>

					                	<div class="text-danger" v-if="errors.orderCancelation.cancelledBy">
								        	{{ errors.orderCancelation.cancelledBy }}
								  		</div>
					                </div>	
				              	</div>

				              	<div 
				              		class="form-group row" 
				              		v-if="singleOrderData.orderCancelation.cancelledBy==='restaurants'"
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
												{{ orderedRestaurant.restaurant.name | capitalize }}
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

	var singleOrderData = {
		order : {

		},
		orderCancelation : {
			reason : {},
			// restaurant : {},
			reason_id : null,
			restaurant_id : null,
			cancelledBy : 'customer',
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
    	formSubmitionMode : false,

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
			.listen('.updation-for-admin', (broadcastedOrder) => {
			    
			    console.log(broadcastedOrder);

			    // due to pagination, checking if this broadcasted one already exists 
			    const objectExist = (orderObject) => orderObject.id==broadcastedOrder.id;

			    // if the order is paid and already ringing restaurant end
			    const restaurantRinging = (restaurantOrderRecord) => restaurantOrderRecord.food_order_acceptance==-1;

			    // now showing the broadcastedOrder in this page
			    if (this.ordersToShow.some(objectExist)) {
				    let index = this.ordersToShow.findIndex(orderObject => orderObject.id==broadcastedOrder.id);
				    // console.log(index);
				    // this.ordersToShow[index] = broadcastedOrder;
				    // this.ordersToShow.$set(index, broadcastedOrder);
				    // this.$set(this.ordersToShow, index, broadcastedOrder)
				    Vue.set(this.ordersToShow, index, broadcastedOrder)
				    toastr.info("Order-update arrives");
			    }
			    // new order and not in the list or nothing in the list or new paid order
			    else if ((broadcastedOrder.customer_confirmation===-1 && !this.ordersToShow.some(objectExist)) || (Array.isArray(this.ordersToShow) && !this.ordersToShow.length) || (broadcastedOrder.customer_confirmation===1 && broadcastedOrder.restaurant_acceptances.filter(restaurantRinging).length==broadcastedOrder.restaurant_acceptances.length)) {

			    	this.ordersToShow.unshift(broadcastedOrder);
			    	toastr.info("New order arrives");
			    }
			    // else
			    else {
			    	toastr.warning("Else");
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
			updateCurrentOrder(){
				this.singleOrderData.order = this.ordersToShow.find(
					currentOrder => currentOrder.id === this.singleOrderData.order.id
				);
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
							console.log(this.singleOrderData.order);
						}
					})
					.catch(error => {
						console.log(error);
					});
    		},
    		showOrderDetailModal(order) {
				this.fetchExpectedOrderDetail(order);
				$("#modal-show-order").modal("show");
    			this.singleOrderData.orderCancelation = {};
			},
			confirmOrder(){
				
				this.formSubmitionMode = true;
				// $("#modal-confirmOrCancel-order").modal("hide");
				
				axios
					.post('/orders/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.order)
					.then(response => {
						if (response.status == 200) {

							this.allOrders = response.data;
							this.showListDataForSelectedTab();
							// this.updateCurrentOrder();
							this.fetchExpectedOrderDetail(this.singleOrderData.order);

							this.formSubmitionMode = false;
							
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
			showCustomerCancelationModal() {
				// this.fetchExpectedOrderDetail(order);
				// console.log(this.singleOrderData.order);
				this.singleOrderData.orderCancelation.cancelledBy = 'customer';
				$("#modal-restaurantOrRider-orderCancelation").modal("show");
			},
			showRiderCancelationModal() {
				// this.fetchExpectedOrderDetail(order);
				// console.log(this.singleOrderData.order);
				this.singleOrderData.orderCancelation.cancelledBy = 'rider';
				$("#modal-restaurantOrRider-orderCancelation").modal("show");
			},
			showRestaurantCancelationModal() {
				// this.fetchExpectedOrderDetail(order);
				// console.log(this.singleOrderData.order);
				this.singleOrderData.orderCancelation.cancelledBy = 'restaurants';
				$("#modal-restaurantOrRider-orderCancelation").modal("show");
			},
			cancelOrder() {

				// if (this.singleOrderData.orderCancelation.cancelledBy==='restaurants' || this.singleOrderData.orderCancelation.cancelledBy === 'rider') {

					if (this.singleOrderData.orderCancelation.cancelledBy==='restaurants' && !this.singleOrderData.orderCancelation.restaurant_id) {	

						this.errors.orderCancelation.restaurant = 'Restaurant name is required';
						this.submitCancelationForm = false;
						return;

					}
					
					if (!this.singleOrderData.orderCancelation.reason_id) {

						this.errors.orderCancelation.reason = 'Reason is required';
						this.submitCancelationForm = false;
						return;
						
					}

					$("#modal-restaurantOrRider-orderCancelation").modal("hide");

				// }
				
				// $("#modal-confirmOrCancel-order").modal("hide");
				
				axios
					.put('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.orderCancelation)
					.then(response => {
						if (response.status == 200) {

							this.allOrders = response.data;
							this.showListDataForSelectedTab();
							// this.updateCurrentOrder();
							this.fetchExpectedOrderDetail(this.singleOrderData.order);

							this.formSubmitionMode = false;
							
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
			allRestaurantsCancelledOrder(order) {
				if (Object.keys(order).length) {

					return order.restaurant_acceptances.length == order.restaurant_order_cancelations.length ? true : false;
				}

				return false;
			},
			defineOrderType(order){
				if (order.order_type=='reservation') {
					return 'Reservation';
				}
				else
					return 'Order';
			},
			orderConfirmed(order){
				return order.customer_confirmation===1 ? true : false;
			},
			orderToBeConfirmed(order){
				return order.customer_confirmation===-1 ? true : false;
			},
			cancelledOrder(order){
				return order.customer_confirmation===0 ? true : false;
			},
			reservationOrder(order){
				if (order.order_type=='reservation') {
					return true;
				}

				return false;
			},
			riderPickedOrder(order) {

				if (Array.isArray(order.rider_food_pick_confirmations)) {
					
					return order.rider_food_pick_confirmations.filter(
						orderPickUpProgression => orderPickUpProgression.rider_food_pick_confirmation==1
					).length;

				}

				return false;

			},
			// completed order
			deliveredOrServedOrder(order) {

				if (order.rider_delivery_confirmation && order.rider_delivery_confirmation.rider_delivery_confirmation==1) {
					return 'Order Deliverd';
				}else if (order.order_serve_confirmation && order.order_serve_confirmation.food_serve_confirmation==1) {
					return 'Order Served';
				}else
					return false;
			},
			allRestaurantOrderPicked(order){

				if (Array.isArray(order.restaurant_acceptances)) {

					let numberRestaurantsAccepted = order.restaurant_acceptances.filter(
														restaurantOrderRecord=>restaurantOrderRecord.food_order_acceptance==1
													).length;

					let numberRestaurantsPicked = order.rider_food_pick_confirmations.filter(
													orderPickUpProgression=>orderPickUpProgression.rider_food_pick_confirmation==1
												).length;

					return numberRestaurantsPicked==numberRestaurantsAccepted ? true : false;
				
				}

				return false;

			},
			// secondary order class definer
			orderProgressionClass(order, index) {
				if (this.secondaryOrder(order, index).includes("cancelled")) {
					return 'badge-secondary bg-secondary';
				}else if (this.secondaryOrder(order, index).includes("picked-up")) {
					return 'badge-success bg-success';
				}else if (this.secondaryOrder(order, index).includes("ready")) {
					return 'badge-success bg-success';
				}else if (this.secondaryOrder(order, index).includes("accepted")) {
					return 'badge-warning bg-warning';
				}else if (this.secondaryOrder(order, index).includes("ringing")) {
					return 'badge-danger bg-danger';
				}
			},
			// after call confirmation
			secondaryOrder(order, index) {
				
				// this restaurant cancelled the order ?
				if(order.restaurant_order_cancelations[index] && order.restaurant_order_cancelations[index].hasOwnProperty('reason_id')) {
					return order.restaurant_order_cancelations[index].restaurant.name + ' cancelled order';
				}
				// if picked up
				else if (order.rider_food_pick_confirmations[index] && order.rider_food_pick_confirmations[index].rider_food_pick_confirmation==1) {
					return 'Order picked-up from ' + order.rider_food_pick_confirmations[index].restaurant.name;	
				}
				// ready ?
				else if (order.order_ready_confirmations[index] && order.order_ready_confirmations[index].food_ready_confirmation==1) {
					return order.order_ready_confirmations[index].restaurant.name + ' is ready';
				}
				// restaurant ringing or accepted ?
				else {
					if (order.restaurant_acceptances[index].food_order_acceptance==1) {
						return order.restaurant_acceptances[index].restaurant.name +' has accepted';
					}else if (order.restaurant_acceptances[index].food_order_acceptance==-1) {
						return order.restaurant_acceptances[index].restaurant.name +' is ringing';
					}
					else
						return false;
						// 	return order.restaurant_acceptances[index].restaurant.name +' has cancelled';
				}

			},
			// initial class for every order
			initialOrderClass(order) {

				if (this.reservationOrder(order) && this.orderToBeConfirmed(order)) {
					return 'bg-secondary'
				}
				else if(!this.reservationOrder(order) && this.orderToBeConfirmed(order)){
					return 'bg-dark';
				}
				else if (this.orderConfirmed(order)) {
					return 'bg-success';
				}
				else
					return 'bg-secondary';
			},
			// before confirmation
			initialOrder(order) {

				if (this.reservationOrder(order) && this.orderToBeConfirmed(order)) {
					return "Unconfirmed Reservation";
				}
				else if (!this.reservationOrder(order) && this.orderToBeConfirmed(order)) {
					return 'To Be Confirmed';
				}else if(this.cancelledOrder(order)) {
					return 'Cancelled by Customer';
				}
				else
					return false;

			},
			// initial status for every order
			initialOrderStatus(order) {

				if (this.initialOrder(order)) {
					return this.initialOrder(order);
				}
				else if (this.orderConfirmed(order)) {
					return 'Confirmed';
				}
				return false;
				
			},
			orderCallConfirmationClass(order) {
				
				if (this.reservationOrder(order) && this.orderToBeConfirmed(order)) {
					return 'badge-secondary';
				}
				else if (!this.reservationOrder(order) && this.orderToBeConfirmed(order)) {
					return 'badge-dark';
				}
				else if (this.orderConfirmed(order)) {
					return 'badge-success';
				}
				else if(this.cancelledOrder(order)) {
					return 'badge-secondary';
				}
				else
					return false;

			},
			orderCallConfirmationStatus(order) {
				
				if (this.cancelledOrder(order)) {
					return 'Cancelled';
				}else if (this.orderConfirmed(order)) {
					return 'Confirmed';
				}else if (this.reservationOrder(order) && this.orderToBeConfirmed(order)) {
					return "Unconfirmed Reservation";
				}else
					return 'To Be Confirmed';

			},
			restaurantOrderAcceptanceClass(restaurantOrderRecord) {
				if (restaurantOrderRecord.food_order_acceptance==-1) {
					return 'badge-danger';
				}else if (restaurantOrderRecord.food_order_acceptance==1) {
					return 'badge-warning';
				}else 
					return 'badge-secondary';		
			},
			restaurantOrderAcceptanceStatus(restaurantOrderRecord) {
				if (restaurantOrderRecord.food_order_acceptance==-1) {
					return restaurantOrderRecord.restaurant.name + ' is ringing';
				}else if (restaurantOrderRecord.food_order_acceptance==1) {
					return restaurantOrderRecord.restaurant.name + ' has accepted';
				}else 
					return restaurantOrderRecord.restaurant.name + ' has cancelled';
			},
			cancelledLaterOrInitially(order, restaurantOrderCancelation) {
				
				if (Object.keys(order).length) {

					// if already included in restaurant_order_records
					let alreadyListed = order.restaurant_acceptances.some(
						restaurantOrderRecord=>(restaurantOrderRecord.food_order_acceptance==0 && restaurantOrderRecord.restaurant_id==restaurantOrderCancelation.restaurant_id)
					);

				}

			},
			riderFoodPickUpClass(riderFoodPickUpConfirmation) {
				if (riderFoodPickUpConfirmation.rider_food_pick_confirmation==1) {
					return 'badge-warning';
				}else if (riderFoodPickUpConfirmation.rider_food_pick_confirmation==-1) {
					return 'badge-primary';
				}else
					return 'badge-secondary';
			},
			//  cancelled by rider
			riderFoodPickUpStatus(riderFoodPickUpConfirmation) {

				if (riderFoodPickUpConfirmation.rider_food_pick_confirmation==0) {
					return 'Rider cancelled ' + riderFoodPickUpConfirmation.restaurant.name;
					// return riderFoodPickUpConfirmation.rider.user_name +' has cancelled order of '+riderFoodPickUpConfirmation.restaurant.name;
				}else if (riderFoodPickUpConfirmation.rider_food_pick_confirmation==1) {
					return 'Picked-up from ' + riderFoodPickUpConfirmation.restaurant.name;
					// return riderFoodPickUpConfirmation.rider.user_name +' picked-up from ' + riderFoodPickUpConfirmation.restaurant.name;
				}else {
					return 'Not picked-up yet';
					// return riderFoodPickUpConfirmation.rider.user_name +' not picked-up yet from '+riderFoodPickUpConfirmation.restaurant.name;
				}
					
			},
			riderFoodDeliveryStatus(riderDeliveryConfirmation) {
				if (riderDeliveryConfirmation.rider_delivery_confirmation==1) {
					return 'Delivered';
					// return 'Delivered by ' + riderDeliveryConfirmation.rider.user_name;
				}else if (riderDeliveryConfirmation.rider_delivery_confirmation===-1) {
					return 'On the way';
					// return riderDeliveryConfirmation.rider.user_name + ' is on the way';
				}else if (riderDeliveryConfirmation.rider_delivery_confirmation==2) {
					return 'Dropped at office';
				}
				else
					return 'No response';
			},
			foodServeStatus(foodServeConfirmation) {
				if (foodServeConfirmation.food_serve_confirmation==1) {
					return 'Served';
				}else if (foodServeConfirmation.food_serve_confirmation===-1) {
					'Not served yet';
				}
				else
					return 'Cancelled';
			},
			riderAssigned(order) {
				if (order.rider_assignment) {
					return true;
				}
				return false;
			}
			
		}
  	}

</script>