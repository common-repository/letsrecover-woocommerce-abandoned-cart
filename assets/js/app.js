jQuery(document).ready(function(e) {
	var q = window.letsrecover.q || [];
	var applicationServerKey,prompt_type,show_prompt,sw_registration, site_url;
	var publicMethods = {
		subscribe: function (key,siteURL,promptType,sw_reg = null) {
			applicationServerKey = key;
			sw_registration = sw_reg;
			
			prompt_type = promptType;
			site_url = siteURL;
			if( Notification.permission == 'granted' )
				_lpRegisterServiceWorker();
		},
		show_prompt: function(){
			if( Notification.permission == 'granted' )
				return;
				
			if ( ! ('serviceWorker' in navigator) ) {
				console.warn("Service workers are not supported by this browser");
				//changePushButtonState('incompatible');
				return;
			}

			if (!('PushManager' in window)) {
				console.warn('Push notifications are not supported by this browser');
				//changePushButtonState('incompatible');
				return;
			}

			if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
				console.warn('Notifications are not supported by this browser');
				//changePushButtonState('incompatible');
				return;
			}
			_lpRegisterServiceWorker();

		},
		prompt_action:	function (action){
			document.getElementsByTagName("wplrpPromptWrapper")[0].style.display = 'none';
			if( action == 'Approve' )
				request_permission();
		}

	};
	
	const testPushButton = document.querySelector('#test-push-button');
	const sendPushButton = document.querySelector('#send-push-button');
	const friendshipAcceptBtn = document.querySelector('#friend-list a.accept');
	
	window.letsrecover = function () {
		publicMethods[arguments[0]].apply(this, Array.prototype.slice.call(arguments, 1));
	};

	q.forEach(function (command) {
		window.letsrecover.apply(this, command);
	});


	function _lpRegisterServiceWorker(){
		if( sw_registration == 'none' ){
			show_prompt();
			return;
		}
		navigator.serviceWorker.register( site_url + '/letsrecover-sw.js', {scope : '/'})
		.then(function(reg)  {
			show_prompt();
		}, e => {
			console.error('[SW] Service worker registration failed', e);
		});
	}

	function show_prompt(){

		if (Notification.permission == 'default'){
			if( prompt_type == 'native' ){
				request_permission()
			}else{
				_lpShowCustomPrompt();
			}
		}
		else if (Notification.permission == 'granted'){
			if(testPushButton)
				testPushButton.setAttribute("style", "display: inline-block;");

			_lpCheckSubscription();
		}

	}

	function _lpShowCustomPrompt(){
		if( sessionStorage.getItem('_letsrecoverPromptShow') )
			return;

		sessionStorage.setItem('_letsrecoverPromptShow',1);
		document.getElementsByTagName("wplrpPromptWrapper")[0].style.display = 'block';
	}

	function request_permission(){
		Notification.requestPermission().then(status => {
			switch(status){
				case"default":
					break;
				case"denied":
					break;
				case"granted":
					if(testPushButton)
						testPushButton.setAttribute("style", "display: inline-block;");
					// send subscription to database
					return _lpGetSubscriptionDetail();
			}//switch
		})//request permission
	}

	function _lpGetSubscriptionDetail(existing_subscription = null){
		
		navigator.serviceWorker.ready
			.then(function(registration){
			registration.pushManager.subscribe({
				userVisibleOnly: true,
				applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
			})
			.then(subscription => {
				// Subscription was successful
				// send subscription to database
				if( existing_subscription )
					push_sendSubscriptionToServer(subscription, 'PUT');
				else
					push_sendSubscriptionToServer(subscription, 'POST');
			})
			.catch(e => {
				existing_subscription.unsubscribe().then(function(successful) {
					request_permission();
				})
			});
		});
	}

	function _lpCheckSubscription(){
		navigator.serviceWorker.ready.then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
		.then(subscription => {

			if (!subscription) {
				console.log('already subscribed but subscription not found');
				request_permission();
				return;
			}
			_lpGetSubscriptionDetail(subscription);
		})
		// .then(subscription => subscription ) // Set your UI to show they have subscribed for push messages
		.catch(e => {
			console.log(e);
		});
	}

	function urlBase64ToUint8Array(base64String) {
		const padding = '='.repeat((4 - base64String.length % 4) % 4);
		const base64 = (base64String + padding)
			.replace(/\-/g, '+')
			.replace(/_/g, '/');

		const rawData = window.atob(base64);
		const outputArray = new Uint8Array(rawData.length);

		for (let i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		}
		return outputArray;
	}


	function push_sendSubscriptionToServer(subscription, method) {

		if( sessionStorage.getItem('_letsrecoverEndPoint') ==  subscription.endpoint)
			return; 
			
		const key 	= subscription.getKey('p256dh');
		const token = subscription.getKey('auth');

		var data = {
			endpoint: subscription.endpoint,
			key		: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
			token	: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
			type	: method,
			action	: 'letsrecover_save_subscription'
		};
		jQuery.ajax({
			url : site_url + '/wp-admin/admin-ajax.php',
			data: data,
			type:'POST',
			success:function(){
				sessionStorage.setItem('_letsrecoverEndPoint', subscription.endpoint)
			}
		});

		return;
	}

	jQuery('body').on( 'added_to_cart', function(){
		if( Notification.permission == 'default' )
			request_permission();
	});


});

