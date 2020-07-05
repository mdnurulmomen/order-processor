
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
								Notifications List
							</h2>

                        	<button 
                        		type="button" 
                        		@click="showNotificationCreateModal" 
                        		class="btn btn-secondary btn-sm float-right mb-2"
                        	>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Notification
					      	</button>
						</div>

						<div class="card-body">
							<div class="mb-3">
								<div class="row">
									<div class="col-sm-12 was-validated">
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
											<th scope="col">Title</th>
											<th scope="col">Banner</th>
											<th scope="col">
												Description
											</th>
											<th scope="col">Status</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr 
									  		v-show="allNotifications.length"
									    	v-for="(notification, index) in allNotifications"
									    	:key="notification.id"
									  	>
									    	<td scope="row">
									    		{{ index + 1 }}
									    	</td>
								    		<td>
								    			{{ notification.title }}
								    		</td>
								    		<td>
								    			<img 
								    				class="profile-user-img img-fluid" 
								    				:src="notification.banner" alt="Notification Banner"
								    			>
								    		</td>
								    		<td>
								    			<span v-html="notification.description">
								    				
								    			</span>
								    		</td>
								    		<td>
								    			<span 
								    				  :class="[notification.status ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					notification.status ? 'Published' : 'Unpublished'
								    				}}
								    			</span>
								    		</td>
								    		<td>
										      	<button 
										      		type="button" 
										      		@click="showNotificationEditModal(notification)" 
										      		class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
										      	<button 
										      		type="button" 
										      		@click="showNotificationDeleteModal(notification)" 
										      		class="btn btn-danger btn-sm">
										        	<i class="fas fa-trash"></i>
										        	Delete
										      	</button>
								    		</td>
									  	</tr>
									  	<tr 
									  		v-show="!allNotifications.length"
									  	>
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No notification found.</div>
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
										@click="query === '' ? fetchAllNotifications() : searchData()"
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
										@paginate="query === '' ? fetchAllNotifications() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-notification -->
			<div class="modal fade" id="modal-createOrEdit-notification">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Notification
						  	</h4>
						  	
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateNotification() : storeNotification()"
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
									              		
								              		<label for="inputNotificationTitle3" class="col-sm-4 col-form-label text-right">
								              			Title
								              		</label>
									                <div class="col-sm-8">
									                	
														<input 
															type="text" 
															class="form-control" 
															v-model="singleNotificationData.notification.title" 
															placeholder="Notification Title" 
															required="true"
															:class="!errors.notification.title  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('notification.title')"
									                	>
														
									                	<div class="invalid-feedback">
												        	{{ errors.notification.title }}
												  		</div>
									                  	
									                </div>	
									              	
								              	</div>


								              	<div class="form-group row">	
								              		<div class="col-sm-4 text-center">
								                  		<img class="profile-user-img img-fluid" :src="singleNotificationData.notification.banner" alt="Notification Banner">
								                	</div>

									                <div class="col-sm-8">
									                  	<div class="input-group">
										                    <div class="custom-file">
										                        <input 
										                        	type="file" 
										                        	class="form-control custom-file-input" 
										                        	v-on:change="onBannerChange" 
										                        	accept="image/*" 
										                        	:required="!editMode" 
										                        	:class="!errors.notification.banner  ? 'is-valid' : 'is-invalid'"
										                        >
										                        <label class="custom-file-label" for="exampleInputFile">
										                        	Change Banner
										                        </label>
										                    </div>
									                    </div>

									                	<div 
									                		class="text-danger" 
									                		v-show="errors.notification.banner"
									                	>
												        	{{ errors.notification.banner }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Description
								              		</label>
									                <div class="col-sm-8">
									                  	<ckeditor 
							                              	class="form-control" 
							                              	:editor="editor" 
							                              	v-model="singleNotificationData.notification.description"
							                              	:class="!errors.notification.description  ? 'is-valid' : 'is-invalid'"
							                              	@blur="validateFormInput('notification.description')"
							                            >
						                              	</ckeditor>

						                              	<div 
						                                  	class="invalid-feedback" 
						                                >
													        {{ errors.notification.description }}
													  	</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputDeliveryManName3" class="col-sm-4 col-form-label text-right">
								              			Status
								              		</label>
									                <div class="col-sm-8">
									                  	<toggle-button 
				                                  			:sync="true" 
				                                  			v-model="singleNotificationData.notification.status" 
				                                  			value="true" 
				                                  			:width="130"  
				                                  			:height="30" 
				                                  			:font-size="15" 
				                                  			:color="{checked: 'green', unchecked: 'red'}" 
				                                  			:labels="{checked: 'Published', unchecked: 'Unpublished' }"
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
							  		{{ editMode ? 'Update' : 'Create' }} Notification
							  	</button>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-notification-->

			<!-- modal-notification-delete-confirmation -->
			<div class="modal fade" id="modal-notification-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Notification Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyNotification" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5 style="color:#c6cacc">
					      			<small>
					      				Once you delete, you can't retreive it again.
					      			</small>
					      		</h5>
					      		<h5>
					      			Are you sure want to delete notification ?? 
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
			<!-- /modal-notification-delete-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import { ToggleButton } from 'vue-js-toggle-button';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	var singleNotificationData = {
    	notification : {
			// id : null,
			// title : null,
			// banner : null,
			// description : null,
			// status : null,
			// editor_id : null,
    	},
    };

	var notificationListData = {
      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	editor: ClassicEditor,
    	
    	newBanner : null,
    	currentTab : 'current',
    	allNotifications : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		notification : {},
    	},

        singleNotificationData : singleNotificationData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

		components: { 
			ToggleButton : ToggleButton,
			ckeditor: CKEditor.component,
		},

	    data() {
	        return notificationListData;
		},

		created(){
			this.fetchAllNotifications();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchAllNotifications();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			
			fetchAllNotifications(){
				this.loading = true;
				axios
					.get('/api/notifications/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allNotifications = response.data.data;
							this.pagination = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			changeNumberContents() {
				this.pagination.current_page = 1;
				if (this.query === '') {
					this.fetchAllNotifications();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllNotifications();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
    		onBannerChange(evnt){
    			let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {

					this.$delete(this.errors.notification, 'banner');
                	this.createImage(files[0]);
		      	}
		      	else {

		      		this.errors.notification.banner = 'Please upload an image';
		      		return;
		      	}
    		},
    		createImage(file, $variable) {
                let reader = new FileReader();
                reader.onload = (evnt) => {
                	
            		this.newBanner = this.singleNotificationData.notification.banner = evnt.target.result;
                    
                };
                reader.readAsDataURL(file);
            },
			showNotificationCreateModal(){

				this.editMode = false;
				this.submitForm = true;
				this.errors.notification = {};

				this.singleNotificationData.notification = {};
				$('#modal-createOrEdit-notification').find('form')[0].reset();

				$('#modal-createOrEdit-notification').modal('show');
			},
    		storeNotification(){

				$('#modal-createOrEdit-notification').modal('hide');
				
				axios
					.post('/notifications/'+ this.perPage, this.singleNotificationData.notification)
					.then(response => {

						if (response.status == 200) {
							this.singleNotificationData.notification = {};

							this.query = '';
							
							this.allNotifications = response.data.data;
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
			showNotificationEditModal(notification) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.notification = {};

				this.singleNotificationData.notification = notification;

				$("#modal-createOrEdit-notification").modal("show");
			},
			updateNotification(){

				$('#modal-createOrEdit-notification').modal('hide');
				
				axios
					.put('/notifications/' + this.singleNotificationData.notification.id + '/' + this.perPage, this.singleNotificationData.notification)
					.then(response => {

						if (response.status == 200) {

							this.singleNotificationData.notification = {};

							if (this.query === '') {
								this.allNotifications = response.data.data;
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
			showNotificationDeleteModal(notification){
				this.singleNotificationData.notification = notification;
				$("#modal-notification-delete-confirmation").modal("show");
			},
			destroyNotification(){

				$('#modal-notification-delete-confirmation').modal('hide');
				
				axios
					.delete('/notifications/' + this.singleNotificationData.notification.id + '/' + this.perPage)
					.then(response => {

						if (response.status == 200) {

							this.singleNotificationData.notification = {};

							if (this.query === '') {
								this.allNotifications = response.data.data;
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
					"/api/notifications/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allNotifications = response.data.all.data;
					this.pagination = response.data.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'notification.title' :

						if (!this.singleNotificationData.notification.title) {
							this.errors.notification.title = 'Title is required';
						}
						else if (this.singleNotificationData.notification.title && !this.singleNotificationData.notification.title.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.notification.title = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.notification, 'title');
						}

						break;	


					case 'notification.description' :

						if (!this.singleNotificationData.notification.description) {
							this.errors.notification.description = 'Description is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.notification, 'description');
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