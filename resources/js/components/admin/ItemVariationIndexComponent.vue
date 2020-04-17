
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
						:rows="variations" 
						:columns="columns" 
						:config="config"
						:actions="actions"
					>

					</vue-bootstrap4-table> 
					-->

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">Variation List</h2>

                        	<button type="button" @click="showVariationCreateModal" class="btn btn-secondary btn-sm float-right mb-2">
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Variation
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showCurrentVariations">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedVariations">Trashed</a>
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
											<th scope="col">Variation Name</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="variationsToShow.length"
									    	v-for="(variation, index) in variationsToShow"
									    	:key="variation.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ variation.variation_name}}</td>
								    		<td>
										      	<button type="button" v-show="variation.deleted_at === null" @click="showVariationEditModal(variation)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="variation.deleted_at === null"
								        			type="button"
								        			@click="showVariationDeletionModal(variation)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="variation.deleted_at !== null"
								        			type="button"
								        			@click="showVariationRestoreModal(variation)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								    		</td>
									  	</tr>
									  	<tr v-show="!variationsToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No variation found.</div>
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
										@click="query === '' ? fetchAllItemVariations() : searchData()"
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
										@paginate="query === '' ? fetchAllItemVariations() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-variation -->
			<div class="modal fade" id="modal-createOrEdit-variation">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">{{ editMode ? 'Edit' : 'Create' }} Variation</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateVariation() : storeVariation()"
						  	autocomplete="off"
					  	>
							<div class="modal-body text-dark">

					      		<input 
						      		type="hidden" 
						      		variation_name="_token" 
						      		:value="csrf"
					      		>

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">Variation Name</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleVariationData.variation.variation_name" 
															placeholder="Menu Name" 
															required="true"
															:class="!errors.variation.name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.variation.name }}
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
							  		{{ editMode ? 'Update' : 'Create' }} Variation
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-variation-->

			<!-- modal-variation-delete-confirmation -->
			<div class="modal fade" id="modal-variation-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Variation Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyVariation" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete variation ?? </h5>
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
			<!-- /modal-variation-delete-confirmation -->

			<!-- modal-variation-restore-confirmation -->
			<div class="modal fade" id="modal-variation-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Variation Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreVariation()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			variation_name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore variation ?? </h5>
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
			<!-- /.modal-variation-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';

	var singleVariationData = {
    	variation : {
			variation_name : null,
			deleted_at : null,
    	},
    };

	var variationListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allVariations : [],
    	variationsToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		variation : {},
    	},

        singleVariationData : singleVariationData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return variationListData;
		},

		created(){
			this.fetchAllItemVariations();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllItemVariations();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentVariations(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedVariations(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.variationsToShow = this.allVariations.current.data;
					this.pagination = this.allVariations.current;
				}else {
					this.variationsToShow = this.allVariations.trashed.data;
					this.pagination = this.allVariations.trashed;
				}
			},
			fetchAllItemVariations(){
				this.loading = true;
				axios
					.get('/api/item-variations/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allVariations = response.data;
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
					this.fetchAllItemVariations();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllItemVariations();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showVariationCreateModal(){
				this.editMode = false;
				this.errors.variation = {};
				this.submitForm = true;

				this.singleVariationData.variation = {};

				$('#modal-createOrEdit-variation').modal('show');
			},
    		storeVariation(){

				$('#modal-createOrEdit-variation').modal('hide');
				
				axios
					.post('/item-variations/'+ this.perPage, this.singleVariationData.variation)
					.then(response => {

						if (response.status == 200) {
							this.singleVariationData.variation = {};

							this.allVariations = response.data;

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
			showVariationEditModal(variation) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.variation = {};
				this.singleVariationData.variation = variation;
				$("#modal-createOrEdit-variation").modal("show");
			},
			updateVariation(){

				$('#modal-createOrEdit-variation').modal('hide');
				
				axios
					.put('/item-variations/' + this.singleVariationData.variation.id + '/' + this.perPage, this.singleVariationData.variation)
					.then(response => {

						if (response.status == 200) {

							this.singleVariationData.variation = {};

							if (this.query === '') {
								this.allVariations = response.data;
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
			showVariationDeletionModal(variation) {
				this.singleVariationData.variation = variation;
				$("#modal-variation-delete-confirmation").modal("show");
			},
			destroyVariation(){

				$("#modal-variation-delete-confirmation").modal("hide");

				axios
					.delete('/item-variations/' + this.singleVariationData.variation.id+ '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleVariationData.variation = {};

							if (this.query === '') {
								this.allVariations = response.data;
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
			showVariationRestoreModal(variation) {
				this.singleVariationData.variation = variation;
				$("#modal-variation-restore-confirmation").modal("show");
			},
			restoreVariation(){

				$("#modal-variation-restore-confirmation").modal("hide");

				axios
					.patch('/item-variations/' + this.singleVariationData.variation.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleVariationData.variation = {};

							if (this.query === '') {
								this.allVariations = response.data;
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
					"/api/item-variations/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allVariations = response.data;
					this.variationsToShow = this.allVariations.all.data;
					this.pagination = this.allVariations.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput () {
				
				this.submitForm = false;

				if (!this.singleVariationData.variation.variation_name) {
					this.errors.variation.name = 'Variation name is required';
				}
				else if (!this.singleVariationData.variation.variation_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
					this.errors.variation.name = 'No special characters';
				}
				else{
					this.submitForm = true;
					this.$delete(this.errors.variation, 'name');
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