Vue.filter("capitalize",  function(value)  {
  if  (!value)  {
    return  "";
  }
  value  =  value.toString();
  return  value.charAt(0).toUpperCase()  +  value.slice(1);
});

Vue.filter("eachcapitalize",  function(str)  {
  if  (!str)  {
    return  "";
  }
  var splitStr = str.toLowerCase().split(' ');
   for (var i = 0; i < splitStr.length; i++) {
       // You do not need to check if i is larger than splitStr length, as your for does that for you
       // Assign it back to the array
       splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
   }
   // Directly return the joined string
   return splitStr.join(' '); 
});

Vue.prototype.$application_settings = JSON.parse(localStorage.getItem('application_settings'))