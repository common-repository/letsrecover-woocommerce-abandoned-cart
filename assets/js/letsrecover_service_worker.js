'use strict';
var payload;
var notificationClicked = false;
var formData = new FormData();
var site_url = self.origin;
formData.append('action', 'wplrp_notification_log');		
self.addEventListener('install', event => {self.skipWaiting();});
self.addEventListener('activate', event => {console.log('LetsRecover service worker is activated! v1.0');});


self.addEventListener('push', event => {
	const payload = event.data.json();
	const title = payload.t;

	formData.delete('id');
	formData.append('id', payload.id);

	const options = {
		body	: payload.m,
		icon	: (typeof(payload.i) != 'undefined')?payload.i:'',
		badge	: (typeof(payload.b) != 'undefined')?payload.b:'',
		image	: (typeof(payload.img) != 'undefined')?payload.img:'',
		data	: {url:payload.u},
		renotify: true,
		tag: 'letsrecover',
		actions	: (typeof(payload.a) != 'undefined')?payload.a:[],
		requireInteraction:payload.ah,
	};


	event.waitUntil(self.registration.showNotification(title, options).then(function(){
		formData.delete('wplrp_log');
		formData.append('wplrp_log', 'delivered');		
		fetch(site_url + "/wp-admin/admin-ajax.php",
			{ 
				method: 'POST',
				body: formData
			}
		);
	}));
	
});
self.addEventListener('notificationclick', function(event) {
	notificationClicked = true
	event.notification.close();
	formData.delete('wplrp_log');
	formData.append('wplrp_log', 'clicked');

	if (!event.action) {
		event.waitUntil(clients.openWindow(event.notification.data.url).then(function(){}));
	}else{
      clients.openWindow(event.action);
   }

	fetch(site_url + "/wp-admin/admin-ajax.php",
		{ 
			method: 'POST',
			body: formData
		}
	);
});

self.addEventListener('notificationclose', function(event) {
	if( notificationClicked == false ){
		formData.delete('wplrp_log');
		formData.append('wplrp_log', 'closed');
		fetch(site_url + "/wp-admin/admin-ajax.php",
			{ 
				method: 'POST',
				body	: formData
			}
		);
	}
});

