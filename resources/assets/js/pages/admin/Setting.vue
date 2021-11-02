
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
					<a class="nav-link active" data-toggle="tab" href="#payment">
						Payment
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#contact">
						Contact
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#delivery">
						Delivery
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#others">
						System
					</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">

				<div class="tab-pane fade show active" id="payment">	
					<div class="row">
						<div  
							v-show="!loading" 
							class="col-sm-12"
						>
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form class="form-horizontal" v-on:submit.prevent="updatePaymentSetting">
						      		
						      		<input 
							      		type="hidden" 
							      		name="_token" 
							      		:value="csrf"
						      		>
						            
						            <div class="card-body box-profile">  

						              	<div class="form-group row">
						              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">
						              			Vat Rate
						              		</label>
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
						              		<label for="inputNewnumber3" class="col-sm-3 col-form-label text-right">
						              			Official Bank
						              		</label>
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
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">
						              			Bank Account Number  
						              		</label>
							                <div class="col-sm-9">
							                  	<input  
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.official_bank_account_number" 
							                  		placeholder="Bank Account Number" 
							                  		required="true" 
							                  		:class="!errors.applicationSettings.official_bank_account_number  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('official_bank_account_number')"
							                  	>

							                  	<div class="invalid-feedback">
										        	{{ errors.applicationSettings.official_bank_account_number }}
										  		</div>
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">
						              			Account Holder Name 
						              		</label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.official_bank_account_holder_name" 
							                  		placeholder="Account Holder Number" 
							                  		required="true" 
							                  		:class="!errors.applicationSettings.official_bank_account_holder_name  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('official_bank_account_holder_name')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.applicationSettings.official_bank_account_holder_name }}
										  		</div>
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">
						              			Merchant Number 
						              		</label>
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
						            	<div class="col-sm-12">
											<span class="text-danger p-0 m-0 small" v-show="!submitForm">
										  		Please input all required fields
										  	</span>
										</div>
						              	<button 
							              	type="submit" 
							              	:disabled="loading || !submitForm" 
							              	class="btn btn-primary"
						              	>
						              		Update Payment Settings
						              	</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane fade show fade" id="contact">	
					<div class="row">
						<div  
							v-show="!loading" 
							class="col-sm-12"
						>
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form 
							      	class="form-horizontal" 
							      	v-on:submit.prevent="updateContactSetting"
						      	>
						      		
						      		<input 
							      		type="hidden" 
							      		name="_token" 
							      		:value="csrf"
						      		>
						            
						            <div class="card-body box-profile">  

						              	<div class="form-group row">
							              	<div class="col-6">
							              		<div class="row">
								              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">
								              			Customer Care Number
								              		</label>
									                <div class="col-sm-8">
									                  <input 
									                  	type="tel" 
									                  	class="form-control" 
									                  	v-model="applicationSettings.official_customer_care_number" 
									                  	placeholder="Official Customer Care Number" 
									                  	required="true"  
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
								              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">
								              			Mail Address
								              		</label>
									                <div class="col-sm-8">
									                  <input 
								                  		type="email" 
									                  	class="form-control" 
									                  	v-model="applicationSettings.official_mail_address" 
									                  	placeholder="Mail Address" 
									                  	required="true"  
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
						              		<label for="inputLastName3" class="col-sm-2 col-form-label text-right">
						              			Contact Address
						              		</label>
							                <div class="col-sm-10">
							                  	<ckeditor 
					                              	class="form-control" 
					                              	:class="!errors.applicationSettings.official_contact_address  ? 'is-valid' : 'is-invalid'"
					                              	:editor="editor" 
					                              	v-model="applicationSettings.official_contact_address" 
					                              	placeholder="Contact Address" 
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
						            	<div class="col-sm-12">
											<span 
												class="text-danger p-0 m-0 small" 
												v-show="!submitForm"
											>
										  		Please input all required fields
										  	</span>
										</div>
						              	<button 
							              	type="submit" 
							              	:disabled="loading || !submitForm" 
							              	class="btn btn-primary"
						              	>
						              		Update Contact Setting
						              	</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane fade show fade" id="delivery">	
					<div class="row">
						<div  v-show="!loading" class="col-sm-12">
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form 
							      	class="form-horizontal" 
							      	v-on:submit.prevent="updateDeliverySetting"
						      	>
						      		
						      		<input 
							      		type="hidden" 
							      		name="_token" 
							      		:value="csrf"
						      		>
						            
						            <div class="card-body box-profile">  

						              	<div class="form-group row">
							              	<div class="col-6">
							              		<div class="row">
								              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">
								              			Delivery Charge
								              		</label>
									                <div class="col-sm-8">
									                  <input 
									                  	type="number" 
														class="form-control" 
														v-model="applicationSettings.delivery_charge" 
														min="0" 
														step="1" 
														required="true"
														placeholder="Delivery Charge" 		
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
								              		<label for="inputLastName3" class="col-sm-4 col-form-label text-right">
								              		 	Delivery Fee Percentage (for multiple)
								              		</label>
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
						            	<div class="col-sm-12">
											<span 
												class="text-danger p-0 m-0 small" 
												v-show="!submitForm"
											>
										  		Please input all required fields
										  	</span>
										</div>
						              	<button 
							              	type="submit" 
							              	:disabled="loading || !submitForm" 
							              	class="btn btn-primary"
						              	>
						              		Update Delivery Setting
						              	</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane fade show fade" id="others">	
					<div class="row">

						<div  
							v-show="!loading" 
							class="col-sm-12"
						>
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form 
							      	class="form-horizontal" 
							      	v-on:submit.prevent="updateOtherSetting"
						      	>	
						      		<input 
							      		type="hidden" 
							      		name="_token" 
							      		:value="csrf"
						      		>
						            
					              	<div class="card-body box-profile">

					              		<div class="row d-flex align-items-center text-center mb-4">
						              		<label for="inputMobile3" class="col-sm-4 col-form-label text-right">
						              			App Logo
						              		</label>
						              		<div class="col-sm-4">
						                  		<img 
							                  		class="profile-user-img img-fluid" 
							                  		:src="applicationSettings.logo || 'uploads/application/logo.png'" 
							                  		alt="Application logo"
						                  		>
						                	</div>
							                <div class="col-sm-4">
							                  	<div class="input-group">
								                    <div class="custom-file">
								                        <input 
								                        	type="file" 
								                        	class="custom-file-input" 
								                        	id="exampleInputFile" 
								                        	v-on:change="onLogoChange" 
								                        	accept="image/*"
								                        >
								                        <label class="custom-file-label" for="exampleInputFile">
								                        	Change Logo
								                        </label>
								                    </div>
							                    </div>
							                </div>
						            	</div>

					                	<div class="row d-flex align-items-center text-center">
						              		<label for="inputMobile3" class="col-sm-4 col-form-label text-right">
						              			Panel Favicon
						              		</label>
						              		<div class="col-sm-4">
						                  		<img 
							                  		class="profile-user-img img-fluid" 
							                  		:src="applicationSettings.favicon || 'uploads/application/favicon.png'" 
							                  		alt="Application favicon"
						                  		>
						                	</div>
							                <div class="col-sm-4">
							                  	<div class="input-group">
								                    <div class="custom-file">
								                        <input 
								                        	type="file" 
								                        	class="custom-file-input" 
								                        	id="exampleInputFile" 
								                        	v-on:change="onFaviconChange" 
								                        	accept="image/*"
								                        >
								                        <label class="custom-file-label" for="exampleInputFile">
								                        	Change Favicon
								                        </label>
								                    </div>
							                    </div>
							                </div>
						            	</div>

					              	</div>
					              	<!-- /.card-body -->
						            
						            <div class="card-footer text-center">
						            	<div class="col-sm-12">
											<span 
												class="text-danger p-0 m-0 small" 
												v-show="!submitForm"
											>
										  		Please input one file at least
										  	</span>
										</div>
						              	<button 
							              	type="submit" 
							              	:disabled="loading || !submitForm" 
							              	class="btn btn-primary"
						              	>
						              		Update Media Settings
						              	</button>
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
	        		// vat_rate : null,
	        		// official_bank : null,
	        		// official_bank_account_number : null,
	        		// official_bank_account_holder_name : null,
	        		// merchant_number : null,
	        		
	        		// official_customer_care_number : null,
	        		// official_mail_address : null,
	        		// official_contact_address : null,

	        		// delivery_charge : null,
	        		// multiple_delivery_charge_percentage : null,

	        		// favicon : null,
	        		// logo : null,
	        	},

	        	newLogo : null,
	        	newFavicon : null,

	        	loading : false,

	        	errors : {
	        		applicationSettings : {},
	        	},

	        	submitForm : true,
	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
	        }
		},
		created(){
			this.fetchSettingData();
		},
		methods : {
			fetchSettingData() {
				this.loading = true;
				axios
					.get('/api/settings')
					.then(response => {
						this.loading = false;
						this.applicationSettings = response.data || {};
					});
			},
			updatePaymentSetting() {

				if (!this.applicationSettings.vat_rate || !this.applicationSettings.official_bank || !this.applicationSettings.official_bank_account_number || !this.applicationSettings.official_bank_account_holder_name || !this.applicationSettings.merchant_number) {

					this.submitForm = false;
					return;
				}

				axios
					.post('/payment-settings', this.applicationSettings)
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
			updateContactSetting() {

				if (!this.applicationSettings.official_customer_care_number || !this.applicationSettings.official_mail_address || !this.applicationSettings.official_contact_address) {

					this.submitForm = false;
					return false;
				}

				axios
					.post('/contact-settings', this.applicationSettings)
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
			updateDeliverySetting() {

				if (!this.applicationSettings.delivery_charge || !this.applicationSettings.multiple_delivery_charge_percentage) {

					this.submitForm = false;
					return false;
				}

				axios
					.post('/delivery-settings', this.applicationSettings)
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
			updateOtherSetting() {

				if (!this.newLogo && !this.newFavicon) {
					this.submitForm = false;
					return;
				}

				this.applicationSettings.logo = this.newLogo;
				this.applicationSettings.favicon = this.newFavicon;

				axios
					.post('/other-settings', this.applicationSettings)
					.then(response => {
						if (response.status == 200) {
							
							this.newLogo = this.newFavicon = null;
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
			onLogoChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.createImage(files[0], 'logo');
		      	}

		      	evnt.target.value = '';

		      	return;
			},
			onFaviconChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.createImage(files[0], 'favicon');
		      	}

		      	evnt.target.value = '';

		      	return;
			},
			createImage(file, filename) {
                let reader = new FileReader();

                if (filename=='favicon') {
	                reader.onload = (evnt) => {
	                    this.newFavicon = this.applicationSettings.favicon = evnt.target.result;
	                };
                }else{
                	reader.onload = (evnt) => {
	                    this.newLogo = this.applicationSettings.logo = evnt.target.result;
	                };
                }

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
							this.errors.applicationSettings.vat_rate = 'Value should be between 1 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'vat_rate');
						}

						break;

					case 'official_bank' :

						if (!this.applicationSettings.official_bank) {
							this.errors.applicationSettings.official_bank = 'Bank name is required';
						}
						else if (!this.applicationSettings.official_bank.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.applicationSettings.official_bank = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_bank');
						}

						break;

					case 'official_bank_account_number' :

						if (!this.applicationSettings.official_bank_account_number) {
							this.errors.applicationSettings.official_bank_account_number = 'Account number is required';
						}
						else if (!this.applicationSettings.official_bank_account_number.match(/^[_A-z0-9]*((-|_|\s)*[_A-z0-9])*$/g)) {
							this.errors.applicationSettings.official_bank_account_number = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_bank_account_number');
						}

						break;

					case 'official_bank_account_holder_name' :

						if (!this.applicationSettings.official_bank_account_holder_name) {
							this.errors.applicationSettings.official_bank_account_holder_name = 'Account holder name is required';
						}
						else if (!this.applicationSettings.official_bank_account_holder_name.match(/^[_A-z0-9]*((-|_|\s)*[_A-z0-9])*$/g)) {
							this.errors.applicationSettings.official_bank_account_holder_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_bank_account_holder_name');
						}

						break;

					case 'merchant_number' :

						if (!this.applicationSettings.merchant_number) {
							this.errors.applicationSettings.merchant_number = 'Mobile is required';
						}
						else if (!this.applicationSettings.merchant_number.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.applicationSettings.merchant_number = 'Invalid mobile number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'merchant_number');
						}

						break;

					case 'official_customer_care_number' :

						if (!this.applicationSettings.official_customer_care_number) {
							this.errors.applicationSettings.official_customer_care_number = 'Customer care number is required';
						}
						else if (!this.applicationSettings.official_customer_care_number.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.applicationSettings.official_customer_care_number = 'Invalid customer care number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_customer_care_number');
						}

						break;

					case 'official_mail_address' :

						if (!this.applicationSettings.official_mail_address) {
							this.errors.applicationSettings.official_mail_address = 'Official mail is required';
						}
						else if (!this.applicationSettings.official_mail_address.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.applicationSettings.official_mail_address = 'Invalid email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_mail_address');
						}

						break;

					case 'official_contact_address' :

						if (!this.applicationSettings.official_contact_address) {
							this.errors.applicationSettings.official_contact_address = 'Official address is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'official_contact_address');
						}

						break;

					case 'delivery_charge' :

						if (!this.applicationSettings.delivery_charge) {
							this.errors.applicationSettings.delivery_charge = 'Delivery charge is required';
						}
						else if (this.applicationSettings.delivery_charge < 0 ) {
							this.errors.applicationSettings.delivery_charge = 'Value should be positive number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'delivery_charge');
						}

						break;

					case 'multiple_delivery_charge_percentage' :

						if (!this.applicationSettings.multiple_delivery_charge_percentage) {
							this.errors.applicationSettings.multiple_delivery_charge_percentage = 'Percentage is required';
						}
						else if (this.applicationSettings.multiple_delivery_charge_percentage < 0 ) {
							this.errors.applicationSettings.multiple_delivery_charge_percentage = 'Value should be positive number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.applicationSettings, 'multiple_delivery_charge_percentage');
						}

						break;
				}
	 
			},
		}
  	}

</script>