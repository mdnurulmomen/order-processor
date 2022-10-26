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
								Agent List
							</h2>

                        	<button type="button" 
                    				@click="showAgentCreateModal" 
                    				class="btn btn-secondary btn-sm float-right mb-2"
                    		>
					        	<i class="fa fa-plus-circle" aria-hidden="true"></i>
                                Add Agent
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
												<a :class="[{ 'active': currentTab=='current' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showCurrentAgents"
												>
													Current
												</a>
											</li>
											<li class="nav-item flex-fill">
												<a :class="[{ 'active': currentTab=='trashed' }, 'nav-link']" 
													data-toggle="tab" 
													@click="showTrashedAgents"
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
											<th scope="col">Username</th>
											<th scope="col">Merchant</th>
											<th scope="col">Mobile</th>
											<th scope="col">Email</th>
											<th scope="col">Agent Approved</th>
											<th scope="col">Action</th>
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="agentsToShow.length"
									    	v-for="(agent, index) in agentsToShow"
									    	:key="agent.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ agent.user_name | capitalize }}</td>
								    		<td>
								    			{{ 
								    				agent.merchant ? agent.merchant.name : 'Trashed' | capitalize 
								    			}}
								    			<span v-show="agent.merchant" 
								    				  :class="[agent.merchant ? agent.merchant.is_approved ? 'badge-success' : 'badge-danger' : '', 'right badge ml-1']"
								    			>
								    				{{ 
								    					agent.merchant ? agent.merchant.is_approved ? 'Approved' : 'Not-approved' : '' 
								    				}}
								    			</span>
								    		</td>
								    		<td>{{ agent.mobile }}</td>
								    		<td>{{ agent.email }}</td>
								    		<td>
								    			<span :class="[agent.is_approved ? 'badge-success' : 'badge-danger', 'right badge']"
								    			>
								    				{{ 
								    					agent.is_approved ? 'Approved' : 'Not-approved' 
								    				}}
								    			</span>
								    		</td>
								    		<td>
										      	<button type="button" v-show="agent.deleted_at === null" @click="showAgentEditModal(agent)" class="btn btn-primary btn-sm">
										        	<i class="fas fa-edit"></i>
										        	Edit
										      	</button>
								      			<button
								        			v-show="agent.deleted_at === null"
								        			type="button"
								        			@click="showMerchantAgentDeletionModal(agent)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-trash-alt"></i>
								        			Delete
								      			</button>
								      			<button
								        			v-show="agent.deleted_at !== null && agent.merchant !== null"
								        			type="button"
								        			@click="showAgentRestoreModal(agent)"
								        			class="btn btn-danger btn-sm"
							      				>
								        			<i class="fas fa-undo"></i>
								        			Restore
								      			</button>
								      			<p 	
								      				class="text-danger" 
								      				v-show="agent.merchant === null"
								      			>
								      				Trashed Merchant
								      			</p>
								    		</td>
									  	</tr>
									  	<tr v-show="!agentsToShow.length">
								    		<td colspan="7">
									      		<div class="alert alert-danger" role="alert">
									      			Sorry, No agent found.
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
										<option>50</option>
									</select>
								</div>
								<div class="col-sm-2">
									<button 
										type="button" 
										class="btn btn-primary btn-sm" 
										@click="query === '' ? fetchAllAgents() : searchData()"
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
										@paginate="query === '' ? fetchAllAgents() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>

			<!-- /.modal-createOrEdit-agent -->
			<div class="modal fade" id="modal-createOrEdit-agent">
				<div class="modal-dialog">
					<div class="modal-content bg-secondary">
						<div class="modal-header">
						  	<h4 class="modal-title">
						  		{{ editMode ? 'Edit' : 'Create' }} Agent
						  	</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form 
						  	class="form-horizontal" 
						  	v-on:submit.prevent=" editMode ? updateAgent() : storeAgent()"
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
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Merchant Name
								              		</label>
									                <div class="col-sm-8">
									                  	<multiselect 
				                                  			v-model="singleAgentData.merchantObject"
				                                  			placeholder="Merchant Name" 
					                                  		label="name" 
					                                  		track-by="id" 
					                                  		:options="allMerchants" 
					                                  		:required="true"
					                                  		:class="!errors.agent.merchant  ? 'is-valid' : 'is-invalid'"
					                                  		:allow-empty="false"
					                                  		selectLabel = "Press/Click"
					                                  		deselect-label="Can't remove single value"
					                                  		@close="validateFormInput('agent.merchantObject')"
				                                  		>
					                                	</multiselect>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.merchant }}
												  		</div>
									                </div>	
									              	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Agent Firstname
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleAgentData.agent.first_name" 
															placeholder="Agents Name" 
															required="true"
															:class="!errors.agent.first_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('agent.first_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.first_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Agent Lastname
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleAgentData.agent.last_name" 
															placeholder="Agents Name" 
															required="true"
															:class="!errors.agent.last_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('agent.last_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.last_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Agent Username
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleAgentData.agent.user_name" 
															placeholder="Login Username" 
															required="true"
															:class="!errors.agent.user_name  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('agent.user_name')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.user_name }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Agent Mobile
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleAgentData.agent.mobile" 
															placeholder="Agent Mobile" 
															required="true"
															:class="!errors.agent.mobile  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('agent.mobile')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.mobile }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Email
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="text" 
															class="form-control" 
															v-model="singleAgentData.agent.email" 
															placeholder="Login Email" 
															required="true"
															:class="!errors.agent.email  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('agent.email')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.email }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleAgentData.agent.password" 
															placeholder="Login Password" 
															:required="!editMode" 
															:class="!errors.agent.password  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('agent.password')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.password }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Confirm Password
								              		</label>
									                <div class="col-sm-8">
									                  	<input 
															type="password" 
															class="form-control" 
															v-model="singleAgentData.agent.password_confirmation" 
															placeholder="Confirm Password" 
															:required="!editMode"
															:class="!errors.agent.password_confirmation  ? 'is-valid' : 'is-invalid'"
															@keyup="validateFormInput('agent.password_confirmation')"
									                	>
									                	<div class="invalid-feedback">
												        	{{ errors.agent.password_confirmation }}
												  		</div>
									                </div>	
								              	</div>

								              	<div class="form-group row">	
								              		<label for="inputAgentName3" class="col-sm-4 col-form-label text-right">
								              			Admin Approval
								              		</label>
									                <div class="col-sm-8">
									                  	<toggle-button 
				                                  			:sync="true" 
				                                  			v-model="singleAgentData.agent.is_approved" 
				                                  			value="true" 
				                                  			:width="140"  
				                                  			:height="30" 
				                                  			:font-size="15" 
				                                  			:color="{checked: 'green', unchecked: 'red'}" 
				                                  			:labels="{checked: 'Approved', unchecked: 'Not-Approved' }"
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
								  		{{ editMode ? 'Update' : 'Create' }} Agent
								  	</button>
								</div>
							</div>
						</form>
					</div>
				<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal-createOrEdit-agent-->

			<!-- modal-agent-delete-confirmation -->
			<div class="modal fade" id="modal-agent-delete-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Agent Deletion</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="destroyAgent" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to delete this agent ?? </h5>
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
			<!-- /modal-agent-delete-confirmation -->

			<!-- modal-agent-restore-confirmation -->
			<div class="modal fade" id="modal-agent-restore-confirmation">
				<div class="modal-dialog">
					<div class="modal-content bg-danger">
						<div class="modal-header">
						  	<h4 class="modal-title">Agent Restoration</h4>
						  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						    	<span aria-hidden="true">&times;</span>
							</button>
						</div>
					  	<!-- form start -->
					  	<form class="form-horizontal" v-on:submit.prevent="restoreAgent()" autocomplete="off">
							<div class="modal-body">
					      		<input 
					      			type="hidden" 
					      			name="_token" 
					      			:value="csrf"
					      		>
					      		<h5>Are you sure want to restore agent ?? </h5>
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
			<!-- /.modal-agent-restore-confirmation -->

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	import axios from 'axios';
	import Multiselect from 'vue-multiselect';
	import { ToggleButton } from 'vue-js-toggle-button';

	var singleAgentData = {

    	agent : {
			first_name : null,
			last_name : null,
			user_name : null,
			email : null,
			mobile : null,
			password : null,
			merchant_id : null,
			is_approved : false,
    	},

    	merchantObject : {
    		// id : null,
			// email : null,
			// mobile : null,
    	}
    };

	var merchantAgentListData = {

      	query : '',
    	perPage : 10,
    	loading : false,
    	submitForm : true,

    	editMode : false,
    	
    	currentTab : 'current',
    	allAgents : [],
    	agentsToShow : [],

    	allMerchants : [],

    	pagination: {
        	current_page: 1
      	},

    	errors : {
    		agent : {},
    	},

        singleAgentData : singleAgentData,

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    components: { 
			Multiselect, // short form of Multiselect : Multiselect
			ToggleButton : ToggleButton,
		},

	    data() {
	        return merchantAgentListData;
		},

		created(){
			this.fetchAllAgents();
			this.fetchAllMerchants();
		},

		watch : {
			'singleAgentData.merchantObject' : function(merchantObject){
				if (merchantObject) {
					this.singleAgentData.agent.merchant_id = merchantObject.id;
				}else
					this.singleAgentData.agent.merchant_id = null;
			},
			query : function(val){
				if (val==='') {
					this.fetchAllAgents();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showCurrentAgents(){
				this.currentTab = 'current';
				this.showDataListOfSelectedTab();
			},
			showTrashedAgents(){
				this.currentTab = 'trashed';
				this.showDataListOfSelectedTab();
			},
			showDataListOfSelectedTab(){
				if (this.currentTab=='current') {
					this.agentsToShow = this.allAgents.current.data;
					this.pagination = this.allAgents.current;
				}else {
					this.agentsToShow = this.allAgents.trashed.data;
					this.pagination = this.allAgents.trashed;
				}
			},
			fetchAllMerchants(){
				this.loading = true;
				axios
					.get('/api/merchants/')
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allMerchants = response.data;
						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			fetchAllAgents(){
				this.loading = true;
				axios
					.get('/api/merchant-agents/' + this.perPage +'?page='+ this.pagination.current_page)
					.then(response => {
						if (response.status == 200) {
							this.loading = false;
							this.allAgents = response.data;
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
					this.fetchAllAgents();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchAllAgents();
				}else {
					this.pagination.current_page = 1;
					this.searchData();
				}
    		},
			showAgentCreateModal(){

				this.editMode = false;
				this.errors.agent = {};
				this.submitForm = true;

				this.singleAgentData.agent = {};
				this.singleAgentData.merchantObject = {};

				$('#modal-createOrEdit-agent').modal('show');
			},
    		storeAgent(){

				if (Object.keys(this.singleAgentData.merchantObject).length === 0) {
					this.errors.agent.merchant = 'Merchant name is required';
					this.submitForm = false;
					return;
				}
				
				axios
					.post('/merchant-agents/'+ this.perPage, this.singleAgentData.agent)
					.then(response => {

						if (response.status == 200) {
							this.singleAgentData.agent = {};

							this.allAgents = response.data;

							this.query = '';
							this.currentTab = 'current';
							this.showDataListOfSelectedTab();

							toastr.success(response.data.success, "Added");

							$('#modal-createOrEdit-agent').modal('hide');
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
			showAgentEditModal(agent) {
				this.editMode = true;
				this.submitForm = true;
				this.errors.agent = {};
				this.singleAgentData.agent = agent;
				this.singleAgentData.merchantObject = agent.merchant;
				$("#modal-createOrEdit-agent").modal("show");
			},
			updateAgent(){
				
				axios
					.put('/merchant-agents/' + this.singleAgentData.agent.id + '/' + this.perPage, this.singleAgentData.agent)
					.then(response => {

						if (response.status == 200) {

							this.singleAgentData.agent = {};

							if (this.query === '') {
								this.allAgents = response.data;
								this.showDataListOfSelectedTab();
							}
							else {
								this.pagination.current_page = 1;
								this.searchData();
							}

							$('#modal-createOrEdit-agent').modal('hide');

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
			showMerchantAgentDeletionModal(agent) {
				this.singleAgentData.agent = agent;
				$("#modal-agent-delete-confirmation").modal("show");
			},
			destroyAgent(){

				$("#modal-agent-delete-confirmation").modal("hide");

				axios
					.delete('/merchant-agents/'+this.singleAgentData.agent.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleAgentData.agent = {};

							if (this.query === '') {
								this.allAgents = response.data;
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
			showAgentRestoreModal(agent) {
				this.singleAgentData.agent = agent;
				$("#modal-agent-restore-confirmation").modal("show");
			},
			restoreAgent(){

				$("#modal-agent-restore-confirmation").modal("hide");

				axios
					.patch('/merchant-agents/'+this.singleAgentData.agent.id+'/'+this.perPage)
					.then(response => {
						if (response.status == 200) {
							
							this.singleAgentData.agent = {};

							if (this.query === '') {
								this.allAgents = response.data;
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
					"/api/merchant-agents/search/"+ this.query +"/" + this.perPage +
				    "?page=" +
				    this.pagination.current_page
				)
				.then(response => {
					this.allAgents = response.data;
					this.agentsToShow = this.allAgents.all.data;
					this.pagination = this.allAgents.all;
				})
				.catch(e => {
					console.log(e);
				});
			},
			validateFormInput (formInputName) {

				this.submitForm = false;

				switch(formInputName) {

					case 'agent.merchantObject' :

						if (Object.keys(singleAgentData.merchantObject).length === 0) {
							this.errors.agent.merchant = 'Merchant name is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'merchant');
						}

						break;

					case 'agent.first_name' :

						if (this.singleAgentData.agent.first_name && !this.singleAgentData.agent.first_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.agent.first_name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'first_name');
						}

						break;

					case 'agent.last_name' :

						if (this.singleAgentData.agent.last_name && !this.singleAgentData.agent.last_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.agent.last_name = 'No special characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'last_name');
						}

						break;	

					case 'agent.user_name' :

						if (!this.singleAgentData.agent.user_name) {
							this.errors.agent.user_name = 'Username is required';
						}
						else if (!this.singleAgentData.agent.user_name.match(/^[_A-z0-9]*((-|_|\w)*[_A-z0-9])*$/g)) {
							this.errors.agent.user_name = 'No space or specail characters';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'user_name');
						}

						break;

					case 'agent.email' :

						if (!this.singleAgentData.agent.email) {
							this.errors.agent.email = 'Email is required';
						}
						else if (!this.singleAgentData.agent.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.agent.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'email');
						}

						break;

					case 'agent.mobile' :

						if (!this.singleAgentData.agent.mobile) {
							this.errors.agent.mobile = 'Mobile is required';
						}
						else if (!this.singleAgentData.agent.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.agent.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'mobile');
						}

						break;

					case 'agent.password' :

						if (this.singleAgentData.agent.password && this.singleAgentData.agent.password.length < 8) {
							this.errors.agent.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'password');
						}

						break;

					case 'agent.password_confirmation' :

						if (this.singleAgentData.agent.password && this.singleAgentData.agent.password !== this.singleAgentData.agent.password_confirmation) {
							this.errors.agent.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.agent, 'password_confirmation');
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