
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
													:class="[{ 'active': currentTab==='unconfirmed' }, 'nav-link']" 
													@click="showNewOrders"
												>
													Unconfirmed
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab==='active' }, 'nav-link']" 
													@click="showActiveOrders"
												>
													Active
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
												<span 
								    				v-if="failedOrder(order)" 
								    				class="badge badge-secondary d-block"
								    			>	
								    				Failed
								    			</span>

												<!-- 
													no option should be shown without picking/cancelling every restaurant orders 
												-->
								    			<span 
								    				v-else-if="deliveredOrServedOrder(order)" 
								    				:class="[returnedOrder(order) ? 'badge-primary' : 'badge-success', 'badge d-block']"
								    			>	
								    				{{ deliveredOrServedOrder(order)}}
								    			</span>

								    			<!-- 
								    				// after call confirmation
								    				if rider has been assigned (riderFoodPickConfirmations is auto set after assignment)  
								    				// for each restaurants in order
								    			-->
							    				<span 
							    					v-else-if="orderConfirmed(order)"
							    				>
								    				<span
								    					v-for="restaurantOrderRecord in order.restaurant_acceptances" 
								    					:class="[secondaryOrderClass(order, restaurantOrderRecord.restaurant_id), 'badge d-block']"
								    				>
									    				{{ secondaryOrder(order, restaurantOrderRecord.restaurant_id) }}
								    				</span>
							    				</span>	

							    				<!-- 
							    					new order before call confirmation
							    				-->
								    			<span :class="[initialOrderClass(order), 'badge d-block']" v-else>	
								    				{{ initialOrder(order) }}
								    			</span>
								    		</td>
								    		<td>
								      			<button 
									      			type="button" 
									      			class="btn btn-outline-primary btn-sm"
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
										class="btn btn-outline-primary btn-sm" 
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
						<div class="modal-header">
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
									v-if="initialOrder(singleOrderData.order)" 
				    				:class="[initialOrderClass(singleOrderData.order)]"
								>
									{{ initialOrder(singleOrderData.order) }}
								</div>
								
				    			<!-- 
				    				if rider has been assigned (riderFoodPickConfirmations is auto set after assignment)  
				    				// for each restaurants in order
				    				// after call confirmation
				    			-->

				    			<div 
				    				class="progress-bar progress-bar-striped progress-bar-animated" 
				    				v-if="singleOrderData.order.restaurant_acceptances.length" 
				    				v-for="restaurantOrderRecord in singleOrderData.order.restaurant_acceptances" 
				    				:class="[secondaryOrderClass(singleOrderData.order, restaurantOrderRecord.restaurant_id)]" 
									:style="{ width: (60/singleOrderData.order.restaurant_acceptances.length) + '%' }"
								>
									{{ 
				    					secondaryOrder(singleOrderData.order, restaurantOrderRecord.restaurant_id)
				    				}}
								</div>

								<!-- 
									no option should be shown without picking/cancelling every restaurant orders 
								-->
								<div 
				    				:class="[returnedOrder(order) ? 'bg-primary' : 'bg-success', 'progress-bar progress-bar-striped progress-bar-animated']"
									style="width:15%"
									v-if="deliveredOrServedOrder(singleOrderData.order)" 
								>
									{{ deliveredOrServedOrder(singleOrderData.order) }}
								</div>

								<div 
				    				class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
									style="width:15%"
									v-else-if="failedOrder(singleOrderData.order)" 
								>
									{{ 'Failed' }}
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
							              			Orderer Name:
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
							              			Phone:
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
							              			Email:
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
							              			Joined on:
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
							              			Order id:
							              		</label>
								                <div class="col-sm-6" >
								                  	{{ singleOrderData.order.id }}
								                </div>
								            </div>
								            
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Type:
							              		</label>

								                <div class="col-sm-6">
								                	{{ singleOrderData.order.order_type | capitalize }}
								                </div>
								            </div>

								            <div class="form-group row" v-if="singleOrderData.order.asap || singleOrderData.order.scheduled">		
							              		<label class="col-sm-6 text-right">
							              			ASAP/Scheduled:
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
								                  	{{ singleOrderData.order.discount }}
								                </div>	
								            </div>

								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Delivery-fee:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.delivery_fee }}
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
							              			Payment type:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.payment_method | capitalize }}
								                  	<span v-show="singleOrderData.order.payment_method!='cash'">
								                  		(Payment id : {{ singleOrderData.order.payment ? singleOrderData.order.payment.payment_id : 'NA' }})
								                  	</span>
								                </div>	
								            </div>

								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Cutlery:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.cutlery_added ? 'Added' : 'None' }}
								                </div>	
								            </div> 
								            
								            <div class="form-group row">		
							              		<label class="col-sm-6 text-right">
							              			Order Status:
							              		</label>
								                <div class="col-sm-6">
								                	<!-- <div v-show="order.payment_method==='cash'"> -->
									    			<span 
									    			v-if="initialOrder(singleOrderData.order)" 
									    			:class="[initialOrderClass(singleOrderData.order), 'badge d-block']"
									    			>
									    				{{ initialOrder(singleOrderData.order) }}
									    			</span>

									    			<span v-if="singleOrderData.order.restaurant_acceptances && singleOrderData.order.restaurant_acceptances.length">
										    			<span 
		 								    				v-for="restaurantOrderRecord in singleOrderData.order.restaurant_acceptances" 
										    				:class="[restaurantOrderAcceptanceClass(restaurantOrderRecord), 'badge d-block']"
										    			>	
										    				{{ restaurantOrderAcceptanceStatus(restaurantOrderRecord) }}
										    			</span>
									    			</span>

									    			<span 
									    				v-if="riderAssigned(singleOrderData.order)"
									    				:class="[riderAssigned(singleOrderData.order) ? 'badge-info' : 'badge-danger', 'badge d-block']"
									    			>
									    				{{ riderAssigned(singleOrderData.order) ? 'Rider Assigned' : 'Not-assigned' }}
									    			</span>

									    			<span v-if="singleOrderData.order.order_ready_confirmations && singleOrderData.order.order_ready_confirmations.length">
										    			<span 
										    				v-for="restaurantReadyConfirmation in singleOrderData.order.order_ready_confirmations" 
										    				:class="[restaurantReadyConfirmation.food_ready_confirmation==1 ? 'badge-success' : 'badge-info', 'badge d-block']"
										    			>
										    				{{ restaurantReadyConfirmation.restaurant_name }}

										    				{{ restaurantReadyConfirmation.food_ready_confirmation==1 ? ' is ready' : ' aint ready yet' }}
										    			</span>
									    			</span>

									    			<span v-if="singleOrderData.order.restaurant_order_cancelations && singleOrderData.order.restaurant_order_cancelations.length">
										    			<span
										    				v-for="restaurantOrderCancelation in singleOrderData.order.restaurant_order_cancelations" 
										    				v-show="restaurantCancelled(singleOrderData.order, restaurantOrderCancelation)" 
										    				class="badge badge-secondary d-block"
										    			>
										    				{{ restaurantOrderCancelation.restaurant_name + ' has cancelled'}} 
										    			</span>
									    			</span>

									    			<span v-if="singleOrderData.order.rider_food_pick_confirmations && singleOrderData.order.rider_food_pick_confirmations.length">
										    			<span 
											    			v-for="riderFoodPickUpConfirmation in singleOrderData.order.rider_food_pick_confirmations" 
										    				:class="[riderFoodPickUpClass(riderFoodPickUpConfirmation), 'badge d-block']"
										    			>
										    				{{ riderFoodPickUpStatus(riderFoodPickUpConfirmation) }}
										    			</span>
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
									    				:class="[singleOrderData.order.rider_delivery_confirmation.rider_delivery_confirmation==1 ? 'badge-success' : singleOrderData.order.rider_delivery_confirmation.rider_delivery_confirmation==2 ? 'badge-primary' : 'badge-warning', 'badge d-block']"
									    			>
									    				{{ riderFoodDeliveryStatus(singleOrderData.order.rider_delivery_confirmation) }}
									    			</span>

									    			<span v-else-if="singleOrderData.order.order_serve_confirmation"
									    				:class="[singleOrderData.order.order_serve_confirmation.food_serve_confirmation==1 ? 'badge-success' : 'badge-warning', 'badge d-block']"
									    			>
									    				{{ foodServeStatus(singleOrderData.order.order_serve_confirmation) }}
									    			</span>

									    			<span 
									    				v-else-if="failedOrder(singleOrderData.order)" 
									    				class="badge badge-secondary d-block"
									    			>	
									    				Failed
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
							              			Order Items:
							              		</label>
								                <div class="col-sm-6">
								                	<ul v-show="singleOrderData.order.restaurants && singleOrderData.order.restaurants.length">
														<li v-for="(orderRestaurant, index) in singleOrderData.order.restaurants" 
															:key="orderRestaurant.id"
														>
															{{ orderRestaurant.name | capitalize }}

															<ol v-show="orderRestaurant.menu_items.length">
																<li v-for="(item, index) in orderRestaurant.menu_items" 
																	:key="item.id"
																>	
																	{{ item.restaurant_menu_item.name | capitalize }} 

																	<span class="d-block"
																		v-if="item.restaurant_menu_item.has_variation" 
																	>
																		(Selected Variation : {{ item.item_variation.restaurant_menu_item_variation | capitalize }} )
																	</span>

																	<p class="d-block">
																		<span class="font-weight-bold">- Quantity : </span>
																		{{ item.quantity }}
																	</p>

																	<span 
																		class="d-block font-weight-bold" 
																		v-if="item.item_addons.length"
																	>
																		Addons
																	</span>

																	<ul v-if="item.restaurant_menu_item.has_addon && item.item_addons.length">

																		<li v-for="(additionalOrderedAddon, index) in item.item_addons">
																			{{ additionalOrderedAddon.restaurant_menu_item_addon | capitalize }} ({{ additionalOrderedAddon.quantity }})
																		</li>
																	</ul>

																	<p class="d-block" v-if="item.customization">
																		<span class="font-weight-bold">- Customization : </span>
																		{{ item.customization | capitalize }}
																	</p>
																</li>
															</ol>

															<p class="text-danger" 
																v-show="! orderRestaurant.menu_items.length"
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
							              			Delivery Address:
							              		</label>
								                <div class="col-sm-6">

								                  	<address v-if="singleOrderData.order.delivery && singleOrderData.order.delivery.customer_address">

														<dl>
															
															<dd>-
																{{
																	singleOrderData.order.delivery.customer_address.house | capitalize 
																}}
										                  	</dd>
															
															<dd>-
																{{ 
										                  			singleOrderData.order.delivery.customer_address.road | capitalize
											                  	}}
															</dd>
															
															<dd v-if="singleOrderData.order.delivery.customer_address.additional_hint"> 
																({{ 
										                  			singleOrderData.order.delivery.customer_address.additional_hint | capitalize
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
						  		v-if="orderToBeConfirmed(singleOrderData.order) && ! reservationOrder(singleOrderData.order) && ! customerCancelledOrder(singleOrderData.order) && ! stoppedOrder(singleOrderData.order)" 
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
									v-if="! returnedOrder(singleOrderData.order) && ! deliveredOrServedOrder(singleOrderData.order) && ! customerCancelledOrder(singleOrderData.order) && ! stoppedOrder(singleOrderData.order) && ! riderPickedOrder(singleOrderData.order) && ! allRestaurantsCancelledOrder(singleOrderData.order) " 
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
						      			:disabled="Boolean(formSubmitionMode || ! riderAssigned(singleOrderData.order) /*|| riderPickedOrder(singleOrderData.order)*/ || orderToBeConfirmed(singleOrderData.order))"
					      			>
					        			<i class="fas fa-times text-danger"></i>
					        			By Rider
					      			</button>
									<!-- if not already cancelled order -->
									<button 
								  		type="button" 
								  		class="btn btn-outline-danger btn-sm dropdown-item" 
								  		@click="showCustomerCancelationModal()" 
								  		:disabled="Boolean(formSubmitionMode  || orderConfirmed(singleOrderData.order))"  
								  	>
								  		<i class="fas fa-times text-danger"></i>
								  		By Customer
								  	</button>

									<!-- disabled if no restaurant accepted the order or already picked -->
					      			<button 
						      			type="button" 
						      			class="btn btn-outline-danger btn-sm dropdown-item" 
						      			@click="showRestaurantCancelationModal()" 
						      			:disabled="Boolean(formSubmitionMode || allRestaurantOrderReady(singleOrderData.order) || allRestaurantOrderPicked(singleOrderData.order) /*|| allRestaurantsCancelledOrder(singleOrderData.order)*/ || orderToBeConfirmed(singleOrderData.order))"
					      			>
					        			<i class="fas fa-times text-danger"></i>
					        			By Restaurant
					      			</button>

					      			<!-- disabled at any stage if not already cancelled -->
					      			<button 
						      			type="button" 
						      			class="btn btn-outline-danger btn-sm dropdown-item" 
						      			@click="showAdminCancelationModal()" 
						      			:disabled="Boolean(formSubmitionMode /*|| allRestaurantsCancelledOrder(singleOrderData.order)*/ || orderToBeConfirmed(singleOrderData.order))"
					      			>
					        			<i class="fas fa-times text-danger"></i>
					        			By Admin
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

			<!-- /.modal-order-cancelation -->
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
											v-model="singleOrderData.orderCancelation.canceller" 
											@change="errors.orderCancelation.canceller=null;submitCancelationForm = true;" 
										>
											<option :value="null" selected="true" disabled="true">
												Pick Canceller Entity 
											</option>
											<option 
												v-for="canceller in cancellers" 
												:value="canceller" 
											>
												{{ canceller | capitalize }}
											</option>
										</select>

					                	<div 
						                	class="text-danger" 
						                	v-if="errors.orderCancelation.canceller"
					                	>
								        	{{ errors.orderCancelation.canceller }}
								  		</div>
					                </div>	
				              	</div>

				              	<div 
				              		class="form-group row" 
				              		v-if="singleOrderData.orderCancelation.canceller==='restaurants' && singleOrderData.order.restaurants && singleOrderData.order.restaurants.length" 
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
												v-for="orderRestaurant in singleOrderData.order.restaurants" 
												:value="orderRestaurant.id" 
											>
												{{ orderRestaurant.name | capitalize }}
											</option>
										</select>

					                	<div 
						                	class="text-danger" 
						                	v-if="errors.orderCancelation.restaurant"
					                	>
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
			<!-- /.modal-order-cancelation-->
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
			canceller : 'customer',
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

    	cancellers : [],

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
      			canceller : null,
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
			    
			    // console.log(broadcastedOrder);

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
			fetchExpectedOrderDetail(order){
    			axios
					.get('/orders/'+ order.id +'/show')
					.then(response => {		
						if (response.status == 200) {
							this.singleOrderData.order = response.data.data;
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
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancelation, 'canceller', 'customer');
				$("#modal-order-cancelation").modal("show");
			},
			showRiderCancelationModal() {
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancelation, 'canceller', 'rider');
				this.singleOrderData.orderCancelation.rider_id = this.singleOrderData.order.rider_assignment.rider_id;
				$("#modal-order-cancelation").modal("show");
			},
			showRestaurantCancelationModal() {
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancelation, 'canceller', 'restaurants');
				$("#modal-order-cancelation").modal("show");
			},
			showAdminCancelationModal() {
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancelation, 'canceller', 'admin');
				$("#modal-order-cancelation").modal("show");
			},
			cancelOrder() {

				// if (this.singleOrderData.orderCancelation.canceller==='restaurants' || this.singleOrderData.orderCancelation.canceller === 'rider') {

					if (this.singleOrderData.orderCancelation.canceller==='restaurants' && ! this.singleOrderData.orderCancelation.restaurant_id) {	

						this.errors.orderCancelation.restaurant = 'Restaurant name is required';
						this.submitCancelationForm = false;
						return;

					}
					
					if (!this.singleOrderData.orderCancelation.reason_id) {

						this.errors.orderCancelation.reason = 'Reason is required';
						this.submitCancelationForm = false;
						return;
						
					}

					$("#modal-order-cancelation").modal("hide");

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
			showAllOrders(){
				// this.pagination.current_page = 1;
				// this.fetchAllOrders();
				this.currentTab = 'all';
				this.showListDataForSelectedTab();
			},
			showNewOrders(){
				// this.pagination.current_page = 1;
				// this.fetchAllOrders();
				this.currentTab = 'unconfirmed';
				this.showListDataForSelectedTab();
			},
			showActiveOrders(){
				// this.pagination.current_page = 1;
				// this.fetchAllOrders();
				this.currentTab = 'active';
				this.showListDataForSelectedTab();
			},
			showDeliveredOrServedOrders(){
				// this.pagination.current_page = 1;
				// this.fetchAllOrders();
				this.currentTab = 'deliveredOrServed';
				this.showListDataForSelectedTab();
			},
			showCancelledOrders(){
				// this.pagination.current_page = 1;
				// this.fetchAllOrders();
				this.currentTab = 'cancelled';
				this.showListDataForSelectedTab();
			},
			showPrePaidOrders(){
				// this.pagination.current_page = 1;
				// this.fetchAllOrders();
				this.currentTab = 'prepaid';
				this.showListDataForSelectedTab();
			},
			showPostPaidOrders(){
				// this.pagination.current_page = 1;
				// this.fetchAllOrders();
				this.currentTab = 'postpaid';
				this.showListDataForSelectedTab();
			},
			showListDataForSelectedTab(){
				if (this.currentTab=='all') {
					this.ordersToShow = this.allOrders.all.data;
					this.pagination = this.allOrders.all;
				}else if (this.currentTab=='unconfirmed') {
					this.ordersToShow = this.allOrders.unconfirmed.data;
					this.pagination = this.allOrders.unconfirmed;
				}else if (this.currentTab=='active') {
					this.ordersToShow = this.allOrders.active.data;
					this.pagination = this.allOrders.active;
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
			/*
			updateCurrentOrder(){
				this.singleOrderData.order = this.ordersToShow.find(
					currentOrder => currentOrder.id === this.singleOrderData.order.id
				);
			},
			*/
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
			allRestaurantsCancelledOrder(order) {
				// if (Object.keys(order).length) {

				return order.hasOwnProperty('restaurant_order_cancelations') && order.restaurant_acceptances.length == order.restaurant_order_cancelations.length ? true : false;
				
				// }

				return false;
			},
			defineOrderType(order){
				if (order.order_type=='reservation') {
					return 'Reservation';
				}
				else
					return 'Order';
			},
			reservationOrder(order){
				if (order.order_type=='reservation') {
					return true;
				}

				return false;
			},
			orderConfirmed(order){
				return order.customer_confirmation===1 ? true : false;
			},
			orderToBeConfirmed(order){
				return order.customer_confirmation===-1 ? true : false;
			},
			customerCancelledOrder(order){
				return order.customer_confirmation===0 ? true : false;
			},
			stoppedOrder(order){
				return order.in_progress==0 ? true : false;
			},
			failedOrder(order){
				return ! this.customerCancelledOrder(this.singleOrderData.order) && order.in_progress===0 && order.complete_order===0 ? true : false;
			},
			// completed order
			deliveredOrServedOrder(order) {

				if (order.rider_delivery_confirmation && order.rider_delivery_confirmation.rider_delivery_confirmation==1) {
					return 'Order Deliverd';
				}
				else if (order.order_serve_confirmation && order.order_serve_confirmation.food_serve_confirmation==1) {
					return 'Order Served';
				}
				else if (order.rider_delivery_confirmation && order.rider_delivery_confirmation.rider_delivery_confirmation==2) {
					return 'Order Returned';
				}
				else
					return false;
			},
			returnedOrder(order){
				
				/*
				if (order.rider_delivery_return!=null && order.rider_delivery_return.rider_return_confirmation==1) {
					return 'Order Returned';
				}
				*/

				return Boolean(order.rider_delivery_confirmation && order.rider_delivery_confirmation.rider_delivery_confirmation==2);	
			},
			riderPickedOrder(order) {

				if (Array.isArray(order.rider_food_pick_confirmations)) {
					
					return order.rider_food_pick_confirmations.filter(
						orderPickUpProgression => orderPickUpProgression.rider_food_pick_confirmation==1
					).length;

				}

				return false;

			},
			allRestaurantOrderPicked(order){

				if (Array.isArray(order.restaurant_acceptances) && order.restaurant_acceptances.length && order.rider_food_pick_confirmations.length) {

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
			allRestaurantOrderReady(order) {

				if (Array.isArray(order.restaurant_acceptances) && order.restaurant_acceptances.length && order.order_ready_confirmations.length) {

					let numberRestaurantsAccepted = order.restaurant_acceptances.filter(
														restaurantOrderRecord=>restaurantOrderRecord.food_order_acceptance==1
													).length;

					let numberRestaurantsReady = order.order_ready_confirmations.filter(
													readyRestaurant=>readyRestaurant.food_ready_confirmation==1
												).length;

					return numberRestaurantsReady==numberRestaurantsAccepted ? true : false;
				
				}

				return false;

			},
			// secondary order class definer
			secondaryOrderClass(order, restaurant) {

				// console.log('Secondary value is : ' + this.secondaryOrder(order, restaurant));

				let secondaryOrder = this.secondaryOrder(order, restaurant);

				if (secondaryOrder.includes("cancelled")) {
					return 'badge-secondary bg-secondary';
				}else if (secondaryOrder.includes("Picked-up")) {
					return 'badge-warning bg-warning';
				}else if (secondaryOrder.includes("ready")) {
					return 'badge-success bg-success';
				}else if (secondaryOrder.includes("accepted")) {
					return 'badge-info bg-info';
				}else if (secondaryOrder.includes("ringing")) {
					return 'badge-danger bg-danger';
				}

			},
			// after call confirmation
			secondaryOrder(order, restaurant) {
				
				// console.log(this.restaurantCancelledOrder(order.restaurant_order_cancelations, restaurant));

				// if current restaurant cancelled
				if (order.hasOwnProperty('restaurant_order_cancelations') && order.restaurant_order_cancelations.length && typeof this.restaurantCancelledOrder(order.restaurant_order_cancelations, restaurant) !== 'undefined') {

					return this.restaurantCancelledOrder(order.restaurant_order_cancelations, restaurant).restaurant_name + ' cancelled order';

				}
				// if current restaurant picked up
				else if (order.rider_food_pick_confirmations.length && typeof this.riderPickedRestaurant(order.rider_food_pick_confirmations, restaurant) !== 'undefined') {

					return 'Picked-up from ' + this.riderPickedRestaurant(order.rider_food_pick_confirmations, restaurant).restaurant_name;

				}
				// if current restaurant order is ready
				else if (order.order_ready_confirmations.length && typeof this.restaurantFoodIsReady(order.order_ready_confirmations, restaurant) !== 'undefined') {

					return this.restaurantFoodIsReady(order.order_ready_confirmations, restaurant).restaurant_name + ' is ready';

				}
				// if curent restaurant has accepted ?
				else if (order.restaurant_acceptances.length && typeof this.restaurantHasAcceptedOrder(order.restaurant_acceptances, restaurant) !== 'undefined') {

					return this.restaurantHasAcceptedOrder(order.restaurant_acceptances, restaurant).restaurant_name + ' has accepted';

				}
				// if current restaurant ringing ?
				else if (order.restaurant_acceptances.length && typeof this.restaurantIsRinging(order.restaurant_acceptances, restaurant) !== 'undefined') {

					return this.restaurantIsRinging(order.restaurant_acceptances, restaurant).restaurant_name + ' is ringing';

				}
				else 
					return false;

			/*
				// this restaurant cancelled the order ?
				if(order.restaurant_order_cancelations.length >= index && order.restaurant_order_cancelations[index].hasOwnProperty('reason_id')) {
					return order.restaurant_order_cancelations[index].restaurant_name + ' cancelled order';
				}
				// if picked up
				else if (order.rider_food_pick_confirmations >= index && order.rider_food_pick_confirmations[index].rider_food_pick_confirmation==1) {
					return 'Order picked-up from ' + order.rider_food_pick_confirmations[index].restaurant_name;	
				}
				// ready ?
				else if (order.order_ready_confirmations >= index && order.order_ready_confirmations[index].food_ready_confirmation==1) {
					return order.order_ready_confirmations[index].restaurant_name + ' is ready';
				}
				// restaurant ringing or accepted ?
				else {
					if (order.restaurant_acceptances[index].food_order_acceptance==1) {
						return order.restaurant_acceptances[index].restaurant_name +' has accepted';
					}else if (order.restaurant_acceptances[index].food_order_acceptance==-1) {
						return order.restaurant_acceptances[index].restaurant_name +' is ringing';
					}
					else
						return false;
						// 	return order.restaurant_acceptances[index].restaurant_name +' has cancelled';
				}
			*/

			},
			// initial class for every order
			initialOrderClass(order) {

				if (this.orderToBeConfirmed(order) && ! this.failedOrder(order)) {
					return 'badge-dark bg-dark'
				}
				else if (this.customerCancelledOrder(order)) {
					return 'badge-secondary bg-secondary';
				}
				else {
					return 'badge-info bg-info';
				}
				
			},
			// before confirmation / cancelled confirmation
			initialOrder(order) {

				if (this.orderToBeConfirmed(order) && ! this.failedOrder(order)) {
					return "Unconfirmed " + (this.reservationOrder(order) ? 'Reservation' : 'Order');
				}
				else if(this.customerCancelledOrder(order)) {
					return 'Cancelled By Customer';
				}
				else if(this.orderConfirmed(order)) {
					return 'Customer Confirmed';
				}
				else
					return false; // required

			},
			restaurantOrderAcceptanceClass(restaurantOrderRecord) {
				if (restaurantOrderRecord.food_order_acceptance==-1) {
					return 'badge-danger';
				}else if (restaurantOrderRecord.food_order_acceptance==1) {
					return 'badge-info';
				}else 
					return 'badge-secondary';		
			},
			restaurantOrderAcceptanceStatus(restaurantOrderRecord) {
				if (restaurantOrderRecord.food_order_acceptance==-1) {
					return restaurantOrderRecord.restaurant_name + ' is ringing';
				}else if (restaurantOrderRecord.food_order_acceptance==1) {
					return restaurantOrderRecord.restaurant_name + ' has accepted';
				}else 
					return restaurantOrderRecord.restaurant_name + ' has cancelled';
			},
			restaurantCancelled(order, restaurantOrderCancelation) {
				
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
					return 'badge-info';
				}else
					return 'badge-secondary';
			},
			//  cancelled by rider
			riderFoodPickUpStatus(riderFoodPickUpConfirmation) {

				if (riderFoodPickUpConfirmation.rider_food_pick_confirmation==0) {
					return 'Rider cancelled ' + riderFoodPickUpConfirmation.restaurant_name;
					// return riderFoodPickUpConfirmation.rider.user_name +' has cancelled order of '+riderFoodPickUpConfirmation.restaurant_name;
				}else if (riderFoodPickUpConfirmation.rider_food_pick_confirmation==1) {
					return 'Picked-up from ' + riderFoodPickUpConfirmation.restaurant_name;
					// return riderFoodPickUpConfirmation.rider.user_name +' picked-up from ' + riderFoodPickUpConfirmation.restaurant_name;
				}else {
					return 'Not picked-up yet';
					// return riderFoodPickUpConfirmation.rider.user_name +' not picked-up yet from '+riderFoodPickUpConfirmation.restaurant_name;
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
			},
			// current restaurant cancelled this order
			restaurantCancelledOrder(restaurantOrderCancelations, restaurant) {
				
				return restaurantOrderCancelations.find(
					cancelation => (cancelation.restaurant_id === restaurant)
				);

				/*
					return restaurantOrderCancelations.some(
						cancelation => (cancelation.restaurant_id === restaurant)
					);
				*/

			},
			// current restaurant has been picked Up
			riderPickedRestaurant(riderPickUpConfirmations, restaurant) {
				
				return riderPickUpConfirmations.find(
					pickedUp => (pickedUp.restaurant_id == restaurant && pickedUp.rider_food_pick_confirmation==1)
				);

			},
			// current restaurant is ready
			restaurantFoodIsReady(orderReadyConfirmations, restaurant) {
				
				return orderReadyConfirmations.find(
					orderReady => (orderReady.restaurant_id === restaurant && orderReady.food_ready_confirmation==1)
				);

			},
			// current restaurant has accepted
			restaurantHasAcceptedOrder(restaurantOrderAcceptances, restaurant) {
				
				return restaurantOrderAcceptances.find(
					restaurantOrderRecord => (restaurantOrderRecord.restaurant_id === restaurant && restaurantOrderRecord.food_order_acceptance==1)
				);

			},
			// current restaurant is ringing
			restaurantIsRinging(restaurantOrderAcceptances, restaurant) {
				
				return restaurantOrderAcceptances.find(
					restaurantOrderRecord => (restaurantOrderRecord.restaurant_id === restaurant && restaurantOrderRecord.food_order_acceptance==-1)
				);

			},
			setApplicableCancellers() {

				this.cancellers = [];

				if (! this.stoppedOrder(this.singleOrderData.order) && ! this.failedOrder(this.singleOrderData.order)) {

					// if confirmed order
					if (this.orderToBeConfirmed(this.singleOrderData.order)) {
						this.cancellers.push('customer');
					}
					
					// if any restaurant which is not ready yet
					if (! this.allRestaurantOrderReady(this.singleOrderData.order)){

						this.cancellers.push('restaurants');

					}
					
					// Rider is assinged and not picked up any
					if (this.riderAssigned(this.singleOrderData.order) && ! this.singleOrderData.order.rider_food_pick_confirmations.some(readyRestaurant=>readyRestaurant.food_ready_confirmation==1).length) {

						this.cancellers.push('rider');

					}

					if (this.orderConfirmed(this.singleOrderData.order) && ! this.deliveredOrServedOrder(this.singleOrderData.order)) {

						this.cancellers.push('admin');

					}

				}

			}
			
		}
  	}

</script>