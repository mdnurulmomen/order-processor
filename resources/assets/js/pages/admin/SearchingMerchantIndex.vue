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
						<div class="card-body">
							<div class="mb-3">
								<!-- form start -->
								<form 
									class="form-horizontal" 
									v-on:submit.prevent="showMerchantProducts()" 
									autocomplete="off" 
									novalidate="true"
								>
									<input type="hidden" name="_token" :value="csrf">

									<div class="form-group row text-center">
										<label for="inputMerchantName3" class="col-sm-12 col-form-label text-left">
					              			Search Merchant Name
					              		</label>

										<div class="col-sm-10 mb-2">
										  	<multiselect 
	                                  			v-model="merchant"
		                                  		label="name" 
		                                  		track-by="id" 
		                                  		:options="allMerchants" 
		                                  		:custom-label="objectNameWithCapitalized" 
		                                  		:required="true"
		                                  		:class="!errors.merchant  ? 'is-valid' : 'is-invalid'"
		                                  		:allow-empty="false"
	                                  			placeholder="Search Merchant" 
		                                  		selectLabel = "Press/Click to select"
		                                  		deselect-label="Can't remove single value"
		                                  		@close="validateFormInput()"
	                                  		>
		                                	</multiselect>
										  	<div 
										  		v-show="errors.merchant" 
										  		class="text-danger"
										  	>
										        {{ errors.merchant }}
										  	</div>
										</div>
										<div class="col-sm-2">
						                  	<button 
							                  	type="submit" 
							                  	class="btn btn-danger rounded-pill" 
							                  	:disabled="!submitForm"
						                  	>
							                    Show Products
						                  	</button>
						                  	<div 
							                  	class="text-danger small" 
							                  	v-show="!submitForm"
						                  	>
										  		Please select merchant
								          	</div>
										</div>
									</div>
								</form>
								<!-- form end -->
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
	import Multiselect from 'vue-multiselect';

	var data = {
      	errors : {},
      	loading : false,
      	merchant : {},
      	submitForm : true,
      	allMerchants : [],
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };

	export default {
		// Local registration of components
		components: { 
			Multiselect, // short form of Multiselect : Multiselect
		},

	    data() {
	        return data;
		},

		created(){
			this.fetchAllMerchants();
		},

		mounted(){
			this.merchant = {};
		},

		methods : {
			
			fetchAllMerchants(){
				this.loading = true;
				axios
					.get('/api/merchants/')
					.then(response => {
							
						if (response.status == 200) {
							this.allMerchants = response.data;
							this.loading = false;

						}
					})
					.catch(error => {
						console.log(error);
					});
			},
			showMerchantProducts(){
				if (Object.keys(this.merchant).length === 0) {
					this.submitForm = false;
					this.errors.merchant = 'Merchant name is required';
					return;
				}

				this.$router.push({
			 		name: 'merchant-all-products', 
			 		params: { 
			 			merchantId : this.merchant.id, 
			 			merchantName : this.merchant.name 
			 		}, 
				});
			},
			objectNameWithCapitalized ({ name, user_name }) {
		      	if (name) {
				    name = name.toString();
					const words = name.split(" ");

					for (let i = 0; i < words.length; i++) {
						
						if (words[i]) {

					    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);
						
						}
						
					}

					return words.join(" ");
		      	}
		      	else if (user_name) {
				    user_name = user_name.toString();
					const words = user_name.split(" ");

					for (let i = 0; i < words.length; i++) {
						
						if (words[i]) {

					    	words[i] = words[i][0].toUpperCase() + words[i].substr(1);
						
						}
						
					}

					return words.join(" ");
		      	}
		      	else 
		      		return ''
		    },
			validateFormInput () {
				this.submitForm = false;	
				if (Object.keys(this.merchant).length === 0) {
					this.errors.merchant = 'Merchant name is required';
				}
				else {
					this.submitForm = true;
					this.$delete(this.errors, 'merchant');
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