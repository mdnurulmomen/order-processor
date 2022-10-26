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
									merchantName | eachcapitalize
								}}

								Products
							</h2>

                        	<button type="button" 
                        			@click="showMerchantProductCreateModal"
                        			class="btn btn-secondary btn-sm float-right mb-2 ml-1"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Product
					      	</button>

                        	<button type="button" 
                        			@click="showMerchantAllProductCategories" class="btn btn-default btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-eye" aria-hidden="true"></i>
                                Product-Categories
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
									  	<tr v-show="merchantAllProductCategories.length"
									    	v-for="(merchantProductCategory, index) in merchantAllProductCategories"
									    	:key="merchantProductCategory.id"
									  	>  		
								  			<td colspan="8">
									    		<p class="font-weight-bold font-italic">
									    			{{ 
									    				merchantProductCategory.product_category ? merchantProductCategory.product_category.name : 'Trashed Product Category' 
									    			}}
										    		<span 
											    		class="badge badge-danger" 
											    		v-show="merchantProductCategory.deleted_at"
											    	>
											    		trashed
											    	</span>
									    		</p>
										    	<table class="table">
										    		<thead>
														<tr>
															<th scope="col">#</th>
															<th scope="col">Product</th>
															<th scope="col">Detail</th>
															<th scope="col">Variations</th>
															<th scope="col">Addons</th>
															<th scope="col">Price</th>
															<th scope="col">Customizable</th>
															<th scope="col">Action</th>
														</tr>
													</thead>
										    		<tbody>
												    	<tr 
												    		v-show="merchantProductCategory.merchant_products.length" 
													    	v-for="(merchantProduct, index) in merchantProductCategory.merchant_products" 
													    	:key="merchantProduct.id"
													  	>
													    	<td scope="row">
													    		{{ index + 1 }}
													    	</td>
												    		<td>
												    			{{ merchantProduct.name | eachcapitalize }}

												    			<span 
														    		class="badge badge-danger" 
														    		v-show="merchantProduct.deleted_at"
														    	>
														    		outdated
														    	</span>

												    		</td>
												    		<td>
												    			<span 
												    				v-html="merchantProduct.detail"
												    			>
												    				
												    			</span>
												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="merchantProduct.has_variation"
												    			>
																  	<li v-for="productVariation in merchantProduct.variations" 
																  		:key="productVariation.id"
																  	>
																    	
																    	{{ 
																    		productVariation.name | capitalize
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="productVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="! merchantProduct.has_variation"
												    			>
												    				Not-Available
												    			</p>
												    		</td>
												    		<td>
												    			
												    			<ul class="text-left" 
												    				v-show="merchantProduct.has_addon"
												    			>
																  	<li v-for="merchantProductAddon in merchantProduct.addons" 
																  		:key="merchantProductAddon.id"
																  	>
																    	
																    	{{ 
																    		merchantProductAddon.name | capitalize
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="merchantProductAddon.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			<p class="text-danger" 
												    				v-show="! merchantProduct.has_addon"
												    			>
												    				Not-Available
												    			</p>

												    		</td>
												    		<td>
												    			<ul class="text-left" 
												    				v-show="merchantProduct.has_variation && merchantProduct.variations.length"
												    			>
																  	<li v-for="productVariation in merchantProduct.variations" 
																  		:key="productVariation.id"
																  	>
																    	
																    	{{ 
																    		productVariation.pivot.price 
																    	}}

																    	<span 
																    		class="badge badge-danger" 
																    		v-show="productVariation.pivot.deleted_at"
																    	>
																    		outdated
																    	</span>
																  	</li>
																</ul>

												    			{{ 
												    				! merchantProduct.has_variation ? merchantProduct.price : ''
												    			}}
												    		</td>
												    		<td>
												    			{{ merchantProduct.customizable ? 'Customizable' : 'Not-Customizable' }}
												    		</td>

												    		<td>
														      	<button type="button" 
														      			v-show="!merchantProduct.deleted_at" 
														      			@click="showMerchantProductEditModal(merchantProduct)" 
														      			class="btn btn-primary btn-sm">
														        	<i class="fas fa-edit"></i>
														        	Edit
														      	</button>
												      			<button type="button" 
												      					v-show="!merchantProduct.deleted_at"  
												        				@click="showMerchantProductDeletionModal(merchantProduct)"
												        				class="btn btn-danger btn-sm"
											      				>
												        			<i class="fas fa-trash-alt"></i>
												        			Delete
												      			</button>
												      			<button type="button" 
												      					v-show="!merchantProductCategory.deleted_at && merchantProduct.deleted_at"  
												        				@click="showMerchantProductRestoreModal(merchantProduct)"
												        				class="btn btn-danger btn-sm"
											      				>
												        			<i class="fas fa-undo-alt"></i>
												        			Restore
												      			</button>

												      			<p 
														      		class="text-danger" 
														      		v-show="merchantProductCategory.deleted_at && merchantProduct.deleted_at"
														      	>
														      		Trashed Product-Category
														      	</p>
												    		</td>
												    	</tr>

												    	<tr v-show="!merchantProductCategory.merchant_products.length"
												    	>
												    		<td colspan="8">
													      		<div class="alert alert-danger" role="alert">
													      			No Products Found
													      		</div>
													    	</td>
													  	</tr>
										    		</tbody>
										    	</table>
									    	</td>
									  	</tr>
									  	<tr v-show="!merchantAllProductCategories.length">
								    		<td colspan="8">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Product Category Found or Trashed.
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
										@click="query === '' ? fetchMerchantAllProducts() : searchData()"
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
										@paginate="query === '' ? fetchMerchantAllProducts() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-merchantProduct -->
			<div class="modal fade" id="modal-createOrEdit-merchantProduct">
				<div class="modal-dialog modal-lg">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ 
						  			editMode ? 'Edit' : 'Create' 
						  		}} 
						  		{{ 
						  			merchantName | capitalize 
						  		}}

						  		Product
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateMerchantProduct() : storeMerchantProduct()"
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
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Product-Category
								              		</label>

									                <div class="col-sm-5">  	
									                  	<multiselect 
				                                  			v-model="singleMerchantProductData.merchantProductCategory"
				                                  			placeholder="Category Name" 
					                                  		:options="merchantAllProductCategories" 
					                                  		:custom-label="productCategoryName" 
					                                  		track-by="id" 
					                                  		:required="true" 
					                                  		class="form-control p-0" 
					                                  		:class="!errors.merchantProduct.merchantProductCategory ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
					                                  		@close="validateFormInput('merchantProduct.merchantProductCategory')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantProduct.merchantProductCategory 
												        	}}
												  		</div>
									                </div>

									                <div class="col-sm-3 align-self-center">
					                                	<button 
					                                		type="button" 
										        			@click="showMerchantProductCategoryCreateModal"
										        			class="btn btn-secondary btn-sm p-0"
									      				>
										        			<i class="fa fa-plus-circle" aria-hidden="true"></i>
										        			More Category
										      			</button>
					                              	</div> 
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Product Name 
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMerchantProductData.merchantProduct.name" 
															placeholder="Product Name" 
															required="true"
															:class="!errors.merchantProduct.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('merchantProduct.name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantProduct.name 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Product Detail 
								              		</label>

									                <div class="col-sm-8">
									                  	<ckeditor 
							                              	class="form-control" 
							                              	:editor="editor" 
							                              	v-model="singleMerchantProductData.merchantProduct.detail"
							                            >
						                              	</ckeditor>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Variations
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleMerchantProductData.merchantProduct.has_variation" 
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
								              		v-show="singleMerchantProductData.merchantProduct.has_variation"
								              	>
									              	<div 
									              		class="form-group row" 
									              		v-for="(value, index) in variationIndex"
									              	>
									              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Variation Name
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<multiselect 
					                                  			v-model="merchantProductVariations[index]"
					                                  			placeholder="Variation Name" 
						                                  		:options="allVariations" 
						                                  		label="name" 
						                                  		track-by="id" 
						                                  		:key="index"
						                                  		:required="true"
						                                  		:allow-empty="false" 
						                                  		:class="!errors.merchantProduct.variationName  ? 'is-valid' : 'is-invalid'"
						                                  		selectLabel = "Press/Click"
						                                  		deselect-label="One selection is required" 
						                                  		@close="validateFormInput('merchantProduct.variationName')"
					                                  		>
						                                	</multiselect>

						                                	<div class="invalid-feedback">
													        	{{ 
													        		errors.merchantProduct.variationName 
													        	}}
													  		</div>
										                </div>	
									              	 		
									              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Variation Price
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<input 
																type="number" 
																class="form-control" 
																v-model.number="priceVariations[index]" 
																placeholder="Product Price" 
																:key="index" 
																:required="singleMerchantProductData.merchantProduct.has_variation ? true : false" 
																min="0" 
																step=".1"  
																:class="!errors.merchantProduct.price  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('merchantProduct.price')"
										                	>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.merchantProduct.price 
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
											        		@click="variationIndex.pop();merchantProductVariations.pop();priceVariations.pop()" 
								              				v-show="variationIndex.length > 2" 
											        	>
											        		
											        	</i>

								              		</div>
								              	</div>

								              	<div 
								              		class="form-group row" 
								              		v-show="!singleMerchantProductData.merchantProduct.has_variation"
								              	>
									              		
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Price
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<input 
															type="number" 
															class="form-control" 
															v-model="singleMerchantProductData.merchantProduct.price" 
															placeholder="Product Price" 
															:required="singleMerchantProductData.merchantProduct.has_variation ? false : true" 
															min="0" 
															step=".1" 
															:class="!errors.merchantProduct.price  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('merchantProduct.price')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantProduct.price 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Discount
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMerchantProductData.merchantProduct.discount" 
															placeholder="Discount" 
															required="true"
															:class="!errors.merchantProduct.discount  ? 'is-valid' : 'is-invalid'" 
															@keyup="validateFormInput('merchantProduct.discount')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ 
												        		errors.merchantProduct.discount 
												        	}}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Addons
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleMerchantProductData.merchantProduct.has_addon" 
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
								              		v-show="singleMerchantProductData.merchantProduct.has_addon"
								              	>
									              	<div 
									              		class="form-group row" 
									              		v-for="(value, index) in addonIndex"
									              	>
									              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Addon Name
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<multiselect 
					                                  			v-model="merchantProductAddons[index]"
					                                  			placeholder="Addon Name" 
						                                  		:options="allAddons" 
						                                  		label="name" 
						                                  		track-by="id" 
						                                  		:key="index"
						                                  		:required="true" 
						                                  		:class="!errors.merchantProduct.addonName  ? 'is-valid' : 'is-invalid'"
						                                  		:allow-empty="false"
						                                  		selectLabel = "Press/Click"
						                                  		deselect-label="One selection is required" 
						                                  		@close="validateFormInput('merchantProduct.addonName')"
					                                  		>
						                                	</multiselect>

						                                	<div class="invalid-feedback">
													        	{{ 
													        		errors.merchantProduct.addonName 
													        	}}
													  		</div>
										                </div>	
									              	 		
									              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right mb-2">
									              			Addon Price
									              		</label>
										                <div class="col-sm-8 mb-2">
										                	<input 
																type="number" 
																class="form-control" 
																v-model.number="priceAddons[index]" 
																placeholder="Addon Price" 
																:key="index" 
																:required="singleMerchantProductData.merchantProduct.has_addon ? true : false" 
																min="0" 
																step=".1" 
																:class="!errors.merchantProduct.price  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('merchantProduct.price')"
										                	>
										                	<div class="invalid-feedback">
													        	{{ 
													        		errors.merchantProduct.price 
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
											        		@click="addonIndex.pop();merchantProductAddons.pop();priceAddons.pop()" 
								              				v-show="addonIndex.length>1" 
											        	>
											        		
											        	</i>

								              		</div>

								              	</div>

								              	<div class="form-group row">	
								              		<label 
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Customizable 
								              		</label>

									                <div class="col-sm-8">
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleMerchantProductData.merchantProduct.customizable" 
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
								              			for="inputProductName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Promoted 
								              		</label>

									                <div class="col-sm-8">
									                  	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleMerchantProductData.merchantProduct.promoted" 
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
								  		{{ editMode ? 'Update' : 'Create' }} Merchant-Product
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-merchantProduct-->

			<!-- /.modal-modal-add-product-category -->
			<div class="modal fade" id="modal-add-product-category">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">Add Merchant Product-Category</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="addMerchantNewProductCategory" 
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
								              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right">
								              			Merchant Name
								              		</label>
									              		
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleMerchantProductData.currentMerchant"
				                                  			placeholder="Category Name" 
					                                  		:options="[]" 
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
								              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right">
								              			Product Category Names
								              		</label>
									              		
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleMerchantProductData.merchantNewProductCategories"
				                                  			placeholder="Category Name" 
					                                  		:options="allProductCategories" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:required="true"
					                                  		:class="!errors.merchantNewProductCategory.productCategory  ? 'is-valid' : 'is-invalid'" 
					                                  		:multiple="true"  
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
					                                  		@close="validateFormInput('merchantNewProductCategory.productCategory')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.merchantNewProductCategory.productCategory }}
												  		</div>
									                </div>
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right">
								              			Serving From
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleMerchantProductData.merchantNewProductCategory.serving_from"
				                                  			placeholder="Category Name" 
					                                  		:options="merchantScheduleHours" 
					                                  		:show-labels="false"  
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
				                                  		>
					                                	</multiselect>
									                </div>
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputProductName3" class="col-sm-4 col-form-label text-right">
								              			Serving to
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleMerchantProductData.merchantNewProductCategory.serving_to"
				                                  			placeholder="Category Name" 
					                                  		:options="merchantScheduleHours" 
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
								  		Add Product-Category
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-modal-add-product-category -->

			<!-- modal-merchantProduct-delete-confirmation -->
			<div class="modal fade" id="modal-merchantProduct-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Merchant-Product Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
				  			  v-on:submit.prevent="destroyMerchantProduct" 
				  			  autocomplete="off"
				  		>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete product ??</h5>
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
			<!-- /modal-merchantProduct-delete-confirmation -->

			<!-- modal-merchantProduct-restore-confirmation -->
			<div class="modal fade" id="modal-merchantProduct-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Merchant-Product Restore</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
				  			  v-on:submit.prevent="restoreMerchantProduct" 
				  			  autocomplete="off"
				  		>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore product ??</h5>
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
			<!-- /modal-merchantProduct-restore-confirmation -->
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import { ToggleButton } from 'vue-js-toggle-button';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	var singleMerchantProductData = {

    	// variationIndex : [0, 1],
    	// merchantProductVariations : [{}, {}],

    	merchantNewProductCategories : [],

    	currentMerchant : {
    		
    	},

    	merchantProductCategory : {
			
    	},

    	merchantNewProductCategory : {
			productCategoriesId : [],
    		serving_from : '10.00',
    		serving_to : '22.00',
    		merchant_id : null,
    		fromMerchantProductIndex : true,
    	},

    	merchantProduct : {
			name : null,
			detail : null,
			price : 0,
			has_variation : false,
			has_addon : false,
			customizable : false,
			merchant_product_category_id : null,

			idVariations : [],
			priceVariations : [],

			idAddons : [],
			priceAddons : [],
    	}
    };

	var merchantProductData = {

      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,

    	editor: ClassicEditor,

    	allVariations : [],
    	merchantProductVariations : [],
    	priceVariations : [],
    	variationIndex : [0, 1],

    	allAddons : [],
    	merchantProductAddons : [],
    	priceAddons : [],
    	addonIndex : [0],
    	
    	allProductCategories : [],

    	merchantAllProductCategories : [],

    	merchantScheduleHours : [
    		'6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00', '20.00', '21.00', '22.00', '23.00', '24.00', '1.00', '2.00'
    	],


    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		merchantProduct : {},
    		merchantNewProductCategory : {}
    	},

        singleMerchantProductData : singleMerchantProductData,

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

			merchantId:{
				type: Number,
				required: true,
			},
			merchantName:{
				type: String,
				required: true,
			},

		},

	    data() {
	        return merchantProductData;
		},

		created(){
			this.fetchAllAddons();
			this.fetchAllVariations();
			this.fetchAllProductCategories();
			this.fetchMerchantAllProducts();
		},

		computed: {
		    // a computed getter
		    /*
		    merchantAllProductCategories: function () {

		    	var array = [];

		      	// `this` points to the vm instance
		      	if (this.merchantAllProductCategories.length) {
		      		array = this.merchantAllProductCategories.filter(merchantProductCategory=>{
						return merchantProductCategory.product_category && merchantProductCategory.deleted_at === null;
					});
		      	}

		      	return array;

		    },
		    */
		},

		watch : {

			query : function(val){
				if (val==='') {
					this.fetchMerchantAllProducts();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			},
			merchantProductVariations : function(merchantProductVariations){
				if (merchantProductVariations.length > 0) {
					
					let array = [];

					$.each(merchantProductVariations, function(key, value) {
				     	array.push(value.id);
				   	});

			     	this.singleMerchantProductData.merchantProduct.idVariations = array;

				}
				else
					this.singleMerchantProductData.merchantProduct.idVariations = [];
			},
			merchantProductAddons : function(merchantProductAddons){
				
				if (merchantProductAddons.length > 0) {
					
					let array = [];

					$.each(merchantProductAddons, function(key, value) {
				     	array.push(value.id);
				   	});

			     	this.singleMerchantProductData.merchantProduct.idAddons = array;

				}
				else
					this.singleMerchantProductData.merchantProduct.idAddons = [];
			},
			'singleMerchantProductData.merchantProductCategory' : function(merchantProductCategory){

				if (merchantProductCategory) {
					this.singleMerchantProductData.merchantProduct.merchant_product_category_id = merchantProductCategory.id;
				}else
					this.singleMerchantProductData.merchantProduct.merchant_product_category_id = null;
			},
			'singleMerchantProductData.currentMerchant' : function(currentMerchant){

				if (currentMerchant) {
					this.singleMerchantProductData.merchantNewProductCategory.merchant_id = currentMerchant.id;
				}else
					this.singleMerchantProductData.merchantNewProductCategory.merchant_id = null;
			},
			'singleMerchantProductData.merchantNewProductCategories' : function(merchantNewProductCategories){
				
				if (merchantNewProductCategories.length > 0) {
					
					let array = [];
					
					$.each(merchantNewProductCategories, function(key, value) {
				     	array.push(value.id);
				   	});

			     	this.singleMerchantProductData.merchantNewProductCategory.productCategoriesId = array;

				}else {
					this.singleMerchantProductData.merchantNewProductCategory.productCategoriesId = [];
				}
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
					.get('/api/variations/')
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
			fetchAllProductCategories(){
				this.loading = true;
				axios
					.get('/api/product-categories/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allProductCategories = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchMerchantAllProducts(){
				this.loading = true;
				axios
					.get('/api/merchant-products/' + this.merchantId + '/' + this.perPage + "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;

							this.merchantAllProductCategories = response.data.data;
							this.pagination = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			productCategoryName({ product_category = { name: 'Product Category' } }) {
				return product_category && product_category.hasOwnProperty('name') ? `${ product_category.name }` : false;
		    },
			changeNumberContents() {

				this.pagination.current_page = 1;
				
				if (this.query === '') {
					this.fetchMerchantAllProducts();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchMerchantAllProducts();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
    		showMerchantAllProductCategories(){

				this.$router.push({
			 		name: 'merchant-all-product-categories', 
			 		params: { 
			 			merchantId : this.merchantId,
			 			merchantName : this.merchantName
			 		}, 
				});

			},
			showMerchantProductCreateModal(){

				this.editMode = false;
				this.submitForm = true;
				this.errors.merchantProduct = {};

				this.singleMerchantProductData.merchantProductCategory = {};
				
				this.singleMerchantProductData.merchantProduct = {};
				this.singleMerchantProductData.merchantProduct.merchant_id = this.merchantId;

				this.variationIndex = [0, 1];
				this.merchantProductVariations = [];
				this.priceVariations = [];

				this.addonIndex = [0];
				this.merchantProductAddons = [];
				this.priceAddons = [];

				$('#modal-createOrEdit-merchantProduct').modal('show');

			},
    		storeMerchantProduct(){

    			if (!this.singleMerchantProductData.merchantProduct.merchant_product_category_id || (this.singleMerchantProductData.merchantProduct.has_variation && this.merchantProductVariations.length < 2) || (this.singleMerchantProductData.merchantProduct.has_addon && this.merchantProductAddons.length < 1)) {
					
					this.validateFormInput('merchantProduct.merchantProductCategory');
					this.validateFormInput('merchantProduct.variationName');
					this.validateFormInput('merchantProduct.addonName');

					this.submitForm = false;
					return;
				}
				
				this.singleMerchantProductData.merchantProduct.priceVariations = this.priceVariations;

				this.singleMerchantProductData.merchantProduct.priceAddons = this.priceAddons;

				axios
					.post('/merchant-products/'+ this.perPage, this.singleMerchantProductData.merchantProduct)
					.then(response => {

						if (response.status == 200) {
							
							this.singleMerchantProductData.merchantProductCategory = {};

							this.singleMerchantProductData.merchantProduct = {};

							this.query = '';

							this.merchantAllProductCategories = response.data.data;
							this.pagination = response.data;

							toastr.success(response.data.success, "Added");

							$('#modal-createOrEdit-merchantProduct').modal('hide');
							
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
			showMerchantProductEditModal(merchantProduct) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.merchantProduct = {};

				this.singleMerchantProductData.merchantProductCategory = this.merchantAllProductCategories.find(merchantProductCategory => {
					return merchantProductCategory.id == merchantProduct.merchant_product_category_id;
				});
				
				this.singleMerchantProductData.merchantProduct = merchantProduct;
				this.singleMerchantProductData.merchantProduct.merchant_id = this.merchantId;

				if (merchantProduct.has_variation && merchantProduct.variations.length) {

					this.variationIndex = [];
					this.merchantProductVariations = [];
					this.priceVariations = [];

					merchantProduct.variations.forEach(
						(value, index) => {

							if (value.pivot.deleted_at===null) {

							    this.variationIndex.push(index);
							    this.merchantProductVariations.push(value);
							    this.priceVariations.push(value.pivot.price);
							
							}
						}
					);

				}

				if (merchantProduct.has_addon && merchantProduct.addons.length) {

					this.addonIndex = [];
					this.merchantProductAddons = [];
					this.priceAddons = [];

					merchantProduct.addons.forEach(
						(value, index) => {

							if (value.pivot.deleted_at===null) {

							    this.addonIndex.push(index);
							    this.merchantProductAddons.push(value);
							    this.priceAddons.push(value.pivot.price);
							
							}
						}
					);

				}

				$('#modal-createOrEdit-merchantProduct').modal('show');
			},
			updateMerchantProduct(){

				if (!this.singleMerchantProductData.merchantProduct.merchant_product_category_id || (this.singleMerchantProductData.merchantProduct.has_variation && this.merchantProductVariations.length < 2) || (this.singleMerchantProductData.merchantProduct.has_addon && this.merchantProductAddons.length < 1)) {
					
					this.validateFormInput('merchantProduct.merchantProductCategory');
					this.validateFormInput('merchantProduct.variationName');
					this.validateFormInput('merchantProduct.addonName');

					this.submitForm = false;
					return;
				}

				this.singleMerchantProductData.merchantProduct.priceVariations = this.priceVariations;

				this.singleMerchantProductData.merchantProduct.priceAddons = this.priceAddons;
				
				axios
					.put('/merchant-products/' + this.singleMerchantProductData.merchantProduct.id + '/' + this.perPage, this.singleMerchantProductData.merchantProduct)
					.then(response => {

						if (response.status == 200) {
							
							this.singleMerchantProductData.merchantProductCategory = {};
							this.singleMerchantProductData.merchantProduct = {};

							if (this.query === '') {
								this.merchantAllProductCategories = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Updated");

							$('#modal-createOrEdit-merchantProduct').modal('hide');
							
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
			showMerchantProductDeletionModal(merchantProduct) {
				this.singleMerchantProductData.merchantProduct = merchantProduct;
				$("#modal-merchantProduct-delete-confirmation").modal("show");
			},
			destroyMerchantProduct(){

				axios
					.delete('/merchant-products/' + this.merchantId + '/' + this.singleMerchantProductData.merchantProduct.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMerchantProductData.merchantProduct = {};

							if (this.query === '') {
								this.merchantAllProductCategories = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Deleted");

							$("#modal-merchantProduct-delete-confirmation").modal("hide");
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
			showMerchantProductRestoreModal(merchantProduct) {
				this.singleMerchantProductData.merchantProduct = merchantProduct;
				$("#modal-merchantProduct-restore-confirmation").modal("show");
			},
			restoreMerchantProduct(){

				axios
					.patch('/merchant-products/' + this.merchantId + '/' + this.singleMerchantProductData.merchantProduct.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMerchantProductData.merchantProduct = {};

							if (this.query === '') {
								this.merchantAllProductCategories = response.data.data;
								this.pagination = response.data;
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Restored");

							$("#modal-merchantProduct-restore-confirmation").modal("hide");
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
					"/api/merchant-products/search/" + this.merchantId +"/"  + this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.merchantAllProductCategories = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			showMerchantProductCategoryCreateModal(){

				this.editMode = false;
				this.submitForm = true;
				this.errors.merchantNewProductCategory = {};

				this.singleMerchantProductData.merchantNewProductCategory = {};
				this.singleMerchantProductData.merchantNewProductCategory.fromMerchantProductIndex = true;
				
				this.singleMerchantProductData.merchantNewProductCategories = [];

				this.singleMerchantProductData.currentMerchant = {
		    		name : this.merchantName, 
					id : this.merchantId 
		    	};

				$('#modal-add-product-category').modal('show');
			},
			addMerchantNewProductCategory(){

				if (!this.singleMerchantProductData.merchantNewProductCategory.productCategoriesId.length) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-add-product-category').modal('hide');

				axios
					.post('/merchant-product-categories/'+ this.perPage, this.singleMerchantProductData.merchantNewProductCategory)
					.then(response => {

						if (response.status == 200) {
							
							this.singleMerchantProductData.merchantNewProductCategories = [];
							this.singleMerchantProductData.merchantNewProductCategory = {};

							this.query = '';

							this.merchantAllProductCategories = response.data.data;
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

					case 'merchantProduct.merchantProductCategory' :

						if (Object.keys(this.singleMerchantProductData.merchantProductCategory).length === 0) {
							this.errors.merchantProduct.merchantProductCategory = 'Product category is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantProduct, 'merchantProductCategory');
						}

						break;


					case 'merchantProduct.name' :

						if (!this.singleMerchantProductData.merchantProduct.name) {
							this.errors.merchantProduct.name = 'Product name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantProduct, 'name');
						}

						break;


					case 'merchantProduct.price' :

						if (this.singleMerchantProductData.merchantProduct.price < 0) {
							this.errors.merchantProduct.price = 'Price should be positive';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantProduct, 'price');
						}

						break;

					case 'merchantProduct.discount' :

						if (this.singleMerchantProductData.merchantProduct.discount < 0 || this.singleMerchantProductData.merchantProduct.discount > 100) {
							this.errors.merchantProduct.discount = 'Value should be between 0 to 100';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantProduct, 'discount');
						}

						break;

					case 'merchantNewProductCategory.productCategory' :

						if (this.singleMerchantProductData.merchantNewProductCategories.length < 1) {
							this.errors.merchantNewProductCategory.productCategory = 'Product category is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.merchantNewProductCategory, 'productCategory');
						}

						break;

					case 'merchantProduct.variationName' :

						if (this.singleMerchantProductData.merchantProduct.has_variation && this.merchantProductVariations.length < 2) {
							this.errors.merchantProduct.variationName = 'Min 2 variation name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantProduct, 'variationName');
						}

						break;

					case 'merchantProduct.addonName' :

						if (this.singleMerchantProductData.merchantProduct.has_addon && this.merchantProductAddons.length < 1) {
							this.errors.merchantProduct.addonName = 'Addon name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.merchantProduct, 'addonName');
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