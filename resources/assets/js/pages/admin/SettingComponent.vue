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
			<ul class="nav nav-pills nav-tabs nav-justified mb-2" v-show="!loading" >
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#application">
						App
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#service">
						Service
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#payment">
						Payment
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#contact">
						Contact
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#system">
						System
					</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane fade show fade active" id="application">	
					<div class="row">
						<div  
							v-show="!loading" 
							class="col-sm-12"
						>
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form 
							      	class="form-horizontal" 
							      	v-on:submit.prevent="updateAppSetting"
						      	>	
						      		<input 
							      		type="hidden" 
							      		name="_token" 
							      		:value="csrf"
						      		>
						            
					              	<div class="card-body box-profile">
					              		<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			App Name
						              		</label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.app_name" 
							                  		placeholder="App Name"  
							                  		:class="!errors.app_name  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('app_name')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.app_name }}
										  		</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			Searching Radius
						              		</label>
							                <div class="col-sm-9">
							                  	<div class="input-group mb-0">
													<input 
														type="number" 
														class="form-control" 
														v-model="applicationSettings.searching_radius" 
														min="1" 
														max="100" 
														step=".1" 
														placeholder="Radius for Restaurant & Rider" 
														:class="! errors.searching_radius  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('searching_radius')"
								                	>
													<div class="input-group-append">
														<span class="input-group-text">
															Meter
														</span>
													</div>
												</div>

							                  	<div 
							                  		class="invalid-feedback"
							                  		style="display: block;"
							                  		v-show="errors.searching_radius" 
							                  	>
										        	{{ errors.searching_radius }}
										  		</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label for="inputLastName3" class="col-sm-3 col-form-label text-right">
						              		 	Delivery Fee Percentage (for multiple)
						              		</label>
							                <div class="col-sm-9">
							                	<div class="input-group mb-0">
													<input 
														type="number" 
														class="form-control" 
														v-model="applicationSettings.multiple_delivery_charge_percentage" 
														min="1" 
														max="100" 
														step=".1" 
														placeholder="For Multiple Delivery" 
														:class="!errors.multiple_delivery_charge_percentage  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('multiple_delivery_charge_percentage')"
								                	>
													<div class="input-group-append">
														<span class="input-group-text">
															%
														</span>
													</div>
								                	<div class="invalid-feedback">
											        	{{ errors.multiple_delivery_charge_percentage }}
											  		</div>
												</div>
							                </div>
					                	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			Welcome Greetings
						              		</label>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3"></label>

						              		<div 
						              			class="col-sm-9" 
						              			v-if="applicationSettings.welcome_greetings.length==errors.welcome_greetings.length"
						              		>
						              			<div class="card" v-for="(welcomeGreeting, welcomeGreetingIndex) in applicationSettings.welcome_greetings">
						              				<div class="card-header">
						              					Greeting # {{ welcomeGreetingIndex + 1 }}
						              				</div>

								              		<div class="card-body">
								              			<div class="form-group row">
										              		<label class="col-sm-3 col-form-label text-right">
										              			Title
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
											                  		type="text" 
											                  		class="form-control" 
											                  		v-model="welcomeGreeting.title" 
											                  		placeholder="Greeting Title" 
											                  		:class="!errors.welcome_greetings[welcomeGreetingIndex].title  ? 'is-valid' : 'is-invalid'"
																	@keyup="validateFormInput('welcome_title')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.welcome_greetings[welcomeGreetingIndex].title }}
														  		</div>
											                </div>
										              	</div>

										              	<div class="form-group row">
										              		<label class="col-sm-3 col-form-label text-right">
										              			Message
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
											                  		type="text" 
											                  		class="form-control" 
											                  		v-model="welcomeGreeting.paragraph" 
											                  		placeholder="Welcome Message" 
											                  		:class="!errors.welcome_greetings[welcomeGreetingIndex].paragraph  ? 'is-valid' : 'is-invalid'"
																	@keyup="validateFormInput('welcome_paragraph')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.welcome_greetings[welcomeGreetingIndex].paragraph }}
														  		</div>
											                </div>
										              	</div>

										              	<div class="row d-flex align-items-center text-center mb-4">
										              		<label for="inputMobile3" class="col-sm-3 col-form-label text-right">
										              			Preview
										              		</label>
										              		<div class="col-sm-4">
										                  		<img 
											                  		class="profile-user-img img-fluid" 
											                  		:src="welcomeGreeting.preview || 'uploads/application/welcome_preview.jpg'" 
											                  		alt="Welcome Preview"
										                  		>
										                  		<div 
										                  			class="invalid-feedback"
										                  			style="display: block;" 
										                  			v-show="errors.welcome_greetings[welcomeGreetingIndex].preview" 
										                  		>
														        	{{ errors.welcome_greetings[welcomeGreetingIndex].preview }}
														  		</div>
										                	</div>
											                <div class="col-sm-5">
											                  	<div class="input-group">
												                    <div class="custom-file">
												                        <input 
												                        	type="file" 
												                        	class="custom-file-input" 
												                        	id="exampleInputFile" 
												                        	v-on:change="onStartingPreviewChange($event, welcomeGreetingIndex)" 
												                        	accept="image/*"
												                        >
												                        <label class="custom-file-label" for="exampleInputFile">
												                        	Change Preview
												                        </label>
												                    </div>
											                    </div>
											                </div>
										            	</div>

										            	<div class="form-group row">
										              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">
										              			Status
										              		</label>
											                <div class="col-sm-9">
											                	<toggle-button 
						                                    		value ="true" 
						                                    		:sync="true" 
						                                    		v-model="welcomeGreeting.status" 
						                                    		:width="140"  
						                                    		:height="30" 
						                                    		:font-size="15" 
						                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
						                                    		:labels="{checked: 'Published', unchecked: 'UnPublished' }"
						                                    	/>
											                </div>
										              	</div>
								              		</div>
								              	</div>

								              	<div class="form-group row">
								              		<i aria-hidden="true" class="fas fa-minus-circle fa-2x  rounded-circle bg-danger ml-auto mr-1" @click="removeWelcomeGreeting()" :disabled="applicationSettings.welcome_greetings.length < 2"></i>

								              		<i aria-hidden="true" class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" @click="addWelcomeGreeting()"></i>
								              	</div>
						              		</div>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			Order Accomplishment Greeting
						              		</label>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3"></label>

						              		<div class="col-sm-9" v-if="applicationSettings.thanks_greeting">
						              			<div class="card">
								              		<div class="card-body">
								              			<div class="form-group row">
										              		<label class="col-sm-3 col-form-label text-right">
										              			Accomplishment Title
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
											                  		type="text" 
											                  		class="form-control" 
											                  		v-model="applicationSettings.thanks_greeting.title" 
											                  		placeholder="Accomplishment Title" 
											                  		:class="!errors.thanks_greeting.title  ? 'is-valid' : 'is-invalid'"
																	@keyup="validateFormInput('thanks_title')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.thanks_greeting.title }}
														  		</div>
											                </div>
										              	</div>

										              	<div class="form-group row">
										              		<label class="col-sm-3 col-form-label text-right">
										              			Accomplishment Message
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
											                  		type="text" 
											                  		class="form-control" 
											                  		v-model="applicationSettings.thanks_greeting.paragraph" 
											                  		placeholder="Accomplishment Message" 
											                  		:class="!errors.thanks_greeting.paragraph  ? 'is-valid' : 'is-invalid'"
																	@keyup="validateFormInput('thanks_paragraph')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.thanks_greeting.paragraph }}
														  		</div>
											                </div>
										              	</div>

										              	<div class="row d-flex align-items-center text-center mb-4">
										              		<label for="inputMobile3" class="col-sm-3 col-form-label text-right">
										              			Accomplishment Preview
										              		</label>
										              		<div class="col-sm-4">
										                  		<img 
											                  		class="profile-user-img img-fluid" 
											                  		:src="applicationSettings.thanks_greeting.preview || 'uploads/application/accomplishment_logo.jpg'" 
											                  		alt="Order Accomplishment Preview"
										                  		>

										                  		<div 
										                  			class="invalid-feedback"
										                  			style="display: block;" 
										                  			v-show="errors.thanks_greeting.preview" 
										                  		>
														        	{{ errors.thanks_greeting.preview }}
														  		</div>
										                	</div>
											                <div class="col-sm-5">
											                  	<div class="input-group">
												                    <div class="custom-file">
												                        <input 
												                        	type="file" 
												                        	class="custom-file-input" 
												                        	id="exampleInputFile" 
												                        	v-on:change="onAccomplishmentPreviewChange" 
												                        	accept="image/*"
												                        >
												                        <label class="custom-file-label" for="exampleInputFile">
												                        	Change Preview
												                        </label>
												                    </div>
											                    </div>
											                </div>
										            	</div>

										            	<!-- 
										            	<div class="form-group row">
										              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">
										              			Status
										              		</label>
											                <div class="col-sm-9">
											                	<toggle-button 
						                                    		value ="true" 
						                                    		:sync="true" 
						                                    		v-model="applicationSettings.thanks_greeting.status" 
						                                    		:width="140"  
						                                    		:height="30" 
						                                    		:font-size="15" 
						                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
						                                    		:labels="{checked: 'Published', unchecked: 'UnPublished' }"
						                                    	/>
											                </div>
										              	</div> 
										              	-->
								              		</div>
								              	</div>
						              		</div>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			Promotional Sliders
						              		</label>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3"></label>

						              		<div class="col-sm-9" v-if="applicationSettings.promotional_sliders.length==errors.promotional_sliders.length">
						              			<div class="card" v-for="(promotionalSlider, promotionalSliderIndex) in applicationSettings.promotional_sliders">
								              		<div class="card-body">
										              	<div class="row d-flex align-items-center text-center mb-4">
										              		<label for="inputMobile3" class="col-sm-3 col-form-label text-right">
										              			Promotional Preview
										              		</label>
										              		<div class="col-sm-4">
										                  		<img 
											                  		class="profile-user-img img-fluid" 
											                  		:src="promotionalSlider.preview || 'uploads/application/promotional_preview.jpg'" 
											                  		alt="Promotional Preview"
										                  		>

										                  		<div 
										                  			class="invalid-feedback"
										                  			style="display: block;" 
										                  			v-show="errors.promotional_sliders[promotionalSliderIndex]" 	
										                  		>
														        	{{ errors.promotional_sliders[promotionalSliderIndex] }}
														  		</div>
										                	</div>
											                <div class="col-sm-5">
											                  	<div class="input-group">
												                    <div class="custom-file">
												                        <input 
												                        	type="file" 
												                        	class="custom-file-input" 
												                        	id="exampleInputFile" 
												                        	v-on:change="onPromotionalPreviewChange($event,promotionalSliderIndex)" 
												                        	accept="image/*"
												                        >
												                        <label class="custom-file-label" for="exampleInputFile">
												                        	Change Preview
												                        </label>
												                    </div>
											                    </div>
											                </div>
										            	</div>

										            	<div class="form-group row">
										              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">
										              			Status
										              		</label>
											                <div class="col-sm-9">
											                	<toggle-button 
						                                    		value ="true" 
						                                    		:sync="true" 
						                                    		v-model="promotionalSlider.status" 
						                                    		:width="140"  
						                                    		:height="30" 
						                                    		:font-size="15" 
						                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
						                                    		:labels="{checked: 'Published', unchecked: 'UnPublished' }"
						                                    	/>
											                </div>
										              	</div>
								              		</div>
								              	</div>

								              	<div class="form-group row">
								              		<i aria-hidden="true" class="fas fa-minus-circle fa-2x  rounded-circle bg-danger ml-auto mr-1" @click="removePromotionalPreview()" :disabled="applicationSettings.promotional_sliders.length < 2"></i>

								              		<i aria-hidden="true" class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" @click="addPromotionalPreview()"></i>
								              	</div>
						              		</div>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			Search Preferences
						              		</label>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right"></label>

						              		<div class="col-sm-9">
						              			<div class="card">
								              		<div class="card-body">
								              			<div 
								              				class="form-group row"
								              				v-if="applicationSettings.hasOwnProperty('search_preferences') && applicationSettings.search_preferences.hasOwnProperty('cuisines') && applicationSettings.search_preferences.cuisines.length"	
								              			>
										              		<label class="col-sm-3 text-right">
										              			<span class="col-form-label">
										              				Cuisines 
										              			</span>
																<router-link :to="{ name: 'cuisines' }">
																	<i class="fa fa-link" aria-hidden="true"></i>
																</router-link>
										              		</label>
											                <div class="col-sm-9">
																<ul id="example-1">
																	<li 
																		v-for="cuisine in applicationSettings.search_preferences.cuisines" 
																		:key="'cuisine-search-preferences-' + cuisine.id"
																	>
																		{{ cuisine.name | capitalize }}
																	</li>
																</ul>
											                </div>
										              	</div>

										              	<div 
										              		class="form-group row"
										              		v-if="applicationSettings.hasOwnProperty('search_preferences') && applicationSettings.search_preferences.hasOwnProperty('meals') && applicationSettings.search_preferences.meals.length"
										              	>
										              		<label class="col-sm-3 text-right">
										              			<span class="col-form-label">
										              				Meals 
										              			</span>
																<router-link :to="{ name: 'meals' }">
																	<i class="fa fa-link" aria-hidden="true"></i>
																</router-link>
										              		</label>

											                <div class="col-sm-9">
																<ul id="example-1">
																	<li 
																		v-for="meal in applicationSettings.search_preferences.meals" 
																		:key="'meal-search-preferences-' + meal.id"
																	>
																		{{ meal.name | capitalize }}
																	</li>
																</ul>
											                </div>
										              	</div>

										              	<div 
										              		class="form-group row"
										              		v-if="applicationSettings.hasOwnProperty('search_preferences') && applicationSettings.search_preferences.hasOwnProperty('product_categories') && applicationSettings.search_preferences.product_categories.length"
										              	>
										              		<label class="col-sm-3 text-right">
										              			<span class="col-form-label">
										              				Product Category 
										              			</span>
																<router-link :to="{ name: 'product-categories' }">
																	<i class="fa fa-link" aria-hidden="true"></i>
																</router-link>
										              		</label>

											                <div class="col-sm-9">
																<ul id="example-1">
																	<li 
																		v-for="productCategory in applicationSettings.search_preferences.product_categories" 
																		:key="'product-category-search-preferences-' + productCategory.id"
																	>
																		{{ productCategory.name | capitalize }}
																	</li>
																</ul>
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
										  		Please input one file at least
										  	</span>
										</div>
						              	<button 
							              	type="submit" 
							              	:disabled="loading || ! submitForm || formSubmitted" 
							              	class="btn btn-primary"
						              	>
						              		Update Application Settings
						              	</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane fade show" id="service">	
					<div class="row">
						<div  
							v-show="!loading" 
							class="col-sm-12"
						>
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form class="form-horizontal" v-on:submit.prevent="updateServiceSetting">
						      		
						      		<input 
							      		type="hidden" 
							      		name="_token" 
							      		:value="csrf"
						      		>
						            
						            <div class="card-body box-profile">
						            	<div class="form-group row">
						            		<label class="col-sm-3 col-form-label text-right">
						              			Services
						              		</label>
						            	</div>
						            	<div class="form-group row">
						              		<label class="col-sm-3"></label>

						              		<div class="col-sm-9" v-if="applicationSettings.services.length==errors.services.length">
						              			<div class="card" v-for="(service, serviceIndex) in applicationSettings.services">
								              		<div class="card-body">
								              			<div class="form-group row">
										              		<label class="col-sm-3 col-form-label text-right">
										              			Name
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
											                  		type="text" 
											                  		class="form-control" 
											                  		v-model="service.name" 
											                  		placeholder="Service Name"  
											                  		:class="! errors.services[serviceIndex].name ? 'is-valid' : 'is-invalid'"
																	@keyup="validateFormInput('service_name')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.services[serviceIndex].name }}
														  		</div>
											                </div>
										              	</div>

										              	<div class="form-group row">
										              		<label class="col-sm-3 col-form-label text-right">
										              			Code
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
											                  		type="text" 
											                  		class="form-control" 
											                  		v-model="service.code" 
											                  		placeholder="Service Code" 
											                  		disabled="true" 
											                  		:class="! errors.services[serviceIndex].code ? 'is-valid' : 'is-invalid'"
											                  		@keyup="validateFormInput('service_code')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.services[serviceIndex].code }}
														  		</div>
											                </div>
										              	</div>

										              	<div class="row d-flex align-items-center text-center mb-4">
										              		<label for="inputMobile3" class="col-sm-3 col-form-label text-right">
										              			Logo
										              		</label>
										              		<div class="col-sm-4">
										                  		<img 
											                  		class="profile-user-img img-fluid" 
											                  		:src="service.logo" 
											                  		alt="Service Logo"
										                  		>
										                  		<div 
										                  			class="invalid-feedback" 
										                  			style="display: block;" 
										                  			v-show="errors.services[serviceIndex].logo" 
										                  		>
														        	{{ errors.services[serviceIndex].logo }}
														  		</div>
										                	</div>
											                <div class="col-sm-5">
											                  	<div class="input-group">
												                    <div class="custom-file">
												                        <input 
												                        	type="file" 
												                        	class="custom-file-input" 
												                        	id="exampleInputFile" 
												                        	v-on:change="onServiceLogoChange($event, serviceIndex)" 
												                        	accept="image/*"
												                        >
												                        <label class="custom-file-label" for="exampleInputFile">
												                        	Change Logo
												                        </label>
												                    </div>
											                    </div>
											                </div>
										            	</div>

										            	<div class="form-group row">
										              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">
										              			Status
										              		</label>
											                <div class="col-sm-9">
											                	<toggle-button 
						                                    		value ="true" 
						                                    		:sync="true" 
						                                    		v-model="service.status" 
						                                    		:width="140"  
						                                    		:height="30" 
						                                    		:font-size="15" 
						                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
						                                    		:labels="{checked: 'Available', unchecked: 'Not-Available' }"
						                                    	/>
											                </div>
										              	</div>
								              		</div>
								              	</div>

								              	<!-- 
								              	<div class="form-group row">
								              		<i 
								              			aria-hidden="true" 
								              			class="fas fa-minus-circle fa-2x  rounded-circle bg-danger ml-auto mr-1" 
								              			@click="removeService" 	
									              		:disabled="applicationSettings.services.length <= 4"
									              	>
									              			
									              	</i>

								              		<i 
									              		aria-hidden="true" 
									              		class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" 
									              		@click="addMoreService()"
								              		>	
								              		</i>
								              	</div> 
								              	-->
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
							              	:disabled="loading || ! submitForm || formSubmitted" 
							              	class="btn btn-primary"
						              	>
						              		Update Service Settings
						              	</button>
						            </div>
						        	<!-- /.card-footer -->
						      	</form>
						    </div>
						</div>
					</div>
				</div>

				<div class="tab-pane fade show" id="payment">	
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
						              		<label class="col-sm-3 col-form-label text-right">
						              			Currency
						              		</label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.official_currency" 
							                  		placeholder="Currency Name" 
							                  		:class="!errors.official_currency ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('official_currency')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.official_currency }}
										  		</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">
						              			Vat Rate
						              		</label>
							                <div class="col-sm-9">
							                	<div class="input-group mb-0">
													<input 
														type="number" 
														class="form-control" 
														v-model="applicationSettings.vat_rate" 
														min="1" 
														max="100" 
														step=".1" 
														placeholder="Vat Rate" 
														:class="!errors.vat_rate  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('vat_rate')"
								                	>
													<div class="input-group-append">
														<span class="input-group-text">
															%
														</span>
													</div>
								                	<div class="invalid-feedback">
											        	{{ errors.vat_rate }}
											  		</div>
												</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			Official Bank
						              		</label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.official_bank" 
							                  		placeholder="Bank Name" 
							                  		:class="!errors.official_bank  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('official_bank')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.official_bank }}
										  		</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">
						              			Account Number  
						              		</label>
							                <div class="col-sm-9">
							                  	<input  
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.official_bank_account_number" 
							                  		placeholder="Bank Account Number" 
							                  		:class="!errors.official_bank_account_number  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('official_bank_account_number')"
							                  	>

							                  	<div class="invalid-feedback">
										        	{{ errors.official_bank_account_number }}
										  		</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">
						              			Account Name 
						              		</label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="text" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.official_bank_account_holder_name" 
							                  		placeholder="Account Holder Number" 
							                  		:class="!errors.official_bank_account_holder_name  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('official_bank_account_holder_name')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.official_bank_account_holder_name }}
										  		</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label for="inputConfirmnumber3" class="col-sm-3 col-form-label text-right">
						              			MFS Number 
						              		</label>
							                <div class="col-sm-9">
							                  	<input 
							                  		type="tel" 
							                  		class="form-control" 
							                  		v-model="applicationSettings.merchant_number" 
							                  		placeholder="Common Merchant Number" 
							                  		:class="!errors.merchant_number  ? 'is-valid' : 'is-invalid'"
													@keyup="validateFormInput('merchant_number')"
							                  	>
							                  	<div class="invalid-feedback">
										        	{{ errors.merchant_number }}
										  		</div>
							                </div>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3 col-form-label text-right">
						              			Payment Methods
						              		</label>
						              	</div>

						              	<div class="form-group row">
						              		<label class="col-sm-3"></label>

						              		<div 
							              		class="col-sm-9" 
							              		v-if="applicationSettings.payment_methods.length==errors.payment_methods.length"
						              		>
						              			<div class="card" v-for="(paymentMethod, paymentMethodIndex) in applicationSettings.payment_methods">
								              		<div class="card-body">
								              			<div class="form-group row">
										              		<label class="col-sm-3 col-form-label text-right">
										              			Payment Name
										              		</label>
											                <div class="col-sm-9">
											                  	<input 
											                  		type="text" 
											                  		class="form-control" 
											                  		v-model="paymentMethod.name" 
											                  		placeholder="Payment Name" 
											                  		:class="! errors.payment_methods[paymentMethodIndex].name ? 'is-valid' : 'is-invalid'"
																	@keyup="validateFormInput('payment_name')"
											                  	>
											                  	<div class="invalid-feedback">
														        	{{ errors.payment_methods[paymentMethodIndex].name }}
														  		</div>
											                </div>
										              	</div>

										              	<div class="row d-flex align-items-center text-center mb-4">
										              		<label for="inputMobile3" class="col-sm-3 col-form-label text-right">
										              			Payment Logo
										              		</label>
										              		<div class="col-sm-4">
										                  		<img 
											                  		class="profile-user-img img-fluid" 
											                  		:src="paymentMethod.logo || 'uploads/application/logo.png'" 
											                  		alt="Payment Logo"
										                  		>
										                  		<div 
										                  			class="invalid-feedback"
										                  			style="display: block;" 
										                  			v-show="errors.payment_methods[paymentMethodIndex].logo" 
										                  		>
														        	{{ errors.payment_methods[paymentMethodIndex].logo }}
														  		</div>
										                	</div>
											                <div class="col-sm-5">
											                  	<div class="input-group">
												                    <div class="custom-file">
												                        <input 
												                        	type="file" 
												                        	class="custom-file-input" 
												                        	id="exampleInputFile" 
												                        	v-on:change="onPaymentLogoChange($event, paymentMethodIndex)" 
												                        	accept="image/*"
												                        >
												                        <label class="custom-file-label" for="exampleInputFile">
												                        	Change Logo
												                        </label>
												                    </div>
											                    </div>
											                </div>
										            	</div>

										            	<div class="form-group row">
										              		<label for="inputnumber3" class="col-sm-3 col-form-label text-right">
										              			Status
										              		</label>
											                <div class="col-sm-9">
											                	<toggle-button 
						                                    		value ="true" 
						                                    		:sync="true" 
						                                    		v-model="paymentMethod.status" 
						                                    		:width="140"  
						                                    		:height="30" 
						                                    		:font-size="15" 
						                                    		:color="{checked: 'green', unchecked: '#17a2b8'}" 
						                                    		:labels="{checked: 'Available', unchecked: 'Not-Available' }"
						                                    	/>
											                </div>
										              	</div>
								              		</div>
								              	</div>

								              	<div class="form-group row">
								              		<i 
								              			aria-hidden="true" 
								              			class="fas fa-minus-circle fa-2x  rounded-circle bg-danger ml-auto mr-1" 
								              			@click="removePaymentMethod" 	
									              		:disabled="applicationSettings.payment_methods.length <= 1"
									              	>
									              			
									              	</i>

								              		<i 
									              		aria-hidden="true" 
									              		class="fas fa-plus-circle fa-2x  rounded-circle bg-primary mr-1" 
									              		@click="addMorePaymentMethod"
								              		>	
								              		</i>
								              	</div>
						              		</div>
						              	</div>
						            </div>

						            <!-- /.card-body -->
						            <div class="card-footer text-center">
						            	<div class="col-sm-12">
											<span 
												class="text-danger p-0 m-0 small" 
												v-show="! submitForm"
											>
										  		Please input all required fields
										  	</span>
										</div>
						              	<button 
							              	type="submit" 
							              	:disabled="loading || ! submitForm || formSubmitted" 
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
									                  	:class="!errors.official_customer_care_number  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('official_customer_care_number')"
									                  >
									                  <div class="invalid-feedback">
											        	{{ errors.official_customer_care_number }}
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
									                  	:class="!errors.official_mail_address  ? 'is-valid' : 'is-invalid'"
														@keyup="validateFormInput('official_mail_address')"
									                  >
									                  	<div class="invalid-feedback">
												        	{{ errors.official_mail_address }}
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
					                              	:class="!errors.official_contact_address  ? 'is-valid' : 'is-invalid'"
					                              	:editor="editor" 
					                              	v-model="applicationSettings.official_contact_address" 
					                              	placeholder="Contact Address" 
					                              	@blur="validateFormInput('official_contact_address')"
					                            >
				                              	</ckeditor>
				                              	<div class="invalid-feedback">
										        	{{ errors.official_contact_address }}
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
							              	:disabled="loading || ! submitForm || formSubmitted" 
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

				<div class="tab-pane fade show fade" id="system">	
					<div class="row">
						<div  
							v-show="!loading" 
							class="col-sm-12"
						>
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form 
							      	class="form-horizontal" 
							      	v-on:submit.prevent="updateSystemSetting"
						      	>	
						      		<input 
							      		type="hidden" 
							      		name="_token" 
							      		:value="csrf"
						      		>
						            
					              	<div class="card-body box-profile">

					              		<div class="row d-flex align-items-center text-center mb-4">
						              		<label for="inputMobile3" class="col-sm-3 col-form-label text-right">
						              			App Logo
						              		</label>
						              		<div class="col-sm-4">
						                  		<img 
							                  		class="profile-user-img img-fluid" 
							                  		:src="applicationSettings.logo || 'uploads/application/logo.png'" 
							                  		alt="Application logo"
						                  		>
						                  		<div 
						                  			class="invalid-feedback"
						                  			style="display: block;" 
						                  			v-show="errors.application_logo" 	
						                  		>
										        	{{ errors.application_logo }}
										  		</div>
						                	</div>
							                <div class="col-sm-5">
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
						              		<label for="inputMobile3" class="col-sm-3 col-form-label text-right">
						              			Panel Favicon
						              		</label>
						              		<div class="col-sm-4">
						                  		<img 
							                  		class="profile-user-img img-fluid" 
							                  		:src="applicationSettings.favicon || 'uploads/application/favicon.png'" 
							                  		alt="Panel Favicon"
						                  		>
						                  		<div 
						                  			class="invalid-feedback"
						                  			style="display: block;" 
						                  			v-show="errors.panel_favicon" 	
						                  		>
										        	{{ errors.panel_favicon }}
										  		</div>
						                	</div>
							                <div class="col-sm-5">
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
												v-show="! submitForm"
											>
										  		Please input one file at least
										  	</span>
										</div>
						              	<button 
							              	type="submit" 
							              	:disabled="loading || ! submitForm || formSubmitted" 
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
	import { ToggleButton } from 'vue-js-toggle-button';
	import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

	export default {

	    components: { 
	    	ToggleButton : ToggleButton, 
			ckeditor: CKEditor.component,
		},

	    data() {
	        return {

	        	editor: ClassicEditor,

	        	applicationSettings : {
	        		// app_name : null,
	        		// vat_rate : null,
	        		// official_bank : null,
	        		// official_bank_account_number : null,
	        		// official_bank_account_holder_name : null,
	        		// merchant_number : null,
	        		
	        		// official_customer_care_number : null,
	        		// official_mail_address : null,
	        		// official_contact_address : null,

	        		// favicon : null,
	        		// logo : null,
		        	
		        	thanks_greeting : {},

		        	promotional_sliders : [
		        		// {}
		        	],

		        	welcome_greetings : [
		        		{
		        			title : null,
		        			preview : null,
		        			paragraph : null,
		        		}
		        	],

		        	payment_methods : [
		        		// {}
		        	],

		        	services : [
		        		// {}
		        	],

		        	// search_preferences : [{}]

	        	},


	        	newLogo : null,
	        	newFavicon : null,

	        	loading : false,

	        	errors : {
	        		
	        		thanks_greeting : {},

		        	promotional_sliders : [
		        		// {}
		        	],

	        		welcome_greetings : [
		        		/*
		        		{
		        			title : null,
		        			preview : null,
		        			paragraph : null,
		        		}
		        		*/
		        	],

		        	payment_methods : [
		        		// {}
		        	],

		        	services : [
		        		// {}
		        	],

		        	// search_preferences : [{}]
	        		
	        	},

	        	submitForm : true,
	        	formSubmitted : false,

	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')

	        }
		},
		created(){
			this.fetchSettingData();
		},
		methods : {
			fetchSettingData() {
				this.loading = true;
				this.formSubmitted = true;

				axios
					.get('/api/settings')
					.then(response => {
						this.applicationSettings = response.data.data || this.applicationSettings;
						this.updateErrorObject();
					})
					.catch(error => {

						if (error.response.status == 422) {

							for (var x in error.response.data.errors) {
								toastr.warning(error.response.data.errors[x], "Warning");
							}
				      	}

					})
					.finally(()=> {
						this.loading = false;
					   	this.formSubmitted = false;
					});
			},
			updateErrorObject(){

				if (this.applicationSettings.promotional_sliders.length) {
					this.applicationSettings.promotional_sliders.forEach(
						(promotionSlider, promotionSliderIndex) => {
							// this.errors.promotional_sliders.push({});
							this.errors.promotional_sliders[promotionSliderIndex] = null;
						}
					);
				}

				if (this.applicationSettings.welcome_greetings.length) {
					this.applicationSettings.welcome_greetings.forEach(
						(welcomeGreeting) => {
							this.errors.welcome_greetings.push({});
						}
					);
				}

				if (this.applicationSettings.payment_methods.length) {
					this.applicationSettings.payment_methods.forEach(
						(paymentMethod) => {
							this.errors.payment_methods.push({});
						}
					);
				}

				if (this.applicationSettings.services.length) {
					this.applicationSettings.services.forEach(
						(service) => {
							this.errors.services.push({});
						}
					);
				}

			},
			updateAppSetting() {

				this.validateFormInput('app_name');
				this.validateFormInput('searching_radius');
				this.validateFormInput('multiple_delivery_charge_percentage');
				
				this.validateFormInput('welcome_title');
				this.validateFormInput('welcome_paragraph');
				this.validateFormInput('welcome_preview');

				this.validateFormInput('thanks_title');
				this.validateFormInput('thanks_paragraph');
				this.validateFormInput('thanks_preview');

				this.validateFormInput('promotional_sliders');

				if (this.errors.app_name || this.errors.searching_radius || this.errors.multiple_delivery_charge_percentage || this.errorInWelcomegreetings() || this.errorInThanksGreeting() || this.errorInPromotionalSliders()) {

					this.submitForm = false;
					return false;
				}

				this.formSubmitted = true;

				axios
					.post('/application-settings', this.applicationSettings)
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

					})
					.finally(()=> {
					   this.formSubmitted = false;
					});
			},
			updateServiceSetting() {
				
				this.validateFormInput('service_name');
				this.validateFormInput('service_code');
				this.validateFormInput('service_logo');

				if (this.errorInServices()) {

					this.submitForm = false;
					return false;
				}

				this.formSubmitted = true;

				axios
					.post('/service-settings', this.applicationSettings)
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

					})
					.finally(()=> {
					   this.formSubmitted = false;
					});
			},
			updatePaymentSetting() {

				this.validateFormInput('payment_name');
				this.validateFormInput('payment_logo');

				if (!this.applicationSettings.official_currency || !this.applicationSettings.vat_rate || !this.applicationSettings.official_bank || !this.applicationSettings.official_bank_account_number || !this.applicationSettings.official_bank_account_holder_name || !this.applicationSettings.merchant_number) {

					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

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

					})
					.finally(()=> {
					   this.formSubmitted = false;
					});
			},
			updateContactSetting() {

				if (!this.applicationSettings.official_customer_care_number || !this.applicationSettings.official_mail_address || !this.applicationSettings.official_contact_address) {

					this.submitForm = false;
					return false;
				}

				this.formSubmitted = true;

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

					})
					.finally(()=> {
					   this.formSubmitted = false;
					});
			},
			updateSystemSetting() {

				if (! this.newLogo && ! this.newFavicon) {
					this.submitForm = false;
					return;
				}

				this.formSubmitted = true;

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

					})
					.finally(()=> {
					   this.formSubmitted = false;
					});
			},
            addWelcomeGreeting(){
            	
            	this.validateFormInput('welcome_title');
            	this.validateFormInput('welcome_preview');
            	this.validateFormInput('welcome_paragraph');

            	if (! this.errorInWelcomegreetings()) {

	            	this.errors.welcome_greetings.push({});

	            	this.$set(this.applicationSettings.welcome_greetings, this.applicationSettings.welcome_greetings.length, 

	            		{
		        			title : null,
		        			preview : null,
		        			paragraph : null,
		        		}

	            	);

            	}

            },
            removeWelcomeGreeting(){

            	if (this.applicationSettings.welcome_greetings.length > 1 && this.errors.welcome_greetings.length > 1 && this.applicationSettings.welcome_greetings.length==this.errors.welcome_greetings.length) {

	            	this.errors.welcome_greetings.pop();
	            	this.applicationSettings.welcome_greetings.pop();

            	}

            },
            addPromotionalPreview(){

            	this.validateFormInput('promotional_sliders');

            	if (! this.errorInPromotionalSliders()) {

	            	this.errors.promotional_sliders.push(null);

	            	this.$set(this.applicationSettings.promotional_sliders, this.applicationSettings.promotional_sliders.length, 

	            		{
		        			preview : '',
	            			status : false,
		        		}

	            	);

            	}

            },
            removePromotionalPreview(){

            	if (this.applicationSettings.promotional_sliders.length > 1 && this.errors.promotional_sliders.length > 1 && this.applicationSettings.promotional_sliders.length==this.errors.promotional_sliders.length) {

	            	this.errors.promotional_sliders.pop();
	            	this.applicationSettings.promotional_sliders.pop();

            	}

            },
            /*
            addMoreService(){

            	this.validateFormInput('service_name');
            	this.validateFormInput('service_code');
            	this.validateFormInput('service_logo');

            	if (! this.errorInServices()) {

	            	this.errors.services.push({});

	            	this.$set(this.applicationSettings.services, this.applicationSettings.services.length, 

	            		{
		            		name : null,
		            		code : null,
		            		logo : null,
		            		status : false,
		            	}

	            	);

            	}

            },
            removeService(){
				
            	if (this.applicationSettings.services.length > 4 && this.errors.services.length > 4 && this.applicationSettings.services.length==this.errors.services.length) {

	            	this.errors.services.pop();
	            	this.applicationSettings.services.pop();

            	}

			},
			*/
			addMorePaymentMethod(){

            	this.validateFormInput('payment_name');
            	this.validateFormInput('payment_logo');

            	if (! this.errorInPaymentMethods()) {

	            	this.errors.payment_methods.push({});

	            	this.$set(this.applicationSettings.payment_methods, this.applicationSettings.payment_methods.length, 

	            		{
		        			name : null,
		        			logo : '',
	            			status : false,
		        		}

	            	);

            	}

            },
            removePaymentMethod(){

            	if (this.applicationSettings.payment_methods.length > 1 && this.errors.payment_methods.length > 1 && this.applicationSettings.payment_methods.length==this.errors.payment_methods.length) {

	            	this.errors.payment_methods.pop();
	            	this.applicationSettings.payment_methods.pop();

            	}

            },
			onPromotionalPreviewChange(evnt, index){
            	let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.createImage(files[0], 'promotional', index);
                	this.errors.promotional_sliders[index] = null;
                	// this.$delete(this.errors.promotional_sliders[index], 'preview');
		      	}
		      	else {

                	this.errors.promotional_sliders[index] = 'Preview is required';

                }

		      	evnt.target.value = '';

		      	return;
            },
			onStartingPreviewChange(evnt, index){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
                if (files.length && files[0].type.match('image.*')) {
                	this.submitForm = true;
                	this.createImage(files[0], 'starting', index);
                	this.$delete(this.errors.welcome_greetings[index], 'preview');
                }
                else {

                	this.errors.welcome_greetings[index].preview = 'Preview is required';

                }

                evnt.target.value = '';

                return;
            },
			onAccomplishmentPreviewChange(evnt){
				
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.createImage(files[0], 'accomplishment');
                	this.$delete(this.errors.thanks_greeting, 'preview');
		      	}
		      	else {

                	this.errors.thanks_greeting.preview = 'Preview is required';

                }

		      	evnt.target.value = '';

		      	return;

			},
			onServiceLogoChange(evnt, index){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.createImage(files[0], 'services', index);
                	this.$delete(this.errors.services[index], 'logo');
		      	}
		      	else {

                	this.errors.services[index].logo = 'Logo is required';

                }

		      	evnt.target.value = '';

		      	return;
			},
			onLogoChange(evnt){
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.createImage(files[0], 'logo');
                	this.$delete(this.errors, 'application_logo');
		      	}
		      	else {

                	this.errors.application_logo = 'Logo should be image';

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
                	this.$delete(this.errors, 'panel_favicon');
		      	}
		      	else {

                	this.errors.panel_favicon = 'Favicon should be image';

                }

		      	evnt.target.value = '';

		      	return;
			},
			onPaymentLogoChange(evnt, index) {
				let files = evnt.target.files || evnt.dataTransfer.files;

                // Only process image files.
		      	if (files.length && files[0].type.match('image.*')) {
		      		this.submitForm = true;
                	this.createImage(files[0], 'payment', index);
                	this.$delete(this.errors.payment_methods[index], 'logo');
		      	}
		      	else {

                	this.errors.payment_methods[index].logo = 'Payment logo is required';

                }

		      	evnt.target.value = '';

		      	return;
			},
			createImage(file, filename, index = false) {
                let reader = new FileReader();

                if (filename=='favicon') {
	                reader.onload = (evnt) => {
	                    this.newFavicon = this.applicationSettings.favicon = evnt.target.result;
	                };
                }
                else if (filename=='logo') {
                	reader.onload = (evnt) => {
	                    this.newLogo = this.applicationSettings.logo = evnt.target.result;
	                };
                }
                else if (filename=='accomplishment') {
                	reader.onload = (evnt) => {
	                    this.applicationSettings.thanks_greeting.preview = evnt.target.result;
	                };
                }
                else if (filename=='promotional' && this.applicationSettings.promotional_sliders.length > index) {
                	reader.onload = (evnt) => {
	                    this.applicationSettings.promotional_sliders[index].preview = evnt.target.result;
	                    // this.applicationSettings.promotional_sliders.length ? this.applicationSettings.promotional_sliders[index].preview = evnt.target.result : this.applicationSettings.promotional_sliders.push({ preview : evnt.target.result, status : false });
	                };
                }
                else if (filename=='starting' && this.applicationSettings.welcome_greetings.length > index) {
                	reader.onload = (evnt) => {
	                    this.applicationSettings.welcome_greetings[index].preview = evnt.target.result;
	                };
                }
                else if (filename=='services' && this.applicationSettings.services.length > index) {
                	reader.onload = (evnt) => {
	                    this.applicationSettings.services[index].logo = evnt.target.result;
	                };
                }
                else if (filename=='payment' && this.applicationSettings.payment_methods.length > index) {
                	reader.onload = (evnt) => {
	                    this.applicationSettings.payment_methods[index].logo = evnt.target.result;
	                };
                }

                reader.readAsDataURL(file);
            },
            errorInWelcomegreetings() {

            	return this.errors.welcome_greetings.some(
            		welcomeError => Object.keys(welcomeError).length > 0
            	);

            },
            errorInThanksGreeting() {

            	return Object.keys(this.errors.thanks_greeting).length > 0;

            },
            errorInPromotionalSliders() {

            	return this.errors.promotional_sliders.some( 
            		promotionalSliderError => promotionalSliderError != null
            	);

            },
            errorInServices() {

            	return this.errors.services.some(serviceError => Object.keys(serviceError).length > 0);

            },
            errorInPaymentMethods() {

            	return this.errors.payment_methods.some(paymentMethodError => Object.keys(paymentMethodError).length > 0);

            },
            validateFormInput (formInputName) {
				
				this.submitForm = false;

				switch(formInputName) {

					case 'app_name' :

						if (! this.applicationSettings.app_name) {
							this.errors.app_name = 'App name is required';
						}
						else if (! this.applicationSettings.app_name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.app_name = 'No special characters';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'app_name');
						}

						break;

					case 'searching_radius' :

						if (! this.applicationSettings.searching_radius || this.applicationSettings.searching_radius <= 0) {
							this.errors.searching_radius = 'Searching radius is required';
						}
						else if (! (/^[+-]?\d+(\.\d+)?$/g).test(this.applicationSettings.searching_radius)) {
							this.errors.searching_radius = 'Should be numbers only';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'searching_radius');
						}

						break;

					case 'multiple_delivery_charge_percentage' :

						if (! this.applicationSettings.multiple_delivery_charge_percentage || this.applicationSettings.multiple_delivery_charge_percentage <= 0) {
							this.errors.multiple_delivery_charge_percentage = 'Delivery percentage is required';
						}
						else if (! (/^[+-]?\d+(\.\d+)?$/g).test(this.applicationSettings.multiple_delivery_charge_percentage)) {
							this.errors.multiple_delivery_charge_percentage = 'Only numbers';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'multiple_delivery_charge_percentage');
						}

						break;

					case 'welcome_title' :

						if (this.applicationSettings.welcome_greetings.length) {
							
							this.applicationSettings.welcome_greetings.forEach((welcomeGreeting, welcomeGreetingIndex) => {

								if (! welcomeGreeting.title) {
									this.errors.welcome_greetings[welcomeGreetingIndex].title = 'Welcome title is required';
								}
								else if (! welcomeGreeting.title.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
									this.errors.welcome_greetings[welcomeGreetingIndex].title = 'No special characters';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.welcome_greetings[welcomeGreetingIndex], 'title');
								}

							});

						}

						if (! this.errorInWelcomegreetings()) {

							this.submitForm = true;

						}

						break;

					case 'welcome_paragraph' :

						if (this.applicationSettings.welcome_greetings.length) {

							this.applicationSettings.welcome_greetings.forEach((welcomeGreeting, welcomeGreetingIndex) => {

								if (! welcomeGreeting.paragraph) {
									this.errors.welcome_greetings[welcomeGreetingIndex].paragraph = 'Welcome paragraph is required';
								}
								else if (! welcomeGreeting.paragraph.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
									this.errors.welcome_greetings[welcomeGreetingIndex].paragraph = 'No special characters';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.welcome_greetings[welcomeGreetingIndex], 'paragraph');
								}

							});

						}

						if (! this.errorInWelcomegreetings()) {

							this.submitForm = true;

						}

						break;

					case 'welcome_preview' :

						if (this.applicationSettings.welcome_greetings.length) {

							this.applicationSettings.welcome_greetings.forEach((welcomeGreeting, welcomeGreetingIndex) => {

								if (! welcomeGreeting.preview) {
									this.errors.welcome_greetings[welcomeGreetingIndex].preview = 'Welcome preview is required';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.welcome_greetings[welcomeGreetingIndex], 'preview');
								}

							});

						}

						if (! this.errorInWelcomegreetings()) {

							this.submitForm = true;

						}

						break;

					case 'thanks_title' :

						if (! this.applicationSettings.thanks_greeting.title) {
							this.errors.thanks_greeting.title = 'Welcome title is required';
						}
						else if (!this.applicationSettings.thanks_greeting.title.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.thanks_greeting.title = 'No special characters';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.thanks_greeting, 'title');
						}

						if (! this.errorInThanksGreeting()) {

							this.submitForm = true;

						}

						break;

					case 'thanks_paragraph' :

						if (! this.applicationSettings.thanks_greeting.paragraph) {
							this.errors.thanks_greeting.paragraph = 'Payment paragraph is required';
						}
						else if (!this.applicationSettings.thanks_greeting.paragraph.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.thanks_greeting.paragraph = 'No special characters';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.thanks_greeting, 'paragraph');
						}

						if (! this.errorInThanksGreeting()) {

							this.submitForm = true;

						}

						break;

					case 'thanks_preview' :

						if (! this.applicationSettings.thanks_greeting.preview) {
							this.errors.thanks_greeting.preview = 'Welcome preview is required';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors.thanks_greeting, 'preview');
						}

						if (! this.errorInThanksGreeting()) {

							this.submitForm = true;

						}

						break;

					case 'promotional_sliders' :

						if (this.applicationSettings.promotional_sliders.length) {

							this.applicationSettings.promotional_sliders.forEach((promotionalSlider, promotionalSliderIndex) => {

								if (! promotionalSlider || ! promotionalSlider.preview) {
									this.errors.promotional_sliders[promotionalSliderIndex] = 'Promotional preview is required';
								}
								else {
									this.submitForm = true;
									this.errors.promotional_sliders[promotionalSliderIndex] = null;
									// this.$delete(this.errors.promotional_sliders[promotionalSliderIndex], 'preview');
								}

							});

						}

						if (! this.errorInPromotionalSliders()) {

							this.submitForm = true;

						}

						break;

					case 'service_name' :

						if (this.applicationSettings.services.length) {
							
							this.applicationSettings.services.forEach((service, serviceIndex) => {

								if (! service.name) {
									this.errors.services[serviceIndex].name = 'Service name is required';
								}
								else if (! service.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
									this.errors.services[serviceIndex].name = 'No special characters';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.services[serviceIndex], 'name');
								}

							});

						}

						if (! this.errorInServices()) {

							this.submitForm = true;

						}

						break;

					case 'service_code' :

						if (this.applicationSettings.services.length) {
							
							this.applicationSettings.services.forEach((service, serviceIndex) => {

								if (! service.code) {
									this.errors.services[serviceIndex].code = 'Service code is required';
								}
								else if (! service.code.match(/^[a-zA-Z0-9-_]+$/g)) {
									this.errors.services[serviceIndex].code = 'No space or special characters';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.services[serviceIndex], 'code');
								}

							});

						}

						if (! this.errorInServices()) {

							this.submitForm = true;

						}

						break;

					case 'service_logo' :

						if (this.applicationSettings.services.length) {
							
							this.applicationSettings.services.forEach((service, serviceIndex) => {

								if (! service.logo) {
									this.errors.services[serviceIndex].logo = 'Service logo is required';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.services[serviceIndex], 'logo');
								}

							});

						}

						if (! this.errorInServices()) {

							this.submitForm = true;

						}

						break;

					case 'payment_name' :

						if (this.applicationSettings.payment_methods.length) {
							
							this.applicationSettings.payment_methods.forEach((paymentMethod, paymentMethodIndex) => {

								if (! paymentMethod.name) {
									this.errors.payment_methods[paymentMethodIndex].name = 'Payment name is required';
								}
								else if (! paymentMethod.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
									this.errors.payment_methods[paymentMethodIndex].name = 'No special characters';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.payment_methods[paymentMethodIndex], 'name');
								}

							});

						}

						if (! this.errorInPaymentMethods()) {

							this.submitForm = true;

						}

						break;

					case 'payment_logo' :

						if (this.applicationSettings.payment_methods.length) {
							
							this.applicationSettings.payment_methods.forEach((paymentMethod, paymentMethodIndex) => {

								if (! paymentMethod.logo) {
									this.errors.payment_methods[paymentMethodIndex].logo = 'Payment logo is required';
								}
								else {
									this.submitForm = true;
									this.$delete(this.errors.payment_methods[paymentMethodIndex], 'logo');
								}

							});

						}

						if (! this.errorInPaymentMethods()) {

							this.submitForm = true;

						}

						break;

					case 'official_currency' :

						if (!this.applicationSettings.official_currency) {
							this.errors.official_bank = 'Bank name is required';
						}
						else if (!this.applicationSettings.official_currency.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.official_bank = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'official_currency');
						}

						break;

					case 'vat_rate' :

						if (! this.applicationSettings.vat_rate) {
							this.errors.vat_rate = 'Vat rate is required';
						}
						else if (this.applicationSettings.vat_rate < 0 || this.applicationSettings.vat_rate > 100 ) {
							this.errors.vat_rate = 'Value should be between 1 and 100';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'vat_rate');
						}

						break;

					case 'official_bank' :

						if (!this.applicationSettings.official_bank) {
							this.errors.official_bank = 'Bank name is required';
						}
						else if (!this.applicationSettings.official_bank.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
							this.errors.official_bank = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'official_bank');
						}

						break;

					case 'official_bank_account_number' :

						if (!this.applicationSettings.official_bank_account_number) {
							this.errors.official_bank_account_number = 'Account number is required';
						}
						else if (!this.applicationSettings.official_bank_account_number.match(/^[_A-z0-9]*((-|_|\s)*[_A-z0-9])*$/g)) {
							this.errors.official_bank_account_number = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'official_bank_account_number');
						}

						break;

					case 'official_bank_account_holder_name' :

						if (!this.applicationSettings.official_bank_account_holder_name) {
							this.errors.official_bank_account_holder_name = 'Account name is required';
						}
						else if (!this.applicationSettings.official_bank_account_holder_name.match(/^[_A-z0-9]*((-|_|\s)*[_A-z0-9])*$/g)) {
							this.errors.official_bank_account_holder_name = 'No special character';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'official_bank_account_holder_name');
						}

						break;

					case 'merchant_number' :

						if (!this.applicationSettings.merchant_number) {
							this.errors.merchant_number = 'Mobile is required';
						}
						else if (!this.applicationSettings.merchant_number.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.merchant_number = 'Invalid mobile number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'merchant_number');
						}

						break;

					case 'payment_method.name' :

						this.applicationSettings.payment_methods.forEach((paymentMethod, paymentMethodIndex) => {

							if (! this.paymentMethod.name) {
								this.errors.payment_methods[paymentMethodIndex].name = 'Payment name is required';
							}
							else if (!this.this.paymentMethod.name.match(/^[_A-z0-9]*((-|&|\s)*[_A-z0-9])*$/g)) {
								this.errors.payment_methods[paymentMethodIndex].name = 'No special characters';
							}
							else {
								this.submitForm = true;
								this.$delete(this.errors.payment_methods[paymentMethodIndex], 'name');
							}

						});

						break;

					case 'payment_method.logo' :

						this.applicationSettings.payment_methods.forEach((paymentMethod, paymentMethodIndex) => {

							if (! this.paymentMethod.logo) {
								this.errors.payment_methods[paymentMethodIndex].logo = 'Payment logo is required';
							}
							else {
								this.submitForm = true;
								this.$delete(this.errors.payment_methods[paymentMethodIndex], 'logo');
							}

						});	

						break;

					case 'official_customer_care_number' :

						if (!this.applicationSettings.official_customer_care_number) {
							this.errors.official_customer_care_number = 'Customer care number is required';
						}
						else if (!this.applicationSettings.official_customer_care_number.match(/\+?(88)?0?1[123456789][0-9]{8}\b/g)) {
							this.errors.official_customer_care_number = 'Invalid customer care number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'official_customer_care_number');
						}

						break;

					case 'official_mail_address' :

						if (!this.applicationSettings.official_mail_address) {
							this.errors.official_mail_address = 'Official mail is required';
						}
						else if (!this.applicationSettings.official_mail_address.match(/[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}$/g)) {
							this.errors.official_mail_address = 'Invalid email';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'official_mail_address');
						}

						break;

					case 'official_contact_address' :

						if (!this.applicationSettings.official_contact_address) {
							this.errors.official_contact_address = 'Official address is required';
						}
						else{
							this.submitForm = true;
							this.$delete(this.errors, 'official_contact_address');
						}

						break;

					/*
					case 'delivery_charge' :

						if (!this.applicationSettings.delivery_charge) {
							this.errors.delivery_charge = 'Delivery charge is required';
						}
						else if (this.applicationSettings.delivery_charge < 0 ) {
							this.errors.delivery_charge = 'Value should be positive number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'delivery_charge');
						}

						break;
					*/

					case 'multiple_delivery_charge_percentage' :

						if (!this.applicationSettings.multiple_delivery_charge_percentage) {
							this.errors.multiple_delivery_charge_percentage = 'Percentage is required';
						}
						else if (this.applicationSettings.multiple_delivery_charge_percentage < 0 ) {
							this.errors.multiple_delivery_charge_percentage = 'Value should be positive number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'multiple_delivery_charge_percentage');
						}

						break;

					/*
					case 'welcome_greetings' :

						if (!this.applicationSettings.multiple_delivery_charge_percentage) {
							this.errors.multiple_delivery_charge_percentage = 'Percentage is required';
						}
						else if (this.applicationSettings.multiple_delivery_charge_percentage < 0 ) {
							this.errors.multiple_delivery_charge_percentage = 'Value should be positive number';
						}
						else {
							this.submitForm = true;
							this.$delete(this.errors, 'multiple_delivery_charge_percentage');
						}

						break;
					*/
				}
	 
			},
		}
  	}

</script>