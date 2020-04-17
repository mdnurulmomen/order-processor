
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
	              	
					<!--
					// For those who has only to view
					<vue-bootstrap4-table 
						:rows="cuisines" 
						:columns="columns" 
						:config="config"
						:actions="actions"
					>

					</vue-bootstrap4-table> 
					-->

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">Cuisine List</h2>

                        	<button type="button" @click="showCuisineCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Cuisine
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showCurrentCuisines">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedCuisines">Trashed</a>
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
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="cuisinesToShow.length"
									    	v-for="(cuisine, index) in cuisinesToShow"
									    	:key="cuisine.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ cuisine.name}}</td>
								    		<td>
										      	<button type="button" v-show="cuisine.deleted_at === null" @click="showCuisineEditModal(cuisine)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="cuisine.deleted_at === null"
								        			type="button"
								        			@click="showCuisineDeletionModal(cuisine)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="cuisine.deleted_at !== null"
								        			type="button"
								        			@click="showCuisineRestoreModal(cuisine)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!cuisinesToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No cuisine found.</div>
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
										@click="query === '' ? fetchAllCuisines() : searchData()"
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
										@paginate="query === '' ? fetchAllCuisines() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-cuisine -->
			<div class="modal fade" id="modal-createOrEdit-cuisine">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Cuisine</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateCuisine() : storeCuisine()"
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
									              		
								              		<label for="inputCuisineName" class="col-sm-4 col-form-label text-right">Cuisine Name</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleCuisineData.cuisine.name" 
															placeholder="Cuisine Name" 
															required="true"
															:class="!errors.cuisine.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('cuisine.name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.cuisine.name }}
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
							  		{{ editMode ? 'Update' : 'Create' }} Cuisine
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-cuisine-->

			<!-- modal-cuisine-delete-confirmation -->
			<div class="modal fade" id="modal-cuisine-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Cuisine Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyCuisine" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete cuisine ?? </h5>
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
			<!-- /modal-cuisine-delete-confirmation -->

			<!-- modal-cuisine-restore-confirmation -->
			<div class="modal fade" id="modal-cuisine-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Cuisine Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreCuisine()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore cuisine ?? </h5>
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
			<!-- /.modal-cuisine-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleCuisineData = {
    	cuisine : {
			// id : null,
			// name : null,
			// deleted_at : null,
    	},
    };

	var cuisineListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allCuisines : [],
    	cuisinesToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		cuisine : {},
    	},

        singleCuisineData : singleCuisineData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return cuisineListData;
		},

		created(){
			this.fetchAllCuisines();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllCuisines();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentCuisines(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedCuisines(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.cuisinesToShow = this.allCuisines.current.data;
					this.pagination = this.allCuisines.current;
				}else {
					this.cuisinesToShow = this.allCuisines.trashed.data;
					this.pagination = this.allCuisines.trashed;
				}
			},
			fetchAllCuisines(){
				this.loading = true;
				axios
					.get('/api/cuisines/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allCuisines = response.data;
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
					this.fetchAllCuisines();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllCuisines();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showCuisineCreateModal(){

				this.editMode = false;
				this.errors.cuisine = {};
				this.submitForm = true;

				this.singleCuisineData.cuisine = {};

				$('#modal-createOrEdit-cuisine').modal('show');
			},
    		storeCuisine(){

				$('#modal-createOrEdit-cuisine').modal('hide');
				
				axios
					.post('/cuisines/'+ this.perPage, this.singleCuisineData.cuisine)
					.then(response => {

						if (response.status == 200) {
							this.singleCuisineData.cuisine = {};

							this.allCuisines = response.data;

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
			showCuisineEditModal(cuisine) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.cuisine = {};
				this.singleCuisineData.cuisine = cuisine;
				$("#modal-createOrEdit-cuisine").modal("show");
			},
			updateCuisine(){

				$('#modal-createOrEdit-cuisine').modal('hide');
				
				axios
					.put('/cuisines/' + this.singleCuisineData.cuisine.id + '/' + this.perPage, this.singleCuisineData.cuisine)
					.then(response => {

						if (response.status == 200) {

							this.singleCuisineData.cuisine = {};

							if (this.query === '') {
								this.allCuisines = response.data;
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
			showCuisineDeletionModal(cuisine) {
				this.singleCuisineData.cuisine = cuisine;
				$("#modal-cuisine-delete-confirmation").modal("show");
			},
			destroyCuisine(){

				$("#modal-cuisine-delete-confirmation").modal("hide");

				axios
					.delete('/cuisines/'+this.singleCuisineData.cuisine.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleCuisineData.cuisine = {};

							if (this.query === '') {
								this.allCuisines = response.data;
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
			showCuisineRestoreModal(cuisine) {
				this.singleCuisineData.cuisine = cuisine;
				$("#modal-cuisine-restore-confirmation").modal("show");
			},
			restoreCuisine(){

				$("#modal-cuisine-restore-confirmation").modal("hide");

				axios
					.patch('/cuisines/'+this.singleCuisineData.cuisine.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleCuisineData.cuisine = {};

							if (this.query === '') {
								this.allCuisines = response.data;
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
					"/api/cuisines/search/"+ this.query +"/" + this.perPage +'?page='+ this.pagination.current_page
				)
				.then(response => {
					this.allCuisines = response.data;
					this.cuisinesToShow = this.allCuisines.all.data;
					this.pagination = this.allCuisines.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				if (!this.singleCuisineData.cuisine.name) {
					this.errors.cuisine.name = 'Cuisine name is required';
				}
				else if (!this.singleCuisineData.cuisine.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
					this.errors.cuisine.name = 'No special characters';
				}
				else{
					this.submitForm = true;
					this.$delete(this.errors.cuisine, 'name');
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