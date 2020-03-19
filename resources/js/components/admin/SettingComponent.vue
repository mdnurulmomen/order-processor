
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
	
		  	<!-- Nav tabs -->
			<ul class="nav nav-pills nav-tabs nav-justified mb-4" v-show="!loading" >
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#payment">Payment</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#contact">Contact</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#delivery">Delivery</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#others">Others</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">

				<div class="tab-pane container active" id="payment">	
					<div class="row">
						<div  v-show="!loading" class="col-sm-12">
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form class="form-horizontal" v-on:submit.prevent="updateSetting">
						      		
						      		<input type="hidden" name="_token" :value="csrf">
						            
						            <div class="card-body box-profile">  

						              	<div class="form-group row">
						              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">Vat Rate</label>
							                <div class="col-sm-9">
							                	<div class="input-group mb-3">
													<input 
														type="number" 
														class="form-control" 
														v-model="applicationSettings.vat_rate" 
														min="1" 
														max="100" 
														step=".1" 
														placeholder="Vat Rate" 
														required="true"
														:class="!errors.applicationSettings.vat_rate  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('vat_rate')"
								                	>
													<div class="input-group-append">
														<span class="input-group-text">
															%
														</span>
													</div>
								                	<div class="invalid-feedback">
											        	{{ errors.applicationSettings.vat_rate }}
											  		</div>
												</div>
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputNewnumber3" class="col-sm-3 col-form-label text-right">Official Bank </label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.official_bank" 
							                  		placeholder="Bank Name" 
							                  		required="true" 
							                  		:class="!errors.applicationSettings.official_bank  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('official_bank')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.applicationSettings.official_bank }}
										  		</div>
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">Bank Account Number : </label>
							                <div class="col-sm-9">
							                  	<input  
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.bank_account_number" 
							                  		placeholder="Bank Account Number" 
							                  		required="true" 
							                  		:class="!errors.applicationSettings.bank_account_number  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('bank_account_number')"
							                  	>

							                  	<div class="invalid-feedback">
										        	{{ errors.applicationSettings.bank_account_number }}
										  		</div>
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">Account Holder Name : </label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.bank_account_holder_name" 
							                  		placeholder="Account Holder Number" 
							                  		required="true" 
							                  		:class="!errors.applicationSettings.bank_account_holder_name  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('bank_account_holder_name')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.applicationSettings.bank_account_holder_name }}
										  		</div>
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">Merchant Number : </label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="tel" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.merchant_number" 
							                  		placeholder="Common Merchant Number" 
							                  		required="true" 
							                  		:class="!errors.applicationSettings.merchant_number  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('merchant_number')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.applicationSettings.merchant_number }}
										  		</div>
							                </div>
						              	</div>

						            </div>
						            <!-- /.card-body -->
						            <div class="card-footer text-center">
						              	<button type="submit" :disabled="loading" class="btn btn-primary">Update Payment Settings</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane container fade" id="contact">	
					<div class="row">
						<div  v-show="!loading" class="col-sm-12">
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form class="form-horizontal" v-on:submit.prevent="updateSetting">
						      		
						      		<input type="hidden" name="_token" :value="csrf">
						            
						            <div class="card-body box-profile">  

						              	<div class="form-group row">
							              	<div class="col-6">
							              		<div class="row">
								              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">Customer Care Number</label>
									                <div class="col-sm-8">
									                  <input 
									                  	type="tel" 
									                  	class="form-control" 
									                  	v-model="applicationSettings.official_customer_care_number" 
									                  	placeholder="Official Customer Care Number" 
									                  	:class="!errors.applicationSettings.official_customer_care_number  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('official_customer_care_number')"
									                  >
									                  <div class="invalid-feedback">
											        	{{ errors.applicationSettings.official_customer_care_number }}
												  	  </div>
									                </div>
							              		</div>
							              	</div>
							                <div class="col-6">
							              		<div class="row">
								              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">Mail Address</label>
									                <div class="col-sm-8">
									                  <input 
								                  		type="email" 
									                  	class="form-control" 
									                  	v-model="applicationSettings.official_mail_address" 
									                  	placeholder="Mail Address" 
									                  	:class="!errors.applicationSettings.official_mail_address  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('official_mail_address')"
									                  >
									                  	<div class="invalid-feedback">
												        	{{ errors.applicationSettings.official_mail_address }}
												  		</div>
									                </div>
							              		</div>
							              	</div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputLastName3" class="col-sm-2 col-form-label text-right">Contact Address</label>
							                <div class="col-sm-10">
							                  	<ckeditor 
					                              	class="form-control" 
					                              	:editor="editor" 
					                              	v-model="applicationSettings.official_contact_address" 
					                              	placeholder="Contact Address" 
					                              	:class="!errors.applicationSettings.official_contact_address  ? 'is-valid' : 'is-invalid'"
					                              	@blur="validateFormInput('official_contact_address')"
					                            >
				                              	</ckeditor>
				                              	<div class="invalid-feedback">
										        	{{ errors.applicationSettings.official_contact_address }}
										  		</div>
							                </div>
						              	</div>
						            </div>
						            <!-- /.card-body -->
						            <div class="card-footer text-center">
						              	<button type="submit" :disabled="loading" class="btn btn-primary">Update Delivery Setting</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane container fade" id="delivery">	
					<div class="row">
						<div  v-show="!loading" class="col-sm-12">
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form class="form-horizontal" v-on:submit.prevent="updateSetting">
						      		
						      		<input type="hidden" name="_token" :value="csrf">
						            
						            <div class="card-body box-profile">  

						              	<div class="form-group row">
							              	<div class="col-6">
							              		<div class="row">
								              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">Delivery Charge</label>
									                <div class="col-sm-8">
									                  <input 
									                  	type="number" 
														class="form-control" 
														v-model="applicationSettings.delivery_charge" 
														min="1" 
														step="1" 
														placeholder="Delivery Charge" 
														required="true"
														:class="!errors.applicationSettings.delivery_charge  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('delivery_charge')"
									                  >
									                  	<div class="invalid-feedback">
												        	{{ errors.applicationSettings.delivery_charge }}
												  		</div>
									                </div>
							              		</div>
							              	</div>
							                <div class="col-6">
							                	<div class="row">
								              		<label for="inputLastName3" class="col-sm-4 col-form-label text-right"> Delivery Fee Percentage (for multiple)</label>
									                <div class="col-sm-8">
									                	<div class="input-group mb-3">
															<input 
																type="number" 
																class="form-control" 
																v-model="applicationSettings.multiple_delivery_charge_percentage" 
																min="1" 
																max="100" 
																step="1" 
																placeholder="For Multiple Delivery" 
																required="true"
																:class="!errors.applicationSettings.multiple_delivery_charge_percentage  ? 'is-valid' : 'is-invalid'"
																@keyup="validateFormInput('multiple_delivery_charge_percentage')"
										                	>
															<div class="input-group-append">
																<span class="input-group-text">
																	%
																</span>
															</div>
										                	<div class="invalid-feedback">
													        	{{ errors.applicationSettings.multiple_delivery_charge_percentage }}
													  		</div>
														</div>
									                </div>
							                	</div>
							              	</div>
						              	</div>

						            </div>
						            <!-- /.card-body -->
						            <div class="card-footer text-center">
						              	<button type="submit" :disabled="loading" class="btn btn-primary">Update Delivery Setting</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane container fade" id="others">	
					<div class="row">

						<div  v-show="!loading" class="col-sm-12">
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form class="form-horizontal" v-on:submit.prevent="updateSetting">
						      		
						      		<input type="hidden" name="_token" :value="csrf">
						            
					              	<div class="card-body box-profile">

					                	<div class="text-center mb-3">
					                  		<img class="profile-user-img img-fluid img-circle" :src="applicationSettings.favicon_icon" alt="Application favicon">
					                	</div>

					                	<div class="row">
						              		<!-- <label for="inputMobile3" class="col-sm-4 col-form-label text-right">Picture</label> -->
							                <div class="col-sm-12">
							                  	<div class="input-group">
								                    <div class="custom-file">
								                        <input 
								                        	type="file" 
								                        	class="custom-file-input" 
								                        	id="exampleInputFile" 
								                        	v-on:change="onImageChange" 
								                        	accept="image/*"
								                        >
								                        <label class="custom-file-label" for="exampleInputFile">Change Picture</label>
								                    </div>
							                    </div>
							                </div>
						            	</div>
					              	</div>
					              	<!-- /.card-body -->
						            
						            <div class="card-footer text-center">
						              	<button type="submit" :disabled="loading" class="btn btn-primary">Update Other</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
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
	import CKEditor from '@ckeditor/ckeditor5-vue';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	export default {

	    components: { 
			ckeditor: CKEditor.component,
		},

	    data() {
	        return {

	        	editor: ClassicEditor,

	        	applicationSettings : {
	        		vat_rate : null,
	        		official_bank : null,
	        		bank_account_number : null,
	        		bank_account_holder_name : null,
	        		merchant_number : null,
	        		
	        		official_customer_care_number : null,
	        		official_mail_address : null,
	        		official_contact_address : null,

	        		delivery_charge : null,
	        		multiple_delivery_charge_percentage : null,

	        		favicon_icon : null,
	        	},

	        	newFavicon : null,
	        	loading : false,
	        	errors : {},
	        	submitForm : true,
	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
	        }
		},
		created(){
			this.fetchSettingData();
		},
		methods : {
			fetchSettingData() {
				this.loading = true;
				this.applicationSettings = false;
				axios
					.get('/api/settings')
					.then(response => {
						this.loading = false;
						this.applicationSettings = response.data;
					});
			},
			updateSetting() {

				this.applicationSettings.favicon_icon = this.newFavicon;

				axios
					.post('/settings', this.applicationSettings)
					.then(response => {
						if (response.status == 200) {
							toastr.success(response.data.success, "Success");
						}
					})
					.catch(error => {

						if (error.response.status == 422) {

							for (var x in error.response.data.errors) {
								toastr.warning(error.response.data.errors[x], "Warning");
							}
				      	}

					});
			},
			onImageChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
                	this.createImage(files[0]);
		      	}

		      	return;
			},
			createImage(file) {
                let reader = new FileReader();
                reader.onload = (evnt) => {
                    this.newFavicon = this.applicationSettings.favicon_icon = evnt.target.result;
                };
                reader.readAsDataURL(file);
            },
            validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'vat_rate' :

						if (!this.applicationSettings.vat_rate) {
							this.errors.applicationSettings.vat_rate = 'Vat rate is required';
						}
						else if (this.applicationSettings.vat_rate < 0 || this.applicationSettings.vat_rate > 100 ) {
							this.errors.applicationSettings.vat_rate = 'Rate should be 1 to 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'vat_rate');
						}

						break;

					case 'official_bank' :

						if (!this.singleRestaurantAdminData.restaurantAdmin.email) {
							this.errors.restaurantAdmin.email = 'Email is required';
						}
						else if (!this.singleRestaurantAdminData.restaurantAdmin.email.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.restaurantAdmin.email = 'Invalid Email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'email');
						}

						break;

					case 'restaurantAdmin.mobile' :

						if (!this.singleRestaurantAdminData.restaurantAdmin.mobile) {
							this.errors.restaurantAdmin.mobile = 'Mobile is required';
						}
						else if (!this.singleRestaurantAdminData.restaurantAdmin.mobile.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.restaurantAdmin.mobile = 'Invalid mobile number';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'mobile');
						}

						break;

					case 'restaurantAdmin.password' :

						if (this.singleRestaurantAdminData.restaurantAdmin.password && this.singleRestaurantAdminData.restaurantAdmin.password.length < 8) {
							this.errors.restaurantAdmin.password = 'Password length has to be 8';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'password');
						}

						break;

					case 'restaurantAdmin.password_confirmation' :

						if (this.singleRestaurantAdminData.restaurantAdmin.password && this.singleRestaurantAdminData.restaurantAdmin.password !== this.singleRestaurantAdminData.restaurantAdmin.password_confirmation) {
							this.errors.restaurantAdmin.password_confirmation = "Password doesn't match" ;
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.restaurantAdmin, 'password_confirmation');
						}

						break;
				}
	 
			},
		}
  	}

</script>