

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import toastr from 'toastr';

window.Alpine = Alpine;
window.Pusher = Pusher;
window.toastr = toastr;

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": 5000
};

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'minimarket-key',
    wsHost: window.location.hostname,
    wsPort: 8080,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});

Alpine.start();
