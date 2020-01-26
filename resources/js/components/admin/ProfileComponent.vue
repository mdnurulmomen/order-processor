
<template>
	<!-- /.card-header -->
	<!-- 
	<div class="card-header">
   	 	<h3 class="card-title">Profile Updation</h3>
  	</div> 
  	-->

<div>
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
		  	<form class="form-horizontal" method="post" v-on:submit.prevent="profileUpdation" enctype="multipart/form-data">

				<div class="row">
					<div v-if="loading">
						Loading ...
					</div>

		      		<input type="hidden" name="_token" :value="csrf">

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
				              	<button type="submit" class="btn btn-primary">Update Profile</button>
				            </div>
				        	<!-- /.card-footer -->
					    </div>
					</div>
				</div>
			</form>
		</div>

		<div class="tab-pane container fade" id="password">	
			<div class="row">
				<div class="col-sm-12">
					<div class="card card-primary card-outline">
						<!-- form start -->
				      	<form class="form-horizontal" method="post" action="/password">
				      		
				      		<input type="hidden" name="_token" :value="csrf">
				            
				            <div class="card-body box-profile">  

				              	<div class="form-group row">
				              		<label for="inputPassword3" class="col-sm-3 col-form-label text-right">Current Password</label>
					                <div class="col-sm-9">
					                  	<input type="password" class="form-control" id="inputPassword3" name="current_password" placeholder="Current Password" required="true">
					                </div>
				              	</div>
				              	<div class="form-group row">
				              		<label for="inputPassword3" class="col-sm-3 col-form-label text-right">New Password</label>
					                <div class="col-sm-9">
					                  	<input type="password" class="form-control" id="inputPassword3" name="password" placeholder="New Password" required="true">
					                </div>
				              	</div>
				              	<div class="form-group row">
				              		<label for="inputPassword3" class="col-sm-3 col-form-label text-right">Confirm Password</label>
					                <div class="col-sm-9">
					                  	<input type="password" class="form-control" id="inputPassword3" name="password_confirmation" placeholder="Confirm Password" required="true">
					                </div>
				              	</div>

				            </div>
				            <!-- /.card-body -->
				            <div class="card-footer text-center">
				              	<button type="submit" class="btn btn-primary">Update Password</button>
				            </div>
				        	<!-- /.card-footer -->
				      	</form>
				    </div>
				</div>
			</div>
		</div>
	</div>
	
</div>

    
</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {

	    data() {
	        return {
	        	loading : false,
	        	admin : null,
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
						// console.log(response.data);
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
			profileUpdation(){

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
						console.log(response.data);
					})
					.catch(error => {
						console.log("ERRRR:: ", error.response.data);
					});
			}
		}
  	}

</script>