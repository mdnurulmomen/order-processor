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
								Cancelation Reasons List
							</h2>

                        	<button 
                        		type="button" 
                        		@click="showReasonCreateModal" 
                        		class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Cancelation-Reason
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-6">
									  	<ul class="nav nav-tabs mb-2" v-show="query === ''">
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" data-toggle="tab" @click="showCurrentReasons">Current</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" data-toggle="tab" @click="showTrashedReasons">Trashed</a>
											</li>
										</ul>
									</div>

									<div class="col-sm-6 was-validated">
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
											<th scope="col">Reason Details</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="reasonsToShow.length"
									    	v-for="(reason, index) in reasonsToShow"
									    	:key="reason.id"
									  	>
									    	<td scope="row">
									    		{{ index + 1 }}
									    	</td>
								    		<td>
								    			<span v-html="reason.reason" class="text-capitalize"></span>
								    		</td>
								    		<td>
										      	<button 
										      		type="button" 
										      		@click="showReasonEditModal(reason)" 
										      		class="btn btn-primary btn-sm"
										      	>
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>

								      			<button 
								      				v-show="reason.deleted_at === null"
								        			type="button"
								        			@click="showReasonDeletionModal(reason)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>

								      			<button
								        			v-show="reason.deleted_at !== null"
								        			type="button"
								        			@click="showReasonRestoreModal(reason)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!reasonsToShow.length"
									  	>
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No reason found.
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
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllReasons() : searchData()"
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
										@paginate="query === '' ? fetchAllReasons() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-reason -->
			<div class="modal fade" id="modal-createOrEdit-reason">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Cancelation Reason
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateReason() : storeReason()"
						  	autocomplete="off"
					  	>
							<div class="modal-body text-dark">

					      		<input 
						      		type="hidden" 
						      		reason="_token" 
						      		:value="csrf"
					      		>

								<div class="row">
									<div class="col-sm-12">
										<div class="card card-outline">
								            <div class="card-body">
								              	<div class="form-group row">
									              		
								              		<label for="inputMenuName3" class="col-sm-4 col-form-label text-right">
								              			Reason Detail
								              		</label>
									                <div class="col-sm-8">

									                	<ckeditor 
							                              	class="form-control" 
							                              	:editor="editor" 
							                              	required="true" 
							                              	v-model="singleReasonData.reason.reason"
							                              	:class="!errors.reason.reason  ? 'is-valid' : 'is-invalid'" 
							                              	placeholder="Reason Detail"
							                              	@blur="validateFormInput()"
							                            >
						                              	</ckeditor>
									                  
									                	<div class="invalid-feedback">
												        	{{ errors.reason.reason }}
												  		</div>
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
								  		{{ editMode ? 'Update' : 'Create' }} Reason
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-reason-->

			<!-- modal-reason-delete-confirmation -->
			<div class="modal fade" id="modal-reason-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Reason Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyReason" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			reason="_token" 
					      			:value="csrf"
					      		>
					      		<h5>
					      			Are you sure want to delete this menu category ?? 
					      		</h5>
					      		<h5 style="color:#c6cacc">
					      			<small>
					      				But once you want, you can retreive it from bin.
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
			<!-- /modal-reason-delete-confirmation -->

			<!-- modal-reason-restore-confirmation -->
			<div class="modal fade" id="modal-reason-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Reason Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreReason()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore reason ?? </h5>
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
			<!-- /.modal-reason-restore-confirmation -->
	    </section>
	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	var singleReasonData = {
    	reason : {
			// id : null,
			// reason : null,
			// deleted_at : null,
    	},
    };

	var menuCategoryListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	currentTab : 'current',

    	allReasons : [],
    	reasonsToShow : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		reason : {},
    	},

    	editor: ClassicEditor,
        singleReasonData : singleReasonData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
	    // Local registration of components
		components: { 
			ckeditor: CKEditor.component,
		},

	    data() {
	        return menuCategoryListData;
		},

		created(){
			this.fetchAllReasons();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllReasons();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {

			fetchAllReasons(){
				this.loading = true;
				axios
					.get('/api/cancellation-reasons/' + this.perPage + '?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allReasons = response.data;
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
					this.fetchAllReasons();
				}else {
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllReasons();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showReasonCreateModal(){

				this.editMode = false;
				this.submitForm = true;
				this.errors.reason = {};

				this.singleReasonData.reason = {};

				$('#modal-createOrEdit-reason').modal('show');
			},
    		storeReason(){

				this.validateFormInput();

				if (!this.submitForm) {
					return;
				}

				$('#modal-createOrEdit-reason').modal('hide');
				
				axios
					.post('/cancellation-reasons/' + this.perPage, this.singleReasonData.reason)
					.then(response => {

						if (response.status == 200) {

							this.singleReasonData.reason = {};

							this.query = '';
							this.currentTab = 'current';

							this.allReasons = response.data;
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
			showReasonEditModal(reason) {
				
				this.editMode = true;
				this.submitForm = true;
				this.errors.reason = {};
				
				this.singleReasonData.reason = reason;
				
				$("#modal-createOrEdit-reason").modal("show");
			},
			updateReason(){

				this.validateFormInput();

				if (!this.submitForm) {
					return;
				}

				$('#modal-createOrEdit-reason').modal('hide');
				
				axios
					.put('/cancellation-reasons/' + this.singleReasonData.reason.id + '/' + this.perPage, this.singleReasonData.reason)
					.then(response => {

						if (response.status == 200) {

							this.singleReasonData.reason = {};

							if (this.query === '') {
								this.allReasons = response.data;
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
			showReasonDeletionModal(reason) {
				this.singleReasonData.reason = reason;
				$("#modal-reason-delete-confirmation").modal("show");
			},
			destroyReason(){

				$("#modal-reason-delete-confirmation").modal("hide");

				axios
					.delete('/cancellation-reasons/' + this.singleReasonData.reason.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleReasonData.reason = {};

							if (this.query === '') {
								this.allReasons = response.data;
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
			showReasonRestoreModal(reason) {
				this.singleReasonData.reason = reason;
				$("#modal-reason-restore-confirmation").modal("show");
			},
			restoreReason(){

				$("#modal-reason-restore-confirmation").modal("hide");

				axios
					.patch('/cancellation-reasons/' + this.singleReasonData.reason.id + '/' + this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleReasonData.reason = {};

							if (this.query === '') {
								this.allReasons = response.data;
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
					"/api/cancellation-reasons/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allReasons = response.data;
					this.reasonsToShow = this.allReasons.all.data;
					this.pagination = this.allReasons.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			showCurrentReasons(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedReasons(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.reasonsToShow = this.allReasons.current.data;
					this.pagination = this.allReasons.current;
				}else {
					this.reasonsToShow = this.allReasons.trashed.data;
					this.pagination = this.allReasons.trashed;
				}
			},
			validateFormInput () {
				
				this.submitForm = false;

				if (!this.singleReasonData.reason.reason) {
					this.errors.reason.reason = 'Reason is required';
				}
				else{
					this.submitForm = true;
					this.$delete(this.errors.reason, 'reason');
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