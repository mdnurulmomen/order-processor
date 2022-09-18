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
							<h2 class="lead float-left mt-1">My Meals</h2>
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
											<th scope="col">Name</th>
											<!-- <th scope="col">Action</th> -->
										</tr>
									</thead>
									<tbody>
									  	<tr v-show="mealsToShow.length"
									    	v-for="(merchantMeal, index) in mealsToShow"
									    	:key="merchantMeal.id"
									  	>
									    	<td scope="row">{{ index + 1 }}</td>
								    		<td>{{ merchantMeal.meal.name}}</td>
									  	</tr>
									  	<tr v-show="!mealsToShow.length">
								    		<td colspan="6">
									      		<div class="alert alert-danger" role="alert">Sorry, No meal found.</div>
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
										@click="query === '' ? fetchMerchantAllMeals() : searchData()"
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
										@paginate="query === '' ? fetchMerchantAllMeals() : searchData()"
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

	var mealListData = {
      	query : '',
    	perPage : 10,
    	loading : false,

    	allMeals : [],
    	mealsToShow : [],

    	pagination: {
        	current_page: 1
      	},

        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        merchant_id: document.querySelector('meta[name="merchant-id"]').getAttribute('content'),
    };

	export default {

	    data() {
	        return mealListData;
		},

		created(){
			this.fetchMerchantAllMeals();
		},

		watch : {
			query : function(val){
				if (val==='') {
					this.fetchMerchantAllMeals();
				}
				else {
					this.pagination.current_page = 1;
					this.searchData();
				}
			}
		},

		methods : {
			showDataListOfSelectedTab(){
				this.mealsToShow = this.allMeals.meals.data;
				this.pagination = this.allMeals.meals;
			},
			fetchMerchantAllMeals(){
				this.loading = true;
				axios
					.get('/merchant-meals/' + this.merchant_id + '/' + this.perPage +'?page='+ this.pagination.current_page)
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
					this.fetchMerchantAllMeals();
				}else {
					this.searchData();
				}
    		},
			reload() {
				if (this.query === '') {
					this.fetchMerchantAllMeals();
				}else {
					// this.pagination.current_page = 1;
					this.searchData();
				}
    		},
		    searchData() {
				
				axios
				.get(
					"/merchant-meals/" + this.merchant_id + '/' + this.query +"/" + this.perPage +
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

		}
  	}

</script>