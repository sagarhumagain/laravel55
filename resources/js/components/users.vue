<template>
    <div class="container">
      <!-- can see this content ony if user is admin // vue authentication -->
    <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">User Management</h3>

                <div class="card-tools">
                  <button class="btn btn-success" @click="newModal" >Add New <i class="fas fa-user-plus fa-fw"></i> </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Registered At</th>
                    <th>Modify</th>
                  </tr>
                  <!-- adding data into the table -->
                  <tr v-for="user in users" :key="user.id"> <!-- ADDED DATA BY THERE ID -->
                    <td>{{user.id}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <!-- adding filters -->
                    <td>{{user.type | capitalize}}</td> <!-- Uppercase -->
                     <td>{{user.created_at | mydate}}</td> <!-- moment js -->
                    <td>
                        <a href="#" @click="editModal(user)" > <i class="fa fa-edit blue"></i> </a>
                        / 
                        <!-- trigger button to edit and delete  -->
                        <a href="#" @click="deleteUser(user.id)"> <i class="fa fa-trash red"></i> </a>                        
                    </td>
                  </tr>                                  
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- displaying error page if user goes to that route  -->
        <div v-if="$gate.isUser()"> 
          <error-page></error-page>
        </div>
              <!-- bootstrap modal -->
<div class="modal fade" id="AddNew" tabindex="-1" role="dialog" aria-labelledby="AddNewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5  v-show="!editmode" class="modal-title" id="AddNewLabel">Add New</h5>
        <h5 v-show="editmode"  class="modal-title" id="AddNewLabel">Update User Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="editmode ? updateUser() :createUser()"> <!--  submit dynamic data with page refresh -->
      <div class="modal-body">
            <div class="form-group">
                <input v-model="form.name" type="text" name="name" placeholder="Name"
                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                <has-error :form="form" field="name"></has-error>
            </div>
            <div class="form-group">
                <input v-model="form.email" type="emil" name="email" placeholder="Email Address"
                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                <has-error :form="form" field="email"></has-error>
            </div>
            <div class="form-group">
                <textarea v-model="form.bio" type="bio" id="bio" placeholder="short bio for user(Optional)"
                class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                <has-error :form="form" field="bio"></has-error>
            </div>
            <div class="form-group">
                <select name="type" v-model="form.type"  id="type" 
                class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                    <option value="">Select User Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">Standard User</option>
                    <option value="author">Author</option>
                </select>
                <has-error :form="form" field="type"></has-error>
            </div>
            <div class="form-group">
                <input v-model="form.password" type="password" name="password" id="Password"
                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"></has-error>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-danger" data-dismiss="modal">Close</button>
        <!-- displaying modal with different condition -->
        <button v-show="editmode" type="submit" class="btn btn-success">update</button>
        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>

      </div>
    </form>
    </div>
  </div>
</div>
<!-- End bootstrap modal -->
    </div>
</template>

<script>
    export default {
        data(){
            return{
                editmode : false,
                users: {}, //retriving data to fill in the form
                form: new Form({
                    id: '',
                    name : '',
                    email: '',
                    password: '',
                    email_verified_at: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods:{
          //update fuction
          updateUser(id){
            //console.log('editing data');
            this.$Progress.start();
            this.form.put('api/user/'+this.form.id)
            .then(()=>{
              $('#AddNew').modal('hide')
                    Toast.fire({
                              type: 'success',
                              title: 'success...',
                              text: 'Information has been updated.',
                              showConfirmButton: true,
                              })
                    this.$Progress.finish();
                    Fire.$emit('AfterCreated');
            })
            .catch(()=>{
                 this.$Progress.fail();
            })
          },
          //open modal for edit
          editModal(user){
            this.editmode = true;
            this.form.reset();
             $('#AddNew').modal('show');
             this.form.fill(user);
          },
          //open model to add new data
          newModal(){
            this.editmode = false;
            this.form.reset();
            $('#AddNew').modal('show');
          },
          //delete user
            deleteUser(id){
                     Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                          }).then((result) => {
                            //send request to the server
                            if (result.value) {
                            this.form.delete('api/user/'+id).then(()=>{
                              Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                              )
                            Fire.$emit('AfterDeleted');    
                            }).catch(()=>{
                              Swal.fire({
                                  type: 'error',
                                  title: 'Oops...',
                                  text: 'Something went wrong!',
                                  footer: '<a href>Why do I have this issue?</a>'
                                })
                            });
                            }
                          })
            },
            loadUsers(){
              if(this.$gate.isAdminOrAuthor()){
                axios.get("api/user").then(({data})=>(this.users = data.data));//loading data
              } 
            },
            // push http request to create user
            createUser(){
                this.$Progress.start()
                this.form.post('api/user')
                .then(()=>{
                    Fire.$emit('AfterCreated');
                    $('#AddNew').modal('hide')
                    Toast.fire({
                              type: 'success',
                              title: 'success...',
                              text: 'Good Job!',
                              showConfirmButton: true,
                              })
                    this.$Progress.finish();
                })
                .catch(()=>{

                })
            }
        },
        created() {
          //display users
            this.loadUsers();
            //setInterval(() => this.loadUsers(), 3000);
            Fire.$on('AfterCreated', ()=>{
              this.loadUsers(); 

            });
            Fire.$on('AfterDeleted', ()=>{
              this.loadUsers(); 

            });
            
        }
    }
</script>
