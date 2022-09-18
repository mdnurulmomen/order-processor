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
							<h2 class="lead float-left mt-1">My Agents</h2>
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
											<th scope="col">Username</th>
											<th scope="col">Mobile</th>
											<th scope="col">Email</th>
											<th scope="col">Restaurant</th>
											<th scope="col">Admin Approval</th>
											<!-- <th scope="col">Action</th> -->
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="agentsToShow.length"
									    	v-for="(agent, index) in agentsToShow"
									    	:key="agent.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ agent.user_name }}</td>
								    		<td>{{ agent.mobile }}</td>
								    		<td>{{ agent.email }}</td>
								    		<td>{{ agent.merchant.name }}</td>
								    		<td>
								    			<toggle-button 
		                                  			:sync="true" 
		                                  			v-model="agent.admin_approval" 
		                                  			:width="130"  
		                                  			:height="30" 
		                                  			:font-size="15" 
		                                  			:color="{checked: 'green', unchecked: 'red'}" 
		                                  			:labels="{checked: 'Approved', unchecked: 'Not-Approved' }" 
		                                  			:disabled="true"
	                                  			/>
								    		</td>
									  	</tr>
									  	<tr v-show="!agentsToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No agent found.</div>
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
										@click="query === '' ? fetchMerchantAllAgents() : searchData()"
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
										@paginate="query === '' ? fetchMerchantAllAgents() : searchData()"
									>
									</pagination>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>
	    </section>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	import { ToggleButton } from 'vue-js-toggle-button';

	var agentListData = {
      	query : '',
    	perPage : 10,
    	loading : false,

    	allAgents : [],
    	agentsToShow : [],

    	pagination: {
        	current_page: 1
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        merchant_id: document.querySelector('meta[name="merchant-id"]').getAttribute('content'),
    };

	export default {

	    // Local registration of components
		components: {
			ToggleButton : ToggleButton, 
		},

	    data() {
	        return agentListData;
		},

		created(){
			this.fetchMerchantAllAgents();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchMerchantAllAgents();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showDataListOfSelectedTab(){
				this.agentsToShow = this.allAgents.agents.data;
				this.pagination = this.allAgents.agents;
			},
			fetchMerchantAllAgents(){
				this.loading = true;
				axios
					.get('/merchant-agents/' + this.merchant_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
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
					this.fetchMerchantAllAgents();
				}else {
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchMerchantAllAgents();
				}else {
					// this.pagination.current_page = 1;
					this.searchData();
				}
    		},
		    searchData() {	
				axios
				.get(
					"/merchant-agents/" + this.merchant_id + '/' + this.query +"/" + this.perPage +
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

		}
  	}

</script>