
<template>

	<!-- /.card-header -->
	<!-- 
	<div class="card-header">
   	 	<h3 class="card-title">Profile Updation</h3>
  	</div> 
  	-->

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
			<ul class="nav nav-pills nav-tabs nav-justified mb-4">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#profile">Profile</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#password">Password</a>
				</li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane container active" id="profile">	
					<!-- form start -->
				  	<form class="form-horizontal" method="post" v-on:submit.prevent="profileUpdation">

			      		<input type="hidden" name="_token" :value="csrf">

						<div class="row">

							<div v-if="admin" class="col-sm-3">
								<div class="card card-primary card-outline">
					              	<div class="card-body box-profile">
					                	<div class="text-center">
					                  		<img class="profile-user-img img-fluid img-circle" :src="admin.profile_picture" alt="Admin picture">
					                	</div>

					                	<h3 class="profile-username text-center">
					                		{{ admin.first_name+' '+admin.last_name }}
					                	</h3>

					                	<p class="text-muted text-center">Role Name</p>

					                	<div class="row">
						              		<!-- <label for="inputMobile3" class="col-sm-4 col-form-label text-right">Picture</label> -->
							                <div class="col-sm-12">
							                  	<div class="input-group">
								                    <div class="custom-file">
								                        <input type="file" class="custom-file-input" id="exampleInputFile" v-on:change="onImageChange" accept="image/*">
								                        <label class="custom-file-label" for="exampleInputFile">Change Picture</label>
								                    </div>
							                    </div>
							                </div>
						            	</div>
					              	</div>
					              	<!-- /.card-body -->
					            </div>
							</div>
						
							<div v-if="admin" class="col-sm-9">
								<div class="card card-primary card-outline">
						            <div class="card-body box-profile">
						              	<div class="form-group row">
							              	<div class="col-6">
							              		<div class="row">
								              		<label for="inputFirstName3" class="col-sm-4 col-form-label text-right">First Name</label>
									                <div class="col-sm-8">
									                  <input type="text" class="form-control" id="inputFirstName3" v-model="admin.first_name" placeholder="First Name">
									                </div>
							              		</div>
							              	</div>
							                <div class="col-6">
							                	<div class="row">
								              		<label for="inputLastName3" class="col-sm-4 col-form-label text-right">Last Name</label>
									                <div class="col-sm-8">
									                  	<input type="text" class="form-control" id="inputLastName3" v-model="admin.last_name" placeholder="Last Name">
									                </div>
							                	</div>
							              	</div>
						              	</div>
						              	<div class="form-group row">
							              	<div class="col-6">
							              		<div class="row">
								              		<label for="inputEmail3" class="col-sm-4 col-form-label text-right">Email</label>
									                <div class="col-sm-8">
									                  <input type="email" class="form-control" id="inputEmail3" v-model="admin.email" placeholder="Email" required="true">
									                </div>
							              		</div>
							              	</div>
							                <div class="col-6">
							                	<div class="row">
							                		<label for="inputMobile3" class="col-sm-4 col-form-label text-right">Mobile</label>
									                <div class="col-sm-8">
									                  	<input type="tel" class="form-control" id="inputMobile3" v-model="admin.mobile" placeholder="Mobile" required="true">
									                </div>
							                	</div>
							              	</div>
						              	</div>
						            </div>
						            <!-- /.card-body -->
						            <div class="card-footer text-center">
						              	<button type="submit" :disabled="loading" class="btn btn-primary">Update Profile</button>
						            </div>
						        	<!-- /.card-footer -->
							    </div>
							</div>
						</div>
					</form>
				</div>

				<div class="tab-pane container fade" id="password">	
					<div class="row">

						<div  v-if="!loading" class="col-sm-12">
							<div class="card card-primary card-outline">
								<!-- form start -->
						      	<form class="form-horizontal" v-on:submit.prevent="passwordUpdation">
						      		
						      		<input type="hidden" name="_token" :value="csrf">
						            
						            <div class="card-body box-profile">  

						              	<div class="form-group row">
						              		<label for="inputPassword3" class="col-sm-3 col-form-label text-right">Current Password</label>
							                <div class="col-sm-9">
							                  	<input type="password" class="form-control" id="inputPassword3" v-model="password.current_password" placeholder="Current Password" required="true">
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputNewPassword3" class="col-sm-3 col-form-label text-right">New Password</label>
							                <div class="col-sm-9">
							                  	<input type="password" class="form-control" id="inputNewPassword3" v-model="password.password" placeholder="New Password" required="true">
							                </div>
						              	</div>
						              	<div class="form-group row">
						              		<label for="inputConfirmPassword3" class="col-sm-3 col-form-label text-right">Confirm Password</label>
							                <div class="col-sm-9">
							                  	<input type="password" class="form-control" id="inputConfirmPassword3" v-model="password.password_confirmation" placeholder="Confirm Password" required="true">
							                </div>
						              	</div>

						            </div>
						            <!-- /.card-body -->
						            <div class="card-footer text-center">
						              	<button type="submit" :disabled="loading" class="btn btn-primary">Update Password</button>
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

	export default {

	    data() {
	        return {
	        	admin : null,
	        	password : {},
	        	loading : false,
	        	newProfilePicture : null,
	            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
	        }
		},
		created(){
			this.fetchData();
		},
		methods : {
			fetchData() {
				this.loading = true;
				this.admin = false;
				axios
					.get('/api/profile')
					.then(response => {
						this.loading = false;
						this.admin = response.data;
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
                    this.newProfilePicture = this.admin.profile_picture = evnt.target.result;
                };
                reader.readAsDataURL(file);
            },
			profileUpdation() {

				let newData = {
					first_name : this.admin.first_name,
					last_name : this.admin.last_name,
					email : this.admin.email,
					mobile : this.admin.mobile,
					profile_picture : this.newProfilePicture,
				};

				axios
					.post('/profile', newData)
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
			passwordUpdation() {

				this.loading = true;

				let newData = {
					current_password : this.password.current_password,
					password : this.password.password,
					password_confirmation : this.password.password_confirmation,
				};

				axios
					.post('/password', newData)
					.then(response => {

						this.loading = false;

						if (response.status == 200) {
							toastr.success(response.data.success, "Success");
						}
					})
					.catch(error => {
						
						this.loading = false;

						if (error.response.status == 422) {

							for (var x in error.response.data.errors) {
								toastr.warning(error.response.data.errors[x], "Warning");
							}
				      	}
				      	else if (error.response.status == 401) {
							
							toastr.error("Wrong Current Password ", "Oops");	
				      	}
					});
			}
		}
  	}

</script>