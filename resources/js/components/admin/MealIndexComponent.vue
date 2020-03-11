
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
						:rows="meals" 
						:columns="columns" 
						:config="config"
						:actions="actions"
					>

					</vue-bootstrap4-table> 
					-->

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">Meal List</h2>

                        	<button type="button" @click="showMealCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Meal
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showCurrentMeals">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedMeals">Trashed</a>
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
									  	<tr v-show="mealsToShow.length"
									    	v-for="(meal, index) in mealsToShow"
									    	:key="meal.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ meal.name}}</td>
								    		<td>
										      	<button type="button" v-show="meal.deleted_at === null" @click="showMealEditModal(meal)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										      	</button>
								      			<button
								        			v-show="meal.deleted_at === null"
								        			type="button"
								        			@click="showMealDeletionModal(meal)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								      			</button>
								      			<button
								        			v-show="meal.deleted_at !== null"
								        			type="button"
								        			@click="showMealRestoreModal(meal)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!mealsToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No data found.</div>
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
										@click="query === '' ? fetchAllMeals() : searchData()"
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
										@paginate="query === '' ? fetchAllMeals() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-meal -->
			<div class="modal fade" id="modal-createOrEdit-meal">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Meal</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateMeal() : storeMeal()"
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
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Meal Name</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleMealData.meal.name" 
															placeholder="Menu Name" 
															required="true"
															:class="!errors.meal.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('meal.name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.meal.name }}
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
							  		{{ editMode ? 'Update' : 'Create' }} Meal
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-meal-->

			<!-- modal-meal-delete-confirmation -->
			<div class="modal fade" id="modal-meal-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Meal Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyMeal" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete meal ?? </h5>
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
			<!-- /modal-meal-delete-confirmation -->

			<!-- modal-meal-restore-confirmation -->
			<div class="modal fade" id="modal-meal-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Meal Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreMeal()" autocomplete="off">
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
			<!-- /.modal-meal-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleMealData = {
    	meal : {
			// id : null,
			// name : null,
			// deleted_at : null,
    	},
    };

	var mealListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allMeals : [],
    	mealsToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		meal : {},
    	},

        singleMealData : singleMealData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return mealListData;
		},

		created(){
			this.fetchAllMeals();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllMeals();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentMeals(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedMeals(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.mealsToShow = this.allMeals.current.data;
					this.pagination = this.allMeals.current;
				}else {
					this.mealsToShow = this.allMeals.trashed.data;
					this.pagination = this.allMeals.trashed;
				}
			},
			fetchAllMeals(){
				this.loading = true;
				axios
					.get('/api/meals/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMeals = response.data;
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
					this.fetchAllMeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllMeals();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showMealCreateModal(){

				this.editMode = false;
				this.errors.meal = {};
				this.submitForm = true;

				this.singleMealData.meal = {};

				$('#modal-createOrEdit-meal').modal('show');
			},
    		storeMeal(){

				$('#modal-createOrEdit-meal').modal('hide');
				
				axios
					.post('/meals/'+ this.perPage, this.singleMealData.meal)
					.then(response => {

						if (response.status == 200) {
							this.singleMealData.meal = {};

							this.allMeals = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

							toastr.success(response.data.success, "Success");
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
			showMealEditModal(meal) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.meal = {};
				this.singleMealData.meal = meal;
				$("#modal-createOrEdit-meal").modal("show");
			},
			updateMeal(){

				$('#modal-createOrEdit-meal').modal('hide');
				
				axios
					.put('/meals/' + this.singleMealData.meal.id + '/' + this.perPage, this.singleMealData.meal)
					.then(response => {

						if (response.status == 200) {

							this.singleMealData.meal = {};

							if (this.query === '') {
								this.allMeals = response.data;
								this.showDataListOfSelectedTab();
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							toastr.success(response.data.success, "Success");
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
			showMealDeletionModal(meal) {
				this.singleMealData.meal = meal;
				$("#modal-meal-delete-confirmation").modal("show");
			},
			destroyMeal(){

				$("#modal-meal-delete-confirmation").modal("hide");

				axios
					.delete('/meals/'+this.singleMealData.meal.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMealData.meal = {};

							if (this.query === '') {
								this.allMeals = response.data;
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
			showMealRestoreModal(meal) {
				this.singleMealData.meal = meal;
				$("#modal-meal-restore-confirmation").modal("show");
			},
			restoreMeal(){

				$("#modal-meal-restore-confirmation").modal("hide");

				axios
					.patch('/meals/'+this.singleMealData.meal.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleMealData.meal = {};

							if (this.query === '') {
								this.allMeals = response.data;
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
					"/api/meals/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allMeals = response.data;
					this.mealsToShow = this.allMeals.all.data;
					this.pagination = this.allMeals.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				if (!this.singleMealData.meal.name) {
					this.errors.meal.name = 'Meal name is required';
				}
				else if (!this.singleMealData.meal.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
					this.errors.meal.name = 'No special characters';
				}
				else{
					this.submitForm = true;
					this.$delete(this.errors.meal, 'name');
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