
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
							<h2 class="lead float-left mt-1">
								{{ 
									restaurantName
								}}
								 
								Menu Categories
							</h2>

                        	<button 
	                        	type="button" 
	                        	@click="showRestaurantMenuCategoryCreateModal" 
	                        	class="btn btn-secondary btn-sm float-right mb-2 ml-1"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Menu categories
					      	</button>

					      	<button 
	                        	type="button" 
	                        	@click="showRestaurantMenuList" 
	                        	class="btn btn-default btn-sm float-right"
                        	>
			        			<i class="fas fa-eye"></i>
			        			All Menu-Items
			      			</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" 
									  		v-show="query === ''"
									  	>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='current' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showRestaurantCurrentMenuCategories"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a 
													:class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showRestaurantTrashedMenuCategories"
												>
													Trashed
												</a>
											</li>
										</ul>
									</div>

									<div class="col-sm-6 float-right was-validated">
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
								<table class="table table-hover table-bordered table-striped text-center">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Menu Category Name</th>
											<th scope="col">Serving From</th>
											<th scope="col">Serving To</th>
											<th scope="col">Number Menu Items</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="restaurantMenuCategoriesToShow.length"
									    	v-for="(restaurantMenuCategory, index) in restaurantMenuCategoriesToShow"
									    	:key="restaurantMenuCategory.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>
								    			{{ 
								    				restaurantMenuCategory.menu_category ? restaurantMenuCategory.menu_category.name : 'Trashed Menu-Category' 
								    			}}
								    		</td>
								    		<td>
								    			{{ restaurantMenuCategory.serving_from }}
								    		</td>
								    		<td>
								    			{{ restaurantMenuCategory.serving_to }}
								    		</td>
								    		<td>
								    			{{ 
								    				restaurantMenuCategory.menu_items.length 
								    			}}
								    		</td>
								    		<td>
										      	 
										      	<button type="button" 
										      		v-show="restaurantMenuCategory.deleted_at === null && restaurantMenuCategory.menu_category !== null"
										      		@click="showRestaurantMenuCategoryEditModal(restaurantMenuCategory)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

										      	<button 
										      		type="button" 
										      		v-show="restaurantMenuCategory.deleted_at === null"
										      		@click="showRestaurantMenuCategoryDeleteModal(restaurantMenuCategory)" class="btn btn-danger btn-sm"
										      	>
										        	<i class="fas fa-trash"></i>
										        	Delete
										      	</button>

										      	<button 
										      		type="button" 
										      		v-show="restaurantMenuCategory.deleted_at !== null && restaurantMenuCategory.menu_category && restaurantMenuCategory.menu_category.deleted_at === null"
										      		@click="showRestaurantMenuCategoryRestoreModal(restaurantMenuCategory)" class="btn btn-danger btn-sm"
										      	>
										        	<i class="fas fa-undo"></i>
										        	Restore
										      	</button>

										      	<p 
										      		class="text-danger" 
										      		v-show="restaurantMenuCategory.deleted_at !== null && restaurantMenuCategory.menu_category && restaurantMenuCategory.menu_category.deleted_at !== null"
										      	>
										      		Trashed Menu-Category
										      	</p>
 												
								    		</td>
									  	</tr>
									  	<tr v-show="!restaurantMenuCategoriesToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No Menu Category found.
									      		</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
							<div class="row d-flex align-items-center align-content-center">
								<div class="col-sm-1">
									<select class="form-control" v-model="perPage" @change="changeNumberContents()">
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
										@click="query === '' ? fetchRestaurantAllMenuCategories() : searchData()"
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
										@paginate="query === '' ? fetchRestaurantAllMenuCategories() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-restaurantMenuCategory -->
			<div class="modal fade" id="modal-createOrEdit-restaurantMenuCategory">
				<div class="modal-dialog modal-lg">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} 
						  		{{ restaurantName }} Menu-Category
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent="editMode ? updateRestaurantMenuCategory() : storeRestaurantMenuCategory()"
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
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Restaurant Name
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="restaurant"
				                                  			placeholder="Restaurant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="[]" 
					                                  		:disabled="true"
				                                  		>
					                                	</multiselect>
									                </div>	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Menu-Categories
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="singleRestaurantMenuCategoryData.menuCategoryObjects"
				                                  			placeholder="Menu Categories" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMenuCategories" 
					                                  		:required="true" 
					                                  		:multiple="!editMode" 
					                                  		:close-on-select="false"
					                                  		:class="!errors.menuCategory ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value" 
					                                  		@input="setMenuCategoryIdCollection()"
					                                  		@close="validateFormInput('restaurantMenuCategory.menuCategory')" 
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{
												        		errors.menuCategory 
												        	}}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Serving From
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleRestaurantMenuCategoryData.serving_from"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantScheduleHours" 
					                                  		:show-labels="false"  
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove is required"
				                                  		>
					                                	</multiselect>
									                </div>
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Serving To
								              		</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="singleRestaurantMenuCategoryData.serving_to"
				                                  			placeholder="Category Name" 
					                                  		:options="restaurantScheduleHours" 
					                                  		:show-labels="false"  
					                                  		selectLabel = "Click to select"
					                                  		deselect-label="Click to remove"
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
								  		{{ editMode ? 'Update' : 'Create' }} Rest. Menu-Category
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-restaurantMenuCategory-->

			<!-- modal-restaurantMenuCategory-delete-confirmation -->
			<div class="modal fade" id="modal-restaurantMenuCategory-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Restaurant-Menu-Category Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
						  	v-on:submit.prevent="destroyRestaurantMenuCategory" 
						  	autocomplete="off"
					  	>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete menu-category ??</h5>
					      		<h5 style="color:#c6cacc">
					      			<small>
					      				But once you want, you can retreive it from bin.
					      			</small>
					      		</h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>

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
			<!-- /modal-restaurantMenuCategory-delete-confirmation -->

			<!-- modal-restaurantMenuCategory-restore-confirmation -->
			<div class="modal fade" id="modal-restaurantMenuCategory-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		Restaurant-Menu-Category Restoration
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" 
						  	v-on:submit.prevent="restoreRestaurantMenuCategory" 
						  	autocomplete="off"
						>
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore meal ??</h5>
							</div>
							<div class="modal-footer justify-content-between">
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
			<!-- /.modal-restaurantMenuCategory-restore-confirmation-->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';

	var singleRestaurantMenuCategoryData = {

		menu_category_ids : [],	// for create mode
    	menuCategoryObjects : [], 

		serving_from : '10.00',
		serving_to : '22.00',
		restaurant_id : null,
		menu_category : {},
		menu_items : [],
    	
    };

	var menuCategoryListData = {
      	query : '',
    	perPage : 10,
    	currentTab : 'current',

    	loading : false,
    	editMode : false,
    	submitForm : true,

    	restaurant : {
			
    	},
    	
    	allMenuCategories : [],
    	
    	restaurantAllMenuCategories : [],
		restaurantMenuCategoriesToShow : [],

    	restaurantScheduleHours : ['6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00', '20.00', '21.00', '22.00', '23.00', '24.00', '1.00', '2.00'],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		
    	},

        singleRestaurantMenuCategoryData : singleRestaurantMenuCategoryData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

		// Local registration of components
		components: {
			Multiselect, // short form of Multiselect : Multiselect
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
	        return menuCategoryListData;
		},

		created(){
			this.fetchAllMenuCategories();
			this.fetchRestaurantAllMenuCategories();
			this.setCurrentRestaurant();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchRestaurantAllMenuCategories();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
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
			fetchRestaurantAllMenuCategories() {
				this.loading = true;
				axios
					.get('/api/restaurant-menu-categories/' + this.restaurantId + '/' + this.perPage + "?page=" +
				    this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.restaurantAllMenuCategories = response.data;
							// this.pagination = response.data;
							this.showDataListOfSelectedTab();
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			setCurrentRestaurant() {

				this.restaurant = {

					id : this.restaurantId,
					name : this.restaurantName,

				}

				this.singleRestaurantMenuCategoryData.restaurant_id = this.restaurant.id;

			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchRestaurantAllMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchRestaurantAllMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showRestaurantMenuCategoryCreateModal(){
				this.editMode = false;
				this.submitForm = true;
				this.errors = {};
				
				this.singleRestaurantMenuCategoryData.menuCategoryObjects = [];
				
				this.singleRestaurantMenuCategoryData.serving_from = this.singleRestaurantMenuCategoryData.serving_to = null;

				$('#modal-createOrEdit-restaurantMenuCategory').modal('show');
			},
			showRestaurantMenuCategoryEditModal(restaurantMenuCategory) {

				this.editMode = true;
				this.submitForm = true;
				this.errors = {};
				
				this.singleRestaurantMenuCategoryData = restaurantMenuCategory;

				var array = [];
				
				if (restaurantMenuCategory.menu_category) {
					array.push(restaurantMenuCategory.menu_category);
				}

				this.singleRestaurantMenuCategoryData.menuCategoryObjects = array;

				this.setMenuCategoryIdCollection();

				$("#modal-createOrEdit-restaurantMenuCategory").modal("show");
			},
    		storeRestaurantMenuCategory(){

    			if (! this.singleRestaurantMenuCategoryData.menu_category_ids || this.singleRestaurantMenuCategoryData.menu_category_ids.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuCategory').modal('hide');
				
				axios
					.post('/restaurant-menu-categories/' + this.perPage, this.singleRestaurantMenuCategoryData)
					.then(response => {

						if (response.status == 200) {
							
							this.singleRestaurantMenuCategoryData.menuCategoryObjects = [];

							this.restaurantAllMenuCategories = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

							// this.pagination = response.data;

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
			updateRestaurantMenuCategory(){

				if (! this.singleRestaurantMenuCategoryData.menu_category_ids || this.singleRestaurantMenuCategoryData.menu_category_ids.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuCategory').modal('hide');
				
				axios
					.put('/restaurant-menu-categories/' + this.singleRestaurantMenuCategoryData.id + '/' + this.perPage, this.singleRestaurantMenuCategoryData)
					.then(response => {

						if (response.status == 200) {

							this.singleRestaurantMenuCategoryData.menuCategoryObjects = [];

							if (this.query === '') {
								this.restaurantAllMenuCategories = response.data;
								// this.pagination = response.data;
								this.showDataListOfSelectedTab();
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
			showRestaurantMenuCategoryDeleteModal(restaurantMenuCategory){
				
				this.singleRestaurantMenuCategoryData = restaurantMenuCategory;

				$("#modal-restaurantMenuCategory-delete-confirmation").modal("show");

			},
			destroyRestaurantMenuCategory(){

				$('#modal-restaurantMenuCategory-delete-confirmation').modal('hide');
				
				axios
					.delete('/restaurant-menu-categories/' + this.singleRestaurantMenuCategoryData.id + '/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {

						if (response.status == 200) {

							if (this.query === '') {
								this.restaurantAllMenuCategories = response.data;
								// this.pagination = response.data;
								this.showDataListOfSelectedTab();
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Deleted");
							
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
			showRestaurantMenuCategoryRestoreModal(restaurantMenuCategory){
				
				this.singleRestaurantMenuCategoryData = restaurantMenuCategory;

				$("#modal-restaurantMenuCategory-restore-confirmation").modal("show");

			},
			restoreRestaurantMenuCategory(){

				$('#modal-restaurantMenuCategory-restore-confirmation').modal('hide');
				
				axios
					.patch('/restaurant-menu-categories/' + this.singleRestaurantMenuCategoryData.id + '/' + this.perPage +
				    "?page=" +
				    this.pagination.current_page)
					.then(response => {

						if (response.status == 200) {

							if (this.query === '') {
								this.restaurantAllMenuCategories = response.data;
								// this.pagination = response.data;
								this.showDataListOfSelectedTab();
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Deleted");
							
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
		    searchData() {
				
				axios
				.get(
					"/api/search-restaurant-menu-categories/" + this.restaurantId + "/"  + this.query + "/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {

					this.restaurantAllMenuCategories = response.data;
					
					this.restaurantMenuCategoriesToShow = this.restaurantAllMenuCategories.all.data;
					this.pagination = this.restaurantAllMenuCategories.all;

				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					/*
					case 'restaurantMenuCategory.restaurant' :

						if (Object.keys(this.singleRestaurantMenuCategoryData.restaurant).length === 0) {
							this.errors.restaurant = 'Restaurant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'restaurant');
						}

						break;
					*/

					case 'restaurantMenuCategory.menuCategory' :

						if (this.singleRestaurantMenuCategoryData.menuCategoryObjects.length === 0) {
							this.errors.menuCategory = 'Menu Category name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'menuCategory');
						}

						break;
				}
	 
			},
			showRestaurantCurrentMenuCategories(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showRestaurantTrashedMenuCategories(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.restaurantMenuCategoriesToShow = this.restaurantAllMenuCategories.current.data;
					this.pagination = this.restaurantAllMenuCategories.current;
				}else {
					this.restaurantMenuCategoriesToShow = this.restaurantAllMenuCategories.trashed.data;
					this.pagination = this.restaurantAllMenuCategories.trashed;
				}
			},
			showRestaurantMenuList() {
				this.$router.push({
			 		name: 'restaurant-menu-items', 
			 		params: { 
			 			restaurantId : this.restaurantId, 
			 			restaurantName : this.restaurantName, 
			 		}, 
				});
			},
			setMenuCategoryIdCollection() {

				if (Array.isArray(this.singleRestaurantMenuCategoryData.menuCategoryObjects) && this.singleRestaurantMenuCategoryData.menuCategoryObjects.length) {

					let array = [];

					$.each(this.singleRestaurantMenuCategoryData.menuCategoryObjects, function(key, value) {
				     	array.push(value.id);
				   	});

			     	this.singleRestaurantMenuCategoryData.menu_category_ids = array;

				}
				/*
				else{

					if (this.singleRestaurantMenuCategoryData.menuCategoryObjects) {
						this.singleRestaurantMenuCategoryData.menu_category_ids[0] = this.singleRestaurantMenuCategoryData.menuCategoryObjects.id;
					}
					else
						this.singleRestaurantMenuCategoryData.menu_category_ids = [];
				}
				*/

			}

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