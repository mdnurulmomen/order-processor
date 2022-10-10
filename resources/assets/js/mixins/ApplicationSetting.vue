<script type="text/javascript">
	
	// define a mixin object
	export default {

		data: function() {
			return {
				application_settings : JSON.parse(window.localStorage.getItem('application_settings')),
			}
		}
	
	}

</script>