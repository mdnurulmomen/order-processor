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
											<li class="nav-product flex-fill">
												<a 
													:class="[{ 'active': currentTab==='all' }, 'nav-link']" 
													@click="showAllOrders"
												>
													All
												</a>
											</li>
											<li class="nav-product flex-fill">
												<a 
													:class="[{ 'active': currentTab==='unconfirmed' }, 'nav-link']" 
													@click="showNewOrders"
												>
													Unconfirmed
												</a>
											</li>
											<li class="nav-product flex-fill">
												<a 
													:class="[{ 'active': currentTab==='active' }, 'nav-link']" 
													@click="showActiveOrders"
												>
													Active
												</a>
											</li>
											<li class="nav-product flex-fill">
												<a 
													:class="[{ 'active': currentTab==='prepaid' }, 'nav-link']" 
													@click="showPrePaidOrders"
												>
													Pre-paid
												</a>
											</li>
											<li class="nav-product flex-fill">
												<a 
													:class="[{ 'active': currentTab==='postpaid' }, 'nav-link']" 
													@click="showPostPaidOrders"
												>
													Post-paid
												</a>
											</li>
											<li class="nav-product flex-fill">
												<a 
													:class="[{ 'active': currentTab==='deliveredOrServed' }, 'nav-link']" 
													@click="showDeliveredOrServedOrders"
												>
													Delivered/Served
												</a>
											</li>
											<li class="nav-product flex-fill">
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
											<th scope="col">Id</th>
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
								    		<td>{{ order.type | capitalize }}</td>
								    		<td>
												<span 
								    				v-if="orderIsFailed(order)" 
								    				class="badge badge-secondary d-block"
								    			>	
								    				Failed
								    			</span>

												<!-- 
													no option should be shown without picking/cancelling every merchant orders 
												-->
								    			<span 
								    				v-else-if="orderIsServedOrDelivered(order)" 
								    				:class="[orderIsReturned(order) ? 'badge-primary' : 'badge-success', 'badge d-block']"
								    			>	
								    				{{ orderServingOrDeliveringStatus(order)  | capitalize }}
								    			</span>

								    			<!-- 
								    				- after call confirmation
								    				- if rider has been assigned (riderFoodPickConfirmations is auto set after assignment)  
								    				- for each merchants in order
								    			-->
							    				<span 
							    					v-else-if="orderIsConfirmed(order)"
							    				>
								    				<span
								    					v-for="merchantOrderRecord in order.merchants" 
								    					:class="[secondaryOrderClass(order, merchantOrderRecord.merchant_id), 'badge d-block']"
								    				>
									    				{{ secondaryOrderStatus(order, merchantOrderRecord.merchant_id) | capitalize }}
								    				</span>
							    				</span>	

							    				<!-- new order before call confirmation -->
								    			<span :class="[primaryOrderClass(order), 'badge d-block']" v-else>	
								    				{{ primaryOrderStatus(order) | capitalize }}
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
							<!-- 
							<div class="progress mb-4">
			    				<div 
									v-if="orderIsPrimary(singleOrderData.order)" 
									class="progress-bar progress-bar-striped progress-bar-animated" 
									style="width:25%" 
				    				:class="[primaryOrderClass(singleOrderData.order)]"
								>
									{{ primaryOrderStatus(singleOrderData.order) | capitalize }}
								</div>

				    			<div 
				    				class="progress-bar progress-bar-striped progress-bar-animated" 
				    				v-if="singleOrderData.order.merchants.length && orderIsConfirmed(singleOrderData.order)" 
				    				v-for="merchantOrderRecord in singleOrderData.order.merchants" 
				    				:class="[secondaryOrderClass(singleOrderData.order, merchantOrderRecord.merchant_id)]" 
									:style="{ width: (60/singleOrderData.order.merchants.length) + '%' }"
								>
									{{ secondaryOrderStatus(singleOrderData.order, merchantOrderRecord.merchant_id) | capitalize }}
								</div>

								<div 
									v-if="orderIsServedOrDelivered(singleOrderData.order)" 
				    				:class="[orderIsReturned(singleOrderData.order) ? 'bg-primary' : 'bg-success', 'progress-bar progress-bar-striped progress-bar-animated']"
									style="width:15%"
								>
									{{ orderServingOrDeliveringStatus(singleOrderData.order) | capitalize }}
								</div>

								<div 
				    				class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
									style="width:15%"
									v-else-if="orderIsFailed(singleOrderData.order)" 
								>
									{{ 'Failed' }}
								</div>
							</div> 
							-->

							<ul class="nav nav-tabs justify-content-around mb-4" role="tablist">
								<li class="nav-product">
									<a class="nav-link active" data-toggle="tab" href="#orderer">
										Orderer
									</a>
								</li>
								<li class="nav-product">
									<a class="nav-link" data-toggle="tab" href="#order">
										Order
									</a>
								</li>
								<li class="nav-product">
									<a class="nav-link" data-toggle="tab" href="#order-products">
										Products
									</a>
								</li>
								<li class="nav-product">
									<a class="nav-link" data-toggle="tab" href="#delivery-info">
										Delivery Info
									</a>
								</li>
								<li class="nav-product">
									<a 
										class="nav-link" 
										data-toggle="tab" 
										href="#canceller-info" 
										v-show="orderIsFailed(singleOrderData.order)"
									>
										Canceller
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div id="orderer" class="container tab-pane active">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Name:
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
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Phone:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ 
								                  		singleOrderData.order.orderer ? 
								                  		singleOrderData.order.orderer.mobile : 'NA' 
								                  	}}
								                </div>	
								            </div>
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Email:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ 
								                  		singleOrderData.order.orderer ? 
								                  		singleOrderData.order.orderer.email : 'NA' 
								                  	}}
								                </div>	
								            </div>
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
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
					            			<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Id:
							              		</label>
								                <div class="col-sm-6" >
								                  	{{ singleOrderData.order.id }}
								                </div>
								            </div>
								            
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Type:
							              		</label>

								                <div class="col-sm-6">
								                	{{ singleOrderData.order.type | capitalize }}
								                </div>
								            </div>

								            <div class="form-group form-row" v-if="singleOrderData.order.is_asap_order || singleOrderData.order.schedule">		
							              		<label class="col-sm-6 text-md-right">
							              			ASAP/Schedule:
							              		</label>
								                <div class="col-sm-6">
								                  	{{
								                  		singleOrderData.order.is_asap_order ?
								                  		'ASAP' : singleOrderData.order.schedule.schedule
								                  	}}
								                </div>	
								            </div> 

								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Payment type:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.payment_method | capitalize }}
								                  	<span v-show="singleOrderData.order.payment_method!='cash'">
								                  		(Payment id : {{ singleOrderData.order.payment ? singleOrderData.order.payment.payment_id : 'NA' }})
								                  	</span>
								                </div>	
								            </div>

								            <div class="form-group form-row" v-show="singleOrderData.order.has_cutlery">		
							              		<label class="col-sm-6 text-md-right">
							              			Cutlery:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ singleOrderData.order.has_cutlery ? 'Added' : 'None' }}
								                </div>	
								            </div> 

								            <div class="form-group form-row" v-show="singleOrderData.order.customer_confirmation==1">		
							              		<label class="col-sm-6 text-md-right">
							              			Status:
							              		</label>
								                <div class="col-sm-6">
								                	<span :class="[singleOrderData.order.is_completed==1 ? 'badge-success' : singleOrderData.order.is_progress==1 ? 'badge-danger' : 'badge-secondary', 'badge d-block']"
									    			>	
									    				{{ singleOrderData.order.is_completed==1 ? 'Completed' : singleOrderData.order.is_progress==1 ? 'Progressive' : 'Failed' }}
									    			</span>
								                </div>	
								            </div> 
								            
								            <div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Steps:
							              		</label>

							              		<div class="col-sm-6">
							              			<span 
									    				v-if="orderIsPrimary(singleOrderData.order)" 
									    				:class="[primaryOrderClass(singleOrderData.order), 'badge d-block']"
									    			>
									    				{{ primaryOrderStatus(singleOrderData.order) | capitalize }}
									    			</span>
							              		</div>
								            </div>  

							                <div 
							                	class="form-group form-row" 
							                	v-if="orderIsConfirmed(singleOrderData.order) && singleOrderData.order.merchants && singleOrderData.order.merchants.length"
							                >
							                	<div 
						                			class="col-sm-6" 
						                			v-for="merchantOrderRecord in singleOrderData.order.merchants"
						                		>
								                	<!-- <div v-show="order.payment_method==='cash'"> -->
									    			<p class="text-center mb-0">
									    				{{ merchantOrderRecord.merchant_name | eachcapitalize }}

									    				<span  
										    				:class="[merchantOrderRecord.is_self_delivery==1 ? 'badge-success' : 'badge-danger' , 'badge']"
										    			>	
										    				{{ merchantOrderRecord.is_self_delivery==1 ? 'Self-Delivery' : 'Rider-Delivery' }}
										    			</span>
									    			</p>

									    			<span 
									    				v-if="! merchantOrderIsSelfDeliverable(merchantOrderRecord) && riderIsAssigned(singleOrderData.order)"
									    				:class="[riderIsAssigned(singleOrderData.order) ? 'badge-info' : 'badge-danger', 'badge d-block']"
									    			>
									    				{{ riderIsAssigned(singleOrderData.order) ? 'Rider Assigned' : 'Not Assigned' }}
									    			</span>

									    			<span v-if="singleOrderData.order.merchant_order_cancellations && singleOrderData.order.merchant_order_cancellations.length">
										    			<span 
										    				class="badge badge-secondary d-block" 
										    				v-for="merchantOrderCancellation in singleOrderData.order.merchant_order_cancellations" 
										    				v-if="typeof merchantAcceptedOrder(singleOrderData.order.merchants, merchantOrderCancellation.merchant_id) !== 'undefined' && merchantCancelledOrder(singleOrderData.order.merchant_order_cancellations, merchantOrderCancellation.merchant_id)" 
										    			>
										    				{{ merchantOrderCancellation.merchant_name + ' has cancelled' | capitalize}} 
										    			</span>
									    			</span>

									    			<span 
									    				:class="[merchantOrderIsReady(singleOrderData.order.merchants, merchantOrderRecord.merchant_id) ? 'badge-success' : orderAcceptanceClass(merchantOrderRecord), 'badge d-block']"
									    			>	
									    				{{ merchantOrderIsReady(singleOrderData.order.merchants, merchantOrderRecord.merchant_id) ? (merchantOrderRecord.merchant_name + ' is ready') : orderAcceptanceStatus(merchantOrderRecord) | capitalize }}
									    			</span>

									    			<span v-if="! merchantOrderIsSelfDeliverable(merchantOrderRecord) && singleOrderData.order.collections && singleOrderData.order.collections.length">
										    			<span 
											    			v-for="riderCollection in singleOrderData.order.collections" 
										    				:class="[riderCollectionClass(riderCollection), 'badge d-block']"
										    			>
										    				{{ riderCollectionStatus(riderCollection) | capitalize }}
										    			</span>
									    			</span>
									    			
								    				<!-- 
									    			<span 
									    				v-if="singleOrderData.order.rider_order_cancellation" 
									    				class="badge badge-secondary d-block"
									    			>
									    				{{
									    					singleOrderData.order.rider_order_cancellation.rider.name + ' has cancelled'
									    				}}
									    			</span>
 													-->

 													<span
 														v-show="orderIsSelfDelivered(merchantOrderRecord)"
 													>
 														{{ (merchantOrderRecord.merchant_name + 'Delivered') | eachcapitalize }}
 													</span>
									    			
									    			<span 
									    				v-if="! merchantOrderIsSelfDeliverable(merchantOrderRecord) && (orderIsDelivered(singleOrderData.order) || orderIsReturned(singleOrderData.order))" 
									    				:class="[riderDeliveryClass(singleOrderData.order.rider_assigned), 'badge d-block']"
									    			>
									    				{{ riderDeliveryStatus(singleOrderData.order.rider_assigned) | capitalize }}
									    			</span>

									    			<span 
									    				v-else-if="orderIsServed(singleOrderData.order)"
									    				:class="[orderServingClass(singleOrderData.order.order_serve_confirmation), 'badge d-block']"
									    			>
									    				{{ orderServingStatus(singleOrderData.order.order_serve_confirmation) | capitalize }}
									    			</span>

									    			<span 
									    				v-else-if="orderIsFailed(singleOrderData.order)" 
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

								<div id="order-products" class="container tab-pane fade">
									<div class="row">
						                <div class="col-sm-12 text-md-center">
						                	<ul 
						                		class="list-group" 
						                		v-show="singleOrderData.order.merchants && singleOrderData.order.merchants.length"
						                	>
												<li 
													class="list-group-item"
													v-for="(merchantOrder, index) in singleOrderData.order.merchants" 
													:key="merchantOrder.id"
												>
													<p class="font-weight-bold">
														{{ merchantOrder.merchant_name | capitalize }}
														<span  
										    				:class="[merchantOrder.is_self_delivery==1 ? 'badge-success' : 'badge-danger' , 'badge']"
										    			>	
										    				{{ merchantOrder.is_self_delivery==1 ? 'Self-Delivery' : 'Rider-Delivery' }}
										    			</span>
													</p>

													<ul 
														class="list-group" 
														v-show="merchantOrder.products.length" 
													>
														<li 
															class="list-group-item" 
															v-for="(product, index) in merchantOrder.products" 
															:key="product.id"
														>	
															{{ product.merchant_product.name | capitalize }} 

															<span v-if="product.merchant_product.has_variation" 
															>
																({{ product.variation.merchant_product_variation | capitalize }} )
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
																	{{ additionalOrderedAddon.merchant_product_addon | capitalize }} (Qty:{{ additionalOrderedAddon.quantity }})
																</li>
															</ul>

															<p class="d-block" v-if="product.customization">
																<span class="font-weight-bold">- Customization : </span>
																{{ product.customization | capitalize }}
															</p>
														</li>
													</ul>

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

								<div id="delivery-info" class="container tab-pane fade">
									<div class="row">
					            		<div class="col-sm-12">
					            			<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
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
								                  				Order for '{{ singleOrderData.order.type | capitalize }}'
								                  			</dd>
								                  		</dl>
								                  	</address>
								                  	
								                </div>	
								            </div>
					            		</div>
					            	</div>
								</div>

								<div 
									id="canceller-info" 
									class="container tab-pane" 
									v-show="orderIsFailed(singleOrderData.order)"
								>
									<div 
										class="row" 
										v-if="singleOrderData.order.merchant_order_cancellations && singleOrderData.order.merchant_order_cancellations.length"
									>
										<div 
											class="col-sm-12 card card-block" 
											v-for="(merchantOrderCancellation, cancellerIndex) in singleOrderData.order.merchant_order_cancellations" 
											:key="'canceller-info-index-' + cancellerIndex + '-canceller-info-' + merchantOrderCancellation.id" 
										>
											<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Merchant:
							              		</label>
								                <div class="col-sm-6">
								                  	{{ merchantOrderCancellation.merchant_name | eachcapitalize }}
								                </div>
								            </div>

								            <div class="form-row" v-if="merchantOrderCancellation.cancellation_reason">		
							              		<label class="col-sm-6 text-md-right">
							              			Reason:
							              		</label>
								                <div class="col-sm-6">
								                	<span v-html="merchantOrderCancellation.cancellation_reason.reason"></span>
								                </div>
								            </div>
										</div>
									</div>

									<div class="row" v-if="singleOrderData.order.admin_order_cancellation">
										<div class="col-sm-12">
											<div class="form-group form-row">		
							              		<label class="col-sm-6 text-md-right">
							              			Admin:
							              		</label>
								                <div class="col-sm-6" >
								                  	{{ singleOrderData.order.admin_order_cancellation.admin_name | eachcapitalize }}
								                </div>
								            </div>

								            <div class="form-group form-row" v-if="singleOrderData.order.admin_order_cancellation.cancellation_reason">		
							              		<label class="col-sm-6 text-md-right">
							              			Reason:
							              		</label>
								                <div class="col-sm-6">
								                	<span v-html="singleOrderData.order.admin_order_cancellation.cancellation_reason.reason"></span>
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
						  		v-if="orderToBeConfirmed(singleOrderData.order) && ! reservationOrder(singleOrderData.order) && ! customerCancelledOrder(singleOrderData.order) && ! orderIsStopped(singleOrderData.order)" 
						  		@click="confirmOrder()" 
						  		:disabled="formSubmitionMode" 
						  	>
						  		<i class="fas fa-check"></i>
						  		Confirm
						  	</button>
			      			
							<div class="btn-group dropright flex-fill">
								<!-- disabled if not even confirmed or already delivered or cancelled by merchants in order-->
								<button 
									type="button" 
									class="btn btn-danger dropdown-toggle" 
									data-toggle="dropdown" 
									v-if="! orderIsReturned(singleOrderData.order) && ! orderIsServedOrDelivered(singleOrderData.order) && ! customerCancelledOrder(singleOrderData.order) && ! orderIsStopped(singleOrderData.order) && ! orderIsPicked(singleOrderData.order) && ! allMerchantsCancelledOrder(singleOrderData.order) " 
								>
									<i class="fas fa-times"></i>
									Cancel
								</button>
								<div class="dropdown-menu">
									<!-- disabled if no rider or not picked up -->
					      			<button 
						      			type="button" 
						      			class="btn btn-outline-danger btn-sm dropdown-product" 
						      			@click="showRiderCancellationModal()" 
						      			:disabled="Boolean(formSubmitionMode || ! riderIsAssigned(singleOrderData.order) /*|| orderIsPicked(singleOrderData.order)*/ || orderToBeConfirmed(singleOrderData.order))"
					      			>
					        			<i class="fas fa-times text-danger"></i>
					        			By Rider
					      			</button>
									<!-- if not already cancelled order -->
									<button 
								  		type="button" 
								  		class="btn btn-outline-danger btn-sm dropdown-product" 
								  		@click="showCustomerCancellationModal()" 
								  		:disabled="Boolean(formSubmitionMode  || orderIsConfirmed(singleOrderData.order))"  
								  	>
								  		<i class="fas fa-times text-danger"></i>
								  		By Customer
								  	</button>

									<!-- disabled if no merchant accepted the order or already picked -->
					      			<button 
						      			type="button" 
						      			class="btn btn-outline-danger btn-sm dropdown-product" 
						      			@click="showMerchantCancellationModal()" 
						      			:disabled="Boolean(formSubmitionMode || allMerchantOrderIsReady(singleOrderData.order) || allMerchantOrderIsPicked(singleOrderData.order) /*|| allMerchantsCancelledOrder(singleOrderData.order)*/ || orderToBeConfirmed(singleOrderData.order))"
					      			>
					        			<i class="fas fa-times text-danger"></i>
					        			By Merchant
					      			</button>

					      			<!-- disabled at any stage if not already cancelled -->
					      			<button 
						      			type="button" 
						      			class="btn btn-outline-danger btn-sm dropdown-product" 
						      			@click="showAdminCancellationModal()" 
						      			:disabled="Boolean(formSubmitionMode /*|| allMerchantsCancelledOrder(singleOrderData.order)*/ || orderToBeConfirmed(singleOrderData.order))"
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

			<!-- /.modal-order-cancellation -->
			<div class="modal fade" id="modal-order-cancellation">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Order Cancellation
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

								<div class="form-group form-row">	              		
				              		<label 
				              			for="inputMenuName3" 
				              			class="col-sm-4 col-form-label text-right"
				              		>
				              			Cancelled By
				              		</label>

					                <div class="col-sm-8"> 
					                	<select 
											class="form-control" 
											v-model="singleOrderData.orderCancellation.canceller" 
											@change="errors.orderCancellation.canceller=null;submitCancellationForm = true;" 
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
						                	v-if="errors.orderCancellation.canceller"
					                	>
								        	{{ errors.orderCancellation.canceller }}
								  		</div>
					                </div>	
				              	</div>

				              	<div 
				              		class="form-group form-row" 
				              		v-if="singleOrderData.orderCancellation.canceller==='merchants' && singleOrderData.order.merchants && singleOrderData.order.merchants.length" 
				              	>	
				              		<label 
				              			for="inputMenuName3" 
				              			class="col-sm-4 col-form-label text-right"
				              		>
				              			Merchant Name
				              		</label>

					                <div class="col-sm-8">
										<select 
											class="form-control" 
											v-model="singleOrderData.orderCancellation.merchant_id" 
											@change="errors.orderCancellation.merchant=null;submitCancellationForm = true;" 
										>
											<option :value="null" selected="true" disabled="true">
												Select Merchant Name 
											</option>
											<option 
												v-for="merchantOrder in singleOrderData.order.merchants" 
												:value="merchantOrder.merchant_id" 
												:disabled="typeof merchantCancelledOrder(singleOrderData.order.merchant_order_cancellations, merchantOrder.merchant_id) !== 'undefined'"
											>
												{{ merchantOrder.merchant_name | capitalize }}
											</option>
										</select>

					                	<div 
						                	class="text-danger" 
						                	v-if="errors.orderCancellation.merchant"
					                	>
								        	{{ errors.orderCancellation.merchant }}
								  		</div>
					                </div>
				              	</div>

				              	<div class="form-group form-row">	
				              		<label 
				              			for="inputMenuName3" 
				              			class="col-sm-4 col-form-label text-right"
				              		>
				              			Cancellation Reason
				              		</label>

					                <div class="col-sm-8">
	                                	<select 
											class="form-control" 
											v-model="singleOrderData.orderCancellation.cancellation_reason_id" 
											@change="errors.orderCancellation.reason=null;submitCancellationForm=true;" 
										>
											<option :value="null" selected="true" disabled="true">
												Select Reason 
											</option>
											<option 
												v-if="allCancellationReasons.length" 
												v-for="cancellationReason in allCancellationReasons" 
												:value="cancellationReason.id" 
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
							<!-- 
								<div class="col-sm-12 text-right">
									<span class="text-danger p-0 m-0 small" v-show="!submitCancellationForm">
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
							  		:disabled="!submitCancellationForm" 
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
			<!-- /.modal-order-cancellation-->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleOrderData = {
		order : {

		},
		orderCancellation : {
			reason : {},
			// merchant : {},
			cancellation_reason_id : null,
			merchant_id : null,
			canceller : 'customer',
		}
	};

	var merchantListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitCancellationForm : true,
    	
    	allOrders : [],
    	ordersToShow : [],
    	allCancellationReasons : [],

    	cancellers : [],

    	currentTab : 'all',
    	formSubmitionMode : false,

    	pagination: {
        	current_page: 1
      	},

      	singleOrderData : singleOrderData,

      	errors : {
      		orderCancellation : {
      			reason : null,
      			merchant : null,
      			canceller : null,
      		},
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return merchantListData;
		},

		created(){
			this.fetchAllOrders();
			this.fetchAllCancellationReasons();
		},

		mounted(){

			Pusher.logToConsole = true;

			Echo.private(`notifyAdmin`)
			.listen('.updation-for-admin', (broadcastedOrder) => {
			    
			    // console.log(broadcastedOrder);

			    // due to pagination, checking if this broadcasted one already exists 
			    const objectExist = (orderObject) => orderObject.id==broadcastedOrder.id;

			    // if the order is paid and already ringing merchant end
			    const merchantRinging = (merchantOrderRecord) => merchantOrderRecord.is_accepted==-1;

			    // now showing the broadcastedOrder in this page
			    if (this.ordersToShow.some(objectExist)) {
				    let index = this.ordersToShow.findIndex(orderObject => orderObject.id==broadcastedOrder.id);
				    // console.log(index);
				    // this.ordersToShow[index] = broadcastedOrder;
				    // this.ordersToShow.$set(index, broadcastedOrder);
				    // this.$set(this.ordersToShow, index, broadcastedOrder)
				    this.$set(this.ordersToShow, index, broadcastedOrder)
				    // toastr.info("Order-update arrives");
			    }
			    // new order and not in the list or nothing in the list or new paid order
			    else if ((broadcastedOrder.customer_confirmation===-1) || (Array.isArray(this.ordersToShow) && ! this.ordersToShow.length) || (broadcastedOrder.customer_confirmation===1 && broadcastedOrder.merchants.filter(merchantRinging).length==broadcastedOrder.merchants.length)) {

			    	this.ordersToShow.unshift(broadcastedOrder);
			    	// toastr.info("New order arrives");
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
    			this.singleOrderData.orderCancellation = {};
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
			showCustomerCancellationModal() {
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancellation, 'canceller', 'customer');
				$("#modal-order-cancellation").modal("show");
			},
			showRiderCancellationModal() {
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancellation, 'canceller', 'rider');
				this.singleOrderData.orderCancellation.rider_id = this.singleOrderData.order.rider_assigned.rider_id;
				$("#modal-order-cancellation").modal("show");
			},
			showMerchantCancellationModal() {
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancellation, 'canceller', 'merchants');
				$("#modal-order-cancellation").modal("show");
			},
			showAdminCancellationModal() {
				this.setApplicableCancellers();
				this.$set(this.singleOrderData.orderCancellation, 'canceller', 'admin');
				$("#modal-order-cancellation").modal("show");
			},
			cancelOrder() {

				// if (this.singleOrderData.orderCancellation.canceller==='merchants' || this.singleOrderData.orderCancellation.canceller === 'rider') {

					if (this.singleOrderData.orderCancellation.canceller==='merchants' && (! this.singleOrderData.orderCancellation.merchant_id || typeof this.merchantCancelledOrder(this.singleOrderData.order.merchant_order_cancellations, this.singleOrderData.orderCancellation.merchant_id) !== 'undefined')) {	

						this.errors.orderCancellation.merchant = 'Merchant name is required';
						this.submitCancellationForm = false;
						return;

					}
					
					if (!this.singleOrderData.orderCancellation.cancellation_reason_id) {

						this.errors.orderCancellation.reason = 'Reason is required';
						this.submitCancellationForm = false;
						return;
						
					}

				// }
				
				// $("#modal-confirmOrCancel-order").modal("hide");
				
				axios
					.put('/orders/'+this.singleOrderData.order.id+'/'+this.perPage+'?page='+ this.pagination.current_page, this.singleOrderData.orderCancellation)
					.then(response => {
						if (response.status == 200) {

							this.allOrders = response.data;
							this.showListDataForSelectedTab();
							// this.updateCurrentOrder();
							this.fetchExpectedOrderDetail(this.singleOrderData.order);

							this.formSubmitionMode = false;

							$("#modal-order-cancellation").modal("hide");
							$("#modal-show-order").modal("hide");
							
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
			defineOrderType(order){
				if (order.type=='reservation') {
					return 'Reservation';
				}
				else
					return 'Order';
			},
			reservationOrder(order){
				if (order.type=='reservation') {
					return true;
				}

				return false;
			},
			orderIsConfirmed(order){
				return order.customer_confirmation===1 ? true : false;
			},
			orderToBeConfirmed(order){
				return order.customer_confirmation===-1 ? true : false;
			},
			orderIsWaitingForRider(order) {

				if (Array.isArray(order.merchants) && order.merchants.length) {

					return this.orderIsConfirmed(order) && order.merchants.some(merchantOrder => merchantOrder.is_accepted == null);
				}

				return false;

			},
			customerCancelledOrder(order){
				return order.customer_confirmation===0 ? true : false;
			},
			orderIsStopped(order){
				return order.in_progress==0 ? true : false;
			},
			orderIsFailed(order){
				return this.orderIsConfirmed(order) && this.orderIsStopped(order) && order.is_completed===0 ? true : false;
			},
			orderIsPicked(order) {

				if (Array.isArray(order.collections)) {
					
					return order.collections.some(
						orderPickUpProgression => orderPickUpProgression.is_collected==1
					);

				}

				return false;

			},
			orderIsDelivered(order){

				return Boolean(order.rider_assigned && order.rider_assigned.is_delivered==1);

			},
			orderIsSelfDelivered(merchantOrder){

				return merchantOrder.is_delivered;

			},
			orderIsServed(order){

				return Boolean(order.order_serve_confirmation && order.order_serve_confirmation.is_served==1);

			},
			orderIsReturned(order){

				return Boolean(order.rider_assigned && order.rider_assigned.is_delivered==2);

			},
			// completed order
			orderIsServedOrDelivered(order) {

				return Boolean((order.rider_assigned && order.rider_assigned.is_delivered==1) || (order.order_serve_confirmation && order.order_serve_confirmation.is_served==1) || this.orderIsReturned(order));

			},
			merchantOrderIsSelfDeliverable(merchantOrder) {

				return merchantOrder.is_self_delivery == 1;

			},
			orderServingOrDeliveringStatus() {

				if (this.orderIsDelivered(order)) {
					return 'Deliverd';
				}
				else if (this.orderIsServed(order)) {
					return 'Served';
				}
				else if (this.orderIsReturned(order)) {
					return 'Returned';
				}

			},
			riderIsAssigned(order) {
				if (order.rider_assigned) {
					return true;
				}
				return false;
			},
			orderAcceptanceClass(merchantOrderRecord) {
				if (merchantOrderRecord.is_accepted==-1) {
					return 'badge-danger';
				}else if (merchantOrderRecord.is_accepted==1) {
					return 'badge-info';
				}else 
					return 'badge-secondary';		
			},
			orderAcceptanceStatus(merchantOrderRecord) {
				if (merchantOrderRecord.is_accepted==-1) {
					return merchantOrderRecord.merchant_name + ' is ringing';
				}else if (merchantOrderRecord.is_accepted==1) {
					return merchantOrderRecord.merchant_name + ' has accepted';
				}else 
					return merchantOrderRecord.merchant_name + ' has cancelled';
			},
			orderServingClass(orderServeConfirmation) {
				if (orderServeConfirmation && orderServeConfirmation.is_served==1) {
					return 'badge-success';
				}
				else if (orderServeConfirmation && orderServeConfirmation.is_served===-1) {
					return 'badge-warning';
				}
			},
			orderServingStatus(orderServeConfirmation) {
				if (orderServeConfirmation && orderServeConfirmation.is_served==1) {
					return 'Served';
				}else if (orderServeConfirmation && orderServeConfirmation.is_served===-1) {
					'Not served yet';
				}
			},
			riderCollectionClass(riderCollection) {
				if (riderCollection.is_collected==1) {
					return 'badge-warning';
				}else if (riderCollection.is_collected==-1) {
					return 'badge-info';
				}else
					return 'badge-secondary';
			},
			//  cancelled by rider
			riderCollectionStatus(riderCollection) {

				if (riderCollection.is_collected==1) {
					return 'Collected from ' + riderCollection.merchant_name;
					// return riderCollection.rider.user_name +' collected from ' + riderCollection.merchant_name;
				}else if (riderCollection.is_collected==-1){
					return 'Not collected yet';
					// return riderCollection.rider.user_name +' not collected yet from '+riderCollection.merchant_name;
				}else {
					return 'Rider cancelled ' + riderCollection.merchant_name;
					// return riderCollection.rider.user_name +' has cancelled order of '+riderCollection.merchant_name;
				}
					
			},
			riderDeliveryClass(riderAssigned) {
				
				if (riderAssigned && riderAssigned.is_delivered==1) { 
					return 'badge-success'; 
				}
				else if (riderAssigned && riderAssigned.is_delivered==2) { 
					return 'badge-primary'; 
				} 
				else {
					return 'badge-warning';
				}

			},
			riderDeliveryStatus(riderAssigned) {

				if (riderAssigned && riderAssigned.is_delivered==1) {
					return 'Delivered';
					// return 'Delivered by ' + rider_assigned.rider.user_name;
				}else if (riderAssigned && riderAssigned.is_delivered===-1) {
					return 'On the way';
					// return rider_assigned.rider.user_name + ' is on the way';
				}else if (riderAssigned && riderAssigned.is_delivered==2) {
					return 'Dropped at office';
				}

			},
			// current merchant cancelled this order
			merchantCancelledOrder(merchantOrderCancellations, merchantId) {
				
				return merchantOrderCancellations.find(
					cancellation => (cancellation.merchant_id === merchantId)
				);

			},
			// current merchant has been picked Up
			merchantOrderIsCollected(collections, merchantId) {
				
				return collections.find(
					collection => (collection.merchant_id == merchantId && collection.is_collected==1)
				);

			},
			// current merchant is ready
			merchantOrderIsReady(orderReadyConfirmations, merchantId) {
				
				return orderReadyConfirmations.find(
					orderReady => (orderReady.merchant_id === merchantId && orderReady.is_ready==1)
				);

			},
			// current merchant has accepted
			merchantAcceptedOrder(merchantOrderAcceptances, merchantId) {
				
				return merchantOrderAcceptances.find(
					merchantOrderRecord => (merchantOrderRecord.merchant_id === merchantId && merchantOrderRecord.is_accepted==1)
				);

			},
			// current merchant is ringing
			merchantIsRinging(merchantOrderAcceptances, merchantId) {
				
				return merchantOrderAcceptances.find(
					merchantOrderRecord => (merchantOrderRecord.merchant_id === merchantId && merchantOrderRecord.is_accepted==-1)
				);

			},
			merchantLostOrder(merchantOrders, merchantId) {

				return merchantOrders.find(
					merchantOrderRecord => (merchantOrderRecord.merchant_id === merchantId && merchantOrderRecord.is_self_delivery==0 && merchantOrderRecord.is_rider_available==0)
				);

			},
			allMerchantOrderIsPicked(order){

				if (Array.isArray(order.merchants) && order.merchants.length && order.collections.length) {

					let numberMerchantsCancelled = order.merchant_order_cancellations?.length || 0;

					let numberMerchantsPicked = order.collections.filter(
						orderPickUpProgression=>orderPickUpProgression.is_collected==1
					).length;

					return numberMerchantsPicked==(order.merchants.length - numberMerchantsCancelled) ? true : false;
				
				}

				return false;

			},
			allMerchantOrderIsReady(order) {

				if (Array.isArray(order.merchants) && order.merchants.length && order.merchants.length) {

					let numberMerchantsCancelled = order.merchant_order_cancellations?.length || 0;

					let numberMerchantsReady = order.merchants.filter(
						readyMerchant => readyMerchant.is_ready==1
					).length;

					return numberMerchantsReady == (order.merchants.length - numberMerchantsCancelled) ? true : false;
				
				}

				return false;

			},
			allMerchantsCancelledOrder(order) {

				return order.hasOwnProperty('merchant_order_cancellations') && order.merchants.length == order.merchant_order_cancellations.length ? true : false;

			},
			// initial class for every order
			primaryOrderClass(order) {

				if (this.orderToBeConfirmed(order) && ! this.orderIsFailed(order)) {
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
			orderIsPrimary(order) {

				return Boolean((this.orderToBeConfirmed(order) && ! this.orderIsFailed(order)) || this.customerCancelledOrder(order) || this.orderIsConfirmed(order));

			},
			primaryOrderStatus(order) {

				if (this.orderToBeConfirmed(order) && ! this.orderIsFailed(order)) {
					return "Unconfirmed " + (this.reservationOrder(order) ? 'Reservation' : 'Order');
				}
				else if(this.customerCancelledOrder(order)) {
					return 'Cancelled';
				}
				else if(this.orderIsConfirmed(order)) {
					return 'Customer Confirmed';
				}
				else
					return false; // required

			},
			// secondary order class definer
			secondaryOrderClass(order, merchantId) {

				let secondaryOrderStatus = this.secondaryOrderStatus(order, merchantId);

				if (secondaryOrderStatus.includes("cancelled") || secondaryOrderStatus.includes("not found")) {
					return 'badge-secondary bg-secondary';
				}else if (secondaryOrderStatus.includes("collected")) {
					return 'badge-warning bg-warning';
				}else if (secondaryOrderStatus.includes("ready")) {
					return 'badge-success bg-success';
				}else if (secondaryOrderStatus.includes("accepted") || secondaryOrderStatus.includes("Searching")) {
					return 'badge-info bg-info';
				}else if (secondaryOrderStatus.includes("ringing")) {
					return 'badge-danger bg-danger';
				}

			},
			// after call confirmation
			secondaryOrderStatus(order, merchantId) {

				// if current merchant cancelled
				if (order.hasOwnProperty('merchant_order_cancellations') && order.merchant_order_cancellations.length && typeof this.merchantCancelledOrder(order.merchant_order_cancellations, merchantId) !== 'undefined') {

					return this.merchantCancelledOrder(order.merchant_order_cancellations, merchantId).merchant_name + ' cancelled order';

				}
				else if (typeof this.merchantLostOrder(order.merchants, merchantId) !== 'undefined') {

					return 'Rider not found for ' + this.merchantLostOrder(order.merchants, merchantId).merchant_name;

				}
				// if current merchant picked up
				else if (order.collections.length && typeof this.merchantOrderIsCollected(order.collections, merchantId) !== 'undefined') {

					return 'Collected from ' + this.merchantOrderIsCollected(order.collections, merchantId).merchant_name;

				}
				// if current merchant order is ready
				else if (order.merchants.length && typeof this.merchantOrderIsReady(order.merchants, merchantId) !== 'undefined') {

					return this.merchantOrderIsReady(order.merchants, merchantId).merchant_name + ' is ready';

				}
				// if curent merchant has accepted ?
				else if (order.merchants.length && typeof this.merchantAcceptedOrder(order.merchants, merchantId) !== 'undefined') {

					return this.merchantAcceptedOrder(order.merchants, merchantId).merchant_name + ' has accepted';

				}
				// if current merchant ringing ?
				else if (order.merchants.length && typeof this.merchantIsRinging(order.merchants, merchantId) !== 'undefined') {

					return this.merchantIsRinging(order.merchants, merchantId).merchant_name + ' is ringing';

				}
				else if (this.orderIsWaitingForRider(order)) {

					return 'Searching for rider';

				}

			},
			setApplicableCancellers() {

				this.cancellers = [];

				if (! this.orderIsStopped(this.singleOrderData.order) && ! this.orderIsFailed(this.singleOrderData.order)) {

					// if confirmed order
					if (this.orderToBeConfirmed(this.singleOrderData.order)) {
						this.cancellers.push('customer');
					}
					
					// if any merchant which is not ready yet
					if (! this.allMerchantOrderIsReady(this.singleOrderData.order)){

						this.cancellers.push('merchants');

					}
					
					// Rider is assinged and not picked up any
					if (this.riderIsAssigned(this.singleOrderData.order) && ! this.singleOrderData.order.collections.some(readyMerchant=>readyMerchant.is_ready==1).length) {

						this.cancellers.push('rider');

					}

					if (this.orderIsConfirmed(this.singleOrderData.order) && ! this.orderIsServedOrDelivered(this.singleOrderData.order)) {

						this.cancellers.push('admin');

					}

				}

			}
			
		}
  	}

</script>