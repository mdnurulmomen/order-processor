
<template>

	<div class="container-fluid">

		<section>
		
			<div class="row">
				<div class="col-sm-12">

					<div class="card">
						<div class="card-header">
							<h2 class="lead float-left mt-1">
								API List
							</h2>
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
											<th scope="col">API</th>
											<th scope="col">Method</th>
											<th scope="col">Purpose</th>
											<th scope="col">Parameters</th>
										</tr>
									</thead>
									<tbody>
										<tr 
									  		v-show="apiToShow.length"
									    	v-for="(api, index) in apiToShow"
									    	:key="api.id"
									  	>
									    	<td scope="row">{{ index }}</td>
								    		<td><mark>{{ api.url }}</mark></td>
								    		<td>{{ api.method }}</td>
								    		<td>{{ api.purpose }}</td>
								    		<td>{{ api.parameters }}</td>
									  	</tr>
								  		<tr 
									  		v-show="!apiToShow.length"
									  	>
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No api found.</div>
									    	</td>
									  	</tr>
									</tbody>
								</table>
							</div>	
						</div>
					</div>
				</div>	
			</div>

	    </section>

	</div>
    
</template>

<script type="text/javascript">

	var apiListData = {
    	
      	query : '',
    	allApi : [],
    	apiToShow : [],

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return apiListData;
		},

		created(){

			this.allApi = [
				{
					url : 'general-info',
					method : 'get',
					purpose : 'Settings Info',
					parameters : 'None',
				},
				{
					url : 'restaurants/{expectedLatitude}/{expectedLongitude}',
					method : 'get',
					purpose : 'Restaurant list',
					parameters : 'None',
				},
				{
					url : 'restaurant-menu-items/{expectedRestaurantId}',
					method : 'get',
					purpose : 'Restaurant menu items',
					parameters : 'None',
				},
			];

			this.showAllApi();

		},

		watch : {
			query : function(val){
				if (val==='') {
					this.showAllApi();
				}
				else {
					this.searchData();
				}
			}
		},

		methods : {
			showAllApi(){
				this.apiToShow = this.allApi; 
			},
		    searchData() {
				this.apiToShow = this.allApi.filter(
					currentObject => {
						if (currentObject.url.toLowerCase().includes(this.query.toLowerCase()) || currentObject.method.toLowerCase().includes(this.query.toLowerCase()) || currentObject.purpose.toLowerCase().includes(this.query.toLowerCase()) || currentObject.parameters.toLowerCase().includes(this.query.toLowerCase())) {

							return currentObject;

						}
					}
				);
			},
		}
  	}

</script>