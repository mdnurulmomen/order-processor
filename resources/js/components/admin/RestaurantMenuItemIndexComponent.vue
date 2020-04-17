
<template>

	<div class="container-fluid">

		<section>

			<div class="row justify-content-center vh-100" v-show="loading">
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
									restaurantAllMenuCategories.length ? restaurantAllMenuCategories[0].restaurant.name : restaurantName 
								}} 
								Menu-Items
							</h2>

                        	<button type="button" 
                        			@click="showRestaurantAllMenuCategories" class="btn btn-default btn-sm float-right mb-2 ml-1"
                        	>
					        	<i class="fa fa-eye" aria-hidden="true"></i>
                                Added Menu-Categories
					      	</button>

                        	<button type="button" 
                        			@click="showRestaurantMenuItemCreateModal"
                        			class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Menu-Item
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
									  	<tr v-show="restaurantAllMenuCategories.length"
									    	v-for="(menuCategory, index) in restaurantAllMenuCategories"
									    	:key="menuCategory.id"
									  	>  		
								  			<td colspan="8">
									    		<p class="font-weight-bold font-italic">
									    			{{ menuCategory.menu_category ? menuCategory.menu_category.name : 'Trashed Menu Category' }}
									    		</p>
										    	<table class="table">
										    		<tbody>
												    	<tr v-show="menuCategory.restaurant_menu_items.length" 
													    	v-for="(menuItem, index) in menuCategory.restaurant_menu_items" 
													    	:key="menuItem.id"
													  	>
													    	<td scope="row">
													    		{{ index + 1 }}
													    	</td>
												    		<td>
												    			{{ menuItem.name }}
												    		</td>
												    		<td>
												    			<span v-html="menuItem.detail"></span>
												    		</td>
												    		<td>
												    			{{ menuItem.has_variation ? 'Available' : 'Not-Available' }}
												    		</td>
												    		<td>
												    			{{ menuItem.has_addon ? 'Available' : 'Not-Available' }}
												    		</td>
												    		<td>
												    			{{ menuItem.price }}
												    		</td>
												    		<td>
												    			{{ menuItem.customizable ? 'Customizable' : 'Not-Customizable' }}
												    		</td>

												    		<td>
														      	<button type="button" 
														      			@click="showRestaurantMenuItemEditModal(menuItem)" 
														      			class="btn btn-primary btn-sm">
														        	<i class="fas fa-edit"></i>
														        	Edit
														      	</button>
												      			<button type="button" 
												        				@click="showRestaurantMenuItemDeletionModal(menuItem)"
												        				class="btn btn-danger btn-sm"
											      				>
												        			<i class="fas fa-trash-alt"></i>
												        			Delete
												      			</button>
												    		</td>
												    	</tr>

												    	<tr v-show="!menuCategory.restaurant_menu_items.length"
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
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} {{ restaurantAllMenuCategories.length ? restaurantAllMenuCategories[0].restaurant.name : restaurantName 
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
								              			Menu-Category : 
								              		</label>

									                <div class="col-sm-5">
									                  	
									                  	<multiselect 
				                                  			v-model="singleRestaurantMenuItemData.restaurantMenuCategoryObject"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantAllMenuCategories" 
					                                  		:custom-label="menuCategoryName" 
					                                  		track-by="id" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantMenuItem.menuCategory  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="One selection is required"
					                                  		@close="validateFormInput('restaurantMenuItem.menuCategory')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.restaurantMenuItem.menuCategory }}
												  		</div>
									                </div>

									                <div class="col-sm-3 align-self-center">
					                                	<button type="button" 
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
												        	{{ errors.restaurantMenuItem.name }}
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
								              			Price
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<input 
															type="number" 
															class="form-control" 
															v-model="singleRestaurantMenuItemData.restaurantMenuItem.price" 
															placeholder="Item Price" 
															required="true" 
															min="0" 
															:class="!errors.restaurantMenuItem.price  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('restaurantMenuItem.price')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.restaurantMenuItem.price }}
												  		</div>

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
								            </div>
								            <!-- /.card-body -->
									    </div>
									</div>
								</div>
							</div>
							<div class="modal-footer justify-content-between">
								<div class="col-sm-12 text-right">
									<span class="text-danger p-0 m-0 small" 
										  v-show="!submitForm"
									>
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
							  		:disabled="!submitForm" 
							  	>
							  		{{ editMode ? 'Update' : 'Create' }} Restaurant Menu
							  	</button>
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
							<div class="modal-footer justify-content-between">
								<div class="col-sm-12 text-right">
									<span class="text-danger p-0 m-0 small" 
										  v-show="!submitForm"
									>
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
							  		:disabled="!submitForm" 
							  		class="btn btn-outline-light"
							  	>
							  		Add Menu-Category
							  	</button>
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
					      		<h5 class="text-secondary">
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
    	}
    };

	var restaurantMenuListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,

    	editor: ClassicEditor,

    	allMenuCategories : [],


    	restaurantAllMenuCategories : [],
    	restaurantArrayToAddNewMenuCategory : [],
    	restaurantScheduleHours : ['6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00', '20.00', '21.00', '22.00', '23.00', '24.00', '1.00', '2.00'],

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

		props : {
			restaurantName : {
		      	type: String,
		      	default: 'Current Restaurant'
		    },
		},

		// Local registration of components
		components: {
			Multiselect, // short form of Multiselect : Multiselect
			ToggleButton : ToggleButton, 
			ckeditor: CKEditor.component,
		},

	    data() {
	        return restaurantMenuListData;
		},

		created(){
			this.fetchAllMenuCategories();
			this.fetchRestaurantAllMenuItems();
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
					.get('/api/restaurant-menu-items/' + this.$route.params.restaurant + '/' + this.perPage + "?page=" +
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
			showRestaurantMenuItemCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors.restaurantMenuItem = {};

				this.singleRestaurantMenuItemData.restaurantMenuCategoryObject = {};
				this.singleRestaurantMenuItemData.restaurantMenuItem = {};
				this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_id = this.$route.params.restaurant;

				$('#modal-createOrEdit-restaurantMenuItem').modal('show');
			},
			showRestaurantAllMenuCategories(){
				this.$router.push({
			 		name: 'admin.restaurantMenuCategoryDetail.index', 
			 		params: { 
			 			restaurant : this.$route.params.restaurant, 
			 			restaurantName : this.restaurantAllMenuCategories.length ? this.restaurantAllMenuCategories[0].restaurant.name : this.restaurantName
			 		}, 
				});
			},
    		storeRestaurantMenuItem(){

    			if (!this.singleRestaurantMenuItemData.restaurantMenuItem.name || !this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_menu_category_id) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuItem').modal('hide');
				
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
				this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_id = this.$route.params.restaurant;

				$('#modal-createOrEdit-restaurantMenuItem').modal('show');
			},
			updateRestaurantMenuItem(){

				if (!this.singleRestaurantMenuItemData.restaurantMenuItem.name || !this.singleRestaurantMenuItemData.restaurantMenuItem.restaurant_menu_category_id) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuItem').modal('hide');
				
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
					.delete('/restaurant-menu-items/' + this.$route.params.restaurant + '/' + this.singleRestaurantMenuItemData.restaurantMenuItem.id + '/' + this.perPage)
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
		    searchData() {
				
				axios
				.get(
					"/api/restaurant-menu-items/search/" + this.$route.params.restaurant +"/"  + this.query +"/" + this.perPage +
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
		    		name : this.restaurantName, 
					id : this.$route.params.restaurant 
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

					case 'restaurantMenuItem.menuCategory' :

						if (Object.keys(this.singleRestaurantMenuItemData.restaurantMenuCategoryObject).length === 0) {
							this.errors.restaurantMenuItem.menuCategory = 'Menu category is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuItem, 'menuCategory');
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
				}
	 
			},

		}
  	}

</script>

<style scoped>
	.modal { 
		overflow: auto !important; 
	}
	.modal-body {
		word-break: break-all;
	}
</style>