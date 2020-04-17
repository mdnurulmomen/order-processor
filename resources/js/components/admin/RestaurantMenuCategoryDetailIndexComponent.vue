
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
							<h2 class="lead float-left mt-1">{{ this.restaurantMenuCategoriesToShow.length ? this.restaurantMenuCategoriesToShow[0].restaurant.name : restaurantName }} Menu Categories</h2>

                        	<button type="button" @click="showRestaurantMenuList" class="btn btn-default btn-sm float-right ml-1">
			        			<i class="fas fa-eye"></i>
			        			View Menu-Items
			      			</button>

                        	<button type="button" @click="showRestaurantMenuCategoryCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add More menu categories
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showRestaurantCurrentMenuCategories">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showRestaurantTrashedMenuCategories">Trashed</a>
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
									        <!-- No special characters (except '@&*+-_=') -->
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
								    			{{ restaurantMenuCategory.menu_category.name }}
								    		</td>
								    		<td>
								    			{{ restaurantMenuCategory.serving_from }}
								    		</td>
								    		<td>
								    			{{ restaurantMenuCategory.serving_to }}
								    		</td>
								    		<td>
								    			{{ restaurantMenuCategory.restaurant_menu_items.length }}
								    		</td>
								    		<td>
										      	 
										      	<button type="button" @click="showRestaurantMenuCategoryEditModal(restaurantMenuCategory)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

										      	<button type="button" 
										      		v-show="restaurantMenuCategory.deleted_at === null"
										      		@click="showRestaurantMenuCategoryDeleteModal(restaurantMenuCategory)" class="btn btn-danger btn-sm">
										        	<i class="fas fa-trash"></i>
										        	Delete
										      	</button>

										      	<button type="button" 
										      		v-show="restaurantMenuCategory.deleted_at !== null"
										      		@click="showRestaurantMenuCategoryRestoreModal(restaurantMenuCategory)" class="btn btn-danger btn-sm">
										        	<i class="fas fa-restore"></i>
										        	Restore
										      	</button>
 												
								    		</td>
									  	</tr>
									  	<tr v-show="!restaurantMenuCategoriesToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No Menu Category found.</div>
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
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} {{ restaurantName }} Menu-Category</h4>
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
								              			Restaurant Name : 
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.restaurantObject"
				                                  			placeholder="Restaurant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="restaurantsToShow" 
					                                  		:required="true"
					                                  		:class="!errors.restaurantMenuCategory.restaurant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantMenuCategory.restaurant')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.restaurantMenuCategory.restaurant }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label 
								              			for="inputMenuName3" 
								              			class="col-sm-4 col-form-label text-right"
								              		>
								              			Menu-Category Name : 
								              		</label>

									                <div class="col-sm-8">
									                  	
									                  	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.menuCategoryObjects"
				                                  			placeholder="MenuCategory Names" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMenuCategories" 
					                                  		:required="true" 
					                                  		:multiple="!editMode" 
					                                  		:class="!errors.restaurantMenuCategory.menuCategory ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('restaurantMenuCategory.menuCategory')"
				                                  		>
					                                	</multiselect>

									                	<div class="invalid-feedback">
												        	{{ errors.restaurantMenuCategory.menuCategory }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Serving From</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.restaurantMenuCategory.serving_from"
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
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Serving To</label>
									                <div class="col-sm-8">
									                	<multiselect 
				                                  			v-model="restaurantSingleMenuCategoryData.restaurantMenuCategory.serving_to"
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
							<div class="modal-footer justify-content-between">
								<div class="col-sm-12 text-right">
									<span class="text-danger p-0 m-0 small" v-show="!submitForm">
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
							  		{{ editMode ? 'Update' : 'Create' }} Restaurant Menu Category
							  	</button>
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
					  	<form class="form-horizontal" v-on:submit.prevent="destroyRestaurantMenuCategory" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete menu-category ?? </h5>
					      		<h5 class="text-secondary"><small>But once you want, you can retreive it from bin.</small></h5>
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
						  	<h4 class="modal-title">Restaurant-Menu-Category Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreRestaurantMenuCategory" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore meal ?? </h5>
							</div>
							<div class="modal-footer justify-content-between">
							  	<button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>

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

	var restaurantSingleMenuCategoryData = {
		
    	restaurantObject : {
			
    	},

    	menuCategoryObjects : [],

    	restaurantMenuCategory : {
			menu_category_id : [],
			serving_from : '10.00',
			serving_to : '22.00',
			restaurant_id : null,
			menu_category : {},
			// restaurant_menu_items : [],
    	}
    };

	var menuCategoryListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,
    	currentTab : 'current',

    	editMode : false,
    	
    	allMenuCategories : [],
    	restaurantsToShow : [],

    	
    	restaurantAllMenuCategories : [],
		restaurantMenuCategoriesToShow : [],

    	restaurantScheduleHours : ['6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '15.00', '16.00', '17.00', '18.00', '19.00', '20.00', '21.00', '22.00', '23.00', '24.00', '1.00', '2.00'],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		restaurantMenuCategory : {},
    	},

        restaurantSingleMenuCategoryData : restaurantSingleMenuCategoryData,

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
		},

	    data() {
	        return menuCategoryListData;
		},

		created(){

			this.restaurantSingleMenuCategoryData.restaurantObject = {
				id : this.$route.params.restaurant,		
				name : this.restaurantName,
			};

			var array = [];
			array.push(this.restaurantSingleMenuCategoryData.restaurantObject);
			this.restaurantsToShow = array;

			this.fetchAllMenuCategories();
			this.fetchRestaurantAllMenuCategories();
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
			'restaurantSingleMenuCategoryData.menuCategoryObjects' : function(menuCategoryObjects){

				if (Array.isArray(menuCategoryObjects)) {

					let array = [];
					$.each(menuCategoryObjects, function(key, value) {
				     	array.push(value.id);
				   	});
			     	this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id = array;

				}
				else{

					if (menuCategoryObjects) {
						this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id[0] = menuCategoryObjects.id;
					}
					else
						this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id[0] = null;
				}

			},
			'restaurantSingleMenuCategoryData.restaurantObject' : function(restaurantObject){
				
				if (restaurantObject) {
					this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id = restaurantObject.id;
				}
				else
					this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id = null;

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
					.get('/api/restaurant-menu-categories/' + this.$route.params.restaurant + '/' + this.perPage + "?page=" +
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
				this.errors.restaurantMenuCategory = {};
				
				this.restaurantSingleMenuCategoryData.menuCategoryObjects = [];
				
				this.restaurantSingleMenuCategoryData.restaurantMenuCategory.serving_from = this.restaurantSingleMenuCategoryData.restaurantMenuCategory.serving_to = null;

				$('#modal-createOrEdit-restaurantMenuCategory').modal('show');
			},
    		storeRestaurantMenuCategory(){

    			if (!this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id || this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuCategory').modal('hide');
				
				axios
					.post('/restaurant-menu-categories/' + this.perPage, this.restaurantSingleMenuCategoryData.restaurantMenuCategory)
					.then(response => {

						if (response.status == 200) {
							
							this.restaurantSingleMenuCategoryData.menuCategoryObjects = [];

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
			showRestaurantMenuCategoryEditModal(restaurantMenuCategory) {

				this.editMode = true;
				this.submitForm = true;
				this.errors.restaurantMenuCategory = {};
				
				this.restaurantSingleMenuCategoryData.restaurantMenuCategory = restaurantMenuCategory;

				var array = [];
				array.push(restaurantMenuCategory.menu_category);
				this.restaurantSingleMenuCategoryData.menuCategoryObjects = array;

				$("#modal-createOrEdit-restaurantMenuCategory").modal("show");
			},
			updateRestaurantMenuCategory(){

				if (!this.restaurantSingleMenuCategoryData.restaurantMenuCategory.restaurant_id || this.restaurantSingleMenuCategoryData.restaurantMenuCategory.menu_category_id.length === 0) {
					
					this.submitForm = false;
					return;
				}

				$('#modal-createOrEdit-restaurantMenuCategory').modal('hide');
				
				axios
					.put('/restaurant-menu-categories/' + this.restaurantSingleMenuCategoryData.restaurantMenuCategory.id + '/' + this.perPage, this.restaurantSingleMenuCategoryData.restaurantMenuCategory)
					.then(response => {

						if (response.status == 200) {

							this.restaurantSingleMenuCategoryData.menuCategoryObjects = [];

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
				
				this.restaurantSingleMenuCategoryData.restaurantMenuCategory = restaurantMenuCategory;

				$("#modal-restaurantMenuCategory-delete-confirmation").modal("show");

			},
			destroyRestaurantMenuCategory(){

				$('#modal-restaurantMenuCategory-delete-confirmation').modal('hide');
				
				axios
					.delete('/restaurant-menu-categories/' + this.restaurantSingleMenuCategoryData.restaurantMenuCategory.id + '/' + this.perPage +
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
				
				this.restaurantSingleMenuCategoryData.restaurantMenuCategory = restaurantMenuCategory;

				$("#modal-restaurantMenuCategory-restore-confirmation").modal("show");

			},
			restoreRestaurantMenuCategory(){

				$('#modal-restaurantMenuCategory-restore-confirmation').modal('hide');
				
				axios
					.patch('/restaurant-menu-categories/' + this.restaurantSingleMenuCategoryData.restaurantMenuCategory.id + '/' + this.perPage +
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
					"/api/restaurant-menu-categories/search/" + this.$route.params.restaurant + "/"  + this.query + "/" + this.perPage +
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

					case 'restaurantMenuCategory.restaurant' :

						if (Object.keys(this.restaurantSingleMenuCategoryData.restaurantObject).length === 0) {
							this.errors.restaurantMenuCategory.restaurant = 'Restaurant name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuCategory, 'restaurant');
						}

						break;

					case 'restaurantMenuCategory.menuCategory' :

						if (this.restaurantSingleMenuCategoryData.menuCategoryObjects.length === 0) {
							this.errors.restaurantMenuCategory.menuCategory = 'Menu Category name is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.restaurantMenuCategory, 'menuCategory');
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
			 		name: 'admin.restaurantMenuItem.index', 
			 		params: { 
			 			restaurant : this.restaurantSingleMenuCategoryData.restaurantObject.id, 
			 			restaurantName : this.restaurantSingleMenuCategoryData.restaurantObject.name 
			 		}, 
				});
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