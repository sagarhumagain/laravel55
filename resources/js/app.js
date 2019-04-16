
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
//
window.Vue = require('vue');
import Vue from 'vue'
//importing vform
import { Form, HasError, AlertError } from 'vform';
//importing moment js
import moment from 'moment';
//importing sweet alert
import Swal from 'sweetalert2'
window.Swal = Swal;
const Toast = Swal.mixin({
  position: 'center',
  timer: 3000
});
window.Toast = Toast;
//vue progress bar
import VueProgressBar from 'vue-progressbar';
Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'green',
  height: '3px'
})
//costom event
window.Fire = new Vue();
//vform
window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)


import VueRouter from 'vue-router'
//vue routing 
Vue.use(VueRouter)
//component path
let routes = [
    { path: '/dashboard', component: require('./components/dashboard.vue' ).default },
    { path: '/profile', component: require('./components/profile.vue').default },
    { path: '/users', component: require('./components/users.vue').default }
  ]
//routing object
const router = new VueRouter({
    mode: 'history', //keep track of history or refresh link 
    routes // short for `routes: routes`
  })
  //uppercase 
  Vue.filter('capitalize', function (value) {
    return value.charAt(0).toUpperCase() + value.slice(1)
  })
  //moment js for date and time
  Vue.filter('mydate', function(created){
    return moment(created).format("MMM Do YY");              
  });
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
//initiation router object
const app = new Vue({
    el: '#app',
    router
});
