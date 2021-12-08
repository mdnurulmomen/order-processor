
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
		
			<div class="row" v-show="!loading">
				<div class="col-sm-12">

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">
								{{ 
									restaurantNameFromData 
								}}

								Menu-Items
							</h2>

                        	<button type="button" 
                        			@click="showRestaurantMenuItemCreateModal"
                        			class="btn btn-secondary btn-sm float-right mb-2 ml-1"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Menu-Item
					      	</button>

                        	<button type="button" 
                        			@click="showRestaurantAllMenuCategories" class="btn btn-default btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-eye" aria-hidden="true"></i>
                                Menu-Categories
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-12 float-right was-validated">
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
							<div class="table-responsive">
								<table class="table table-hover table-bordered text-center">
									
									<tbody>
									  	<tr v-show="restaurantAllMenuCategories.length"
									    	v-for="(restaurantMenuCategory, index) in restaurantAllMenuCategories"
									    	:key="restaurantMenuCategory.id"
									  	>  		
								  			<td colspan="8">
									    		<p class="font-weight-bold font-italic">
									    			{{ 
									    				restaurantMenuCategory.menu_category ? restaurantMenuCategory.menu_category.name : 'Trashed Menu Category' 
									    			}}
										    		<span 
											    		class="badge badge-danger" 
											    		v-show="restaurantMenuCategory.deleted_at"
											    	>
											    		trashed
											    	</span>
									    		</p>
										    	<table class="table">
										    		<thead>
														<tr>
															<th scope="col">#</th>
															<th scope="col">Menu Item</th>
															<th scope="col">Detail</th>
															<th scope="col">Variations</th>
															<th scope="col">Addons</th>
															<th scope="col">Price</th>
															<th scope="col">Customizable</th>
															<th scope="col">Action</th>
														</tr>
													</thead>
										    		<tbody>
												    	<tr v-show="restaurantMenuCategory.menu_items.length" 
													    	v-for="(menuItem, index) in restaurantMenuCategory.menu_items" 
													    	:key="menuItem.id"
													  	>
													    	<td scope="row">
													    		{{ index + 1 }}
													    	</td>
												    		<td>
												    			{{ menuItem.name }}

												    			<span 
														    		class="badge badge-danger" 
														    		v-show="menuItem.deleted_at"
														    	>
														    		outdated
														    	</span>

												    		</td>
												    		<td>
												    			<span 
												    				v-html="menuItem.detail"
												    			>
												    				
												    			</span>
												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="menuItem.has_variation"
												    			>
																  	<li v-for="itemVariation in menuItem.variations" 
																  		:key="itemVariation.id"
																  	>
																    	
																    	{{ 
																    		itemVariation.name 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="itemVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="!menuItem.has_variation"
												    			>
												    				Not-Available
												    			</p>
												    		</td>
												    		<td>
												    			
												    			<ul class="text-left" 
												    				v-show="menuItem.has_addon"
												    			>
																  	<li v-for="addonItem in menuItem.addons" 
																  		:key="addonItem.id"
																  	>
																    	
																    	{{ 
																    		addonItem.name 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="addonItem.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="!menuItem.has_addon"
												    			>
												    				Not-Available
												    			</p>

												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="menuItem.has_variation && menuItem.variations.length"
												    			>
																  	<li v-for="itemVariation in menuItem.variations" 
																  		:key="itemVariation.id"
																  	>
																    	
																    	{{ 
																    		itemVariation.pivot.price 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="itemVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			{{ 
												    				!menuItem.has_variation ? menuItem.price : ''
												    			}}
												    		</td>
												    		<td>
												    			{{ menuItem.customizable ? 'Customizable' : 'Not-Customizable' }}
												    		</td>

												    		<td>
														      	<button type="button" 
														      			v-show="!menuItem.deleted_at" 
														      			@click="showRestaurantMenuItemEditModal(menuItem)" 
														      			class="btn btn-primary btn-sm">
														        	<i class="fas fa-edit"></i>
														        	Edit
														      	</button>
												      			<button type="button" 
												      					v-show="!menuItem.deleted_at"  
												        				@click="showRestaurantMenuItemDeletionModal(menuItem)"
												        				class="btn btn-danger btn-sm"
											      				>
												        			<i class="fas fa-trash-alt"></i>
												        			Delete
												      			</button>
												      			<button type="button" 
												      					v-show="!restaurantMenuCategory.deleted_at && menuItem.deleted_at"  
												        				@click="showRestaurantMenuItemRestoreModal(menuItem)"
												        				class="btn btn-danger btn-sm"
											      				>
												        			<i class="fas fa-undo-alt"></i>
												        			Restore
												      			</button>

												      			<p 
														      		class="text-danger" 
														      		v-show="restaurantMenuCategory.deleted_at && menuItem.deleted_at"
														      	>
														      		Trashed Menu-Category
														      	</p>
												    		</td>
												    	</tr>

												    	<tr v-show="!restaurantMenuCategory.menu_items.length"
												    	>
												    		<td colspan="8">
													      		<div class="alert alert-danger" role="alert">
													      			No Menu-Items Found
													      		</div>
													    	</td>
													  	</tr>
										    		</tbody>
										    	</table>
									    	</td>
									  	</tr>
									  	<tr v-show="!restaurantAllMenuCategories.length">
								    		<td colspan="8">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Menu Category Found or Trashed.
									      		</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
							<div class="row d-flex align-items-center align-content-center">
								<div class="col-sm-1">
									<select class="form-control" 
											v-model="perPage" 
											@change="changeNumberContents()"
									>
										<option>10</option>
										<option>20</option>
										<option>30</option>
										<option>40</option>
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchRestaurantAllMenuItems() : searchData()"
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
										@paginate="query === '' ? fetchRestaurantAllMenuItems() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-restaurantMenuItem -->
			<div class="modal fade" id="modal-createOrEdit-restaurantMenuItem">
				<div class="modal-dialog modal-lg">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ 
						  			editMode ? 'Edit' : 'Create' 
						  		}} 
						  		{{ 
						  			restaurantNameFromData 
						  		}}

						  		Menu-Item
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateRestaurantMenuItem() : storeRestaurantMenuItem()"
						  	autocomplete="off"
					  	>
							<div class="modal-body text-dark">

					      		<input 
						      		type="hidden" 
						      		name="_token" 
						      		:value="csrf"
					      		>

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row d-flex">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Menu-Category
								              		</label>

									                <div class="col-sm-5">  	
									                  	<multiselect 
				                                  			v-model="singleRestaurantMenuItemData.restaurantMenuCategoryObject"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantShowableMenuCategories" 
					                                  		:custom-label="menuCategoryName" 
					                                  		track-by="id" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantMenuItem.restaurantMenuCategory  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
					                                  		@close="validateFormInput('restaurantMenuItem.restaurantMenuCategory')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.restaurantMenuItem.restaurantMenuCategory 
												        	}}
												  		</div>
									                </div>

									                <div class="col-sm-3 align-self-center">
					                                	<button 
					                                		type="button" 
										        			@click="showRestaurantAddMenuCategoryCreateModal"
										        			class="btn btn-secondary btn-sm p-0"
									      				>
										        			<i class="fa fa-plus-circle" aria-hidden="true"></i>
										        			More Category
										      			</button>
					                              	</div> 
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Item Name 
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleRestaurantMenuItemData.restaurantMenuItem.name" 
															placeholder="Menu Name" 
															required="true"
															:class="!errors.restaurantMenuItem.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantMenuItem.name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.restaurantMenuItem.name 
												        	}}
												  		</div>

									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Item Detail 
								              		</label>

									                <div class="col-sm-8">
									                  	<ckeditor 
							                              	class="form-control" 
							                              	:editor="editor" 
							                              	v-model="singleRestaurantMenuItemData.restaurantMenuItem.detail"
							                            >
						                              	</ckeditor>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Variations
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleRestaurantMenuItemData.restaurantMenuItem.has_variation" 
				                                    		:width="140"  
				                                    		:height="30" 
				                                    		:font-size="15" 
				                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
				                                    		:labels="{checked: 'Available', unchecked: 'Not-Available' }"
				                                    	/>

									                </div>	
									              	
								              	</div>

								              	<div 
								              		class="card card-body mb-4" 
								              		v-show="singleRestaurantMenuItemData.restaurantMenuItem.has_variation"
								              	>
									              	<div 
									              		class="form-group row" 
									              		v-for="(value, index) in variationIndex"
									              	>
									              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Variation Name
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<multiselect 
					                                  			v-model="variationObjects[index]"
					                                  			placeholder="Variation Name" 
						                                  		:options="allVariations" 
						                                  		label="name" 
						                                  		track-by="id" 
						                                  		:key="index"
						                                  		:required="true"
						                                  		:allow-empty="false" 
						                                  		:class="!errors.restaurantMenuItem.variationName  ? 'is-valid' : 'is-invalid'"
						                                  		selectLabel = "Press/Click"
						                                  		deselect-label="One selection is required" 
						                                  		@close="validateFormInput('restaurantMenuItem.variationName')"
					                                  		>
						                                	</multiselect>

						                                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantMenuItem.variationName 
													        	}}
													  		</div>
										                </div>	
									              	 		
									              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Variation Price
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<input 
																type="number" 
																class="form-control" 
																v-model.number="price_item_variations[index]" 
																placeholder="Item Price" 
																:key="index" 
																:required="singleRestaurantMenuItemData.restaurantMenuItem.has_variation ? true : false" 
																min="0" 
																step=".1"  
																:class="!errors.restaurantMenuItem.price  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('restaurantMenuItem.price')"
										                	>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantMenuItem.price 
													        	}}
													  		</div>
										                </div>	
									              	</div>

								              		<div class="col-sm-12 text-right">
											        	
											        	<i 
												        	class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" 
												        	aria-hidden="true" 
												        	@click="variationIndex.push(variationIndex[variationIndex.length-1]+1)"
											        	>
											        		
											        	</i>
								              		
											        	<i 
											        		class="fas fa-minus-circle fa-2x  rounded-circle bg-info" aria-hidden="true" 
											        		@click="variationIndex.pop();variationObjects.pop();price_item_variations.pop()" 
								              				v-show="variationIndex.length>2" 
											        	>
											        		
											        	</i>

								              		</div>

								              	</div>

								              	<div 
								              		class="form-group row" 
								              		v-show="!singleRestaurantMenuItemData.restaurantMenuItem.has_variation"
								              	>
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Price
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<input 
															type="number" 
															class="form-control" 
															v-model="singleRestaurantMenuItemData.restaurantMenuItem.price" 
															placeholder="Item Price" 
															:required="singleRestaurantMenuItemData.restaurantMenuItem.has_variation ? false : true" 
															min="0" 
															step=".1" 
															:class="!errors.restaurantMenuItem.price  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantMenuItem.price')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.restaurantMenuItem.price 
												        	}}
												  		</div>

									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Addons
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleRestaurantMenuItemData.restaurantMenuItem.has_addon" 
				                                    		:width="140"  
				                                    		:height="30" 
				                                    		:font-size="15" 
				                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
				                                    		:labels="{checked: 'Available', unchecked: 'Not-Available' }"
				                                    	/>

									                </div>	
									              	
								              	</div>

								              	<div 
								              		class="card card-body mb-4" 
								              		v-show="singleRestaurantMenuItemData.restaurantMenuItem.has_addon"
								              	>
									              	<div 
									              		class="form-group row" 
									              		v-for="(value, index) in addonIndex"
									              	>
									              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Addon Name
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<multiselect 
					                                  			v-model="addonObjects[index]"
					                                  			placeholder="Addon Name" 
						                                  		:options="allAddons" 
						                                  		label="name" 
						                                  		track-by="id" 
						                                  		:key="index"
						                                  		:required="true" 
						                                  		:class="!errors.restaurantMenuItem.addonName  ? 'is-valid' : 'is-invalid'"
						                                  		:allow-empty="false"
						                                  		selectLabel = "Press/Click"
						                                  		deselect-label="One selection is required" 
						                                  		@close="validateFormInput('restaurantMenuItem.addonName')"
					                                  		>
						                                	</multiselect>

						                                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantMenuItem.addonName 
													        	}}
													  		</div>
										                </div>	
									              	 		
									              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Addon Price
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<input 
																type="number" 
																class="form-control" 
																v-model.number="price_addon_items[index]" 
																placeholder="Addon Price" 
																:key="index" 
																:required="singleRestaurantMenuItemData.restaurantMenuItem.has_addon ? true : false" 
																min="0" 
																step=".1" 
																:class="!errors.restaurantMenuItem.price  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('restaurantMenuItem.price')"
										                	>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.restaurantMenuItem.price 
													        	}}
													  		</div>
										                </div>	
									              	</div>

								              		<div class="col-sm-12 text-right">
											        	
											        	<i 
												        	class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" 
												        	aria-hidden="true" 
												        	@click="addonIndex.push(addonIndex[addonIndex.length-1]+1)"
											        	>
											        		
											        	</i>
								              		
											        	<i 
											        		class="fas fa-minus-circle fa-2x  rounded-circle bg-info" aria-hidden="true" 
											        		@click="addonIndex.pop();addonObjects.pop();price_addon_items.pop()" 
								              				v-show="addonIndex.length>1" 
											        	>
											        		
											        	</i>

								              		</div>

								              	</div>

								              	<div class="form-group row">	
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Customizable 
								              		</label>

									                <div class="col-sm-8">
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleRestaurantMenuItemData.restaurantMenuItem.customizable" 
				                                    		:width="140"  
				                                    		:height="30" 
				                                    		:font-size="15" 
				                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
				                                    		:labels="{checked: 'Enabled', unchecked: 'Disabled' }"
				                                    	/>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Promoted 
								              		</label>

									                <div class="col-sm-8">
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleRestaurantMenuItemData.restaurantMenuItem.promoted" 
				                                    		:width="140"  
				                                    		:height="30" 
				                                    		:font-size="15" 
				                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
				                                    		:labels="{checked: 'Promoted', unchecked: 'No' }"
				                                    	/>
									                </div>	
								              	</div>
								            </div>
								            <!-- /.card-body -->
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer flex-column">
								<div class="col-sm-12 text-right">
									<span 
										class="text-white small" 
										v-show="!submitForm"
									>
								  		Please input all required fields
								  	</span>
								</div>
								<div class="col-sm-12 d-flex justify-content-between">
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
								  		:disabled="!submitForm" 
								  	>
								  		{{ editMode ? 'Update' : 'Create' }} Restaurant-Menu
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-restaurantMenuItem-->

			<!-- /.modal-modal-add-menu-category -->
			<div class="modal fade" id="modal-add-menu-category">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Add Restaurant Menu-Category</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="addRestaurantNewMenuCategory" 
						  	autocomplete="off"
					  	>
							<div class="modal-body text-dark">

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
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Menu Category Names
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleRestaurantMenuItemData.restaurantNewMenuCategoryObjects"
				                                  			placeholder="Category Name" 
					                                  		:options="allMenuCategories" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantNewMenuCategory.menuCategory  ? 'is-valid' : 'is-invalid'" 
					                                  		:multiple="true"  
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
					                                  		@close="validateFormInput('restaurantNewMenuCategory.menuCategory')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.restaurantNewMenuCategory.menuCategory 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Restaurant Name
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleRestaurantMenuItemData.restaurantObjectToAddMenuCategory"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantArrayToAddNewMenuCategory" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:required="true"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
				                                  		>
					                                	</multiselect>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Serving From
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleRestaurantMenuItemData.restaurantNewMenuCategory.serving_from"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantScheduleHours" 
					                                  		:show-labels="false"  
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
				                                  		>
					                                	</multiselect>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Serving to
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleRestaurantMenuItemData.restaurantNewMenuCategory.serving_to"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantScheduleHours" 
					                                  		:show-labels="false"  
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
				                                  		>
					                                	</multiselect>
									                </div>	
									              	
								              	</div>
								            </div>
								            <!-- /.card-body -->
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer flex-column">
								<div class="col-sm-12 text-right">
									<span 
										class="text-white small" 
										v-show="!submitForm"
									>
								  		Please input all required fields
								  	</span>
								</div>
								<div class="col-sm-12 d-flex justify-content-between">
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
								  		:disabled="!submitForm" 
								  	>
								  		Add Menu-Category
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-modal-add-menu-category -->

			<!-- modal-restaurantMenuItem-delete-confirmation -->
			<div class="modal fade" id="modal-restaurantMenuItem-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant-Menu Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
				  			  v-on:submit.prevent="destroyRestaurantMenuItem" 
				  			  autocomplete="off"
				  		>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete menu-item ??</h5>
					      		<h5 style="color:#c6cacc">
					      			<small>
						      			Once you delete, you can't retreive it again.
						      		</small>
					      		</h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">
							  		Close
							  	</button>

							  	<button 
							  		type="submit" 
							  		class="btn btn-outline-light"
							  	>
							  		Delete
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-restaurantMenuItem-delete-confirmation -->

			<!-- modal-restaurantMenuItem-restore-confirmation -->
			<div class="modal fade" id="modal-restaurantMenuItem-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant-Menu Restore</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
				  			  v-on:submit.prevent="restoreRestaurantMenuItem" 
				  			  autocomplete="off"
				  		>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore menu-item ??</h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">
							  		Close
							  	</button>

							  	<button 
							  		type="submit" 
							  		class="btn btn-outline-light"
							  	>
							  		Restore
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /modal-restaurantMenuItem-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import { ToggleButton } from 'vue-js-toggle-button';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	var singleRestaurantMenuItemData = {

    	// variationIndex : [0, 1],
    	// variationObjects : [{}, {}],

    	restaurantNewMenuCategoryObjects : [],

    	restaurantObjectToAddMenuCategory : {
    		
    	},

    	restaurantMenuCategoryObject : {
			
    	},

    	restaurantNewMenuCategory : {
			menu_category_id : [],
    		serving_from : '10.00',
    		serving_to : '22.00',
    		restaurant_id : null,
    		from_menu_item_index : true,
    	},

    	restaurantMenuItem : {
			name : null,
			detail : null,
			price : 0,
			has_variation : false,
			has_addon : false,
			customizable : false,
			restaurant_menu_category_id : null,

			variations_id : [],
			price_item_variations : [],

			addons_id : [],
			price_addon_items : [],
    	}
    };

	var restaurantMenuListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,

    	editor: ClassicEditor,

    	allVariations : [],
    	variationObjects : [],
    	price_item_variations : [],
    	variationIndex : [0, 1],

    	allAddons : [],
    	addonObjects : [],
    	price_addon_items : [],
    	addonIndex : [0],
    	
    	allMenuCategories : [],

    	restaurantAllMenuCategories : [],
    	restaurantArrayToAddNewMenuCategory : [],

    	restaurantScheduleHours : [
    		'6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00', '20.00', '21.00', '22.00', '23.00', '24.00', '1.00', '2.00'
    	],


    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		restaurantMenuItem : {},
    		restaurantNewMenuCategory : {}
    	},

        singleRestaurantMenuItemData : singleRestaurantMenuItemData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

		// Local registration of components
		components: {
			Multiselect, // short form of Multiselect : Multiselect
			ToggleButton : ToggleButton, 
			ckeditor: CKEditor.component,
		},

		props: {

			restaurantId:{
				type: Number,
				required: true,
			},
			restaurantName:{
				type: String,
				required: true,
			},

		},

	    data() {
	        return restaurantMenuListData;
		},

		created(){
			this.fetchAllAddons();
			this.fetchAllVariations();
			this.fetchAllMenuCategories();
			this.fetchRestaurantAllMenuItems();
		},

		computed: {
		    // a computed getter
		    restaurantNameFromData: function () {
		      // `this` points to the vm instance
		      if (this.restaurantAllMenuCategories.length) {
	      		return this.restaurantAllMenuCategories[0].restaurant.name;
		      }
		      else if (this.restaurantName) {
		      	return this.restaurantName;
		      }

		      return 'Current Restaurant';
		    },
		    // a computed getter
		    restaurantShowableMenuCategories: function () {
		    	var array = [];
		      	// `this` points to the vm instance
		      	if (this.restaurantAllMenuCategories.length) {
		      		array = this.restaurantAllMenuCategories.filter(restaurantMenuCategory=>{
						return restaurantMenuCategory.deleted_at === null;
					});
		      	}

		      	return array;
		    },
		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchRestaurantAllMenuItems();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			variationObjects : function(variationObjects){
				if (variationObjects.length > 0) {
					
					let array = [];
					$.each(variationObjects, function(key, value) {
				     	array.push(value.id);
				   	});
			     	this.singleRestaurantMenuItemData.restaurantMenuItem.variations_id = array;

				}
				else
					this.singleRestaurantMenuItemData.restaurantMenuItem.variations_id = [];
			},
			addonObjects : function(addonObjects){
				if (addonObjects.length > 0) {
					
					let array = [];
					$.each(addonObjects, function(key, value) {
				     	array.push(value.id);
				   	});
			     	this.singleRestaurantMenuItemData.restaurantMenuItem.addons_id = array;

				}
				else
					this.singleRestaurantMenuItemData.restaurantMenuItem.addons_id = [];
			},
			'singleRestaurantMenuItemData.restaurantMenuCategoryObject' : function(restaurantMenuCategoryObject){

				if (restaurantMenuCategoryObject) {
					this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_menu_category_id = restaurantMenuCategoryObject.id;
				}else
					this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_menu_category_id = null;
			},
			'singleRestaurantMenuItemData.restaurantObjectToAddMenuCategory' : function(restaurantObjectToAddMenuCategory){

				if (restaurantObjectToAddMenuCategory) {
					this.singleRestaurantMenuItemData.restaurantNewMenuCategory.restaurant_id = restaurantObjectToAddMenuCategory.id;
				}else
					this.singleRestaurantMenuItemData.restaurantNewMenuCategory.restaurant_id = null;
			},
			'singleRestaurantMenuItemData.restaurantNewMenuCategoryObjects' : function(restaurantNewMenuCategoryObjects){
				if (restaurantNewMenuCategoryObjects.length > 0) {
					
					let array = [];
					$.each(restaurantNewMenuCategoryObjects, function(key, value) {
				     	array.push(value.id);
				   	});
			     	this.singleRestaurantMenuItemData.restaurantNewMenuCategory.menu_category_id = array;

				}
				else
					this.singleRestaurantMenuItemData.restaurantNewMenuCategory.menu_category_id = [];
			},

		},

		methods : {

			fetchAllAddons(){
				this.loading = true;
				axios
					.get('/api/add-ons/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allAddons = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllVariations(){
				this.loading = true;
				axios
					.get('/api/item-variations/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allVariations = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllMenuCategories(){
				this.loading = true;
				axios
					.get('/api/menu-categories/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMenuCategories = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchRestaurantAllMenuItems(){
				this.loading = true;
				axios
					.get('/api/restaurant-menu-items/' + this.restaurantId + '/' + this.perPage + "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;

							this.restaurantAllMenuCategories = response.data.data;
							this.pagination = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			menuCategoryName({ menu_category = { name: 'Menu Category' } }) {
				return `${ menu_category.name }`;
		    },
			changeNumberContents() {

				this.pagination.current_page = 1;
				
				if (this.query === '') {
					this.fetchRestaurantAllMenuItems();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchRestaurantAllMenuItems();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
    		showRestaurantAllMenuCategories(){
				this.$router.push({
			 		name: 'expected-restaurant-menu-categories', 
			 		params: { 
			 			restaurantId : this.restaurantId,
			 			restaurantName : this.restaurantName
			 		}, 
				});
			},
			showRestaurantMenuItemCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.restaurantMenuItem = {};

				this.singleRestaurantMenuItemData.restaurantMenuCategoryObject = {};
				
				this.singleRestaurantMenuItemData.restaurantMenuItem = {};
				this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_id = this.restaurantId;

				this.variationIndex = [0, 1];
				this.variationObjects = [];
				this.price_item_variations = [];

				this.addonIndex = [0];
				this.addonObjects = [];
				this.price_addon_items = [];

				$('#modal-createOrEdit-restaurantMenuItem').modal('show');
			},
    		storeRestaurantMenuItem(){

    			if (!this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_menu_category_id || (this.singleRestaurantMenuItemData.restaurantMenuItem.has_variation && this.variationObjects.length < 2) || (this.singleRestaurantMenuItemData.restaurantMenuItem.has_addon && this.addonObjects.length < 1)) {
					
					this.validateFormInput('restaurantMenuItem.restaurantMenuCategory');
					this.validateFormInput('restaurantMenuItem.variationName');
					this.validateFormInput('restaurantMenuItem.addonName');

					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuItem').modal('hide');
				
				this.singleRestaurantMenuItemData.restaurantMenuItem.price_item_variations = this.price_item_variations;

				this.singleRestaurantMenuItemData.restaurantMenuItem.price_addon_items = this.price_addon_items;

				axios
					.post('/restaurant-menu-items/'+ this.perPage, this.singleRestaurantMenuItemData.restaurantMenuItem)
					.then(response => {

						if (response.status == 200) {
							
							this.singleRestaurantMenuItemData.restaurantMenuCategoryObject = {};

							this.singleRestaurantMenuItemData.restaurantMenuItem = {};

							this.query = '';

							this.restaurantAllMenuCategories = response.data.data;
							this.pagination = response.data;

							toastr.success(response.data.success, "Added");
							
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});
			},
			showRestaurantMenuItemEditModal(menuItem) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurantMenuItem = {};

				var restaurantMenuCategoryObjects = this.restaurantAllMenuCategories.filter(restaurantMenuCategory=>{
					return restaurantMenuCategory.id == menuItem.restaurant_menu_category_id;
				});

				this.singleRestaurantMenuItemData.restaurantMenuCategoryObject = restaurantMenuCategoryObjects[0];
				
				this.singleRestaurantMenuItemData.restaurantMenuItem = menuItem;
				this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_id = this.restaurantId;

				if (menuItem.has_variation && menuItem.variations.length) {

					this.variationIndex = [];
					this.variationObjects = [];
					this.price_item_variations = [];

					menuItem.variations.forEach(
						(value, index) => {

							if (value.pivot.deleted_at===null) {

							    this.variationIndex.push(index);
							    this.variationObjects.push(value);
							    this.price_item_variations.push(value.pivot.price);
							
							}
						}
					);

				}

				if (menuItem.has_addon && menuItem.addons.length) {

					this.addonIndex = [];
					this.addonObjects = [];
					this.price_addon_items = [];

					menuItem.addons.forEach(
						(value, index) => {

							if (value.pivot.deleted_at===null) {

							    this.addonIndex.push(index);
							    this.addonObjects.push(value);
							    this.price_addon_items.push(value.pivot.price);
							
							}
						}
					);

				}

				$('#modal-createOrEdit-restaurantMenuItem').modal('show');
			},
			updateRestaurantMenuItem(){

				if (!this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_menu_category_id || (this.singleRestaurantMenuItemData.restaurantMenuItem.has_variation && this.variationObjects.length < 2) || (this.singleRestaurantMenuItemData.restaurantMenuItem.has_addon && this.addonObjects.length < 1)) {
					
					this.validateFormInput('restaurantMenuItem.restaurantMenuCategory');
					this.validateFormInput('restaurantMenuItem.variationName');
					this.validateFormInput('restaurantMenuItem.addonName');

					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuItem').modal('hide');

				this.singleRestaurantMenuItemData.restaurantMenuItem.price_item_variations = this.price_item_variations;

				this.singleRestaurantMenuItemData.restaurantMenuItem.price_addon_items = this.price_addon_items;
				
				axios
					.put('/restaurant-menu-items/' + this.singleRestaurantMenuItemData.restaurantMenuItem.id + '/' + this.perPage, this.singleRestaurantMenuItemData.restaurantMenuItem)
					.then(response => {

						if (response.status == 200) {
							
							this.singleRestaurantMenuItemData.restaurantMenuCategoryObject = {};
							this.singleRestaurantMenuItemData.restaurantMenuItem = {};

							if (this.query === '') {
								this.restaurantAllMenuCategories = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Updated");
							
						}
						
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});

			},
			showRestaurantMenuItemDeletionModal(menuItem) {
				this.singleRestaurantMenuItemData.restaurantMenuItem = menuItem;
				$("#modal-restaurantMenuItem-delete-confirmation").modal("show");
			},
			destroyRestaurantMenuItem(){

				$("#modal-restaurantMenuItem-delete-confirmation").modal("hide");

				axios
					.delete('/restaurant-menu-items/' + this.restaurantId + '/' + this.singleRestaurantMenuItemData.restaurantMenuItem.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantMenuItemData.restaurantMenuItem = {};

							if (this.query === '') {
								this.restaurantAllMenuCategories = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Deleted");
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
			showRestaurantMenuItemRestoreModal(menuItem) {
				this.singleRestaurantMenuItemData.restaurantMenuItem = menuItem;
				$("#modal-restaurantMenuItem-restore-confirmation").modal("show");
			},
			restoreRestaurantMenuItem(){

				$("#modal-restaurantMenuItem-restore-confirmation").modal("hide");

				axios
					.patch('/restaurant-menu-items/' + this.restaurantId + '/' + this.singleRestaurantMenuItemData.restaurantMenuItem.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleRestaurantMenuItemData.restaurantMenuItem = {};

							if (this.query === '') {
								this.restaurantAllMenuCategories = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Restored");
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
					"/api/restaurant-menu-items/search/" + this.restaurantId +"/"  + this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.restaurantAllMenuCategories = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			showRestaurantAddMenuCategoryCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.restaurantNewMenuCategory = {};

				this.singleRestaurantMenuItemData.restaurantNewMenuCategory = {};
				this.singleRestaurantMenuItemData.restaurantNewMenuCategory.from_menu_item_index = true;
				
				this.singleRestaurantMenuItemData.restaurantNewMenuCategoryObjects = [];

				this.singleRestaurantMenuItemData.restaurantObjectToAddMenuCategory = {
		    		name : this.restaurantNameFromData, 
					id : this.restaurantId 
		    	};

				var array = [];
				array.push(this.singleRestaurantMenuItemData.restaurantObjectToAddMenuCategory);

				this.restaurantArrayToAddNewMenuCategory = array;

				$('#modal-add-menu-category').modal('show');
			},
			addRestaurantNewMenuCategory(){

				if (!this.singleRestaurantMenuItemData.restaurantNewMenuCategory.menu_category_id.length) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-add-menu-category').modal('hide');

				axios
					.post('/restaurant-menu-categories/'+ this.perPage, this.singleRestaurantMenuItemData.restaurantNewMenuCategory)
					.then(response => {

						if (response.status == 200) {
							
							this.singleRestaurantMenuItemData.restaurantNewMenuCategoryObjects = [];
							this.singleRestaurantMenuItemData.restaurantNewMenuCategory = {};

							this.query = '';

							this.restaurantAllMenuCategories = response.data.data;
							this.pagination = response.data;

							toastr.success(response.data.success, "Added");
							
						}
					})
					.catch(error => {
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'restaurantMenuItem.restaurantMenuCategory' :

						if (Object.keys(this.singleRestaurantMenuItemData.restaurantMenuCategoryObject).length === 0) {
							this.errors.restaurantMenuItem.restaurantMenuCategory = 'Menu category is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuItem, 'restaurantMenuCategory');
						}

						break;


					case 'restaurantMenuItem.name' :

						if (!this.singleRestaurantMenuItemData.restaurantMenuItem.name) {
							this.errors.restaurantMenuItem.name = 'Menu name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuItem, 'name');
						}

						break;


					case 'restaurantMenuItem.price' :

						if (this.singleRestaurantMenuItemData.restaurantMenuItem.price < 0) {
							this.errors.restaurantMenuItem.price = 'Price should be positive';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuItem, 'price');
						}

						break;

					case 'restaurantNewMenuCategory.menuCategory' :

						if (this.singleRestaurantMenuItemData.restaurantNewMenuCategoryObjects.length < 1) {
							this.errors.restaurantNewMenuCategory.menuCategory = 'Menu category is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantNewMenuCategory, 'menuCategory');
						}

						break;

					case 'restaurantMenuItem.variationName' :

						if (this.singleRestaurantMenuItemData.restaurantMenuItem.has_variation && this.variationObjects.length < 2) {
							this.errors.restaurantMenuItem.variationName = 'Min 2 variation name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuItem, 'variationName');
						}

						break;

					case 'restaurantMenuItem.addonName' :

						if (this.singleRestaurantMenuItemData.restaurantMenuItem.has_addon && this.addonObjects.length < 1) {
							this.errors.restaurantMenuItem.addonName = 'Addon name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuItem, 'addonName');
						}

						break;
				}
	 
			},

		}
  	}

</script>

<style scoped>
	@import '~vue-multiselect/dist/vue-multiselect.min.css';
	
	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}
</style>