
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
							<h2 class="lead float-left mt-1">Menu Categories List</h2>

                        	<button type="button" @click="showMenuCategoryCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Menu Category
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showCurrentMenuCategories">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedMenuCategories">Trashed</a>
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
											<th scope="col">Name</th>
											<th scope="col">Show On App</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="menuCategoriesToShow.length"
									    	v-for="(menuCategory, index) in menuCategoriesToShow"
									    	:key="menuCategory.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ menuCategory.name}}</td>
								    		<td>
								    			<span class="badge" :class="menuCategory.search_preference ? 'badge-success' : 'badge-info'">
								    				{{ menuCategory.search_preference ? 'Published' : 'UnPublished' }}
								    			</span>
								    		</td>
								    		<td>
										      	<button type="button" v-show="menuCategory.deleted_at === null" @click="showMenuCategoryEditModal(menuCategory)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="menuCategory.deleted_at === null"
								        			type="button"
								        			@click="showMenuCategoryDeletionModal(menuCategory)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="menuCategory.deleted_at !== null"
								        			type="button"
								        			@click="showMenuCategoryRestoreModal(menuCategory)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!menuCategoriesToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No menu-category found.</div>
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
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllMenuCategories() : searchData()"
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
										@paginate="query === '' ? fetchAllMenuCategories() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-menuCategory -->
			<div class="modal fade" id="modal-createOrEdit-menuCategory">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Menu Category</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateMenuCategory() : storeMenuCategory()"
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
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Category Name</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMenuCategoryData.name" 
															placeholder="Appetizer / Burger / Pizza" 
															required="true"
															:class="!errors.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('menuCategory.name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">
								              		<label for="inputnumber3" class="col-sm-4 col-form-label text-right">
								              			Publish On App
								              		</label>
									                <div class="col-sm-8">
									                	<toggle-button 
				                                    		value ="true" 
				                                    		:sync="true" 
				                                    		v-model="singleMenuCategoryData.search_preference" 
				                                    		:width="140"  
				                                    		:height="30" 
				                                    		:font-size="15" 
				                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
				                                    		:labels="{checked: 'Published', unchecked: 'UnPublished' }"
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
								  		{{ editMode ? 'Update' : 'Create' }} Menu-Category
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-menuCategory-->

			<!-- modal-menuCategory-delete-confirmation -->
			<div class="modal fade" id="modal-menuCategory-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Menu Category Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyMenuCategory" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete this menu category ?? </h5>
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
			<!-- /modal-menuCategory-delete-confirmation -->

			<!-- modal-menuCategory-restore-confirmation -->
			<div class="modal fade" id="modal-menuCategory-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Menu Category Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreMenuCategory()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore this menu category ?? </h5>
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
			<!-- /.modal-menuCategory-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import { ToggleButton } from 'vue-js-toggle-button';

	var singleMenuCategoryData = {
    	
		// id : null,
		// name : null,
		// search_preference : false,
		// deleted_at : null,
    	
    };

	var menuCategoryListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allMenuCategories : [],
    	menuCategoriesToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {

    	},

        singleMenuCategoryData : singleMenuCategoryData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    components: { 
	    	ToggleButton : ToggleButton, 
		},

	    data() {
	        return menuCategoryListData;
		},

		created(){
			this.fetchAllMenuCategories();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllMenuCategories();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentMenuCategories(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedMenuCategories(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.menuCategoriesToShow = this.allMenuCategories.current.data;
					this.pagination = this.allMenuCategories.current;
				}else {
					this.menuCategoriesToShow = this.allMenuCategories.trashed.data;
					this.pagination = this.allMenuCategories.trashed;
				}
			},
			fetchAllMenuCategories(){
				this.loading = true;
				axios
					.get('/api/menu-categories/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMenuCategories = response.data;
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
					this.fetchAllMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMenuCategories();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showMenuCategoryCreateModal(){

				this.editMode = false;
				this.errors = {};
				this.submitForm = true;

				this.singleMenuCategoryData = {};

				$('#modal-createOrEdit-menuCategory').modal('show');
			},
    		storeMenuCategory(){

				$('#modal-createOrEdit-menuCategory').modal('hide');
				
				axios
					.post('/menu-categories/'+ this.perPage, this.singleMenuCategoryData)
					.then(response => {

						if (response.status == 200) {
							this.singleMenuCategoryData = {};

							this.allMenuCategories = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

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
			showMenuCategoryEditModal(menuCategory) {
				this.editMode = true;
				this.submitForm = true;
				this.errors = {};
				this.singleMenuCategoryData = menuCategory;
				$("#modal-createOrEdit-menuCategory").modal("show");
			},
			updateMenuCategory(){

				$('#modal-createOrEdit-menuCategory').modal('hide');
				
				axios
					.put('/menu-categories/' + this.singleMenuCategoryData.id + '/' + this.perPage, this.singleMenuCategoryData)
					.then(response => {

						if (response.status == 200) {

							this.singleMenuCategoryData = {};

							if (this.query === '') {
								this.allMenuCategories = response.data;
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
			showMenuCategoryDeletionModal(menuCategory) {
				this.singleMenuCategoryData = menuCategory;
				$("#modal-menuCategory-delete-confirmation").modal("show");
			},
			destroyMenuCategory(){

				$("#modal-menuCategory-delete-confirmation").modal("hide");

				axios
					.delete('/menu-categories/'+this.singleMenuCategoryData.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMenuCategoryData = {};

							if (this.query === '') {
								this.allMenuCategories = response.data;
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
						console.log(error);
						if (error.response.status == 422) {
							for (var x in error.response.data.errors) {
								toastr.error(error.response.data.errors[x], "Wrong Input");
							}
				      	}
					});
			},
			showMenuCategoryRestoreModal(menuCategory) {
				this.singleMenuCategoryData = menuCategory;
				$("#modal-menuCategory-restore-confirmation").modal("show");
			},
			restoreMenuCategory(){

				$("#modal-menuCategory-restore-confirmation").modal("hide");

				axios
					.patch('/menu-categories/'+this.singleMenuCategoryData.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMenuCategoryData = {};

							if (this.query === '') {
								this.allMenuCategories = response.data;
								this.showDataListOfSelectedTab();
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
					"/api/menu-categories/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allMenuCategories = response.data;
					this.menuCategoriesToShow = this.allMenuCategories.all.data;
					this.pagination = this.allMenuCategories.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				if (!this.singleMenuCategoryData.name) {
					this.errors.name = 'Menu Category name is required';
				}
				else if (!this.singleMenuCategoryData.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
					this.errors.name = 'No special characters';
				}
				else{
					this.submitForm = true;
					this.$delete(this.errors, 'name');
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